<form method="GET" action="{{ route('product.list') }}">
    <div class="row g-3">

        <!-- Name -->
        <div class="col-md-3 col-lg-2">
            <label for="name" class="form-label">Product Name</label>
            <input type="text" name="name" id="name" class="form-control"
                   value="{{ request()->get('name') }}">
        </div>

        <!-- SKU -->
        <div class="col-md-3 col-lg-2">
            <label for="sku" class="form-label">SKU</label>
            <input type="text" name="sku" id="sku" class="form-control"
                   value="{{ request()->get('sku') }}">
        </div>

        <!-- Category -->
        <div class="col-md-3 col-lg-2">
            <label for="category" class="form-label">Category</label>
            <select name="category" id="category" class="form-control">
                <option value="">Select Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ request()->get('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Sub Category -->
        <div class="col-md-3 col-lg-2">
            <label for="sub_category" class="form-label">Sub Category</label>
            <select name="sub_category" id="sub_category" class="form-control">
                <option value="">Select Sub Category</option>
                @foreach ($subCategories as $subCategory)
                    <option value="{{ $subCategory->id }}"
                        {{ request()->get('sub_category') == $subCategory->id ? 'selected' : '' }}>
                        {{ $subCategory->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Brand -->
        <div class="col-md-3 col-lg-2">
            <label for="brand" class="form-label">Brand</label>
            <select name="brand" id="brand" class="form-control">
                <option value="">Select Brand</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->id }}"
                        {{ request()->get('brand') == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Price Min -->
        <div class="col-md-3 col-lg-2">
            <label for="price_min" class="form-label">Price (Min)</label>
            <input type="number" name="price_min" id="price_min" class="form-control"
                   value="{{ request()->get('price_min') }}" min="0">
        </div>

        <!-- Price Max -->
        <div class="col-md-3 col-lg-2">
            <label for="price_max" class="form-label">Price (Max)</label>
            <input type="number" name="price_max" id="price_max" class="form-control"
                   value="{{ request()->get('price_max') }}" min="0">
        </div>

        <!-- Sale Log -->
        <div class="col-md-3 col-lg-2">
            <label for="sale_log_id" class="form-label">Sale Log</label>
            <select name="sale_log_id" id="sale_log_id" class="form-control">
                <option value="">Select Sale Log</option>
                @foreach ($saleLogs as $saleLog)
                    <option value="{{ $saleLog->id }}"
                        {{ request('sale_log_id') == $saleLog->id ? 'selected' : '' }}>
                        {{ $saleLog->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Submit Button -->
        <div class="col-md-3 col-lg-2 align-self-end">
            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>

    </div>
</form>
