<?php

use App\Http\Controllers\TaiKhoanController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::group(['prefix' => 'api',], function () {
//     Route::get('/taikhoan', [TaiKhoanController::class, 'index'])->name('taikhoan.index');

//     Route::post('/login', [TaiKhoanController::class, 'login'])->name('taikhoan.login');
// });
