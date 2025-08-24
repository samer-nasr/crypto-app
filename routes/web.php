<?php

use App\Http\Controllers\CryptoDataController;
use App\Http\Controllers\CryptoPriceController;
use App\Http\Controllers\ModelTrainController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrainController;
use App\Jobs\LabelDataJob;
use App\Models\Symbol;
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
    Route::resource('/models', App\Http\Controllers\ModelTrainController::class);
    Route::resource('/trains', App\Http\Controllers\TrainController::class);
    Route::resource('/labels', App\Http\Controllers\DataLabelController::class);

    Route::get('/update_crypto_data', [
        CryptoDataController::class, 'update_crypto_data'
    ]);

    Route::post('/predict' , [PredictionController::class, 'predict'])->name('predict');
    Route::get('/train' , [ModelTrainController::class, 'train']);

   

    Route::get('/test' , [PredictionController::class, 'test_prediction']);
    Route::get('/backtest' , [TrainController::class, 'backtest']);

     Route::get('/config' , function () {
        LabelDataJob::dispatch(0.010);
        dd('done');
     });




    Route::get('/crypto/data', [CryptoPriceController::class, 'getChartData']);

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
