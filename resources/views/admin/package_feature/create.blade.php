@extends('layouts.admin_master')

@section('title', 'Add Package Feature')
@section('page_title', 'Add Package Feature')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
            <h3 class="card-title">Add New Package Feature</h3>
        </div>
        <div class="card-body">
            <!-- Form to create package feature -->
            <form action="{{ route('package_feature.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Feature Name</label>
                    <input type="text" name="name" id="name"
                        class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="order">Order</label>
                    <input type="number" name="order" id="order"
                        class="form-control @error('order') is-invalid @enderror"
                        value="{{ old('order') }}" required>
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">Save Feature</button>
                    <a href="{{ route('package_feature.list') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
