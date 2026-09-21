@extends('layouts.master')

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

        @if (session('error'))
            <div class="alert float-right-bottom alert-danger shadow-sm rounded">
                {{ session('error') }}
            </div>
        @endif

        {{-- Filter Form --}}
        <div class="card border-0 shadow-sm rounded mb-4">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center"
                style="background: linear-gradient(to right, #252f51, #1c223a);">
                <h4 class="mb-0">Filter Products</h4>
            </div>
            <div class="card-body">
                @include('prime_user.stock.stock_partial._filter_form')
            </div>
        </div>
        @php
            $activeLogs = $stockLogs
                ->filter(function ($log) use ($activeReferenceSaleLogIds) {
                    return in_array($log->sale_log_id, $activeReferenceSaleLogIds);
                })
                ->unique('sale_log_id')
                ->values();

            $isPrime = auth()->user()->is_super_prime;
            $authUser = auth()->user();
        @endphp

        @if (!$isPrime)

            <div class="p-4 mb-4 text-md text-green-700 bg-green-100 rounded-lg" role="alert">
                <p class="font-semibold mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-green-500"></i>
                    Your reference user active status:
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($saleLogs as $log)
                        @php
                            $isActiveInTree = auth()->user()->isActiveOrPendingInSaleLog($log->id);
                            $isActiveInTreeStyle = '';
                            if ($isActiveInTree == 1) {
                                $isActiveInTreeStyle = 'bg-green-600';
                            }

                            if ($isActiveInTree == 2) {
                                $isActiveInTreeStyle = 'bg-orange-600';
                            }

                            if ($isActiveInTree == 0) {
                                $isActiveInTreeStyle = 'bg-red-600';
                            }

                        @endphp
                        <span
                            class="px-3 py-1 text-xs font-medium text-white {{ $isActiveInTreeStyle }} rounded-full shadow">
                            {{ $log->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="card">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center"
                style="background: linear-gradient(to right, #252f51, #1c223a);">
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
                                    $logUnit = auth()->user()->saleLogUnits->firstWhere('sale_log_id', $saleLogId);
                                    $isActiveOnSaleLog = $logUnit && $logUnit->is_active == 1;
                                    $isRefActive = in_array($saleLogId, $activeReferenceSaleLogIds);
                                    $alreadyRequested = App\Models\UserActiveRequest::where('user_id', $authUser->id)
                                        ->where('product_id', $item->product_id)
                                        ->where('sale_log_id', $item->sale_log_id)
                                        ->where('status',1)
                                        ->exists();

                                    $isActiveInTree = !empty(
                                        $authUser->binaryTreeNodes
                                            ->where('sale_log_id', $item->id)
                                            ->where('status', 1)
                                            ->where('active_in_sale_log', 1)
                                            ->first()
                                    )
                                        ? 1
                                        : 0;

                                    $isWaitingForDeactivation = !empty(
                                        $authUser->binaryTreeNodes
                                            ->where('sale_log_id', $item->id)
                                            ->where('status', 0)
                                            ->where('active_in_sale_log', 1)
                                            ->first()
                                    )
                                        ? 1
                                        : 0;

                                    $isInactiveInTree = !empty(
                                        $authUser->binaryTreeNodes
                                            ->where('sale_log_id', $item->id)
                                            ->where('status', 0)
                                            ->where('active_in_sale_log', 0)
                                            ->first()
                                    )
                                        ? 1
                                        : 0;

                                    $showUseButton = false;

                                    $latestNode = App\Models\BinaryTreeNode::query()
                                        ->where('user_id', $authUser->id)
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
                                            @if ($item->total_quantity && !$alreadyRequested)
                                                <button type="button"
                                                    class="btn btn-md btn-primary me-1 h-10 open-sale-modal flex gap-2 items-center justify-center"
                                                    id="triggerSaleModal" data-product-id="{{ $item->product_id }}"
                                                    data-sale-log-id="{{ $item->sale_log_id }}"
                                                    data-product-name="{{ $item->product->name }}"
                                                    data-sale-log-name="{{ $item->sale_log->name }}"
                                                    data-prduct-quantity="{{ $item->total_quantity }}"
                                                    data-bs-toggle="modal" data-bs-target="#saleModal" title="Make a Sale">
                                                    <i class="fas fa-shopping-cart"></i> Sale
                                                </button>
                                            @endif
                                            @if (
                                                (!$isActiveOnSaleLog && $item->total_quantity && $isRefActive) ||
                                                    ($isPrime && $item->total_quantity && $isInactiveInTree) ||
                                                    $showUseButton)
                                                <button type="button"
                                                    class="btn btn-md btn-dark open-use-modal h-10 flex gap-2 items-center justify-center"
                                                    data-bs-toggle="modal" data-bs-target="#useProductModal"
                                                    data-product-id="{{ $item->product_id }}"
                                                    data-sale-log-id="{{ $item->sale_log_id }}"
                                                    data-product-name="{{ $item->product->name }}"
                                                    data-sale-log-name="{{ $item->sale_log->name }}"
                                                    data-product-quantity="{{ $item->total_quantity }}"
                                                    data-unit="{{ $unit }}" data-price="{{ $price }}"
                                                    data-use_url="{{ route('use_product', [$item->product_id, $item->sale_log_id]) }}">
                                                    <i class="fas fa-cogs"></i> Use
                                                </button>
                                            @endif
                                            @if (!$isPrime && empty($isRefActive) && !$alreadyRequested)
                                                @if (!empty($referenceUser->phone))
                                                    <a href="tel:{{ $referenceUser->phone }}" type="button"
                                                        class="btn btn-md bg-green-500 flex gap-2 h-10 items-center justify-center text-white">
                                                        <i class="fas fa-phone-volume"></i>
                                                        <span class="whitespace-pre">Call to Referrer</span>
                                                    </a>
                                                @endif

                                                <form action="{{ route('user-active-requests.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="product_id"
                                                        value="{{ $item->product_id }}">
                                                    <input type="hidden" name="sale_log_id"
                                                        value="{{ $item->sale_log_id }}">
                                                    <input type="hidden" name="present_reference_user_id"
                                                        value="{{ $referenceUser->id ?? '' }}">

                                                    <button type="submit"
                                                        class="btn btn-md bg-blue-500 flex gap-2 items-center justify-center text-white hover:bg-blue-600">
                                                        <i class="fas fa-user-shield"></i>
                                                        <span class="whitespace-pre">Request Admin</span>
                                                    </button>
                                                </form>
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
    @include('prime_user.stock.modals.sale_modal')
    @include('prime_user.stock.modals.use_product_modal')
@endsection

@section('scripts')

    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('#modal_user_id').select2({
                dropdownParent: $('#saleModal'),
                width: '100%',
                placeholder: 'Choose a user'
            });

            const modal = new bootstrap.Modal(document.getElementById('saleModal'));
            // Populate modal with data when button is clicked
            $('.open-sale-modal').on('click', function() {
                var productId = $(this).data('product-id');
                var saleLogId = $(this).data('sale-log-id');
                var productName = $(this).data('product-name');
                var saleLogName = $(this).data('sale-log-name');
                var productQuantity = $(this).data('prduct-quantity');

                $('.modal_sale_button').attr('disabled', 'disabled');

                $('#modal_quantity').attr('max', productQuantity);
                $('#modal_product_id').val(productId);
                $('#modal_sale_log_id').val(saleLogId);
                $('#modal_product_quantity').val(productQuantity);
                $('#modal_product_name').text(productName);
                $('#modal_sale_log_name').text(saleLogName);

                $('#modal_user_id').val('').trigger('change');
                $('#modal_quantity').val('1');

                modal.show();
            });

            $('.cancel').on('click', function() {
                modal.hide();
                $('#saleForm')[0].reset();
                $('#modal_user_id').val('').trigger('change');
            });

            $('#modal_quantity').on('input', function() {
                let availableQuantity = parseInt($('#modal_product_quantity').val());
                let enteredQuantity = parseInt($(this).val());

                if (enteredQuantity > availableQuantity) {
                    $(this).val(availableQuantity);
                    toastr.error('Quantity exceeds available stock!')
                }
            });

            $('#modal_user_id').on('change', function() {
                let userId = $(this).val();
                let saleLogId = $('#modal_sale_log_id').val();

                if (!userId) return;

                $.ajax({
                    url: "{{ route('get_user_sale_info') }}",
                    type: "get",
                    data: {
                        user_id: userId,
                        sale_log_id: saleLogId,
                    },
                    beforeSend: function() {
                        // You can show a spinner or loader here
                        console.log("Fetching user sale info...");
                    },
                    success: function(response) {
                        if (!response) {
                            // Example: display returned data
                            Swal.fire({
                                icon: 'error',
                                title: 'Reference Information',
                                html: `Reference of this user is not active in the sale log.`
                            });
                            $('.modal_sale_button').attr('disabled', 'disabled');

                        } else {
                            $('.modal_sale_button').removeAttr('disabled');

                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
                    }
                });
            });

        });
    </script>
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
