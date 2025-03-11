<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Pemesanan;
use App\Models\DetailPemesanan;
use App\Models\User;
use App\Models\Product;
use App\Models\Pembayaran;

use App\Services\WhatsappService;

class PembayaranController extends Controller
{
    private $waService;
    public function __construct(WhatsappService $waService)
    {
        $this->middleware('just-admin');
        $this->waService = $waService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $data = Pemesanan::with(['mitra', 'detail', 'bayar'])->whereNotNull('mitra_id')->where('is_accepted', true)->orderByDesc('id');

            if ($request->has('tanggal') && !empty($request->tanggal)) {
                $data->where('tanggal_penerimaan', $request->tanggal);
            }

            return DataTables::of($data->get())
                ->addIndexColumn()
                ->addColumn('transfer', function($row) {
                    if($row['bayar']) {
                        return "Rp ".number_format($row['bayar']['total_transfer'], 0, '.', '.');
                    } else {
                        $amount = 0;
                        $jasatitip = 0;
                        $plastik = 1000;
                        foreach ($row['detail'] as $key => $value) {
                            $product = Product::find($value['produk_id']);
                            $amount += $value['jumlah_terjual'] * $product['harga'];
                            $jasatitip += ($value['jumlah_terima'] - $value['jumlah_terjual']) * 1000;
                        }

                        return "Rp ".number_format(($amount - $jasatitip - $plastik), 0, '.', '.');
                    }
                })
                ->addColumn('status', function($row) {
                    $showButton = "";
                    $status = "";
                    $color = "";
                    if($row['bayar']) {
                        $status = $row['bayar']['status_pembayaran'];
                        if($status == 'Pending') {
                            $showButton = "<a href='".route('pembayaran.accept', $row->id)."' class='ms-2 btn btn-success py-1 rounded small btn-xs me-1'><i class='bx bx-check'></i></a>";
                            $color = 'text-warning';
                        } elseif($status == 'Lunas') {
                            $color = 'text-success';
                        } else {
                            $color = 'text-danger';
                        }
                    } else {
                        $status = 'Belum Dibayarkan';
                        $color = "text-danger";
                    }
                    return "
                        <div>
                            <span class='$color'>$status</span>
                            $showButton
                        </div>
                    ";
                })
                ->addColumn('tanggal_bayar', function($row) {
                    if($row['bayar']) {
                        return $row['bayar']['tanggal_pembayaran'];
                    } else {
                        return "-";
                    }
                })
                ->addColumn('action', function($row){
                    $showButton = "<a href='".route('pembayaran.show', $row->id)."' class='btn btn-info py-1 rounded small btn-xs me-1'><i class='bx bx-show-alt'></i></a>";
                    return $showButton;
                })
                ->rawColumns(['transfer', 'status', 'tanggal_bayar', 'action'])
                ->make(true);
        }

        return view('Pages.Pembayaran.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $dataPemesanan = Pemesanan::with(['mitra', 'detail', 'bayar'])->findOrFail($id);

        $totalTerjual = 0;
        $biayaTerjual = 0;
        $totalSisa = 0;
        $biayaSisa = 0;
        $biayaPlastik = 1000;

        $bukti = null;
        $status = 'Pending';

        if(!$dataPemesanan['bayar']) {
            foreach ($dataPemesanan['detail'] as $key => $value) {
                $product = Product::find($value['produk_id']);
                $totalTerjual += $value['jumlah_terjual'];
                $biayaTerjual += $product['harga'] * $value['jumlah_terjual'];

                $sisa = $value['jumlah_terima'] - $value['jumlah_terjual'];
                $totalSisa += $sisa;
                $biayaSisa += $sisa * 1000;
            }
        } else {
            $bayar = Pembayaran::where('pemesanan_id', $id)->first();
            $totalTerjual = $bayar->produk_terjual;
            $biayaTerjual = $bayar->total_penjualan;
            $totalSisa = $bayar->produk_sisa;
            $biayaSisa = $bayar->total_potongan;
            $biayaPlastik = $bayar->biaya_plastik;
            $bukti = $bayar->bukti_transfer;
            $status = $bayar->status_pembayaran;
        }

        $biaya = [
            'totalTerjual' => $totalTerjual,
            'biayaTerjual' => $biayaTerjual,
            'totalSisa' => $totalSisa,
            'biayaSisa' => $biayaSisa,
            'biayaPlastik' => $biayaPlastik,
            'bukti' => $bukti,
            'status' => $status
        ];

        return view('Pages.Pembayaran.view', compact(['dataPemesanan', 'biaya']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->__rules($request);
        try {
            DB::beginTransaction();

            $pesanan = Pemesanan::with(['mitra', 'detail', 'bayar'])->find($id);

            $phone = $pesanan['mitra']['phone_number'];
            $name = $pesanan['mitra']['name'];

            $totalTerjual = 0;
            $biayaTerjual = 0;
            $totalSisa = 0;
            $biayaSisa = 0;
            $biayaPlastik = 1000;

            if(!$pesanan['bayar']) {
                foreach ($pesanan['detail'] as $key => $value) {
                    $product = Product::find($value['produk_id']);
                    $totalTerjual += $value['jumlah_terjual'];
                    $biayaTerjual += $product['harga'] * $value['jumlah_terjual'];

                    $sisa = $value['jumlah_terima'] - $value['jumlah_terjual'];
                    $totalSisa += $sisa;
                    $biayaSisa += $sisa * 1000;
                }
            }

            $data = [
                'mitra_id' => $pesanan->mitra_id,
                'pemesanan_id' => $pesanan->id,
                'tanggal_pembayaran' => now(),
                'total_penjualan' => $biayaTerjual,
                'total_potongan' => $biayaSisa,
                'total_transfer' => $biayaTerjual - ($biayaSisa + $biayaPlastik),
                'status_pembayaran' => 'Pending',
                'biaya_plastik' => $biayaPlastik,
                'nomor_rekening_penerima' => $pesanan['mitra']['nomor_rekening'],
                'nama_penerima' => $pesanan['mitra']['atas_nama'],
                'produk_terjual' => $totalTerjual,
                'produk_sisa' => $totalSisa
            ];

            if ($request->hasFile('bukti_bayar')) {
                $file = $request->file('bukti_bayar');
                $filename = time() . '_' . $file->getClientOriginalName(); // Nama unik
                $file->move(public_path('dist/images'), $filename); // Simpan ke /dist/images

                $data['bukti_transfer'] = 'dist/images/' . $filename;
            }

            $pesanan->update(['is_paid' => true]);


            $totalMasuk = $totalTerjual + $totalSisa;
            $bt = 'Rp '.number_format($biayaTerjual, 0, '.', '.');
            $bs = 'Rp '.number_format($biayaSisa, 0, '.', '.');
            $bp = 'Rp '.number_format($biayaPlastik, 0, '.', '.');
            $tf = $biayaTerjual - ($biayaSisa + $biayaPlastik);

$message = "
Kepada Yth. $name
Admin telah melakukan transfer uang ke rekening anda dengan rincian sebagai berikut:

- Produk diterima: $totalMasuk
- Produk terjual: $totalTerjual
- Produk sisa: $totalSisa

- Total penjualan: $bt
- Total potongan: $bs
- Biaya Plastik: $bp
- Total Transfer: $tf


Tertanda

Admin Serba Ceban
";

            $fileUrl = asset('/').$data['bukti_transfer'];

            $response = json_decode($this->waService->sendMessageWithFile($message, $phone, $fileUrl), true);

            if (isset($response['error'])) {
                return back()->with(['errorData' => $response['error'].': data whatsapp tidak valid gunakan format 628XXXX']);
            }

            Pembayaran::createOrFirst($data);


            DB::commit();
            return redirect(route('pembayaran.index'))->with(['success' => 'Data berhasil diperbarui']);
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with(['errorData' => $th->getMessage()]);
        }
    }

    public function accepted(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $data = Pembayaran::with(['mitra'])->find($id);

            if($data) {
                $data->update(['status_pembayaran' => 'Lunas']);
            } else {
                abort(404);
            }


            DB::commit();
            return back()->with(['success' => 'Pembayaran dikonfirmasi']);
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with(['errorData' => $th->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function __rules(Request $request)
    {
        $message = [
            'bukti_bayar.required' => 'Bukti tidak boleh kosong',
            'bukti_bayar.file' => 'Bukti tidak valid',
            'bukti_bayar.mimes' => 'File bukti yang diizinkan adalah jpg, jpeg dan png',
            'bukti_bayar.max' => 'Bukti maksimal 2 MB',
        ];

        return $request->validate([
            'bukti_bayar' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048']
        ],$message);
    }
}
