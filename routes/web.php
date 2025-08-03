<?php

use App\Http\Controllers\CryptoPriceController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/prices', [App\Http\Controllers\CryptoPriceController::class, 'index'])->name('prices');
    // Route::get('/orders', [App\Http\Controllers\OrderController::class, 'index'])->name('orders');
    Route::resource('/orders', App\Http\Controllers\OrderController::class);

    Route::get('/test', [
        App\Http\Controllers\CryptoPriceController::class,
        'test'
    ]);

    Route::get('/crypto/data', [CryptoPriceController::class, 'getChartData']);

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
