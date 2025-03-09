@extends('Components.Layout')
@section('content')

<div class="row gap-4">
    <div class="col-12">
        <h4 class="m-md-0 text-md-start text-center">Detail Pemesanan</h4>
    </div>
    <div class="col-12">
        <div class="card mb-3">
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-12">
                        <h5 class="card-title m-0 text-primary">Data Mitra</h5>
                    </div>
                    <hr class="mb-2 mt-3">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="email">Nama Mitra</label>
                            <input readonly type="email" class="form-control" name="email" placeholder="Autofill email mitra disini" value="{{ $dataPemesanan['nama_mitra'] }}">
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input readonly type="email" class="form-control" name="email" placeholder="Autofill email mitra disini" value="{{ $dataPemesanan['mitra']['email'] }}">
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="whatsapp">Nomor Whatsapp Aktif</label>
                            <input readonly type="number" class="form-control" name="whatsapp" placeholder="Autofill whatsapp mitra disini" value="{{ $dataPemesanan['mitra']['phone_number'] }}">
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
                    </div>

                    <hr class="mb-2 mt-3">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hovered table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Kode Produk</th>
                                        <th>Nama Produk</th>
                                        <th style="min-width: 120px">Jumlah Pesanan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($dataPemesanan['detail']) > 0)
                                        @foreach ($dataPemesanan['detail'] as $key => $detail)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $detail->kode_produk }}</td>
                                                <td>{{ $detail->nama_produk }}</td>
                                                <td>{{ $detail->jumlah_pesan }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="4" class="text-center small">Tidak ada produk ditemukan</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $('#pemesanan').addClass('active')
    </script>
@endpush
@endsection
