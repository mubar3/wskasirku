<?php

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


Route::post('/get_data', [App\Http\Controllers\Auth_controller::class, 'get_data']);
Route::post('/ubah_data', [App\Http\Controllers\Auth_controller::class, 'ubah_data']);
Route::post('/register', [App\Http\Controllers\Auth_controller::class, 'register']);
Route::post('/login', [App\Http\Controllers\Auth_controller::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth_controller::class, 'logout']);

Route::post('/get_barang', [App\Http\Controllers\Trans_controller::class, 'get_barang']);
Route::post('/get_barang_new', [App\Http\Controllers\Trans_controller::class, 'get_barang_new']);
Route::post('/get_barangall', [App\Http\Controllers\Trans_controller::class, 'get_barangall']);
Route::post('/status_barang', [App\Http\Controllers\Trans_controller::class, 'status_barang']);
Route::post('/hapus_barang', [App\Http\Controllers\Trans_controller::class, 'hapus_barang']);
Route::post('/add_barang', [App\Http\Controllers\Trans_controller::class, 'add_barang']);
Route::post('/edit_barang', [App\Http\Controllers\Trans_controller::class, 'edit_barang']);
Route::post('/add_stok', [App\Http\Controllers\Trans_controller::class, 'add_stok']);
Route::post('/remove_stok', [App\Http\Controllers\Trans_controller::class, 'remove_stok']);
Route::post('/detail_penjualan', [App\Http\Controllers\Trans_controller::class, 'detail_penjualan']);
Route::post('/detail_penjualan_new', [App\Http\Controllers\Trans_controller::class, 'detail_penjualan_new']);
Route::post('/pembelian', [App\Http\Controllers\Trans_controller::class, 'pembelian']);
Route::post('/get_transaksi', [App\Http\Controllers\Trans_controller::class, 'get_transaksi']);

Route::post('/add_absensi', [App\Http\Controllers\Absen_controller::class, 'add_absensi']);
Route::post('/get_absen', [App\Http\Controllers\Absen_controller::class, 'get_absen']);
Route::post('/get_absen_all', [App\Http\Controllers\Absen_controller::class, 'get_absen_all']);

Route::post('/report', [App\Http\Controllers\Report_controller::class, 'report']);
Route::post('/save_report', [App\Http\Controllers\Report_controller::class, 'save_report']);
Route::post('/get_report', [App\Http\Controllers\Report_controller::class, 'get_report']);
Route::post('/del_report', [App\Http\Controllers\Report_controller::class, 'del_report']);
Route::post('/endis_gaji', [App\Http\Controllers\Report_controller::class, 'endis_gaji']);

Route::post('/update_bahan', [App\Http\Controllers\Stok_controller::class, 'update_bahan']);
Route::post('/add_bahan', [App\Http\Controllers\Stok_controller::class, 'add_bahan']);
Route::post('/get_bahan', [App\Http\Controllers\Stok_controller::class, 'get_bahan']);