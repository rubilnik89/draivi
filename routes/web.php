<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', [\App\Http\Controllers\PriceController::class, 'index']);
Route::get('/load-table-data', [\App\Http\Controllers\PriceController::class, 'loadTableData'])->name('loadTableData');
Route::post('/prices/{id}', [\App\Http\Controllers\PriceController::class, 'update']);
Route::delete('/prices', [\App\Http\Controllers\PriceController::class, 'delete'])->name('delete');
Route::post('/fetch', [\App\Http\Controllers\PriceController::class, 'fetch'])->name('fetch');
Route::post('/update-order-amount', [\App\Http\Controllers\PriceController::class, 'updateOrderAmount'])->name('updateOrderAmount');
