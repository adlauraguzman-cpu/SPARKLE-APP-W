<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ConfiguradorController;
use App\Http\Controllers\DiarioController;
use App\Http\Controllers\EspecialistaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\RutinaController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SPARKLE · MI ESPACIO VITAMINA
|--------------------------------------------------------------------------
*/

// Rutas Públicas
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/aromas', [HomeController::class, 'aromas'])->name('aromas.index');

// Configurador (Acceso tanto para Invitado como para Usuario registrado)
Route::get('/configurador', [ConfiguradorController::class, 'index'])->name('configurador.index');
Route::post('/configurador/calcular', [ConfiguradorController::class, 'calcular'])->name('configurador.calcular');

// Autenticación y Demostración rápida de roles
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/demo-login/{role}', [AuthController::class, 'quickLogin'])->name('demo.login');

// Rutas Protegidas para Usuarios Autenticados
Route::middleware(['auth'])->group(function () {
    // Guardar fórmula generada
    Route::post('/configurador/guardar', [ConfiguradorController::class, 'guardar'])->name('configurador.guardar');

    // Onboarding sensorial y de seguridad
    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

    // Rutinas y Reproductor de Ritual Sensorial
    Route::get('/rutinas', [RutinaController::class, 'index'])->name('rutinas.index');
    Route::get('/rutinas/crear', [RutinaController::class, 'create'])->name('rutinas.create');
    Route::post('/rutinas', [RutinaController::class, 'store'])->name('rutinas.store');
    Route::post('/rutinas/{rutina}/toggle', [RutinaController::class, 'toggle'])->name('rutinas.toggle');
    Route::post('/rutinas/{rutina}/simular-recordatorio', [RutinaController::class, 'simularRecordatorio'])->name('rutinas.simular');
    Route::get('/rutinas/{rutina}/ritual', [RutinaController::class, 'ritual'])->name('rutinas.ritual');

    // Diario de Experiencias y Seguimiento de Racha
    Route::get('/diario', [DiarioController::class, 'index'])->name('diario.index');
    Route::post('/diario', [DiarioController::class, 'store'])->name('diario.store');
});

// Rutas Especialista en Aromas (Maker-Checker, reglas de afinidad, métricas agregadas)
Route::middleware(['auth', 'role:especialista'])->prefix('especialista')->name('especialista.')->group(function () {
    Route::get('/', [EspecialistaController::class, 'index'])->name('index');
    Route::post('/reglas', [EspecialistaController::class, 'storeRegla'])->name('reglas.store');
    Route::post('/reglas/{regla}/aprobar', [EspecialistaController::class, 'aprobarRegla'])->name('reglas.aprobar');
    Route::post('/simular', [EspecialistaController::class, 'simular'])->name('simular');
});

// Rutas Administrador (Métricas globales, gobernanza de roles, ARCO/GDPR, auditoría. Sin acceso a diarios privados)
Route::middleware(['auth', 'role:administrador'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::post('/usuarios/{usuario}', [AdminController::class, 'updateUsuario'])->name('usuarios.update');
    Route::get('/consentimientos', [AdminController::class, 'consentimientos'])->name('consentimientos');
    Route::get('/usuarios/{usuario}/exportar', [AdminController::class, 'exportarDatos'])->name('usuarios.exportar');
});
