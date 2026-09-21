@extends('layouts.master')

@section('title', 'My Orders')
@section('page_title', 'My Orders')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529); prime_table_bg">
                <h3 class="card-title mb-0">My Orders</h3>
            </div>

            <div class="card-body px-0 pb-4 pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Tracking Number</th>
                                <th>Payment Method</th>
                                <th>Transaction ID</th>
                                <th>Shipping Charge</th>
                                <th>Sub Total</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $order->order_tracking_number }}</td>
                                    <td>{{ $order->is_cod ? 'Cash On Deliver' : $order->paymentOption->name }}</td>
                                    <td>{{ $order->transaction_id }}</td>
                                    <td>{{ $order->shipping_charge }}</td>
                                    <td>{{ $order->subtotal }}</td>
                                    <td>{{ $order->total }}</td>

                                    <td>
                                        <span class="badge text-white" style="background: {{\App\Models\Order::ORDER_STATUS_PRIME[$order->status]['color']}}">
                                            {{ \App\Models\Order::ORDER_STATUS_PRIME[$order->status]['label'] ?? ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{route('prime_order_details',$order->id)}}" class="btn btn-dark btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center">No requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    {{-- <div class="mt-3">
                        {{ $orders->links('vendor.pagination.tailwind') }}
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
@endsection
