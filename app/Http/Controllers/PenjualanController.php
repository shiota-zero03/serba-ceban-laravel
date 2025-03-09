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

class PenjualanController extends Controller
{
    public function __construct()
    {
        $this->middleware('just-admin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            $data = Pemesanan::with(['mitra', 'detail'])->whereNotNull('mitra_id')->where('is_accepted', true)->orderByDesc('id');

            if ($request->has('tanggal') && !empty($request->tanggal)) {
                $data->where('tanggal_penerimaan', $request->tanggal);
            }

            return DataTables::of($data->get())
                ->addIndexColumn()
                ->addColumn('banyak_produk', function($row) {
                    return count($row->detail);
                })

                ->addColumn('action', function($row){
                    $showButton = "<a href='".route('penjualan.show', $row->id)."' class='btn btn-info py-1 rounded small btn-xs me-1'><i class='bx bx-show-alt'></i></a>";
                    return $showButton;
                })
                ->rawColumns(['banyak_produk', 'action'])
                ->make(true);
        }

        return view('Pages.Penjualan.index');
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
        return view('Pages.Penjualan.view', compact(['dataPemesanan']));
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

            $pesanan = Pemesanan::with(['detail'])->find($id);

            foreach ($pesanan->detail as $value) {
                $detail = DetailPemesanan::find($value->id);
                $dataToInsert = [
                    'jumlah_terjual' => $request->jumlah[$detail->id],
                ];
                DetailPemesanan::find($detail->id)->update($dataToInsert);
            }


            DB::commit();
            return redirect(route('penjualan.index'))->with(['success' => 'Data berhasil diperbarui']);
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

    public function accepted(string $id)
    {
        try {
            DB::beginTransaction();
            Pemesanan::find($id)->update(['is_accepted' => true]);
            DB::commit();
            return response()->json(['message' => 'Data berhasil diterima']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['responseJSON' => ['message' => $th->getMessage()] ], 500);
        }
    }
}
