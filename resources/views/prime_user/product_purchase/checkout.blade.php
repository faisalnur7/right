@extends('layouts.master')

@section('title', 'Checkout')
@section('page_title', 'Checkout')

@section('contents')
    <div class="container-fluid">
        <div class="card" style="height: 100vhd">
            <div class="card-body px-0 pb-4 pt-0">
                <div class="max-w-8xl mx-auto p-6">
                    <form action="{{ route('prime_checkout.place_order') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                            <!-- Billing Details -->
                            <div class="lg:col-span-2 space-y-6">
                                <a href="{{ route('cart') }}"
                                    class="btn btn-success bg-gray-600 hover:bg-gray-700 text-white font-semibold py-2 px-4 rounded">Back
                                    to the Cart page</a>

                                <div class="bg-white p-6 rounded-lg shadow-md border-2 border-cyan-300">
                                    @php
                                        $userAddresses = auth()->user()->addresses ?? collect();
                                        $userShippingAddresses = auth()->user()->shippingAddresses ?? collect();
                                    @endphp
                                    <h3 class="text-xl font-bold mb-4">Billing Details</h3>
                                    @if ($userAddresses->isEmpty())
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block mb-1 font-semibold">Full Name</label>
                                                <input type="text" name="billing_address[name]"
                                                    value="{{ old('name', auth()->user()->name ?? '') }}" required
                                                    class="form-input w-full border-gray-300">
                                            </div>

                                            <div>
                                                <label class="block mb-1 font-semibold">Phone</label>
                                                <input type="text" name="billing_address[phone]"
                                                    value="{{ old('phone', auth()->user()->phone ?? '') }}" required
                                                    class="form-input w-full border-gray-300">
                                            </div>

                                            <!-- New: District -->
                                            <div>
                                                <label class="block mb-1 font-semibold">District</label>
                                                <select name="billing_address[district_id]" id="district_id"
                                                    class="form-select select2 w-full" required>
                                                    <option value="">Select District</option>
                                                    @foreach ($districts as $id => $district)
                                                        <option value="{{ $id }}"
                                                            @if ($id == auth()->user()->kyc->present_district_id) selected @endif>
                                                            {{ $district }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block mb-1 font-semibold">Police Station</label>
                                                <select name="billing_address[police_station_id]" id="police_station_id"
                                                    class="form-select select2 w-full" required>
                                                    <option value="">Select Police Station</option>
                                                    @foreach ($policeStations as $id => $policeStation)
                                                        <option value="{{ $id }}"
                                                            @if (auth()->user()->kyc->present_police_station_id == $id) selected @endif>
                                                            {{ $policeStation }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block mb-1 font-semibold">Post Office</label>
                                                <select name="billing_address[post_office_id]" id="post_office_id"
                                                    class="form-select select2 w-full" required>
                                                    <option value="">Select Post Office</option>
                                                    @foreach ($postOffices as $id => $postOffice)
                                                        <option value="{{ $id }}"
                                                            @if (auth()->user()->kyc->present_post_office_id == $id) selected @endif>
                                                            {{ $postOffice }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block mb-1 font-semibold">Postal Code</label>
                                                <input type="text" name="billing_address[zip]"
                                                    value="{{ old('zip') }}" class="form-input w-full border-gray-300">
                                            </div>

                                            <div>
                                                <label class="block mb-1 font-semibold">Address</label>
                                                <input type="text" name="billing_address[address]"
                                                    value="{{ old('address') }}" class="form-input w-full border-gray-300">
                                            </div>

                                            <input type="hidden" name="billing_address[type]" value="1" />

                                        </div>
                                    @else
                                        {{-- Show Saved Addresses --}}
                                        <div id="saved-addresses-section">
                                            <h2 class="text-lg font-bold mb-2">Saved Addresses</h2>
                                            <form id="billing-address-form">
                                                <ul class="grid grid-cols-1 md:grid-cols-2 gap-3" id="address-list">
                                                    @foreach ($userAddresses as $address)
                                                        <li class="address-card-wrapper flex flex-col">
                                                            <label class="block cursor-pointer h-full">
                                                                <input type="radio" name="selected_address_id"
                                                                    value="{{ $address->id }}"
                                                                    class="hidden address-radio"
                                                                    @if ($loop->first) checked @endif />

                                                                <div
                                                                    class="address-card h-full p-4 border rounded-2xl shadow-[0_20px_40px_-10px_rgba(0,0,0,0.2)] transition-all flex flex-col justify-between relative bg-white">
                                                                    <div>
                                                                        <div><strong>Name:</strong> {{ $address->name }}
                                                                        </div>
                                                                        <div><strong>Phone:</strong> {{ $address->phone }}
                                                                        </div>
                                                                        <div><strong>Email:</strong> {{ $address->email }}
                                                                        </div>
                                                                        <div><strong>Address:</strong>
                                                                            {{ $address->address }}, {{ $address->city }},
                                                                            {{ $address->zip }}</div>
                                                                        <div>
                                                                            <strong>P/O -
                                                                            </strong>{{ optional($address->post_office)->name }},
                                                                            <strong>P/S-
                                                                            </strong>{{ optional($address->police_station)->name }},
                                                                            <strong>District:</strong>{{ optional($address->district)->name }}
                                                                        </div>
                                                                    </div>

                                                                    <!-- Delete Button -->
                                                                    <button type="button"
                                                                        class="mt-3 text-sm text-red-400 hover:text-red-600 delete-address-btn absolute top-0 right-3"
                                                                        data-id="{{ $address->id }}">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </form>




                                            {{-- Button to show the address form again --}}
                                            <div class="mt-4">
                                                <button type="button" id="add-new-address-btn"
                                                    class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded">
                                                    Add Another Billing Address
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Hidden address form --}}
                                        <div id="address-form-section" class="hidden mt-6">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block mb-1 font-semibold">Full Name</label>
                                                    <input type="text" name="billing_address[name]"
                                                        value="{{ old('name', auth()->user()->name ?? '') }}" required
                                                        class="form-input w-full border-gray-300">
                                                </div>
                                                <div>
                                                    <label class="block mb-1 font-semibold">Email</label>
                                                    <input type="email" name="billing_address[email]"
                                                        value="{{ old('email', auth()->user()->email ?? '') }}" required
                                                        class="form-input w-full border-gray-300">
                                                </div>
                                                <div>
                                                    <label class="block mb-1 font-semibold">Phone</label>
                                                    <input type="text" name="billing_address[phone]"
                                                        value="{{ old('phone', auth()->user()->phone ?? '') }}" required
                                                        class="form-input w-full border-gray-300">
                                                </div>

                                                <!-- New: District -->
                                                <div>
                                                    <label class="block mb-1 font-semibold">District</label>
                                                    <select name="billing_address[district_id]" id="district_id"
                                                        class="form-select select2 w-full" required>
                                                        <option value="">Select District</option>
                                                        @foreach ($districts as $id => $district)
                                                            <option value="{{ $id }}"
                                                                @if ($id == auth()->user()->kyc->present_district_id) selected @endif>
                                                                {{ $district }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block mb-1 font-semibold">Police Station</label>
                                                    <select name="billing_address[police_station]" id="police_station_id"
                                                        class="form-select select2 w-full" required>
                                                        <option value="">Select Police Station</option>
                                                        @foreach ($policeStations as $id => $policeStation)
                                                            <option value="{{ $id }}"
                                                                @if (auth()->user()->kyc->present_police_station_id == $id) selected @endif>
                                                                {{ $policeStation }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block mb-1 font-semibold">Post Office</label>
                                                    <select name="billing_address[post_office]" id="post_office_id"
                                                        class="form-select select2 w-full" required>
                                                        <option value="">Select Post Office</option>
                                                        @foreach ($postOffices as $id => $postOffice)
                                                            <option value="{{ $id }}"
                                                                @if (auth()->user()->kyc->present_post_office_id == $id) selected @endif>
                                                                {{ $postOffice }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block mb-1 font-semibold">Address</label>
                                                    <input type="text" name="billing_address[address]"
                                                        value="{{ old('address') }}"
                                                        class="form-input w-full border-gray-300">
                                                </div>

                                                <div>
                                                    <label class="block mb-1 font-semibold">City</label>
                                                    <input type="text" name="billing_address[city]"
                                                        value="{{ old('city') }}"
                                                        class="form-input w-full border-gray-300">
                                                </div>

                                                <div>
                                                    <label class="block mb-1 font-semibold">Postal Code</label>
                                                    <input type="text" name="billing_address[zip]"
                                                        value="{{ old('zip') }}"
                                                        class="form-input w-full border-gray-300">
                                                </div>

                                            </div>
                                        </div>
                                    @endif

                                    <!-- Checkbox -->
                                    <div class="mt-4">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" id="toggle-shipping"
                                                class="form-checkbox text-indigo-600">
                                            <span class="ml-2 text-gray-700 font-extrabold">Delivery and billing address
                                                are different</span>
                                        </label>
                                    </div>


                                    <!-- Title outside the grid -->
                                    <h3 class="text-xl font-bold mt-6 mb-2 hidden delivery_title">Delivery Details</h3>
                                    @if ($userShippingAddresses->isEmpty())
                                        <div id="shipping-address" class="grid grid-cols-1 md:grid-cols-2 gap-4 hidden">
                                            <div>
                                                <label class="block mb-1 font-semibold">Full Name</label>
                                                <input type="text" name="shipping_address[name]"
                                                    value="{{ old('shipping_name', auth()->user()->name ?? '') }}"
                                                    class="form-input w-full border-gray-300">
                                            </div>

                                            <div>
                                                <label class="block mb-1 font-semibold">Phone</label>
                                                <input type="text" name="shipping_address[phone]"
                                                    value="{{ old('shipping_phone', auth()->user()->phone ?? '') }}"
                                                    class="form-input w-full border-gray-300">
                                            </div>

                                            <div>
                                                <label class="block mb-1 font-semibold">District</label>
                                                <select name="shipping_address[district_id]"
                                                    class="form-select select2 w-full">
                                                    <option value="">Select District</option>
                                                    @foreach ($districts as $id => $district)
                                                        <option value="{{ $id }}"
                                                            @if ($id == auth()->user()->kyc->present_district_id) selected @endif>
                                                            {{ $district }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block mb-1 font-semibold">Police Station</label>
                                                <select name="shipping_address[police_station_id]"
                                                    class="form-select select2 w-full">
                                                    <option value="">Select Police Station</option>
                                                    @foreach ($policeStations as $id => $station)
                                                        <option value="{{ $id }}"
                                                            @if ($id == auth()->user()->kyc->present_police_station_id) selected @endif>
                                                            {{ $station }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div>
                                                <label class="block mb-1 font-semibold">Post Office</label>
                                                <select name="shipping_address[post_office_id]"
                                                    class="form-select select2 w-full">
                                                    <option value="">Select Post Office</option>
                                                    @foreach ($postOffices as $id => $office)
                                                        <option value="{{ $id }}"
                                                            @if ($id == auth()->user()->kyc->present_post_office_id) selected @endif>
                                                            {{ $office }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block mb-1 font-semibold">Postal Code</label>
                                                <input type="text" name="shipping_address[zip]"
                                                    value="{{ old('shipping_zip') }}"
                                                    class="form-input w-full border-gray-300">
                                            </div>

                                            <div>
                                                <label class="block mb-1 font-semibold">Address</label>
                                                <input type="text" name="shipping_address[address]"
                                                    value="{{ old('shipping_address') }}"
                                                    class="form-input w-full border-gray-300">
                                            </div>


                                            <input type="hidden" name="shipping_address[type]" value="2" />
                                        </div>
                                    @else
                                        <div id="saved-shipping-addresses-section" class="hidden">
                                            <h2 class="text-lg font-bold mb-2">Saved Delivery Addresses</h2>
                                            <form id="shipping-address-form">
                                                <ul class="grid grid-cols-1 md:grid-cols-2 gap-3"
                                                    id="shipping-address-list">
                                                    @foreach ($userShippingAddresses as $address)
                                                        <li class="delivery-address-card-wrapper flex flex-col">
                                                            <label class="block cursor-pointer h-full">
                                                                <input type="radio" name="selected_shipping_address_id"
                                                                    value="{{ $address->id }}"
                                                                    class="hidden address-radio"
                                                                    @if ($loop->first) checked @endif />

                                                                <div
                                                                    class="address-card h-full p-4 border rounded-2xl shadow-[0_20px_40px_-10px_rgba(0,0,0,0.2)] transition-all flex flex-col justify-between relative bg-white">
                                                                    <div>
                                                                        <div><strong>Name:</strong> {{ $address->name }}
                                                                        </div>
                                                                        <div><strong>Phone:</strong> {{ $address->phone }}
                                                                        </div>
                                                                        <div><strong>Email:</strong> {{ $address->email }}
                                                                        </div>
                                                                        <div><strong>Address:</strong>
                                                                            {{ $address->address }}, {{ $address->city }},
                                                                            {{ $address->zip }}</div>
                                                                        <div>
                                                                            <strong>P/O -
                                                                            </strong>{{ optional($address->post_office)->name }},
                                                                            <strong>P/S -
                                                                            </strong>{{ optional($address->police_station)->name }},
                                                                            <strong>District -
                                                                            </strong>{{ optional($address->district)->name }}
                                                                        </div>
                                                                    </div>

                                                                    <button type="button"
                                                                        class="mt-3 text-sm text-red-400 hover:text-red-600 delete-address-btn absolute top-0 right-3"
                                                                        data-id="{{ $address->id }}">
                                                                        <i class="fa fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </form>


                                        </div>

                                        {{-- Hidden shipping address form --}}
                                        <div id="shipping-address-form-section" class="hidden mt-6">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                <div>
                                                    <label class="block mb-1 font-semibold">Full Name</label>
                                                    <input type="text" name="shipping_address[name]"
                                                        value="{{ auth()->user()->name ?? '' }}"
                                                        class="form-input w-full border-gray-300">
                                                </div>
                                                <div>
                                                    <label class="block mb-1 font-semibold">Phone</label>
                                                    <input type="text" name="shipping_address[phone]"
                                                        value="{{ auth()->user()->phone ?? '' }}"
                                                        class="form-input w-full border-gray-300">
                                                </div>
                                                {{-- Repeat select options: District, Police Station, Post Office --}}
                                                {{-- Repeat Address, City, Zip --}}
                                                {{-- You can copy from the billing address form section --}}
                                            </div>
                                        </div>
                                    @endif
                                </div>


                                <div class="bg-white p-6 rounded-lg shadow-md  border-2 border-cyan-300">
                                    <h3 class="text-xl font-bold mb-4">Payment Method</h3>
                                    <div class="space-y-4">
                                        {{-- ✅ Cash On Delivery Checkbox --}}
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" id="is_cod" name="is_cod" value="1"
                                                class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                            <label for="is_cod" class="font-medium text-gray-800 text-base">
                                                Cash On Delivery
                                            </label>
                                        </div>

                                        {{-- ✅ Payment Methods Section --}}
                                        <div id="payment-section">
                                            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4"
                                                id="payment-options">
                                                @foreach ($paymentOptions as $index => $paymentOption)
                                                    <label
                                                        class="payment-option cursor-pointer border rounded-md overflow-hidden transition relative @if (old('payment_method', $index === 0 ? $paymentOption->id : '') == $paymentOption->id) ring-2 ring-indigo-500 @endif"
                                                        data-account="{{ $paymentOption->account_number }}">
                                                        <input type="radio" name="payment_method"
                                                            value="{{ $paymentOption->id }}"
                                                            class="sr-only payment-radio"
                                                            {{ old('payment_method', $index === 0 ? $paymentOption->id : '') == $paymentOption->id ? 'checked' : '' }}>
                                                        @if ($paymentOption->logo)
                                                            <img src="{{ asset($paymentOption->logo) }}"
                                                                alt="{{ $paymentOption->name }} logo"
                                                                class="w-full h-20 object-contain p-2">
                                                        @endif
                                                        <div
                                                            class="check-icon absolute top-2 right-2 text-blue-800 text-xl hidden">
                                                            ✔</div>
                                                    </label>
                                                @endforeach
                                            </div>

                                            {{-- Payment Details --}}
                                            <div id="payment-details"
                                                class="mt-6 p-4 border rounded-md bg-gray-50 hidden">
                                                <p class="mb-3 text-lg">
                                                    Please pay to the following account number:
                                                    <span id="account-number"
                                                        class="font-mono font-bold text-indigo-700"></span>
                                                </p>

                                                <div class="mb-4">
                                                    <label for="transaction_id" class="block font-medium mb-1">Transaction
                                                        ID</label>
                                                    <input type="text" id="transaction_id" name="transaction_id"
                                                        class="w-full border rounded px-3 py-2"
                                                        placeholder="Enter your transaction ID">
                                                </div>

                                                <div>
                                                    <label for="sender_phone_number" class="block font-medium mb-1">Sender
                                                        Phone Number</label>
                                                    <input type="text" id="sender_phone_number"
                                                        name="sender_phone_number" class="w-full border rounded px-3 py-2"
                                                        placeholder="Enter sender's phone number">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="bg-[#252f51] text-white p-6 rounded-lg shadow-md sticky top-20 h-fit">
                                <h3 class="text-xl font-bold mb-4">Order Summary</h3>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between text-lg">
                                        <span>Subtotal</span>
                                        <span>৳ <span id="subtotal">{{ $subtotal }}</span></span>
                                    </div>
                                    <div class="flex justify-between text-lg">
                                        <span>Shipping</span>
                                        <span>৳ <span id="shipping">{{ $shipping }}</span></span>
                                    </div>
                                    <div class="border-t pt-2 flex justify-between font-bold text-lg">
                                        <span>Total</span>
                                        <span>৳ <span id="total">{{ $total }}</span></span>
                                    </div>
                                </div>

                                <button type="button" id="place_order"
                                    class="mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded font-semibold transition">
                                    Place Order
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('layouts.partials._checkout_scripts')
@endsection
