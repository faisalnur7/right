@extends('layouts.master')

@section('contents')
    <div class="container-fluid mt-4">
        <!-- Progress Bar -->
        <div class="progress-container mb-4">
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" id="progress-bar" style="width: 33%;">
                </div>
            </div>
            <div class="step-labels d-flex justify-content-between mt-2">
                <span class="step-label active">Information</span>
                <span class="step-label">Contact</span>
                <span class="step-label">Withdrawal</span>
            </div>
        </div>
        <div class="card">
            <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title">KYC Form</h3>
            </div>
            <div class="card-body">
                @php
                    $referer_id = null;
                    $affiliate_id = null;
                    $father = null;
                    $mother = null;
                    $document_number = null;
                    $dob = null;
                    $doc_type = null;
                    $document_file = null;
                    $permanent_division_id = null;
                    $permanent_district_id = null;
                    $permanent_police_station_id = null;
                    $permanent_post_office_id = null;
                    $permanent_address = null;

                    $present_division_id = null;
                    $present_district_id = null;
                    $present_police_station_id = null;
                    $present_post_office_id = null;
                    $present_address = null;

                    $account_type = null;
                    $account_number = null;
                    $is_same_address = 0;

                    if (!empty($kyc)) {
                        $referer_id = $kyc->referer_id ? $kyc->referer_id : null;
                        $affiliate_id = $kyc->affiliate_id ? $kyc->affiliate_id : null;
                        $father = $kyc->father ? $kyc->father : null;
                        $mother = $kyc->mother ? $kyc->mother : null;
                        $document_number = $kyc->document_number ? $kyc->document_number : null;
                        $dob = $kyc->dob ? $kyc->dob : null;
                        $doc_type = $kyc->doc_type ? $kyc->doc_type : null;
                        $document_file = $kyc->document_file ? $kyc->document_file : null;

                        $permanent_division_id = $kyc->permanent_division_id ? $kyc->permanent_division_id : null;
                        $permanent_district_id = $kyc->permanent_district_id ? $kyc->permanent_district_id : null;
                        $permanent_police_station_id = $kyc->permanent_police_station_id
                            ? $kyc->permanent_police_station_id
                            : null;
                        $permanent_post_office_id = $kyc->permanent_post_office_id
                            ? $kyc->permanent_post_office_id
                            : null;
                        $permanent_address = $kyc->permanent_address ? $kyc->permanent_address : null;

                        $present_division_id = $kyc->present_division_id ? $kyc->present_division_id : null;
                        $present_district_id = $kyc->present_district_id ? $kyc->present_district_id : null;
                        $present_police_station_id = $kyc->present_police_station_id
                            ? $kyc->present_police_station_id
                            : null;
                        $present_post_office_id = $kyc->present_post_office_id ? $kyc->present_post_office_id : null;
                        $present_address = $kyc->present_address ? $kyc->present_address : null;

                        $account_type = $kyc->account_type ? $kyc->account_type : null;
                        $account_number = $kyc->account_number ? $kyc->account_number : null;
                        $is_same_address = $kyc->is_same_address ? $kyc->is_same_address : 0;
                    }
                @endphp
                <!-- Multi-step Form -->
                <form id="kycForm" enctype="multipart/form-data">

                    <!-- Step 1: Personal Information -->
                    <div class="form-step active">
                        <h5 class="text-bold text-lg">Step 1: Personal Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Reference ID</label>
                                    <select class="form-control reference_user" name="referer_id">
                                        <option value="">Select Reference</option>
                                        @foreach ($affiliates as $affiliate)
                                            <option value="{{ $affiliate->id }}"
                                                @if ($referer_id == $affiliate->id) selected @endif>
                                                {{ $affiliate?->kyc?->affiliate_id }} - {{ $affiliate->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Affiliate ID</label>
                                    <input type="text" class="form-control affiliate_id" name="affiliate_id"
                                        value="{{ $affiliate_id }}" placeholder="Enter Reference ID" readonly>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Father’s Name</label>
                                    <input type="text" class="form-control" value="{{ $father }}"
                                        placeholder="Enter Father's Name" name="father">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Mother’s Name</label>
                                    <input type="text" class="form-control" value="{{ $mother }}"
                                        placeholder="Enter Mother's Name" name="mother">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date of Birth (DOB)</label>
                                    <input type="date" class="form-control date" name="dob"
                                        value="{{ $dob }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Document File (NID/BC/Passport) -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <!-- Radio Buttons -->
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="nid" name="doc_type"
                                            @if ($doc_type == App\Models\Kyc::NID) checked @endif class="custom-control-input"
                                            value="{{ App\Models\Kyc::NID }}">
                                        <label class="custom-control-label" for="nid">NID</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="bc" name="doc_type"
                                            @if ($doc_type == App\Models\Kyc::BC) checked @endif class="custom-control-input"
                                            value="{{ App\Models\Kyc::BC }}">
                                        <label class="custom-control-label" for="bc">Birth Certificate</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="passport" name="doc_type"
                                            @if ($doc_type == App\Models\Kyc::PASSPORT) checked @endif class="custom-control-input"
                                            value="{{ App\Models\Kyc::PASSPORT }}">
                                        <label class="custom-control-label" for="passport">Passport</label>
                                    </div>

                                    <input type="hidden" name="has_document" value="{{ $document_file }}"
                                        id="has_document" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" class="form-control" value="{{ $document_number }}"
                                        placeholder="Enter document number" name="document_number">
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary next-steps first_step">Next</button>
                    </div>
                    <!-- Step 2: Contact Information -->
                    <div class="form-step">
                        <h5 class="text-lg text-bold">Step 2: Contact Information</h5>
                        <!-- Permanent Address -->
                        <h6 class="mt-3 text-lg text-bold mb-2">Permanent Address</h6>
                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Division</label>
                                    <select class="form-control permanent_division" name="permanent_division_id">
                                        <option value="">Select Division</option>
                                        @foreach ($divisions as $division)
                                            <option value="{{ $division->id }}"
                                                @if ($permanent_division_id == $division->id) selected @endif>{{ $division->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>District</label>
                                    <select class="form-control permanent_district" name="permanent_district_id">
                                        <option value="">Select District</option>
                                        @foreach ($districts as $district)
                                            <option value="{{ $district->id }}"
                                                @if ($permanent_district_id == $district->id) selected @endif>{{ $district->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Police Station</label>
                                    <select class="form-control permanent_police_station"
                                        name="permanent_police_station_id">
                                        <option value="">Select Police Station</option>
                                        @foreach ($policeStations as $policeStation)
                                            <option value="{{ $policeStation->id }}"
                                                @if ($permanent_police_station_id == $policeStation->id) selected @endif>
                                                {{ $policeStation->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Post Office</label>
                                    <select class="form-control permanent_post_office" name="permanent_post_office_id">
                                        <option value="">Select Post Office</option>
                                        @foreach ($postOffices as $postOffice)
                                            <option value="{{ $postOffice->id }}"
                                                @if ($permanent_post_office_id == $postOffice->id) selected @endif>{{ $postOffice->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-12 mb-2">
                                <textarea class="form-control permanent_address" placeholder="Enter Permanent Address" name="permanent_address">{{ $permanent_address }}</textarea>
                            </div>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sameAddress">
                            <input type="hidden" name="sameAddress" id="sameAddressValue"
                                value="{{ $is_same_address }}">
                            <label class="form-check-label" for="sameAddress">
                                Present and Permanent Address are the same
                            </label>
                        </div>


                        <!-- Present Address Section -->
                        <div class="present_address_section mt-3">
                            <h6 class="text-lg text-bold mb-2">Present Address</h6>
                            <hr>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Division</label>
                                        <select class="form-control present_division" name="present_division_id">
                                            <option value="">Select Division</option>
                                            @foreach ($divisions as $division)
                                                <option value="{{ $division->id }}"
                                                    @if ($present_division_id == $division->id) selected @endif>{{ $division->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>District</label>
                                        <select class="form-control present_district" name="present_district_id">
                                            <option value="">Select District</option>
                                            @foreach ($districts as $district)
                                                <option value="{{ $district->id }}"
                                                    @if ($present_district_id == $district->id) selected @endif>{{ $district->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Police Station</label>
                                        <select class="form-control present_police_station"
                                            name="present_police_station_id">
                                            <option value="">Select Police Station</option>
                                            @foreach ($policeStations as $policeStation)
                                                <option value="{{ $policeStation->id }}"
                                                    @if ($present_police_station_id == $policeStation->id) selected @endif>
                                                    {{ $policeStation->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Post Office</label>
                                        <select class="form-control present_post_office" name="present_post_office_id">
                                            <option value="">Select Post Office</option>
                                            @foreach ($postOffices as $postOffice)
                                                <option value="{{ $postOffice->id }}"
                                                    @if ($present_post_office_id == $postOffice->id) selected @endif>
                                                    {{ $postOffice->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <textarea class="form-control present_address mb-2" placeholder="Enter Present Address" name="present_address">{{ $present_address }}</textarea>
                        </div>

                        <button type="button" class="btn btn-secondary prev-step">Previous</button>
                        <button type="button" class="btn btn-primary next-step second_step">Next</button>
                    </div>

                    <!-- Step 3: Withdrawal Information -->
                    <div class="form-step">
                        <h5>Step 3: Withdrawal Information</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Withdrawal A/C Number (bKash/Nagad/Rocket)</label>
                                    <br>
                                    <input type="hidden" value="1" name="final_step" />
                                    <div class="custom-control custom-radio custom-control-inline"> 
                                        <input type="radio" id="bkash" name="account_type"
                                            @if ($account_type == App\Models\Kyc::BKASH) checked @endif class="custom-control-input"
                                            value={{ App\Models\Kyc::BKASH }}>
                                        <label class="custom-control-label" for="bkash">BKash</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="nagad" name="account_type"
                                            @if ($account_type == App\Models\Kyc::NAGAD) checked @endif class="custom-control-input"
                                            value={{ App\Models\Kyc::NAGAD }}>
                                        <label class="custom-control-label" for="nagad">Nagad</label>
                                    </div>
                                    <div class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" id="rocket" name="account_type"
                                            @if ($account_type == App\Models\Kyc::ROCKET) checked @endif class="custom-control-input"
                                            value={{ App\Models\Kyc::ROCKET }}>
                                        <label class="custom-control-label" for="rocket">Rocket</label>
                                    </div>
                                    <br>
                                    <input type="text" class="form-control" id="account_number" name="account_number"
                                        value="{{ $account_number }}" placeholder="Enter A/C Number">


                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-secondary prev-step">Previous</button>
                        <button type="button" class="btn btn-success final_step">Submit</button>
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {


            let currentStep = 0;
            const steps = $(".form-step");
            const progressBar = $("#progress-bar");
            const stepLabels = $(".step-label");

            function updateStep() {
                steps.removeClass("active").eq(currentStep).addClass("active");

                const progress = ((currentStep + 1) / steps.length) * 100;
                progressBar.css("width", progress + "%");

                stepLabels.removeClass("active").eq(currentStep).addClass("active");
            }

            $(".prev-step").click(function() {
                if (currentStep > 0) {
                    currentStep--;
                    updateStep();
                }
            });
            if ($('#sameAddressValue').val() == 1) {
                $('.present_address_section').hide();
                $('#sameAddressValue').val(1);
                $('#sameAddress').attr('checked', 'checked');
            } else {
                $('#sameAddressValue').val(0);
                $('.present_address_section').show();
            }

            $("#sameAddress").change(function() {
                if (this.checked) {
                    $('.present_address_section').hide();
                    $('#sameAddressValue').val(1);
                } else {
                    $('#sameAddressValue').val(0);
                    $('.present_address_section').show();
                }
            });

            $(document).on('change', '.reference_user', function() {
                let ref_user_id = $(this).val();
                if (ref_user_id) {
                    $.ajax({
                        url: "{{ route('load_affiliate_id') }}",
                        type: "GET",
                        data: {
                            ref_user_id: ref_user_id
                        },
                        success: function(data) {
                            $('.affiliate_id').val(data.reference_id);
                        },
                    });
                } else {
                    $('.affiliate_id').val(null);
                }
            })

            // Fetch districts when division is selected
            $(".present_division, .permanent_division").change(function() {
                let divisionId = $(this).val();
                let districtDropdown = $(this).hasClass("present_division") ?
                    $(".present_district") :
                    $(".permanent_district");

                districtDropdown.html('<option value="">Loading...</option>');

                if (divisionId) {
                    $.ajax({
                        url: "{{ route('load_districts') }}",
                        type: "GET",
                        data: {
                            division_id: divisionId
                        },
                        success: function(data) {
                            districtDropdown.html('<option value="">Select District</option>');
                            data.districts.forEach(district => {
                                districtDropdown.append(
                                    `<option value="${district.id}">${district.name}</option>`
                                );
                            });
                        },
                    });
                }
            });

            // Fetch police stations when district is selected
            $(".present_district, .permanent_district").change(function() {
                let districtId = $(this).val();
                let policeDropdown = $(this).hasClass("present_district") ?
                    $(".present_police_station") :
                    $(".permanent_police_station");

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
            $(".present_police_station, .permanent_police_station").change(function() {
                let police_station_id = $(this).val();
                let postDropdown = $(this).hasClass("present_police_station") ?
                    $(".present_post_office") :
                    $(".permanent_post_office");

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
                                    `<option value="${post.id}">${post.name}</option>`
                                );
                            });
                        },
                    });
                }
            });

            updateStep();

            $(document).on('click', '.first_step', function() {
                let reference_user = $('.reference_user').val();
                let affiliate_id = $('.affiliate_id').val();
                let father = $('[name="father"]').val();
                let mother = $('[name="mother"]').val();
                let referer_id = $('[name="referer_id"]').val();
                let dob = $('[name="dob"]').val();
                let doc_type = $('[name="doc_type"]').val();
                let document_number = $('[name="document_number"]').val();
                let photo = $('#imageUpload')[0]?.files[0];


                let hasError = false;

                if (!reference_user) {
                    toastr.error('Reference user is required');
                    hasError = true;
                    return;
                }

                if (!affiliate_id) {
                    toastr.error('Affiliate ID is required');
                    hasError = true;
                    return;
                }

                if (!father) {
                    toastr.error('Father\'s name is required');
                    hasError = true;
                    return;
                }

                if (!mother) {
                    toastr.error('Mother\'s name is required');
                    hasError = true;
                    return;
                }

                if (!dob) {
                    toastr.error('Date of Birth is required');
                    hasError = true;
                    return;
                }


                if (hasError) return;

                // Build FormData
                let formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('reference_user', reference_user);
                formData.append('affiliate_id', affiliate_id);
                formData.append('father', father);
                formData.append('mother', mother);
                formData.append('referer_id', referer_id);
                formData.append('doc_type', doc_type);
                formData.append('document_number', document_number);
                formData.append('dob', dob);
                formData.append('photo', photo);
                formData.append('step', 1);

                $.ajax({
                    url: "{{ route('kyc.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        toastr.success('Information submitted successfully!');
                        if (currentStep < steps.length - 1) {
                            currentStep++;
                            updateStep();
                        }
                    },
                    error: function(xhr) {
                        let res = xhr.responseJSON; // get JSON response
                        if (res && res.message) {
                            toastr.error('KYC submission failed. ' + res.message);
                        } else {
                            toastr.error('KYC submission failed. Unknown error.');
                        }
                        console.error(xhr.responseText);
                    }

                });
            });

            $(document).on('click', '.second_step', function() {
                let sameAddress = $('#sameAddressValue').val();
                let permanent_division_id = $('[name="permanent_division_id"]').val();
                let permanent_district_id = $('[name="permanent_district_id"').val();
                let permanent_police_station_id = $('[name="permanent_police_station_id"]').val();
                let permanent_post_office_id = $('[name="permanent_post_office_id"]').val();
                let permanent_address = $('[name="permanent_address"]').val();

                let present_division_id = $('[name="present_division_id"]').val();
                let present_district_id = $('[name="present_district_id"').val();
                let present_police_station_id = $('[name="present_police_station_id"]').val();
                let present_post_office_id = $('[name="present_post_office_id"]').val();
                let present_address = $('[name="present_address"]').val();

                let hasError = false;

                if (hasError) return;

                

                // Build FormData
                let formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr(
                    'content')); // or manually insert CSRF token
                formData.append('sameAddress', sameAddress);
                formData.append('permanent_division_id', permanent_division_id);
                formData.append('permanent_district_id', permanent_district_id);
                formData.append('permanent_police_station_id', permanent_police_station_id);
                formData.append('permanent_post_office_id', permanent_post_office_id);
                formData.append('permanent_address', permanent_address);

                formData.append('present_division_id', present_division_id);
                formData.append('present_district_id', present_district_id);
                formData.append('present_police_station_id', present_police_station_id);
                formData.append('present_post_office_id', present_post_office_id);
                formData.append('present_address', present_address);
                formData.append('second_step', 1);

                $.ajax({
                    url: "{{ route('kyc.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        toastr.success('Contact information submitted successfully!');
                        if (currentStep < steps.length - 1) {
                            currentStep++;
                            updateStep();
                        }
                    },
                    error: function(xhr) {
                        toastr.error('KYC submission failed');
                        console.error(xhr.responseText);
                    }
                });
            });


            $(document).on('click', '.final_step', function() {

                let account_type = $('[name="account_type"]:checked').val();
                let account_number = $('[name="account_number"]').val();

                let hasError = false;

                if (!account_type) {
                    toastr.error('Select account type');
                    hasError = true;
                    return;
                }

                if (!account_number) {
                    toastr.error('Account Number is required');
                    hasError = true;
                    return;
                }

                if (hasError) return;

                

                // Build FormData
                let formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr(
                    'content')); // or manually insert CSRF token
                formData.append('account_type', account_type);
                formData.append('account_number', account_number);
                formData.append('final_step', 1);

                $.ajax({
                    url: "{{ route('kyc.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(data) {
                        toastr.success(
                            'Withdrawal information submitted successfully! Redirecting to Dashboard.'
                        );
                        if (currentStep < steps.length - 1) {
                            currentStep++;
                            updateStep();
                        }
                        setTimeout(() => {
                            window.location.replace(
                                "{{ route('dashboard') }}",
                            );
                        }, 1000)
                    },
                    error: function(xhr) {
                        toastr.error('KYC submission failed');
                        console.error(xhr.responseText);
                    }
                });
            });

        });
    </script>

    <style>
        .form-step {
            display: none;
        }

        .form-step.active {
            display: block;
        }

        .progress-container {
            text-align: center;
        }

        .progress {
            height: 8px;
            background-color: #ddd;
        }

        .progress-bar {
            background-color: #007bff;
            height: 8px;
        }

        .step-labels {
            font-size: 14px;
            font-weight: bold;
        }

        .step-label {
            flex: 1;
            text-align: center;
            color: #999;
        }

        .step-label.active {
            color: #007bff;
        }
    </style>
@endsection
