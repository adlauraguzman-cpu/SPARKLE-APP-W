<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\InventoryController as AdminInventoryController;
use App\Http\Controllers\Admin\LoyaltyController as AdminLoyaltyController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AromaGuideController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoyaltyController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

// --- RUTAS PÚBLICAS (Visitante sin registro & Cliente) ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::post('/suscribirse', [HomeController::class, 'suscribirse'])->name('suscribirse');

Route::get('/catalogo', [ProductController::class, 'index'])->name('products.index');
Route::get('/producto/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::post('/producto/{id}/aviso-stock', [ProductController::class, 'solicitarAvisoStock'])->name('products.aviso-stock');

Route::get('/guia-de-aromas', [AromaGuideController::class, 'index'])->name('guia.index');
Route::post('/guia-de-aromas/resultado', [AromaGuideController::class, 'resultado'])->name('guia.resultado');

Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrito/agregar', [CartController::class, 'store'])->name('cart.store');
Route::post('/carrito/actualizar/{id}', [CartController::class, 'update'])->name('cart.update');
Route::post('/carrito/eliminar/{id}', [CartController::class, 'destroy'])->name('cart.destroy');

// Autenticación & Acceso Rápido Demo
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/registro', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/demo-login/{rol}', [AuthController::class, 'quickLogin'])->name('demo.login');

// --- RUTAS CLIENTE REGISTRADO (Con cuenta) ---
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/procesar', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/confirmacion/{numeroPedido}', [CheckoutController::class, 'confirmacion'])->name('checkout.confirmacion');

    Route::get('/mis-pedidos', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/mis-pedidos/{numeroPedido}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/mis-pedidos/{numeroPedido}/solicitar-cambio', [OrderController::class, 'solicitarCambio'])->name('orders.cambio');
    Route::post('/mis-pedidos/{numeroPedido}/repetir', [OrderController::class, 'repetirPedido'])->name('orders.repetir');

    Route::get('/mis-puntos', [LoyaltyController::class, 'index'])->name('loyalty.index');
    Route::post('/mis-puntos/canjear/{id}', [LoyaltyController::class, 'canjear'])->name('loyalty.canjear');

    Route::post('/resenas', [ReviewController::class, 'store'])->name('reviews.store');
});

// --- RUTAS DE GESTIÓN INTERNA (Colaborador & Administrador) ---
Route::middleware(['auth', 'role:administrador,colaborador'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Inventario
    Route::get('/inventario', [AdminInventoryController::class, 'index'])->name('inventario.index');
    Route::post('/inventario/ajustar', [AdminInventoryController::class, 'ajustar'])->name('inventario.ajustar');

    // Pedidos
    Route::get('/pedidos', [AdminOrderController::class, 'index'])->name('pedidos.index');
    Route::get('/pedidos/{id}', [AdminOrderController::class, 'show'])->name('pedidos.show');
    Route::post('/pedidos/{id}/enviar', [AdminOrderController::class, 'registrarEnvio'])->name('pedidos.enviar');
    Route::post('/pedidos/{id}/entregar', [AdminOrderController::class, 'marcarEntregado'])->name('pedidos.entregar');
});

// --- RUTAS EXCLUSIVAS DE ADMINISTRADOR (Dueña de la marca) ---
Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/inventario/umbral/{id}', [AdminInventoryController::class, 'actualizarUmbral'])->name('inventario.umbral');
    Route::post('/pedidos/{id}/anular', [AdminOrderController::class, 'anular'])->name('pedidos.anular');

    Route::resource('productos', AdminProductController::class);

    Route::get('/fidelizacion', [AdminLoyaltyController::class, 'index'])->name('fidelizacion.index');
    Route::post('/fidelizacion/recompensas', [AdminLoyaltyController::class, 'store'])->name('fidelizacion.store');
    Route::post('/fidelizacion/recompensas/{id}/toggle', [AdminLoyaltyController::class, 'toggle'])->name('fidelizacion.toggle');

    Route::get('/resenas', [AdminReviewController::class, 'index'])->name('resenas.index');
    Route::post('/resenas/{id}/toggle', [AdminReviewController::class, 'toggleEstado'])->name('resenas.toggle');
    Route::delete('/resenas/{id}', [AdminReviewController::class, 'destroy'])->name('resenas.destroy');

    Route::get('/usuarios', [AdminUserController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [AdminUserController::class, 'store'])->name('usuarios.store');
    Route::post('/usuarios/{id}/rol', [AdminUserController::class, 'cambiarRol'])->name('usuarios.rol');
});
