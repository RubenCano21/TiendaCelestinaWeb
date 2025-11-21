<?php

use App\Enum\PermissionEnum;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\EntradaStockController;
use App\Http\Controllers\SalidaStockController;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('prueba', function () {
    return Inertia::render('Prueba');
})->name('prueba');

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

Route::middleware(['auth', 'verified'])->group(function () {
    // ========================================
    // Rutas de clientes
    // ========================================
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

    // ========================================
    // PRODUCTOS - Rutas generales
    // ========================================
    Route::get('productos', [ProductoController::class, 'index'])
        ->middleware('permission:' . PermissionEnum::VIEW_PRODUCTS->value)
        ->name('productos.index');

    Route::get('productos/create', [ProductoController::class, 'create'])
        ->middleware('permission:' . PermissionEnum::CREATE_PRODUCTS->value)
        ->name('productos.create');

    // ========================================
    // ENTRADAS DE STOCK
    // ========================================
    Route::get('productos/entrada-stock', [EntradaStockController::class, 'index'])
        ->middleware('permission:' . PermissionEnum::VIEW_ENTRADAS_STOCK->value)
        ->name('entradas-stock.index');

    Route::get('productos/entrada-stock/create', [EntradaStockController::class, 'create'])
        ->middleware('permission:' . PermissionEnum::CREATE_ENTRADAS_STOCK->value)
        ->name('entradas-stock.create');

    Route::post('productos/entrada-stock', [EntradaStockController::class, 'store'])
        ->middleware('permission:' . PermissionEnum::CREATE_ENTRADAS_STOCK->value)
        ->name('entradas-stock.store');

    Route::get('productos/entrada-stock/{entradaStock}/edit', [EntradaStockController::class, 'edit'])
        ->middleware('permission:' . PermissionEnum::EDIT_ENTRADAS_STOCK->value)
        ->name('entradas-stock.edit');
    // ... resto de rutas de entrada-stock

    // ========================================
    // SALIDAS DE STOCK
    // ========================================
    Route::get('productos/salida-stock', [SalidaStockController::class, 'index'])
        ->middleware('permission:' . PermissionEnum::VIEW_SALIDAS_STOCK->value)
        ->name('salidas-stock.index');

    Route::get('productos/salida-stock/create', [SalidaStockController::class, 'create'])
        ->middleware('permission:' . PermissionEnum::CREATE_SALIDAS_STOCK->value)
        ->name('salidas-stock.create');

    Route::post('productos/salida-stock', [SalidaStockController::class, 'store'])
        ->middleware('permission:' . PermissionEnum::CREATE_SALIDAS_STOCK->value)
        ->name('salidas-stock.store');

    Route::get('productos/salida-stock/{salidaStock}/edit', [SalidaStockController::class, 'edit'])
        ->middleware('permission:' . PermissionEnum::EDIT_SALIDAS_STOCK->value)
        ->name('salidas-stock.edit');

    Route::get('productos/salida-stock/{salidaStock}', [SalidaStockController::class, 'show'])
        ->middleware('permission:' . PermissionEnum::VIEW_SALIDAS_STOCK->value)
        ->name('salidas-stock.show');

    Route::put('productos/salida-stock/{salidaStock}', [SalidaStockController::class, 'update'])
        ->middleware('permission:' . PermissionEnum::EDIT_SALIDAS_STOCK->value)
        ->name('salidas-stock.update');

    Route::delete('productos/salida-stock/{salidaStock}', [SalidaStockController::class, 'destroy'])
        ->middleware('permission:' . PermissionEnum::DELETE_SALIDAS_STOCK->value)
        ->name('salidas-stock.destroy');

    // ... resto de rutas de salida-stock

    // ========================================
    // CATEGORÍAS
    // ========================================
    Route::get('productos/categorias', [CategoriaController::class, 'index'])
        ->middleware('permission:' . PermissionEnum::VIEW_CATEGORIAS->value)
        ->name('categorias.index');

    Route::get('productos/categorias/create', [CategoriaController::class, 'create'])
        ->middleware('permission:' . PermissionEnum::CREATE_CATEGORIAS->value)
        ->name('categorias.create');

    Route::post('productos/categorias', [CategoriaController::class, 'store'])
        ->middleware('permission:' . PermissionEnum::CREATE_CATEGORIAS->value)
        ->name('categorias.store');

    Route::get('productos/categorias/{categoria}/edit', [CategoriaController::class, 'edit'])
        ->middleware('permission:' . PermissionEnum::EDIT_CATEGORIAS->value)
        ->name('categorias.edit');

    Route::get('productos/categorias/{categoria}', [CategoriaController::class, 'show'])
        ->middleware('permission:' . PermissionEnum::VIEW_CATEGORIAS->value)
        ->name('categorias.show');

    Route::put('productos/categorias/{categoria}', [CategoriaController::class, 'update'])
        ->middleware('permission:' . PermissionEnum::EDIT_CATEGORIAS->value)
        ->name('categorias.update');

    Route::patch('productos/categorias/{categoria}', [CategoriaController::class, 'update'])
        ->middleware('permission:' . PermissionEnum::EDIT_CATEGORIAS->value);

    Route::delete('productos/categorias/{categoria}', [CategoriaController::class, 'destroy'])
        ->middleware('permission:' . PermissionEnum::DELETE_CATEGORIAS->value)
        ->name('categorias.destroy');

    // ========================================
    // UNIDADES DE MEDIDA -
    // ========================================
    Route::get('productos/unidades', [UnidadMedidaController::class, 'index'])
        ->middleware('permission:' . PermissionEnum::VIEW_UNIDADES->value)
        ->name('unidades.index');

    Route::get('productos/unidades/create', [UnidadMedidaController::class, 'create'])
        ->middleware('permission:' . PermissionEnum::CREATE_UNIDADES->value)
        ->name('unidades.create');

    Route::post('productos/unidades', [UnidadMedidaController::class, 'store'])
        ->middleware('permission:' . PermissionEnum::CREATE_UNIDADES->value)
        ->name('unidades.store');

    Route::get('productos/unidades/{unidadMedida}/edit', [UnidadMedidaController::class, 'edit'])
        ->middleware('permission:' . PermissionEnum::EDIT_UNIDADES->value)
        ->name('unidades.edit');

    Route::get('productos/unidades/{unidadMedida}', [UnidadMedidaController::class, 'show'])
        ->middleware('permission:' . PermissionEnum::VIEW_UNIDADES->value)
        ->name('unidades.show');

    Route::put('productos/unidades/{unidadMedida}', [UnidadMedidaController::class, 'update'])
        ->middleware('permission:' . PermissionEnum::EDIT_UNIDADES->value)
        ->name('unidades.update');

    Route::patch('productos/unidades/{unidadMedida}', [UnidadMedidaController::class, 'update'])
        ->middleware('permission:' . PermissionEnum::EDIT_UNIDADES->value);

    Route::delete('productos/unidades/{unidadMedida}', [UnidadMedidaController::class, 'destroy'])
        ->middleware('permission:' . PermissionEnum::DELETE_UNIDADES->value)
        ->name('unidades.destroy');

    // ========================================
    // PRODUCTOS
    // ========================================
    Route::post('productos', [ProductoController::class, 'store'])
        ->middleware('permission:' . PermissionEnum::CREATE_PRODUCTS->value)
        ->name('productos.store');

    Route::get('productos/{producto}/edit', [ProductoController::class, 'edit'])
        ->middleware('permission:' . PermissionEnum::EDIT_PRODUCTS->value)
        ->name('productos.edit');

    Route::get('productos/{producto}', [ProductoController::class, 'show'])
        ->middleware('permission:' . PermissionEnum::VIEW_PRODUCTS->value)
        ->name('productos.show');

    Route::put('productos/{producto}', [ProductoController::class, 'update'])
        ->middleware('permission:' . PermissionEnum::EDIT_PRODUCTS->value)
        ->name('productos.update');

    Route::patch('productos/{producto}', [ProductoController::class, 'update'])
        ->middleware('permission:' . PermissionEnum::EDIT_PRODUCTS->value);

    Route::delete('productos/{producto}', [ProductoController::class, 'destroy'])
        ->middleware('permission:' . PermissionEnum::DELETE_PRODUCTS->value)
        ->name('productos.destroy');
});

require __DIR__.'/settings.php';
