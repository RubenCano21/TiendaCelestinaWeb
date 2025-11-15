<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import Pagination from '@/components/Pagination.vue';
import { Plus, Pencil, Trash2, Eye, Mail, Phone, MapPin } from 'lucide-vue-next';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { ref } from 'vue';

interface Cliente {
    id: number;
    nombre: string;
    apellido: string;
    nombre_completo: string;
    email: string;
    telefono: string | null;
    domicilio: string | null;
    created_at: string;
    updated_at: string;
}

interface PaginatedClientes {
    data: Cliente[];
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
    clientes: PaginatedClientes;
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
        title: 'Clientes',
        href: '/clientes',
    },
];

// Estado para el diálogo de confirmación de eliminación
const showDeleteDialog = ref(false);
const clienteToDelete = ref<Cliente | null>(null);

const confirmDelete = (cliente: Cliente) => {
    clienteToDelete.value = cliente;
    showDeleteDialog.value = true;
};

const deleteCliente = () => {
    if (clienteToDelete.value) {
        router.delete(`/clientes/${clienteToDelete.value.id}`, {
            onSuccess: () => {
                showDeleteDialog.value = false;
                clienteToDelete.value = null;
            },
        });
    }
};

const cancelDelete = () => {
    showDeleteDialog.value = false;
    clienteToDelete.value = null;
};
</script>

<template>
    <Head title="Clientes" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Clientes</h1>
                    <p class="text-muted-foreground mt-1">
                        Gestiona los clientes de tu tienda
                    </p>
                </div>
                <Button
                    v-if="can.create"
                    as-child
                    class="gap-2"
                >
                    <Link href="/clientes/create">
                        <Plus class="h-4 w-4" />
                        Nuevo Cliente
                    </Link>
                </Button>
            </div>

            <!-- Tabla de Clientes -->
            <Card>
                <CardHeader>
                    <CardTitle>Lista de Clientes</CardTitle>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="clientes.data.length === 0"
                        class="flex flex-col items-center justify-center py-12"
                    >
                        <p class="text-muted-foreground mb-4 text-lg">
                            No hay clientes registrados
                        </p>
                        <Button
                            v-if="can.create"
                            as-child
                        >
                            <Link href="/clientes/create">
                                <Plus class="mr-2 h-4 w-4" />
                                Crear Primer Cliente
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
                                        Codigo
                                    </th>
                                    <th
                                        class="text-muted-foreground px-4 py-3 text-left text-sm font-medium"
                                    >
                                        Nombre Completo
                                    </th>
                                    <th
                                        class="text-muted-foreground px-4 py-3 text-left text-sm font-medium"
                                    >
                                        <div class="flex items-center gap-2">
                                            <Mail class="h-4 w-4" />
                                            Email
                                        </div>
                                    </th>
                                    <th
                                        class="text-muted-foreground px-4 py-3 text-left text-sm font-medium"
                                    >
                                        <div class="flex items-center gap-2">
                                            <Phone class="h-4 w-4" />
                                            Teléfono
                                        </div>
                                    </th>
                                    <th
                                        class="text-muted-foreground px-4 py-3 text-left text-sm font-medium"
                                    >
                                        <div class="flex items-center gap-2">
                                            <MapPin class="h-4 w-4" />
                                            Domicilio
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
                                    v-for="(cliente, index) in clientes.data"
                                    :key="cliente.id"
                                    class="border-b transition-colors hover:bg-muted/50"
                                >
                                    <td class="px-4 py-3 text-sm">
                                        {{ (clientes.current_page - 1) * clientes.per_page + index + 1 }}
                                    </td>
                                    <td class="px-4 py-3 text-sm font-medium">
                                        {{ cliente.nombre_completo }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        {{ cliente.email }}
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span v-if="cliente.telefono">
                                            {{ cliente.telefono }}
                                        </span>
                                        <span
                                            v-else
                                            class="text-muted-foreground"
                                        >
                                            -
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span v-if="cliente.domicilio">
                                            {{ cliente.domicilio }}
                                        </span>
                                        <span
                                            v-else
                                            class="text-muted-foreground"
                                        >
                                            -
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <Button
                                                as-child
                                                variant="ghost"
                                                size="sm"
                                            >
                                                <Link
                                                    :href="`/clientes/${cliente.id}`"
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
                                                    :href="`/clientes/${cliente.id}/edit`"
                                                >
                                                    <Pencil class="h-4 w-4" />
                                                </Link>
                                            </Button>

                                            <Button
                                                v-if="can.delete"
                                                variant="ghost"
                                                size="sm"
                                                @click="confirmDelete(cliente)"
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
                        v-if="clientes.data.length > 0"
                        class="mt-4"
                    >
                        <Pagination :data="clientes" />
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
                    <DialogTitle>¿Eliminar cliente?</DialogTitle>
                    <DialogDescription>
                        ¿Estás seguro de que deseas eliminar al cliente
                        <strong v-if="clienteToDelete">
                            {{ clienteToDelete.nombre_completo }}
                        </strong>
                        ? Esta acción no se puede deshacer.
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
                        @click="deleteCliente"
                    >
                        Eliminar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
