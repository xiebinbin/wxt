<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home/index', [HomeController::class, 'index'])->name('home');
Route::get('/customer', [HomeController::class, 'customer'])->name('customer');

Route::get('/orders/index', [OrderController::class,'index'])->name('orders.index');
Route::get('/products/{productCode}', [ProductController::class,'show'])->name('products.show');

