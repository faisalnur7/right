@extends('layouts.admin_master')

@section('title', 'Order List')
@section('page_title', 'Order List')

@section('contents')
    <div class="container-fluid">

        {{-- Success Alert --}}
        @if (session('success'))
            <div class="float-right-bottom alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Filter Form --}}
        <div class="card border-0 shadow-sm rounded mb-4">
            <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h4 class="mb-0">Filter Orders</h4>
            </div>
            <div class="card-body">
                @include('admin.orders._filter_forms')
            </div>
        </div>

        {{-- Order Table --}}
        <div class="card border-0 shadow-sm rounded">
            <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h4 class="mb-0">All Orders</h4>
            </div>

            <div class="card-body px-0 pb-4 pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Invoice</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Subtotal</th>
                                <th>Shipping Charge</th>
                                <th>Total</th>
                                <th>Payment Method</th>
                                <th>Date</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="font-weight-bold">{{ $order->order_tracking_number }}</td>
                                    <td>{{ $order->user->name ?? '-' }}</td>
                                    <td>
                                        <span class="badge text-white"
                                            style="background: {{ \App\Models\Order::ORDER_STATUS_PRIME[$order->status]['color'] }}">
                                            {{ \App\Models\Order::ORDER_STATUS_PRIME[$order->status]['label'] ?? ucfirst($order->status) }}
                                        </span>
                                    </td>
                                    <td>৳{{ number_format($order->subtotal, 2) }}</td>
                                    <td>৳{{ number_format($order->shipping_charge, 2) }}</td>
                                    <td>৳{{ number_format($order->total, 2) }}</td>
                                    <td>{{ $order->is_cod ? 'Cash On Deliver' : $order->paymentOption->name }}</td>
                                    <td>{{ $order->created_at->format('d M, Y') }}</td>
                                    <td class="text-center">
                                        @if ($order->status != \App\Models\Order::REJECTED)
                                            <a href="javascript:void(0);"
                                                class="btn btn-outline-info btn-sm rounded view-order-btn"
                                                data-id="{{ $order->id }}" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <a href="#"
                                                class="btn btn-outline-secondary btn-sm rounded print-invoice-btn"
                                                onclick="openInvoicePopup(event, '{{ route('order.print_invoice', $order->id) }}')"
                                                title="Print Invoice">
                                                <i class="fas fa-print"></i>
                                            </a>

                                            @if ($order->status != \App\Models\Order::COMPLETED)
                                            <!-- Reject button -->
                                            <button type="button"
                                                class="btn btn-outline-danger btn-sm rounded reject-order-btn"
                                                data-id="{{ $order->id }}" title="Reject">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @endif
                                            <!-- Hidden form -->
                                            <form id="reject-form-{{ $order->id }}"
                                                action="{{ route('order.update.status') }}" method="POST"
                                                style="display:none;">
                                                @csrf
                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                <input type="hidden" name="status"
                                                    value="{{ \App\Models\Order::REJECTED }}">
                                            </form>
                                        @else
                                            <span class="text-danger fw-bold">Rejected</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Order Details Modal -->
                    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header bg-dark text-white">
                                    <h5 class="modal-title" id="orderDetailsLabel">Order Details</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close">&times;</button>
                                </div>
                                <div class="modal-body" id="orderDetailsContent">
                                    <div class="text-center">
                                        <div class="spinner-border text-dark" role="status">
                                            <span class="sr-only">Loading...</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-close"
                                        data-bs-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- End Modal -->
                </div>
                {{-- Pagination --}}
                @if ($orders instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-3 px-4">
                        {{ $orders->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>
        </div>
    </div>


    <script>
        $(function() {
            // View Order Details
            $(document).on('click', '.view-order-btn', function() {
                let orderId = $(this).data('id');
                $('#orderDetailsModal').modal('show');

                $('#orderDetailsContent').html(`
                    <div class="text-center p-5">
                        <div class="spinner-border text-dark" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                `);

                $.ajax({
                    url: "{{ route('order.show') }}",
                    method: 'GET',
                    data: {
                        order_id: orderId
                    },
                    success: function(response) {
                        console.log(response.html)
                        $('#orderDetailsContent').html(response);
                    },
                    error: function() {
                        $('#orderDetailsContent').html(
                            '<div class="float-right-bottom alert-danger">Failed to load order details.</div>'
                        );
                    }
                });
            });

            // Update Order Status
            $(document).on('click', '#updateOrderStatusBtn', function() {
                let formData = $('#orderStatusForm').serialize();
                $.ajax({
                    url: "{{ route('order.update.status') }}",
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function() {
                        $('#orderDetailsModal').modal('hide');
                        toastr.success('Order status updated.');
                        location.reload();
                    },
                    error: function() {
                        toastr.error('Failed to update order status.')
                    }
                });
            });

            $(".reject-order-btn").on("click", function() {
                let orderId = $(this).data("id");

                Swal.fire({
                    title: "Are you sure?",
                    text: "This order will be marked as REJECTED!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d33",
                    cancelButtonColor: "#3085d6",
                    confirmButtonText: "Yes, reject it!"
                }).then((result) => {
                    if (result.isConfirmed) {
                        $("#reject-form-" + orderId).submit();
                    }
                });
            });
        });

        function openInvoicePopup(e, url) {
            e.preventDefault(); // Prevent normal navigation

            const width = 1024;
            const height = 900;
            const left = 0;
            const top = 0;
            window.open(
                url,
                '_blank',
                `width=${width},height=${height},top=${top},left=${left},resizable=yes,scrollbars=yes`
            );
        }
    </script>
@endsection
