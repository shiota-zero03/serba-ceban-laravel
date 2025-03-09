<script src="{{ asset('/') }}assets/vendor/libs/jquery/jquery.js"></script>
<script src="{{ asset('/') }}assets/vendor/libs/popper/popper.js"></script>
<script src="{{ asset('/') }}assets/vendor/js/bootstrap.js"></script>
<script src="{{ asset('/') }}assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="{{ asset('/') }}assets/vendor/js/menu.js"></script>
<script src="{{ asset('/') }}assets/vendor/libs/apex-charts/apexcharts.js"></script>
<script src="{{ asset('/') }}assets/vendor/libs/sweetalert2/dist/sweetalert2.min.js"></script>
<script src="{{ asset('/') }}assets/js/main.js"></script>
<script src="{{ asset('/') }}assets/js/dashboards-analytics.js"></script>
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script async defer src="https://buttons.github.io/buttons.js"></script>

@if (Session::has('errorData'))
    <script>
        window.onload = function() {
            Swal.fire({
                text: "{{ Session::get('errorData') }}",
                icon: "error"
            });
        };
    </script>
@endif

@if (Session::has('success'))
    <script>
        window.onload = function() {
            Swal.fire({
                text: "{{ Session::get('success') }}",
                icon: "success"
            });
        };
    </script>
@endif
