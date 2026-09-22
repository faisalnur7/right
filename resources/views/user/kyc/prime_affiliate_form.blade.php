@extends('layouts.master')

@section('hide_page_header', true)
@section('title', 'Prime Membership')

@section('contents')
    <div class="prime-page">
        <div class="prime-container">
            <div class="card card-primary prime-card">
                <div class="prime-hero">
                    <div class="prime-hero__content">
                        <span class="prime-eyebrow">Right premium</span>
                        <h1>Upgrade to Prime</h1>
                        <p>Choose a plan and complete your payment to unlock the full Prime member experience.</p>
                    </div>
                    <span class="prime-hero__badge"><i class="fas fa-gem"></i> Premium membership</span>
                    <span class="prime-hero__orb" aria-hidden="true"></span>
                </div>

                <form id="kycForm" method="POST" action="{{ route('kyc.prime_store') }}" enctype="multipart/form-data">
                @csrf
                <div class="card-body">
                    <!-- STEP NAVIGATION -->
                    <ul class="nav nav-pills mb-3 prime-stepper" id="form-steps">
                        @php
                            $activePrimeStatus = $primeStatus + 1;
                            $activeStep = $primeStatus < App\Models\User::PRIME_VERIFIED_STATUS_PACKAGE ? 1 : 2;
                        @endphp
                        @if ($activePrimeStatus <= App\Models\User::PRIME_VERIFIED_STATUS_PAYMENT)
                            <li class="nav-item"><a class="nav-link @if ($activeStep === 1) active @endif" href="#" data-step="1">Step 1: Package</a></li>
                            <li class="nav-item"><a class="nav-link @if ($activeStep === 2) active @endif" href="#" data-step="2">Step 2: Payment</a></li>
                        @endif
                    </ul>

                    @if ($activePrimeStatus <= App\Models\User::PRIME_VERIFIED_STATUS_PAYMENT)
                        @if (false)
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
                                <div class="prime-first-step-packages mt-4">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div>
                                            <h5 class="mb-1 text-bold">Choose your package</h5>
                                            <p class="text-muted mb-0">Review the available Prime membership packages.</p>
                                        </div>
                                        <span class="badge badge-light">Prime plans</span>
                                    </div>
                                    @include('user.kyc.partials.package_cards')
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

                        <!-- STEP 2: Nominee Info -->
                        <div class="form-step step-2" data-form_step="2">
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

                            <button type="button" class="btn btn-primary save_nominee" data-next="3">Next</button>
                        </div>

                        @endif

                        <!-- STEP 1: Package Selection -->
                        <div class="form-step step-1" data-form_step="1">
                            @include('user.kyc.partials.package_cards')

                            <button type="button" class="btn btn-primary choose_package_next" disabled>Next</button>

                        </div>

                        <!-- STEP 2: Payment -->
                        <div class="form-step step-2" data-form_step="2">
                            <div class="payment-layout">
                            <div class="col-md-6">
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

                            <div class="payment-details">
                            <div class="form-group ml-3">
                                <label>Payment Type</label><br>
                                @foreach (App\Models\PaymentOption::all() as $paymentOption)
                                    <div class="form-check form-check-inline payment-option {{ $paymentOption->zoomable ? 'zoomable' : '' }}">
                                        <input type="radio" class="form-check-input" name="payment_type"
                                            value="{{ $paymentOption->id }}" id="payment_{{ $paymentOption->id }}"
                                            data-account_number="{{ $paymentOption->account_number }}">
                                        <label class="form-check-label" for="payment_{{ $paymentOption->id }}">
                                            <img src="{{ asset($paymentOption->logo) }}" class="h-12" />
                                        </label>
                                        @if ($paymentOption->zoomable)
                                            <button type="button" class="payment-option-zoom" data-logo="{{ asset($paymentOption->logo) }}"
                                                data-name="{{ $paymentOption->name }}" aria-label="Zoom {{ $paymentOption->name }} logo"
                                                title="Zoom logo">
                                                <i class="fas fa-search-plus" aria-hidden="true"></i>
                                            </button>
                                        @endif
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
                                    <label>Payment Phone Number</label>
                                    <input type="text" class="form-control transaction_mobile_number"
                                        name="transaction_mobile_number" required>
                                </div>
                            </div>

                            <div class="ml-3">
                                <button type="button" class="btn btn-success finish_payment">Finish</button>
                                <button type="button" class="prev-step btn btn-secondary"
                                    data-prev="1">Previous</button>
                            </div>
                            </div>
                            </div>
                        </div>
                    @else
                        @if (auth()->user()->prime_verified == App\Models\User::PRIME_VERIFIED_STATUS_PAYMENT)
                            {{-- Your request is under process. --}}
                            <!-- Pending request status -->
                            <div class="prime-request-status" role="status">
                                <div class="prime-request-status__icon"><i class="fas fa-hourglass-half" aria-hidden="true"></i></div>
                                <div class="prime-request-status__copy">
                                    <span class="prime-request-status__eyebrow">Payment submitted successfully</span>
                                    <strong>Your Prime membership request is under process</strong>
                                    <small>Our team is reviewing your request. You will be notified once it is approved.</small>
                                </div>
                                <span class="prime-request-status__badge"><i class="fas fa-clock" aria-hidden="true"></i> Pending review</span>
                            </div>
                            <!-- Information Card -->
                            <div class="card mb-4 prime-request-card">
                                <div class="card-header prime-request-card__header">
                                    <div>
                                        <span class="prime-request-card__eyebrow">Application overview</span>
                                        <strong>Request Details</strong>
                                    </div>
                                    <i class="fas fa-file-invoice-dollar" aria-hidden="true"></i>
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
                                            <td>{{ $authUser->district?->name ?? 'N/A' }}</td>
                                        </tr>

                                        <tr>
                                            <th>Thana</th>
                                            <td>{{ $authUser->police_station?->name ?? 'N/A' }}</td>
                                        </tr>

                                        <tr>
                                            <th>Post Office</th>
                                            <td>{{ $authUser->post_office?->name ?? 'N/A' }}</td>
                                        </tr>

                                        <tr>
                                            <th>Post Code</th>
                                            <td>{{ $authUser->post_office?->postcode ?? 'N/A' }}</td>
                                        </tr>

                                        <tr>
                                            <th>Nominee Name</th>
                                            <td>{{ $authUser->nominee?->nominee_name ?? 'N/A' }}</td>
                                        </tr>

                                        <tr>
                                            <th>Relation</th>
                                            <td>{{ $authUser->nominee?->relation ?? 'N/A' }}</td>
                                        </tr>

                                        <tr>
                                            <th>Document Type</th>
                                            <td>{{ $authUser->nominee?->doc_type ?? 'N/A' }}</td>
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
                                            <td>{{ App\Models\PaymentOption::query()->find($authUser->packages->first()?->pivot?->payment_option_id)?->name ?? 'N/A' }}
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
                    <div class="modal fade" id="paymentOptionZoomModal" tabindex="-1" role="dialog"
                        aria-labelledby="paymentOptionZoomModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered payment-option-zoom-dialog" role="document">
                            <div class="modal-content payment-option-zoom-modal">
                                <button type="button" class="close payment-option-zoom-close" data-dismiss="modal"
                                    aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                <div class="modal-body">
                                    <h5 id="paymentOptionZoomModalLabel" class="sr-only"></h5>
                                    <img id="paymentOptionZoomImage" src="" alt="" class="img-fluid">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
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

                if (!selectedPaymentType) {
                    toastr.error('Please select a payment type.');
                    return;
                }

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

            $(document).on('click', '.payment-option-zoom', function(event) {
                event.preventDefault();
                event.stopPropagation();

                var button = $(this);
                var name = button.data('name') || 'Payment option';

                $('#paymentOptionZoomImage')
                    .attr('src', button.data('logo'))
                    .attr('alt', name + ' logo');
                $('#paymentOptionZoomModalLabel').text(name + ' logo');
                $('#paymentOptionZoomModal').modal('show');
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
    <style>
        .prime-page { width: 100%; min-height: calc(100vh - 74px); padding: 24px 16px 44px; background: #f4f7fb; }
        .prime-container { display: block; width: 100% !important; max-width: none !important; margin: 0; }
        .prime-card { display: block; width: 100% !important; max-width: none !important; overflow: hidden; border: 0; border-radius: 24px; background: #fff; box-shadow: 0 18px 42px rgba(18, 37, 58, .1); }
        .prime-hero { position: relative; display: flex; align-items: center; justify-content: space-between; min-height: 190px; overflow: hidden; padding: 32px 42px; color: #fff; background: linear-gradient(116deg, #11243e 0%, #1b3b5d 62%, #235d7f 100%); }
        .prime-hero__content { position: relative; z-index: 1; max-width: 690px; }
        .prime-eyebrow { display: block; margin-bottom: 8px; color: #ff5962; font-size: 11px; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
        .prime-hero h1 { margin: 0; font-size: clamp(30px, 4vw, 44px); font-weight: 700; letter-spacing: -.04em; }
        .prime-hero p { margin: 9px 0 0; color: #c0d1e2; font-size: 15px; line-height: 1.6; }
        .prime-hero__badge { position: relative; z-index: 1; flex: 0 0 auto; padding: 11px 15px; color: #f8e5a3; border: 1px solid rgba(255,220,110,.35); border-radius: 999px; background: rgba(240,177,0,.16); font-size: 12px; font-weight: 700; }
        .prime-hero__badge i { margin-right: 6px; color: #f5bd22; }
        .prime-hero__orb { position: absolute; right: 6%; top: -180px; width: 390px; height: 390px; border: 1px solid rgba(255,255,255,.14); border-radius: 50%; box-shadow: 0 0 0 30px rgba(255,255,255,.025), 0 0 0 70px rgba(255,255,255,.025); background: radial-gradient(circle, rgba(224,47,83,.28), transparent 63%); }
        .prime-card > form > .card-body { padding: 34px 42px 42px; }
        .prime-stepper { display: flex; gap: 10px; margin: 0 -42px 32px; padding: 0 42px 22px; border-bottom: 1px solid #e5edf5; }
        .prime-stepper .nav-item { flex: 1; }
        .prime-stepper .nav-link { display: flex; align-items: center; justify-content: center; min-height: 46px; padding: 11px 15px; color: #8292a5; border: 1px solid #dce6f0; border-radius: 11px; background: #f8fafc; font-size: 13px; font-weight: 700; pointer-events: none; }
        .prime-stepper .nav-link.active { color: #fff; border-color: #e2313e; background: linear-gradient(135deg, #e2313e, #bd1f32); box-shadow: 0 8px 16px rgba(226,49,62,.2); }
        .prime-stepper .nav-link.text-bold { color: #218653; border-color: #bde8cf; background: #f0fbf4; }
        .prime-first-step-packages { padding-top: 4px; }
        .prime-first-step-packages h5 { color: #152b45; font-size: 20px; }
        .prime-first-step-packages .badge { padding: 8px 12px; color: #7b5a00; border: 1px solid #f2dda1; border-radius: 999px; background: #fff8dd; }
        .prime-package-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 22px; margin: 6px 0 0; }
        .prime-package-grid > [class*="col-"] { width: auto; max-width: none; margin: 0 !important; padding: 0; }
        .prime-request-status { position: relative; display: flex; align-items: center; gap: 18px; margin: 4px 0 22px; padding: 22px 26px; overflow: hidden; color: #fff; border: 1px solid rgba(255,255,255,.18); border-radius: 18px; background: linear-gradient(120deg, #122b4a 0%, #17627a 58%, #16a6b9 100%); box-shadow: 0 16px 32px rgba(18,55,79,.18); }
        .prime-request-status::after { position: absolute; right: -42px; top: -78px; width: 210px; height: 210px; border: 1px solid rgba(255,255,255,.15); border-radius: 50%; box-shadow: 0 0 0 22px rgba(255,255,255,.035), 0 0 0 45px rgba(255,255,255,.025); content: ''; }
        .prime-request-status__icon { position: relative; z-index: 1; display: inline-flex; width: 54px; height: 54px; flex: 0 0 54px; align-items: center; justify-content: center; color: #ffe18b; border: 1px solid rgba(255,225,139,.35); border-radius: 16px; background: rgba(255,255,255,.12); font-size: 22px; }
        .prime-request-status__copy { position: relative; z-index: 1; display: flex; flex-direction: column; gap: 3px; }
        .prime-request-status__eyebrow, .prime-request-card__eyebrow { color: #a8f1e8; font-size: 10px; font-weight: 800; letter-spacing: .14em; text-transform: uppercase; }
        .prime-request-status__copy strong { font-size: 19px; letter-spacing: -.01em; }
        .prime-request-status__copy small { color: #c7e0e9; font-size: 12px; }
        .prime-request-status__badge { position: relative; z-index: 1; display: inline-flex; align-items: center; gap: 7px; margin-left: auto; padding: 9px 13px; color: #6e4e00; white-space: nowrap; border: 1px solid #f5d879; border-radius: 999px; background: #fff1b8; font-size: 11px; font-weight: 800; text-transform: uppercase; }
        .prime-request-card { overflow: hidden; border: 1px solid #dbe8f0; border-radius: 18px; background: #fff; box-shadow: 0 14px 32px rgba(18,37,58,.08); }
        .prime-request-card__header { display: flex; align-items: center; justify-content: flex-start; padding: 19px 24px; color: #fff; border: 0; background: linear-gradient(120deg, #182941, #235d7f); }
        .prime-request-card__header > div { flex: 1 1 auto; min-width: 0; }
        .prime-request-card__header strong { display: block; margin-top: 3px; font-size: 20px; letter-spacing: -.02em; }
        .prime-request-card__header > i { flex: 0 0 auto; margin-left: auto; color: #a8f1e8; font-size: 28px; }
        .prime-request-card .card-body { padding: 18px 20px 22px; }
        .prime-request-card table { overflow: hidden; margin: 0; border: 1px solid #dbe8f0; border-radius: 12px; }
        .prime-request-card table tr { transition: background .18s ease; }
        .prime-request-card table tr:hover { background: #f5fbfc; }
        .prime-request-card table th { width: 48%; color: #29415b; background: #f5f9fc; font-size: 12px; font-weight: 800; letter-spacing: .01em; }
        .prime-request-card table td { color: #20344c; font-size: 13px; font-weight: 600; }
        .prime-request-card table th, .prime-request-card table td { padding: 13px 15px; border-color: #dbe8f0; vertical-align: middle; }
        .prime-request-card table tr:nth-child(12) td, .prime-request-card table tr:nth-child(13) td { color: #168497; font-weight: 800; }
        .package-card { position: relative; min-height: 100%; overflow: hidden; border: 1px solid #dfe8f1 !important; border-radius: 18px !important; background: #fff; box-shadow: 0 10px 22px rgba(18,37,58,.06) !important; transition: .22s ease; }
        .package-card:hover, .package-card.package_selected, .package-card.selected { border-color: #e2313e !important; background: #fffafb; box-shadow: 0 15px 28px rgba(226,49,62,.13) !important; transform: translateY(-3px); }
        .package-card__topline { display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; }
        .package-card__icon { display: inline-flex; width: 38px; height: 38px; align-items: center; justify-content: center; color: #d92e3d; border-radius: 11px; background: #ffecef; }
        .package-card__label { color: #9a6d00; font-size: 11px; font-weight: 800; letter-spacing: .1em; text-transform: uppercase; }
        .package-card__name { color: #152b45 !important; font-size: 24px; }
        .package-card__subtitle { min-height: 24px; color: #76889c !important; font-size: 13px; font-weight: 400; }
        .package-card__price { color: #152b45 !important; font-size: 25px !important; }
        .package-card__duration { color: #526a83; font-size: 12px; }
        .package-card .choose_package { border: 0; border-radius: 10px; background: #182941; font-size: 13px !important; box-shadow: 0 8px 16px rgba(24,41,65,.13); }
        .package-card:hover .choose_package, .package-card.package_selected .choose_package, .package-card.selected .choose_package { background: #e2313e; }
        .package-card .feature-item { color: #526a83; font-size: 13px; line-height: 1.55; }
        .package-card .feature-item::before { margin-right: 7px; color: #25a568; content: '\f00c'; font-family: 'Font Awesome 5 Free'; font-weight: 900; }
        .step-1 > .choose_package_next { display: block; min-width: 132px; margin: 24px 0 0 auto; padding: 13px 25px; border: 0; border-radius: 10px; background: #182941; font-weight: 700; box-shadow: 0 8px 16px rgba(24,41,65,.14); }
        .step-1 > .choose_package_next:not(:disabled):hover { background: #e2313e; }
        .step-2 { padding: 8px 0 0; }
        .payment-layout { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(360px, .85fr); gap: 30px; align-items: start; }
        .payment-layout > .col-md-6 { grid-column: 2; grid-row: 1; width: 100%; max-width: none; margin: 0 !important; padding: 24px; border: 1px solid #dce6f0; border-radius: 16px; background: #fff; box-shadow: 0 8px 22px rgba(18,37,58,.05); }
        .payment-layout > .col-md-6 > .text-red { display: block; max-width: 920px; margin: 0 0 22px; padding: 14px 18px; color: #a3212d; border: 1px solid #ffd0d5; border-radius: 12px; background: #fff4f5; font-size: 13px; line-height: 1.55; }
        .payment-details { grid-column: 1; grid-row: 1; padding: 24px; border: 1px solid #dce6f0; border-radius: 16px; background: #fbfdff; box-shadow: 0 8px 22px rgba(18,37,58,.05); }
        .step-2 label { display: block; margin: 16px 0 9px; color: #223853; font-size: 13px; font-weight: 700; }
        .payment-layout > .col-md-6 > label { margin-top: 0; color: #152b45; font-size: 15px; }
        .step-2 .table { overflow: hidden; margin-bottom: 0; border: 1px solid #dce6f0; border-radius: 14px; box-shadow: 0 8px 20px rgba(18,37,58,.05); }
        .step-2 .table td { padding: 16px 18px; border-color: #e6edf4; font-size: 14px; }
        .step-2 .table td:first-child { width: 34%; color: #627990; background: #f7f9fc; font-weight: 700; }
        .step-2 > hr { margin: 30px 0 22px; border-color: #e5edf5; }
        .step-2 .form-group.ml-3 { margin-left: 0 !important; }
        .step-2 .form-group.ml-3 > label { margin-top: 0; font-size: 15px; }
        .step-2 .form-control { min-height: 50px; border: 1px solid #d5e1ec; border-radius: 11px; box-shadow: 0 4px 12px rgba(18,37,58,.03); }
        .step-2 .form-control:focus { border-color: #e2313e; box-shadow: 0 0 0 4px rgba(226,49,62,.1); }
        .step-2 .payment-option { position: relative; display: inline-flex; width: 170px; min-height: 102px; align-items: center; justify-content: center; margin: 0 12px 12px 0; padding: 17px 16px 13px; border: 1px solid #d5e1ec; border-radius: 15px; background: linear-gradient(145deg, #fff, #f8fbfe); box-shadow: 0 6px 16px rgba(18,37,58,.05); transition: .2s ease; vertical-align: top; }
        .step-2 .payment-option:hover, .step-2 .payment-option:has(input:checked) { border-color: #e2313e; background: linear-gradient(145deg, #fff8f9, #fff); box-shadow: 0 10px 22px rgba(226,49,62,.14); transform: translateY(-2px); }
        .step-2 .payment-option input { position: absolute; width: 1px; height: 1px; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); white-space: nowrap; border: 0; opacity: 0; }
        .step-2 .payment-option .form-check-label { display: flex; width: 100%; height: 100%; align-items: center; justify-content: center; margin: 0; cursor: pointer; }
        .step-2 .payment-option img { width: auto; max-width: 112px; max-height: 48px; object-fit: contain; }
        .step-2 .payment-option.zoomable .payment-option-zoom { position: absolute; top: 8px; right: 8px; display: inline-flex; width: 28px; height: 28px; align-items: center; justify-content: center; padding: 0; color: #526a83; border: 1px solid #d5e1ec; border-radius: 50%; background: #fff; font-size: 12px; cursor: zoom-in; }
        .step-2 .payment-option.zoomable .payment-option-zoom:hover, .step-2 .payment-option.zoomable .payment-option-zoom:focus { color: #e2313e; border-color: #e2313e; outline: none; box-shadow: 0 0 0 3px rgba(226,49,62,.12); }
        .payment-option-zoom-dialog { width: max-content; max-width: 90vw; margin-right: auto; margin-left: auto; }
        .payment-option-zoom-modal { position: relative; width: fit-content; max-width: 90vw; border: 0; border-radius: 0; background: transparent; box-shadow: none; }
        .payment-option-zoom-modal .modal-body { display: flex; min-height: 180px; align-items: center; justify-content: center; padding: 0; }
        .payment-option-zoom-modal img { max-width: min(90vw, 720px); max-height: 82vh; object-fit: contain; }
        .payment-option-zoom-close { position: absolute; z-index: 1; top: 8px; right: 8px; width: 32px; height: 32px; padding: 0; color: #fff; border: 0; border-radius: 50%; background: rgba(0,0,0,.6); font-size: 25px; line-height: 1; opacity: 1; }
        .payment-option-zoom-close:hover, .payment-option-zoom-close:focus { color: #fff; opacity: .85; outline: none; }
        .step-2 .payment-option:has(input:checked)::after { position: absolute; right: 10px; bottom: 8px; padding: 3px 7px; color: #168451; border-radius: 999px; background: #e8f8ef; content: 'Selected'; font-size: 9px; font-weight: 800; letter-spacing: .03em; text-transform: uppercase; }
        .step-2 .account_number_div { margin: 18px 0 0 !important; padding: 13px 16px; color: #a3212d; border: 1px solid #ffd0d5; border-radius: 11px; background: #fff4f5; font-size: 13px; }
        .payment-details > .row.ml-3.d-flex { margin: 24px -10px 0 !important; }
        .payment-details > .row.ml-3.d-flex .form-group { padding: 0 10px; }
        .payment-details > .row.ml-3.d-flex label { margin-top: 0; }
        .payment-details > .ml-3:last-child { display: flex; justify-content: flex-end; gap: 10px; margin: 26px 0 0 !important; }
        .step-2 .finish_payment, .step-2 .prev-step { min-width: 112px; min-height: 44px; border: 0; border-radius: 10px; font-weight: 700; }
        .step-2 .finish_payment { order: 2; background: #182941; box-shadow: 0 8px 16px rgba(24,41,65,.15); }
        .step-2 .finish_payment:hover { background: #e2313e; }
        .step-2 .prev-step { order: 1; }
        @media (max-width: 900px) { .prime-package-grid { grid-template-columns: 1fr; } .payment-layout { grid-template-columns: 1fr; gap: 16px; } .payment-layout > .col-md-6, .payment-details { grid-column: 1; grid-row: auto; } }
        @media (max-width: 768px) { .prime-page { padding: 16px 12px 30px; } .prime-hero { display: block; min-height: 210px; padding: 28px 24px; } .prime-hero__badge { display: inline-block; margin-top: 20px; } .prime-hero__orb { right: -150px; } .prime-card > form > .card-body { padding: 25px 20px 30px; } .prime-stepper { gap: 7px; margin: 0 -20px 25px; padding: 0 20px 18px; } .prime-stepper .nav-link { min-height: 42px; padding: 8px 5px; font-size: 11px; } .prime-request-status { align-items: flex-start; flex-wrap: wrap; padding: 19px; } .prime-request-status__copy { flex: 1 1 calc(100% - 72px); } .prime-request-status__copy strong { font-size: 16px; } .prime-request-status__badge { margin-left: 72px; } .prime-request-card__header { padding: 17px 18px; } .prime-request-card .card-body { padding: 12px; } .prime-request-card table th { width: 42%; } .prime-request-card table th, .prime-request-card table td { padding: 11px 10px; font-size: 11px; } }
    </style>
@endsection
