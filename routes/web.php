<?php

use App\Enum\PermissionEnum;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    // Obtener productos agrupados por categoría con relación
    $productosPorCategoria = Producto::with('categoria')
        ->get()
        ->groupBy('categoria_codigo')
        ->map(function ($productos, $categoriaId) {
            $categoria = $productos->first()->categoria;
            return [
                'name' => $categoria?->nombre ?? 'Sin categoría',
                'value' => $productos->count(),
            ];
        })
        ->values();

    return Inertia::render('Dashboard', [
        'productosPorCategoria' => $productosPorCategoria,
    ]);
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

    // Rutas de productos
    Route::get('productos', [ProductoController::class, 'index'])
        ->middleware('permission:' . PermissionEnum::VIEW_PRODUCTS->value)
        ->name('productos.index');

    // Rutas para Categorias
    Route::get('categorias', [CategoriaController::class, 'index'])
        ->middleware('permission:' . PermissionEnum::VIEW_CATEGORIAS->value)
        ->name('categorias.index');

    // Crear categorías (DEBE IR ANTES de {categoria})
    Route::get('categorias/create', [CategoriaController::class, 'create'])
        ->middleware('permission:' . PermissionEnum::CREATE_CATEGORIAS->value)
        ->name('categorias.create');

    Route::post('categorias', [CategoriaController::class, 'store'])
        ->middleware('permission:' . PermissionEnum::CREATE_CATEGORIAS->value)
        ->name('categorias.store');

    // Editar categorías (DEBE IR ANTES de {categoria})
    Route::get('categorias/{categoria}/edit', [CategoriaController::class, 'edit'])
        ->middleware('permission:' . PermissionEnum::EDIT_CATEGORIAS->value)
        ->name('categorias.edit');

    // Ver detalle de categoría (DEBE IR DESPUÉS de rutas específicas)
    Route::get('categorias/{categoria}', [CategoriaController::class, 'show'])
        ->middleware('permission:' . PermissionEnum::VIEW_CATEGORIAS->value)
        ->name('categorias.show');

    Route::put('categorias/{categoria}', [CategoriaController::class, 'update'])
        ->middleware('permission:' . PermissionEnum::EDIT_CATEGORIAS->value)
        ->name('categorias.update');

    Route::patch('categorias/{categoria}', [CategoriaController::class, 'update'])
        ->middleware('permission:' . PermissionEnum::EDIT_CATEGORIAS->value);

    // Eliminar categorías
    Route::delete('categorias/{categoria}', [CategoriaController::class, 'destroy'])
        ->middleware('permission:' . PermissionEnum::DELETE_CATEGORIAS->value)
        ->name('categorias.destroy');
});

require __DIR__.'/settings.php';
