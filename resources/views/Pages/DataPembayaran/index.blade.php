@extends('Components.Layout')
@section('content')

<div class="row gap-4">
    <div class="col-12">
        <div class="d-md-flex align-items-center justify-content-between">
            <div>
                <h4 class="m-md-0 text-md-start text-center">Manajemen Pembayaran dari Admin</h4>
                <span><em>Data diambil dari pembayaran yang dilakukan oleh admin</em></span>
            </div>
        </div>
    </div>
    <div class="col-12 position-relative">
        <div class="card">
            <div class="card-body">
                <div class="mb-4">
                    <label for="filter">Filter Tanggal Pembayaran</label>
                    <input type="date" id="filter-tanggal" class="form-control" value="{{ now()->timezone('Asia/Jakarta')->toDateString() }}">
                </div>
                <div class="table-responsive">
                    <table id="data-table" class="table border table-striped table-bordered text-nowrap dataTable text-start span" aria-describedby="file_export_info">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Pembayaran</th>
                                <th>Nama Mitra</th>
                                <th>Tanggal Bayar</th>
                                <th>Total Transfer</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $('#data-pembayaran').addClass('active')

        var table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('data-pembayaran.index') }}",
                data: function(d) {
                    d.tanggal = $('#filter-tanggal').val(); // Kirim filter tanggal ke server
                }
            },
            lengthChange: false,
            columns: [
                { data: 'DT_RowIndex', searchable: false },
                { data: 'kode_pembayaran' },
                { data: 'mitra.name' },
                { data: 'tanggal_bayar' },
                { data: 'transfer' },
                { data: 'status' },
                { data: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                { targets: 0, width: "20px" }, // Kolom pertama hanya 20px
                { targets: "_all", className: "text-start small" } // Semua teks lebih kecil & rata kanan
            ]
        });

        $('#filter-tanggal').on('change', function() {
            table.ajax.reload(); // Reload DataTable dengan filter baru
        });

    </script>
@endpush
@endsection
