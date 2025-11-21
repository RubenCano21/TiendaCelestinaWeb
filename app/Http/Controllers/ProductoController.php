<?php

namespace App\Http\Controllers;

use App\Enum\PermissionEnum;
use App\Models\Producto;
use App\Traits\HasRolePermissionChecks;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class ProductoController extends Controller
{
    use HasRolePermissionChecks;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): InertiaResponse
    {
        $user = $request->user();

        $query = Producto::with(['categoria', 'unidadMedida']);

        // Filtro de búsqueda por nombre o código
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'ilike', "%{$search}%")
                    ->orWhere('codigo_producto', 'like', "%{$search}%");
            });
        }

        // Filtro por categoría
        if ($request->filled('categoria')) {
            $query->where('categoria_codigo', $request->input('categoria'));
        }

        $productos = $query->orderBy('codigo_producto', 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(function ($producto) {
                return [
                    'codigo_producto' => $producto->codigo_producto,
                    'nombre' => $producto->nombre,
                    'imagen' => $producto->imagen,
                    'precio_unitario' => $producto->precio_unitario,
                    'stock' => $producto->stock,
                    'categoria' => $producto->categoria?->nombre,
                    'categoria_codigo' => $producto->categoria_codigo,
                    'unidad_medida' => $producto->unidadMedida?->nombre,
                    'unidad_codigo' => $producto->unidad_codigo,
                    'created_at' => $producto->created_at,
                    'upda ted_at' => $producto->updated_at,
                ];
            });

        // Obtener todas las categorías para el filtro
        $categorias = \App\Models\Categoria::orderBy('nombre')->get()->map(function ($cat) {
            return [
                'codigo' => $cat->codigo_categoria,
                'nombre' => $cat->nombre,
            ];
        });

        return Inertia::render('productos/Index', [
            'productos' => $productos,
            'categorias' => $categorias,
            'filters' => $request->only(['search', 'categoria']),
            'can' => [
                'create' => $user->hasPermission(PermissionEnum::CREATE_PRODUCTS->value),
                'edit' => $user->hasPermission(PermissionEnum::EDIT_PRODUCTS->value),
                'delete' => $user->hasPermission(PermissionEnum::DELETE_PRODUCTS->value),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = \App\Models\Categoria::orderBy('nombre')->get();
        $unidades = \App\Models\UnidadMedida::orderBy('nombre')->get();

        return Inertia::render('productos/Create', [
            'categorias' => $categorias,
            'unidades' => $unidades,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreProductoRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto = Producto::create($data);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        $producto->load(['categoria', 'unidadMedida', 'entradasStock', 'salidasStock']);

        return Inertia::render('productos/Show', [
            'producto' => $producto,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $categorias = \App\Models\Categoria::orderBy('nombre')->get();
        $unidades = \App\Models\UnidadMedida::orderBy('nombre')->get();

        return Inertia::render('productos/Edit', [
            'producto' => $producto,
            'categorias' => $categorias,
            'unidades' => $unidades,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateProductoRequest $request, Producto $producto)
    {
        $data = $request->validated();

        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('productos', 'public');
        }

        $producto->update($data);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        try {
            // Eliminar imagen si existe
            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }

            $producto->delete();
            return redirect()->route('productos.index')
                ->with('success', 'Producto eliminado exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('productos.index')
                ->with('error', 'No se puede eliminar el producto porque tiene movimientos de stock asociados.');
        }
    }
}
