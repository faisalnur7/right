<script>
    $(function() {

        $('.select2').select2({
            width: '100%'
        });

        $('#is_cod').on('change', function() {
            if ($(this).is(':checked')) {
                // Hide payment section and uncheck any selected payment method
                $('#payment-section').addClass('hidden');
                $('input[name="payment_method"]').prop('checked', false);
                $('#payment-details').addClass('hidden');
            } else {
                // Show payment methods again
                $('#payment-section').removeClass('hidden');
                $('#payment-details').removeClass('hidden');

            }
        });

        // Initial highlight for pre-checked input
        $('#payment-options input[type=radio]:checked').closest('.payment-option')
            .addClass('ring-2 ring-indigo-500')
            .find('.check-icon').removeClass('hidden');

        $('#payment-options input[type=radio]').on('change', function() {
            // Remove highlight from all
            $('.payment-option').removeClass('ring-2 ring-indigo-500');
            $('.check-icon').addClass('hidden');

            // Highlight the selected one
            $(this).closest('.payment-option')
                .addClass('ring-2 ring-indigo-500')
                .find('.check-icon').removeClass('hidden');
        });

        // Highlight selected payment option on page load
        $('#payment-options input[type=radio]:checked').each(function() {
            $(this).closest('.payment-option').addClass('ring-2 ring-indigo-500').find('.check-icon')
                .removeClass('hidden');
            const account = $(this).closest('label').data('account');
            if (account) {
                $('#account-number').text(account);
                $('#payment-details').removeClass('hidden');
            }
        });

        $('#payment-options input[type=radio]').on('change', function() {
            // Remove highlight from all and hide check icons
            $('.payment-option').removeClass('ring-2 ring-indigo-500');
            $('.check-icon').addClass('hidden');

            // Highlight selected option
            $(this).closest('.payment-option').addClass('ring-2 ring-indigo-500').find('.check-icon')
                .removeClass('hidden');

            // Show account number and inputs
            const account = $(this).closest('label').data('account');
            if (account) {
                $('#account-number').text(account);
                $('#payment-details').removeClass('hidden');
            } else {
                $('#payment-details').addClass('hidden');
                $('#account-number').text('');
            }
        });

        // On page load if none selected, hide details
        if (!$('#payment-options input[type=radio]:checked').length) {
            $('#payment-details').addClass('hidden');
        }

        $(document).on('click', '.payment-option-zoom', function(event) {
            event.preventDefault();
            event.stopPropagation();

            const button = $(this);
            const name = button.data('name') || 'Payment option';

            $('#paymentOptionZoomImage')
                .attr('src', button.data('logo'))
                .attr('alt', name + ' logo');
            $('#paymentOptionZoomTitle').text(name + ' logo');
            $('#paymentOptionZoomOverlay').removeClass('hidden');
        });

        $('#paymentOptionZoomClose, #paymentOptionZoomOverlay').on('click', function(event) {
            if (event.target === this) {
                $('#paymentOptionZoomOverlay').addClass('hidden');
            }
        });


        function updateSelectedCardStyle() {
            $('.address-card').removeClass('selected-card');
            $('.address-radio:checked').closest('.address-card').addClass('selected-card');
        }

        // Initial call on page load
        updateSelectedCardStyle();

        // Billing Address Selection
        $(document).on('click', '.address-card-wrapper', function() {
            const radio = $(this).find('.address-radio');
            radio.prop('checked', true).trigger('change');

            $('.address-card-wrapper').removeClass('address_selected');
            $(this).addClass('address_selected');

            updateSelectedCardStyle();
        });

        // Shipping (Delivery) Address Selection
        $(document).on('click', '.delivery-address-card-wrapper', function() {
            const radio = $(this).find('.delivery-address-radio');
            radio.prop('checked', true).trigger('change');

            $('.delivery-address-card-wrapper').removeClass('address_selected');
            $(this).addClass('address_selected');

            updateSelectedCardStyle();
        });



        // Optional: delete handler
        $('.delete-address-btn').on('click', function(e) {
            e.stopPropagation(); // prevent triggering the card selection
            const id = $(this).data('id');
            if (confirm('Are you sure you want to delete this address?')) {
                $.ajax({
                    url: "{{ route('address-book.destroy') }}",
                    type: "POST",
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        toastr.success('Successfully deleted');
                        window.location.reload();
                    },
                });
            }
        });

        $('#toggle-shipping').on('change', function() {
            if ($(this).is(':checked')) {
                $('#shipping-address').removeClass('hidden');
                $('#saved-shipping-addresses-section').removeClass('hidden');
                $('.delivery_title').removeClass('hidden');
                $('#shipping-address').slideDown();
            } else {
                $('#shipping-address').slideUp();
                $('#shipping-address').addClass('hidden');
                $('.delivery_title').addClass('hidden');
                $('#saved-shipping-addresses-section').addClass('hidden');

            }
        });

        // Fetch police stations when district is selected
        $("#district_id").change(function() {
            let districtId = $(this).val();
            let policeDropdown = $('#police_station_id');

            policeDropdown.html('<option value="">Loading...</option>');

            if (districtId) {
                $.ajax({
                    url: "{{ route('load_police_stations') }}",
                    type: "GET",
                    data: {
                        district_id: districtId
                    },
                    success: function(data) {
                        policeDropdown.html(
                            '<option value="">Select Police Station</option>');
                        data.police_stations.forEach(station => {
                            policeDropdown.append(
                                `<option value="${station.id}">${station.name}</option>`
                            );
                        });
                    },
                });
            }
        });

        // Fetch post offices when police station is selected
        $("#police_station_id").change(function() {
            let police_station_id = $(this).val();
            let postDropdown = $("#post_office_id");

            postDropdown.html('<option value="">Loading...</option>');

            if (police_station_id) {
                $.ajax({
                    url: "{{ route('load_post_offices') }}",
                    type: "GET",
                    data: {
                        police_station_id: police_station_id
                    },
                    success: function(data) {
                        postDropdown.html('<option value="">Select Post Office</option>');
                        data.post_offices.forEach(post => {
                            postDropdown.append(
                                `<option value="${post.id}" data-postal_code="${post.postcode}">${post.name}</option>`
                            );
                        });
                    },
                });
            }
        });

        $("#post_office_id").change(function() {
            let postalCode = $(this).find(":selected").data("postal_code");
            console.log(postalCode); // Correctly logs the value
            $("#postal_code").val(postalCode); // Optional: auto-fill input
        });

        // Fetch delivery police stations when delivery district is selected
        $("#delivery_district_id").change(function() {
            let districtId = $(this).val();
            let policeDropdown = $('#delivery_police_station_id');
            let postDropdown = $('#delivery_post_office_id');

            policeDropdown.html('<option value="">Loading...</option>');
            postDropdown.html('<option value="">Select Post Office</option>');

            if (districtId) {
                $.ajax({
                    url: "{{ route('load_police_stations') }}",
                    type: "GET",
                    data: {
                        district_id: districtId
                    },
                    success: function(data) {
                        policeDropdown.html(
                            '<option value="">Select Police Station</option>');
                        data.police_stations.forEach(station => {
                            policeDropdown.append(
                                `<option value="${station.id}">${station.name}</option>`
                            );
                        });
                    },
                });
            }
        });

        // Fetch delivery post offices when delivery police station is selected
        $("#delivery_police_station_id").change(function() {
            let police_station_id = $(this).val();
            let postDropdown = $("#delivery_post_office_id");

            postDropdown.html('<option value="">Loading...</option>');

            if (police_station_id) {
                $.ajax({
                    url: "{{ route('load_post_offices') }}",
                    type: "GET",
                    data: {
                        police_station_id: police_station_id
                    },
                    success: function(data) {
                        postDropdown.html('<option value="">Select Post Office</option>');
                        data.post_offices.forEach(post => {
                            postDropdown.append(
                                `<option value="${post.id}" data-postal_code="${post.postcode}">${post.name}</option>`
                            );
                        });
                    },
                });
            }
        });

        // Optional: Auto-fill postal code field if available
        $("#delivery_post_office_id").change(function() {
            let postalCode = $(this).find(":selected").data("postal_code");
            console.log(postalCode);
            $("#delivery_postal_code").val(postalCode); // Optional input field
        });

        $('#save-address-btn').click(function() {
            let formData = {
                name: $('input[name="name"]').val(),
                email: $('input[name="email"]').val(),
                phone: $('input[name="phone"]').val(),
                district_id: $('#district_id').val(),
                police_station_id: $('#police_station_id').val(),
                post_office_id: $('#post_office_id').val(),
                address: $('input[name="address"]').val(),
                city: $('input[name="city"]').val(),
                zip: $('input[name="zip"]').val(),
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: '{{ route('prime_user.address.store') }}', // Replace with your route
                method: 'POST',
                data: formData,
                success: function(response) {
                    window.location.reload();
                },
                error: function(xhr) {
                    alert('Failed to save address. Please check your input.');
                }
            });
        });

        $('#add-new-address-btn').click(function() {
            $('#address-form-section').removeClass('hidden');
            $('html, body').animate({
                scrollTop: $('#address-form-section').offset().top
            }, 500);
        });

        $('#place_order').on('click', function() {
            let selected_address_id = $('input[name="selected_address_id"]:checked').val();
            let selected_shipping_address_id = $('input[name="selected_shipping_address_id"]:checked')
                .val();

            let billing = null;
            if (!selected_address_id) {
                billing = {
                    name: $('input[name="billing_address[name]"]').val(),
                    email: $('input[name="billing_address[email]"]').val(),
                    phone: $('input[name="billing_address[phone]"]').val(),
                    district_id: $('select[name="billing_address[district_id]"]').val(),
                    police_station_id: $('select[name="billing_address[police_station_id]"]').val(),
                    post_office_id: $('select[name="billing_address[post_office_id]"]').val(),
                    address: $('input[name="billing_address[address]"]').val(),
                    city: $('input[name="billing_address[city]"]').val(),
                    zip: $('input[name="billing_address[zip]"]').val(),
                    type: 1,
                };
            }

            const isShippingDifferent = $('#shipping-address').is(':visible');
            let shipping = null;
            if (isShippingDifferent && !selected_shipping_address_id) {
                shipping = {
                    name: $('input[name="shipping_address[name]"]').val(),
                    phone: $('input[name="shipping_address[phone]"]').val(),
                    district_id: $('select[name="shipping_address[district_id]"]').val(),
                    police_station_id: $('select[name="shipping_address[police_station_id]"]').val(),
                    post_office_id: $('select[name="shipping_address[post_office_id]"]').val(),
                    address: $('input[name="shipping_address[address]"]').val(),
                    city: $('input[name="shipping_address[city]"]').val(),
                    zip: $('input[name="shipping_address[zip]"]').val(),
                    type: 2,
                };
            }

            const is_cod = $('#is_cod').is(':checked');

            let payment_method_id = null;
            let payment_account_number = null;
            let transaction_id = null;
            let sender_phone_number = null;

            if (!is_cod) {
                const paymentMethod = $('input[name="payment_method"]:checked');
                payment_method_id = paymentMethod.val();
                payment_account_number = paymentMethod.closest('label').data('account');
                transaction_id = $('#transaction_id').val();
                sender_phone_number = $('#sender_phone_number').val();

                // Validation only if NOT COD
                if (!payment_method_id) {
                    toastr.error('Please select a payment method.');
                    return;
                }

                if (!transaction_id) {
                    toastr.error('Please enter Transaction ID.');
                    return;
                }

                if (!sender_phone_number) {
                    toastr.error('Please enter Transaction phone number / sender phone number.');
                    return;
                }
            }

            const subtotal = parseFloat($('#subtotal').text());
            const shipping_charge = parseFloat($('#shipping').text());

            // Send AJAX
            $.ajax({
                url: "{{ route('prime_checkout.place_order') }}",
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    billing_address: billing,
                    selected_address_id: selected_address_id,
                    shipping_address: shipping,
                    selected_shipping_address_id: selected_shipping_address_id,
                    has_shipping: isShippingDifferent ? 1 : 0,
                    save_addresses: 1,

                    is_cod: is_cod ? 1 : 0,
                    payment_method_id: payment_method_id,
                    payment_account_number: payment_account_number,
                    transaction_id: transaction_id,
                    sender_phone_number: sender_phone_number,
                    subtotal: subtotal,
                    shipping_charge: shipping_charge,
                },
                beforeSend: function() {
                    $('#place_order').prop('disabled', true).text('Please wait...');
                },
                success: function(res) {
                    if (res.status == 404) {
                        window.location.href = "{{ route('products') }}";
                    }

                    if (res.order_id) {
                        window.location.href =
                            "{{ route('order_success', ['order_id' => '__ORDER_ID__']) }}"
                            .replace('__ORDER_ID__', res.order_id);
                    }
                },
                error: function(xhr) {
                    $('#place_order').prop('disabled', false).text('Place Order');
                    let message = 'Something went wrong.';
                    if (xhr.responseJSON?.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    alert(message);
                }
            });
        });

    });
</script>
