<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, Pencil, ImageIcon, Package } from 'lucide-vue-next';

interface Categoria {
    codigo_categoria: number;
    nombre: string;
}

interface UnidadMedida {
    codigo_unidad: number;
    nombre: string;
}

interface Producto {
    codigo_producto: number;
    nombre: string;
    descripcion: string | null;
    precio_unitario: number;
    stock: number;
    imagen: string | null;
    categoria_codigo: number;
    unidad_codigo: number;
    categoria: Categoria;
    unidadMedida: UnidadMedida;
    created_at: string;
    updated_at: string;
}

interface Props {
    producto: Producto;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Productos',
        href: '/productos',
    },
    {
        title: 'Ver Producto',
        href: `/productos/${props.producto.codigo_producto}`,
    },
];

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatCurrency = (amount: number) => {
    return new Intl.NumberFormat('es-BO', {
        style: 'currency',
        currency: 'BOB',
    }).format(amount);
};
</script>

<template>
    <Head title="Ver Producto" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Detalles del Producto</h1>
                    <p class="text-muted-foreground mt-1">
                        Información completa del producto
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        as-child
                        class="gap-2"
                    >
                        <a :href="`/productos/${producto.codigo_producto}/edit`">
                            <Pencil class="h-4 w-4" />
                            Editar
                        </a>
                    </Button>
                    <Button
                        variant="outline"
                        as-child
                        class="gap-2"
                    >
                        <a href="/productos">
                            <ArrowLeft class="h-4 w-4" />
                            Volver
                        </a>
                    </Button>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <Card class="md:col-span-2">
                    <CardHeader>
                        <CardTitle>Información General</CardTitle>
                        <CardDescription>
                            Datos principales del producto
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Código</p>
                                <p class="text-lg font-semibold">{{ producto.codigo_producto }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Nombre</p>
                                <p class="text-lg font-semibold">{{ producto.nombre }}</p>
                            </div>
                        </div>

                        <div v-if="producto.descripcion">
                            <p class="text-sm font-medium text-muted-foreground">Descripción</p>
                            <p class="text-base mt-1">{{ producto.descripcion }}</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Precio Unitario</p>
                                <p class="text-lg font-semibold text-green-600">
                                    {{ formatCurrency(producto.precio_unitario) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Stock Actual</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <Package class="h-5 w-5" />
                                    <Badge
                                        :variant="producto.stock > 0 ? 'default' : 'destructive'"
                                        class="text-base"
                                    >
                                        {{ producto.stock }} {{ producto.unidadMedida.nombre }}
                                    </Badge>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Categoría</p>
                                <Badge variant="outline" class="mt-1">
                                    {{ producto.categoria.nombre }}
                                </Badge>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Unidad de Medida</p>
                                <Badge variant="outline" class="mt-1">
                                    {{ producto.unidadMedida.nombre }}
                                </Badge>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Fecha de Creación</p>
                                <p class="text-sm mt-1">{{ formatDate(producto.created_at) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-muted-foreground">Última Actualización</p>
                                <p class="text-sm mt-1">{{ formatDate(producto.updated_at) }}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Imagen del Producto</CardTitle>
                        <CardDescription>
                            Vista previa de la imagen
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div
                            v-if="producto.imagen"
                            class="relative w-full aspect-square border rounded-lg overflow-hidden"
                        >
                            <img
                                :src="`/storage/${producto.imagen}`"
                                :alt="producto.nombre"
                                class="w-full h-full object-cover"
                            />
                        </div>
                        <div
                            v-else
                            class="w-full aspect-square border rounded-lg flex items-center justify-center bg-muted"
                        >
                            <ImageIcon class="h-16 w-16 text-muted-foreground" />
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
