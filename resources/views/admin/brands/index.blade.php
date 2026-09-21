@extends('layouts.admin_master')

@section('title', 'Brand list')
@section('page_title', 'Brand list')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title mb-0">Brand List</h3>
                <a href="{{ route('brand.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                    <i class="fas fa-plus"></i> Add Brand
                </a>
            </div>

            <div class="card-body px-0 pb-4 pt-0">
                @if (session('success'))
                    <div class="float-right-bottom alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($brands as $brand)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $brand->name }}</td>
                                    <td>
                                        <a href="{{ route('brand.edit', $brand->id) }}" class="btn btn-outline-dark btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('brand.delete', $brand->id) }}" method="POST"
                                            style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Are you sure?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center">No brands found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
