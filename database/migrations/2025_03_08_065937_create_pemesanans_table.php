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
        Schema::create('pemesanans', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pemesanan')->unique();
            $table->bigInteger('mitra_id')->unsigned()->nullable();
            $table->foreign('mitra_id')->references('id')->on('users')->onDelete('set null');
            $table->string('nama_mitra')->nullable();
            $table->date('tanggal_pemesanan')->nullable();
            $table->enum('type', ['Pemesanan', 'Penerimaan']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanans');
    }
};
