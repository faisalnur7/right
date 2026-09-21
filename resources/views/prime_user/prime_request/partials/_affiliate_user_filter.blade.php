<form method="GET" action="{{ route('approved_prime_requests') }}">
    <div class="row g-3">
        @php
            $saleLogs = App\Models\SaleLog::all();
        @endphp
        <!-- Total Users -->
        <div class="col-md-2 col-lg-2">
            <label class="form-label fw-bold">Total Users</label>
            <div class="form-control-plaintext">
                {{ $totalUsers ?? 0 }}
            </div>
        </div>

        <!-- Sale Log -->
        <div class="col-md-2 col-lg-2">
            <label for="sale_log_id" class="form-label">Sale Log</label>
            <select name="sale_log_id" id="sale_log_id" class="form-control">
                <option value="">Select Sale Log</option>
                @foreach ($saleLogs as $saleLog)
                    <option value="{{ $saleLog->id }}" {{ request('sale_log_id') == $saleLog->id ? 'selected' : '' }}>
                        {{ $saleLog->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Active Users -->
        <div class="col-md-2 col-lg-2">
            <label class="form-label fw-bold">Active Users</label>
            <div class="form-control-plaintext text-success">
                {{ $activeUsers ?? 0 }}
            </div>
        </div>

        <!-- Inactive Users -->
        <div class="col-md-2 col-lg-2">
            <label class="form-label fw-bold">Inactive Users</label>
            <div class="form-control-plaintext text-danger">
                {{ $inactiveUsers ?? 0 }}
            </div>
        </div>

        <!-- Per page -->
        <div class="col-md-3 col-lg-2">
            @include('form_components._per_page')
        </div>

        <!-- Submit Button -->
        <div class="col-md-3 col-lg-2 align-self-end">
            <button type="submit" class="btn btn-primary shadow-sm">
                <i class="fas fa-filter"></i>
            </button>
            <a href="{{ route('adminUserList') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-undo"></i>
            </a>
        </div>

    </div>
</form>
