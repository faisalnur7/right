@extends('layouts.admin_master')

@section('title', 'Supplier List')
@section('page_title', 'Supplier List')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title mb-0">Supplier List</h3>
                <a href="{{ route('supplier.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                    <i class="fas fa-plus"></i> Add Supplier
                </a>
            </div>

            <div class="card-body px-0 pb-4 pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Logo</th>
                                <th>Company Name</th>
                                <th>Contact Person</th>
                                <th>Mobile Number</th>
                                <th>Email</th>
                                <th>Address</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($suppliers as $supplier)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($supplier->image && file_exists(public_path($supplier->image)))
                                            <img src="{{ asset($supplier->image) }}" width="60" alt="Logo" class="rounded">
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $supplier->company_name }}</td>
                                    <td>{{ $supplier->contact_person }}</td>
                                    <td>{{ $supplier->mobile_number }}</td>
                                    <td>{{ $supplier->email }}</td>
                                    <td>{{ $supplier->address }}</td>
                                    <td>
                                        <a href="{{ route('supplier.edit', $supplier->id) }}"
                                            class="btn btn-outline-dark btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('supplier.delete', $supplier->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this supplier?')"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">No suppliers found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="mt-3">
                        {{ $suppliers->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
