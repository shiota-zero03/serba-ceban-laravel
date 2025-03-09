<link rel="stylesheet" href="{{ asset('/') }}assets/vendor/fonts/boxicons.css" />
<link rel="stylesheet" href="{{ asset('/') }}assets/vendor/css/core.css" class="template-customizer-core-css" />
<link rel="stylesheet" href="{{ asset('/') }}assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
<link rel="stylesheet" href="{{ asset('/') }}assets/css/demo.css" />
<link rel="stylesheet" href="{{ asset('/') }}assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
<link rel="stylesheet" href="{{ asset('/') }}assets/vendor/libs/apex-charts/apex-charts.css" />
<link rel="stylesheet" href="{{ asset('/') }}assets/vendor/libs/sweetalert2/dist/sweetalert2.min.css" />
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="{{ asset('/') }}assets/vendor/js/helpers.js"></script>
<script src="{{ asset('/') }}assets/js/config.js"></script>

<style>
    .select2-container--default .select2-selection--single {
        background-color: transparent;
        color: #5A6A85;
        border: 1px solid #5A6A8550;
        font-weight: 400;
        font-size: .8rem;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #5A6A85;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        color: #5A6A85;
    }
    .datatables .table-responsive {
        overflow-x: scroll;
        overflow-y: hidden;
        padding-bottom: 2rem;
        padding-top: 1rem;
    }
    .datatables .table-responsive::-webkit-scrollbar{
        width: 5px;
    }
    @media screen and (max-width: 767px){
        .dataTables_info{
            margin-bottom: 1.2rem;
        }
        .image-view-master{
            width: 100%;
            margin-bottom: -16px
        }
    }
    @media screen and (min-width: 768px){
        .border-for-lpk{
            border-right: 1px solid #2B2B2B66
        }
        .image-view-master{
            width: 60%;
            margin-bottom: -16px
        }
        table.table{
            width: 100%;
        }
        div.dataTables_wrapper div.dataTables_filter{
            margin-top: -3rem;
        }
        div.dataTables_wrapper div.dataTables_paginate {
            margin-top: -1rem;
        }
    }
</style>
