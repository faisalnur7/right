@extends('layouts.admin_master')

@section('title', 'Payment Option List')
@section('page_title', 'Payment Option List')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title mb-0">Payment Option List</h3>
                <a href="{{ route('payment_option.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                    <i class="fas fa-plus"></i> Add Payment Option
                </a>
            </div>

            <div class="card-body px-0 pb-4 pt-0">
                @if (session('success'))
                    <div class="float-right-bottom alert alert-success mb-3 ">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Account Number</th>
                                <th>Order</th>
                                <th>Logo</th>
                                <th>Zoomable</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paymentOptions as $option)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $option->name }}</td>
                                    <td>{{ $option->account_number }}</td>
                                    <td>{{ $option->order }}</td>
                                    <td>
                                        @if ($option->logo)
                                            <img src="{{ asset($option->logo) }}" alt="Logo" class="h-12">
                                        @else
                                            <span class="text-muted">No logo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $option->zoomable ? 'success' : 'secondary' }}">
                                            {{ $option->zoomable ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('payment_option.edit', $option->id) }}" 
                                           class="btn btn-outline-dark btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('payment_option.delete', $option->id) }}" 
                                              method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-outline-danger btn-sm"
                                                    onclick="return confirm('Are you sure you want to delete this payment option?')" 
                                                    title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($paymentOptions instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-3">
                        {{ $paymentOptions->links('vendor.pagination.tailwind') }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
