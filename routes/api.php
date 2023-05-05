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
Route::post('/get_barangall', [App\Http\Controllers\Trans_controller::class, 'get_barangall']);
Route::post('/status_barang', [App\Http\Controllers\Trans_controller::class, 'status_barang']);
Route::post('/hapus_barang', [App\Http\Controllers\Trans_controller::class, 'hapus_barang']);
Route::post('/add_barang', [App\Http\Controllers\Trans_controller::class, 'add_barang']);
Route::post('/edit_barang', [App\Http\Controllers\Trans_controller::class, 'edit_barang']);
Route::post('/add_stok', [App\Http\Controllers\Trans_controller::class, 'add_stok']);
Route::post('/remove_stok', [App\Http\Controllers\Trans_controller::class, 'remove_stok']);
Route::post('/detail_penjualan', [App\Http\Controllers\Trans_controller::class, 'detail_penjualan']);
