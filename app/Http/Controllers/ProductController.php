<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if($request->ajax()) {
            if(Auth::user()->role == 'MITRA') {
                $data = Product::with(['mitra'])->orderByDesc('id')->where('mitra_id', Auth::user()->id)->get();
            } else {
                $data = Product::with(['mitra'])->orderByDesc('id')->get();
            }

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('price', function($row) {
                    return "Rp ".number_format($row->harga, 0, ',', '.');
                })
                ->addColumn('action', function($row){
                    return "<a href='#' onclick='editData($row->id)' class='btn btn-warning py-1 rounded small btn-xs'><i class='bx bx-edit-alt'></i></a>
                            <a href='#' onclick='deleteData($row->id)' class='btn btn-danger py-1 rounded small btn-xs'><i class='bx bx-trash'></i></a>";
                })
                ->rawColumns(['price', 'action'])
                ->make(true);
        }

        $mitra = User::where('role', 'MITRA')->get();

        return view('Pages.Produk.index', compact(['mitra']));
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
        $this->__rules($request, 'create');
        try {
            DB::beginTransaction();

            $data = [
                'kode_produk' => $request->kode_produk,
                'nama_produk' => $request->nama_produk,
                'harga' => $request->harga,
                'mitra_id' => $request->mitra_id,
            ];

            Product::createOrFirst($data);

            DB::commit();
            return response()->json(['message' => 'Data berhasil ditambahkan']);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $Product = Product::findOrFail($id);
        return response()->json(['message' => 'Data berhasil didapatkan', 'data' => $Product]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $Product = Product::findOrFail($id);
        $this->__rules($request, 'update');
        try {
            DB::beginTransaction();

            if($Product->kode_produk !== $request->kode_produk) {
                $checkEmail = Product::where('kode_produk', $request->kode_produk)->first();
                if($checkEmail) return response()->json(['errorData' => 'Kode sudah pernah digunakan', 'errors' => ['kode_produk' => ['Kode sudah pernah digunakan']]], 422);
            }

            $data = [
                'kode_produk' => $request->kode_produk,
                'nama_produk' => $request->nama_produk,
                'harga' => $request->harga,
            ];

            Product::find($id)->update($data);

            DB::commit();
            return response()->json(['message' => 'Data berhasil diperbarui']);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();
            Product::find($id)->delete();
            DB::commit();
            return response()->json(['message' => 'Data berhasil dihapus']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['message' => $th->getMessage()], 500);
        }
    }

    public function __rules(Request $request, string $type)
    {
        $message = [
            'mitra_id.required' => 'Mitra tidak boleh kosong',
            'mitra_id.string' => 'Mitra tidak valid',
            'mitra_id.exist' => 'Mitra tidak terdaftar',
            'kode_produk.required' => 'Kode produk tidak boleh kosong',
            'kode_produk.string' => 'Kode produk tidak valid',
            'kode_produk.unique' => 'Kode produk sudah pernah digunakan',
            'kode_produk.max' => 'Kode produk maksimal terdiri dari 255 karakter',
            'nama_produk.required' => 'Nama produk tidak boleh kosong',
            'nama_produk.string' => 'Nama produk tidak valid',
            'nama_produk.max' => 'Nama produk maksimal terdiri dari 255 karakter',
            'harga.required' => 'Harga mitra tidak boleh kosong',
            'harga.string' => 'Harga mitra tidak valid',
            'harga.max' => 'Harga mitra maksimal terdiri dari 255 karakter',
        ];

        if($type == 'create') {
            return $request->validate([
                'mitra_id' => ['required', 'string', 'exists:users,id'],
                'kode_produk' => ['required', 'string', 'max:255', 'unique:products,kode_produk'],
                'nama_produk' => ['required', 'string', 'max:255'],
                'harga' => ['required', 'string', 'max:255']
            ],$message);
        } elseif($type == 'update') {
            return $request->validate([
                'kode_produk' => ['required', 'string', 'max:255'],
                'nama_produk' => ['required', 'string', 'max:255'],
                'harga' => ['required', 'string', 'max:255']
            ],$message);
        }
    }
}
