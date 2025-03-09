@extends('Components.Layout')
@section('content')

<div class="row gap-4">
    <div class="col-12">
        <div class="d-md-flex align-items-center justify-content-between">
            <div>
                <h4 class="m-md-0 text-md-start text-center">Manajemen Pemesanan Barang</h4>
                <span><em>Digunakan untuk melakukan broadcasting kepada pihak mitra</em></span>
            </div>
            <a href="{{ route('pemesanan.create') }}" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                <i class="bx bx-plus"></i>
                Tambah Data
            </a>
        </div>
    </div>
    <div class="col-12 position-relative">
        <div class="card">
            <div class="card-body">
                <div class="mb-4">
                    <label for="filter">Filter Tanggal Pemesanan</label>
                    <input type="date" id="filter-tanggal" class="form-control" value="{{ now()->timezone('Asia/Jakarta')->toDateString() }}">
                </div>
                <div class="table-responsive">
                    <table id="data-table" class="table border table-striped table-bordered text-nowrap dataTable text-start span" aria-describedby="file_export_info">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Pemesanan</th>
                                <th>Nama Mitra</th>
                                <th>Banyak Produk</th>
                                <th>Tanggal Pemesanan</th>
                                <th>Broadcast</th>
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
        $('#pemesanan').addClass('active')

        var table = $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('pemesanan.index') }}",
                data: function(d) {
                    d.tanggal = $('#filter-tanggal').val(); // Kirim filter tanggal ke server
                }
            },
            lengthChange: false,
            columns: [
                { data: 'DT_RowIndex', searchable: false },
                { data: 'kode_pemesanan' },
                { data: 'nama_mitra' },
                { data: 'banyak_produk' },
                { data: 'tanggal_pemesanan' },
                { data: 'broadcast' },
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

        function deleteData(id)
        {
            Swal.fire({
                title: "Apakah anda yakin ?",
                text: "Anda akan menghapus data ini!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus data!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#loading-set').removeClass('d-none');
                    $('#loading-set').addClass('d-flex');

                    $.ajax({
                        url: `{{ route('pemesanan.index') }}/${id}`,
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response){
                            table.ajax.reload()
                            Swal.fire({
                                text: response.message,
                                icon: "success"
                            });
                        },
                        error: function(error){
                            Swal.fire({
                                text: error.responseJSON.message,
                                icon: "error"
                            });
                        },
                        complete: function(){
                            $('#loading-set').addClass('d-none');
                            $('#loading-set').removeClass('d-flex');
                        }
                    })
                }
            });
        }
    </script>
@endpush
@endsection
