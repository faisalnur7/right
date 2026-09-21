@extends('layouts.admin_master')

@section('title', 'Product List')
@section('page_title', 'Product List')

@section('contents')
    <div class="container-fluid">

        {{-- Success Alert --}}
        @if (session('success'))
            <div class="alert float-right-bottom alert-success shadow-sm rounded ">
                {{ session('success') }}
            </div>
        @endif

        {{-- Filter Form --}}
        <div class="card border-0 shadow-sm rounded mb-4">
            <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h4 class="mb-0">Filter Products</h4>
            </div>
            <div class="card-body">
                @include('admin.products._filter_form')
            </div>
        </div>

        {{-- Product Table --}}
        <div class="card border-0 shadow-sm rounded">
            <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h4 class="mb-0">All Products</h4>
                <div class="ml-auto">
                    <a href="{{ route('product.create') }}" class="btn btn-primary shadow-sm">
                        <i class="fas fa-plus"></i> Add New Product
                    </a>
                </div>
            </div>


            <div class="card-body px-0 pb-4 pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>SKU</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        @if ($product->image && file_exists(public_path($product->image)))
                                            <img src="{{ asset($product->image) }}" class="rounded"
                                                alt="{{ $product->name }}" width="60" height="60">
                                        @else
                                            <span class="badge badge-secondary">No Image</span>
                                        @endif
                                    </td>
                                    <td class="font-weight-bold">{{ $product->name }}</td>
                                    <td>{{ $product->category->name ?? 'N/A' }}</td>
                                    <td>{{ $product->subCategory->name ?? 'N/A' }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>৳{{ number_format($product->price, 2) }}</td>
                                    <td>{{ $product->calculated_stock }}</td>
                                    <td>
                                        @if ($product->status)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="text-center">

                                        <a href="{{ route('product.show', $product->id) }}"
                                            class="btn btn-outline-info btn-sm rounded" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('product.edit', $product->id) }}"
                                            class="btn btn-outline-dark btn-sm rounded" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('product.delete', $product->id) }}" method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center text-muted">No products found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($products instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-3 mx-3">
                        {{ $products->appends(request()->query())->links('vendor.pagination.tailwind') }}
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
