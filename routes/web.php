<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PdvController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Redireciona a raiz para a tela de login
Route::get('/', function () {
    return redirect()->route('login');
});

use Illuminate\Support\Facades\Password;

// Autenticação
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset Routes
Route::get('password/reset', function () {
    return view('auth.passwords.email');
})->name('password.request');

Route::post('password/email', function (\Illuminate\Http\Request $request) {
    $request->validate(['email' => 'required|email']);
    $status = Password::sendResetLink(
        $request->only('email')
    );
    return $status === Password::RESET_LINK_SENT
                ? back()->with(['status' => __($status)])
                : back()->withErrors(['email' => __($status)]);
})->name('password.email');

Route::get('password/reset/{token}', function ($token) {
    return view('auth.passwords.reset', ['token' => $token]);
})->name('password.reset');

Route::post('password/reset', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|confirmed|min:8',
    ]);
    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => bcrypt($password)
            ])->save();
        }
    );
    return $status === Password::PASSWORD_RESET
                ? redirect()->route('login')->with('status', __($status))
                : back()->withErrors(['email' => [__($status)]]);
})->name('password.update');



// Dashboard
Route::middleware('auth')->get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Produtos
Route::middleware('auth')->resource('products', ProductController::class);

// Clientes
Route::middleware('auth')->resource('customers', CustomerController::class);
Route::middleware('auth')->get('customers/{id}/edit-contact', [CustomerController::class, 'editContact'])->name('customers.edit-contact');
Route::middleware('auth')->put('customers/{id}/update-contact', [CustomerController::class, 'updateContact'])->name('customers.update-contact');

// Pedidos
Route::middleware('auth')->get('orders', [OrderController::class, 'index'])->name('orders.index');

// Administração
Route::middleware(['auth', 'role:admin,super_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.users.index'))->name('dashboard');
    Route::resource('users', AdminUserController::class)->names([
        'index'   => 'users.index',
        'create'  => 'users.create',
        'store'   => 'users.store',
        'edit'    => 'users.edit',
        'update'  => 'users.update',
        'destroy' => 'users.destroy',
    ]);
});

// PDV
Route::middleware(['auth', 'role:super_admin,admin,cashier'])->prefix('pdv')->name('pdv.')->group(function () {
    Route::get('/', [PdvController::class, 'index'])->name('sales');
    Route::get('create', [PdvController::class, 'create'])->name('sales.create');
    Route::post('sale', [PdvController::class, 'processSale'])->name('sales.store');
    Route::get('product/{id}', [PdvController::class, 'getProduct'])->name('product.show');
    Route::get('{id}', [PdvController::class, 'show'])->name('sales.show');
    // ...
});

// Estoque
Route::middleware(['auth', 'role:estoquista,admin,super_admin'])->prefix('estoque')->name('estoque.')->group(function () {
    Route::get('/', [StockController::class, 'index'])->name('index');
    Route::get('/entrada', [StockController::class, 'entrada'])->name('entrada');
    Route::post('/entrada', [StockController::class, 'storeEntrada'])->name('entrada.store');
    Route::get('/saida', [StockController::class, 'saida'])->name('saida');
    Route::post('/saida', [StockController::class, 'storeSaida'])->name('saida.store');
    Route::get('/relatorio', [StockController::class, 'relatorio'])->name('relatorio');
});

// Rota fallback para página 404
Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
