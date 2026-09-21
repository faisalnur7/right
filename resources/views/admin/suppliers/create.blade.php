@extends('layouts.admin_master')

@section('title','Add Supplier')
@section('page_title','Add Supplier')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);"><h3 class="card-title mb-0">Add New Supplier</h3></div>

        <div class="card-body">
            @if ($errors->any())
                <div class="float-right-bottom alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('supplier.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>Company Name <span class="text-danger">*</span></label>
                    <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}" required>
                </div>

                <div class="form-group">
                    <label>Contact Person</label>
                    <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person') }}">
                </div>

                <div class="form-group">
                    <label>Mobile Number</label>
                    <input type="text" name="mobile_number" class="form-control" value="{{ old('mobile_number') }}">
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>

                <div class="form-group">
                    <label>Address</label>
                    <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
                </div>

                <div class="form-group">
                    <label>Logo / Image</label>
                    <input type="file" name="image" class="form-control-file">
                </div>

                <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save</button>
            </form>
        </div>
    </div>
</div>
@endsection
