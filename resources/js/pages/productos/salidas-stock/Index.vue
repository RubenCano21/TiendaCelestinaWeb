<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
} from '@/components/ui/alert-dialog';
import { Eye, Pencil, Trash2, Plus, Search } from 'lucide-vue-next';
import { ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';

interface Salida {
    codigo_salida: number;
    producto: string;
    codigo_producto: number;
    cantidad: number;
    motivo: string;
    usuario: string;
    fecha: string;
}

interface Producto {
    codigo: number;
    nombre: string;
}

interface Props {
    salidas: {
        data: Salida[];
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
    };
    productos: Producto[];
    filters?: {
        search?: string;
        producto?: number;
    };
    can: {
        create: boolean;
        edit: boolean;
        delete: boolean;
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Salidas de Stock',
        href: '/productos/salida-stock',
    },
];

// ✅ CAMBIO 1: Usar 'all' en lugar de ''
const search = ref(props.filters?.search || '');
const selectedProducto = ref(props.filters?.producto ? props.filters.producto.toString() : 'all');
const showDeleteDialog = ref(false);
const salidaToDelete = ref<number | null>(null);

const performSearch = useDebounceFn(() => {
    router.get(
        '/productos/salida-stock',
        {
            search: search.value,
            // ✅ CAMBIO 2: No enviar el parámetro si es 'all'
            producto: selectedProducto.value !== 'all' ? selectedProducto.value : undefined,
        },
        {
            preserveState: true,
            replace: true,
        }
    );
}, 500);

watch([search, selectedProducto], () => {
    performSearch();
});

const confirmDelete = (codigo: number) => {
    salidaToDelete.value = codigo;
    showDeleteDialog.value = true;
};

const deleteSalida = () => {
    if (salidaToDelete.value) {
        router.delete(`/productos/salida-stock/${salidaToDelete.value}`, {
            onSuccess: () => {
                showDeleteDialog.value = false;
                salidaToDelete.value = null;
            },
        });
    }
};
</script>

<template>
    <Head title="Salidas de Stock" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Salidas de Stock</h1>
                    <p class="text-muted-foreground mt-1">
                        Gestiona las salidas de stock del inventario
                    </p>
                </div>
                <Button
                    v-if="can.create"
                    as-child
                    class="gap-2"
                >
                    <a href="/productos/salida-stock/create">
                        <Plus class="h-4 w-4" />
                        Nueva Salida
                    </a>
                </Button>
            </div>

            <Card>
                <CardHeader>
                    <CardTitle>Listado de Salidas</CardTitle>
                    <CardDescription>
                        Todas las salidas de stock registradas
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="mb-4 flex gap-4">
                        <div class="relative flex-1">
                            <Search class="absolute left-2 top-2.5 h-4 w-4 text-muted-foreground" />
                            <Input
                                v-model="search"
                                placeholder="Buscar por código, producto o motivo..."
                                class="pl-8"
                            />
                        </div>
                        <Select v-model="selectedProducto">
                            <SelectTrigger class="w-[250px]">
                                <SelectValue placeholder="Filtrar por producto" />
                            </SelectTrigger>
                            <SelectContent>
                                <!-- ✅ CAMBIO 3: Cambiar value="" por value="all" -->
                                <SelectItem value="all">Todos los productos</SelectItem>
                                <SelectItem
                                    v-for="producto in productos"
                                    :key="producto.codigo"
                                    :value="producto.codigo.toString()"
                                >
                                    {{ producto.nombre }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <div class="rounded-md border">
                        <Table>
                            <TableHeader>
                                <TableRow>
                                    <TableHead>Código</TableHead>
                                    <TableHead>Producto</TableHead>
                                    <TableHead>Cantidad</TableHead>
                                    <TableHead>Motivo</TableHead>
                                    <TableHead>Usuario</TableHead>
                                    <TableHead>Fecha</TableHead>
                                    <TableHead class="text-right">Acciones</TableHead>
                                </TableRow>
                            </TableHeader>
                            <TableBody>
                                <TableRow
                                    v-for="salida in salidas.data"
                                    :key="salida.codigo_salida"
                                >
                                    <TableCell class="font-medium">
                                        {{ salida.codigo_salida }}
                                    </TableCell>
                                    <TableCell>{{ salida.producto }}</TableCell>
                                    <TableCell>
                                        <span class="text-red-600 font-semibold">
                                            -{{ salida.cantidad }}
                                        </span>
                                    </TableCell>
                                    <TableCell class="max-w-xs truncate">
                                        {{ salida.motivo }}
                                    </TableCell>
                                    <TableCell>{{ salida.usuario }}</TableCell>
                                    <TableCell>{{ salida.fecha }}</TableCell>
                                    <TableCell class="text-right">
                                        <div class="flex justify-end gap-2">
                                            <Button
                                                variant="ghost"
                                                size="icon"
                                                as-child
                                            >
                                                <a :href="`/productos/salida-stock/${salida.codigo_salida}`">
                                                    <Eye class="h-4 w-4" />
                                                </a>
                                            </Button>
                                            <Button
                                                v-if="can.edit"
                                                variant="ghost"
                                                size="icon"
                                                as-child
                                            >
                                                <a :href="`/productos/salida-stock/${salida.codigo_salida}/edit`">
                                                    <Pencil class="h-4 w-4" />
                                                </a>
                                            </Button>
                                            <Button
                                                v-if="can.delete"
                                                variant="ghost"
                                                size="icon"
                                                @click="confirmDelete(salida.codigo_salida)"
                                            >
                                                <Trash2 class="h-4 w-4" />
                                            </Button>
                                        </div>
                                    </TableCell>
                                </TableRow>
                                <TableRow v-if="salidas.data.length === 0">
                                    <TableCell
                                        colspan="7"
                                        class="text-center text-muted-foreground"
                                    >
                                        No se encontraron salidas de stock
                                    </TableCell>
                                </TableRow>
                            </TableBody>
                        </Table>
                    </div>

                    <div
                        v-if="salidas.last_page > 1"
                        class="mt-4 flex items-center justify-between"
                    >
                        <p class="text-sm text-muted-foreground">
                            Mostrando {{ salidas.data.length }} de {{ salidas.total }} resultados
                        </p>
                        <div class="flex gap-2">
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="salidas.current_page === 1"
                                @click="router.get(`/productos/salida-stock?page=${salidas.current_page - 1}`)"
                            >
                                Anterior
                            </Button>
                            <Button
                                variant="outline"
                                size="sm"
                                :disabled="salidas.current_page === salidas.last_page"
                                @click="router.get(`/productos/salida-stock?page=${salidas.current_page + 1}`)"
                            >
                                Siguiente
                            </Button>
                        </div>
                    </div>
                </CardContent>
            </Card>
        </div>

        <AlertDialog v-model:open="showDeleteDialog">
            <AlertDialogContent>
                <AlertDialogHeader>
                    <AlertDialogTitle>¿Estás seguro?</AlertDialogTitle>
                    <AlertDialogDescription>
                        Esta acción eliminará la salida de stock y restaurará la cantidad al inventario.
                        Esta acción no se puede deshacer.
                    </AlertDialogDescription>
                </AlertDialogHeader>
                <AlertDialogFooter>
                    <AlertDialogCancel>Cancelar</AlertDialogCancel>
                    <AlertDialogAction @click="deleteSalida">
                        Eliminar
                    </AlertDialogAction>
                </AlertDialogFooter>
            </AlertDialogContent>
        </AlertDialog>
    </AppLayout>
</template>
