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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pembayaran')->unique();
            $table->bigInteger('mitra_id')->unsigned()->nullable();
            $table->foreign('mitra_id')->references('id')->on('users')->onDelete('set null');
            $table->date('tanggal_pembayaran');
            $table->double('total_penjualan');
            $table->double('total_potongan');
            $table->double('total_transfer');
            $table->string('bukti_transfer');
            $table->enum('status_pembayaran', ['Belum Diproses', 'Pending', 'Lunas', 'Gagal']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
