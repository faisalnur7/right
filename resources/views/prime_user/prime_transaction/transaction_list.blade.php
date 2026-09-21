@extends('layouts.master')

@section('title', 'Prime Transactions')
@section('page_title', 'My Prime Transactions')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title mb-0">My Prime Transactions</h3>

                <!-- Per Page Dropdown -->
                <form method="GET" action="{{ url()->current() }}" id="perPageForm" class="d-flex align-items-center text-black ml-auto">
                    <label for="per_page" class="me-2 mb-0 text-white">Per Page:</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm"
                        onchange="document.getElementById('perPageForm').submit()">
                        @foreach ([10, 25, 50, 100] as $size)
                            <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                {{ $size }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>


            <div class="card-body px-0 pb-4 pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>BD</th>
                                <th>Source ID</th>
                                <th>Type</th>
                                <th>Income</th>
                                <th>Disbursement</th>
                                <th>Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($primeTransaction as $tx)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $tx->business_day ?? 'Admin' }}</td>
                                    <td>{{ $tx->source_user->kyc->affiliate_id ?? 'N/A' }}</td>
                                    <td>{{ App\Models\PrimeTransaction::$commissionTypes[$tx->type] ?? 'Unknown Commission' }}</td>
                                    <td class="text-success">{{ number_format($tx->amount_in, 2) }}</td>
                                    <td class="text-danger">{{ number_format($tx->amount_out, 2) }}</td>
                                    <td>{{ $tx->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No transactions found.</td>
                                </tr>
                            @endforelse
                        </tbody>

                        @if ($primeTransaction->count())
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="4" class="text-end">Total:</td>
                                    <td class="text-success">{{ number_format($primeTransaction->sum('amount_in'), 2) }}
                                    </td>
                                    <td class="text-danger">{{ number_format($primeTransaction->sum('amount_out'), 2) }}
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>

                </div>
                <div class="mt-3 px-3">
                    {{ $primeTransaction->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>
@endsection
