<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import Pagination from '@/components/Pagination.vue';
import { Plus, Pencil, Trash2, Eye, Ruler, Search, X } from 'lucide-vue-next';
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

interface UnidadMedida {
    codigo_medida: number;
    nombre: string;
}

interface PaginatedUnidad {
    data: UnidadMedida[];
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
    unidades: PaginatedUnidad;
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
        title: 'Unidades de Medida',
        href: '/unidades',
    },
];

const search = ref(props.filters.search || '');
const showDeleteDialog = ref(false);
const unidadToDelete = ref<UnidadMedida | null>(null);

const debouncedSearch = useDebounceFn(() => {
    applyFilters();
}, 500);

watch(search, () => {
    debouncedSearch();
});

const applyFilters = () => {
    router.get(
        '/unidades',
        {
            search: search.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    );
};

const clearFilters = () => {
    search.value = '';
};

const confirmDelete = (unidad: UnidadMedida) => {
    unidadToDelete.value = unidad;
    showDeleteDialog.value = true;
};

const deleteUnidad = () => {
    if (unidadToDelete.value) {
        router.delete(`/unidades/${unidadToDelete.value.codigo_medida}`, {
            onSuccess: () => {
                showDeleteDialog.value = false;
                unidadToDelete.value = null;
            },
        });
    }
};

const cancelDelete = () => {
    showDeleteDialog.value = false;
    unidadToDelete.value = null;
};
</script>

<template>
    <Head title="Unidades de Medida" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Unidades de Medida</h1>
                </div>
                <Button
                    v-if="can.create"
                    as-child
                    class="gap-2"
                >
                    <Link href="/unidades/create">
                        <Plus class="h-4 w-4" />
                        Nueva Unidad
                    </Link>
                </Button>
            </div>

            <Card>
                <CardContent class="pt-6">
                    <div class="flex flex-col gap-4 md:flex-row md:items-end">
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

            <Card>
                <CardHeader>
                    <CardTitle>Lista de Unidades de Medida</CardTitle>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="unidades.data.length === 0"
                        class="flex flex-col items-center justify-center py-12"
                    >
                        <p class="text-muted-foreground mb-4 text-lg">
                            No hay unidades de medida registradas
                        </p>
                        <Button
                            v-if="can.create"
                            as-child
                        >
                            <Link href="/unidades/create">
                                <Plus class="mr-2 h-4 w-4" />
                                Crear Primera Unidad
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
                                    <th class="text-muted-foreground px-4 py-3 text-left text-sm font-medium">
                                        #
                                    </th>
                                    <th class="text-muted-foreground px-4 py-3 text-left text-sm font-medium">
                                        Código
                                    </th>
                                    <th class="text-muted-foreground px-4 py-3 text-left text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <Ruler class="h-4 w-4" />
                                            Nombre
                                        </div>
                                    </th>
                                    <th class="text-muted-foreground px-4 py-3 text-right text-sm font-medium">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(unidad, index) in unidades.data"
                                    :key="unidad.codigo_medida"
                                    class="border-b transition-colors hover:bg-muted/50"
                                >
                                    <td class="px-4 py-3 text-sm">
                                        {{ (unidades.current_page - 1) * unidades.per_page + index + 1 }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ unidad.codigo_medida }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium">
                                        {{ unidad.nombre }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <Button
                                                as-child
                                                variant="ghost"
                                                size="sm"
                                            >
                                                <Link :href="`/unidades/${unidad.codigo_medida}`">
                                                    <Eye class="h-4 w-4" />
                                                </Link>
                                            </Button>

                                            <Button
                                                v-if="can.edit"
                                                as-child
                                                variant="ghost"
                                                size="sm"
                                            >
                                                <Link :href="`/unidades/${unidad.codigo_medida}/edit`">
                                                    <Pencil class="h-4 w-4" />
                                                </Link>
                                            </Button>

                                            <Button
                                                v-if="can.delete"
                                                variant="ghost"
                                                size="sm"
                                                @click="confirmDelete(unidad)"
                                            >
                                                <Trash2 class="h-4 w-4 text-destructive" />
                                            </Button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div
                        v-if="unidades.data.length > 0"
                        class="mt-4"
                    >
                        <Pagination :data="unidades" />
                    </div>
                </CardContent>
            </Card>
        </div>

        <Dialog
            :open="showDeleteDialog"
            @update:open="cancelDelete"
        >
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>¿Eliminar unidad de medida?</DialogTitle>
                    <DialogDescription>
                        ¿Estás seguro de que deseas eliminar la unidad de medida
                        <strong v-if="unidadToDelete">
                            {{ unidadToDelete.nombre }}
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
                        @click="deleteUnidad"
                    >
                        Eliminar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
