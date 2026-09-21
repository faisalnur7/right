@extends('layouts.admin_master')

@section('title', 'Subscription Package List')
@section('page_title', 'Subscription Package List')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title mb-0">Subscription Package List</h3>
                <a href="{{ route('subscription_package.create') }}" class="btn btn-primary btn-sm ml-auto text-bold">
                    <i class="fas fa-plus"></i> Add Package
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
                                <th>Subtitle</th>
                                <th>Duration (Days)</th>
                                <th>Amount</th>
                                <th>Discount (%)</th>
                                <th>Total Commission (TK)</th>
                                <th>Tier 1 (%)</th>
                                <th>Tier 2 (%)</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subscriptionPackages as $package)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $package->name }}</td>
                                    <td>{{ $package->sub_title }}</td>
                                    <td>{{ $package->duration }}</td>
                                    <td>{{ number_format($package->amount, 2) }}</td>
                                    <td>{{ number_format($package->discount, 2) }}</td>
                                    <td>{{ number_format($package->total_commission, 2) }}</td>
                                    <td>{{ number_format($package->tier1_percentage, 2) }}</td>
                                    <td>{{ number_format($package->tier2_percentage, 2) }}</td>
                                    <td>
                                        <a href="{{ route('subscription_package.edit', $package->id) }}"
                                            class="btn btn-outline-dark btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('subscription_package.delete', $package->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Are you sure you want to delete this package?')"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No subscription packages found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if ($subscriptionPackages instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-3">
                        {{ $subscriptionPackages->links('vendor.pagination.tailwind') }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
