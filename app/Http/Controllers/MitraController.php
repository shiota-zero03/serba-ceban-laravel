<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MitraController extends Controller
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
            $data = User::where('role', 'MITRA')->orderByDesc('id')->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('status', function($row) {
                    return $row->email_verified_at
                        ? '<span class="bg-success text-white small px-3 py-1 rounded">Aktif</span>'
                        : '<span class="bg-danger text-white small px-3 py-1 rounded">Tidak Aktif</span>';
                })
                ->addColumn('action', function($row){
                    return "<a href='".route('mitra.edit', $row->id)."' class='btn btn-warning py-1 rounded small btn-xs'><i class='bx bx-edit-alt'></i></a>
                            <a href='#' onclick='deleteData($row->id)' class='btn btn-danger py-1 rounded small btn-xs'><i class='bx bx-trash'></i></a>";
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('Pages.Mitra.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('Pages.Mitra.create');
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
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'MITRA',
                'phone_number' => $request->whatsapp,
                'email_verified_at' => $request->status == 'Aktif' ? now() : null,

                'nama_bank' => $request->bank_name,
                'nomor_rekening' => $request->rekening,
                'atas_nama' => $request->atas_nama
            ];

            User::createOrFirst($data);

            DB::commit();
            return redirect(route('mitra.index'))->with(['success' => 'Data berhasil ditambahkan']);
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with(['errorData' => $th->getMessage()]);
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
        $user = User::findOrFail($id);
        return view('Pages.Mitra.edit', compact(['user']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $this->__rules($request, 'update');
        try {
            DB::beginTransaction();

            if($user->email !== $request->email) {
                $checkEmail = User::where('email', $request->email)->first();
                if($checkEmail) return back()->with(['errorData' => 'Email sudah pernah digunakan']);
            }

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->whatsapp,
                'email_verified_at' => $request->status == 'Aktif' ? now() : null,

                'nama_bank' => $request->bank_name,
                'nomor_rekening' => $request->rekening,
                'atas_nama' => $request->atas_nama
            ];

            if($request->password) {
                $data['password'] = Hash::make($request->password);
            }

            User::find($id)->update($data);

            DB::commit();
            return redirect(route('mitra.index'))->with(['success' => 'Data berhasil diperbarui']);
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
        try {
            DB::beginTransaction();
            User::find($id)->delete();
            DB::commit();
            return response()->json(['message' => 'Data berhasil dihapus']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['responseJSON' => ['message' => $th->getMessage()] ], 500);
        }
    }

    public function __rules(Request $request, string $type)
    {
        $message = [
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah pernah digunakan',
            'email.max' => 'Email maksimal terdiri dari 255 karakter',
            'password.required' => 'Password tidak boleh kosong',
            'password.string' => 'Password tidak valid',
            'password.max' => 'Password maksimal terdiri dari 255 karakter',
            'name.required' => 'Nama mitra tidak boleh kosong',
            'name.string' => 'Nama mitra tidak valid',
            'name.max' => 'Nama mitra maksimal terdiri dari 255 karakter',
            'whatsapp.required' => 'Whatsapp tidak boleh kosong',
            'whatsapp.string' => 'Whatsapp tidak valid',
            'whatsapp.max' => 'Whatsapp maksimal terdiri dari 255 karakter',
            'status.required' => 'Status tidak boleh kosong',
            'status.string' => 'Status tidak valid',
            'status.max' => 'Status maksimal terdiri dari 255 karakter',
        ];

        if($type == 'create') {
            return $request->validate([
                'email' => ['required', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'whatsapp' => ['required', 'string', 'max:255'],
                'status' => ['required', 'string', 'max:255']
            ],$message);
        } elseif($type == 'update') {
            return $request->validate([
                'email' => ['required', 'email', 'max:255'],
                'name' => ['required', 'string', 'max:255'],
                'whatsapp' => ['required', 'string', 'max:255'],
                'status' => ['required', 'string', 'max:255']
            ],$message);
        }
    }
}
