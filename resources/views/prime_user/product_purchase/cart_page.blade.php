@extends('layouts.master')

@section('title', 'Shopping Cart')
@section('page_title', 'Shopping Cart')


@section('contents')
    @php
//        $cart = auth()->check() ? \App\Models\PrimeCart::with(['items.product', 'items.sale_log'])->where('user_id', auth()->id())->first() : null;
    @endphp
    <div class="container-fluid">
        <div class="card" style="height: 100vhd">
            <div class="card-body px-0 pb-4 pt-0">
                <div class="max-w-8xl mx-auto p-6">
                    <h2 class="text-3xl font-bold mb-6">Shopping Cart</h2>
                    {{-- <div class="grid grid-cols-1 lg:grid-cols-3 gap-8"> --}}
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
                        <!-- Cart Items -->
                        <div class="lg:col-span-2 space-y-6">
                            <input type="item_count" class="hidden" value="@if($cart && $cart->items) {{$cart->items->count()}} @endif" id="item_count" />
                            @if ($cart && $cart->items->count())
                                <div id="cart_page_items">
                                    @include('prime_user.product_purchase.partials.cart_item')
                                </div>
                            @else
                                <p>No item in the cart.</p>
                            @endif

                            <div class="flex">
                                <a href="{{route('products')}}" class="btn btn-info bg-[#252f51] font-bold">Continue Shopping</a>
                            </div>
                        </div>

                        <!-- Sticky Order Summary -->
                        <div class="bg-[#252f51]  text-white p-6 rounded-lg shadow-md sticky top-20 h-fit">
                            <h3 class="text-xl font-bold mb-4">Order summary</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between text-lg">
                                    <span>Subtotal</span>
                                    <span>৳ <span id="subtotal">{{$subtotal}}</span></span>
                                </div>
                                <div class="flex justify-between text-lg">
                                    <span>Shipping estimate </span>
                                    <span>৳ <span id="shipping">{{$shipping}}</span></span>
                                </div>

                                <div class="border-t pt-2 flex justify-between font-bold text-lg">
                                    <span>Order total</span>
                                    <span>৳ <span id="total">{{$total}}</span></span>
                                </div>
                            </div>
                            <a href="{{route('prime_checkout')}}"
                                class="btn mt-4 w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded font-semibold transition">
                                Checkout
                            </a>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
@endsection

@section('scripts')

    <script>
        $(document).ready(function() {
            // Increase quantity
            $(document).on('click', '.quantity-increase', function() {
                let id = $(this).data('id');
                let $input = $(`.quantity-input[data-id='${id}']`);
                let newVal = parseInt($input.val()) + 1;
                $input.val(newVal);
                updateQuantity(id, newVal);
                updateSubtotal();
            });

            // Decrease quantity
            $(document).on('click', '.quantity-decrease', function() {
                let id = $(this).data('id');
                let $input = $(`.quantity-input[data-id='${id}']`);
                let newVal = Math.max(1, parseInt($input.val()) - 1);
                $input.val(newVal);
                updateQuantity(id, newVal);
                updateSubtotal();
            });

            // On manual input change
            $('.quantity-input').on('change', function() {
                let id = $(this).data('id');
                let qty = Math.max(1, parseInt($(this).val()));
                $(this).val(qty);
                updateQuantity(id, qty);
                updateSubtotal();
            });

            function updateQuantity(itemId, quantity) {
                $.ajax({
                    url: "{{ route('update_item_qty') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        quantity: quantity,
                        id: itemId,
                    },
                    success: function(response) {
                        if (response.success) {
                            console.log('Quantity updated');
                            $('#shopping_cart').html(response.view);
                        }
                    },
                    error: function(xhr) {
                        console.error('Failed to update:', xhr.responseText);
                    }
                });
            }

            function updateSubtotal() {
                let count_items = $('#item_count').val();
                let subtotal = 0;

                if(count_items > 0){
                    $('.quantity-input').each(function() {
                        const qty = parseInt($(this).val());
                        const price = parseFloat($(this).data('price'));
                        subtotal += qty * price;
                    });
                    $('#subtotal').text(subtotal.toFixed(2));

                    $.ajax({
                        url: "{{ route('get_shipping_cost')}}",
                        method: 'POST',
                        data: {
                            subtotal: subtotal,
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            const shipping = parseFloat(response.shipping);
                            const total = subtotal + shipping;

                            $('#shipping').text(shipping.toFixed(2));
                            $('#total').text(total.toFixed(2));
                        }
                    });
                }else{
                    $('#shipping').text(0);
                    $('#total').text(0);
                    $('#subtotal').text(0);
                }
            }

            $(document).on('input change', '.quantity-input', function() {
                updateSubtotal();
            });

            updateSubtotal();

        });
    </script>
@endsection
