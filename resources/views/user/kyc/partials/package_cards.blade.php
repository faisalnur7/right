<div class="row prime-package-grid">
    @foreach ($packages as $prime_package)
        <div class="col-lg-4 col-md-4 col-sm-12 mb-4 d-flex">
            <label class="package-card card shadow-xl p-3 cursor-pointer w-100 d-flex flex-column justify-content-between"
                data-id="{{ $prime_package->id }}">
                <input type="radio" name="package" value="{{ $prime_package->id }}" class="d-none">

                <div>
                    <div class="package-card__topline"><span class="package-card__icon"><i class="fas fa-gem"></i></span><span class="package-card__label">Prime plan</span></div>
                    <h3 class="mb-2 text-black text-bold package-card__name">{{ $prime_package->name }}</h3>
                    <h5 class="mb-4 text-black package-card__subtitle">{{ $prime_package->sub_title }}</h5>

                    <div class="text-end fw-bold">
                        <span class="text-black text-xl text-bold package-card__price">৳{{ number_format($prime_package->amount - $prime_package->discount, 2) }}</span>
                        <span class="text-muted strikethrough me-2">৳{{ number_format($prime_package->amount, 2) }}</span>
                    </div>

                    <div class="text-end fw-bold mt-2 mb-4">
                        @if ($prime_package->discount > 0)
                            <span class="save_amount">Save ৳{{ number_format($prime_package->discount, 2) }}</span>
                        @endif
                        <span class="package-card__duration">For {{ $prime_package->duration }} days</span>
                    </div>

                    <button type="button" class="col-12 btn btn-lg btn-primary choose_btn text-lg text-bold mb-4 choose_package"
                        data-next="2" data-package_id="{{ $prime_package->id }}"
                        data-price="{{ number_format($prime_package->amount - $prime_package->discount, 2) }}"
                        data-duration="{{ $prime_package->duration }}"
                        data-package_name="{{ $prime_package->name }}">Choose Package</button>

                    <ul class="list-unstyled min-vh-25">
                        @forelse ($prime_package->features as $feature)
                            <li class="mb-2 feature-item">{{ $feature->name }}</li>
                        @empty
                            <li class="text-muted">No features available</li>
                        @endforelse
                    </ul>
                </div>
            </label>
        </div>
    @endforeach
</div>
