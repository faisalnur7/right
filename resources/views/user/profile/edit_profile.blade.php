@extends('layouts.master')

@section('title', 'Profile Settings')

@section('contents')
    <div class="container-fluid mt-4 profile-settings-page">
        <div class="card card-primary profile-settings-card">
            <div class="card-header bg-gradient-dark text-white">
                <div class="profile-header-copy">
                    <span class="profile-eyebrow">Account center</span>
                    <h3 class="card-title">Profile settings</h3>
                    <p>Keep your personal, identity, contact and payment details up to date.</p>
                </div>
                <span class="profile-header-badge"><i class="fas fa-user-shield"></i> Secure profile</span>
                <span class="profile-header-orb" aria-hidden="true"></span>
            </div>

            <nav class="profile-section-nav" aria-label="Profile sections">
                <a href="#basic-information">Basic information</a>
                <a href="#account-contact">Account &amp; contact</a>
                <a href="#identity-document">Identity document</a>
                <a href="#personal-information">Personal information</a>
                <a href="#address-information">Addresses</a>
                <a href="#nominee-information">Nominee</a>
            </nav>

            <form action="{{ route('updateUserProfile') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card-body">
                    {{-- Basic Info --}}
                    <h5 id="basic-information" class="text-bold profile-section-title">Basic Information</h5>
                    <div class="row">
                        @php $basic_fields = ['name' => 'Name','email' => 'Email','phone' => 'Phone','photo' => 'Photo']; @endphp
                        @foreach ($basic_fields as $field => $label)
                            <div class="col-md-6 form-group">
                                <label>{{ $label }}</label>

                                @if ($field == 'photo')
                                    @if (auth()->user()->photo)
                                        <img src="{{ asset('storage/' . auth()->user()->photo) }}" alt="Profile Photo"
                                            style="max-height: 100px; display: block; margin-bottom: 10px;">
                                    @endif
                                    <input type="file" class="form-control-file" name="{{ $field }}"
                                        id="{{ $field }}">
                                @else
                                    <input type="text" class="form-control" name="{{ $field }}"
                                        value="{{ old($field, auth()->user()->$field) }}">
                                @endif
                            </div>
                        @endforeach

                    </div>

                    {{-- Contact & Account --}}
                    <h5 id="account-contact" class="mt-4 text-bold profile-section-title">Account &amp; Contact</h5>
                    <div class="row">
                        @php $account_contact_fields = ['mobile_number' => 'Mobile Number','emergency_contact' => 'Emergency Contact','account_number' => 'Account Number']; @endphp
                        @foreach ($account_contact_fields as $field => $label)
                            <div class="col-md-6 form-group">
                                <label>{{ $label }}</label>
                                <input type="text" class="form-control" name="{{ $field }}"
                                    value="{{ old($field, auth()->user()->kyc ? auth()->user()->kyc->$field : null) }}">
                            </div>
                        @endforeach
                        <div class="col-md-6 form-group">
                            <label>Withdrawal account type</label>
                            <select class="form-control select2" name="account_type" data-placeholder="Select account type">
                                <option value="">Select Account Type</option>
                                <option value="{{ App\Models\Kyc::BKASH }}" @selected(old('account_type', auth()->user()->kyc?->account_type) == App\Models\Kyc::BKASH)>Bkash</option>
                                <option value="{{ App\Models\Kyc::NAGAD }}" @selected(old('account_type', auth()->user()->kyc?->account_type) == App\Models\Kyc::NAGAD)>Nagad</option>
                                <option value="{{ App\Models\Kyc::ROCKET }}" @selected(old('account_type', auth()->user()->kyc?->account_type) == App\Models\Kyc::ROCKET)>Rocket</option>
                            </select>
                        </div>
                    </div>

                    {{-- Identity Document --}}
                    <h5 id="identity-document" class="mt-4 text-bold profile-section-title">Identity Document</h5>
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>Document Type</label>
                            <select class="form-control select2" name="doc_type" data-placeholder="Select document type">
                                <option value="">Select Document Type</option>
                                <option value="{{ App\Models\Kyc::NID }}" @selected(old('doc_type', auth()->user()->kyc?->doc_type) == App\Models\Kyc::NID)>NID</option>
                                <option value="{{ App\Models\Kyc::BC }}" @selected(old('doc_type', auth()->user()->kyc?->doc_type) == App\Models\Kyc::BC)>Birth Certificate</option>
                                <option value="{{ App\Models\Kyc::PASSPORT }}" @selected(old('doc_type', auth()->user()->kyc?->doc_type) == App\Models\Kyc::PASSPORT)>Passport</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label>Document Number</label>
                            <input type="text" class="form-control" name="document_number"
                                value="{{ old('document_number', auth()->user()->kyc?->document_number) }}">
                        </div>
                    </div>

                    {{-- Personal Info --}}
                    <h5 id="personal-information" class="mt-4 text-bold profile-section-title">Personal Information</h5>
                    <div class="row">
                        @php $personal_fields = ['father' => 'Father Name','mother' => 'Mother Name','dob' => 'Date of Birth']; @endphp

                        @foreach ($personal_fields as $field => $label)
                            <div class="col-md-4 form-group">
                                <label>{{ $label }}</label>
                                @if ($field === 'dob')
                                    <div class="input-group date profile-date-picker" id="dobPicker" data-target-input="nearest">
                                        <input type="text" class="form-control datetimepicker-input" name="dob"
                                            value="{{ old('dob', auth()->user()->kyc?->dob) }}" placeholder="YYYY-MM-DD"
                                            data-target="#dobPicker" autocomplete="off">
                                        <div class="input-group-append" data-target="#dobPicker" data-toggle="datetimepicker">
                                            <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                        </div>
                                    </div>
                                @else
                                    <input type="text" class="form-control" name="{{ $field }}"
                                        value="{{ old($field, auth()->user()->kyc ? auth()->user()->kyc->$field : null) }}">
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Address --}}
                    <h5 id="address-information" class="mt-4 text-2xl text-bold profile-section-title">Address</h5>
                    @foreach (['present', 'permanent'] as $type)
                        <div class="row">
                            @php $addresses = ['division_id' => 'Division','district_id' => 'District','police_station_id' => 'Police Station','post_office_id' => 'Post Office','post_code' => 'Post Code','address' => 'Full Address']; @endphp
                            <div class="col-md-12"><strong class="d-block text-capitalize text-lg">{{ $type }}
                                    Address</strong></div>
                            @foreach ($addresses as $suffix => $label)
                                <div class="col-md-4 form-group">
                                    <label>{{ $label }}</label>
                                    @if (in_array($suffix, ['division_id', 'district_id', 'police_station_id', 'post_office_id']))
                                        <select class="form-control select2" name="{{ $type . '_' . $suffix }}">
                                            <option value="">Select {{ $label }}</option>
                                            @foreach (${$suffix . 's'} as $item)
                                                <option value="{{ $item->id }}"
                                                    {{ old($type . '_' . $suffix, auth()->user()->kyc->{$type . '_' . $suffix}) == $item->id ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="text" class="form-control" name="{{ $type . '_' . $suffix }}"
                                            value="{{ old($type . '_' . $suffix, auth()->user()->kyc->{$type . '_' . $suffix}) }}">
                                    @endif

                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    {{-- Nominee Info --}}
                    <h5 id="nominee-information" class="mt-4 text-bold profile-section-title">Nominee Information</h5>
                    <div class="row">
                        @foreach ([
                                'nominee_name' => 'Nominee Name',
                                'relation' => 'Relation',
                            ] as $field => $label)
                            <div class="col-md-6 form-group">
                                <label>{{ $label }}</label>
                                @if ($field == 'doc_type')
                                    <select class="form-control" name="{{ $field }}">
                                        <option value="nid">NID</option>
                                        <option value="passport">Passport</option>
                                        <option value="birth_certificate">Birth Certificate</option>
                                    </select>
                                @elseif ($field == 'document_file')
                                    <input type="file" class="form-control-file" name="{{ $field }}">
                                @else
                                    <input type="text" class="form-control" name="{{ $field }}"
                                        value="{{ old($field, auth()->user()->nominee ? auth()->user()->nominee->$field : null) }}">
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-success">Update Profile</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function() {
            $('#dobPicker').datetimepicker({
                format: 'YYYY-MM-DD',
                useCurrent: false,
                maxDate: moment()
            });
        });
    </script>
    <style>
        .profile-settings-page { min-height: calc(100vh - 74px); padding-bottom: 42px; background: #f4f7fb; }
        .profile-settings-card { width: 100%; overflow: visible; border: 0; border-radius: 22px; background: #fff; box-shadow: 0 18px 42px rgba(18, 37, 58, .1); }
        .profile-settings-card > .card-header { position: relative; display: flex; align-items: center; justify-content: space-between; min-height: 142px; overflow: hidden; padding: 28px 34px; border: 0; border-radius: 22px 22px 0 0; background: linear-gradient(116deg, #11243e 0%, #1b3b5d 62%, #235d7f 100%) !important; }
        .profile-header-copy { position: relative; z-index: 1; }
        .profile-eyebrow { display: block; margin-bottom: 7px; color: #ff5962; font-size: 11px; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; }
        .profile-settings-card > .card-header .card-title { float: none; display: block; margin: 0; font-size: 30px; font-weight: 700; letter-spacing: -.04em; }
        .profile-header-copy p { margin: 7px 0 0; color: #b9cce0; font-size: 14px; }
        .profile-header-badge { position: relative; z-index: 1; flex: 0 0 auto; margin-left: auto; padding: 10px 14px; color: #dff8e9; border: 1px solid rgba(173,239,200,.35); border-radius: 999px; background: rgba(29,135,85,.2); font-size: 12px; font-weight: 700; }
        .profile-header-badge i { margin-right: 6px; color: #6de29b; }
        .profile-header-orb { position: absolute; right: 6%; top: -170px; width: 360px; height: 360px; border: 1px solid rgba(255,255,255,.15); border-radius: 50%; box-shadow: 0 0 0 28px rgba(255,255,255,.025), 0 0 0 65px rgba(255,255,255,.025); background: radial-gradient(circle, rgba(224,47,83,.28), transparent 63%); }
        .profile-settings-card .card-body { overflow: visible; padding: 32px 34px; }
        .profile-section-nav { display: flex; flex-wrap: wrap; gap: 8px; padding: 14px 30px; border-bottom: 1px solid #e4eaf1; background: #f7f9fc; }
        .profile-section-nav a { padding: 8px 13px; color: #39516c; border: 1px solid #d8e2ed; border-radius: 999px; background: #fff; font-size: 12px; font-weight: 700; text-decoration: none; transition: .2s ease; }
        .profile-section-nav a:hover { color: #fff; border-color: #e2313e; background: #e2313e; }
        .profile-section-title { scroll-margin-top: 20px; display: flex; align-items: center; gap: 10px; margin-bottom: 18px; padding: 12px 14px; color: #152b45; border: 1px solid #e7edf4; border-radius: 12px; background: linear-gradient(90deg, #f8fafc, #fff); font-size: 18px; }
        .profile-section-title::before { display: inline-flex; width: 28px; height: 28px; align-items: center; justify-content: center; color: #df3441; border-radius: 8px; background: #ffecef; font-family: "Font Awesome 5 Free"; font-size: 12px; font-weight: 900; }
        #basic-information::before { content: "\f2bd"; }
        #account-contact::before { content: "\f2bb"; }
        #identity-document::before { content: "\f2c2"; }
        #personal-information::before { content: "\f007"; }
        #address-information::before { content: "\f3c5"; }
        #nominee-information::before { content: "\f0c0"; }
        .profile-settings-card .form-group { margin-bottom: 20px; }
        .profile-settings-card label { display: block; margin-bottom: 8px; color: #223853; font-size: 13px; font-weight: 700; }
        .profile-settings-card .form-control { min-height: 46px; border: 1px solid #dce6f0; border-radius: 10px; color: #18314e; background: #fbfdff; box-shadow: none; transition: .2s ease; }
        .profile-settings-card .form-control:focus { border-color: #e2313e; background: #fff; box-shadow: 0 0 0 4px rgba(226,49,62,.1); }
        .profile-settings-card .select2-container { width: 100% !important; }
        .profile-settings-card .select2-container--default .select2-selection--single { height: 46px; padding: 8px 13px; border: 1px solid #dce6f0; border-radius: 10px; background: #fbfdff; }
        .profile-settings-card .select2-container--default .select2-selection--single .select2-selection__rendered { color: #18314e; line-height: 28px; }
        .profile-settings-card .select2-container--default.select2-container--open .select2-selection--single { border-color: #e2313e; box-shadow: 0 0 0 4px rgba(226,49,62,.1); }
        .profile-date-picker .input-group-text { min-width: 46px; justify-content: center; color: #df3441; border: 1px solid #dce6f0; border-left: 0; border-radius: 0 10px 10px 0; background: #fff5f6; }
        .profile-date-picker .form-control { border-radius: 10px 0 0 10px; }
        .profile-settings-card .card-footer { padding: 22px 34px; border-top: 1px solid #e8eef5; background: #fbfcfe; border-radius: 0 0 20px 20px; }
        .profile-settings-card .card-footer .btn { min-width: 170px; padding: 12px 20px; border: 0; border-radius: 10px; background: #182941; box-shadow: 0 8px 18px rgba(24,41,65,.16); font-weight: 700; }
        .profile-settings-card .card-footer .btn:hover { background: #e2313e; }
        .bootstrap-datetimepicker-widget { z-index: 1100; }
        @media (max-width: 768px) { .profile-settings-page { padding: 12px 0 28px; } .profile-settings-card > .card-header, .profile-settings-card .card-body { padding: 24px 18px; } .profile-settings-card > .card-header { display: block; min-height: 170px; } .profile-header-badge { display: inline-block; margin-top: 18px; } .profile-header-orb { right: -150px; } .profile-section-nav { padding: 12px 18px; } .profile-settings-card .card-footer { padding: 18px; } .profile-settings-card .card-footer .btn { width: 100%; } }
    </style>
@endsection
