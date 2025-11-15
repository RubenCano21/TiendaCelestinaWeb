<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import Pagination from '@/components/Pagination.vue';
import { Plus, Pencil, Trash2, Eye, Package, DollarSign, Tag } from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { ref } from 'vue';

interface Producto {
    codigo_producto: number;
    nombre: string;
    imagen: string | null;
    precio_unitario: number;
    categoria: string | null;
    unidad_medida: string;
    created_at: string;
    updated_at: string;
}

interface PaginatedProductos {
    data: Producto[];
    current_page: number;
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: Array<{
        url: string | null;
        label: string;
        active: boolean;
    }>;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}

interface Props {
    productos: PaginatedProductos;
    can: {
        create: boolean;
        edit: boolean;
        delete: boolean;
    };
}

defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Productos',
        href: '/productos',
    },
];

// Estado para el diálogo de confirmación de eliminación
const showDeleteDialog = ref(false);
const productoToDelete = ref<Producto | null>(null);

const confirmDelete = (producto: Producto) => {
    productoToDelete.value = producto;
    showDeleteDialog.value = true;
};

const deleteProducto = () => {
    if (productoToDelete.value) {
        router.delete(`/productos/${productoToDelete.value.codigo_producto}`, {
            onSuccess: () => {
                showDeleteDialog.value = false;
                productoToDelete.value = null;
            },
        });
    }
};

const cancelDelete = () => {
    showDeleteDialog.value = false;
    productoToDelete.value = null;
};

// Formatear precio
const formatPrice = (price: number) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
    }).format(price);
};
</script>

<template>
    <Head title="Productos" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Productos</h1>
                    <p class="text-muted-foreground mt-1">
                        Gestiona el inventario de productos
                    </p>
                </div>
                <Button
                    v-if="can.create"
                    as-child
                    class="gap-2"
                >
                    <Link href="/productos/create">
                        <Plus class="h-4 w-4" />
                        Nuevo Producto
                    </Link>
                </Button>
            </div>

            <!-- Tabla de Productos -->
            <Card>
                <CardHeader>
                    <CardTitle>Lista de Productos</CardTitle>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="productos.data.length === 0"
                        class="flex flex-col items-center justify-center py-12"
                    >
                        <p class="text-muted-foreground mb-4 text-lg">
                            No hay productos registrados
                        </p>
                        <Button
                            v-if="can.create"
                            as-child
                        >
                            <Link href="/productos/create">
                                <Plus class="mr-2 h-4 w-4" />
                                Crear Primer Producto
                            </Link>
                        </Button>
                    </div>

                    <div
                        v-else
                        class="overflow-x-auto"
                    >
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th
                                        class="text-muted-foreground px-4 py-3 text-left text-sm font-medium"
                                    >
                                        #
                                    </th>
                                    <th
                                        class="text-muted-foreground px-4 py-3 text-left text-sm font-medium"
                                    >
                                        Código
                                    </th>
                                    <th
                                        class="text-muted-foreground px-4 py-3 text-left text-sm font-medium"
                                    >
                                        <div class="flex items-center gap-2">
                                            <Package class="h-4 w-4" />
                                            Nombre
                                        </div>
                                    </th>
                                    <th
                                        class="text-muted-foreground px-4 py-3 text-left text-sm font-medium"
                                    >
                                        <div class="flex items-center gap-2">
                                            <Tag class="h-4 w-4" />
                                            Categoría
                                        </div>
                                    </th>
                                    <th
                                        class="text-muted-foreground px-4 py-3 text-left text-sm font-medium"
                                    >
                                        <div class="flex items-center gap-2">
                                            <DollarSign class="h-4 w-4" />
                                            Precio
                                        </div>
                                    </th>
                                    <th
                                        class="text-muted-foreground px-4 py-3 text-left text-sm font-medium"
                                    >
                                        Unidad
                                    </th>
                                    <th
                                        class="text-muted-foreground px-4 py-3 text-right text-sm font-medium"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(producto, index) in productos.data"
                                    :key="producto.codigo_producto"
                                    class="border-b transition-colors hover:bg-muted/50"
                                >
                                    <td class="px-4 py-3 text-sm">
                                        {{ (productos.current_page - 1) * productos.per_page + index + 1 }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ producto.codigo_producto }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium">
                                        {{ producto.nombre }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span v-if="producto.categoria">
                                            {{ producto.categoria }}
                                        </span>
                                        <span
                                            v-else
                                            class="text-muted-foreground"
                                        >
                                            Sin categoría
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium">
                                        {{ formatPrice(producto.precio_unitario) }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ producto.unidad_medida }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <Button
                                                as-child
                                                variant="ghost"
                                                size="sm"
                                            >
                                                <Link
                                                    :href="`/productos/${producto.codigo_producto}`"
                                                >
                                                    <Eye class="h-4 w-4" />
                                                </Link>
                                            </Button>

                                            <Button
                                                v-if="can.edit"
                                                as-child
                                                variant="ghost"
                                                size="sm"
                                            >
                                                <Link
                                                    :href="`/productos/${producto.codigo_producto}/edit`"
                                                >
                                                    <Pencil class="h-4 w-4" />
                                                </Link>
                                            </Button>

                                            <Button
                                                v-if="can.delete"
                                                variant="ghost"
                                                size="sm"
                                                @click="confirmDelete(producto)"
                                            >
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    <div
                        v-if="productos.data.length > 0"
                        class="mt-4"
                    >
                        <Pagination :data="productos" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <!-- Diálogo de confirmación de eliminación -->
        <Dialog
            :open="showDeleteDialog"
            @update:open="cancelDelete"
        >
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>¿Eliminar producto?</DialogTitle>
                    <DialogDescription>
                        ¿Estás seguro de que deseas eliminar el producto
                        <strong v-if="productoToDelete">
                            {{ productoToDelete.nombre }}
                        </strong>?
                        Esta acción no se puede deshacer.
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button
                        variant="outline"
                        @click="cancelDelete"
                    >
                        Cancelar
                    </Button>
                    <Button
                        variant="destructive"
                        @click="deleteProducto"
                    >
                        Eliminar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
