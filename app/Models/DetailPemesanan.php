<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPemesanan extends Model
{
    use HasFactory;
    protected $table = 'detail_pemesanans';
    protected $primaryKey = 'id';
    protected $fillable = [
        'pemesanan_id',
        'produk_id',
        'nama_produk',
        'jumlah_pesan',
        'jumlah_terjual',
        'jumlah_terima',
        'kode_produk'
    ];
}
