<!-- Sale Modal -->
<div class="modal fade" id="saleModal" tabindex="-1" aria-labelledby="saleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="saleForm" method="POST" action="{{ route('stock_sale') }}">
            @csrf
            <div class="modal-content rounded-xl shadow-lg">
                <div class="modal-header prime_table_bg text-white rounded-t-xl">
                    <h5 class="modal-title font-semibold text-lg" id="saleModalLabel">🛒 Make a Sale</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 space-y-4">
                    <input type="hidden" name="product_id" id="modal_product_id">
                    <input type="hidden" name="sale_log_id" id="modal_sale_log_id">
                    <input type="hidden" name="stock_product_quantity" id="modal_product_quantity">
                    <input type="hidden" name="from_user_id" id="from_user_id" value="{{ auth()->user()->id }}">

                    <div class="flex justify-start items-center gap-6">
                        <label for="product_name" class="form-label m-0 p-0 font-medium text-gray-700">Product
                            Name</label>
                        <span>:</span>
                        <span id="modal_product_name"></span>
                    </div>

                    <div class="flex justify-start items-center gap-6">
                        <label for="sale_log_name" class="form-label m-0 p-0 font-medium text-gray-700">Sale Log
                            Name</label>
                        <span>:</span>
                        <span id="modal_sale_log_name"></span>
                    </div>

                    <div>
                        <label for="user_id" class="form-label font-medium text-gray-700">Select User</label>
                        <select class="form-select w-full select2" name="user_id" id="modal_user_id" required>
                            <option value="">-- Choose User --</option>
                            @foreach ($users->where('is_active',1) as $user)
                                <option value="{{ $user->id }}">{{ $user?->kyc?->affiliate_id }} - {{ $user->name }} - {{ $user->email }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="quantity" class="form-label font-medium text-gray-700">Quantity</label>
                        <input type="number" class="form-control border-gray-300 rounded-md" name="quantity"
                            id="modal_quantity" required min="1">
                    </div>

                    <div>
                        <label for="quantity" class="form-label font-medium text-gray-700">Remarks</label>
                        <textarea class="form-control border-gray-300 rounded-md" name="remarks"></textarea>
                    </div>
                </div>

                <div class="modal-footer flex flex-col sm:flex-row justify-between gap-3 px-4 py-3">
                    <button type="submit" class="btn btn-primary rounded shadow w-full sm:w-auto flex-1 modal_sale_button">Sale</button>
                    <button type="button" class="btn btn-secondary rounded cancel w-full sm:w-auto flex-1"
                        data-bs-dismiss="modal">Cancel</button>
                </div>

            </div>
        </form>
    </div>
</div>
