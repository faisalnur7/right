<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PrimeTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\NotificationService;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['user', 'paymentOption'])
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('status', $request->status);
            })
            ->when($request->filled('from_date'), function ($query) use ($request) {
                return $query->whereDate('created_at', '>=', $request->from_date);
            })
            ->when($request->filled('to_date'), function ($query) use ($request) {
                return $query->whereDate('created_at', '<=', $request->to_date);
            })
            ->when($request->filled('order_no'), function ($query) use ($request) {
                return $query->where('order_tracking_number', $request->order_no);
            })
            ->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Request $request)
    {
        $order = Order::with(['user', 'paymentOption', 'items'])->findOrFail($request->order_id);
        return view('admin.orders._details', compact('order'))->render();
    }


    public function update_status(Request $request)
    {

        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'status'   => 'required|integer'
        ]);

        $status = (int) $request->status;
        $now    = Carbon::now();

        DB::transaction(function () use ($request, $status, $now) {
            $order = Order::lockForUpdate()->findOrFail($request->order_id);

            // Define status → timestamp mapping
            $timestamps = [
                Order::CONFIRMED  => 'start_processing_at',
                Order::PROCESSING => 'packaged_at',
                Order::SHIPPED    => 'shipped_at',
                Order::COMPLETED  => 'completed_at',
            ];

            foreach ($timestamps as $key => $column) {
                if ($status >= $key && empty($order->{$column})) {
                    $order->{$column} = $now;

                    // Distribute commission once when CONFIRMED
                    if ($key == Order::CONFIRMED) {
                        $this->distribute_product_commission($order);
                    }
                }
            }

            $order->status = $status;
            $order->save();
            
            $formatted = Carbon::parse($now)->format('jS F, Y \a\t g:i A');

            $notificationService =  new NotificationService();
            $notificationService->create('order_status_change', $order->user_id, [
                'order_id' => $order->order_tracking_number,
                'amount' => $order->total,
                'status' => Order::ORDER_STATUS_FILTER[$status],
                'time' => $formatted
            ], 0);
        });

        if ($request->ajax()) {
            return response()->json(['message' => 'Status updated']);
        } else {
            return redirect()->back();
        }
    }

    public function print_invoice($id){
        $order = Order::with(['user', 'paymentOption'])->findOrFail($id);

        $groupedItems = $order->items->groupBy('product_id')
                                ->map(function ($group) {
                                    $first = $group->first();
                                    return (object)[
                                        'product'     => $first->product,
                                        'quantity'    => $group->sum('quantity'),
                                        'unit_price'  => $first->unit_price, // assuming unit price is same
                                        'total_price' => $group->sum(function ($item) {
                                            return $item->unit_price * $item->quantity;
                                        }),
                                    ];
                                })
                                ->values();
        return view('admin.orders.invoices', compact('order', 'groupedItems'));
    }

    public function distribute_product_commission(Order $order)
    {
        $user = $order->user;
        $referenceUser = $user->referenceUser;
        $referenceUserOfReferrer = $user->reference_user_of_referrer;
        $notificationService = new NotificationService();    

        // Skip if no referrers found
        if (!$referenceUser && !$referenceUserOfReferrer) {
            return;
        }

        $tier1Total = 0;
        $tier2Total = 0;

        foreach ($order->items as $item) {
            $quantity = $item->quantity;

            if (!empty($item)) {
                $totalCommission = $item->product->total_commission * $quantity ?? 0;
                $tier1 = $item->product->tier1_percentage ?? 0;
                $tier2 = $item->product->tier2_percentage ?? 0;
                $tier1Total += $this->calculateCommission($tier1, $totalCommission);
                $tier2Total += $this->calculateCommission($tier2, $totalCommission);
            }
        }

        $transaction1 = $this->creditUserWalletAndLogTransaction(
            $referenceUser,
            $tier1Total,
            $user->id,
            PrimeTransaction::DIRECT_PRODUCT_PURCHASE_COMMISSION
        );

        $transaction2 = $this->creditUserWalletAndLogTransaction(
            $referenceUserOfReferrer,
            $tier2Total,
            $user->id,
            PrimeTransaction::INDIRECT_PRODUCT_PURCHASE_COMMISSION
        );

        $notificationData1 = [
            'type' => 'Affiliate commission',
            'amount' => $tier1Total,
            'transaction' => $transaction1?->id
        ];

        $notificationData2 = [
            'type' => 'Affiliate commission',
            'amount' => $tier2Total,
            'transaction' => $transaction2?->id
        ];

        $notificationService->create('wallet_notification', $referenceUser->id, $notificationData1, 0);
        if(!empty($referenceUserOfReferrer)){
            $notificationService->create('wallet_notification', $referenceUserOfReferrer->id, $notificationData2, 0);            
        }

    }

    public function creditUserWalletAndLogTransaction($refUser, $amount, $sourceUserId, $type = null)
    {
        if (!$refUser || $amount <= 0) {
            return;
        }

        $wallet = $refUser->primeWallet;

        if ($wallet) {
            $wallet->balance += $amount;
            $wallet->save();

            $primeTransaction = PrimeTransaction::create([
                'user_id'        => $refUser->id,
                'type'           => $type,
                'source_user_id' => $sourceUserId,
                'amount_in'      => $amount,
                'amount_out'     => 0,
            ]);

            return $primeTransaction;
        }
    }

    protected function calculateCommission(float $percentage, float $total): float
    {
        return round(($percentage * $total) / 100, 2);
    }
}
