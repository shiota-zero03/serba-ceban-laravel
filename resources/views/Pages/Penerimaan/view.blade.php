@extends('Components.Layout')
@section('content')

@php
    use Carbon\Carbon;

    // Pastikan zona waktu diatur ke Asia/Jakarta
    date_default_timezone_set('Asia/Jakarta');

    // Ambil tanggal penerimaan dan hanya ambil bagian Y-m-d
    $tanggalPenerimaan = Carbon::parse($dataPemesanan['tanggal_penerimaan'])->startOfDay(); // Set ke 00:00:00
    $batasAkhir = $tanggalPenerimaan->copy()->addDays(1)->endOfDay(); // Akhir hari ke-2

    // Ambil tanggal hari ini tanpa jam
    $hariIni = Carbon::now('Asia/Jakarta')->startOfDay();
@endphp

<div class="row gap-4">
    <div class="col-12">
        <h4 class="m-md-0 text-md-start text-center">Detail Penerimaan</h4>
    </div>
    <form action="{{ route('penerimaan.update', $dataPemesanan['id']) }}" class="col-12" method="POST">
        @csrf
        @method('PUT')
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
                                        <th style="min-width: 120px">Jumlah Pesanan (Request)</th>
                                        <th style="min-width: 120px">Jumlah Barang Diterima</th>
                                        <th style="min-width: 120px">Selisih Penerimaan</th>
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
                                                <td>
                                                    @if($dataPemesanan['is_paid'])
                                                        {{ $detail->jumlah_terima }}
                                                    @else
                                                        @if($hariIni->between($tanggalPenerimaan, $batasAkhir))
                                                            <input type="number" name="jumlah[{{ $detail['id'] }}]" class="form-control jumlah-terima" min="0"
                                                                value="{{ $detail->jumlah_terima }}"
                                                                data-jumlah-pesan="{{ $detail->jumlah_pesan }}"
                                                                data-id="{{ $detail['id'] }}">
                                                        @else
                                                            {{ $detail->jumlah_terima }}
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($dataPemesanan['is_paid'])
                                                        {{ $detail->jumlah_pesan - $detail->jumlah_terima }}
                                                    @else
                                                        @if($hariIni->between($tanggalPenerimaan, $batasAkhir))
                                                            <input readonly type="number" name="selisih[{{ $detail['id'] }}]" class="form-control selisih" min="0"
                                                                value="{{ $detail->jumlah_pesan - $detail->jumlah_terima }}" id="selisih-{{ $detail['id'] }}">
                                                        @else
                                                            {{ $detail->jumlah_pesan - $detail->jumlah_terima }}
                                                        @endif
                                                    @endif
                                                </td>
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
        @if(!$dataPemesanan['is_paid'])
            @if($hariIni->between($tanggalPenerimaan, $batasAkhir))
                <button class="btn btn-primary col-12 d-flex align-items-center justify-content-center gap-2" type="submit">
                    <i class="bx bx-save"></i>
                    Simpan
                </button>
            @endif
        @endif
    </form>
</div>

@push('script')
    <script>
        $('#penerimaan').addClass('active')

        document.addEventListener('input', function (event) {
            if (event.target.classList.contains('jumlah-terima')) {
                let jumlahTerima = parseInt(event.target.value) || 0;
                let jumlahPesan = parseInt(event.target.dataset.jumlahPesan) || 0;
                let id = event.target.dataset.id;
                let selisihField = document.getElementById('selisih-' + id);

                selisihField.value = jumlahPesan - jumlahTerima;
            }
        });
    </script>
@endpush
@endsection
