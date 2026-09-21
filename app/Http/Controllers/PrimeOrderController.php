<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class PrimeOrderController extends Controller
{
    public function prime_orders(){
        $data['orders'] = Order::query()->where('user_id', auth()->user()->id)->latest()->get();
        return view('prime_user.product_purchase.orders', $data);
    }

    public function prime_order_details($id){
        $data['order'] = Order::query()->findOrFail($id);

        $data['groupedItems'] = $data['order']->items->groupBy('product_id')
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
        return view('prime_user.product_purchase.order_details', $data);
    }
}
