@extends('layouts.master')

@section('title', 'Sold Product')
@section('page_title', 'Sold Product')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center"
                style="background: linear-gradient(to right, #252f51, #1c223a);">
                <h3 class="card-title mb-0">Sold Product</h3>
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
                            @forelse($soldProducts as $usedProduct)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $usedProduct->product->name ?? 'N/A' }}</td>
                                    <td>{{ $usedProduct->fromUser->name ?? 'N/A' }}</td>
                                    <td>{{ $usedProduct->fromUser->phone ?? 'N/A' }}</td>
                                    <td>{{ $usedProduct->saleLog->name ?? 'N/A' }}</td>
                                    <td>{{ $usedProduct->quantity }}</td>
                                    <td>{{ number_format($usedProduct->product->affiliate_price, 2) }}</td>
                                    <td>{{ number_format($usedProduct->product->affiliate_price * $usedProduct->quantity, 2) }}</td>
                                    <td>{{ $usedProduct->used_at ? $usedProduct->used_at->format('d M Y h:i A') : 'N/A' }}</td>
                                    <td>{{ $usedProduct->remarks ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No sale records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection