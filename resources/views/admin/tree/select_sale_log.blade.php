@extends('layouts.admin_master')

@section('title', 'Select Sale Log')
@section('page_title', 'Select Sale Log for Tree View')

@section('contents')
    <div class="container-fluid">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="float-right-bottom alert-success rounded shadow-sm">{{ session('success') }}</div>
        @elseif(session('error'))
            <div class="float-right-bottom alert-danger rounded shadow-sm">{{ session('error') }}</div>
        @endif

        <div class="card border-0 shadow-sm rounded">
            <div class="card-header text-white" style="background: linear-gradient(to right, #252f51, #1c223a);">
                <h4 class="mb-0">Choose a Sale Log</h4>
            </div>

            <div class="card-body">
                <form action="{{ route('adminTreeView') }}" method="GET">
                    <div class="form-group">
                        <label for="sale_log_id" class="form-label font-weight-bold">Select Sale Log:</label>
                        <select name="sale_log_id" id="sale_log_id" class="form-control" required>
                            <option value="" disabled selected>-- Choose Sale Log --</option>
                            @foreach($saleLogs as $saleLog)
                                <option value="{{ $saleLog->id }}">{{ $saleLog->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-sitemap me-1"></i> View Tree
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
