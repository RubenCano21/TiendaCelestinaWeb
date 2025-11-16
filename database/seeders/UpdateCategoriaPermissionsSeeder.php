<?php

namespace Database\Seeders;

use App\Enum\PermissionEnum;
use App\Enum\RoleEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class UpdateCategoriaPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Este seeder actualiza los permisos de categorías sin afectar datos existentes
     */
    public function run(): void
    {
        // Crear permisos de categorías si no existen
        $categoriaPermissions = [
            PermissionEnum::VIEW_CATEGORIAS,
            PermissionEnum::CREATE_CATEGORIAS,
            PermissionEnum::EDIT_CATEGORIAS,
            PermissionEnum::DELETE_CATEGORIAS,
        ];

        $permissionIds = [];
        foreach ($categoriaPermissions as $permissionEnum) {
            $permission = Permission::firstOrCreate(
                ['name' => $permissionEnum->value],
                [
                    'display_name' => $permissionEnum->label(),
                    'description' => $permissionEnum->description(),
                ]
            );
            $permissionIds[] = $permission->id;
        }

        // Obtener rol de Propietario y agregar todos los permisos de categorías
        $propietarioRole = Role::where('name', RoleEnum::PROPIETARIO->value)->first();
        if ($propietarioRole) {
            $currentPermissions = $propietarioRole->permissions()->pluck('permissions.id')->toArray();
            $allPermissions = array_unique(array_merge($currentPermissions, $permissionIds));
            $propietarioRole->permissions()->sync($allPermissions);
            $this->command->info('Permisos de categorías agregados al rol Propietario');
        }

        // Obtener rol de Vendedor y agregar permiso de ver categorías
        $vendedorRole = Role::where('name', RoleEnum::VENDEDOR->value)->first();
        if ($vendedorRole) {
            $viewCategoriaPermission = Permission::where('name', PermissionEnum::VIEW_CATEGORIAS->value)->first();
            if ($viewCategoriaPermission) {
                $currentPermissions = $vendedorRole->permissions()->pluck('permissions.id')->toArray();
                if (!in_array($viewCategoriaPermission->id, $currentPermissions)) {
                    $vendedorRole->permissions()->attach($viewCategoriaPermission->id);
                    $this->command->info('Permiso VIEW_CATEGORIAS agregado al rol Vendedor');
                }
            }
        }

        $this->command->info('Permisos de categorías actualizados exitosamente.');
    }
}

