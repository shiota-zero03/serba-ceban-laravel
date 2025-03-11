<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Pemesanan;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

class LaporanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');

        $startDate = $request->startDate
            ? Carbon::parse($request->startDate)
            : Carbon::now()->subWeek(); // Default: 7 hari sebelum sekarang

        $endDate = $request->endDate
            ? Carbon::parse($request->endDate)
            : Carbon::now(); // Default: Hari ini

        // Pastikan endDate tidak lebih kecil dari startDate
        if ($endDate->lt($startDate)) {
            $endDate = $startDate;
        }

        $dates = CarbonPeriod::create($startDate, $endDate)->toArray();

        $result = $this->mitra_data($dates, $startDate, $endDate);

        $dateData = collect($dates)->map(fn($d) => $d->translatedFormat('l, d F Y'));
        $dateFormat = collect($dates)->map(fn($d) => $d->translatedFormat('Y-m-d'));
        return view('pages.laporan.index', compact(['dateData', 'dateFormat', 'result']));
    }

    public function export(Request $request)
    {
        Carbon::setLocale('id');
        date_default_timezone_set('Asia/Jakarta');

        $startDate = $request->startDate
            ? Carbon::parse($request->startDate)
            : Carbon::now()->subWeek(); // Default: 7 hari sebelum sekarang

        $endDate = $request->endDate
            ? Carbon::parse($request->endDate)
            : Carbon::now(); // Default: Hari ini

        // Pastikan endDate tidak lebih kecil dari startDate
        if ($endDate->lt($startDate)) {
            $endDate = $startDate;
        }

        $dates = CarbonPeriod::create($startDate, $endDate)->toArray();

        $result = $this->mitra_data($dates, $startDate, $endDate);

        $dateData = collect($dates)->map(fn($d) => $d->translatedFormat('l, d F Y'));
        $dateFormat = collect($dates)->map(fn($d) => $d->translatedFormat('Y-m-d'));

        $data1 = [
            'dateData' => $dateData,
            'dateFormat' => $dateFormat,
            'result' => $result
        ];
        return Excel::download(new LaporanExport($data1), 'laporn-penjualan.xlsx');
    }

    public function mitra_data($dates, $startDate, $endDate)
    {

        $mitra = User::select('id', 'name', 'nama_bank', 'nomor_rekening')
            ->where('role', 'MITRA')
            ->with(['produk'])
            ->get();

        $pesanan = Pemesanan::whereBetween('tanggal_penerimaan', [$startDate, $endDate])
            ->with(['detail', 'bayar'])
            ->get();

        $result = [];

        foreach ($mitra as $mitraItem) {
            $mitraId = $mitraItem->id;

            // Ambil semua produk yang pernah dijual mitra ini
            $produkMitra = $mitraItem->produk->map(fn($p) => [
                'produk_id' => $p->id,
                'kode_produk' => $p->kode_produk,
                'nama_produk' => $p->nama_produk
            ])->toArray();

            // Jika mitra belum pernah menjual produk, tambahkan produk kosong
            if (empty($produkMitra)) {
                $produkMitra = [['produk_id' => null, 'kode_produk' => '-', 'nama_produk' => '-']];
            }

            // Kumpulkan data penjualan per tanggal (tetap tampil meskipun kosong)
            $dataPenjualan = [];
            $transferMitra = [];

            foreach ($dates as $date) {
                $tanggal = $date->format('Y-m-d');

                // Filter pesanan sesuai tanggal dan mitra_id
                $pesananHarian = $pesanan->where('tanggal_penerimaan', $tanggal)->where('mitra_id', $mitraId);

                // Data produk yang terjual dan sisa stok per tanggal
                $detailHarian = $pesananHarian->flatMap(fn($p) => $p->detail)
                    ->mapWithKeys(fn($d) => [$d->produk_id => [
                        'produk_id' => $d->produk_id,
                        'dipesan' => $d->jumlah_pesan,
                        'terima' => $d->jumlah_terima,
                        'terjual' => $d->jumlah_terjual,
                        'sisa' => $d->jumlah_terima - $d->jumlah_terjual
                    ]]);

                // Jika tidak ada penjualan di tanggal ini, isi dengan default 0 untuk semua produk
                $produkFinal = collect($produkMitra)->map(fn($p) => [
                    'produk_id' => $p['produk_id'],
                    'kode_produk' => $p['kode_produk'],
                    'nama_produk' => $p['nama_produk'],
                    'dipesan' => $detailHarian[$p['produk_id']]['dipesan'] ?? 0,
                    'terima' => $detailHarian[$p['produk_id']]['terima'] ?? 0,
                    'terjual' => $detailHarian[$p['produk_id']]['terjual'] ?? 0,
                    'sisa' => $detailHarian[$p['produk_id']]['sisa'] ?? 0
                ])->values()->toArray();

                // Simpan ke data penjualan
                $dataPenjualan[$tanggal] = $produkFinal;

                // Total transfer mitra per tanggal, default 0 jika tidak ada pesanan
                $transferMitra[$tanggal] = $pesananHarian->sum(fn($p) => $p->bayar?->total_transfer ?? 0);
            }

            // Simpan data mitra dalam array hasil
            $result[] = [
                'id' => $mitraItem->id,
                'name' => $mitraItem->name,
                'nama_bank' => $mitraItem->nama_bank,
                'nomor_rekening' => $mitraItem->nomor_rekening,
                'dataProduk' => $produkMitra,
                'dataPenjualan' => $dataPenjualan,
                'transfer' => $transferMitra
            ];
        }
        return $result;
    }
}
