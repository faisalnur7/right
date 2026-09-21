@extends('layouts.admin_master')

@section('title', 'User List')
@section('page_title', 'User List')

@section('contents')
    @php
        $saleLogs = App\Models\SaleLog::all();
    @endphp
    <div class="container-fluid">
        {{-- Filter Form --}}
        <div class="card border-0 shadow-sm rounded mb-2">
            <div class="card-body">
                @include('admin.user.partials._user_filter')
            </div>
        </div>
        <div class="card">
            <div class="card-body px-0 pb-4 pt-0">
                @if (session('success'))
                    <div class="float-right-bottom alert alert-success mb-3 ">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Affiliate ID</th>
                                <th>Reference ID</th>
                                <th>Email</th>
                                <th>Mobile</th>
                                <th>Stock</th>
                                <th>Sale Log Status<br>
                                    @foreach ($saleLogs as $saleLog)
                                        <span class="badge bg-blue-600 text-white">{{ $saleLog->name }}</span>
                                    @endforeach
                                </th>
                                <th class="text-center">Today's Income<br>
                                    <span class="badge bg-blue-600 text-white">Leads</span>
                                    <span class="badge bg-gray-500 text-white">Affiliate</span>
                                    <span class="badge bg-green-600 text-white">Subscription</span>
                                    <span class="badge bg-warning text-dark">Associate</span>
                                </th>
                                <th>Prime Wallet</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->kyc->affiliate_id ?? '-' }}</td>
                                    <td>{{ $user->referenceUser->kyc->affiliate_id ?? '-' }}</td>
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
                                    <td class="text-center">
                                        <div class="flex flex-row gap-1 items-center justify-center h-full">
                                            <span
                                                class="badge bg-blue-600 text-white">{{ $user->getIncomeByDateRange()['Leads'] }}</span>
                                            <span
                                                class="badge bg-gray-500 text-white">{{ $user->getIncomeByDateRange()['Affiliate'] }}</span>
                                            <span
                                                class="badge bg-green-600 text-white">{{ $user->getIncomeByDateRange()['Subscription'] }}</span>
                                            @if ($user->is_super_prime)
                                                <span
                                                    class="badge bg-warning text-dark">{{ $user->getIncomeByDateRange()['Associate'] }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    <td class="text-right">৳{{ number_format($user->primeWallet->balance, 0) }}</td>

                                    <td>
                                        <a href="{{ route('adminUserStock', $user->id) }}" class="btn btn-md btn-primary">
                                            <i class="fas fa-user mr-1"></i>
                                        </a>
                                        <a href="{{ route('adminUserStock', $user->id) }}" class="btn btn-md btn-dark">
                                            <i class="fas fa-box mr-1"></i> Stock
                                        </a>
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


                {{-- Pagination --}}
                @if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
                    <div class="mt-3 mx-3">
                        {{ $users->appends(request()->query())->links('vendor.pagination.tailwind') }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
