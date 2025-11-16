<?php

namespace App\Http\Controllers;

use App\Enum\PermissionEnum;
use App\Models\Categoria;
use App\Traits\HasRolePermissionChecks;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CategoriaController extends Controller
{

    use HasRolePermissionChecks;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): InertiaResponse
    {
        $user = $request->user();

        // Obtener query base
        $query = Categoria::query();

        // Aplicar filtro de búsqueda si existe
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo_categoria', 'like', "%{$search}%");
            });
        }

        // Obtener categorías paginadas
        $categorias = $query->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('productos/categorias/Index', [
            'categorias' => $categorias,
            'filters' => $request->only(['search']),
            'can' => [
                'create' => $user->hasPermission(PermissionEnum::CREATE_CATEGORIAS->value),
                'edit' => $user->hasPermission(PermissionEnum::EDIT_CATEGORIAS->value),
                'delete' => $user->hasPermission(PermissionEnum::DELETE_CATEGORIAS->value),
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
    public function store(\App\Http\Requests\StoreCategoriaRequest $request)
    {
        $categoria = Categoria::create($request->validated());

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        $categoria->load(['productos' => function($query) {
            $query->with(['unidadMedida'])->latest()->take(10);
        }]);

        return Inertia::render('categorias/Show', [
            'categoria' => $categoria,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        return Inertia::render('categorias/Edit', [
            'categoria' => $categoria,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateCategoriaRequest $request, Categoria $categoria)
    {
        $categoria->update($request->validated());

        return redirect()->route('categorias.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        try {
            $categoria->delete();
            return redirect()->route('categorias.index')
                ->with('success', 'Categoría eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('categorias.index')
                ->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }
    }
}
