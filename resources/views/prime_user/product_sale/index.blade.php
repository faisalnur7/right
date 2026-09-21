@extends('layouts.master')

@section('title', 'Product Sale History')
@section('page_title', 'Product Sale History')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center"
                style="background: linear-gradient(to right, #252f51, #1c223a);">
                <h3 class="card-title mb-0">📋 Product Sale History</h3>
            </div>

            <div class="card-body px-0 pb-3 pt-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Prime User</th>
                                <th>Mobile Number</th>
                                <th>Sale Log</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th>Used At</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $sale->product->name ?? 'N/A' }}</td>
                                    <td>{{ $sale->user->name ?? 'N/A' }}</td>
                                    <td>{{ $sale->user->phone ?? 'N/A' }}</td>
                                    <td>{{ $sale->saleLog->name ?? 'N/A' }}</td>
                                    <td>{{ $sale->quantity }}</td>
                                    <td>{{ number_format($sale->product->affiliate_price, 2) }}</td>
                                    <td>{{ number_format($sale->product->affiliate_price*$sale->quantity, 2) }}</td>
                                    <td>{{ $sale->used_at ? $sale->used_at->format('d M Y h:i A') : 'N/A' }}</td>
                                    <td>{{ $sale->remarks ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="14" class="text-center">No sale records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
