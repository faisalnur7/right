<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $order->order_tracking_number }}</title>
    @vite(['resources/css/app.css'])
</head>

<body class="bg-gray-100 p-6">

    <div class="container mx-auto">
        <div id="printableArea"
            class="max-w-4xl mx-auto bg-white p-8 rounded-md shadow-md text-sm text-gray-700 font-sans">
            <!-- Header -->
            <div class="flex items-center justify-center pb-4 w-full">
                <img src="{{ asset('assets/img/logo.webp') }}" alt="Company Logo" class="h-12 w-auto">
            </div>
            <div class="flex justify-between items-center border-b pb-4 mb-6">

                <div class="flex items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Invoice</h2>
                        <p class="text-sm text-gray-500">Tracking #: {{ $order->order_tracking_number }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p><span class="font-semibold">Date:</span> {{ $order->created_at->format('d M, Y h:i A') }}</p>
                    <p><span class="font-semibold">Status:</span>
                        {{ \App\Models\Order::ORDER_STATUS[$order->status] ?? ucfirst($order->status) }}
                    </p>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-1 text-gray-800">Customer Information</h3>
                <div class="grid grid-cols-2 gap-4">
                    <p><span class="font-medium">Name:</span> {{ $order->user->name ?? '-' }}</p>
                    <p><span class="font-medium">Payment Method:</span> {{ $order->paymentOption->name }}</p>
                </div>
            </div>

            <!-- Items Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border border-gray-300">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-4 py-2 border-b">Product</th>
                            <th class="px-4 py-2 border-b">Qty</th>
                            <th class="px-4 py-2 border-b">Unit Price</th>
                            <th class="px-4 py-2 border-b">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($groupedItems as $item)
                            <tr class="border-b">
                                <td class="px-4 py-2">{{ $item->product->name ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ $item->quantity }}</td>
                                <td class="px-4 py-2">৳{{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-4 py-2">৳{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="mt-6 text-right space-y-2">
                <p><span class="font-semibold">Subtotal:</span> ৳{{ number_format($order->subtotal, 2) }}</p>
                <p><span class="font-semibold">Shipping:</span> ৳{{ number_format($order->shipping_charge, 2) }}</p>
                <p class="text-lg font-bold text-gray-900"><span class="font-semibold">Total:</span>
                    ৳{{ number_format($order->total, 2) }}</p>
            </div>

            <!-- Footer -->
            <div class="mt-10 border-t pt-4 text-center text-xs text-gray-500">
                <p>Thank you for your order.</p>
                <p>This invoice was generated electronically and does not require a signature.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(() => {
                console.log('Page loaded');
                window.print();
            }, 1000);
        });
    </script>

    @vite(['resources/js/app.js'])
    
</body>

</html>
