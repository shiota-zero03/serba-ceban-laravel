<?php

use App\Http\Controllers\PenerimaanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::controller(\App\Http\Controllers\AuthController::class)->group(function(){
    Route::get('/', 'login')->name('login');
    Route::post('/', 'login_process')->name('login.process');
    Route::get('/register', 'register')->name('register');
});

Route::middleware('auth')->group(function(){
    Route::get('/logout', [\App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('/mitra', \App\Http\Controllers\MitraController::class);
    Route::resource('/produk', \App\Http\Controllers\ProductController::class);
    Route::resource('/pemesanan', \App\Http\Controllers\PemesananController::class);
    Route::resource('/penerimaan', \App\Http\Controllers\PenerimaanController::class);
    Route::resource('/penjualan', \App\Http\Controllers\PenjualanController::class);
    Route::resource('/pembayaran', \App\Http\Controllers\PembayaranController::class);
    Route::resource('/laporan', \App\Http\Controllers\LaporanController::class);
    Route::resource('/pesan-terima', \App\Http\Controllers\PesanTerimaController::class);
    Route::resource('/data-pembayaran', \App\Http\Controllers\DataPembayaranController::class);
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index'])->name('profile.index');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'store'])->name('profile.store');
});
