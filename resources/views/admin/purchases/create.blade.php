@extends('layouts.admin_master')
@section('title', 'Add New Purchase')
@section('page_title', 'Add New Purchase')

@section('contents')
<div class="container-fluid">
    <div class="row">
        <!-- Left: Purchase Form -->
        <div class="col-md-9">
            <div class="card shadow-lg border-light mb-4">
                <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                    <h3 class="card-title">Purchase Information</h3>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="float-right-bottom alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('purchase.store') }}" method="POST" enctype="multipart/form-data" id="purchase-form">
                        @csrf

                        <div class="row">
                            <!-- Supplier -->
                            <div class="col-md-6 form-group">
                                <label>Supplier</label>
                                <select name="supplier_id" class="form-control select2" required>
                                    <option value="">Select Supplier</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->company_name }} - {{ $supplier->contact_person }} - {{ $supplier->mobile_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Category -->
                            <div class="col-md-6 form-group">
                                <label>Category</label>
                                <select name="category_id" class="form-control select2" id="category" required>
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Subcategory -->
                            <div class="col-md-6 form-group">
                                <label>Sub Category</label>
                                <select name="sub_category_id" class="form-control select2" id="sub_category">
                                    <option value="">Select Subcategory</option>
                                    @foreach ($subCategories as $subcategory)
                                        <option value="{{$subcategory->id}}">{{$subcategory->name}}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Brand -->
                            <div class="col-md-6 form-group">
                                <label>Brand</label>
                                <select name="brand_id" class="form-control select2" id="brand">
                                    <option value="">Select Brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{$brand->id}}">{{$brand->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Product Table -->
                            <div class="col-md-12 form-group">
                                <label>Products</label>
                                <table class="table table-bordered table-striped" id="product-table">
                                    <thead class="table-dark bg-gradient-dark">
                                        <tr>
                                            <th>Product Name</th>
                                            <th>Unit Price</th>
                                            <th>MRP</th>
                                            <th>Affiliate Price</th>
                                            <th>Quantity</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="product-body">
                                        <!-- Dynamic rows -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Subtotal / Discount / Grand Total -->
                        <div class="row align-items-center mb-3">
                            <div class="col-md-6 text-md-end">
                                <label class="mb-0">Subtotal</label>
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" name="sub_total" class="form-control" id="subtotal" readonly>
                            </div>

                            <div class="col-md-6 text-md-end">
                                <label class="mb-0">Discount</label>
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="number" name="discount_value" class="form-control" id="discount" value="0" step="0.01" required>
                            </div>

                            <div class="col-md-6 text-md-end">
                                <label class="mb-0">Delivery Charge</label>
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="number" name="delivery_charge" class="form-control" id="delivery-charge" value="0" step="0.01" required>
                            </div>

                            <div class="col-md-6 text-md-end">
                                <label class="mb-0">Grand Total</label>
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" name="total_amount" class="form-control" id="grand-total" readonly>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Invoice Upload -->
                        <div class="row">
                            <div class="col-md-6 text-md-end">
                                <label class="mb-0">Reference No (Optional)</label>
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" name="reference_no" class="form-control" id="reference_no" placeholder="Invoice Number">
                            </div>

                            <div class="col-md-6 text-md-end">
                                <label class="mb-0">Comments (Optional)</label>
                            </div>
                            <div class="col-md-6 mb-2">
                                <input type="text" name="comments" class="form-control" id="comments" placeholder="Any comments">
                            </div>

                            <div class="col-md-6 text-md-end">
                                <label>Invoice Upload (Optional)</label>
                            </div>

                            <div class="col-md-6 mb-2">
                                <input type="file" name="invoice" class="form-control-file">
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Buttons -->
                        <div class="row">
                            <div class="col-12 text-right">
                                <button type="submit" class="btn btn-success btn-lg shadow-sm">Save Purchase</button>
                                <a href="{{ route('purchase.list') }}" class="btn btn-secondary btn-lg shadow-sm">Cancel</a>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Right Panel for Product Cards -->
        <div class="col-md-3">
            <div class="card shadow-lg border-primary mb-4">
                <div class="card-header text-white rounded-top d-flex justify-content-between align-items-center shadow p-3"
                    style="background: linear-gradient(90deg, #343a40, #212529);">
                    <h4 class="card-title">Available Products</h4>
                    <div class="text-right ml-auto badge badge-pill badge-primary" id="product_count">0</div>
                </div>
                <div class="card-body p-0">
                    <div id="product-panel" style="max-height: 80vh; overflow-y: auto; padding: 15px;">
                        <p class="text-muted">Select Category, Subcategory & Brand to load products.</p>
                        <!-- Product cards will be appended here -->
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {

        $('.select2').select2({
            placeholder: "Please select"
        });

        function loadProducts() {
            let categoryId = $('#category').val();
            let subCategoryId = $('#sub_category').val();
            let brandId = $('#brand').val();

            if (!categoryId) {
                $('#product-panel').html('<p class="text-muted">Please select at least a Category to load products.</p>');
                return;
            }
            // Get the base URL dynamically from the address bar
            let baseUrl = window.location.origin;
            $.ajax({
                url: "{{route('getProducts')}}",
                method: 'GET',
                data: {
                    category_id: categoryId,
                    sub_category_id: subCategoryId,
                    brand_id: brandId
                },
                success: function(response) {
                    let panel = $('#product-panel');
                    panel.empty();
                    
                    if (response.products.length === 0) {
                        panel.append('<p class="text-danger">No products found for the selected filters.</p>');
                        $('#product_count').text(response.products.length);
                        return;
                    }

                    let row = $('<div class="row"></div>');

                    $('#product_count').text(response.products.length);

                    $.each(response.products, function(index, product) {
                        let imageUrl = baseUrl + '/' + (product.image || 'default-image.png'); // Fallback image if no image

                        let card = `
                            <div class="col-md-12 mb-1">
                                <div class="card h-90 shadow-xl mb-0">
                                    <div class="d-flex align-items-center p-2">
                                        <div class="flex-shrink-0">
                                            <img src="${imageUrl}" alt="${product.name}" 
                                                style="width: 100px; height: 100px; object-fit: contain;">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="mb-1 text-bold">${product.name}</h6>
                                            <p class="mb-2">Unit Price: ${product.purchase_price}</p>
                                            <button class="btn btn-sm btn-primary add-to-cart text-bold" 
                                                data-id="${product.id}" 
                                                data-name="${product.name}" 
                                                data-price="${product.purchase_price}"
                                                data-mrp="${product.price}"
                                                data-affiliate_price="${product.affiliate_price}">
                                                Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                        row.append(card);
                    });

                    panel.append(row);
                }
            });
        }

        // Triggers
        $('#category, #sub_category, #brand').change(function() {
            loadProducts();
        });

        // Calculate totals
        $('#purchase-form').on('change', '.unit-price, .quantity', function() {
            var row = $(this).closest('tr');
            var unitPrice = parseFloat(row.find('.unit-price').val()) || 0; // Ensure valid number
            var quantity = parseInt(row.find('.quantity').val()) || 0;
            var total = unitPrice * quantity;
            row.find('.total').val(total.toFixed(2));
            calculateSubtotal();
        });

        // Calculate subtotal and grand total
        function calculateSubtotal() {
            var subtotal = 0;
            $('#product-body .total').each(function() {
                subtotal += parseFloat($(this).val()) || 0; // Ensure valid number
            });
            $('#subtotal').val(subtotal.toFixed(2));
            calculateGrandTotal(subtotal);
        }

        // Calculate grand total with discount
        function calculateGrandTotal(subtotal) {
            var discount = parseFloat($('#discount').val()) || 0;
            var deliveryCharge = parseFloat($('#delivery-charge').val()) || 0;
            var grandTotal = (subtotal - discount) + deliveryCharge;

            $('#grand-total').val(grandTotal.toFixed(2));
        }

        $('#discount, #delivery-charge').on('input', function() {
            var subtotal = parseFloat($('#subtotal').val()) || 0;
            calculateGrandTotal(subtotal);
        });

        // Remove product row
        $('#product-table').on('click', '.remove-product', function() {
            var row = $(this).closest('tr');
            var productId = row.data('id');
            
            // Re-enable the "Add to Cart" button for the removed product
            $('#product-panel').find('.add-to-cart[data-id="' + productId + '"]').prop('disabled', false);

            row.remove();
            calculateSubtotal();
            toastr.success(`Product removed!`);
        });

        // Add product to table when "Add to Cart" is clicked
        // Add product to table when "Add to Cart" is clicked
        $('#product-panel').on('click', '.add-to-cart', function() {
            let button = $(this);
            let productId = button.data('id');
            let productName = button.data('name');
            let unitPrice = button.data('price');
            let mrp = button.data('mrp');
            let affiliate_price = button.data('affiliate_price');

            // Prevent adding the same product twice
            if ($('#product-body').find('tr[data-id="' + productId + '"]').length > 0) {
                alert('This product is already in the cart.');
                return;
            }

            let newRow = `
                <tr data-id="${productId}">
                    <td>
                        <input type="hidden" name="products[${productId}][id]" value="${productId}">
                        <input type="text" class="form-control-plaintext" readonly value="${productName}">
                    </td>
                    <td>
                        <input type="number" name="products[${productId}][unit_price]" class="form-control unit-price" value="${unitPrice}" min="0" step="0.01" required>
                    </td>
                    <td>
                        <input type="number" name="products[${productId}][mrp]" class="form-control mrp-price" value="${mrp}" min="0" step="0.01">
                    </td>
                    <td>
                        <input type="number" name="products[${productId}][affiliate_price]" class="form-control affiliate-price" value="${affiliate_price}" min="0" step="0.01">
                    </td>
                    <td>
                        <input type="number" name="products[${productId}][quantity]" class="form-control quantity" value="1" min="1" required>
                    </td>
                    <td>
                        <input type="text" name="products[${productId}][total]" class="form-control total" value="${unitPrice}" readonly>
                    </td>
                    <td>
                        <i class="fas fa-trash remove-product" style="display: flex; justify-content: center; align-items: center; cursor: pointer;"></i>
                    </td>
                </tr>
            `;

            $('#product-body').append(newRow);

            // Disable the button once the product is added
            button.prop('disabled', true);

            calculateSubtotal();

            // Show toastr success notification
            toastr.success(`${productName} added to cart!`);
        });

        // Calculate totals with MRP and Affiliate Price
        $('#purchase-form').on('change', '.unit-price, .mrp-price, .affiliate-price, .quantity', function() {
            var row = $(this).closest('tr');
            var unitPrice = parseFloat(row.find('.unit-price').val()) || 0;
            var quantity = parseInt(row.find('.quantity').val()) || 0;
            var mrpPrice = parseFloat(row.find('.mrp-price').val()) || 0;
            var affiliatePrice = parseFloat(row.find('.affiliate-price').val()) || 0;
            
            var total = unitPrice * quantity;
            row.find('.total').val(total.toFixed(2));

            // Update MRP and Affiliate Price totals if needed (if you want to calculate based on these values)
            // For now, these values won't affect the grand total unless you want them to.

            calculateSubtotal();
        });

    });
</script>


@endsection
