@extends('layouts.admin_master')

@section('title', 'Placement Requests')
@section('page_title', 'Placement Requests')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title mb-0">Placement Requests</h3>
            </div>

            <div class="card-body px-0 pb-4 pt-0">
                @if (session('success'))
                    <div class="float-right-bottom alert-success">{{ session('success') }}</div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Present Referrer</th>
                                <th>New Referrer</th>
                                <th>Product</th>
                                <th>Sale Log</th>
                                <th>Status</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($adminReferenceRequest as $request)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $request->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $request->presentReference?->name ?? 'N/A' }}</td>
                                    <td>{{ $request->newReference?->name ?? '-' }}</td>
                                    <td>{{ $request->product?->name ?? '-' }}</td>
                                    <td>{{ $request->saleLog?->name ?? '-' }}</td>
                                    <td>
                                        @if ($request->status == App\Models\UserActiveRequest::PENDING)
                                            <span
                                                class="badge bg-warning text-dark">{{ App\Models\UserActiveRequest::USER_ACTIVE_STATUS[$request->status] }}</span>
                                        @elseif ($request->status == App\Models\UserActiveRequest::APPROVED)
                                            <span
                                                class="badge bg-success">{{ App\Models\UserActiveRequest::USER_ACTIVE_STATUS[$request->status] }}</span>
                                        @elseif ($request->status == App\Models\UserActiveRequest::REJECTED)
                                            <span
                                                class="badge bg-danger">{{ App\Models\UserActiveRequest::USER_ACTIVE_STATUS[$request->status] }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($request->status == '1')
                                            <button class="btn btn-sm btn-outline-success approveBtn"
                                                data-id="{{ $request->id }}"
                                                data-sale_log_id="{{ $request->saleLog->id }}">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <form action="{{ route('admin_reference_request.delete', $request->id) }}"
                                                method="POST" style="display:inline;">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('Are you sure you want to delete this request?')"
                                                    title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No reference requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="approveForm" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title" id="approveModalLabel">Approve Reference Request</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="request_id" id="request_id">

                        <div class="form-group mb-3">
                            <label for="prime_user_id">Select Prime User</label>
                            <select name="new_reference_id" id="prime_user_id" class="form-control" required>
                                <option value="">Select Prime User</option>
                                @foreach ($primeUsers as $prime)
                                    <option value="{{ $prime->user_id }}" data-sale_log_id="{{ $prime->sale_log_id }}">
                                        {{ $prime->user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Approve</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).on('click', '.approveBtn', function() {
            let requestId = $(this).data('id');
            let saleLogId = $(this).data('sale_log_id');

            // Set form action dynamically
            $('#request_id').val(requestId);
            $('#approveForm').attr('action', "{{ route('prime_active_request.approve', '') }}/" + requestId);

            // Filter options based on saleLogId
            $('#prime_user_id option').each(function() {
                let optionSaleLogId = $(this).data('sale_log_id');

                // Always show the default option
                if (!optionSaleLogId) {
                    $(this).show();
                } else if (optionSaleLogId == saleLogId) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            // Reset selection
            $('#prime_user_id').val('');

            // Show modal
            $('#approveModal').modal('show');
        });
    </script>
@endsection
