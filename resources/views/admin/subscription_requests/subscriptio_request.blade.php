@extends('layouts.admin_master')

@section('title', 'Subscription Request List')
@section('page_title', 'Subscription Request List')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div
                class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title mb-0">Subscription Request List</h3>
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
                                <th>Prime Name</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>District</th>
                                <th>Thana</th>
                                <th>Post Office</th>
                                <th>Package</th>
                                <th>Price</th>
                                <th>Payment Type</th>
                                <th>Transaction ID</th>
                                <th>Account Number</th>
                                <th>Requested At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($packageUsers as $packageUser)
                                <?php
                                $primeRequest = App\Models\PrimeRequest::query()->where('requester_id', $packageUser->user_id)->latest()->first();
                                $primeUser = $primeRequest?->prime;
                                
                                ?>
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $primeUser->name ?? 'N/A' }}</td>
                                    <td>{{ $packageUser->user->name ?? 'N/A' }}</td>
                                    <td>{{ $packageUser->user->phone ?? 'N/A' }}</td>
                                    <td>{{ $packageUser->user->district->name ?? 'N/A' }}</td>
                                    <td>{{ $packageUser->user->police_station->name ?? 'N/A' }}</td>
                                    <td>{{ $packageUser->user->post_office->name ?? 'N/A' }}</td>
                                    <td>{{ $packageUser->subscription_package->name ?? 'N/A' }}</td>
                                    <td>{{ $packageUser->subscription_package->discount ?? 'N/A' }}</td>
                                    <td>{{ $packageUser->payment_options->name ?? 'N/A' }}</td>
                                    <td>{{ $packageUser->transaction_number ?? 'N/A' }}</td>
                                    <td>{{ $packageUser->transaction_mobile_number ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($packageUser->created_at)->format('d M Y, h:i A') ?? 'N/A' }}
                                    </td>
                                    <td>
                                        @if ($packageUser->status === App\Models\User::USER_PACKAGE_PENDING)
                                            <form action="{{ route('admin.respond', $packageUser->id) }}" method="POST"
                                                class="d-inline-block">
                                                @csrf
                                                <input type="hidden" name="status"
                                                    value="{{ App\Models\User::USER_PACKAGE_ACTIVE }}">
                                                <button type="submit" class="btn btn-success btn-sm"
                                                    onclick="return confirm('Accept this request?')">
                                                    <i class="fas fa-check"></i> Accept
                                                </button>
                                            </form>
                                        @else
                                            <em>No action available</em>
                                        @endif

                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="14" class="text-center">No data available.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
