<?php

use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitsOfMeasurementsController;
use \App\Http\Controllers\OrderStatusController;

Route::redirect('/', '/products')->name('products');
Route::get('/api/products', [ProductsController::class, 'getProducts'])->name('getProducts');
Route::get('/api/product/{id}', [ProductsController::class, 'getProduct'])->name('getProduct');
Route::resource('/products', ProductsController::class);

Route::get('/api/measuring_units', [UnitsOfMeasurementsController::class, 'getUnits'])->name('getUnits');
Route::get('/api/measuring_units/{id}', [UnitsOfMeasurementsController::class, 'getUnit'])->name('getUnit');
Route::resource('/measuring_units', UnitsOfMeasurementsController::class);

Route::get('/api/order_statuses', [OrderStatusController::class, 'getOrderStatuses'])->name('getOrderStatuses');
Route::get('/api/order_statuses/{id}', [OrderStatusController::class, 'getOrderStatus'])->name('getOrderStatus');
Route::resource('/order_statuses', OrderStatusController::class);

Route::get('/{any}', function () {
    return view('index'); // Замените 'app' на ваше основное представление Vue
})->where('any', '.*');


