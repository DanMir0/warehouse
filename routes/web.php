<?php

use App\Http\Controllers\ProductsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UnitsOfMeasurementsController;
use \App\Http\Controllers\OrderStatusController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\CounterpartiesController;
use App\Http\Controllers\TechCardController;
use App\Http\Controllers\TechCardProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderTechCardController;

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

Route::get('/api/document_types', [DocumentTypeController::class, 'getDocumentTypes'])->name('getDocumentTypes');
Route::get('/api/document_types/{id}', [DocumentTypeController::class, 'getDocumentType'])->name('getDocumentType');
Route::resource('/document_types', DocumentTypeController::class);

Route::get('/api/counterparties', [CounterpartiesController::class, 'getCounterparties'])->name('getCounterparties');
Route::get('/api/counterparties/{id}', [CounterpartiesController::class, 'getCounterparty'])->name('getCounterparty');
Route::resource('/counterparties', CounterpartiesController::class);

Route::get('/api/tech_cards', [TechCardController::class, 'getTechCards'])->name('getTechCards');
Route::get('/api/tech_cards/{id}', [TechCardController::class, 'getTechCard'])->name('getTechCard');
Route::get('/api/products_tech_cards', [TechCardController::class, 'getProductsTechCard'])->name('getProductsTechCard');
Route::resource('/tech_cards', TechCardController::class);

Route::get('/api/tech_card_products/{id}', [TechCardProductController::class, 'getTechCardProducts'])->name('getTechCardProducts');

Route::get('/api/orders', [OrderController::class, 'getOrders'])->name('getOrders');
Route::get('/api/orders/{id}', [OrderController::class, 'getOrder'])->name('getOrder');
Route::put('/api/orders/{id}', [OrderController::class, 'setStatus'])->name('setStatus');
Route::resource('/orders', OrderController::class);

Route::get('/api/order_tech_card/{id}', [OrderTechCardController::class, 'getOrderTechCard'])->name('getOrderTechCard');

Route::get('/{any}', function () {
    return view('index'); // Замените 'app' на ваше основное представление Vue
})->where('any', '.*');


