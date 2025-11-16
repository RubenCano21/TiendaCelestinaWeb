<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';
import {
    BookOpen,
    Folder,
    LayoutGrid,
    Users,
    Package,
    FolderArchiveIcon,
    TrendingUp,
    TrendingDown,
    Ruler,
} from 'lucide-vue-next';
import { computed } from 'vue';
import AppLogo from './AppLogo.vue';

const page = usePage();

const allNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Clientes',
        href: '/clientes',
        icon: Users,
        permission: 'view_clients',
    },
    {
        title: 'Gestionar Productos',
        href: '/productos',
        icon: Package,
        permission: 'view_products',
        children: [
            {
                title: 'Productos',
                href: '/productos',
                icon: Package,
                permission: 'view_products',
            },
            {
                title: 'Categorías',
                href: '/productos/categorias',
                icon: FolderArchiveIcon,
                permission: 'view_categorias',
            },
            {
                title: 'Unidades de Medida',
                href: '/productos/unidades',
                icon: Ruler,
                permission: 'view_unidades',
            },
            {
                title: 'Entradas de Stock',
                href: '/productos/entrada-stock',
                icon: TrendingUp,
                permission: 'view_entradas_stock',
            },
            {
                title: 'Salidas de Stock',
                href: '/productos/salida-stock',
                icon: TrendingDown,
                permission: 'view_salidas_stock',
            },
        ],
    },
];

const mainNavItems = computed(() => {
    const permissions = page.props.auth.permissions as string[];
    return allNavItems
        .filter((item) => {
            if (!item.permission) return true;
            return permissions.includes(item.permission);
        })
        .map((item) => {
            // Si el item tiene children, filtrar también los children según permisos
            if (item.children) {
                const filteredChildren = item.children.filter((child) => {
                    if (!child.permission) return true;
                    return permissions.includes(child.permission);
                });

                // Solo incluir el item padre si tiene al menos un hijo visible
                if (filteredChildren.length === 0) return null;

                return {
                    ...item,
                    children: filteredChildren,
                };
            }
            return item;
        })
        .filter((item) => item !== null);
});

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
