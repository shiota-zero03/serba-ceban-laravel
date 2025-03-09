@extends('Components.Layout')
@section('content')

<div class="row gap-4">
    <div class="col-12">
        <div class="d-md-flex align-items-center justify-content-between">
            <h4 class="m-md-0 text-md-start text-center">Manajemen Produk</h4>
            @if(auth()->user()->role == 'MITRA')
                <button onclick="handleCreate()" class="btn btn-primary d-flex align-items-center justify-content-center gap-2">
                    <i class="bx bx-plus"></i>
                    Tambah Data
                </button>
            @endif
        </div>
    </div>
    <div class="col-12 position-relative">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="data-table" class="table border table-striped table-bordered text-nowrap dataTable text-start span" aria-describedby="file_export_info">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Produk</th>
                                <th>Nama Produk</th>
                                <th>Nama Mitra</th>
                                <th>Harga</th>
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

<div class="modal fade" id="createProduct" tabindex="-1" aria-labelledby="createProductLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="createProductLabel">Tambah Produk</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="product-form">
                    <div class="form-group mb-2">
                        <label for="code-create">Kode Produk</label>
                        <input type="text" class="form-control" id="code-create" name="code-create" placeholder="Masukkan kode produk">
                        <small><em class="text-danger" id="code-create-error"></em></small>
                    </div>
                    <div class="form-group mb-2">
                        <label for="name-create">Nama Produk</label>
                        <input type="text" class="form-control" id="name-create" name="name-create" placeholder="Masukkan nama produk">
                        <small><em class="text-danger" id="name-create-error"></em></small>
                    </div>
                    <div class="form-group mb-4">
                        <label for="price-create">Harga Produk</label>
                        <input type="text" class="form-control" id="price-create" name="price-create" placeholder="Masukkan harga produk">
                        <small><em class="text-danger" id="price-create-error"></em></small>
                    </div>
                    <div class="send-button">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                    <div class="loading-submit d-none align-items-center justify-content-center gap-2 text-primary">
                        <div class="spinner-border text-primary" role="status"></div>
                        Loading ...
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="updateProduct" tabindex="-1" aria-labelledby="updateProductLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="updateProductLabel">Edit Produk</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="product-update">
                    <div class="form-group mb-2">
                        <label for="code-update">Kode Produk</label>
                        <input type="hidden" class="form-control" id="id-update" name="id-update" placeholder="Masukkan kode produk">
                        <input type="text" class="form-control" id="code-update" name="code-update" placeholder="Masukkan kode produk">
                        <small><em class="text-danger" id="code-update-error"></em></small>
                    </div>
                    <div class="form-group mb-2">
                        <label for="name-update">Nama Produk</label>
                        <input type="text" class="form-control" id="name-update" name="name-update" placeholder="Masukkan nama produk">
                        <small><em class="text-danger" id="name-update-error"></em></small>
                    </div>
                    <div class="form-group mb-4">
                        <label for="price-update">Harga Produk</label>
                        <input type="text" class="form-control" id="price-update" name="price-update" placeholder="Masukkan harga produk">
                        <small><em class="text-danger" id="price-update-error"></em></small>
                    </div>
                    <div class="send-button">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                    <div class="loading-submit d-none align-items-center justify-content-center gap-2 text-primary">
                        <div class="spinner-border text-primary" role="status"></div>
                        Loading ...
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $('#produk').addClass('active')

        $('#data-table').DataTable().destroy();
        var table = $('#data-table').DataTable({
            ajax: "{{ route('produk.index') }}",
            lengthChange: false,
            columns: [
                { data: 'DT_RowIndex', searchable: false },
                { data: 'kode_produk' },
                { data: 'nama_produk' },
                { data: 'mitra.name' },
                { data: 'price' },
                { data: 'action', orderable: false, searchable: false }
            ],
            columnDefs: [
                { targets: 0, width: "20px" }, // Kolom pertama hanya 20px
                { targets: "_all", className: "text-start small" } // Semua teks lebih kecil & rata kanan
            ]
        });

        function handleCreate()
        {
            $('#code-create').val('');
            $('#code-create-error').html('');
            $('#name-create').val('');
            $('#name-create-error').html('');
            $('#price-create').val('');
            $('#price-create-error').html('');

            $('#createProduct').modal('show')
        }

        function editData(id)
        {
            $('#id-update').val('');
            $('#code-update').val('');
            $('#code-update-error').html('');
            $('#name-update').val('');
            $('#name-update-error').html('');
            $('#price-update').val('');
            $('#price-update-error').html('');

            $.ajax({
                url: `{!! route('produk.index') !!}/${id}/edit`,
                method: 'GET',
                success: function(res) {
                    const data = res.data
                    $('#id-update').val(data.id);
                    $('#code-update').val(data.kode_produk);
                    $('#name-update').val(data.nama_produk);
                    $('#price-update').val(data.harga);

                    $('#updateProduct').modal('show')
                },
                error: function(err) {
                    console.log(err)
                }
            })
        }


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
                        url: `{{ route('produk.index') }}/${id}`,
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

        $(document).ready(function () {
            $('#product-form').submit(function (e) {
                e.preventDefault(); // Mencegah reload halaman

                $('.loading-submit').removeClass('d-none');
                $('.loading-submit').addClass('d-flex');
                $('.send-button').addClass('d-none');

                $('#code-create-error').html('');
                $('#name-create-error').html('');
                $('#price-create-error').html('');

                let formData = {
                    _token: $('meta[name="csrf-token"]').attr('content'), // CSRF Token
                    kode_produk: $('#code-create').val(),
                    nama_produk: $('#name-create').val(),
                    harga: $('#price-create').val(),
                };

                $.ajax({
                    url: '{{ route("produk.store") }}', // Ganti dengan route Laravel yang sesuai
                    type: 'POST',
                    data: formData,
                    dataType: 'json', // Pastikan response di-handle sebagai JSON
                    success: function (response) {
                        $('#createProduct').modal('hide') // Tutup modal jika ada
                        table.ajax.reload(); // Reload DataTables (jika digunakan)

                        Swal.fire({
                            text: response.message,
                            icon: "success"
                        });
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON?.errors;
                        if (errors) {
                            $('#code-create-error').text(errors.kode_produk ? errors.kode_produk[0] : '');
                            $('#name-create-error').text(errors.nama_produk ? errors.nama_produk[0] : '');
                            $('#price-create-error').text(errors.harga ? errors.harga[0] : '');
                        } else {
                            $('#createProduct').modal('hide')
                            Swal.fire({
                                text: xhr.responseJSON.message,
                                icon: "error"
                            });
                        }
                    },
                    complete: function () {
                        $('.loading-submit').addClass('d-none');
                        $('.loading-submit').removeClass('d-flex');
                        $('.send-button').removeClass('d-none');
                    }
                });
            });
            $('#product-update').submit(function (e) {
                e.preventDefault(); // Mencegah reload halaman

                $('.loading-submit').removeClass('d-none');
                $('.loading-submit').addClass('d-flex');
                $('.send-button').addClass('d-none');

                $('#code-update-error').html('');
                $('#name-update-error').html('');
                $('#price-update-error').html('');

                let id = $('#id-update').val();
                let formData = {
                    _token: $('meta[name="csrf-token"]').attr('content'), // CSRF Token
                    kode_produk: $('#code-update').val(),
                    nama_produk: $('#name-update').val(),
                    harga: $('#price-update').val(),
                };

                $.ajax({
                    url: `{{ route("produk.index") }}/${id}`, // Ganti dengan route Laravel yang sesuai
                    type: 'PUT',
                    data: formData,
                    dataType: 'json', // Pastikan response di-handle sebagai JSON
                    success: function (response) {
                        $('#updateProduct').modal('hide') // Tutup modal jika ada
                        table.ajax.reload(); // Reload DataTables (jika digunakan)

                        Swal.fire({
                            text: response.message,
                            icon: "success"
                        });
                    },
                    error: function (xhr) {
                        let errors = xhr.responseJSON?.errors;
                        if (errors) {
                            $('#code-update-error').text(errors.kode_produk ? errors.kode_produk[0] : '');
                            $('#name-update-error').text(errors.nama_produk ? errors.nama_produk[0] : '');
                            $('#price-update-error').text(errors.harga ? errors.harga[0] : '');
                        } else {
                            $('#updateProduct').modal('hide')
                            Swal.fire({
                                text: xhr.responseJSON.message,
                                icon: "error"
                            });
                        }
                    },
                    complete: function () {
                        $('.loading-submit').addClass('d-none');
                        $('.loading-submit').removeClass('d-flex');
                        $('.send-button').removeClass('d-none');
                    }
                });
            });
        });
    </script>
@endpush
@endsection
