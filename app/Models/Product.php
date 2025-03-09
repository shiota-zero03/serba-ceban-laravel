<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $fillable = [
        'kode_produk',
        'mitra_id',
        'nama_produk',
        'harga',
    ];

    public function mitra() {
        return $this->belongsTo(User::class, 'mitra_id', 'id');
    }
}
