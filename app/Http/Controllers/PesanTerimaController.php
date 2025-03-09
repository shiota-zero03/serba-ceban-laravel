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

class PesanTerimaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $data = Pemesanan::with(['mitra', 'detail'])->where('mitra_id', auth()->user()->id)->orderByDesc('id');

            if ($request->has('tanggal') && !empty($request->tanggal)) {
                $data->where('tanggal_penerimaan', $request->tanggal)
                    ->orWhere('tanggal_pemesanan', $request->tanggal);
            }

            return DataTables::of($data->get())
                ->addIndexColumn()
                ->addColumn('banyak_produk', function($row) {
                    return count($row->detail);
                })
                ->addColumn('accepted', function($row) {
                    if($row['is_accepted']){
                        return "
                            <div class='flex align-items-center gap-2'>
                                <span class='text-success'><em>Sudah Diterima</em></span>
                            </div>
                        ";
                    } else {
                        return "
                            <div class='d-flex align-items-center gap-3'>
                                <div class='text-danger'><em>Belum Diterima</em></div>
                            </div>
                        ";
                    }
                })
                ->addColumn('action', function($row){
                    $showButton = "<a href='".route('pesan-terima.show', $row->id)."' class='btn btn-info py-1 rounded small btn-xs me-1'><i class='bx bx-show-alt'></i></a>";
                    return $showButton;
                })
                ->rawColumns(['banyak_produk', 'accepted', 'action'])
                ->make(true);
        }

        return view('Pages.PesanTerima.index');
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
        $dataPemesanan = Pemesanan::with(['mitra', 'detail'])->findOrFail($id);
        return view('Pages.PesanTerima.view', compact(['dataPemesanan']));
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
