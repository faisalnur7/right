@extends('layouts.master')

@section('title', 'Leads Centre')
@section('page_title', 'Leads Centre')

@section('contents')
    <div class="container-fluid">
        {{-- Filter Form --}}
        <div class="card border-0 shadow-sm rounded mb-2">
            <div class="card-body">
                @include('prime_user.prime_request.partials._affiliate_user_filter')
            </div>
        </div>
        <div class="card">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                style="background: linear-gradient(90deg, #343a40, #212529); prime_table_bg">
                <h3 class="card-title mb-0">Leads Centre</h3>
            </div>

            <div class="card-body px-0 pb-4 pt-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Affiliate ID</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Stock</th>
                                <th>Sale Log Status<br>
                                    @foreach ($saleLogs as $saleLog)
                                        <span class="badge bg-blue-600 text-white">{{ $saleLog->name }}</span>
                                    @endforeach
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->kyc->affiliate_id ?? '-' }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?? '-' }}</td>
                                    <td>
                                        @foreach ($saleLogs as $saleLog)
                                            @php
                                                $userId = $user->id;

                                                // Get total purchased quantity for this saleLog
                                                $purchasedQuantity = App\Models\OrderItem::whereHas('order', function (
                                                    $query,
                                                ) use ($userId) {
                                                    $query
                                                        ->where('user_id', $userId)
                                                        ->where('status', App\Models\Order::COMPLETED);
                                                })
                                                    ->where('sale_log_id', $saleLog->id)
                                                    ->sum('quantity');

                                                // Get total used quantity for this saleLog
                                                $usedQuantity = App\Models\UseProduct::where(function ($query) use (
                                                    $userId,
                                                ) {
                                                    $query->where('from_user_id', $userId)->orWhere('user_id', $userId);
                                                })
                                                    ->where('sale_log_id', $saleLog->id)
                                                    ->sum('quantity');

                                                // Remaining = purchased - used
                                                $remainingQuantity = max(0, $purchasedQuantity - $usedQuantity);

                                                // Tree active status
                                                $isActive = $user->isActiveInSaleLogTreeAdmin($saleLog->id);
                                                $activeStyle = 'bg-red-600';
                                                if ($isActive == App\Models\BinaryTreeNode::ACTIVE_IN_TREE) {
                                                    $activeStyle = 'bg-green-600';
                                                } elseif (
                                                    $isActive == App\Models\BinaryTreeNode::INACTIVE_PENDING_IN_TREE
                                                ) {
                                                    $activeStyle = 'bg-yellow-400';
                                                }
                                            @endphp

                                            <span class="badge me-1 text-white {{ $activeStyle }}">
                                                {{ $saleLog->item }} {{ $remainingQuantity }}
                                            </span>
                                        @endforeach

                                    </td>
                                    <td>
                                        @foreach ($saleLogs as $saleLog)
                                            @php
                                                $isActive = $user->isActiveInSaleLogTreeAdmin($saleLog->id);
                                                $activeStyle = 'bg-red-600';
                                                if ($isActive == App\Models\BinaryTreeNode::ACTIVE_IN_TREE) {
                                                    $activeStyle = 'bg-green-600';
                                                } elseif (
                                                    $isActive == App\Models\BinaryTreeNode::INACTIVE_PENDING_IN_TREE
                                                ) {
                                                    $activeStyle = 'bg-yellow-400';
                                                }
                                            @endphp
                                            <span class="badge me-1 text-white {{ $activeStyle }}">
                                                {{ $user->maxActivationInSaleLog($saleLog->id) }}
                                            </span>
                                        @endforeach
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">No users with stock found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 mx-3">
                    {{ $users->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>
@endsection
