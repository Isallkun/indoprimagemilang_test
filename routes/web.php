<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', [ProductController::class, 'index'])->name('home');
Route::post('/produk', [ProductController::class, 'store'])->name('produk.store');
Route::post('/update-stok', [ProductController::class, 'updateStock'])->name('stok.update');
Route::delete('/produk/{id}', [ProductController::class, 'destroy'])->name('produk.destroy');
Route::get('/history/{id}', [ProductController::class, 'history'])->name('produk.history');
