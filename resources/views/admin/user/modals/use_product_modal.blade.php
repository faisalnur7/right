<!-- Use Product Modal -->
<div class="modal fade" id="useProductModal" tabindex="-1" aria-labelledby="useProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="useProductForm" method="POST" action="">
            @csrf
            <div class="modal-content rounded-xl shadow-lg">
                <div class="modal-header bg-dark text-white rounded-t-xl">
                    <h5 class="modal-title font-semibold text-lg" id="useProductModalLabel">🧪 Use Product</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body p-4 space-y-4">
                    <input type="hidden" name="product_id" id="use_modal_product_id">
                    <input type="hidden" name="sale_log_id" id="use_modal_sale_log_id">
                    <input type="hidden" name="stock_product_quantity" id="use_modal_product_quantity">
                    <input type="hidden" name="used_by" value="{{ auth()->user()->id }}">

                    <div class="grid grid-cols-1 gap-4">
                        <div class="flex justify-start items-center gap-4">
                            <label class="form-label m-0 p-0 font-medium text-gray-700 w-32">Product Name</label>
                            <span>:</span>
                            <span id="use_modal_product_name"></span>
                        </div>

                        <div class="flex justify-start items-center gap-4">
                            <label class="form-label m-0 p-0 font-medium text-gray-700 w-32">Sale Log Name</label>
                            <span>:</span>
                            <span id="use_modal_sale_log_name"></span>
                        </div>

                        <div class="flex justify-start items-center gap-4">
                            <label class="form-label m-0 p-0 font-medium text-gray-700 w-32">Unit</label>
                            <span>:</span>
                            <span id="use_modal_unit"></span>
                        </div>

                        <div class="flex justify-start items-center gap-4">
                            <label class="form-label m-0 p-0 font-medium text-gray-700 w-32">Unit Price</label>
                            <span>:</span>
                            <span id="use_modal_price"></span>
                        </div>
                    </div>



                    <div>
                        <label class="form-label font-medium text-gray-700">Quantity</label>
                        <input type="number" class="form-control border-gray-300 rounded-md" name="quantity"
                            id="use_modal_quantity" required min="1">
                    </div>

                    <div>
                        <label class="form-label font-medium text-gray-700">Remarks</label>
                        <textarea class="form-control border-gray-300 rounded-md" name="remarks"></textarea>
                    </div>
                </div>

                <div class="modal-footer flex flex-col sm:flex-row justify-end gap-3 px-4 py-3">
                    <button type="submit" class="btn btn-dark rounded shadow w-full sm:w-auto flex-1">Use</button>
                    <button type="button" class="btn btn-secondary rounded cancel-use w-full sm:w-auto flex-1"
                        data-bs-dismiss="modal">Cancel</button>
                </div>

            </div>
        </form>
    </div>
</div>
