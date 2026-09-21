<ul class="nav nav-tabs" id="saleLogTab" role="tablist">
    @foreach ($saleLogs as $index => $log)
        <li class="nav-item" role="presentation">
            <button class="nav-link @if ($log->id == 1) active @endif" id="tab-{{ $log->id }}-tab"
                data-bs-toggle="tab" data-bs-target="#tab-{{ $log->id }}" type="button" role="tab"
                aria-controls="tab-{{ $log->id }}" aria-selected="{{ $log->id == 1 ? 'true' : 'false' }}">
                {{ $log->name }}
                <span class="badge badge-pill badge-info ml-2">
                    {{ count($log->products) }}
                </span>
            </button>
        </li>
    @endforeach
</ul>

<!-- Tab Content -->
<div class="tab-content mt-4" id="saleLogTabContent">
    @forelse ($saleLogs as $log)
        <div class="tab-pane fade @if ($log->id == 1) show active @endif" id="tab-{{ $log->id }}"
            role="tabpanel" aria-labelledby="tab-{{ $log->id }}-tab">

            <!-- Layout: Filter Sidebar + Products Grid -->
            <div class="d-flex" id="sidebarLayout">
                <!-- Products Grid -->
                <div class="flex-grow-1">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                        @foreach ($log->products as $product)
                            <div class="card bg-white shadow-sm">
                                <figure class="px-4 pt-10 flex justify-center">
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}"
                                        class="rounded-xl max-w-[350px] w-full" />
                                </figure>
                                <div class="card-body flex flex-col items-center text-center">
                                    <h2 class="card-title">{{ $product->name }}</h2>

                                    <div class="text-center">
                                        @if ($product->affiliate_price)
                                            <p class="text-gray-500 line-through text-sm">
                                                ৳{{ number_format($product->price, 2) }}
                                            </p>
                                            <p class="text-lg text-green-600 font-semibold">
                                                ৳{{ number_format($product->affiliate_price, 2) }}
                                            </p>
                                        @else
                                            <p class="text-lg text-black font-semibold">
                                                ৳{{ number_format($product->price, 2) }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="card-actions mt-3">
                                        <a data-sale_log_id="{{ $log->id }}"
                                            data-product_id="{{ $product->id }}"
                                            href="{{ route('add_to_cart', $product->id) }}"
                                            data-price="{{ $product->affiliate_price ?? $product->price }}"
                                            class="btn btn-primary bg-sky-800 atc_btn">
                                            <i class="fa fa-cart-plus"></i>
                                        </a>
                                        <a href="{{ route('product_details', [$product->id, $log->id]) }}"
                                            class="btn btn-info bg-amber-600">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="flex justify-center items-center">No product found.</div>
    @endforelse
</div>
