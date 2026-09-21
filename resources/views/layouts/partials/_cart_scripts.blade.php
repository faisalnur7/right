<script>
    $(document).on('click', '.atc_btn', function(e) {
        e.preventDefault();
        let product_id = $(this).data('product_id');
        let sale_log_id = $(this).data('sale_log_id');
        let price = $(this).data('price');

        $.ajax({
            url: "{{ route('add_to_cart') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                product_id: product_id,
                sale_log_id: sale_log_id,
                price: price
            },
            success: function(response) {
                if (response.success) {
                    console.log(response.message);
                    $('#shopping_cart').html(response.html)
                    toastr.success(`Added to cart!`);
                } else {
                    alert("Something went wrong.");
                }
            },
            error: function(xhr) {
                alert("AJAX error: " + xhr.responseText);
            }
        });
    });

    $(document).on('click', '.remove-cart-item', function(e) {
        e.preventDefault();
        let item_id = $(this).data('id');

        $.ajax({
            url: "{{route('remove_item')}}",
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                item_id : item_id
            },
            success: function(res) {
                if (res.success) {
                    $('#shopping_cart').html(res.view);
                    $('#cart_page_items').html(res.cart_view);
                    updateSubtotal();
                }
            },
            error: function(xhr) {
                alert('Failed to remove item.');
            }
        });
    });

    function updateSubtotal() {
        let count_items = $('#item_count').val();
        let subtotal = 0;

        if(count_items > 0){
            $('.quantity-input').each(function() {
                const qty = parseInt($(this).val());
                const price = parseFloat($(this).data('price'));
                subtotal += qty * price;
            });
            $('#subtotal').text(subtotal.toFixed(2));

            $.ajax({
                url: "{{ route('get_shipping_cost')}}",
                method: 'POST',
                data: {
                    subtotal: subtotal,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    const shipping = parseFloat(response.shipping);
                    const total = subtotal + shipping;

                    $('#shipping').text(shipping.toFixed(2));
                    $('#total').text(total.toFixed(2));
                }
            });
        }else{
            $('#shipping').text(0);
            $('#total').text(0);
            $('#subtotal').text(0);
        }
    }
</script>
