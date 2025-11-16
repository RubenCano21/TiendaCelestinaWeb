<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { ArrowLeft, Pencil, Package } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';

interface Producto {
    codigo_producto: number;
    nombre: string;
    stock: number;
    precio_unitario: number;
    categoria?: {
        nombre: string;
    };
}

interface UnidadMedida {
    codigo_medida: number;
    nombre: string;
    created_at: string;
    updated_at: string;
    productos?: Producto[];
}

interface Props {
    unidad: UnidadMedida;
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
    {
        title: props.unidad.nombre,
        href: `/unidades/${props.unidad.codigo_medida}`,
    },
];

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
    }).format(value);
};
</script>

<template>
    <Head :title="`Unidad: ${unidad.nombre}`" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">{{ unidad.nombre }}</h1>
                    <p class="text-muted-foreground mt-1">
                        Detalles de la unidad de medida
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        as-child
                        class="gap-2"
                    >
                        <Link href="/unidades">
                            <ArrowLeft class="h-4 w-4" />
                            Volver
                        </Link>
                    </Button>
                    <Button
                        as-child
                        class="gap-2"
                    >
                        <Link :href="`/unidades/${unidad.codigo_medida}/edit`">
                            <Pencil class="h-4 w-4" />
                            Editar
                        </Link>
                    </Button>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Información General</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div>
                            <p class="text-sm text-muted-foreground">Código</p>
                            <p class="text-lg font-medium">{{ unidad.codigo_medida }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Nombre</p>
                            <p class="text-lg font-medium">{{ unidad.nombre }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Fecha de Creación</p>
                            <p class="text-lg">{{ formatDate(unidad.created_at) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-muted-foreground">Última Actualización</p>
                            <p class="text-lg">{{ formatDate(unidad.updated_at) }}</p>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle class="flex items-center gap-2">
                            <Package class="h-5 w-5" />
                            Productos con esta Unidad
                        </CardTitle>
                        <CardDescription>
                            {{ unidad.productos?.length || 0 }} producto(s) registrado(s)
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="!unidad.productos || unidad.productos.length === 0"
                            class="text-center py-8 text-muted-foreground"
                        >
                            No hay productos con esta unidad de medida
                        </div>
                        <div
                            v-else
                            class="space-y-3"
                        >
                            <div
                                v-for="producto in unidad.productos"
                                :key="producto.codigo_producto"
                                class="flex items-center justify-between p-3 border rounded-lg hover:bg-muted/50 transition-colors"
                            >
                                <div>
                                    <p class="font-medium">{{ producto.nombre }}</p>
                                    <p class="text-sm text-muted-foreground">
                                        {{ formatCurrency(producto.precio_unitario) }}
                                        <span v-if="producto.categoria">
                                            | {{ producto.categoria.nombre }}
                                        </span>
                                    </p>
                                </div>
                                <Badge :variant="producto.stock > 0 ? 'default' : 'destructive'">
                                    Stock: {{ producto.stock }}
                                </Badge>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
