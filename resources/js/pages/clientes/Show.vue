<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Pencil, Trash2, Mail, Phone, MapPin, Calendar, CheckCircle } from 'lucide-vue-next';
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
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

interface Props {
    cliente: Cliente;
    can: {
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
        title: 'Clientes',
        href: '/clientes',
    },
    {
        title: props.cliente.nombre_completo,
        href: `/clientes/${props.cliente.id}`,
    },
];

// Estado para el diálogo de confirmación de eliminación
const showDeleteDialog = ref(false);

const confirmDelete = () => {
    showDeleteDialog.value = true;
};

const deleteCliente = () => {
    router.delete(`/clientes/${props.cliente.id}`, {
        onSuccess: () => {
            router.visit('/clientes');
        },
    });
};

const cancelDelete = () => {
    showDeleteDialog.value = false;
};

// Formato de fecha
const formatDate = (date: string | null) => {
    if (!date) return '-';
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};
</script>

<template>
    <Head :title="`Cliente - ${cliente.nombre_completo}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Button
                        as-child
                        variant="outline"
                        size="icon"
                    >
                        <Link href="/clientes">
                            <ArrowLeft class="h-4 w-4" />
                        </Link>
                    </Button>
                    <div>
                        <h1 class="text-3xl font-bold tracking-tight">
                            {{ cliente.nombre_completo }}
                        </h1>
                        <p class="text-muted-foreground mt-1">
                            Detalles del cliente
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <Button
                        v-if="can.edit"
                        as-child
                        variant="outline"
                        class="gap-2"
                    >
                        <Link :href="`/clientes/${cliente.id}/edit`">
                            <Pencil class="h-4 w-4" />
                            Editar
                        </Link>
                    </Button>
                    <Button
                        v-if="can.delete"
                        variant="destructive"
                        class="gap-2"
                        @click="confirmDelete"
                    >
                        <Trash2 class="h-4 w-4" />
                        Eliminar
                    </Button>
                </div>
            </div>

            <!-- Información del Cliente -->
            <div class="grid gap-4 md:grid-cols-2">
                <!-- Información Personal -->
                <Card>
                    <CardHeader>
                        <CardTitle>Información Personal</CardTitle>
                        <CardDescription>Datos personales del cliente</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-1">
                            <p class="text-muted-foreground text-sm font-medium">
                                Nombre
                            </p>
                            <p class="text-sm">{{ cliente.nombre }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-muted-foreground text-sm font-medium">
                                Apellido
                            </p>
                            <p class="text-sm">{{ cliente.apellido }}</p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-muted-foreground text-sm font-medium">
                                ID del Cliente
                            </p>
                            <p class="text-sm">{{ cliente.id }}</p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Información de Contacto -->
                <Card>
                    <CardHeader>
                        <CardTitle>Información de Contacto</CardTitle>
                        <CardDescription>Datos de contacto del cliente</CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="space-y-1">
                            <p class="text-muted-foreground text-sm font-medium">
                                Email
                            </p>
                            <div class="flex items-center gap-2">
                                <Mail class="text-muted-foreground h-4 w-4" />
                                <p class="text-sm">{{ cliente.email }}</p>
                                <CheckCircle
                                    v-if="cliente.email_verified_at"
                                    class="h-4 w-4 text-green-600"
                                    title="Email verificado"
                                />
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-muted-foreground text-sm font-medium">
                                Teléfono
                            </p>
                            <div
                                v-if="cliente.telefono"
                                class="flex items-center gap-2"
                            >
                                <Phone class="text-muted-foreground h-4 w-4" />
                                <p class="text-sm">{{ cliente.telefono }}</p>
                            </div>
                            <p
                                v-else
                                class="text-muted-foreground text-sm"
                            >
                                No especificado
                            </p>
                        </div>
                        <div class="space-y-1">
                            <p class="text-muted-foreground text-sm font-medium">
                                Domicilio
                            </p>
                            <div
                                v-if="cliente.domicilio"
                                class="flex items-center gap-2"
                            >
                                <MapPin class="text-muted-foreground h-4 w-4" />
                                <p class="text-sm">{{ cliente.domicilio }}</p>
                            </div>
                            <p
                                v-else
                                class="text-muted-foreground text-sm"
                            >
                                No especificado
                            </p>
                        </div>
                    </CardContent>
                </Card>

                <!-- Información del Sistema -->
                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>Información del Sistema</CardTitle>
                        <CardDescription>Fechas de registro y actualización</CardDescription>
                    </CardHeader>
                    <CardContent class="grid gap-4 md:grid-cols-2">
                        <div class="space-y-1">
                            <p class="text-muted-foreground text-sm font-medium">
                                Fecha de Registro
                            </p>
                            <div class="flex items-center gap-2">
                                <Calendar class="text-muted-foreground h-4 w-4" />
                                <p class="text-sm">{{ formatDate(cliente.created_at) }}</p>
                            </div>
                        </div>
                        <div class="space-y-1">
                            <p class="text-muted-foreground text-sm font-medium">
                                Última Actualización
                            </p>
                            <div class="flex items-center gap-2">
                                <Calendar class="text-muted-foreground h-4 w-4" />
                                <p class="text-sm">{{ formatDate(cliente.updated_at) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
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
                        <strong>{{ cliente.nombre_completo }}</strong>?
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
                        @click="deleteCliente"
                    >
                        Eliminar
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
