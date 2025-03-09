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

class DataPembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $data = Pembayaran::where('mitra_id', auth()->user()->id)->with(['mitra'])->orderByDesc('id');

            if ($request->has('tanggal') && !empty($request->tanggal)) {
                $data->where('tanggal_pembayaran', $request->tanggal);
            }

            return DataTables::of($data->get())
                ->addIndexColumn()
                ->addColumn('transfer', function($row) {
                    return "Rp ".number_format($row['total_transfer'], 0, '.', '.');
                })
                ->addColumn('status', function($row) {
                    $status = "";
                    $color = "";

                    $status = $row['status_pembayaran'];
                    if($status == 'Pending') {
                        $color = 'text-warning';
                    } elseif($status == 'Lunas') {
                        $color = 'text-success';
                    } else {
                        $color = 'text-danger';
                    }

                    return "
                        <span class='$color'>$status</span>
                    ";
                })
                ->addColumn('tanggal_bayar', function($row) {
                    return $row['tanggal_pembayaran'];
                })
                ->addColumn('action', function($row){
                    $showButton = "<a href='".route('data-pembayaran.show', $row->id)."' class='btn btn-info py-1 rounded small btn-xs me-1'><i class='bx bx-show-alt'></i></a>";
                    return $showButton;
                })
                ->rawColumns(['transfer', 'status', 'tanggal_bayar', 'action'])
                ->make(true);
        }

        return view('Pages.DataPembayaran.index');
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
        $bayar = Pembayaran::where('mitra_id', auth()->user()->id)->with(['mitra'])->findOrFail($id);

        $totalTerjual = $bayar->produk_terjual;
        $biayaTerjual = $bayar->total_penjualan;
        $totalSisa = $bayar->produk_sisa;
        $biayaSisa = $bayar->total_potongan;
        $biayaPlastik = $bayar->biaya_plastik;
        $bukti = $bayar->bukti_transfer;
        $status = $bayar->status_pembayaran;

        $biaya = [
            'totalTerjual' => $totalTerjual,
            'biayaTerjual' => $biayaTerjual,
            'totalSisa' => $totalSisa,
            'biayaSisa' => $biayaSisa,
            'biayaPlastik' => $biayaPlastik,
            'bukti' => $bukti,
            'status' => $status
        ];

        return view('Pages.DataPembayaran.view', compact(['bayar', 'biaya']));
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
        try {
            DB::beginTransaction();

            $data = Pembayaran::with(['mitra'])->find($id);

            if($data) {
                $data->update(['status_pembayaran' => 'Lunas']);
            } else {
                abort(404);
            }


            DB::commit();
            return redirect(route('data-pembayaran.index'))->with(['success' => 'Data berhasil diperbarui']);
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
}
