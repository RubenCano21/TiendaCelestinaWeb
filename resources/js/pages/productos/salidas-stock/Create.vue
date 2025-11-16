<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { ArrowLeft, Save, Package, AlertTriangle } from 'lucide-vue-next';
import { computed } from 'vue';

interface Producto {
    codigo_producto: number;
    nombre: string;
    stock_actual: number;
    categoria: string;
    unidad_medida: string;
}

interface Props {
    productos: Producto[];
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
        title: 'Nueva Salida',
        href: '/productos/salida-stock/create',
    },
];

const form = useForm({
    codigo_producto: '' as number | string,
    cantidad: 0,
    motivo: '',
    fecha: new Date().toISOString().split('T')[0],
});

const selectedProducto = computed(() => {
    if (!form.codigo_producto || form.codigo_producto === '') return null;
    return props.productos.find(
        (p) => p.codigo_producto === Number(form.codigo_producto)
    );
});

const stockInsuficiente = computed(() => {
    if (!selectedProducto.value) return false;
    return form.cantidad > selectedProducto.value.stock_actual;
});

const submit = () => {
    form.post('/productos/salida-stock', {
        onSuccess: () => {
            router.visit('/productos/salida-stock');
        },
    });
};
</script>

<template>
    <Head title="Nueva Salida de Stock" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Nueva Salida de Stock</h1>
                    <p class="text-muted-foreground mt-1">
                        Registra una nueva salida de stock
                    </p>
                </div>
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

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Información de la Salida</CardTitle>
                    <CardDescription>
                        Completa los datos de la salida de stock
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form
                        @submit.prevent="submit"
                        class="space-y-6"
                    >
                        <div class="grid gap-2">
                            <Label for="codigo_producto">
                                Producto
                                <span class="text-destructive">*</span>
                            </Label>
                            <Select
                                v-model="form.codigo_producto"
                                :disabled="form.processing"
                            >
                                <SelectTrigger id="codigo_producto">
                                    <SelectValue placeholder="Selecciona un producto" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="producto in productos"
                                        :key="producto.codigo_producto"
                                        :value="producto.codigo_producto"
                                    >
                                        {{ producto.nombre }} - {{ producto.categoria }}
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.codigo_producto" />

                            <div
                                v-if="selectedProducto"
                                class="flex items-center gap-2 p-3 bg-muted rounded-lg text-sm"
                            >
                                <Package class="h-4 w-4" />
                                <span>
                                    Stock disponible: <strong>{{ selectedProducto.stock_actual }}</strong>
                                    {{ selectedProducto.unidad_medida }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="cantidad">
                                    Cantidad
                                    <span class="text-destructive">*</span>
                                </Label>
                                <Input
                                    id="cantidad"
                                    v-model.number="form.cantidad"
                                    type="number"
                                    min="1"
                                    required
                                    placeholder="0"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.cantidad" />

                                <div
                                    v-if="stockInsuficiente"
                                    class="flex items-center gap-2 p-2 bg-red-50 border border-red-200 rounded text-sm text-red-700"
                                >
                                    <AlertTriangle class="h-4 w-4" />
                                    <span>Stock insuficiente</span>
                                </div>
                            </div>

                            <div class="grid gap-2">
                                <Label for="fecha">
                                    Fecha
                                    <span class="text-destructive">*</span>
                                </Label>
                                <Input
                                    id="fecha"
                                    v-model="form.fecha"
                                    type="date"
                                    required
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.fecha" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="motivo">
                                Motivo
                                <span class="text-destructive">*</span>
                            </Label>
                            <Select
                                v-model="form.motivo"
                                :disabled="form.processing"
                            >
                                <SelectTrigger id="motivo">
                                    <SelectValue placeholder="Selecciona el motivo" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="Venta contado">
                                        Venta contado
                                    </SelectItem>
                                    <SelectItem value="Venta al crédito">
                                        Venta al crédito
                                    </SelectItem>
                                </SelectContent>
                            </Select>
                            <InputError :message="form.errors.motivo" />
                        </div>

                        <div class="flex justify-end gap-4">
                            <Button
                                type="button"
                                variant="outline"
                                @click="router.visit('/productos/salida-stock')"
                                :disabled="form.processing"
                            >
                                Cancelar
                            </Button>
                            <Button
                                type="submit"
                                :disabled="form.processing || stockInsuficiente"
                                class="gap-2"
                            >
                                <Save class="h-4 w-4" />
                                Registrar Salida
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
