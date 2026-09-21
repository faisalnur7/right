<form action="{{ route('purchase.list') }}" method="GET" class="row g-3">

    <div class="col-md-4">
        <label for="supplier" class="form-label">Supplier</label>
        <select name="supplier_id" id="supplier" class="form-control select2">
            <option value="">-- All Suppliers --</option>
            @foreach($suppliers as $supplier)
                <option value="{{ $supplier->id }}" 
                    {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                    {{ $supplier->company_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label for="date_from" class="form-label">Date From</label>
        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control">
    </div>

    <div class="col-md-4">
        <label for="date_to" class="form-label">Date To</label>
        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control">
    </div>

    <div class="col-12 d-flex justify-content-end mt-2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-filter"></i> Filter
        </button>
    </div>
</form>
