<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Head } from '@inertiajs/vue3';
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend, type ChartOptions } from 'chart.js';
import { computed } from 'vue';

ChartJS.register(ArcElement, Tooltip, Legend);

interface CategoryData {
    name: string;
    value: number;
}

interface Props {
    productosPorCategoria: CategoryData[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// Colores para el gráfico de pie
const COLORS = [
    '#0088FE', // Azul
    '#00C49F', // Verde
    '#FFBB28', // Amarillo
    '#FF8042', // Naranja
    '#8884D8', // Púrpura
    '#82CA9D', // Verde claro
    '#FFC658', // Amarillo oscuro
    '#FF6B9D', // Rosa
    '#8DD1E1', // Cyan
    '#A4DE6C', // Verde lima
];

// Calcular total de productos
const totalProductos = computed(() =>
    props.productosPorCategoria.reduce((sum, item) => sum + item.value, 0)
);

// Configurar datos para vue-chartjs
const chartData = computed(() => ({
    labels: props.productosPorCategoria.map(item => item.name),
    datasets: [
        {
            data: props.productosPorCategoria.map(item => item.value),
            backgroundColor: COLORS.slice(0, props.productosPorCategoria.length),
            borderColor: '#fff',
            borderWidth: 2,
        },
    ],
}));

// Opciones del gráfico
const chartOptions: ChartOptions<'pie'> = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                padding: 15,
                font: {
                    size: 12,
                },
            },
        },
        tooltip: {
            callbacks: {
                label: function (context) {
                    const label = context.label || '';
                    const value = context.parsed || 0;
                    const total = (context.dataset.data as number[]).reduce((a, b) => a + b, 0);
                    const percentage = ((value / total) * 100).toFixed(1);
                    return `${label}: ${value} (${percentage}%)`;
                },
            },
        },
    },
};

</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div>
                <h1 class="text-3xl font-bold tracking-tight">Dashboard</h1>
                <p class="text-muted-foreground mt-1">
                    Resumen general del sistema
                </p>
            </div>

            <!-- Tarjeta de resumen -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader>
                        <CardDescription>Total de Productos</CardDescription>
                        <CardTitle class="text-4xl">{{ totalProductos }}</CardTitle>
                    </CardHeader>
                </Card>

                <Card>
                    <CardHeader>
                        <CardDescription>Categorías</CardDescription>
                        <CardTitle class="text-4xl">{{ productosPorCategoria.length }}</CardTitle>
                    </CardHeader>
                </Card>

                <Card>
                    <CardHeader>
                        <CardDescription>Categoría Principal</CardDescription>
                        <CardTitle class="text-2xl">
                            {{ productosPorCategoria[0]?.name || 'N/A' }}
                        </CardTitle>
                    </CardHeader>
                </Card>
            </div>

            <!-- Gráfico de pie -->
            <div class="grid gap-4 md:grid-cols-2">
                <Card>
                    <CardHeader>
                        <CardTitle>Productos por Categoría</CardTitle>
                        <CardDescription>
                            Distribución de productos según su categoría
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="h-[350px]">
                            <Pie :data="chartData" :options="chartOptions" />
                        </div>
                    </CardContent>
                </Card>

                <!-- Tabla de detalles -->
                <Card>
                    <CardHeader>
                        <CardTitle>Detalle por Categoría</CardTitle>
                        <CardDescription>
                            Cantidad de productos en cada categoría
                        </CardDescription>
                    </CardHeader>
                    <CardContent>
                        <div class="space-y-4">
                            <div
                                v-for="(item, index) in productosPorCategoria"
                                :key="index"
                                class="flex items-center justify-between"
                            >
                                <div class="flex items-center gap-3">
                                    <div
                                        class="h-4 w-4 rounded"
                                        :style="{ backgroundColor: COLORS[index % COLORS.length] }"
                                    />
                                    <span class="text-sm font-medium">{{ item.name }}</span>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-bold">{{ item.value }}</span>
                                    <span class="text-muted-foreground text-sm">
                                        {{ ((item.value / totalProductos) * 100).toFixed(1) }}%
                                    </span>
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
