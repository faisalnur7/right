@foreach ($cart->items as $item)
    <div class="flex items-center justify-between border-b pb-3 pt-4">
        <div class="flex gap-6">
            <img src="{{ asset($item->product->image) }}" class="w-24 h-24 rounded object-cover"
                alt="{{ $item->product->name }}">
            <div>
                <h3 class="font-semibold text-lg">{{ $item->product->name }}</h3>
                <p class="mt-1 font-medium">BDT
                    {{ number_format($item->product->affiliate_price, 2) }}</p>
                <p class="text-green-600 text-sm mt-1">{{ $item->sale_log->name }}</p>
            </div>
        </div>

        <div class="flex rounded overflow-hidden gap-12" style="height: 48px;">
            <div class="inline-flex border rounded overflow-hidden">
                <button type="button" class="quantity-decrease px-3 text-gray-600 hover:bg-gray-100 font-extrabold"
                    data-id="{{ $item->id }}">−</button>

                <input type="text" class="quantity-input w-14 text-center font-extrabold border-0 outline-none"
                    value="{{ $item->quantity }}" min="1" data-id="{{ $item->id }}" data-price="{{ $item->price }}" />

                <button type="button" class="quantity-increase px-3 text-gray-600 hover:bg-gray-100 font-extrabold"
                    data-id="{{ $item->id }}">+</button>
            </div>

            <button class="text-gray-400 hover:text-red-500 text-lg remove-cart-item" data-id="{{ $item->id }}">
                &times;
            </button>
        </div>

    </div>
@endforeach
