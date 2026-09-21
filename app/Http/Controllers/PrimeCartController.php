<?php

namespace App\Http\Controllers;

use App\Models\PrimeCart;
use App\Models\PrimeCartItem;
use App\Models\ShippingRule;
use Illuminate\Http\Request;
use App\Traits\ShippingCalculator;

class PrimeCartController extends Controller
{
    use ShippingCalculator;

    public function cart(Request $request){
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $cart = PrimeCart::with(['items.product', 'items.sale_log'])->where('user_id', $user->id)->first();

        $subtotal = 0;

        if ($cart && $cart->items) {
            foreach ($cart->items as $item) {
                $subtotal += $item->price * $item->quantity;
            }
        }

        $shipping = $this->calculateShipping($subtotal); // Use your method

        $total = $subtotal + $shipping;

        return view('prime_user.product_purchase.cart_page', compact('cart', 'subtotal', 'shipping', 'total'));
    }
    public function add_to_cart(Request $request){
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'sale_log_id' => 'nullable|exists:sale_logs,id',
        ]);

        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Please log in to add items to cart.'
            ], 401);
        }

        // Get or create PrimeCart for the user
        $cart = PrimeCart::firstOrCreate(['user_id' => $user->id]);

        // Check if item already exists
        $cartItem = PrimeCartItem::where('prime_cart_id', $cart->id)
            ->where('product_id', $request->product_id)
            ->where('sale_log_id', $request->sale_log_id)
            ->first();

        if ($cartItem) {
            // Increase quantity
            $cartItem->increment('quantity');
        } else {
            // Add new item
            PrimeCartItem::create([
                'prime_cart_id' => $cart->id,
                'product_id' => $request->product_id,
                'sale_log_id' => $request->sale_log_id,
                'quantity' => 1,
                'price' => $request->price ?? 0,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully!',
            'html' => view('layouts.partials._top_cart')->render()
        ]);
    }

    public function remove_item(Request $request){
        $user = auth()->user();
        $item = PrimeCartItem::where('id', $request->item_id)
            ->whereHas('prime_cart', fn($q) => $q->where('user_id', $user->id))
            ->firstOrFail();

        $item->delete();

        $cart = PrimeCart::with(['items.product', 'items.sale_log'])->where('user_id', $user->id)->first();
        $view = view('layouts.partials._top_cart', compact('cart'))->render();
        $cartView = view('prime_user.product_purchase.partials.cart_item', compact('cart'))->render();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart.',
            'view' => $view,
            'cart_view' => $cartView
        ]);
    }


    public function update_item_qty(Request $request){
        $request->validate(['quantity' => 'required|integer|min:1']);
        $user = auth()->user();

        $item = PrimeCartItem::findOrFail($request->id);

        if ($item->prime_cart->user_id !== auth()->id()) {
            return response()->json(['success' => false], 403);
        }

        $item->quantity = $request->quantity;
        $item->save();

        $cart = PrimeCart::with(['items.product', 'items.sale_log'])->where('user_id', $user->id)->first();
        $view = view('layouts.partials._top_cart', compact('cart'))->render();


        return response()->json([
            'success' => true,
            'view' => $view
        ]);
    }

    public function get_shipping_cost(Request $request){
        $subtotal = $request->subtotal;
        $shipping = $this->calculateShipping($subtotal);
        return response()->json([
            'shipping' => $shipping
        ]);
    }

}
