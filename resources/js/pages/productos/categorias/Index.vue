<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import Pagination from '@/components/Pagination.vue';
import { Plus, Pencil, Trash2, Eye, Package,  Search, X } from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { ref, watch } from 'vue';
import { useDebounceFn } from '@vueuse/core';

interface Categoria {
    codigo_categoria: number;
    nombre: string;
}

interface PaginatedCategoria {
    data: Categoria[];
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
    categorias: PaginatedCategoria;
    filters: {
        search?: string;
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
        title: 'Categorias',
        href: '/categorias',
    },
];

// Estado para los filtros
const search = ref(props.filters.search || '');

// Estado para el diálogo de confirmación de eliminación
const showDeleteDialog = ref(false);
const categoriaToDelete = ref<Categoria | null>(null);

// Función debounced para búsqueda
const debouncedSearch = useDebounceFn(() => {
    applyFilters();
}, 500);

// Watch para búsqueda con debounce
watch(search, () => {
    debouncedSearch();
});

// Aplicar filtros
const applyFilters = () => {
    router.get(
        '/categorias',
        {
            search: search.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

// Limpiar filtros
const clearFilters = () => {
    search.value = '';
};

const confirmDelete = (categoria: Categoria) => {
    categoriaToDelete.value = categoria;
    showDeleteDialog.value = true;
};

const deleteProducto = () => {
    if (categoriaToDelete.value) {
        router.delete(`/categorias/${categoriaToDelete.value.codigo_categoria}`, {
            onSuccess: () => {
                showDeleteDialog.value = false;
                categoriaToDelete.value = null;
            },
        });
    }
};

const cancelDelete = () => {
    showDeleteDialog.value = false;
    categoriaToDelete.value = null;
};

</script>

<template>
    <Head title="Categorias"></Head>

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Categorias</h1>

                </div>
                <Button
                    v-if="can.create"
                    as-child
                    class="gap-2"
                >
                    <Link href="/categorias/create">
                        <Plus class="h-4 w-4" />
                        Nueva Categoria
                    </Link>
                </Button>
            </div>

            <!-- Filtros de búsqueda -->
            <Card>
                <CardContent class="pt-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-end">
                        <!-- Campo de búsqueda -->
                        <div class="flex-1">
                            <label class="text-sm font-medium mb-2 block">
                                Buscar
                            </label>
                            <div class="relative">
                                <Search class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted-foreground" />
                                <Input
                                    v-model="search"
                                    placeholder="Buscar por nombre o código..."
                                    class="pl-9"
                                />
                            </div>
                        </div>



                        <!-- Botón limpiar filtros -->
                        <Button
                            v-if="search"
                            variant="outline"
                            @click="clearFilters"
                            class="gap-2"
                        >
                            <X class="h-4 w-4" />
                            Limpiar
                        </Button>
                    </div>
                </CardContent>
            </Card>

            <!-- Tabla de categorias -->
            <Card>
                <CardHeader>
                    <CardTitle>Lista de Categorias</CardTitle>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="categorias.data.length === 0"
                        class="flex flex-col items-center justify-center py-12"
                    >
                        <p class="text-muted-foreground mb-4 text-lg">
                            No hay categorias registrados
                        </p>
                        <Button
                            v-if="can.create"
                            as-child
                        >
                            <Link href="/categorias/create">
                                <Plus class="mr-2 h-4 w-4" />
                                Crear Primer Categoria
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
                                        class="text-muted-foreground px-4 py-3 text-right text-sm font-medium"
                                    >
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(categoria, index) in categorias.data"
                                    :key="categoria.codigo_categoria"
                                    class="border-b transition-colors hover:bg-muted/50"
                                >
                                    <td class="px-4 py-3 text-sm">
                                        {{ (categorias.current_page - 1) * categorias.per_page + index + 1 }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ categoria.codigo_categoria }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium">
                                        {{ categoria.nombre }}
                                    </td>


                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <Button
                                                as-child
                                                variant="ghost"
                                                size="sm"
                                            >
                                                <Link
                                                    :href="`/categorias/${categoria.codigo_categoria}`"
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
                                                    :href="`/categorias/${categoria.codigo_categoria}/edit`"
                                                >
                                                    <Pencil class="h-4 w-4" />
                                                </Link>
                                            </Button>

                                            <Button
                                                v-if="can.delete"
                                                variant="ghost"
                                                size="sm"
                                                @click="confirmDelete(categoria)"
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
                        v-if="categorias.data.length > 0"
                        class="mt-4"
                    >
                        <Pagination :data="categorias" />
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
                    <DialogTitle>¿Eliminar categoría?</DialogTitle>
                    <DialogDescription>
                        ¿Estás seguro de que deseas eliminar la categoría
                        <strong v-if="categoriaToDelete">
                            {{ categoriaToDelete.nombre }}
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
