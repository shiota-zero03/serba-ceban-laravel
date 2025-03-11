@extends('Components.Layout')
@section('content')

<div class="row gap-4">
    <div class="col-12">
        <div class="d-md-flex align-items-center justify-content-between">
            <div>
                <h4 class="m-md-0 text-md-start text-center">Laporan Penjualan</h4>
            </div>
        </div>
    </div>
    <div class="col-12 position-relative">
        <div class="card">
            <div class="card-body">
                <div class="mb-4">
                    <label for="filter">Filter Tanggal Laporan</label>
                    <form class="d-flex align-items-center gap-2">
                        <input type="date" id="startDate" name="startDate" class="form-control" value="{{ isset($_GET['startDate']) ? $_GET['startDate'] : "" }}">
                        s/d
                        <input type="date" id="endDate" name="endDate" class="form-control" value="{{ isset($_GET['endDate']) ? $_GET['endDate'] : "" }}">
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-2">
                            <i class="bx bx-filter"></i>Filter
                        </button>
                        <button type="button" onclick="exportData()" class="btn btn-success d-flex align-items-center gap-2">
                            <i class="bx bx-download"></i> Export
                        </button>
                    </form>
                </div>
                <div style="overflow-x: auto; overflow-y: hidden">
                    <table class="table border table-striped table-bordered text-nowrap dataTable text-start span">
                        <thead>
                            <tr>
                                <th class="text-center align-middle" rowspan="2">No</th>
                                <th class="text-center align-middle" rowspan="2">Nama Supplier</th>
                                <th class="text-center align-middle" rowspan="2">Nomor Rekening</th>
                                <th class="text-center align-middle" rowspan="2">Jenis Bank/E-Wallet</th>
                                <th class="text-center align-middle" rowspan="2">Kode Produk</th>
                                <th class="text-center align-middle" rowspan="2">Nama Produk</th>
                                @foreach ($dateData as $inDate => $date)
                                    <th class="text-center align-middle" colspan="3">{{ $date }}</th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach ($dateData as $date)
                                    <th class="text-center align-middle">Terjual</th>
                                    <th class="text-center align-middle">Sisa</th>
                                    <th class="text-center align-middle">Pendapatan Mitra</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($result as $ind => $res)
                                @php $rowspan = count($res['dataProduk']) @endphp
                                @foreach ($res['dataProduk'] as $pIndex => $product)
                                    <tr>
                                        @if ($pIndex === 0)
                                            <td rowspan="{{ $rowspan }}">{{ $ind + 1 }}</td>
                                            <td rowspan="{{ $rowspan }}">{{ $res['name'] }}</td>
                                            <td rowspan="{{ $rowspan }}">{{ $res['nomor_rekening'] ?? "-" }}</td>
                                            <td rowspan="{{ $rowspan }}">{{ $res['nama_bank'] ?? "-" }}</td>
                                        @endif
                                        <td>{{ $product['kode_produk'] }}</td>
                                        <td>{{ $product['nama_produk'] }}</td>
                                        @foreach ($dateFormat as $tanggal => $date)
                                            @php
                                                $penjualan = $res['dataPenjualan'][$date][$pIndex] ?? ['terjual' => 0, 'sisa' => 0];
                                                $pendapatanMitra = $res['transfer'][$date] ?? 0;
                                            @endphp
                                            <td class="text-center">{{ $penjualan['terjual'] }}</td>
                                            <td class="text-center">{{ $penjualan['sisa'] }}</td>
                                            @if ($pIndex === 0)
                                                <td rowspan="{{ $rowspan }}" class="text-center">Rp {{ number_format($pendapatanMitra, 0, ',', '.') }}</td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $('#laporan').addClass('active')

        function exportData(){
            let start = $('#startDate').val()
            let end = $('#endDate').val()
            document.location.href=`{!! route('laporan.export') !!}?startDate=${start}&endDate=${end}`
        }
    </script>
@endpush
@endsection
