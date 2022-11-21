<?php

use App\Http\Controllers\AuthController;
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
    Route::get('/', [AuthController::class, 'index'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('auth.login');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/home', [AuthController::class, 'dashboard'])->name('home');
    Route::get('/getMessage/{id}', [AuthController::class, 'getMessage'])->name('getMessage');
    Route::post('/sendmessage', [AuthController::class, 'sendmessage'])->name('sendmessage');
    Route::post('/getRefreshMessage', [AuthController::class, 'getRefreshMessage'])->name('getRefreshMessage');
