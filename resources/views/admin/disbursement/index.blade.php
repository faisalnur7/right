@extends('layouts.admin_master')

@section('title', 'Disbursement List')
@section('page_title', 'Disbursement List')

@section('contents')
    @php
        $saleLogs = App\Models\SaleLog::all();
    @endphp
    <div class="container-fluid">
        {{-- Filter Form --}}
        <div class="card border border-black shadow-sm rounded mb-3">
            <div class="card-body">
                @include('admin.disbursement.partials._user_filter')
            </div>
        </div>

        <form action="{{ route('admin.disbursement.bulk') }}" method="POST" id="bulkForm">
            @csrf
            <input type="hidden" name="selected_business_day" value="{{ request('business_day') }}" />
            <input type="hidden" name="business_day_number" value="{{ $business_day_number ?? null }}" />
            <div class="card">
                <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">

                    <!-- Title with icon -->
                    <div class="d-flex align-items-center">
                        <h3 class="card-title mb-0 fw-bold">User List</h3>
                    </div>

                    <!-- Disburse button -->

                    <button type="submit" class="btn bg-blue-600 text-white font-bold btn-md shadow-sm ml-auto"
                        id="bulkDisburseBtn" disabled>
                        <i class="fas fa-hand-holding-usd mr-1"></i> Disburse </button>

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
                                    <th>Affiliate ID</th>
                                    <th>Account Type</th>
                                    <th>Account Number</th>
                                    <th class="text-center flex flex-col justify-center items-center">
                                        <div class="flex justify-center">Incomes</div>
                                        <div class="flex gap-1 justify-center">
                                            <span class="badge bg-blue-600 text-white">Leads</span>
                                            <span class="badge bg-gray-500 text-white">Affiliate</span>
                                            <span class="badge bg-green-600 text-white">Subscription</span>
                                            <span class="badge bg-warning text-dark">Associate</span>
                                        </div>
                                    </th>
                                    <th>Total Income</th>
                                    <th>Total Paid</th>
                                    <th>Rest</th>
                                    <th>Status</th>
                                    <th>
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- {{dd($date)}} --}}
                                @forelse ($users as $user)
                                    @php
                                        $incomes = $user->getIncomeByDate($date);
                                        $totalIncome = $user->total_amount_in;
                                        $paidAmount = $user->total_paid;
                                        $restAmount = $totalIncome - $paidAmount;
                                    @endphp

                                    <tr>

                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->kyc->affiliate_id ?? '-' }}</td>
                                        <td>{{ App\Models\Kyc::PAYMENT_METHODS[$user->kyc->account_type] ?? '-' }}</td>
                                        <td>{{ $user->kyc->account_number }}</td>
                                        <td class="text-center">
                                            <div class="flex flex-row gap-1 items-center justify-center h-full">
                                                <span class="badge bg-blue-600 text-white">{{ $incomes['Leads'] }}</span>
                                                <span
                                                    class="badge bg-gray-500 text-white">{{ $incomes['Affiliate'] }}</span>
                                                <span
                                                    class="badge bg-green-600 text-white">{{ $incomes['Subscription'] }}</span>
                                                @if ($user->is_super_prime)
                                                    <span
                                                        class="badge bg-warning text-dark">{{ $incomes['Associate'] }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>{{ number_format($totalIncome, 0) }}</td>
                                        <td>{{ number_format($paidAmount, 0) }}</td>
                                        <td>{{ number_format($restAmount, 0) }}</td>
                                        <td>
                                            @php
                                                $statusMap = [
                                                    0 => ['label' => 'Unpaid', 'class' => 'badge-danger'],
                                                    1 => ['label' => 'Paid', 'class' => 'badge-success'],
                                                    2 => ['label' => 'Partially Paid', 'class' => 'badge-warning'],
                                                ];

                                                $status = $statusMap[$user->is_paid] ?? [
                                                    'label' => 'Unknown',
                                                    'class' => 'badge-secondary',
                                                ];
                                            @endphp

                                            <span class="badge {{ $status['class'] }}">
                                                {{ $status['label'] }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($user->is_paid == 0 || $user->is_paid == 2)
                                                <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" data-total="{{ $restAmount }}" class="user-checkbox">
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">No users with stock found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if ($users instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="mt-3 mx-3">
                            {{ $users->links('vendor.pagination.tailwind') }}
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            const $selectAll = $('#selectAll');
            const $checkboxes = $('.user-checkbox');
            const $bulkBtn = $('#bulkDisburseBtn');

            // Select/Deselect all
            $selectAll.on('change', function() {
                $checkboxes.prop('checked', this.checked);
                toggleBulkButton();
            });

            // Individual checkbox change
            $checkboxes.on('change', function() {
                toggleBulkButton();
            });

            function toggleBulkButton() {
                const anyChecked = $('.user-checkbox:checked').length > 0;
                $bulkBtn.prop('disabled', !anyChecked);
            }

            // Handle AJAX form submit with SweetAlert confirm
            $('#bulkForm').on('submit', function(e) {
                e.preventDefault();

                let form = this;
                let formData = $(form).serialize();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "This will disburse the payment for the selected users.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, disburse'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: $(form).attr('action'),
                            method: 'POST',
                            data: formData,
                            success: function(response) {
                                Swal.fire(
                                    'Success!',
                                    response.message ||
                                    'Disbursement completed successfully!',
                                    'success'
                                );

                                // Update badge and fade out checkbox
                                $('.user-checkbox:checked').each(function() {
                                    let $row = $(this).closest('tr');

                                    // Update Paid badge
                                    $row.find('td:nth-child(10)').html(
                                        `<span class="badge badge-success">Paid</span>`
                                    );

                                    // Animate checkbox fade out
                                    $(this).fadeOut(500, function() {
                                        $(this)
                                            .remove(); // remove from DOM after fade
                                    });
                                });

                                // Reset select all checkbox
                                $selectAll.prop('checked', false);
                                toggleBulkButton();

                                // Disable the bulk disburse button
                                $('#bulkDisburseBtn').prop('disabled', true);
                            },
                            error: function(xhr) {
                                let error = xhr.responseJSON?.message ||
                                    'Something went wrong!';
                                Swal.fire('Error!', error, 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
