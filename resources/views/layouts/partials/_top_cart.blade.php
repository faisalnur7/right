@php
    $cart = auth()->check()
        ? \App\Models\PrimeCart::with(['items.product', 'items.sale_log'])
            ->where('user_id', auth()->id())
            ->first()
        : null;
@endphp

<a class="nav-link" data-toggle="dropdown" href="#" role="button">
    <i class="fa fa-shopping-cart"></i>
    @if ($cart && count($cart->items) > 0)
        <span class="item_count badge badge-pill badge-info ml-2 font-bold">
            {{ count($cart->items) }}
        </span>
    @endif
</a>

<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
    <span class="dropdown-item dropdown-header font-extrabold">Cart Items</span>
    <div class="dropdown-divider"></div>

    @if ($cart && $cart->items->count())
        <div class="scrollable-cart" style="max-height: 300px; overflow-y: auto;">
            @foreach ($cart->items as $item)
                <div class="dropdown-item d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <img src="{{ asset($item->product->image) }}" alt="{{ $item->product->name }}"
                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; margin-right: 10px;">
                        <div>
                            <strong>{{ $item->product->name }}</strong><br>
                            <small>৳{{ number_format($item->price, 2) }} × {{ $item->quantity }}</small><br>
                            <small class="text-muted">Sale Log: {{ $item->sale_log->name ?? 'N/A' }}</small>
                        </div>
                    </div>
                    <button class="ml-2 remove-cart-item" data-id="{{ $item->id }}"
                        title="Remove Item">
                        &times;
                    </button>
                </div>

                <div class="dropdown-divider"></div>
            @endforeach
        </div>
    @else
        <div class="dropdown-item text-muted">No items in cart.</div>
        <div class="dropdown-divider"></div>
    @endif

    <a href="{{route('cart')}}" class="dropdown-item dropdown-footer font-extrabold">See All on Cart page</a>
</div>
