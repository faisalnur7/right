@extends('layouts.admin_master')

@section('title', 'Sale Log list')
@section('page_title', 'Sale Log list')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title mb-0">Sale Log List</h3>
                <a href="{{ route('sale_log.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                    <i class="fas fa-plus"></i> Add Sale Log
                </a>
            </div>

            <div class="card-body px-0 pb-4 pt-0">
                @if (session('success'))
                    <div class="float-right-bottom alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Total Commission (TK)</th>
                                <th>Tier 1 (%)</th>
                                <th>Tier 2 (%)</th>
                                <th>Total Use Commission (TK)</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale_logs as $sale_log)
                                <tr>
                                    <td>{{ $sale_log->name }}</td>
                                    <td>{{ number_format($sale_log->total_commission, 2) }}</td>
                                    <td>{{ number_format($sale_log->tier1_percentage, 2) }}</td>
                                    <td>{{ number_format($sale_log->tier2_percentage, 2) }}</td>
                                    <td>{{ number_format($sale_log->total_use_commission ?? 0, 2) }}</td>
                                    <td>
                                        <a href="{{ route('sale_log.edit', $sale_log->id) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('sale_log.delete', $sale_log->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>


                </div>
            </div>
        </div>
    </div>
@endsection
