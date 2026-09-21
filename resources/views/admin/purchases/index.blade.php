@extends('layouts.admin_master')

@section('title', 'Purchase History')
@section('page_title', 'Purchase History')

@section('contents')
    <div class="container-fluid">

        {{-- Success Alert --}}
        @if (session('success'))
            <div class="float-right-bottom alert alert-success shadow-sm rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter Form (Optional) --}}
        <div class="card border-0 shadow-sm rounded mb-4">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h4 class="mb-0">Filter Purchase History</h4>
            </div>
            <div class="card-body">
                @include('admin.purchases._filter_form') {{-- Create this partial for filtering if needed --}}
            </div>
        </div>

        {{-- Purchase History Table --}}
        <div class="card border-0 shadow-sm rounded">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h4 class="mb-0">All Purchases</h4>
                <div class="ml-auto">
                    <a href="{{ route('purchase.create') }}" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus"></i> Add New Purchase
                    </a>
                </div>
            </div>

            <div class="card-body px-0 pb-4 pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Invoice No</th>
                                <th>Supplier</th>
                                <th>Subtotal</th>
                                <th>Discount</th>
                                <th>Delivery Charge</th>
                                <th>Grand Total</th>
                                <th>Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($purchases as $purchase)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="font-weight-bold">{{ $purchase->reference_no }}</td>
                                    <td>{{ $purchase->supplier->company_name ?? 'N/A' }}</td>
                                    <td>৳{{ number_format($purchase->sub_total, 2) }}</td>
                                    <td>৳{{ number_format($purchase->discount_value, 2) }}</td>
                                    <td>৳{{ number_format($purchase->delivery_charge, 2) }}</td>
                                    <td>৳{{ number_format($purchase->total_amount, 2) }}</td>
                                    <td>{{ $purchase->purchase_date }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('purchase.show', $purchase->id) }}"
                                            class="btn btn-outline-info btn-sm rounded" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No purchase records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="pagination-container flex justify-center mt-0">
                    <div class="flex items-center space-x-1 [&_svg]:w-4 [&_svg]:h-4">
                        {{ $purchases->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
