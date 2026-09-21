@extends('layouts.admin_master')

@section('title', 'Stock Summary')
@section('page_title', 'Product Stock Summary')

@section('contents')
    <div class="container-fluid">
        {{-- Success Alert --}}
        @if (session('success'))
            <div class="alert float-right-bottom alert-success shadow-sm rounded">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter Form --}}
        <div class="card border-0 shadow-sm rounded mb-4">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h4 class="mb-0">Filter Products</h4>
            </div>
            <div class="card-body">
                @include('admin.user.partials._filter')
            </div>
        </div>
        <div class="card">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title mb-0">Product Stock Summary</h3>
            </div>

            <div class="card-body px-0 pb-3 pt-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Sale Log ID</th>
                                <th>Total Quantity</th>
                                <th>Unit</th>
                                <th>Price</th>
                                <th style="width: 200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stockLogs as $item)
                                @php

                                    $saleLogId = $item->sale_log->id;
                                    $matchedSaleLog = $item->product->saleLogs->firstWhere('id', $saleLogId);
                                    $unit = $matchedSaleLog->pivot->unit ?? 'N/A';
                                    $price = $matchedSaleLog->pivot->price ?? 0;

                                    // Check if current user is active under this sale_log
                                    $logUnit = $user->saleLogUnits->firstWhere('sale_log_id', $saleLogId);
                                    $isActiveOnSaleLog = $logUnit && $logUnit->is_active == 1;

                                    $showUseButton = false;

                                    $latestNode = App\Models\BinaryTreeNode::query()
                                        ->where('user_id', $user->id)
                                        ->where('sale_log_id', $saleLogId)
                                        ->latest()
                                        ->first();

                                    if (!empty($latestNode)) {
                                        if ($latestNode->status == 0 && $latestNode->active_in_sale_log == 0) {
                                            $showUseButton = true;
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                                    <td>{{ $item->sale_log->name ?? 'N/A' }}</td>
                                    <td>{{ $item->total_quantity }}</td>
                                    <td>{{ $unit }}</td>
                                    <td>৳{{ number_format($price, 2) }}</td>
                                    <td>
                                        <div class="d-flex justify-content-start gap-2">
                                            @if ((!$isActiveOnSaleLog && $item->total_quantity) || $showUseButton || empty($latestNode))
                                                <button type="button"
                                                    class="btn btn-md btn-dark open-use-modal flex gap-2 items-center justify-center"
                                                    data-bs-toggle="modal" data-bs-target="#useProductModal"
                                                    data-product-id="{{ $item->product_id }}"
                                                    data-sale-log-id="{{ $item->sale_log_id }}"
                                                    data-product-name="{{ $item->product->name }}"
                                                    data-sale-log-name="{{ $item->sale_log->name }}"
                                                    data-product-quantity="{{ $item->total_quantity }}"
                                                    data-unit="{{ $unit }}" data-price="{{ $price }}"
                                                    data-use_url="{{ route('adminUseProduct', [$user->id, $item->product_id, $item->sale_log_id]) }}">
                                                    <i class="fas fa-cogs"></i> Use
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No stock records found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @include('admin.user.modals.use_product_modal')
@endsection

@section('scripts')
    <script>
        const useModal = new bootstrap.Modal(document.getElementById('useProductModal'));

        $('.open-use-modal').on('click', function() {
            const productId = $(this).data('product-id');
            const saleLogId = $(this).data('sale-log-id');
            const productName = $(this).data('product-name');
            const saleLogName = $(this).data('sale-log-name');
            const quantity = $(this).data('product-quantity');
            const unit = $(this).data('unit');
            const price = $(this).data('price');
            const use_url = $(this).data('use_url');

            $('#use_modal_product_id').val(productId);
            $('#use_modal_sale_log_id').val(saleLogId);
            $('#use_modal_product_quantity').val(quantity);
            $('#use_modal_product_name').text(productName);
            $('#use_modal_sale_log_name').text(saleLogName);
            $('#use_modal_quantity').val('1');
            $('#use_modal_quantity').attr('max', quantity);
            $('#use_modal_unit').text(unit);
            $('#use_modal_price').text(`৳${parseFloat(price).toFixed(2)}`);
            $('#useProductForm').attr('action', use_url);

            useModal.show();
        });

        $('.cancel-use').on('click', function() {
            $('#useProductForm')[0].reset();
            useModal.hide();
        });

        $('#use_modal_quantity').on('input', function() {
            let maxQty = parseInt($('#use_modal_product_quantity').val());
            let qty = parseInt($(this).val());

            if (qty > maxQty) {
                $(this).val(maxQty);
                toastr.error('Quantity exceeds available stock!')
            }
        });
    </script>


@endsection
