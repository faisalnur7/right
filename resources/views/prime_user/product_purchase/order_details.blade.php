@extends('layouts.master')

@section('title', 'My Order details')
@section('page_title', 'My Order details')

@section('contents')
    <div class="container-fluid">
        <div class="mx-auto bg-white p-6 rounded-lg shadow">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-xl font-semibold">Order ID: {{ $order->order_tracking_number }}</h1>
                    <p class="text-gray-500 text-md mt-2">{{ $order->created_at->format('F j, Y \a\t g:i a') }}</p>
                </div>
                {{-- <div class="space-x-2">
                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">Payment pending</span>
                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Unfulfilled</span>
                </div> --}}
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-[3fr_2fr] gap-4 mb-6">
                <div class="grid grid-cols-1">
                    <!-- Order Item -->
                    <div class="border rounded-lg p-4 mb-6">
                        <div class="flex justify-between items-center mb-2">
                            <h2 class="font-semibold">Order Item</h2>
                        </div>

                        @foreach ($groupedItems as $item)
                            <?php $product = $item->product; ?>
                            <div class="flex items-center gap-6 my-4">
                                <img src="{{ asset($product->image) }}" alt="Macbook Air"
                                    class="w-24 h-24 rounded border" />
                                <div class="flex-1">
                                    <h3 class="font-bold text-[18px]">{{ $product->name }}</h3>
                                    <p class="mt-1 text-[16px]">{{ $item->quantity }} x ৳ {{ $item->unit_price }} = ৳
                                        {{ $item->total_price }}</p>
                                </div>
                            </div>
                        @endforeach

                        <div class="flex justify-between items-center mb-2">
                            <h2 class="font-semibold">Order Summary</h2>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span>Subtotal</span><span>৳ {{ $order->subtotal }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Shipping</span><span>৳ {{ $order->shipping_charge }}</span>
                            </div>
                            <div class="border-t pt-2 flex justify-between font-semibold">
                                <span>Total</span><span>৳ {{ $order->total }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1">
                    <!-- Customer & Shipping Info -->
                    <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-1 gap-4 mb-6">
                        <div class="border p-4 rounded">
                            <h3 class="font-semibold mb-2">Customers</h3>
                            <p class="text-sm font-bold">{{ $order->user->name }}</p>
                            <p class="text-gray-500 text-sm">{{ $order->user->email }}</p>
                            <p class="text-gray-500 text-sm">{{ $order->user->phone }}</p>
                        </div>
                        <div class="border p-4 rounded">
                            <h3 class="font-semibold mb-2">Order Timeline</h3>

                            <div class="bg-white rounded-lg p-4 space-y-6 mx-auto">

                                <div class="relative border-l-2 border-gray-200">
                                    @php
                                        $billing_address = null;
                                        $shipping_address = null;

                                        $billingAddress = !empty($order->billing_address)
                                            ? json_decode($order->billing_address, true)
                                            : null;
                                        $shippingAddress = !empty($order->shipping_address)
                                            ? json_decode($order->shipping_address, true)
                                            : null;

                                        if ($billingAddress) {
                                            $district = App\Models\District::findOrFail($billingAddress['district_id']);
                                            $policeStation = App\Models\PoliceStation::findOrFail(
                                                $billingAddress['police_station_id'],
                                            );
                                            $postOffice = App\Models\PostOffice::findOrFail(
                                                $billingAddress['post_office_id'],
                                            );

                                            $districtName = !empty($district) ? $district->name : 'NA';
                                            $policeStationName = !empty($policeStation) ? $policeStation->name : 'NA';
                                            $postOfficeName = !empty($postOffice) ? $postOffice->name : 'NA';

                                            $billing_address =
                                                $billingAddress['address'] .
                                                ', P.O.-' .
                                                $postOfficeName .
                                                ', P.S- ' .
                                                $policeStationName .
                                                ', District- ' .
                                                $districtName .
                                                ', ' .
                                                $billingAddress['zip'];
                                        }

                                        if ($shippingAddress) {
                                            $district = App\Models\District::findOrFail(
                                                $shippingAddress['district_id'],
                                            );
                                            $policeStation = App\Models\PoliceStation::findOrFail(
                                                $shippingAddress['police_station_id'],
                                            );
                                            $postOffice = App\Models\PostOffice::findOrFail(
                                                $shippingAddress['post_office_id'],
                                            );

                                            $districtName = !empty($district) ? $district->name : 'NA';
                                            $policeStationName = !empty($policeStation) ? $policeStation->name : 'NA';
                                            $postOfficeName = !empty($postOffice) ? $postOffice->name : 'NA';

                                            $shipping_address =
                                                $shippingAddress['address'] .
                                                ', P.O.-' .
                                                $postOfficeName .
                                                ', P.S- ' .
                                                $policeStationName .
                                                ', District- ' .
                                                $districtName .
                                                ', ' .
                                                $shippingAddress['zip'];
                                        }

                                        $statuses = [
                                            [
                                                'status' => App\Models\Order::PENDING,
                                                'title' => 'Order Placed',
                                                'time' => !empty($order->created_at)
                                                    ? $order->created_at->format('F j, Y \a\t g:i a')
                                                    : null,
                                                'color' => 'bg-yellow-400',
                                                'icon' => 'fas fa-clock text-yellow-700',
                                            ],
                                            [
                                                'status' => App\Models\Order::CONFIRMED,
                                                'title' => 'Confirmed',
                                                'time' => !empty($order->start_processing_at)
                                                    ? $order->start_processing_at->format('F j, Y \a\t g:i a')
                                                    : null,
                                                'color' => 'bg-blue-400',
                                                'icon' => 'fas fa-cogs text-white',
                                            ],
                                            [
                                                'status' => App\Models\Order::PROCESSING,
                                                'title' => 'Packed',
                                                'time' => !empty($order->packaged_at)
                                                    ? $order->packaged_at->format('F j, Y \a\t g:i a')
                                                    : null,
                                                'color' => 'bg-purple-500',
                                                'icon' => 'fas fa-box text-purple-100',
                                            ],
                                            [
                                                'status' => App\Models\Order::SHIPPED,
                                                'title' => 'Shipped',
                                                'time' => !empty($order->shipped_at)
                                                    ? $order->shipped_at->format('F j, Y \a\t g:i a')
                                                    : null,
                                                'color' => 'bg-indigo-500',
                                                'icon' => 'fas fa-truck text-indigo-100',
                                            ],
                                            [
                                                'status' => App\Models\Order::COMPLETED,
                                                'title' => 'Delivered',
                                                'time' => !empty($order->completed_at)
                                                    ? $order->completed_at->format('F j, Y \a\t g:i a')
                                                    : null,
                                                'color' => 'bg-green-500',
                                                'icon' => 'fas fa-check-circle text-green-100',
                                            ],
                                        ];
                                    @endphp

                                    @foreach ($statuses as $status)
                                        <?php
                                        if (empty($status['time'])) {
                                            $status['color'] = 'bg-gray-400';
                                            $fontStyle = 'font-medium';
                                        } else {
                                            $fontStyle = 'font-bold';
                                        }
                                        ?>
                                        <div class="mb-10 ml-1 relative">
                                            <span
                                                class="absolute -left-6 flex items-center justify-center w-10 h-10 rounded-full ring-8 ring-white {{ $status['color'] }}">
                                                <i class="{{ $status['icon'] }} fa-lg"></i>
                                            </span>
                                            <h3 class="{{ $fontStyle }} text-gray-800 ml-8">{{ $status['title'] }}</h3>
                                            <time class="block text-sm text-gray-500 ml-8">{{ $status['time'] ?? 'Yet to be updated' }}</time>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Billing and Notes -->
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4 mb-6">
                        <div class="border p-4 rounded">
                            <h3 class="font-semibold mb-2">Billing address</h3>
                            <p>{{ $billing_address }}</p>
                        </div>

                        <div class="border p-4 rounded">
                            <h3 class="font-semibold mb-2">Shipping address</h3>
                            <p>{{ $shipping_address ?? 'Shipping and billing addresses are same' }}</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endsection
