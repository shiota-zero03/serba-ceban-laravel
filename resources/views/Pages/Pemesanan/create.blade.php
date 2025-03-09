@extends('Components.Layout')
@section('content')

<div class="row gap-4">
    <div class="col-12">
        <h4 class="m-md-0 text-md-start text-center">Tambah Pemesanan</h4>
    </div>
    <div class="col-12">
        <form action="{{route('pemesanan.store')}}" method="POST">
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-2">
                        @csrf
                        <div class="col-12">
                            <h5 class="card-title m-0 text-primary">Data Mitra</h5>
                        </div>
                        <hr class="mb-2 mt-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="mitra_id">Pilih Mitra</label>
                                <select name="mitra_id" id="mitra_id" class="form-control">
                                    <option value=""></option>
                                    @foreach ($mitra as $user)
                                        <option @selected(old('mitra_id') == $user->id) value="{{ $user->id }}">{{ $user->name }} - {{ $user->phone_number }}</option>
                                    @endforeach
                                </select>
                                @error('mitra_id')
                                    <small class="text-danger"><em>{{ $message }}</em></small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input readonly type="email" class="form-control" name="email" placeholder="Autofill email mitra disini" value="{{ old('email') }}">
                                @error('email')
                                    <small class="text-danger"><em>{{ $message }}</em></small>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label for="whatsapp">Nomor Whatsapp Aktif</label>
                                <input readonly type="number" class="form-control" name="whatsapp" placeholder="Autofill whatsapp mitra disini" value="{{ old('whatsapp') }}">
                                @error('whatsapp')
                                    <small class="text-danger"><em>{{ $message }}</em></small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-2">
                        @csrf
                        <div class="col-12">
                            <h5 class="card-title m-0 text-primary">Data Produk Mitra</h5>
                            <small><em>Pilih produk yang ingin di request dan isi jumlah pesanannya</em></small>
                        </div>

                        @if ($errors->has('produk_id') || $errors->has('jumlah') || $errors->get('jumlah.*'))
                            <small class="text-danger"><em>Harap cek form yang ingin dipesan dan masukkan jumlah pesananya</em></small><br>
                        @endif

                        <hr class="mb-2 mt-3">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hovered table-striped">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Kode Produk</th>
                                            <th>Nama Produk</th>
                                            <th>Harga</th>
                                            <th style="min-width: 120px">Jumlah Pesanan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="5" class="text-center small">Tidak ada produk ditemukan</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-primary col-12 d-flex align-items-center justify-content-center gap-2" type="submit">
                <i class="bx bx-save"></i>
                Simpan
            </button>
        </form>
    </div>
</div>

@push('script')
    <script>
        $('#pemesanan').addClass('active')

        function getTable () {
            let mitraId = $('#mitra_id').val()

            if(mitraId) {
                $('#loading-set').removeClass('d-none');
                $('#loading-set').addClass('d-flex');
                $.ajax({
                    url: `{!! route('pemesanan.index') !!}/${mitraId}`,
                    method: "GET",
                    success: function(res) {
                        let data = res.data;
                        $('input[name=email]').val(data.email)
                        $('input[name=whatsapp]').val(data.phone_number)
                        let tableBody = $('table tbody');
                        tableBody.empty();

                        if (data.produk.length > 0) {
                            data.produk.forEach(product => {
                                tableBody.append(`
                                    <tr>
                                        <td class="text-center">
                                            <input type="checkbox" name="produk_id[]" value="${product.id}" class="produk-checkbox">
                                        </td>
                                        <td>${product.kode_produk}</td>
                                        <td>${product.nama_produk}</td>
                                        <td>Rp ${product.harga.toLocaleString('id-ID')}</td>
                                        <td>
                                            <input type="number" name="jumlah[${product.id}]" class="form-control" min="1" disabled>

                                        </td>
                                    </tr>
                                `);
                            });
                        } else {
                            tableBody.append('<tr><td colspan="5" class="text-center small">Tidak ada produk ditemukan</td></tr>');
                        }
                    },
                    error: function(err) {
                        console.error(err)
                    },
                    complete: function(){
                        $('#loading-set').addClass('d-none');
                        $('#loading-set').removeClass('d-flex');
                    }
                })
            }
        }

        $(document).ready(function() {
            getTable()
            $('#mitra_id').select2({
                placeholder: "Pilh mitra",
                width: '100%'
            });

            $('#mitra_id').on('change', function(){
                getTable();
            })

            $(document).on('change', '.produk-checkbox', function() {
                let inputJumlah = $(this).closest('tr').find('input[type="number"]');
                inputJumlah.prop('disabled', !this.checked);
            });
        });
    </script>
@endpush
@endsection
