<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('/user', UserController::class)->middleware('auth:sanctum');
Route::put('/user/act/pw', [UserController::class, 'updatePassword'])->middleware('auth:sanctum');
Route::post('/auth/register', [AuthController::class, 'createUser']);
Route::post('/auth/login', [AuthController::class, 'loginUser']);

Route::resource('/barang', BarangController::class)->middleware('auth:sanctum');
Route::post('/search/barang', [BarangController::class, 'searching'])->middleware('auth:sanctum');

Route::post('/transaksi/addcart', [TransaksiController::class, 'addCart'])->middleware('auth:sanctum');
Route::get('/transaksi/carts', [TransaksiController::class, 'carts'])->middleware('auth:sanctum');
Route::get('/transaksi/simpan', [TransaksiController::class, 'simpanTransaksi'])->middleware('auth:sanctum');

Route::post('/laporan/penjualan', [LaporanPenjualanController::class, 'laporanPenjualan'])->middleware('auth:sanctum');
