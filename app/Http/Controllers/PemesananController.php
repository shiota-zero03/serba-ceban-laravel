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

use App\Services\WhatsappService;

class PemesananController extends Controller
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
            $data = Pemesanan::with(['mitra', 'detail'])->whereNotNull('mitra_id')->orderByDesc('id');

            if ($request->has('tanggal') && !empty($request->tanggal)) {
                $data->where('tanggal_pemesanan', $request->tanggal);
            }

            return DataTables::of($data->get())
                ->addIndexColumn()
                ->addColumn('banyak_produk', function($row) {
                    return count($row->detail);
                })
                ->addColumn('broadcast', function($row) {
                    $url = route('pemesanan.index') . "/broadcast/" . $row->id;

                    return Carbon::parse($row->tanggal_pemesanan)->gte(Carbon::today('Asia/Jakarta'))
                        ? "<button onclick=\"window.location.href='$url'\" class='btn btn-primary btn-xs rounded py-1'>Broadcast Now</button>"
                        : "<button class='btn btn-primary btn-xs rounded py-1' disabled>Broadcast Now</button>";
                })
                ->addColumn('action', function($row){
                    $isTodayOrFuture = Carbon::parse($row->tanggal_pemesanan)->gte(Carbon::today('Asia/Jakarta'));

                    $showButton = "<a href='".route('pemesanan.show', $row->id)."' class='btn btn-info py-1 rounded small btn-xs me-1'><i class='bx bx-show-alt'></i></a>";

                    $editButton = $row->is_broadcast
                        ? "<button disabled class='btn btn-warning py-1 rounded small btn-xs me-1'><i class='bx bx-edit-alt'></i></button>"
                        : "<a href='".route('pemesanan.edit', $row->id)."' class='btn btn-warning py-1 rounded small btn-xs me-1'><i class='bx bx-edit-alt'></i></a>";

                    $deleteButton = $row->is_broadcast
                        ? "<button disabled class='btn btn-danger py-1 rounded small btn-xs me-1'><i class='bx bx-trash'></i></button>"
                        : "<a href='#' onclick='deleteData($row->id)' class='btn btn-danger py-1 rounded small btn-xs me-1'><i class='bx bx-trash'></i></a>";

                    if ($isTodayOrFuture) {
                        return $showButton . $editButton . $deleteButton;
                    }

                    return $showButton .
                        "<button disabled class='btn btn-warning py-1 rounded small btn-xs me-1'><i class='bx bx-edit-alt'></i></button>" .
                        "<button disabled class='btn btn-danger py-1 rounded small btn-xs me-1'><i class='bx bx-trash'></i></button>";
                })
                ->rawColumns(['banyak_produk', 'broadcast', 'action'])
                ->make(true);
        }

        return view('Pages.Pemesanan.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $mitra = User::where('role', 'MITRA')->get();
        return view('Pages.Pemesanan.create', compact(['mitra']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->__rules($request, 'create');
        try {
            DB::beginTransaction();

            $mitra = User::find($request->mitra_id);

            $data = [
                'mitra_id' => $mitra->id,
                'nama_mitra' => $mitra->name,
                'tanggal_pemesanan' => Carbon::today('Asia/Jakarta')->toDateString(),
                'tanggal_penerimaan' => Carbon::today('Asia/Jakarta')->addDay()->toDateString(),
                'type' => 'Pemesanan',
                'is_broadcast' => false,
                'is_accepted' => false
            ];

            $create = Pemesanan::createOrFirst($data);
            $dataToInsert = [];
            foreach ($request->produk_id as $value) {
                $product = Product::find($value);
                $dataToInsert[] = [
                    'pemesanan_id' => $create->id,
                    'produk_id' => $product->id,
                    'nama_produk' => $product->nama_produk,
                    'jumlah_pesan' => $request->jumlah[$value],
                    'kode_produk' => $product->kode_produk
                ];
            }

            DetailPemesanan::insert($dataToInsert);

            DB::commit();
            return redirect(route('pemesanan.index'))->with(['success' => 'Data berhasil ditambahkan']);
        } catch (\Throwable $th) {
            DB::rollback();
            return back()->with(['errorData' => $th->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        if($request->ajax()) {
            $user = User::with(['produk'])->findOrFail($id);
            return response()->json(['message' => 'Data berhasil didapatkan', 'data' => $user]);
        }

        $dataPemesanan = Pemesanan::with(['mitra', 'detail'])->findOrFail($id);
        return view('Pages.Pemesanan.view', compact(['dataPemesanan']));
    }

    public function broadcast (Request $request, string $id) {
        $dataPemesanan = Pemesanan::with(['mitra', 'detail'])->findOrFail($id);

        $phone = $dataPemesanan['mitra']['phone_number'];

        $name = $dataPemesanan['mitra']['name'];

        $textproduct = '';
        foreach ($dataPemesanan['detail'] as $key => $value) {
            $productName = $value['nama_produk'];
            $qty = $value['jumlah_pesan'];
            $textproduct .= "- $productName : $qty \n";
        }

$message = "
Kepada Yth. $name
Berikut informasi mengenai request produk untuk dikirimkan.
$textproduct
Tertanda

Admin Serba Ceban
";

        $response = json_decode($this->waService->sendMessage($message, $phone), true);

        if (isset($response['error'])) {
            return back()->with(['errorData' => $response['error'].': data whatsapp tidak valid gunakan format 628XXXX']);
        }

        Pemesanan::find($id)->update(['is_broadcast' => true]);

        return back()->with(['success' => 'Broadcast berhasil dikirim']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = Pemesanan::with(['mitra', 'detail'])->findOrFail($id);
        $mitra = User::where('role', 'MITRA')->get();
        return view('Pages.Pemesanan.edit', compact(['mitra', 'data']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->__rules($request, 'update');
        try {
            DB::beginTransaction();

            $mitra = User::find($request->mitra_id);

            $data = [
                'mitra_id' => $mitra->id,
                'nama_mitra' => $mitra->name,
                'tanggal_pemesanan' => Carbon::today('Asia/Jakarta')->toDateString(),
                'tanggal_penerimaan' => Carbon::today('Asia/Jakarta')->addDay()->toDateString(),
                'type' => 'Pemesanan',
            ];

            Pemesanan::find($id)->update($data);

            DetailPemesanan::where('pemesanan_id', $id)->delete();

            $dataToInsert = [];
            foreach ($request->produk_id as $value) {
                $product = Product::find($value);
                $dataToInsert[] = [
                    'pemesanan_id' => $id,
                    'produk_id' => $product->id,
                    'nama_produk' => $product->nama_produk,
                    'jumlah_pesan' => $request->jumlah[$value],
                    'kode_produk' => $product->kode_produk
                ];
            }

            DetailPemesanan::insert($dataToInsert);

            DB::commit();
            return redirect(route('pemesanan.index'))->with(['success' => 'Data berhasil diperbarui']);
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
            Pemesanan::find($id)->delete();
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
            'mitra_id.required' => 'Mitra tidak boleh kosong',
            'mitra_id.exist' => 'Mitra tidak ditemukan',
        ];

        if($type == 'create') {
            return $request->validate([
                'mitra_id' => ['required', 'exists:users,id'],
                'produk_id' => 'required|array',
                'produk_id.*' => 'exists:products,id',
                'jumlah' => 'array',
                'jumlah.*' => [
                    'nullable',
                    'numeric',
                    'min:1',
                    Rule::requiredIf(function () use ($request) {
                        return isset($request->produk_id);
                    })
                ]
            ],$message);
        } elseif($type == 'update') {
            return $request->validate([
                'mitra_id' => ['required', 'exists:users,id'],
                'produk_id' => 'required|array',
                'produk_id.*' => 'exists:products,id',
                'jumlah' => 'array',
                'jumlah.*' => [
                    'nullable',
                    'numeric',
                    'min:1',
                    Rule::requiredIf(function () use ($request) {
                        return isset($request->produk_id);
                    })
                ]
            ],$message);
        }
    }
}
