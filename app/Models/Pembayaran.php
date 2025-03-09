<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pembayaran extends Model
{
    use HasFactory;
    protected $table = 'pembayarans';
    protected $primaryKey = 'id';
    protected $fillable = [
        'kode_pembayaran',
        'mitra_id',
        'pemesanan_id',
        'tanggal_pembayaran',
        'total_penjualan',
        'total_potongan',
        'total_transfer',
        'bukti_transfer',
        'status_pembayaran',
        'biaya_plastik',
        'nomor_rekening_penerima',
        'nama_penerima',
        'produk_terjual',
        'produk_sisa'
    ];

    public function mitra() {
        return $this->belongsTo(User::class, 'mitra_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pemesanan) {
            $pemesanan->kode_pembayaran = 'PB-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        });
    }
}
