@extends('layouts.master')

@section('contents')
    <div class="container-fluid mt-4">
        <div class="card card-primary">
            <div class="card-header bg-gradient-dark text-white">
                <h3 class="card-title">Edit Profile</h3>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="card-body">
                    {{-- Basic Info --}}
                    <h5 class="text-bold">Basic Information</h5>
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
                    <h5 class="mt-4 text-bold">Account & Contact</h5>
                    <div class="row">
                        @php $account_contact_fields = ['mobile_number' => 'Mobile Number','emergency_contact' => 'Emergency Contact','account_type' => 'Account Type','account_number' => 'Account Number']; @endphp
                        @foreach ($account_contact_fields as $field => $label)
                            <div class="col-md-6 form-group">
                                <label>{{ $label }}</label>
                                <input type="text" class="form-control" name="{{ $field }}"
                                    value="{{ old($field, auth()->user()->kyc ? auth()->user()->kyc->$field : null) }}">
                            </div>
                        @endforeach
                    </div>

                    {{-- Personal Info --}}
                    <h5 class="mt-4 text-bold">Personal Information</h5>
                    <div class="row">
                        @php $personal_fields = ['father' => 'Father Name','mother' => 'Mother Name','dob' => 'Date of Birth']; @endphp

                        @foreach ($personal_fields as $field => $label)
                            <div class="col-md-4 form-group">
                                <label>{{ $label }}</label>
                                <input type="{{ $field == 'dob' ? 'date' : 'text' }}" class="form-control"
                                    name="{{ $field }}"
                                    value="{{ old($field, auth()->user()->kyc ? auth()->user()->kyc->$field : null) }}">
                            </div>
                        @endforeach
                    </div>

                    {{-- Address --}}
                    <h5 class="mt-4 text-2xl text-bold">Address</h5>
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
                    <h5 class="mt-4 text-bold">Nominee Information</h5>
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
