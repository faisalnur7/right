@extends('layouts.admin_master')

@section('title','Edit Attribute')
@section('page_title','Edit Attribute')

@section('contents')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
            <h3 class="card-title">Edit Attribute</h3>
        </div>
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

            <form action="{{ route('attribute.update', $attribute->id) }}" method="POST">
                @csrf
                @method('POST')

                <div class="form-group">
                    <label for="name">Attribute Name</label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name', $attribute->name) }}" 
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">Update Attribute</button>
                    <a href="{{ route('attribute.list') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
