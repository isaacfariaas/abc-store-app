<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
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
    return ['Laravel' => app()->version()];
});
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/products', [ProductController::class, 'index'])
        ->name('products');
    Route::get('/categories', [CategoryController::class, 'index'])
        ->name('categories');
    Route::resource('sales', SaleController::class)->except(['create', 'edit']);
    Route::put('/sales/{id}/addProduct' , [SaleController::class, 'addProduct'])
        ->name('sales_add_product');
    Route::delete('/sales/{id}/removeProduct' , [SaleController::class, 'removeProduct'])
        ->name('sales_remove_product');
});

require __DIR__ . '/auth.php';
