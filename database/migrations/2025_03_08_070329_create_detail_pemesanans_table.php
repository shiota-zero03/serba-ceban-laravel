<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_pemesanans', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('pemesanan_id')->unsigned();
            $table->foreign('pemesanan_id')->references('id')->on('pemesanans')->onDelete('cascade');
            $table->bigInteger('produk_id')->unsigned()->nullable();
            $table->foreign('produk_id')->references('id')->on('products')->onDelete('set null');
            $table->string('nama_produk');
            $table->integer('jumlah_pesan');
            $table->integer('jumlah_terjual')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pemesanans');
    }
};
