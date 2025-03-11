<style>
    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
    }
    th, td {
        padding: 5px;
        text-align: center;
    }
</style>

<table border="10">
    <thead>
        <tr>
            <th rowspan="2"><strong>No</strong></th>
            <th rowspan="2"><strong>Nama Supplier</strong></th>
            <th rowspan="2"><strong>Kode Produk</strong></th>
            <th rowspan="2"><strong>Nama Produk</strong></th>
            @foreach ($dateData as $date)
                <th colspan="4"><strong>{{ $date }}</strong></th>
            @endforeach
        </tr>
        <tr>
            @foreach ($dateData as $date)
                <th style="width: 150px"><strong>Plan Produk</strong></th>
                <th style="width: 150px"><strong>Act Produk</strong></th>
                <th style="width: 150px"><strong>Produk Terjual</strong></th>
                <th style="width: 150px"><strong>Produk Sisa</strong></th>
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
                    @endif
                    <td>{{ "".$product['kode_produk'] }}</td>
                    <td>{{ $product['nama_produk'] }}</td>
                    @foreach ($dateFormat as $tanggal => $date)
                        @php
                            $penjualan = $res['dataPenjualan'][$date][$pIndex] ?? ['terjual' => 0, 'sisa' => 0, 'dipesan' => 0, 'terima' => 0];
                            $pendapatanMitra = $res['transfer'][$date] ?? 0;
                        @endphp
                        <td>{{ $penjualan['dipesan'] }}</td>
                        <td>{{ $penjualan['terima'] }}</td>
                        <td>{{ $penjualan['terjual'] }}</td>
                        <td>{{ $penjualan['sisa'] }}</td>
                    @endforeach
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
