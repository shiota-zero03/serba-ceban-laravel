<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Pemesanan extends Model
{
    use HasFactory;
    protected $table = 'pemesanans';
    protected $primaryKey = 'id';
    protected $fillable = [
        'kode_pemesanan',
        'mitra_id',
        'nama_mitra',
        'tanggal_pemesanan',
        'tanggal_penerimaan',
        'type',
        'is_broadcast',
        'is_accepted',
        'is_paid'
    ];

    public function mitra() {
        return $this->belongsTo(User::class, 'mitra_id', 'id');
    }

    public function detail() {
        return $this->hasMany(DetailPemesanan::class, 'pemesanan_id', 'id');
    }

    public function bayar() {
        return $this->hasOne(Pembayaran::class, 'pemesanan_id', 'id');
    }


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($pemesanan) {
            $pemesanan->kode_pemesanan = 'PM-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        });
    }
}
