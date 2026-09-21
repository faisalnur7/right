@extends('layouts.admin_master')

@section('title', 'Edit Product')
@section('page_title', 'Edit Product')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title">Product Information</h3>
            </div>

            <div class="card-body">
                @if ($errors->any())
                    <div class="float-right-bottom alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Category</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Sub Category</label>
                            <select name="sub_category_id" class="form-control">
                                <option value="">Select Subcategory</option>
                                @foreach ($subcategories as $subcategory)
                                    <option value="{{ $subcategory->id }}"
                                        {{ old('sub_category_id', $product->sub_category_id) == $subcategory->id ? 'selected' : '' }}>
                                        {{ $subcategory->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Brand</label>
                            <select name="brand_id" class="form-control">
                                <option value="">Select Brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}"
                                        {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Product Name</label>
                            <input type="text" name="name" class="form-control"
                                value="{{ old('name', $product->name) }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Slug</label>
                            <input type="text" name="slug" class="form-control"
                                value="{{ old('slug', $product->slug) }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Unit</label>
                            <input type="text" name="unit" class="form-control"
                                value="{{ old('unit', $product->unit) }}">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Purchase Price</label>
                            <input type="number" name="purchase_price" class="form-control"
                                value="{{ old('purchase_price', $product->purchase_price) }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>MRP Price</label>
                            <input type="number" name="price" class="form-control"
                                value="{{ old('price', $product->price) }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Affiliate Price</label>
                            <input type="number" name="affiliate_price" class="form-control"
                                value="{{ old('affiliate_price', $product->affiliate_price) }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Stock</label>
                            <input type="number" name="stock" class="form-control"
                                value="{{ old('stock', $product->stock) }}">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Short Description</label>
                            <textarea name="short_description" class="form-control" rows="2">{{ old('short_description', $product->short_description) }}</textarea>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Product Image</label><br>
                            @if ($product->image)
                                <img src="{{ asset($product->image) }}" alt="Product Image" width="80" class="mb-2">
                            @endif
                            <input type="file" name="image" class="form-control-file">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Gallery Images (Multiple)</label><br>
                            @if ($product->gallery_images)
                                @foreach (json_decode($product->gallery_images) as $img)
                                    <img src="{{ asset($img) }}" alt="Gallery Image" width="60" class="mr-1 mb-2">
                                @endforeach
                            @endif
                            <input type="file" name="gallery_images[]" class="form-control-file" multiple>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="1" {{ old('status', $product->status) == '1' ? 'selected' : '' }}>
                                    Active</option>
                                <option value="0" {{ old('status', $product->status) == '0' ? 'selected' : '' }}>
                                    Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Featured</label>
                            <select name="featured" class="form-control">
                                <option value="1" {{ old('featured', $product->featured) == '1' ? 'selected' : '' }}>
                                    Yes</option>
                                <option value="0" {{ old('featured', $product->featured) == '0' ? 'selected' : '' }}>
                                    No</option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Weight</label>
                            <input type="text" name="weight" class="form-control"
                                value="{{ old('weight', $product->weight) }}">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Meta Data</label>
                            <textarea name="meta" class="form-control" rows="2">{{ old('meta', $product->meta) }}</textarea>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Sale Logs</label>

                            @php
                                $existingSaleLogs = $product->saleLogs->keyBy('id');
                            @endphp

                            @foreach ($saleLogs as $key => $saleLog)
                                @php
                                    $attached = $existingSaleLogs->has($saleLog->id);
                                    $pivot = $attached ? $existingSaleLogs[$saleLog->id]->pivot : null;
                                @endphp

                                <div class="border rounded p-3 mb-3">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox"
                                            name="sale_logs[{{ $saleLog->id }}][selected]" value="1"
                                            id="saleLog{{ $saleLog->id }}"
                                            {{ old("sale_logs.$saleLog->id.selected", $attached) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="saleLog{{ $saleLog->id }}">
                                            {{ $saleLog->name }}
                                        </label>
                                    </div>
                                    <div class="form-row">
                                        <div class="col">
                                            <input type="number" class="form-control"
                                                name="sale_logs[{{ $saleLog->id }}][unit]" placeholder="Sale Unit"
                                                value="{{ old("sale_logs.$saleLog->id.unit", $pivot->unit ?? '') }}">
                                        </div>
                                        <div class="col">
                                            <input type="number" class="form-control"
                                                name="sale_logs[{{ $saleLog->id }}][price]" placeholder="Sale Price"
                                                value="{{ old("sale_logs.$saleLog->id.price", $pivot->price ?? '') }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>

                        <div class="form-group col-md-12">
                            <!-- New Float Fields -->
                            <h2 class="text-lg font-extrabold py-4">Affiliate Commission</h2>
                            <div class="p-4 border rounded-md flex gap-4 justify-between">
                                <div class="form-group w-full">
                                    <label for="total_commission">Total Commission (TK)</label>
                                    <input type="number" step="0.01" name="total_commission" id="total_commission"
                                        class="form-control @error('total_commission') is-invalid @enderror"
                                        value="{{ old('total_commission', $product->total_commission) }}">
                                    @error('total_commission')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group w-full">
                                    <label for="tier1_percentage">Tier 1 Commission (%)</label>
                                    <input type="number" step="0.01" name="tier1_percentage" id="tier1_percentage"
                                        class="form-control @error('tier1_percentage') is-invalid @enderror"
                                        value="{{ old('tier1_percentage', $product->tier1_percentage) }}">
                                    @error('tier1_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group w-full">
                                    <label for="tier2_percentage">Tier 2 Commission (%)</label>
                                    <input type="number" step="0.01" name="tier2_percentage" id="tier2_percentage"
                                        class="form-control @error('tier2_percentage') is-invalid @enderror"
                                        value="{{ old('tier2_percentage', $product->tier2_percentage) }}">
                                    @error('tier2_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            {{-- Product Use Commission --}}
                            <h2 class="text-lg font-extrabold py-4">Leads Commission</h2>
                            <div class="p-4 border rounded-md flex gap-4 justify-between mb-4">

                                <div class="form-group w-full">
                                    <label for="total_use_commission">Total Commission (TK)</label>
                                    <input type="number" step="0.01" name="total_use_commission"
                                        id="total_use_commission"
                                        class="form-control @error('total_use_commission') is-invalid @enderror"
                                        value="{{ old('total_use_commission', $product->total_use_commission) }}">
                                    @error('total_use_commission')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Associate Commission --}}
                            <h2 class="text-lg font-extrabold py-4">Associate Commission</h2>
                            <div class="p-4 border rounded-md flex gap-4 justify-between mb-4">

                                <div class="form-group w-full">
                                    <label for="associate_commission">Total Commission (TK)</label>
                                    <input type="number" step="0.01" name="associate_commission"
                                        id="associate_commission"
                                        class="form-control @error('associate_commission') is-invalid @enderror"
                                        value="{{ old('associate_commission', $product->associate_commission) }}">
                                    @error('associate_commission')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Update Product</button>
                            <a href="{{ route('product.list') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function slugify(text) {
            return text
                .toString()
                .toLowerCase()
                .trim()
                .replace(/[\s\W-]+/g, '-') // replace spaces & special chars with -
                .replace(/^-+|-+$/g, ''); // trim leading & trailing hyphens
        }

        $(document).ready(function() {
            var slugManuallyEdited = false;

            // Auto-generate slug when product name is typed
            $('input[name="name"]').on('input', function() {
                if (!slugManuallyEdited) {
                    let slug = slugify($(this).val());
                    $('input[name="slug"]').val(slug);
                }
            });

            // Detect if the user edits the slug manually
            $('input[name="slug"]').on('input', function() {
                slugManuallyEdited = true;
            });

            // Auto-calculate sale log price when sale unit is typed
            $(document).on('input', 'input[name^="sale_logs["][name$="][unit]"]', function() {
                calculateSaleLogPrice($(this));
            });

            // When affiliate price changes, update all sale log prices
            $('input[name="affiliate_price"]').on('input', function() {
                $('input[name^="sale_logs"]').each(function() {
                    calculateSaleLogPrice($(this));
                });
            });


            function calculateSaleLogPrice(unitInput) {
                let unit = parseFloat(unitInput.val());
                let affiliatePrice = parseFloat($('input[name="affiliate_price"]').val());

                let priceInput = unitInput.closest('.form-row').find('input[name$="[price]"]');

                if (!isNaN(unit) && unit > 0 && !isNaN(affiliatePrice)) {
                    let calculatedPrice = (affiliatePrice / unit).toFixed(2);
                    priceInput.val(calculatedPrice);
                } else {
                    priceInput.val('');
                }

                priceInput.prop('readonly', true);
            }


            // Initial binding for dynamically generated elements (like sale logs)
            $(document).on('input', 'input[name^="sale_logs"][name$="[unit]"]', function() {
                calculateSaleLogPrice($(this));
            });
        });
    </script>
@endsection
