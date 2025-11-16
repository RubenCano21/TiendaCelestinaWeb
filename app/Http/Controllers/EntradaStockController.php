<?php

namespace App\Http\Controllers;

use App\Enum\PermissionEnum;
use App\Models\EntradaStock;
use App\Models\Producto;
use App\Traits\HasRolePermissionChecks;
use App\Http\Requests\StoreEntradaStockRequest;
use App\Http\Requests\UpdateEntradaStockRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class EntradaStockController extends Controller
{
    use HasRolePermissionChecks;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): InertiaResponse
    {
        $user = $request->user();

        $query = EntradaStock::with(['producto.categoria', 'producto.unidadMedida']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo_entrada', 'like', "%{$search}%")
                  ->orWhere('motivo', 'like', "%{$search}%")
                  ->orWhereHas('producto', function($q) use ($search) {
                      $q->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('producto')) {
            $query->where('codigo_producto', $request->input('producto'));
        }

        $entradas = $query->orderBy('fecha', 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($entrada) {
                return [
                    'codigo_entrada' => $entrada->codigo_entrada,
                    'producto' => $entrada->producto->nombre,
                    'codigo_producto' => $entrada->codigo_producto,
                    'cantidad' => $entrada->cantidad,
                    'precio_unitario' => $entrada->producto->precio_unitario,
                    'monto_total' => $entrada->cantidad * $entrada->producto->precio_unitario,
                    'motivo' => $entrada->motivo,
                    'usuario' => $entrada->usuario,
                    'fecha' => $entrada->fecha->format('d/m/Y H:i'),
                    'created_at' => $entrada->created_at,
                ];
            });

        $productos = Producto::orderBy('nombre')->get()->map(function ($prod) {
            return [
                'codigo' => $prod->codigo_producto,
                'nombre' => $prod->nombre,
            ];
        });

        return Inertia::render('productos/entradas-stock/Index', [
            'entradas' => $entradas,
            'productos' => $productos,
            'filters' => $request->only(['search', 'producto']),
            'can' => [
                'create' => $user->hasPermission(PermissionEnum::CREATE_ENTRADAS_STOCK->value),
                'edit' => $user->hasPermission(PermissionEnum::EDIT_ENTRADAS_STOCK->value),
                'delete' => $user->hasPermission(PermissionEnum::DELETE_ENTRADAS_STOCK->value),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
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

        return Inertia::render('productos/entradas-stock/Create', [
            'productos' => $productos,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEntradaStockRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['usuario'] = $request->user()->name;

            $entrada = EntradaStock::create($data);

            // Actualizar stock del producto
            $producto = Producto::find($data['codigo_producto']);
            $producto->increment('stock', $data['cantidad']);

            DB::commit();

            return redirect()->route('productos.entradas-stock.index')
                ->with('success', 'Entrada de stock registrada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar la entrada de stock: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(EntradaStock $entradaStock)
    {
        $entradaStock->load(['producto.categoria', 'producto.unidadMedida']);

        return Inertia::render('productos/entradas-stock/Show', [
            'entrada' => $entradaStock,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EntradaStock $entradaStock)
    {
        $entradaStock->load('producto');

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

        return Inertia::render('productos/entradas-stock/Edit', [
            'entrada' => $entradaStock,
            'productos' => $productos,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntradaStockRequest $request, EntradaStock $entradaStock)
    {
        DB::beginTransaction();
        try {
            $oldCantidad = $entradaStock->cantidad;
            $oldProducto = $entradaStock->codigo_producto;

            $data = $request->validated();

            // Revertir el stock anterior
            $productoAnterior = Producto::find($oldProducto);
            $productoAnterior->decrement('stock', $oldCantidad);

            // Actualizar entrada
            $entradaStock->update($data);

            // Aplicar nuevo stock
            $productoNuevo = Producto::find($data['codigo_producto']);
            $productoNuevo->increment('stock', $data['cantidad']);

            DB::commit();

            return redirect()->route('productos.entradas-stock.index')
                ->with('success', 'Entrada de stock actualizada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al actualizar la entrada de stock: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EntradaStock $entradaStock)
    {
        DB::beginTransaction();
        try {
            // Revertir el stock
            $producto = Producto::find($entradaStock->codigo_producto);
            $producto->decrement('stock', $entradaStock->cantidad);

            $entradaStock->delete();

            DB::commit();

            return redirect()->route('productos.entradas-stock.index')
                ->with('success', 'Entrada de stock eliminada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar la entrada de stock: ' . $e->getMessage());
        }
    }
}
