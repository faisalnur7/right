@extends('layouts.admin_master')

@section('title','Add Payment Option')
@section('page_title','Add Payment Option')

@section('contents')

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-dark text-white">
            <h3 class="card-title">Add New Payment Option</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('payment_option.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" required value="{{ old('name') }}">
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Account Number</label>
                    <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror" required value="{{ old('account_number') }}">
                    @error('account_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Order</label>
                    <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" required value="{{ old('order') }}">
                    @error('order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Logo (optional)</label>
                    <input type="file" name="logo" class="form-control-file @error('logo') is-invalid @enderror">
                    @error('logo') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mt-3">
                    <button class="btn btn-success" type="submit">Save</button>
                    <a href="{{ route('payment_option.list') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
