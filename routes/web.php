<?php

use App\Http\Controllers\AreaComunController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartamentoController;
use App\Http\Controllers\ExpensaController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ResidenteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - CondominioApp
|--------------------------------------------------------------------------
*/

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Authenticated Routes (All Active Roles)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
    Route::post('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Expensas (Resident can view & pay their own, Admin sees all)
    Route::get('/expensas', [ExpensaController::class, 'index'])->name('expensas.index');
    Route::post('/expensas/{expensa}/pagar', [ExpensaController::class, 'registrarPago'])->name('expensas.pagar');

    // Áreas Comunes (Resident and Admin can view)
    Route::get('/areas-comunes', [AreaComunController::class, 'index'])->name('areas-comunes.index');

    // Reservas (Shared access with internal authorization)
    Route::get('/reservas', [ReservaController::class, 'index'])->name('reservas.index');
    Route::get('/reservas/crear', [ReservaController::class, 'create'])->name('reservas.create');
    Route::post('/reservas', [ReservaController::class, 'store'])->name('reservas.store');
    Route::post('/reservas/{reserva}/cancelar', [ReservaController::class, 'cancelar'])->name('reservas.cancelar');

    // ==========================================
    // Admin Only Protected Routes
    // ==========================================
    Route::middleware('role:admin')->group(function () {
        // Departamentos CRUD
        Route::resource('departamentos', DepartamentoController::class)->except(['show']);

        // Residentes CRUD
        Route::resource('residentes', ResidenteController::class);

        // Expensas Admin Management
        Route::get('/expensas/crear', [ExpensaController::class, 'create'])->name('expensas.create');
        Route::post('/expensas', [ExpensaController::class, 'store'])->name('expensas.store');
        Route::get('/expensas/generar-masivo', [ExpensaController::class, 'generarMasivoForm'])->name('expensas.generar-masivo');
        Route::post('/expensas/generar-masivo', [ExpensaController::class, 'generarMasivo'])->name('expensas.generar-masivo.post');
        Route::post('/expensas/{expensa}/estado', [ExpensaController::class, 'cambiarEstado'])->name('expensas.estado');
        Route::delete('/expensas/{expensa}', [ExpensaController::class, 'destroy'])->name('expensas.destroy');

        // Áreas Comunes Admin Management
        Route::get('/areas-comunes/crear', [AreaComunController::class, 'create'])->name('areas-comunes.create');
        Route::post('/areas-comunes', [AreaComunController::class, 'store'])->name('areas-comunes.store');
        Route::get('/areas-comunes/{areaComun}/editar', [AreaComunController::class, 'edit'])->name('areas-comunes.edit');
        Route::put('/areas-comunes/{areaComun}', [AreaComunController::class, 'update'])->name('areas-comunes.update');
        Route::delete('/areas-comunes/{areaComun}', [AreaComunController::class, 'destroy'])->name('areas-comunes.destroy');

        // Reservas Admin Management
        Route::delete('/reservas/{reserva}', [ReservaController::class, 'destroy'])->name('reservas.destroy');

        // Gastos y Egresos de la Administración CRUD
        Route::resource('gastos', GastoController::class)->except(['show']);
    });
});
