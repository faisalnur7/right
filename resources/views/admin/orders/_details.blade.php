<div id="printableArea">
    <div class="p-3">
        <form id="orderStatusForm-{{ $order->id }}" method="POST" action="{{ route('order.update.status') }}">
            @csrf
            <input type="hidden" name="order_id" value="{{ $order->id }}">

            @php
                $statuses = [
                    \App\Models\Order::PENDING => ['label' => 'Pending', 'icon' => 'fa-clock', 'class' => 'warning'],
                    \App\Models\Order::CONFIRMED => ['label' => 'Confirmed','icon' => 'fa-check-circle','class' => 'info',],
                    \App\Models\Order::PROCESSING => ['label' => 'Processing','icon' => 'fa-cogs','class' => 'primary',],
                    \App\Models\Order::SHIPPED => ['label' => 'Shipped', 'icon' => 'fa-truck', 'class' => 'secondary'],
                    \App\Models\Order::COMPLETED => ['label' => 'Completed','icon' => 'fa-check-double','class' => 'success',],
                ];
            @endphp

            <div class="btn-group w-100" role="group">
                @foreach ($statuses as $key => $status)
                    <button type="button" name="status" data-status="{{ $key }}"
                        class="status-btn btn btn-sm flex-fill d-flex flex-column align-items-center justify-content-center
                    {{ $order->status == $key ? 'btn-' . $status['class'] : 'btn-outline-' . $status['class'] }}"
                        title="{{ $status['label'] }}" {{-- Disable all buttons up to current status --}}
                        {{ $key <= $order->status ? 'disabled' : '' }}>
                        <i class="fas {{ $status['icon'] }} mb-1"></i>
                        <span class="small">{{ $status['label'] }}</span>
                    </button>
                @endforeach
            </div>
        </form>
        <h5 class="mb-3 text-lg py-1 font-bold">Order Summary</h5>
        <p><strong>Tracking Number:</strong> {{ $order->order_tracking_number }}</p>
        <p><strong>Customer:</strong> {{ $order->user->name ?? '-' }}</p>
        <p><strong>Payment Method:</strong> {{ $order->is_cod ? 'Cash On Deliver' : $order->paymentOption->name }}</p>
        <p><strong>Ordered At:</strong> {{ $order->created_at->format('d M, Y h:i A') }}</p>


        <hr class="py-2">
        <h3 class="text-lg font-bold">Order Items</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    @php
                        $total = number_format($item->unit_price * $item->quantity, 2);
                    @endphp
                    <tr>
                        <td>{{ $item->product->name ?? 'N/A' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>৳{{ number_format($item->unit_price, 2) }}</td>
                        <td>৳{{ $total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="text-right mt-3">
            <p><strong>Subtotal:</strong> ৳{{ number_format($order->subtotal, 2) }}</p>
            <p><strong>Shipping:</strong> ৳{{ $order->shipping_charge }}</p>
            <p><strong>Total:</strong> ৳{{ number_format($order->total, 2) }}</p>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $(".status-btn").on("click", function() {
            var status = $(this).data("status");
            var form = $(this).closest("form");

            // Optional: get button label for confirmation text
            var label = $(this).find("span").text();

            Swal.fire({
                title: "Are you sure?",
                text: "Change order status to '" + label + "'?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, change it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Add hidden input with the selected status
                    if (form.find('input[name="status"]').length === 0) {
                        form.append('<input type="hidden" name="status" value="' + status +
                            '">');
                    } else {
                        form.find('input[name="status"]').val(status);
                    }
                    form.submit();
                }
            });
        });
    });
</script>
