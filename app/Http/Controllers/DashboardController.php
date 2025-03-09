<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request){
        if($request->ajax()){
            $tahun = $request->query('tahun', Carbon::now()->year);

            if(auth()->user()->role == 'MITRA') {
                $data = DB::table('pembayarans')
                    ->selectRaw('MONTH(tanggal_pembayaran) as bulan, SUM(produk_terjual) as total, SUM(total_transfer) as transfer')
                    ->where('mitra_id', auth()->user()->id)
                    ->whereYear('tanggal_pembayaran', $tahun)
                    ->groupBy('bulan')
                    ->orderBy('bulan')
                    ->get()
                    ->keyBy('bulan');

                $bulan = [];
                $count = [];
                $transfer = [];
                for ($i = 1; $i <= 12; $i++) {
                    $bulan[] = Carbon::createFromDate(null, $i, 1)->locale('id')->translatedFormat('M');
                    $c = $data->get($i)->total ?? 0;
                    $count[] = doubleval($c);
                    $t = $data->get($i)->transfer ?? 0;
                    $transfer[] = doubleval($t);
                }

                $data = [
                    'bulan' => $bulan,
                    'count' => $count,
                    'transfer' => $transfer,
                    'role' => 'mitra'
                ];
            } else {
                $data = DB::table('pembayarans')
                    ->selectRaw('MONTH(tanggal_pembayaran) as bulan, SUM(produk_terjual) as total, SUM(total_penjualan) as penjualan, SUM(total_potongan) as potongan')
                    ->whereYear('tanggal_pembayaran', $tahun)
                    ->groupBy('bulan')
                    ->orderBy('bulan')
                    ->get()
                    ->keyBy('bulan');

                $pemesanan = DB::table('pemesanans')
                    ->join('detail_pemesanans', 'pemesanans.id', '=', 'detail_pemesanans.pemesanan_id')
                    ->selectRaw('
                        MONTH(tanggal_pemesanan) as bulan,
                        SUM(detail_pemesanans.jumlah_pesan) as total_pesan,
                        SUM(detail_pemesanans.jumlah_terjual) as total_terjual,
                        SUM(detail_pemesanans.jumlah_terima) as total_terima
                    ')
                    ->whereYear('tanggal_pemesanan', $tahun) // Filter berdasarkan tahun pemesanan
                    ->groupBy('bulan')
                    ->orderBy('bulan')
                    ->get()
                    ->keyBy('bulan');

                $bulan = [];
                $count = [];
                $penjualan = [];
                $potongan = [];

                $pesan = [];
                $terima = [];
                $jual = [];


                for ($i = 1; $i <= 12; $i++) {
                    $bulan[] = Carbon::createFromDate(null, $i, 1)->locale('id')->translatedFormat('M');
                    $c = $data->get($i)->total ?? 0;
                    $count[] = doubleval($c);

                    $t = $data->get($i)->penjualan ?? 0;
                    $penjualan[] = doubleval($t);

                    $pot = $data->get($i)->potongan ?? 0;
                    $potongan[] = doubleval($pot);

                    $pes = $pemesanan->get($i)->total_pesan ?? 0;
                    $pesan[] = doubleval($pes);

                    $ju = $pemesanan->get($i)->total_terjual ?? 0;
                    $jual[] = doubleval($ju);

                    $ter = $pemesanan->get($i)->total_terima ?? 0;
                    $terima[] = doubleval($ter);
                }

                $data = [
                    'bulan' => $bulan,
                    'count' => $count,
                    'penjualan' => $penjualan,
                    'potongan' => $potongan,
                    'pesan' => $pesan,
                    'terima' => $terima,
                    'jual' => $jual,


                    'role' => 'admin'
                ];
            }

            return response()->json(['data' => $data]);
        }

        if(auth()->user()->role == 'MITRA') {
            $mitraId = auth()->user()->id;

            $today = Carbon::now('Asia/Jakarta')->toDateString();
            $yesterday = Carbon::now('Asia/Jakarta')->subDay()->toDateString();

            $totalProduk = Product::where('mitra_id', $mitraId)->count();

            $totalHariIni = Pembayaran::where('mitra_id', $mitraId)
                ->whereDate('tanggal_pembayaran', $today)
                ->sum('total_transfer');

            $totalKemarin = Pembayaran::where('mitra_id', $mitraId)
                ->whereDate('tanggal_pembayaran', $yesterday)
                ->sum('total_transfer');

            if ($totalKemarin > 0) {
                $persentasePerubahan = (($totalHariIni - $totalKemarin) / $totalKemarin) * 100;
            } else {
                $persentasePerubahan = $totalHariIni > 0 ? 100 : 0; // Jika hari ini ada transaksi dan kemarin tidak, maka 100% naik
            }

            $data = [
                'totalProduk' => $totalProduk,
                'totalTransferHariIni' => $totalHariIni,
                'totalTransferKemarin' => $totalKemarin,
                'persentasePerubahan' => round($persentasePerubahan, 2) // Dibulatkan 2 desimal
            ];
        } else {
            $today = Carbon::now('Asia/Jakarta')->toDateString();
            $yesterday = Carbon::now('Asia/Jakarta')->subDay()->toDateString();

            $mitra = User::where('role', 'MITRA')->get()->count();
            $pesananHariIni = Pemesanan::whereDate('tanggal_pemesanan', $today)->get()->count();
            $penjualan = Pemesanan::with(['detail'])->whereDate('tanggal_penerimaan', $today)->get();
            $penjualanHariIni = 0;
            foreach ($penjualan as $key => $value) {
                foreach ($value['detail'] as $vkey => $vvalue) {
                    $penjualanHariIni += $vvalue->jumlah_terjual;
                }
            }

            $pendapatanHariIni = Pembayaran::whereDate('tanggal_pembayaran', $today)
                ->sum('total_penjualan');

            $data = [
                'totalMitra' => $mitra,
                'pesananHariIni' => $pesananHariIni,
                'penjualanHariIni' => $penjualanHariIni,
                'pendapatanHariIni' => $pendapatanHariIni,
            ];
        }

        return view('Pages.Dashboard', compact(['data']));
    }
}
