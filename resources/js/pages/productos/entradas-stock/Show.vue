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

interface EntradaStock {
    codigo_entrada: number;
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
    entrada: EntradaStock;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Entradas de Stock',
        href: '/productos/entrada-stock',
    },
    {
        title: 'Detalle de Entrada',
        href: `/productos/entrada-stock/${props.entrada.codigo_entrada}`,
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
    <Head title="Ver Entrada de Stock" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Detalles de la Entrada</h1>
                    <p class="text-muted-foreground mt-1">
                        Información completa de la entrada de stock
                    </p>
                </div>
                <div class="flex gap-2">
                    <Button
                        variant="outline"
                        as-child
                        class="gap-2"
                    >
                        <a :href="`/productos/entrada-stock/${entrada.codigo_entrada}/edit`">
                            <Edit class="h-4 w-4" />
                            Editar
                        </a>
                    </Button>
                    <Button
                        variant="outline"
                        as-child
                        class="gap-2"
                    >
                        <a href="/productos/entrada-stock">
                            <ArrowLeft class="h-4 w-4" />
                            Volver
                        </a>
                    </Button>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Información de la Entrada</CardTitle>
                        <CardDescription>
                            Datos del movimiento de stock
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Código de Entrada</p>
                            <p class="text-lg font-semibold">{{ entrada.codigo_entrada }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Cantidad</p>
                            <div class="flex items-center gap-2 mt-1">
                                <Package class="h-5 w-5 text-green-600" />
                                <Badge variant="default" class="text-base bg-green-600">
                                    +{{ entrada.cantidad }} {{ entrada.producto.unidadMedida?.nombre || '' }}
                                </Badge>
                            </div>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-muted-foreground mb-2">
                                <Calendar class="h-4 w-4 inline mr-1" />
                                Fecha del Movimiento
                            </p>
                            <p class="text-base">{{ formatDate(entrada.fecha) }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-muted-foreground mb-2">
                                <User class="h-4 w-4 inline mr-1" />
                                Usuario que Registró
                            </p>
                            <Badge variant="outline">{{ entrada.usuario }}</Badge>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Motivo</p>
                            <p class="text-base mt-1 p-3 bg-muted rounded-lg">
                                {{ entrada.motivo }}
                            </p>
                        </div>

                        <div class="pt-4 border-t">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Fecha de Creación</p>
                                    <p class="text-sm mt-1">{{ formatDate(entrada.created_at) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-muted-foreground">Última Actualización</p>
                                    <p class="text-sm mt-1">{{ formatDate(entrada.updated_at) }}</p>
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
                            <p class="text-lg font-semibold">{{ entrada.producto.codigo_producto }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Nombre del Producto</p>
                            <p class="text-lg font-semibold">{{ entrada.producto.nombre }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Categoría</p>
                            <Badge variant="outline" class="mt-1">
                                {{ entrada.producto.categoria.nombre }}
                            </Badge>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-muted-foreground">Stock Actual</p>
                            <div class="flex items-center gap-2 mt-1">
                                <Package class="h-5 w-5" />
                                <Badge
                                    :variant="entrada.producto.stock > 0 ? 'default' : 'destructive'"
                                    class="text-base"
                                >
                                    {{ entrada.producto.stock }} {{ entrada.producto.unidadMedida?.nombre || '' }}
                                </Badge>
                            </div>
                        </div>

                        <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-sm font-medium text-green-800">Impacto del Movimiento</p>
                            <p class="text-sm text-green-700 mt-1">
                                Este movimiento incrementó el stock en
                                <strong>+{{ entrada.cantidad }}</strong>
                                {{ entrada.producto.unidadMedida?.nombre || '' }}
                            </p>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
