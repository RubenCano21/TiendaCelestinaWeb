<?php

namespace App\Enum;

enum PermissionEnum: string
{
    // Gestión de Usuarios
    case VIEW_USERS = 'view_users';
    case CREATE_USERS = 'create_users';
    case EDIT_USERS = 'edit_users';
    case DELETE_USERS = 'delete_users';

    // Gestión de Clientes
    case VIEW_CLIENTS = 'view_clients';
    case CREATE_CLIENTS = 'create_clients';
    case EDIT_CLIENTS = 'edit_clients';
    case DELETE_CLIENTS = 'delete_clients';

    // Gestión de Productos
    case VIEW_PRODUCTS = 'view_products';
    case CREATE_PRODUCTS = 'create_products';
    case EDIT_PRODUCTS = 'edit_products';
    case DELETE_PRODUCTS = 'delete_products';

    // Gestión de Ventas
    case VIEW_SALES = 'view_sales';
    case CREATE_SALES = 'create_sales';
    case EDIT_SALES = 'edit_sales';
    case DELETE_SALES = 'delete_sales';

    // Gestión de Reportes
    case VIEW_REPORTS = 'view_reports';
    case EXPORT_REPORTS = 'export_reports';

    // Gestión de Configuración
    case MANAGE_SETTINGS = 'manage_settings';
    case MANAGE_ROLES = 'manage_roles';
    case MANAGE_PERMISSIONS = 'manage_permissions';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function label(): string
    {
        return match($this) {
            self::VIEW_USERS => 'Ver Usuarios',
            self::CREATE_USERS => 'Crear Usuarios',
            self::EDIT_USERS => 'Editar Usuarios',
            self::DELETE_USERS => 'Eliminar Usuarios',

            self::VIEW_CLIENTS => 'Ver Clientes',
            self::CREATE_CLIENTS => 'Crear Clientes',
            self::EDIT_CLIENTS => 'Editar Clientes',
            self::DELETE_CLIENTS => 'Eliminar Clientes',

            self::VIEW_PRODUCTS => 'Ver Productos',
            self::CREATE_PRODUCTS => 'Crear Productos',
            self::EDIT_PRODUCTS => 'Editar Productos',
            self::DELETE_PRODUCTS => 'Eliminar Productos',

            self::VIEW_SALES => 'Ver Ventas',
            self::CREATE_SALES => 'Crear Ventas',
            self::EDIT_SALES => 'Editar Ventas',
            self::DELETE_SALES => 'Eliminar Ventas',

            self::VIEW_REPORTS => 'Ver Reportes',
            self::EXPORT_REPORTS => 'Exportar Reportes',

            self::MANAGE_SETTINGS => 'Gestionar Configuración',
            self::MANAGE_ROLES => 'Gestionar Roles',
            self::MANAGE_PERMISSIONS => 'Gestionar Permisos',
        };
    }

    public function description(): string
    {
        return match($this) {
            self::VIEW_USERS => 'Permite ver la lista de usuarios',
            self::CREATE_USERS => 'Permite crear nuevos usuarios',
            self::EDIT_USERS => 'Permite editar usuarios existentes',
            self::DELETE_USERS => 'Permite eliminar usuarios',

            self::VIEW_CLIENTS => 'Permite ver la lista de clientes',
            self::CREATE_CLIENTS => 'Permite crear nuevos clientes',
            self::EDIT_CLIENTS => 'Permite editar clientes existentes',
            self::DELETE_CLIENTS => 'Permite eliminar clientes',

            self::VIEW_PRODUCTS => 'Permite ver la lista de productos',
            self::CREATE_PRODUCTS => 'Permite crear nuevos productos',
            self::EDIT_PRODUCTS => 'Permite editar productos existentes',
            self::DELETE_PRODUCTS => 'Permite eliminar productos',

            self::VIEW_SALES => 'Permite ver la lista de ventas',
            self::CREATE_SALES => 'Permite realizar ventas',
            self::EDIT_SALES => 'Permite editar ventas existentes',
            self::DELETE_SALES => 'Permite eliminar ventas',

            self::VIEW_REPORTS => 'Permite ver reportes del sistema',
            self::EXPORT_REPORTS => 'Permite exportar reportes',

            self::MANAGE_SETTINGS => 'Permite gestionar la configuración del sistema',
            self::MANAGE_ROLES => 'Permite gestionar roles de usuario',
            self::MANAGE_PERMISSIONS => 'Permite gestionar permisos',
        };
    }
}
