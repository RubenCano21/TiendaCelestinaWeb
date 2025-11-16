<?php

namespace App\Http\Controllers;

use App\Enum\PermissionEnum;
use App\Models\UnidadMedida;
use App\Traits\HasRolePermissionChecks;
use App\Http\Requests\StoreUnidadMedidaRequest;
use App\Http\Requests\UpdateUnidadMedidaRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class UnidadMedidaController extends Controller
{
    use HasRolePermissionChecks;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): InertiaResponse
    {
        $user = $request->user();

        $query = UnidadMedida::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('codigo_medida', 'like', "%{$search}%");
            });
        }

        $unidades = $query->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('productos/unidades/Index', [
            'unidades' => $unidades,
            'filters' => $request->only(['search']),
            'can' => [
                'create' => $user->hasPermission(PermissionEnum::CREATE_UNIDADES->value),
                'edit' => $user->hasPermission(PermissionEnum::EDIT_UNIDADES->value),
                'delete' => $user->hasPermission(PermissionEnum::DELETE_UNIDADES->value),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('productos/unidades/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUnidadMedidaRequest $request)
    {
        $unidad = UnidadMedida::create($request->validated());

        return redirect()->route('productos.unidades.index')
            ->with('success', 'Unidad de medida creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UnidadMedida $unidadMedida)
    {
        $unidadMedida->load(['productos' => function($query) {
            $query->with(['categoria'])->latest()->take(10);
        }]);

        return Inertia::render('productos/unidades/Show', [
            'unidad' => $unidadMedida,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UnidadMedida $unidadMedida)
    {
        return Inertia::render('productos/unidades/Edit', [
            'unidad' => $unidadMedida,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUnidadMedidaRequest $request, UnidadMedida $unidadMedida)
    {
        $unidadMedida->update($request->validated());

        return redirect()->route('productos.unidades.index')
            ->with('success', 'Unidad de medida actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UnidadMedida $unidadMedida)
    {
        try {
            $unidadMedida->delete();
            return redirect()->route('productos.unidades.index')
                ->with('success', 'Unidad de medida eliminada exitosamente.');
        } catch (\Exception $e) {
            return redirect()->route('productos.unidades.index')
                ->with('error', 'No se puede eliminar la unidad de medida porque tiene productos asociados.');
        }
    }
}
