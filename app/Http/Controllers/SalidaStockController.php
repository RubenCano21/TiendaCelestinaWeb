<?php

namespace App\Http\Controllers;

use App\Enum\PermissionEnum;
use App\Models\SalidaStock;
use App\Models\Producto;
use App\Traits\HasRolePermissionChecks;
use App\Http\Requests\StoreSalidaStockRequest;
use App\Http\Requests\UpdateSalidaStockRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class SalidaStockController extends Controller
{
    use HasRolePermissionChecks;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): InertiaResponse
    {
        $user = $request->user();

        $query = SalidaStock::with(['producto.categoria', 'producto.unidadMedida']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo_salida', 'like', "%{$search}%")
                  ->orWhere('motivo', 'like', "%{$search}%")
                  ->orWhereHas('producto', function($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('producto')) {
            $query->where('codigo_producto', $request->input('producto'));
        }

        $salidas = $query->orderBy('fecha', 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($salida) {
                return [
                    'codigo_salida' => $salida->codigo_salida,
                    'producto' => $salida->producto->nombre,
                    'codigo_producto' => $salida->codigo_producto,
                    'cantidad' => $salida->cantidad,
                    'motivo' => $salida->motivo,
                    'usuario' => $salida->usuario,
                    'fecha' => $salida->fecha->format('d/m/Y H:i'),
                    'created_at' => $salida->created_at,
                ];
            });

        $productos = Producto::orderBy('nombre')->get()->map(function ($prod) {
            return [
                'codigo' => $prod->codigo_producto,
                'nombre' => $prod->nombre,
            ];
        });

        return Inertia::render('productos/salidas-stock/Index', [
            'salidas' => $salidas,
            'productos' => $productos,
            'filters' => $request->only(['search', 'producto']),
            'can' => [
                'create' => $user->hasPermission(PermissionEnum::CREATE_SALIDAS_STOCK->value),
                'edit' => $user->hasPermission(PermissionEnum::EDIT_SALIDAS_STOCK->value),
                'delete' => $user->hasPermission(PermissionEnum::DELETE_SALIDAS_STOCK->value),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productos = Producto::with(['categoria', 'unidadMedida'])
            ->where('stock', '>', 0)
            ->orderBy('nombre')
            ->get()
            ->map(function ($prod) {
                return [
                    'codigo_producto' => $prod->codigo_producto,
                    'nombre' => $prod->nombre,
                    'stock_actual' => $prod->stock,
                    'categoria' => $prod->categoria?->nombre,
                    'unidad_medida' => $prod->unidadMedida?->nombre,
                ];
            });

        return Inertia::render('productos/salidas-stock/Create', [
            'productos' => $productos,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSalidaStockRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['usuario'] = $request->user()->name;

            // Verificar stock disponible
            $producto = Producto::find($data['codigo_producto']);
            if ($producto->stock < $data['cantidad']) {
                return redirect()->back()
                    ->with('error', "Stock insuficiente. Stock actual: {$producto->stock}")
                    ->withInput();
            }

            $salida = SalidaStock::create($data);

            // Actualizar stock del producto
            $producto->decrement('stock', $data['cantidad']);

            DB::commit();

            return redirect()->route('productos.salidas-stock.index')
                ->with('success', 'Salida de stock registrada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar la salida de stock: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(SalidaStock $salidaStock)
    {
        $salidaStock->load(['producto.categoria', 'producto.unidadMedida']);

        return Inertia::render('productos/salidas-stock/Show', [
            'salida' => $salidaStock,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalidaStock $salidaStock)
    {
        $salidaStock->load('producto');

        $productos = Producto::with(['categoria', 'unidadMedida'])
            ->orderBy('nombre')
            ->get()
            ->map(function ($prod) {
                return [
                    'codigo_producto' => $prod->codigo_producto,
                    'nombre' => $prod->nombre,
                    'stock_actual' => $prod->stock,
                    'categoria' => $prod->categoria?->nombre,
                    'unidad_medida' => $prod->unidadMedida?->nombre,
                ];
            });

        return Inertia::render('productos/salidas-stock/Edit', [
            'salida' => $salidaStock,
            'productos' => $productos,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSalidaStockRequest $request, SalidaStock $salidaStock)
    {
        DB::beginTransaction();
        try {
            $oldCantidad = $salidaStock->cantidad;
            $oldProducto = $salidaStock->codigo_producto;

            $data = $request->validated();

            // Revertir el stock anterior
            $productoAnterior = Producto::find($oldProducto);
            $productoAnterior->increment('stock', $oldCantidad);

            // Verificar stock disponible para la nueva cantidad
            $productoNuevo = Producto::find($data['codigo_producto']);
            if ($productoNuevo->stock < $data['cantidad']) {
                DB::rollBack();
                return redirect()->back()
                    ->with('error', "Stock insuficiente. Stock actual: {$productoNuevo->stock}")
                    ->withInput();
            }

            // Actualizar salida
            $salidaStock->update($data);

            // Aplicar nuevo stock
            $productoNuevo->decrement('stock', $data['cantidad']);

            DB::commit();

            return redirect()->route('productos.salidas-stock.index')
                ->with('success', 'Salida de stock actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar la salida de stock: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalidaStock $salidaStock)
    {
        DB::beginTransaction();
        try {
            // Revertir el stock
            $producto = Producto::find($salidaStock->codigo_producto);
            $producto->increment('stock', $salidaStock->cantidad);

            $salidaStock->delete();

            DB::commit();

            return redirect()->route('productos.salidas-stock.index')
                ->with('success', 'Salida de stock eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar la salida de stock: ' . $e->getMessage());
        }
    }
}
