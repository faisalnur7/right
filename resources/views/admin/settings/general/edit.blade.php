@extends('layouts.admin_master')

@section('title','General Settings')
@section('page_title','General Settings')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
            <h3 class="card-title">Edit General Settings</h3>
        </div>
        <div class="card-body">
            @if (session('success'))
                <div class="float-right-bottom alert alert-success mb-3 ">{{ session('success') }}</div>
            @endif
            <form action="{{ route('admin.general-settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="site_name">Site Name</label>
                    <input type="text" name="site_name" id="site_name" class="form-control @error('site_name') is-invalid @enderror" value="{{ old('site_name', $settings->site_name ?? '') }}">
                    @error('site_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="site_title">Site Title</label>
                    <input type="text" name="site_title" id="site_title" class="form-control @error('site_title') is-invalid @enderror" value="{{ old('site_title', $settings->site_title ?? '') }}">
                    @error('site_title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="business_start_date">Business Start Date</label>
                    <input type="date" name="business_start_date" id="business_start_date" class="form-control @error('business_start_date') is-invalid @enderror" value="{{ old('business_start_date', $settings->business_start_date ?? '') }}">
                    @error('business_start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $settings->email ?? '') }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $settings->phone ?? '') }}">
                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $settings->address ?? '') }}</textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="facebook">Facebook</label>
                    <input type="url" name="facebook" id="facebook" class="form-control @error('facebook') is-invalid @enderror" value="{{ old('facebook', $settings->facebook ?? '') }}">
                    @error('facebook') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="twitter">Twitter</label>
                    <input type="url" name="twitter" id="twitter" class="form-control @error('twitter') is-invalid @enderror" value="{{ old('twitter', $settings->twitter ?? '') }}">
                    @error('twitter') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="instagram">Instagram</label>
                    <input type="url" name="instagram" id="instagram" class="form-control @error('instagram') is-invalid @enderror" value="{{ old('instagram', $settings->instagram ?? '') }}">
                    @error('instagram') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="linkedin">LinkedIn</label>
                    <input type="url" name="linkedin" id="linkedin" class="form-control @error('linkedin') is-invalid @enderror" value="{{ old('linkedin', $settings->linkedin ?? '') }}">
                    @error('linkedin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="youtube">YouTube</label>
                    <input type="url" name="youtube" id="youtube" class="form-control @error('youtube') is-invalid @enderror" value="{{ old('youtube', $settings->youtube ?? '') }}">
                    @error('youtube') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Site Logo Upload -->
                <div class="form-group">
                    <label for="site_logo">Site Logo</label>
                    <input type="file" name="site_logo" class="form-control-file @error('site_logo') is-invalid @enderror">
                    @error('site_logo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    @if(!empty($settings->site_logo))
                        <div class="mt-4">
                            <img src="{{ asset($settings->site_logo) }}" class="h-24" alt="Logo" height="80">
                        </div>
                    @endif
                </div>

                <!-- Favicon Upload -->
                <div class="form-group">
                    <label for="site_favicon">Site Favicon</label>
                    <input type="file" name="site_favicon" class="form-control-file @error('site_favicon') is-invalid @enderror">
                    @error('site_favicon') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    @if(!empty($settings->site_favicon))
                        <div class="mt-4">
                            <img src="{{ asset($settings->site_favicon) }}" class="h-20" alt="Favicon" height="40">
                        </div>
                    @endif
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-success">Update Settings</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-neutral">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
