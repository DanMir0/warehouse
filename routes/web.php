<?php

use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitsOfMeasurementsControllers;


Route::redirect('/', '/products')->name('products');
Route::get( '/api/products', [ProductsController::class, 'getProducts'])->name('getProducts');
Route::get( '/api/product/{id}', [ProductsController::class, 'getProduct'])->name('getProduct');
Route::resource( '/products', ProductsController::class);
Route::get('/api/units', [UnitsOfMeasurementsControllers::class, 'getUnits'])->name('getUnits');


Route::get('/{any}', function () {
    return view('/products'); // Замените 'app' на ваше основное представление Vue
})->where('any', '.*');


