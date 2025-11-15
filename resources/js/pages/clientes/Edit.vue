<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import InputError from '@/components/InputError.vue';
import { ArrowLeft, Save } from 'lucide-vue-next';

interface Cliente {
    id: number;
    nombre: string;
    apellido: string;
    email: string;
    telefono: string | null;
    domicilio: string | null;
}

interface Props {
    cliente: Cliente;
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
        title: `${props.cliente.nombre} ${props.cliente.apellido}`,
        href: `/clientes/${props.cliente.id}`,
    },
    {
        title: 'Editar',
        href: `/clientes/${props.cliente.id}/edit`,
    },
];

const form = useForm({
    nombre: props.cliente.nombre,
    apellido: props.cliente.apellido,
    email: props.cliente.email,
    password: '',
    password_confirmation: '',
    telefono: props.cliente.telefono || '',
    domicilio: props.cliente.domicilio || '',
});

const submit = () => {
    form.put(`/clientes/${props.cliente.id}`, {
        onSuccess: () => {
            router.visit(`/clientes/${props.cliente.id}`);
        },
    });
};
</script>

<template>
    <Head :title="`Editar Cliente - ${cliente.nombre} ${cliente.apellido}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Editar Cliente</h1>
                    <p class="text-muted-foreground mt-1">
                        Actualiza la información del cliente
                    </p>
                </div>
                <Button
                    variant="outline"
                    as-child
                    class="gap-2"
                >
                    <a :href="`/clientes/${cliente.id}`">
                        <ArrowLeft class="h-4 w-4" />
                        Volver
                    </a>
                </Button>
            </div>

            <!-- Formulario -->
            <Card class="max-w-3xl">
                <CardHeader>
                    <CardTitle>Información del Cliente</CardTitle>
                    <CardDescription>
                        Modifica los datos del cliente. Los campos marcados con (*) son obligatorios.
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form
                        @submit.prevent="submit"
                        class="space-y-6"
                    >
                        <!-- Nombre y Apellido -->
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="nombre">
                                    Nombre
                                    <span class="text-destructive">*</span>
                                </Label>
                                <Input
                                    id="nombre"
                                    v-model="form.nombre"
                                    type="text"
                                    required
                                    placeholder="Nombre del cliente"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.nombre" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="apellido">
                                    Apellido
                                    <span class="text-destructive">*</span>
                                </Label>
                                <Input
                                    id="apellido"
                                    v-model="form.apellido"
                                    type="text"
                                    required
                                    placeholder="Apellido del cliente"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.apellido" />
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="grid gap-2">
                            <Label for="email">
                                Email
                                <span class="text-destructive">*</span>
                            </Label>
                            <Input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                placeholder="correo@ejemplo.com"
                                :disabled="form.processing"
                            />
                            <InputError :message="form.errors.email" />
                        </div>

                        <!-- Contraseñas -->
                        <div class="space-y-2">
                            <p class="text-muted-foreground text-sm">
                                Deja estos campos vacíos si no deseas cambiar la contraseña
                            </p>
                            <div class="grid gap-6 md:grid-cols-2">
                                <div class="grid gap-2">
                                    <Label for="password">Nueva Contraseña</Label>
                                    <Input
                                        id="password"
                                        v-model="form.password"
                                        type="password"
                                        placeholder="••••••••"
                                        :disabled="form.processing"
                                    />
                                    <InputError :message="form.errors.password" />
                                </div>

                                <div class="grid gap-2">
                                    <Label for="password_confirmation">
                                        Confirmar Nueva Contraseña
                                    </Label>
                                    <Input
                                        id="password_confirmation"
                                        v-model="form.password_confirmation"
                                        type="password"
                                        placeholder="••••••••"
                                        :disabled="form.processing"
                                    />
                                    <InputError :message="form.errors.password_confirmation" />
                                </div>
                            </div>
                        </div>

                        <!-- Teléfono y Domicilio -->
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="telefono">Teléfono</Label>
                                <Input
                                    id="telefono"
                                    v-model="form.telefono"
                                    type="tel"
                                    placeholder="Ej: 70123456"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.telefono" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="domicilio">Domicilio</Label>
                                <Input
                                    id="domicilio"
                                    v-model="form.domicilio"
                                    type="text"
                                    placeholder="Dirección del cliente"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.domicilio" />
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="flex items-center justify-end gap-4 pt-4">
                            <Button
                                type="button"
                                variant="outline"
                                :disabled="form.processing"
                                @click="router.visit(`/clientes/${cliente.id}`)"
                            >
                                Cancelar
                            </Button>
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="gap-2"
                            >
                                <Save class="h-4 w-4" />
                                {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
