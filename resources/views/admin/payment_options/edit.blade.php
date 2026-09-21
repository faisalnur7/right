@extends('layouts.admin_master')

@section('title','Edit Payment Option')
@section('page_title','Edit Payment Option')

@section('contents')

<div class="container-fluid">
    <div class="card">
        <div class="card-header bg-gradient-dark text-white">
            <h3 class="card-title">Edit Payment Option</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('payment_option.update', $paymentOption->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $paymentOption->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Account Number</label>
                    <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror" required value="{{ old('account_number') }}">
                    @error('account_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Order</label>
                    <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" value="{{ old('order', $paymentOption->order) }}" required>
                    @error('order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label>Current Logo</label><br>
                    @if($paymentOption->logo)
                        <img src="{{ asset($paymentOption->logo) }}" alt="Logo" class="h-12">
                    @else
                        <p>No logo uploaded.</p>
                    @endif
                </div>

                <div class="form-group">
                    <label>Change Logo</label>
                    <input type="file" name="logo" class="form-control-file @error('logo') is-invalid @enderror">
                    @error('logo') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="form-group mt-3">
                    <button class="btn btn-primary" type="submit">Update</button>
                    <a href="{{ route('payment_option.list') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
