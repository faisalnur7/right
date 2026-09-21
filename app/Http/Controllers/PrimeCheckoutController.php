<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\PrimeCart;
use App\Models\PaymentOption;
use App\Models\District;
use App\Models\PoliceStation;
use App\Models\PostOffice;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\NotificationService;
use App\Traits\ShippingCalculator;

class PrimeCheckoutController extends Controller
{
    use ShippingCalculator;

    public function showCheckout(){
        $data['cart'] = $cart = PrimeCart::with('items.product')->where('user_id', auth()->id())->first();

        if (!$cart || $cart->items->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Your cart is empty.');
        }

        $data['subtotal'] = $subtotal = $cart->items->sum(function ($item) {
            return $item->quantity * $item->product->affiliate_price;
        });

        $data['shipping'] = $shipping = $this->calculateShipping($subtotal);
        $data['total'] = $subtotal + $shipping;
        $data['paymentOptions'] = PaymentOption::all();
        $data['districts'] = District::pluck('name','id');
        $data['policeStations'] = PoliceStation::pluck('name','id');
        $data['postOffices'] = PostOffice::pluck('name','id');

        return view('prime_user.product_purchase.checkout', $data);
    }

    public function placeOrder(Request $request, NotificationService $notificationService){
        
        $user = Auth::user();
        if (empty($user->cart)) {
            return response()->json(['status' => '404', 'message' => 'No active cart!']);
        }

        $rules = [
            'has_shipping' => 'required|boolean',
            'selected_shipping_address_id' => 'nullable|exists:addresses,id',
            'payment_method_id' => 'required_unless:is_cod,1|exists:payment_options,id|nullable',
            'payment_account_number' => 'required_unless:is_cod,1|string|max:255|nullable',
            'transaction_id' => 'required_unless:is_cod,1|string|max:255|nullable',
            'sender_phone_number' => 'required_unless:is_cod,1|string|max:20|nullable',
            'is_cod' => 'nullable|boolean',
        ];

        // Require shipping address fields if shipping is enabled and no saved address selected
        if ($request->boolean('has_shipping') && !$request->filled('selected_shipping_address_id')) {
            $rules = array_merge($rules, [
                'shipping_address.name' => 'required|string|max:255',
                'shipping_address.phone' => 'required|string|max:20',
                'shipping_address.district_id' => 'required|integer|exists:districts,id',
                'shipping_address.police_station_id' => 'required|integer|exists:police_stations,id',
                'shipping_address.post_office_id' => 'required|integer|exists:post_offices,id',
                'shipping_address.address' => 'required|string|max:255',
                'shipping_address.city' => 'nullable|string|max:100',
                'shipping_address.zip' => 'required|string|max:10',
            ]);
        }

        // Require billing address fields if no saved billing address is selected
        if (!$request->filled('selected_address_id')) {
            $rules = array_merge($rules, [
                'billing_address.name' => 'required|string|max:255',
                'billing_address.email' => 'nullable|email|max:255',
                'billing_address.phone' => 'required|string|max:20',
                'billing_address.district_id' => 'required|integer|exists:districts,id',
                'billing_address.police_station_id' => 'required|string|max:255',
                'billing_address.post_office_id' => 'required|string|max:255',
                'billing_address.address' => 'required|string|max:255',
                'billing_address.city' => 'nullable|string|max:100',
                'billing_address.zip' => 'required|string|max:10',
            ]);
        }

        $request->validate($rules);

        DB::beginTransaction();
        try {
            // Save billing address if user asked to save and didn't select existing one
            if ($request->boolean('save_addresses') && !$request->filled('selected_address_id')) {
                Address::updateOrCreate(
                    ['user_id' => $user->id, 'type' => '1'],
                    $request->input('billing_address') + ['type' => '1']
                );
            }

            // Save new shipping address if requested
            if (
                $request->boolean('has_shipping') &&
                !$request->filled('selected_shipping_address_id') &&
                $request->boolean('save_addresses')
            ) {
                Address::updateOrCreate(
                    ['user_id' => $user->id, 'type' => '2'],
                    $request->input('shipping_address') + ['type' => '2']
                );
            }

            // Prepare billing address
            $billingAddress = $request->filled('selected_address_id')
                ? Address::findOrFail($request->selected_address_id)->toArray()
                : $request->input('billing_address');

            // Prepare shipping address
            $shippingAddress = null;
            if ($request->boolean('has_shipping')) {
                if ($request->filled('selected_shipping_address_id')) {
                    $shippingAddress = Address::findOrFail($request->selected_shipping_address_id)->toArray();
                } else {
                    $shippingAddress = $request->input('shipping_address');
                }
            }

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'billing_address' => json_encode($billingAddress),
                'shipping_address' => $shippingAddress ? json_encode($shippingAddress) : null,
                'payment_option_id' => $request->payment_method_id,
                'payment_account_number' => $request->payment_account_number,
                'transaction_id' => $request->transaction_id,
                'sender_phone_number' => $request->sender_phone_number,
                'status' => 1,
                'total' => 0,
                'subtotal' => 0,
                'shipping_charge' => 0,
                'order_tracking_number' => Str::upper(Str::random(8)),
                'is_cod' => $request->is_cod
            ]);

            // Order Items
            $totalAmount = 0;
            $subTotalAmount = 0;

            foreach ($user->cart->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = $item['quantity'];
                $price = $product->affiliate_price;
                $subtotal = $quantity * $price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'sale_log_id' => $item['sale_log_id'],
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'subtotal' => $subtotal,
                ]);

                $subTotalAmount += $subtotal;
            }

            $shippingCharge = $this->calculateShipping($subTotalAmount);
            $totalAmount = $subTotalAmount + $shippingCharge;

            $order->update([
                'subtotal' => $subTotalAmount,
                'total' => $totalAmount,
                'shipping_charge' => $shippingCharge,
            ]);

            $notificationData = [
                'order_id' => $order->order_tracking_number,
                'amount' => $totalAmount,
            ];

            $notificationService->create('order_placed_admin', $order->user_id, $notificationData, 1);
            $notificationService->create('order_placed', $order->user_id, $notificationData, 0);

            DB::commit();

            return response()->json(['message' => 'Order placed successfully!', 'order_id' => $order->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Something went wrong!', 'error' => $e->getMessage()], 500);
        }
    }

    public function order_success($order_id){
        $data['order'] = Order::query()->findOrFail($order_id);
        if(auth()->user()->cart){
            auth()->user()->cart->delete();
        }

        return view('prime_user.product_purchase.order_success', $data);
    }

}
