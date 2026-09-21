@extends('layouts.admin_master')

@section('title', 'Add New Product')
@section('page_title', 'Add New Product')

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

                <form action="{{ route('product.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Category</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id') == $category->id ? 'selected' : '' }}>
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
                                        {{ old('sub_category_id') == $subcategory->id ? 'selected' : '' }}>
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
                                        {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                        {{ $brand->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Product Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Slug</label>
                            <input type="text" name="slug" class="form-control" value="{{ old('slug') }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Unit</label>
                            <input type="text" name="unit" class="form-control" value="{{ old('unit') }}">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Purchase Price</label>
                            <input type="number" name="purchase_price" class="form-control"
                                value="{{ old('purchase_price') }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>MRP Price</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price') }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Affiliate Price</label>
                            <input type="number" name="affiliate_price" class="form-control"
                                value="{{ old('affiliate_price') }}" required>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Stock</label>
                            <input type="number" name="stock" class="form-control" value="{{ old('stock') }}">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Short Description</label>
                            <textarea name="short_description" class="form-control" rows="2">{{ old('short_description') }}</textarea>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Product Image</label>
                            <input type="file" name="image" class="form-control-file">
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Gallery Images (Multiple)</label>
                            <input type="file" name="gallery_images[]" class="form-control-file" multiple>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Featured</label>
                            <select name="featured" class="form-control">
                                <option value="1" {{ old('featured') == '1' ? 'selected' : '' }}>Yes</option>
                                <option value="0" {{ old('featured') == '0' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>

                        <div class="col-md-6 form-group">
                            <label>Weight</label>
                            <input type="text" name="weight" class="form-control" value="{{ old('weight') }}">
                        </div>

                        <div class="col-md-12 form-group">
                            <label>Meta Data</label>
                            <textarea name="meta" class="form-control" rows="2">{{ old('meta') }}</textarea>
                        </div>

                        <div class="form-group col-md-12">
                            <label>Sale Logs</label>
                            <div class="row">
                                @foreach ($saleLogs as $key => $saleLog)
                                    <div class="col-md-4 py-3 mb-3">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox"
                                                name="sale_logs[{{ $saleLog->id }}][selected]" value="1"
                                                id="saleLog{{ $saleLog->id }}">
                                            <label class="form-check-label" for="saleLog{{ $saleLog->id }}">
                                                {{ $saleLog->name }}
                                            </label>
                                        </div>
                                        <div class="form-row p-0">
                                            <div class="col">
                                                <input type="number" class="form-control"
                                                    name="sale_logs[{{ $saleLog->id }}][unit]" placeholder="Sale Unit">
                                            </div>
                                            <div class="col">
                                                <input type="number" class="form-control"
                                                    name="sale_logs[{{ $saleLog->id }}][price]"
                                                    placeholder="Sale Price">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                        <div class="form-group col-md-12">
                            <!-- Purchase Commission -->
                            <h2 class="text-lg font-extrabold py-4 border-b-2">Affiliate Commission</h2>
                            <div class="p-4 border rounded-md flex gap-4 justify-between">
                                <div class="form-group w-full">
                                    <label for="total_commission">Total Commission (TK)</label>
                                    <input type="number" step="0.01" name="total_commission" id="total_commission"
                                        class="form-control @error('total_commission') is-invalid @enderror"
                                        value="{{ old('total_commission') }}">
                                    @error('total_commission')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group w-full">
                                    <label for="tier1_percentage">Tier 1 Commission (%)</label>
                                    <input type="number" step="0.01" name="tier1_percentage" id="tier1_percentage"
                                        class="form-control @error('tier1_percentage') is-invalid @enderror"
                                        value="{{ old('tier1_percentage') }}">
                                    @error('tier1_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group w-full">
                                    <label for="tier2_percentage">Tier 2 Commission (%)</label>
                                    <input type="number" step="0.01" name="tier2_percentage" id="tier2_percentage"
                                        class="form-control @error('tier2_percentage') is-invalid @enderror"
                                        value="{{ old('tier2_percentage') }}">
                                    @error('tier2_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Product Use Commission -->
                            <h2 class="text-lg font-extrabold py-4 border-b-2">Leads Commission</h2>
                            <div class="p-4 border rounded-md flex gap-4 justify-between">

                                <div class="form-group w-full">
                                    <label for="total_use_commission">Total Commission (TK)</label>
                                    <input type="number" step="0.01" name="total_use_commission"
                                        id="total_use_commission"
                                        class="form-control @error('total_use_commission') is-invalid @enderror"
                                        value="{{ old('total_use_commission') }}">
                                    @error('total_use_commission')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Associate Commission -->
                            <h2 class="text-lg font-extrabold py-4 border-b-2">Associate Commission</h2>
                            <div class="p-4 border rounded-md flex gap-4 justify-between">

                                <div class="form-group w-full">
                                    <label for="associate_commission">Total Commission (TK)</label>
                                    <input type="number" step="0.01" name="associate_commission"
                                        id="associate_commission"
                                        class="form-control @error('associate_commission') is-invalid @enderror"
                                        value="{{ old('associate_commission') }}">
                                    @error('associate_commission')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>



                        <div class="col-12">
                            <button type="submit" class="btn btn-success">Save Product</button>
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

        $(function () {
            let slugManuallyEdited = false;

            // Auto-generate slug when product name is typed
            $('input[name="name"]').on('input', function () {
                if (!slugManuallyEdited) {
                    $('input[name="slug"]').val(slugify($(this).val()));
                }
            });

            // Detect if slug is manually edited
            $('input[name="slug"]').on('input', function () {
                slugManuallyEdited = true;
            });

            // Delegate: When a sale unit changes, recalc its price
            $(document).on('input', 'input[name^="sale_logs"][name$="[unit]"]', function () {
                calculateSaleLogPrice($(this));
            });

            // When affiliate price changes, update all sale log prices
            $('input[name="affiliate_price"]').on('input', function () {
                $('input[name^="sale_logs"][name$="[unit]"]').each(function () {
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
        });
    </script>
@endsection
