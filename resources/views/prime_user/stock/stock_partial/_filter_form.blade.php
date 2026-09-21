<form method="GET" action="{{ route('prime_stock') }}">
    <div class="row g-3">
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
