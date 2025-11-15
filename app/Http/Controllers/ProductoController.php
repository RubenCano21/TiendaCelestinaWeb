<?php

namespace App\Http\Controllers;

use App\Enum\PermissionEnum;
use App\Models\Producto;
use App\Traits\HasRolePermissionChecks;
use Illuminate\Http\Request;
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

        $productos = Producto::orderBy('codigo_producto', 'desc')
            ->paginate(10)
            ->through(function ($producto) {
                return [
                    'codigo_producto' => $producto->codigo_producto,
                    'nombre' => $producto->nombre,
                    'imagen' => $producto->imagen,
                    'precio_unitario' => $producto->precio_unitario,
                    'categoria' => $producto->categoria,
                    'unidad_medida' => $producto->unidad_medida,
                    'created_at' => $producto->created_at,
                    'updated_at' => $producto->updated_at,
                ];
            });

        return Inertia::render('productos/Index', [
            'productos' => $productos,
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        //
    }
}
