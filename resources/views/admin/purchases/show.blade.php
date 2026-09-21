@extends('layouts.admin_master')

@section('title', 'Purchase Invoice')
@section('page_title', 'Purchase Invoice')

@section('contents')
<div class="container-fluid">

    <div class="card border-0 shadow-sm rounded mb-4">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
            <h4 class="mb-0">Purchase Invoice</h4>
            <a href="{{ route('purchase.list') }}" class="btn btn-outline-light btn-sm ml-auto">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>

        <div class="card-body p-4">

            {{-- Header --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <h5><strong>Supplier Info</strong></h5>
                    <p class="mb-0">{{ $purchase->supplier->company_name ?? 'N/A' }}</p>
                    <p class="mb-0">{{ $purchase->supplier->address ?? '' }}</p>
                    <p class="mb-0">{{ $purchase->supplier->email ?? '' }}</p>
                    <p class="mb-0">{{ $purchase->supplier->phone ?? '' }}</p>
                </div>

                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <h5><strong>Invoice Details</strong></h5>
                    <p class="mb-0"><strong>Invoice No:</strong> {{ $purchase->reference_no }}</p>
                    <p class="mb-0"><strong>Purchase Date:</strong> {{ $purchase->purchase_date }}</p>
                    <p class="mb-0"><strong>Status:</strong> 
                        @if ($purchase->status)
                            <span class="badge badge-success">Completed</span>
                        @else
                            <span class="badge badge-warning">Pending</span>
                        @endif
                    </p>
                </div>
            </div>

            <hr>

            {{-- Product Items Table --}}
            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Product Name</th>
                            <th>SKU</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $subTotal = 0; @endphp
                        @foreach ($purchase->purchaseItems as $item)
                            @php $total = $item->quantity * $item->unit_price; @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td>{{ $item->product->sku ?? 'N/A' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>৳{{ number_format($item->unit_price, 2) }}</td>
                                <td>৳{{ number_format($total, 2) }}</td>
                            </tr>
                            @php $subTotal += $total; @endphp
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="row justify-content-end">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th>Subtotal:</th>
                            <td class="text-end">৳{{ number_format($subTotal, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Discount:</th>
                            <td class="text-end">৳{{ number_format($purchase->discount_value ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Delivery Charge:</th>
                            <td class="text-end">৳{{ number_format($purchase->delivery_charge ?? 0, 2) }}</td>
                        </tr>
                        <tr class="fw-bold border-top">
                            <th>Grand Total:</th>
                            <td class="text-end">৳{{ number_format($purchase->total_amount, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Footer --}}
            <div class="text-center mt-4">
                <button onclick="window.print()" class="btn btn-outline-dark">
                    <i class="fas fa-print"></i> Print Invoice
                </button>
            </div>

        </div>
    </div>
</div>
@endsection
