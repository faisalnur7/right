<form method="GET" action="{{ route('order.list') }}">
    <div class="row g-3">
        <div class="col-md-3 col-lg-2">
            <label>Status</label>
            <select name="status" class="form-control">
                    <option value="">All</option>
                    @foreach (\App\Models\Order::ORDER_STATUS_FILTER as $key => $value)
                        <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
        </div>

        <!-- From Date -->
        <div class="col-md-3 col-lg-2">
            <label for="from_date" class="form-label">From Date</label>
            <input type="date" name="from_date" id="from_date" class="form-control"
                value="{{ request()->get('from_date') }}">
        </div>

        <!-- To Date -->
        <div class="col-md-3 col-lg-2">
            <label for="to_date" class="form-label">To Date</label>
            <input type="date" name="to_date" id="to_date" class="form-control"
                value="{{ request()->get('to_date') }}">
        </div>

        <!-- Order Number -->
        <div class="col-md-3 col-lg-2">
            <label for="order_no" class="form-label">Order No</label>
            <input type="text" name="order_no" id="order_no" class="form-control"
                value="{{ request()->get('order_no') }}">
        </div>

        <!-- Submit Button -->
        <div class="col-md-3 col-lg-2 align-self-end">
            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                <i class="fas fa-filter"></i> Filter
            </button>
        </div>

    </div>
</form>
