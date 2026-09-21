@extends('layouts.master')

@section('title', 'Prime Requests')
@section('page_title', 'Prime Affiliate Requests')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                style="background: linear-gradient(90deg, #343a40, #212529); prime_table_bg">
                <h3 class="card-title mb-0">Prime Affiliate Requests</h3>
            </div>

            <div class="card-body px-0 pb-4 pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Requester Name</th>
                                <th>Email</th>
                                <th>Requested At</th>
                                <th>Status</th>
                                <th style="width: 180px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($primeRequests as $request)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $request->requester?->name }}</td>
                                    <td>{{ $request->requester?->email }}</td>
                                    <td>{{ $request->created_at->format('d M Y H:s A') }}</td>
                                    <td>
                                        @if ($request->status === App\Models\PrimeRequest::PENDING)
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif ($request->status === App\Models\PrimeRequest::ACCEPTED)
                                            <span class="badge bg-success">Approved</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($request->status === App\Models\PrimeRequest::PENDING)
                                            <form action="{{ route('prime.respond', $request->id) }}" method="POST"
                                                class="d-inline-block">
                                                @csrf
                                                <input type="hidden" name="status"
                                                    value="{{ App\Models\PrimeRequest::ACCEPTED }}">
                                                <button type="submit" class="btn btn-success btn-sm"
                                                    onclick="return confirm('Accept this request?')">
                                                    <i class="fas fa-check"></i> Accept
                                                </button>
                                            </form>

                                            <form action="{{ route('prime.respond', $request->id) }}" method="POST"
                                                class="d-inline-block">
                                                @csrf
                                                <input type="hidden" name="status"
                                                    value="{{ App\Models\PrimeRequest::REJECTED }}">
                                                <button type="submit" class="btn btn-danger btn-sm"
                                                    onclick="return confirm('Reject this request?')">
                                                    <i class="fas fa-times"></i> Reject
                                                </button>
                                            </form>
                                        @else
                                            <em>No action available</em>
                                        @endif


                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 mx-3">
                    {{ $primeRequests->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>
@endsection
