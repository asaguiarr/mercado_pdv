<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PdvController;

// ------------------------
// AUTHENTICATION
// ------------------------
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// ------------------------
// PROTECTED ROUTES
// ------------------------
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Products
    Route::resource('products', ProductController::class);

    // Customers
    Route::resource('customers', CustomerController::class)->parameters([
        'customers' => 'id',
    ]);

    // Orders
    Route::resource('orders', OrderController::class);
    Route::post('orders/{order}/move', [OrderController::class, 'move'])->name('orders.move');

    // ------------------------
    // PDV (Ponto de Venda)
    // ------------------------
    Route::prefix('pdv')->name('pdv.')->group(function () {
        // Página principal de vendas
        Route::get('/', [PdvController::class, 'index'])->name('sales');

        // Criar nova venda (formulário)
        Route::get('create', [PdvController::class, 'create'])->name('create');

        // Processar venda
        Route::post('sale', [PdvController::class, 'processSale'])->name('sale');

        // Produtos para o PDV (AJAX)
        Route::get('products', [PdvController::class, 'getProducts'])->name('products');
        Route::get('product/{id}', [PdvController::class, 'getProduct'])->name('product.show');
    });
});

// ------------------------
// FALLBACK (404)
// ------------------------
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});