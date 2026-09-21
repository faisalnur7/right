<script>
    $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="{{ asset('assets/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- ChartJS -->
<script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
<!-- Sparkline -->
<script src="{{ asset('assets/plugins/sparklines/sparkline.js') }}"></script>
<!-- JQVMap -->
<script src="{{ asset('assets/plugins/jqvmap/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('assets/plugins/jqvmap/maps/jquery.vmap.usa.js') }}"></script>
<!-- jQuery Knob Chart -->
<script src="{{ asset('assets/plugins/jquery-knob/jquery.knob.min.js') }}"></script>
<!-- daterangepicker -->
<script src="{{ asset('assets/plugins/moment/moment.min.js') }}"></script>
<script src="{{ asset('assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ asset('assets/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Summernote -->
<script src="{{ asset('assets/plugins/summernote/summernote-bs4.min.js') }}"></script>

<!-- Toastr -->
<script src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>

<!-- overlayScrollbars -->
<script src="{{ asset('assets/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('assets/dist/js/adminlte.js') }}"></script>
<!-- AdminLTE for demo purposes -->

<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-bottom-right",
        "preventDuplicates": true
    };

    $(document).on('click', '.btn-close', function() {
        $('.modal').modal('hide');
    })

    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "Please select"
        });

        $('#division_id').on('change', function() {
            const divisionId = $(this).val();
            const url = '{{ route('getDistricts', ':id') }}'.replace(':id', divisionId);

            // Clear and show loading
            $('#district_id').html('<option value="">Loading...</option>');

            if (divisionId) {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(data) {
                        let options = '<option value="">All Districts</option>';
                        data.forEach(function(district) {
                            options +=
                                `<option value="${district.id}">${district.name}</option>`;
                        });
                        $('#district_id').html(options).trigger('change');
                    },
                    error: function() {
                        $('#district_id').html('<option value="">Failed to load</option>');
                    }
                });
            } else {
                $('#district_id').html('<option value="">All Districts</option>');
            }
        });

        $('#district_id').on('change', function() {
            const districtId = $(this).val();
            const url = '{{ route('getStations', '___id___') }}'.replace('___id___', districtId);

            $('#police_station_id').html('<option value="">Loading...</option>');

            if (districtId) {
                $.get(url, function(data) {
                    let options = '<option value="">All Police Stations</option>';
                    data.forEach(function(station) {
                        options +=
                            `<option value="${station.id}">${station.name}</option>`;
                    });
                    $('#police_station_id').html(options);
                });
            } else {
                $('#police_station_id').html('<option value="">All Police Stations</option>');
            }
        });
    });
</script>
