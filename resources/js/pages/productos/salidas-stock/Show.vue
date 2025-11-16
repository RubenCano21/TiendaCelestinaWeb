<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Badge } from '@/components/ui/badge';
import { ArrowLeft, Package, Calendar, User, Edit } from 'lucide-vue-next';

interface Producto {
    codigo_producto: number;
    nombre: string;
    stock: number;
    categoria: {
        nombre: string;
    };
    unidadMedida: {
        nombre: string;
    };
}

interface SalidaStock {
    codigo_salida: number;
    codigo_producto: number;
    cantidad: number;
    motivo: string;
    usuario: string;
    fecha: string;
    created_at: string;
    updated_at: string;
    producto: Producto;
}

interface Props {
    salida: SalidaStock;
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
    {
        title: 'Detalle de Salida',
        href: `/productos/salida-stock/${props.salida.codigo_salida}`,
    },
];

const formatDate = (date: string) => {
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
    <Head title="Ver Salida de Stock" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Detalles de la Salida</h1>
                    <p class="text-muted-foreground mt-1">
                        Información completa de la salida de stock
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        as-child
                        class="gap-2"
                    >
                        <a :href="`/productos/salida-stock/${salida.codigo_salida}/edit`">
                            <Edit class="h-4 w-4" />
                            Editar
                        </a>
                    </Button>
                    <Button
                        variant="outline"
                        as-child
                        class="gap-2"
                    >
                        <a href="/productos/salida-stock">
                            <ArrowLeft class="h-4 w-4" />
                            Volver
                        </a>
                    </Button>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Información de la Salida</CardTitle>
                        <CardDescription>
                            Datos del movimiento de stock
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Código de Salida</p>
                            <p class="text-lg font-semibold">{{ salida.codigo_salida }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Cantidad</p>
                            <div class="flex items-center gap-2 mt-1">
                                <Package class="h-5 w-5 text-red-600" />
                                <Badge variant="destructive" class="text-base">
                                    -{{ salida.cantidad }} {{ salida.producto.unidadMedida?.nombre || '' }}
                                </Badge>
                            </div>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-muted-foreground mb-2">
                                <Calendar class="h-4 w-4 inline mr-1" />
                                Fecha del Movimiento
                            </p>
                            <p class="text-base">{{ formatDate(salida.fecha) }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-muted-foreground mb-2">
                                <User class="h-4 w-4 inline mr-1" />
                                Usuario que Registró
                            </p>
                            <Badge variant="outline">{{ salida.usuario }}</Badge>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Motivo</p>
                            <p class="text-base mt-1 p-3 bg-muted rounded-lg">
                                {{ salida.motivo }}
                            </p>
                        </div>

                        <div class="pt-4 border-t">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Fecha de Creación</p>
                                    <p class="text-sm mt-1">{{ formatDate(salida.created_at) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Última Actualización</p>
                                    <p class="text-sm mt-1">{{ formatDate(salida.updated_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <CardTitle>Información del Producto</CardTitle>
                        <CardDescription>
                            Detalles del producto afectado
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Código del Producto</p>
                            <p class="text-lg font-semibold">{{ salida.producto.codigo_producto }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Nombre del Producto</p>
                            <p class="text-lg font-semibold">{{ salida.producto.nombre }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Categoría</p>
                            <Badge variant="outline" class="mt-1">
                                {{ salida.producto.categoria.nombre }}
                            </Badge>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Stock Actual</p>
                            <div class="flex items-center gap-2 mt-1">
                                <Package class="h-5 w-5" />
                                <Badge
                                    :variant="salida.producto.stock > 0 ? 'default' : 'destructive'"
                                    class="text-base"
                                >
                                    {{ salida.producto.stock }} {{ salida.producto.unidadMedida?.nombre || '' }}
                                </Badge>
                            </div>
                        </div>

                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <p class="text-sm font-medium text-red-800">Impacto del Movimiento</p>
                            <p class="text-sm text-red-700 mt-1">
                                Este movimiento redujo el stock en
                                <strong>-{{ salida.cantidad }}</strong>
                                {{ salida.producto.unidadMedida?.nombre || '' }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
