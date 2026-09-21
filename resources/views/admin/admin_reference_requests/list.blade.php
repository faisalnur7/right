@extends('layouts.admin_master')

@section('title', 'Admin Reference Requests')
@section('page_title', 'Admin Reference Requests')

@section('contents')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                <h3 class="card-title mb-0">Admin Reference Requests</h3>
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
                                <th>User</th>
                                <th>Previous Referrer</th>
                                <th>Status</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($adminReferenceRequest as $request)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $request->user?->name ?? 'N/A' }}</td>
                                    <td>{{ $request->previousReferenceUser?->name ?? 'N/A' }}</td>
                                    <td>
                                        @if ($request->status == '1')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif ($request->status == '2')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif ($request->status == '0')
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($request->status == '1')
                                            <button class="btn btn-sm btn-outline-success approveBtn"
                                                data-id="{{ $request->id }}">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                        @endif
                                        <form action="{{ route('admin_reference_request.delete', $request->id) }}"
                                            method="POST" style="display:inline;">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Are you sure you want to delete this request?')"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
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
                            <select name="prime_user_id" id="prime_user_id" class="form-control" required>
                                <option value="">Select Prime User</option>
                                @foreach ($primeUsers as $prime)
                                    <option value="{{ $prime->id }}">{{ $prime->name }}</option>
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
            $('#request_id').val(requestId);
            $('#approveForm').attr('action', "{{ route('admin_reference_request.approve', '') }}/" + requestId);
            $('#approveModal').modal('show');
        });
    </script>
@endsection