@extends('layouts.admin_master')

@section('title', 'Add Sale Log')
@section('page_title', 'Add Sale Log')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title">Add New Sale Log</h3>
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

                <form action="{{ route('sale_log.store') }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="name">Sale Log Name</label>
                        <input type="text" name="name" id="name"
                            class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </form>


            </div>
        </div>
    </div>
@endsection
