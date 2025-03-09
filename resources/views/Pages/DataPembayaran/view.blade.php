@extends('Components.Layout')
@section('content')

<div class="row gap-4">
    <div class="col-12">
        <h4 class="m-md-0 text-md-start text-center">Detail Pembayaran</h4>
    </div>
    <form action="{{ route('data-pembayaran.update', $bayar['id']) }}" class="col-12" method="POST" enctype="multipart/form-data">
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
                            <input readonly type="email" class="form-control" name="email" placeholder="Autofill email mitra disini" value="{{ $bayar['mitra']['name'] }}">
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input readonly type="email" class="form-control" name="email" placeholder="Autofill email mitra disini" value="{{ $bayar['mitra']['email'] }}">
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="whatsapp">Nomor Whatsapp Aktif</label>
                            <input readonly type="number" class="form-control" name="whatsapp" placeholder="Autofill whatsapp mitra disini" value="{{ $bayar['mitra']['phone_number'] }}">
                        </div>
                    </div>

                    <div class="col-md-4 col-12">
                        <div class="form-group">
                            <label for="nama_bank">Nama Bank Mitra</label>
                            <input readonly type="text" class="form-control" name="nama_bank" placeholder="Autofill nama bank mitra disini" value="{{ $bayar['mitra']['nama_bank'] }}">
                        </div>
                    </div>

                    <div class="col-md-4 col-12">
                        <div class="form-group">
                            <label for="nomor_rekening">Nomor Rekening</label>
                            <input readonly type="text" class="form-control" name="nomor_rekening" placeholder="Autofill nomor rekening mitra disini" value="{{ $bayar['mitra']['nomor_rekening'] }}">
                        </div>
                    </div>

                    <div class="col-md-4 col-12">
                        <div class="form-group">
                            <label for="atas_nama">Nama Pemilik Rekening</label>
                            <input readonly type="text" class="form-control" name="atas_nama" placeholder="Autofill nama pemilik rekening mitra disini" value="{{ $bayar['mitra']['atas_nama'] }}">
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
                        <h5 class="card-title m-0 text-primary">Data Pembayaran</h5>
                    </div>

                    <hr class="mb-2 mt-3">
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="total">Total Produk Terjual</label>
                            <input readonly type="text" class="form-control" name="total" placeholder="Autofill total produk terjual disini" value="{{ $biaya['totalTerjual'] }}">
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="harga">Harga Produk Terjual</label>
                            <input readonly type="text" class="form-control" name="harga" placeholder="Autofill harga produk terjual disini" value="Rp {{ number_format($biaya['biayaTerjual'], 0, '.', '.') }}">
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="total_sisa">Total Produk Sisa</label>
                            <input readonly type="text" class="form-control" name="total_sisa" placeholder="Autofill total sisa produk disini" value="{{ $biaya['totalSisa'] }}">
                        </div>
                    </div>
                    <div class="col-md-6 col-12">
                        <div class="form-group">
                            <label for="harga_sisa">Harga Produk Sisa</label>
                            <input readonly type="text" class="form-control" name="harga_sisa" placeholder="Autofill harga sisa produk disini" value="Rp {{ number_format($biaya['biayaSisa'], 0, '.', '.') }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="plastik">Biaya Plastik</label>
                            <input readonly type="text" class="form-control" name="plastik" placeholder="Autofill biaya plastik produk disini" value="Rp {{ number_format($biaya['biayaPlastik'], 0, '.', '.') }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <hr>
                    </div>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="total_bayar">Total Bayar ke Mitra</label>
                            <input readonly type="text" class="form-control" name="total_bayar" placeholder="Autofill total bayar ke mitra disini" value="Rp {{ number_format(($biaya['biayaTerjual'] - $biaya['biayaSisa'] - $biaya['biayaPlastik']), 0, '.', '.') }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <div>
                            <div class="mb-2">
                                <label>Status Pembayaran</label><br />
                                <span class="@if($biaya['status'] == 'Pending') text-warning @elseif($biaya['status'] == 'Lunas') text-success @else text-danger @endif">{{ $biaya['status'] }}</span>
                            </div>
                            <div>
                                <label>Bukti Pembayaran</label><br />
                                <img src="{{ asset('/').$biaya['bukti'] }}" alt="" width="150" class="rounded">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($biaya['status'] == 'Pending')
            <button class="btn btn-success col-12 d-flex align-items-center justify-content-center gap-2" type="submit">
                <i class="bx bx-check"></i>
                Terima Pembayaran
            </button>
        @endif
    </form>
</div>

@push('script')
    <script>
        $('#data-pembayaran').addClass('active')
    </script>
@endpush
@endsection
