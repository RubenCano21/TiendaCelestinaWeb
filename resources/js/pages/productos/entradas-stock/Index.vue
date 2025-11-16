<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import Pagination from '@/components/Pagination.vue';
import { Plus, Pencil, Trash2, Eye, TrendingUp, Search, X, Package } from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';

interface EntradaStock {
    codigo_entrada: number;
    producto: string;
    codigo_producto: number;
    cantidad: number;
    precio_unitario: number;
    monto_total: number;
    motivo: string | null;
    usuario: string | null;
    fecha: string;
    created_at: string;
}

interface PaginatedEntradas {
    data: EntradaStock[];
    current_page: number;
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: Array<{ url: string | null; label: string; active: boolean }>;
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}

interface Producto {
    codigo: number;
    nombre: string;
}

interface Props {
    entradas: PaginatedEntradas;
    productos: Producto[];
    filters?: { search?: string; producto?: string };
    can: { create: boolean; edit: boolean; delete: boolean };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Entradas de Stock', href: '/productos/entrada-stock' },
];

const search = ref(props.filters?.search || '');
const productoFilter = ref(props.filters?.producto || '');
const showDeleteDialog = ref(false);
const entradaToDelete = ref<EntradaStock | null>(null);

const debouncedSearch = useDebounceFn(() => applyFilters(), 500);

watch(search, () => debouncedSearch());
watch(productoFilter, () => applyFilters());

const applyFilters = () => {
    router.get('/productos/entrada-stock', {
        search: search.value || undefined,
        producto: productoFilter.value || undefined,
    }, { preserveState: true, preserveScroll: true });
};

const clearFilters = () => {
    search.value = '';
    productoFilter.value = '';
};

const confirmDelete = (entrada: EntradaStock) => {
    entradaToDelete.value = entrada;
    showDeleteDialog.value = true;
};

const deleteEntrada = () => {
    if (entradaToDelete.value) {
        router.delete(`/productos/entrada-stock/${entradaToDelete.value.codigo_entrada}`, {
            onSuccess: () => {
                showDeleteDialog.value = false;
                entradaToDelete.value = null;
            },
        });
    }
};

const cancelDelete = () => {
    showDeleteDialog.value = false;
    entradaToDelete.value = null;
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
    <Head title="Entradas de Stock" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Entradas de Stock</h1>
                </div>
                <Button v-if="can.create" as-child class="gap-2">
                    <Link href="/productos/entrada-stock/create">
                        <Plus class="h-4 w-4" />
                        Nueva Entrada
                    </Link>
                </Button>
            </div>

            <Card>
                <CardContent class="pt-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-end">
                        <div class="flex-1">
                            <Label class="text-sm font-medium mb-2 block">Buscar</Label>
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input v-model="search" placeholder="Buscar por código o motivo..." class="pl-9" />
                            </div>
                        </div>
                        <div class="w-full md:w-64">
                            <Label class="text-sm font-medium mb-2 block">Producto</Label>
                            <Select v-model="productoFilter">
                                <SelectTrigger>
                                    <SelectValue placeholder="Todos los productos" />
                                </SelectTrigger>
                                <!-- ✅ CORRECTO -->
                                <SelectContent>
                                    <SelectItem value="all">Todos</SelectItem>
                                    <SelectItem v-for="producto in productos" :key="producto.codigo" :value="producto.codigo.toString()">
                                        {{ producto.nombre }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                        <Button v-if="search || productoFilter" variant="outline" @click="clearFilters" class="gap-2">
                            <X class="h-4 w-4" />
                            Limpiar
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle>Historial de Entradas</CardTitle>
                </CardHeader>
                <CardContent>
                    <div v-if="entradas.data.length === 0" class="flex flex-col items-center justify-center py-12">
                        <TrendingUp class="h-12 w-12 text-muted-foreground mb-4" />
                        <p class="text-muted-foreground mb-4 text-lg">No hay entradas de stock registradas</p>
                        <Button v-if="can.create" as-child>
                            <Link href="/productos/entrada-stock/create">
                                <Plus class="mr-2 h-4 w-4" />
                                Registrar Primera Entrada
                            </Link>
                        </Button>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-muted-foreground px-4 py-3 text-left text-sm font-medium">#</th>
                                    <th class="text-muted-foreground px-4 py-3 text-left text-sm font-medium">Código</th>
                                    <th class="text-muted-foreground px-4 py-3 text-left text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <Package class="h-4 w-4" />
                                            Producto
                                        </div>
                                    </th>
                                    <th class="text-muted-foreground px-4 py-3 text-left text-sm font-medium">Cantidad</th>
                                    <th class="text-muted-foreground px-4 py-3 text-left text-sm font-medium">Precio Unitario</th>
                                    <th class="text-muted-foreground px-4 py-3 text-left text-sm font-medium">Monto Total</th>
                                    <th class="text-muted-foreground px-4 py-3 text-left text-sm font-medium">Motivo</th>
                                    <th class="text-muted-foreground px-4 py-3 text-left text-sm font-medium">Usuario</th>
                                    <th class="text-muted-foreground px-4 py-3 text-left text-sm font-medium">Fecha</th>
                                    <th class="text-muted-foreground px-4 py-3 text-right text-sm font-medium">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(entrada, index) in entradas.data" :key="entrada.codigo_entrada" class="border-b transition-colors hover:bg-muted/50">
                                    <td class="px-4 py-3 text-sm">
                                        {{ (entradas.current_page - 1) * entradas.per_page + index + 1 }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-mono">{{ entrada.codigo_entrada }}</td>
                                    <td class="px-4 py-3 text-sm font-medium">{{ entrada.producto }}</td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="font-semibold text-green-600">+{{ entrada.cantidad }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="font-semibold text-green-600">+{{formatPrice( entrada.precio_unitario) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="font-semibold text-green-600">+{{ formatPrice(entrada.monto_total) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">{{ entrada.motivo || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ entrada.usuario || '-' }}</td>
                                    <td class="px-4 py-3 text-sm">{{ entrada.fecha }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <Button as-child variant="ghost" size="sm">
                                                <Link :href="`/productos/entrada-stock/${entrada.codigo_entrada}`">
                                                    <Eye class="h-4 w-4" />
                                                </Link>
                                            </Button>
                                            <Button v-if="can.edit" as-child variant="ghost" size="sm">
                                                <Link :href="`/productos/entrada-stock/${entrada.codigo_entrada}/edit`">
                                                    <Pencil class="h-4 w-4" />
                                                </Link>
                                            </Button>
                                            <Button v-if="can.delete" variant="ghost" size="sm" @click="confirmDelete(entrada)">
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="entradas.data.length > 0" class="mt-4">
                        <Pagination :data="entradas" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <Dialog :open="showDeleteDialog" @update:open="cancelDelete">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>¿Eliminar entrada de stock?</DialogTitle>
                    <DialogDescription>
                        Esta acción revertirá el stock del producto. ¿Estás seguro de que deseas eliminar esta entrada?
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button variant="outline" @click="cancelDelete">Cancelar</Button>
                    <Button variant="destructive" @click="deleteEntrada">Eliminar</Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
