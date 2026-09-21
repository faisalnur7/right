@extends('layouts.master')

@section('contents')
    <div class="container-fluid mt-4">
        <div class="card card-primary">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title">Prime Affiliate Registration</h3>
            </div>

            <form id="kycForm" method="POST" action="{{ route('kyc.prime_store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <!-- STEP NAVIGATION -->
                    <ul class="nav nav-pills mb-3" id="form-steps">
                        @php
                            $activePrimeStatus = $primeStatus + 1;
                        @endphp
                        @if ($activePrimeStatus <= App\Models\User::PRIME_VERIFIED_STATUS_PAYMENT)
                            @foreach (App\Models\User::PRIME_STATUS as $key => $prime_status)
                                <li class="nav-item"><a
                                        class="nav-link @if ($prime_status <= $primeStatus) text-bold @endif @if ($prime_status == $activePrimeStatus) active @else disabled @endif"
                                        href="#" data-step="{{ $prime_status }}">Step {{ $prime_status }}:
                                        {{ $key }}</a></li>
                            @endforeach
                        @endif
                    </ul>

                    @php
                        $isActiveReferrer = count(auth()->user()->referenceUser->activePackage);
                        $isSuperReferrer = auth()->user()->referenceUser->is_super_prime;
                        $hasAdminRequest = !empty(auth()->user()->adminReferenceRequest?->where('status', 1)->first())
                            ? true
                            : false;
                        $adminApproved = auth()->user()->adminReferenceRequest?->first()?->status == 2 ? true : false;
                    @endphp

                    @if ($activePrimeStatus <= App\Models\User::PRIME_VERIFIED_STATUS_PAYMENT)
                        <!-- STEP 1: Reference -->
                        <div class="form-step step-1 @if ($adminApproved) isAdminApproved @endif"
                            data-form_step="1">
                            @if (!$hasAdminRequest)
                                <div class="form-group">
                                    {{-- {{dd(auth()->user()->referenceUser->activePackage)}} --}}
                                    {{-- {{dd(auth()->user()->reference_user_id)}} --}}

                                    <label for="referenceId">Reference ID</label>
                                    <select class="form-control" id="referenceId" name="reference_user_id"
                                        @if (!empty($primeRequest) || empty($isPrimeReferrer)) disabled @else required @endif>
                                        <option value="">Select Reference</option>
                                        @foreach ($affiliates as $affiliate)
                                            @if (auth()->user()->id !== $affiliate->id)
                                                <option value="{{ $affiliate->id }}"
                                                    @if (
                                                        (!empty($primeRequest) && $primeRequest->prime_id == $affiliate->id) ||
                                                            (auth()->user()->reference_user_id == $affiliate->id && ($isActiveReferrer || $isSuperReferrer))) selected @endif>
                                                    {{ $affiliate->kyc->affiliate_id }} - {{ $affiliate->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                @if (!empty($primeRequest))
                                    <button type="button" class="btn btn-danger cancel_request"
                                        data-request_id="{{ $primeRequest->id }}" data-next="2">Cancel Request</button>
                                @elseif(empty($isPrimeReferrer))
                                    <div class="emptyPrimeReferrer">
                                        <p class="text-red-500 font-bold pb-2">Your referrer is not a prime customer.</p>
                                        <a href="tel:{{ $referrer_phone }}" type="button" class="btn btn-success"
                                            title="Call to referrer"><i class="fa fa-phone"></i>&nbsp;</a>
                                        <button type="button" class="btn btn-primary change_referrer">Change the
                                            referrer</button>
                                        <button type="button" class="btn btn-info adminRequest">Admin</button>
                                    </div>
                                    <button type="button" class="btn btn-primary send_for_approval hidden"
                                        data-next="2">Send for Approval</button>
                                    <button type="button" class="btn btn-danger change_mind hidden"
                                        data-next="2">Cancel</button>
                                @else
                                    <button type="button" class="btn btn-primary send_for_approval" data-next="2">Send for
                                        Approval</button>
                                @endif
                            @else
                                <p class="text-green-500 font-bold pb-2 text-lg">You have requested admin for reference.
                                    Please wait for the admin response. Thanks</p>
                            @endif
                        </div>

                        <!-- STEP 2: Postal Info -->
                        <div class="form-step step-2" data-form_step="2">
                            <div class="d-flex gap-4 flex-wrap">
                                <div class="form-group" style="flex: 1; min-width: 200px;">
                                    <label for="district">District</label>
                                    <select class="form-control select2" name="district_id" id="district_id">
                                        <option value="">Select District</option>
                                        @foreach ($districts as $district)
                                            <option value="{{ $district->id }}">{{ $district->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" style="flex: 1; min-width: 200px;">
                                    <label for="thana">Thana</label>
                                    <select class="form-control select2" name="police_station_id" id="police_station_id">
                                        <option value="">Select Police Station</option>
                                    </select>
                                </div>
                                <div class="form-group" style="flex: 1; min-width: 200px;">
                                    <label for="post_office_id">Post Office</label>
                                    <select class="form-control select2" name="post_office_id" id="post_office_id">
                                        <option value="">Select Post Office</option>
                                    </select>
                                </div>
                                <div class="form-group" style="flex: 1; min-width: 200px;">
                                    <label for="postal_code">Postal Code</label>
                                    <input type="text" class="form-control" name="postal_code" id="postal_code"
                                        placeholder="Enter Postal Code" readonly>
                                </div>
                            </div>

                            <!-- Table before button -->
                            <!-- Fancy Styled Table -->
                            <div class="mt-4">
                                <table class="table table-bordered shadow-sm"
                                    style="table-layout: fixed; width: 100%; border-radius: 8px; overflow: hidden;">
                                    <tbody>
                                        <tr>
                                            <td
                                                style="border-right:none; width: 50%; font-weight: 600; background-color: #f8f9fa; padding: 12px; border-right: none;">
                                                District Associate</td>
                                            <td style="border-left:none;width: 50%; padding: 12px;">Nil</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-right:none; font-weight: 600; background-color: #f8f9fa; padding: 12px;">
                                                Thana Associate</td>
                                            <td style="border-left:none;padding: 12px;">Nil</td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-right:none; font-weight: 600; background-color: #f8f9fa; padding: 12px;">
                                                Postal Associate</td>
                                            <td style="border-left:none; padding: 12px;">Nil</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>


                            <button type="button" class="btn btn-primary mt-3 assign_postal_area" data-next="3">Assign to
                                Postal Area</button>
                        </div>

                        <!-- STEP 3: Nominee Info -->
                        <div class="form-step step-3" data-form_step="3">
                            <div class="form-group">
                                <label>Nominee Name</label>
                                <input type="text" class="form-control" name="nominee_name" required>
                            </div>
                            <div class="form-group">
                                <label>Relation</label>
                                <select name="relation" class="form-control" required>
                                    <option value="">-- Select Relation --</option>
                                    @foreach (\App\Models\User::RELATION as $label => $value)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- <div class="form-group">
                                <label>Document Type</label>
                                <select class="form-control" name="doc_type" required>
                                    <option value="nid">NID</option>
                                    <option value="passport">Passport</option>
                                    <option value="birth_certificate">Birth Certificate</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Document Number</label>
                                <input type="text" class="form-control" name="nominee_nid" required>
                            </div> --}}

                            <button type="button" class="btn btn-primary save_nominee" data-next="4">Next</button>
                        </div>

                        <!-- STEP 4: Package Selection -->
                        <div class="form-step step-4" data-form_step="4">
                            <div class="row d-flex align-items-stretch">
                                @foreach ($packages as $prime_package)
                                    <div class="col-lg-4 col-md-4 col-xs-12 col-sm-12 mb-4 d-flex">
                                        <label
                                            class="package-card card shadow-xl p-3 cursor-pointer w-100 d-flex flex-column justify-content-between"
                                            style="border: 2px solid transparent;" data-id="{{ $prime_package->id }}">
                                            <input type="radio" name="package" value="{{ $prime_package->id }}"
                                                class="d-none" required>

                                            <div>
                                                <h3 class="mb-2 text-black text-bold">{{ $prime_package->name }}</h3>
                                                <h5 class="mb-4 text-black">{{ $prime_package->sub_title }}</h5>

                                                <div class="text-end fw-bold">
                                                    <span
                                                        class="text-black text-xl text-bold">৳{{ number_format($prime_package->amount - $prime_package->discount, 2) }}</span>
                                                    <span
                                                        class="text-muted strikethrough me-2">৳{{ number_format($prime_package->amount, 2) }}</span>
                                                </div>

                                                <div class="text-end fw-bold mt-2 mb-4">
                                                    @if ($prime_package->discount > 0)
                                                        <span class="save_amount"> Save
                                                            ৳{{ number_format($prime_package->discount, 2) }}</span>
                                                    @endif

                                                    <span>For {{ $prime_package->duration }} days</span>
                                                </div>

                                                <button type="button"
                                                    class="col-12 btn btn-lg btn-primary choose_btn text-lg text-bold mb-4 choose_package"
                                                    data-next="5" data-package_id={{ $prime_package->id }}
                                                    data-price="{{ number_format($prime_package->discount, 2) }}"
                                                    data-duration="{{ $prime_package->duration }}"
                                                    data-package_name="{{ $prime_package->name }}">Choose Package</button>

                                                <ul class="list-unstyled min-vh-25">
                                                    @forelse ($prime_package->features as $feature)
                                                        <li class="mb-2 feature-item"> {{ $feature->name }}</li>
                                                    @empty
                                                        <li class="text-muted">No features available</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button" class="btn btn-primary choose_package_next" disabled>Next</button>

                        </div>

                        <!-- STEP 5: Payment -->
                        <div class="form-step step-5" data-form_step="5">
                            <div class="col-md-6 mt-4">
                                <span class="text-red"><strong>**Note: You can go back with previous button and change the
                                        selected package until completed the payment.</strong></span>
                                <label>Package Information</label>
                                <table class="table table-bordered shadow-sm"
                                    style="table-layout: fixed; width: 100%; border-radius: 8px; overflow: hidden;">
                                    <tbody>
                                        <tr>
                                            <td
                                                style="border-right:none; width: 50%; font-weight: 600; background-color: #f8f9fa; padding: 12px; border-right: none;">
                                                Name</td>
                                            <td style="border-left:none;width: 50%; padding: 12px;">
                                                <span class="package_name"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-right:none; font-weight: 600; background-color: #f8f9fa; padding: 12px;">
                                                Discount Price</td>
                                            <td style="border-left:none;padding: 12px;">
                                                <span class="package_price"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td
                                                style="border-right:none; font-weight: 600; background-color: #f8f9fa; padding: 12px;">
                                                Duration</td>
                                            <td style="border-left:none; padding: 12px;">
                                                <span class="package_duration"></span> Days
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <hr>

                            <div class="form-group ml-3">
                                <label>Payment Type</label><br>
                                @foreach (App\Models\PaymentOption::all() as $paymentOption)
                                    <div class="form-check form-check-inline">
                                        <input type="radio" class="form-check-input" name="payment_type"
                                            value="{{ $paymentOption->id }}" id="payment_{{ $paymentOption->id }}"
                                            data-account_number="{{ $paymentOption->account_number }}">
                                        <label class="form-check-label" for="payment_{{ $paymentOption->id }}">
                                            <img src="{{ asset($paymentOption->logo) }}" class="h-12" />
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="row ml-3 mb-3 d-none account_number_div">
                                <span class="text-red">
                                    <strong>Please pay with this number: <span class="account_number"></span></strong>
                                </span>
                            </div>
                            <div class="row ml-3 d-flex">
                                <div class="form-group col-md-6">
                                    <label>Transaction ID</label>
                                    <input type="text" class="form-control transaction_number"
                                        name="transaction_number" required>
                                </div>
                                <div class="form-group col-md-6">
                                    <label>Mobile Number</label>
                                    <input type="text" class="form-control transaction_mobile_number"
                                        name="transaction_mobile_number" required>
                                </div>
                            </div>

                            <div class="ml-3">
                                <button type="submit" class="btn btn-success finish_payment">Finish</button>
                                <button type="button" class="prev-step btn btn-secondary"
                                    data-prev="4">Previous</button>
                            </div>
                        </div>
                    @else
                        @if (auth()->user()->prime_verified == App\Models\User::PRIME_VERIFIED_STATUS_PAYMENT)
                            {{-- Your request is under process. --}}
                            <!-- Notification Alert -->
                            <div class="alert float-right-bottom alert-danger d-flex align-items-center" role="alert">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span>Your Prime membership request is under process.</span>
                            </div>
                            <!-- Information Card -->
                            <div class="card mb-4 shadow-sm border-info">
                                <div class="card-header bg-info text-white">
                                    <strong>Request Details</strong>
                                </div>
                                @php
                                    $referrer_id = '';
                                    $referrer_name = '';
                                    $requested_at = '';
                                    $approved_at = '';
                                    $affiliate_id = '';

                                    $authUser = auth()->user();
                                    if (!empty($authUser->prime_request)) {
                                        if (!empty($authUser->prime_request->prime)) {
                                            $referrer_id = $authUser->prime_request->prime->kyc->affiliate_id;
                                            $referrer_name = $authUser->prime_request->prime->name;
                                            $affiliate_id = $authUser->kyc->affiliate_id;
                                            $requested_at = $authUser->prime_request->created_at;
                                            $approved_at = $authUser->prime_request->updated_at;
                                        }
                                    }

                                @endphp
                                <div class="card-body">
                                    <table class="table table-bordered mb-0">
                                        <tr>
                                            <th>New Referrer ID</th>
                                            <td>{{ $referrer_id }}</td>
                                        </tr>
                                        <tr>
                                            <th>New Affiliate ID</th>
                                            <td>{{ $affiliate_id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Reference Name</th>
                                            <td>{{ $referrer_name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Requested At</th>
                                            <td>{{ \Carbon\Carbon::parse($requested_at)->format('d M Y, h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Approved At</th>
                                            <td>{{ \Carbon\Carbon::parse($approved_at)->format('d M Y, h:i A') }}</td>
                                        </tr>

                                        <tr>
                                            <th>District</th>
                                            <td>{{ $authUser->district->name }}</td>
                                        </tr>

                                        <tr>
                                            <th>Thana</th>
                                            <td>{{ $authUser->police_station->name }}</td>
                                        </tr>

                                        <tr>
                                            <th>Post Office</th>
                                            <td>{{ $authUser->post_office->name }}</td>
                                        </tr>

                                        <tr>
                                            <th>Post Code</th>
                                            <td>{{ $authUser->post_office->postcode }}</td>
                                        </tr>

                                        <tr>
                                            <th>Nominee Name</th>
                                            <td>{{ $authUser->nominee->nominee_name }}</td>
                                        </tr>

                                        <tr>
                                            <th>Relation</th>
                                            <td>{{ $authUser->nominee->relation }}</td>
                                        </tr>

                                        <tr>
                                            <th>Document Type</th>
                                            <td>{{ $authUser->nominee->doc_type }}</td>
                                        </tr>

                                        <tr>
                                            <th>Package Name</th>
                                            <td><strong>{{ $authUser->packages->first()->name }}</strong></td>
                                        </tr>

                                        <tr>
                                            <th>Package Duration</th>
                                            <td>{{ $authUser->packages->first()->duration }} Days</td>
                                        </tr>
                                        <tr>
                                            <th>Amount</th>
                                            <td>BDT. {{ $authUser->packages->first()->discount }}</td>
                                        </tr>

                                        <tr>
                                            <th>Payment Option</th>
                                            <td>{{ App\Models\PaymentOption::query()->findOrFail($authUser->packages->first()->pivot->payment_option_id)->name }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <th>Transaction ID</th>
                                            <td>{{ $authUser->packages->first()->pivot->transaction_number }}</td>
                                        </tr>

                                        <tr>
                                            <th>Transaction Mobile</th>
                                            <td>{{ $authUser->packages->first()->pivot->transaction_mobile_number }}</td>
                                        </tr>

                                    </table>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {

            $('.select2').select2({
                placeholder: "Please select"
            });

            let step = $("#form-steps .nav-link.active").attr('data-step');
            $('.form-step').each(function(index, item) {
                if ($(item).attr('data-form_step') !== step) {
                    $(item).addClass('d-none');
                }
            })

            $('.prev-step').on('click', function() {
                const prevStep = $(this).data('prev');
                $('.form-step').addClass('d-none');
                $('.step-' + prevStep).removeClass('d-none');

                $('#form-steps .nav-link').removeClass('active').addClass('disabled');
                $('#form-steps .nav-link[data-step="' + prevStep + '"]').removeClass('disabled').addClass(
                    'active');
            });

            $('.package-card').on('click', function() {
                $('.package-card').removeClass('selected');
                $(this).addClass('selected');
                $(this).find('input[type="radio"]').prop('checked', true);
            });

            function nextStep(element) {
                const nextStep = $(element).data('next');
                $('.form-step').addClass('d-none');
                $('.step-' + nextStep).removeClass('d-none');

                $('#form-steps .nav-link').removeClass('active').addClass('disabled');
                $('#form-steps .nav-link[data-step="' + nextStep + '"]').removeClass('disabled').addClass('active');
            }

            $(document).on('click', '.send_for_approval', function() {
                let reference_id = $('[name="reference_user_id"]').val();
                let auth_id = "{{ auth()->user()->id }}";

                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('requester_id', auth_id);
                formData.append('prime_id', reference_id);

                if (reference_id) {
                    $.ajax({
                        url: "{{ route('prime.request') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            toastr.success(data.message);
                            location.reload();
                        },
                        error: function(xhr) {
                            toastr.error('Request for approval not sent');
                            console.error(xhr.responseText);
                        }
                    });
                } else {
                    toastr.error('Please select a reference ID.');
                }
            });

            $(document).on('click', '.cancel_request', function() {
                let request_id = $(this).attr('data-request_id');

                if (request_id) {
                    $.ajax({
                        url: "{{ route('prime.cancel_request') }}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            request_id: request_id
                        },
                        success: function(data) {
                            toastr.success(data.status);
                            location.reload();
                        },
                        error: function(xhr) {
                            toastr.error('Failed to cancel the request.');
                            console.error(xhr.responseText);
                        }
                    });
                }
            });

            // Fetch police stations when district is selected
            $("#district_id").change(function() {
                let districtId = $(this).val();
                let policeDropdown = $('#police_station_id');

                policeDropdown.html('<option value="">Loading...</option>');

                if (districtId) {
                    $.ajax({
                        url: "{{ route('load_police_stations') }}",
                        type: "GET",
                        data: {
                            district_id: districtId
                        },
                        success: function(data) {
                            policeDropdown.html(
                                '<option value="">Select Police Station</option>');
                            data.police_stations.forEach(station => {
                                policeDropdown.append(
                                    `<option value="${station.id}">${station.name}</option>`
                                );
                            });
                        },
                    });
                }
            });

            // Fetch post offices when police station is selected
            $("#police_station_id").change(function() {
                let police_station_id = $(this).val();
                let postDropdown = $("#post_office_id");

                postDropdown.html('<option value="">Loading...</option>');

                if (police_station_id) {
                    $.ajax({
                        url: "{{ route('load_post_offices') }}",
                        type: "GET",
                        data: {
                            police_station_id: police_station_id
                        },
                        success: function(data) {
                            postDropdown.html('<option value="">Select Post Office</option>');
                            data.post_offices.forEach(post => {
                                postDropdown.append(
                                    `<option value="${post.id}" data-postal_code="${post.postcode}">${post.name}</option>`
                                );
                            });
                        },
                    });
                }
            });

            $("#post_office_id").change(function() {
                let postalCode = $(this).find(":selected").data("postal_code");
                console.log(postalCode); // Correctly logs the value
                $("#postal_code").val(postalCode); // Optional: auto-fill input
            });

            $(document).on('click', '.assign_postal_area', function() {

                let district_id = $('#district_id').val();
                let police_station_id = $('#police_station_id').val();
                let post_office_id = $('#post_office_id').val();
                let postal_code = $('#postal_code').val();
                let auth_id = "{{ auth()->user()->id }}";

                console.log('clicked');

                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('user_id', auth_id);
                formData.append('district_id', district_id);
                formData.append('police_station_id', police_station_id);
                formData.append('post_office_id', post_office_id);
                formData.append('postal_code', postal_code);
                if (district_id && police_station_id && post_office_id) {
                    $.ajax({
                        url: "{{ route('assign_postal_area') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            toastr.success(data.message);
                            nextStep($(this));
                            location.reload();
                        },
                        error: function(xhr) {
                            toastr.error('Request for approval not sent');
                            console.error(xhr.responseText);
                        }
                    });
                } else {
                    toastr.error('Please fill up all the fields');
                }

            })

            $(document).on('click', '.save_nominee', function() {

                let nominee_name = $('[name="nominee_name"]').val();
                let relation = $('[name="relation"]').val();
                // let doc_type = $('[name="doc_type"]').val();
                // let nominee_nid = $('[name="nominee_nid"]').val();


                let formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('nominee_name', nominee_name);
                formData.append('relation', relation);
                // formData.append('doc_type', doc_type);
                // formData.append('nominee_nid', nominee_nid);

                let hasError = false;

                if (!nominee_name) {
                    toastr.error('Nominee name is required');
                    hasError = true;
                    return;
                }

                if (!relation) {
                    toastr.error('Relation with nominee name is required');
                    hasError = true;
                    return;
                }

                if (hasError) return;


                $.ajax({
                    url: "{{ route('nominee') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        toastr.success(data.message);
                        location.reload();
                    },
                    error: function(xhr) {
                        toastr.error('Request for approval not sent');
                        console.error(xhr.responseText);
                    }
                });
            });

            $(document).on('click', '.choose_package', function() {
                let package_id = $(this).data('package_id');
                $('.package-card').removeClass('package_selected');
                $(this).closest('.package-card').addClass('package_selected')
                $('.choose_package_next').removeAttr('disabled');

                let package_name = $(this).data('package_name');
                let duration = $(this).data('duration');
                let price = $(this).data('price');

                const formData = {
                    package_name,
                    package_id,
                    duration,
                    price,
                };

                localStorage.setItem('packageInfo', JSON.stringify(formData));
            });

            $(document).on('click', '.choose_package_next', function() {
                let package_id = null;
                const packageInfoJSON = localStorage.getItem('packageInfo');
                if (packageInfoJSON) {
                    const packageInfo = JSON.parse(packageInfoJSON);
                    package_id = packageInfo.package_id;
                }

                if (package_id) {
                    $.ajax({
                        url: "{{ route('choose_package') }}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            subscription_package_id: package_id,
                        },
                        success: function(data) {
                            if (!data.status) {
                                toastr.error(data.message);
                            } else {
                                toastr.success(data.message);
                                location.reload();
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Request for approval not sent');
                            console.error(xhr.responseText);
                        }
                    });
                } else {
                    toastr.error('Please select package.');
                }
            })

            $(document).on('click', '.finish_payment', function() {
                const selectedPaymentType = $('input[name="payment_type"]:checked').val();

                if (packageInformation) {
                    const packageInfo = JSON.parse(packageInformation);
                    let transaction_number = $('.transaction_number').val();
                    let transaction_mobile_number = $('.transaction_mobile_number').val();

                    $.ajax({
                        url: "{{ route('finish_payment') }}",
                        type: "POST",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            subscription_package_id: packageInfo.package_id,
                            payment_option_id: selectedPaymentType,
                            transaction_number: transaction_number,
                            transaction_mobile_number: transaction_mobile_number
                        },
                        success: function(data) {
                            if (!data.status) {
                                toastr.error(data.message);
                            } else {
                                toastr.success(data.message);
                                location.reload();
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Request for approval not sent');
                            console.error(xhr.responseText);
                        }
                    });
                }

            });

            $('input[name="payment_type"]').on('change', function() {
                var accountNumber = $(this).data('account_number');

                // Set the account number text
                $('.account_number').text(accountNumber);

                // Show the account number div
                $('.account_number_div')
                    .removeClass('d-none')
                    .addClass('d-flex');
            });
            const packageInformation = localStorage.getItem('packageInfo');
            if (packageInformation) {
                const packageInfo = JSON.parse(packageInformation);
                package_id = packageInfo.package_id;

                $('.package_name').text(packageInfo.package_name);
                $('.package_price').text(packageInfo.price);
                $('.package_duration').text(packageInfo.duration);
            }


            $(document).on('click', '.change_mind', function() {
                $('#referenceId').prop('disabled', true);
                $('.emptyPrimeReferrer').removeClass('hidden');
                $('.send_for_approval').addClass('hidden');
                $('.change_mind').addClass('hidden');
            });

            $(document).on('click', '.change_referrer', function() {
                $('#referenceId').prop('disabled', false);
                $('.emptyPrimeReferrer').addClass('hidden');
                $('.send_for_approval').removeClass('hidden');
                $('.change_mind').removeClass('hidden');
            });

            $(document).on('click', '.adminRequest', function() {

                let auth_id = "{{ auth()->user()->id }}";
                let formData = new FormData();
                formData.append('_token', '{{ csrf_token() }}');
                formData.append('user_id', auth_id);

                if (auth_id) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to submit this request?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, submit it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ route('kyc.adminRequest') }}",
                                type: "POST",
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(data) {
                                    Swal.fire({
                                        title: 'Submitted!',
                                        text: data.message,
                                        icon: 'success',
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    setTimeout(() => location.reload(), 2000);
                                },
                                error: function(xhr) {
                                    Swal.fire({
                                        title: 'Error!',
                                        text: 'Request for approval not sent.',
                                        icon: 'error'
                                    });
                                    console.error(xhr.responseText);
                                }
                            });
                        }
                    });
                } else {
                    toastr.error('Please select a reference ID.');
                }
            });

        });
    </script>
@endsection
