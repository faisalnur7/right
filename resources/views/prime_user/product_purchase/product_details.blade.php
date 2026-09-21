@extends('layouts.master')

@section('title', 'Product Details')
@section('page_title', 'Product Details')

@section('contents')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <div class="card shadow-sm p-3">
                <div class="row">
                    <div class="col-md-5">

                        {{-- Image Slider Start --}}
                        @php
                            $galleryImages = json_decode($product->gallery_images, true);
                        @endphp

                        <div id="productImageSlider" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">

                                @if (!empty($galleryImages))
                                    @foreach ($galleryImages as $index => $imagePath)
                                        <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                            <img src="{{ asset($imagePath) }}" class="d-block w-100 rounded" alt="{{ $product->name }}">
                                        </div>
                                    @endforeach
                                @else
                                    <div class="carousel-item active">
                                        @if ($product->image && file_exists(public_path($product->image)))
                                            <img src="{{ asset($product->image) }}" class="d-block w-100 rounded" alt="{{ $product->name }}">
                                        @else
                                            <img src="https://via.placeholder.com/300x300?text=No+Image" class="d-block w-100 rounded" alt="No Image">
                                        @endif
                                    </div>
                                @endif

                            </div>

                            @if (!empty($galleryImages) && count($galleryImages) > 1)
                                <a class="carousel-control-prev" href="#productImageSlider" role="button" data-slide="prev">
                                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Previous</span>
                                </a>
                                <a class="carousel-control-next" href="#productImageSlider" role="button" data-slide="next">
                                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                    <span class="sr-only">Next</span>
                                </a>
                            @endif
                        </div>
                        {{-- Image Slider End --}}

                    </div>

                    <div class="col-md-7">
                        <div class="card-body">
                            <div class="row">
                                <h2 class="card-title mb-3 text-bold text-xl">{{ $product->name }}</h2>
                            </div>

                            <div class="row">
                                <h4 class="text-primary mb-3">BDT {{ number_format($product->price, 2) }}</h4>
                            </div>

                            <p class="mb-1"><strong>SKU:</strong> {{ $product->sku ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Sub Category:</strong> {{ $product->subCategory->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Brand:</strong> {{ $product->brand->name ?? 'N/A' }}</p>
                            <p class="mb-1"><strong>Stock:</strong> {{ $product->stock }}</p>

                            <p class="mb-3">
                                <strong>Status:</strong>
                                @if ($product->status)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                            </p>

                            <hr>

                            <p class="mt-3">
                                <strong>Description:</strong><br>
                                {!! nl2br(e($product->description ?? 'No description provided.')) !!}
                            </p>

                            <div class="mt-4">
                                <a data-sale_log_id="{{ $log->id }}"
                                    data-product_id="{{ $product->id }}"
                                    href="{{ route('add_to_cart', $product->id) }}"
                                    data-price="{{ $product->affiliate_price ?? $product->price }}"
                                    class="btn btn-primary bg-sky-800 atc_btn">
                                    <i class="fa fa-cart-plus"></i> Add To Cart
                                </a>
                                <a href="{{ route('products') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
