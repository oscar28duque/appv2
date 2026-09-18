<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\ReservationController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PublicCatalogController;
use App\Models\News;
use Illuminate\Support\Facades\Route;

// ---------- Rutas Públicas (Frontend Comercial) ----------

// Catálogo de alquiler de equipos y cotizador (Requerimiento 3.2 y 3.4)
Route::get('/', [PublicCatalogController::class, 'index'])->name('home');
Route::get('/equipos/{code}', [PublicCatalogController::class, 'show'])->name('items.show');

// Detalle de noticias (Guía Reto B)
Route::get('/noticias/{slug}', function (string $slug) {
    $noticia = News::with('media')
        ->where('slug', $slug)
        ->published()
        ->firstOrFail();

    return view('news.show', compact('noticia'));
})->name('news.show');

// Sistema de correos / Contacto y cotizaciones (Guía Reto C)
Route::get('/contacto', [ContactController::class, 'show'])->name('contact.index');
Route::post('/contacto', [ContactController::class, 'send'])->name('contact.send');

// ---------- Autenticación (guest) ----------

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');

    Route::get('/register', [LoginController::class, 'createRegister'])->name('register');
    Route::post('/register', [LoginController::class, 'register'])->name('register.store');
});

// ---------- Panel Administrativo / CMS (auth) ----------

Route::middleware('auth')->group(function () {
    // 3.1 Dashboard General con KPIs en tiempo real de eventos
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // 3.2 Gestión de Inventario de Equipos
    Route::prefix('dashboard/items')->name('admin.items.')->group(function () {
        Route::get('/', [ItemController::class, 'index'])->name('index');
        Route::get('/crear', [ItemController::class, 'create'])->name('create');
        Route::post('/', [ItemController::class, 'store'])->name('store');
        Route::get('/{item}', [ItemController::class, 'show'])->name('show');
        Route::get('/{item}/editar', [ItemController::class, 'edit'])->name('edit');
        Route::put('/{item}', [ItemController::class, 'update'])->name('update');
        Route::delete('/{item}', [ItemController::class, 'destroy'])->name('destroy');
        Route::post('/{item}/mantenimiento', [ItemController::class, 'storeMaintenance'])->name('maintenance');
    });

    // 3.3 & 3.4 Gestión de Reservas, Disponibilidad y Sobrecupo
    Route::prefix('dashboard/reservas')->name('admin.reservations.')->group(function () {
        Route::get('/', [ReservationController::class, 'index'])->name('index');
        Route::get('/crear', [ReservationController::class, 'create'])->name('create');
        Route::post('/', [ReservationController::class, 'store'])->name('store');
        Route::get('/{reservation}', [ReservationController::class, 'show'])->name('show');
        Route::patch('/{reservation}/status', [ReservationController::class, 'updateStatus'])->name('status');
        Route::post('/{reservation}/abono', [ReservationController::class, 'storePayment'])->name('payment');
        Route::post('/{reservation}/evidencia', [ReservationController::class, 'uploadEvidence'])->name('evidence');
        Route::post('/{reservation}/devolucion', [ReservationController::class, 'storeReturn'])->name('return');
    });

    // 3.5 Directorio de Clientes
    Route::prefix('dashboard/clientes')->name('admin.clients.')->group(function () {
        Route::get('/', [ClientController::class, 'index'])->name('index');
        Route::get('/crear', [ClientController::class, 'create'])->name('create');
        Route::post('/', [ClientController::class, 'store'])->name('store');
        Route::get('/{client}', [ClientController::class, 'show'])->name('show');
        Route::get('/{client}/editar', [ClientController::class, 'edit'])->name('edit');
        Route::put('/{client}', [ClientController::class, 'update'])->name('update');
        Route::delete('/{client}', [ClientController::class, 'destroy'])->name('destroy');
    });

    // 3.7 & 3.8 Finanzas, Cuentas por Cobrar y Gastos Operativos
    Route::prefix('dashboard/finanzas')->name('admin.finances.')->group(function () {
        Route::get('/', [FinanceController::class, 'index'])->name('index');
        Route::post('/gastos', [FinanceController::class, 'storeExpense'])->name('expense.store');
        Route::delete('/gastos/{expense}', [FinanceController::class, 'destroyExpense'])->name('expense.destroy');
    });

    // 3.9 Personal Operativo y Bitácora de Turnos (Nómina)
    Route::prefix('dashboard/personal')->name('admin.staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::post('/', [StaffController::class, 'store'])->name('store');
        Route::post('/turnos', [StaffController::class, 'storeWorkLog'])->name('worklog.store');
        Route::patch('/turnos/{workLog}/pagar', [StaffController::class, 'togglePayWorkLog'])->name('worklog.pay');
    });

    // Guía Reto A: Biblioteca Multimedia
    Route::prefix('dashboard/media')->name('admin.media.')->group(function () {
        Route::get('/', [MediaController::class, 'index'])->name('index');
        Route::post('/', [MediaController::class, 'store'])->name('store');
        Route::put('/{media}', [MediaController::class, 'update'])->name('update');
        Route::delete('/{media}', [MediaController::class, 'destroy'])->name('destroy');
    });

    // Guía Reto B: Módulo de Noticias
    Route::prefix('dashboard/noticias')->name('admin.news.')->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('index');
        Route::get('/crear', [NewsController::class, 'create'])->name('create');
        Route::post('/', [NewsController::class, 'store'])->name('store');
        Route::get('/{news}/editar', [NewsController::class, 'edit'])->name('edit');
        Route::put('/{news}', [NewsController::class, 'update'])->name('update');
        Route::delete('/{news}', [NewsController::class, 'destroy'])->name('destroy');
        Route::patch('/{news}/toggle', [NewsController::class, 'togglePublish'])->name('toggle');
    });
});

// ---------- API Sanctum ----------

Route::post('/api/register', [LoginController::class, 'apiRegister']);
Route::post('/api/login', [LoginController::class, 'apiLogin'])->middleware('throttle:login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/api/profile', [LoginController::class, 'apiProfile']);
    Route::post('/api/logout', [LoginController::class, 'apiLogout']);

    // API Gestión Multimedia / Imágenes (Reto A)
    Route::prefix('api/media')->name('api.media.')->group(function () {
        Route::get('/', [MediaController::class, 'index'])->name('index');
        Route::post('/', [MediaController::class, 'store'])->name('store');
        Route::post('/{media}', [MediaController::class, 'update'])->name('update');
        Route::put('/{media}', [MediaController::class, 'update']);
        Route::delete('/{media}', [MediaController::class, 'destroy'])->name('destroy');
    });
});