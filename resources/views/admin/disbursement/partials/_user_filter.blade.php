<form method="GET" action="{{ route('disbursement_list') }}">
    <div class="d-flex flex-wrap align-items-end gap-3">
        <!-- From Business Day -->
        <div class="flex flex-col w-48">
            <label for="business_day" class="form-label">Business Day</label>
            <select name="business_day" id="business_day" class="form-control">
                <option value="">Select Business Day</option>
                @foreach ($businessDays as $dayNumber => $dayDate)
                    <option value="{{ $dayDate }}" {{ request('business_day') == $dayDate ? 'selected' : '' }}>
                        Day {{ $dayNumber }} ({{ $dayDate }})
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Per Page -->
        <div class="flex flex-col w-40">
            @include('form_components._per_page')
        </div>

        <!-- Buttons -->
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary shadow-sm">
                <i class="fas fa-filter"></i>
            </button>
            <a href="{{ route('disbursement_list') }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-undo"></i>
            </a>
        </div>
    </div>
</form>
