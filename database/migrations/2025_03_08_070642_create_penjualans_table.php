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
        Schema::create('penjualans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_penjualan')->unique();
            $table->bigInteger('penjualan_id')->unsigned();
            $table->foreign('penjualan_id')->references('id')->on('pemesanans')->onDelete('cascade');
            $table->bigInteger('produk_id')->unsigned();
            $table->foreign('produk_id')->references('id')->on('products')->onDelete('cascade');
            $table->integer('jumlah_terjual');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penjualans');
    }
};
