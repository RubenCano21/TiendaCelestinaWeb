<?php

use App\Enum\PermissionEnum;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\ClienteController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Rutas de clientes con permisos específicos
Route::middleware(['auth', 'verified'])->group(function () {
    // Ver clientes
    Route::get('clientes', [ClienteController::class, 'index'])
        ->middleware('permission:' . PermissionEnum::VIEW_CLIENTS->value)
        ->name('clientes.index');

    // Crear clientes (DEBE IR ANTES de {cliente})
    Route::get('clientes/create', [ClienteController::class, 'create'])
        ->middleware('permission:' . PermissionEnum::CREATE_CLIENTS->value)
        ->name('clientes.create');

    Route::post('clientes', [ClienteController::class, 'store'])
        ->middleware('permission:' . PermissionEnum::CREATE_CLIENTS->value)
        ->name('clientes.store');

    // Editar clientes (DEBE IR ANTES de {cliente})
    Route::get('clientes/{cliente}/edit', [ClienteController::class, 'edit'])
        ->middleware('permission:' . PermissionEnum::EDIT_CLIENTS->value)
        ->name('clientes.edit');

    // Ver detalle de cliente (DEBE IR DESPUÉS de rutas específicas)
    Route::get('clientes/{cliente}', [ClienteController::class, 'show'])
        ->middleware('permission:' . PermissionEnum::VIEW_CLIENTS->value)
        ->name('clientes.show');

    Route::put('clientes/{cliente}', [ClienteController::class, 'update'])
        ->middleware('permission:' . PermissionEnum::EDIT_CLIENTS->value)
        ->name('clientes.update');

    Route::patch('clientes/{cliente}', [ClienteController::class, 'update'])
        ->middleware('permission:' . PermissionEnum::EDIT_CLIENTS->value);

    // Eliminar clientes
    Route::delete('clientes/{cliente}', [ClienteController::class, 'destroy'])
        ->middleware('permission:' . PermissionEnum::DELETE_CLIENTS->value)
        ->name('clientes.destroy');
});

require __DIR__.'/settings.php';
