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
import { ArrowLeft, Save } from 'lucide-vue-next';
import { ref } from 'vue';

interface Categoria {
    codigo_categoria: number;
    nombre: string;
}

interface UnidadMedida {
    codigo_medida: number;
    nombre: string;
}

interface Props {
    categorias: Categoria[];
    unidades: UnidadMedida[];
}

defineProps<Props>();

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
        title: 'Crear Producto',
        href: '/productos/create',
    },
];

const imagePreview = ref<string | null>(null);

const form = useForm({
    nombre: '',
    imagen: null as File | null,
    precio_unitario: '',
    stock: '0',
    categoria_codigo: undefined as string | undefined, // Usar undefined en lugar de ''
    unidad_codigo: undefined as string | undefined, // Usar undefined en lugar de ''
});

const handleImageChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];

    if (file) {
        form.imagen = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target?.result as string;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    // Convertir "none" o undefined a null antes de enviar
    const formData = new FormData();

    formData.append('nombre', form.nombre);
    if (form.imagen) {
        formData.append('imagen', form.imagen);
    }
    formData.append('precio_unitario', form.precio_unitario);
    formData.append('stock', form.stock);

    // Solo enviar si no es "none" ni undefined
    if (form.categoria_codigo && form.categoria_codigo !== 'none') {
        formData.append('categoria_codigo', form.categoria_codigo);
    }

    if (form.unidad_codigo && form.unidad_codigo !== 'none') {
        formData.append('unidad_codigo', form.unidad_codigo);
    }

    router.post('/productos', formData, {
        onSuccess: () => {
            router.visit('/productos');
        },
    });
};
</script>

<template>
    <Head title="Crear Producto" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Crear Producto</h1>
                    <p class="text-muted-foreground mt-1">
                        Registra un nuevo producto en el inventario
                    </p>
                </div>
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

            <Card class="max-w-3xl">
                <CardHeader>
                    <CardTitle>Información del Producto</CardTitle>
                    <CardDescription>
                        Complete los datos del nuevo producto
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form
                        @submit.prevent="submit"
                        class="space-y-6"
                    >
                        <!-- Nombre -->
                        <div class="grid gap-2">
                            <Label for="nombre">
                                Nombre del Producto
                                <span class="text-destructive">*</span>
                            </Label>
                            <Input
                                id="nombre"
                                v-model="form.nombre"
                                type="text"
                                required
                                placeholder="Nombre del producto"
                                :disabled="form.processing"
                            />
                            <InputError :message="form.errors.nombre" />
                        </div>

                        <!-- Imagen -->
                        <div class="grid gap-2">
                            <Label for="imagen">
                                Imagen del Producto
                            </Label>
                            <div class="flex items-center gap-4">
                                <div
                                    v-if="imagePreview"
                                    class="w-32 h-32 border rounded-lg overflow-hidden"
                                >
                                    <img
                                        :src="imagePreview"
                                        alt="Preview"
                                        class="w-full h-full object-cover"
                                    />
                                </div>
                                <div class="flex-1">
                                    <Input
                                        id="imagen"
                                        type="file"
                                        accept="image/*"
                                        @change="handleImageChange"
                                        :disabled="form.processing"
                                    />
                                    <p class="text-sm text-muted-foreground mt-1">
                                        Formatos: JPG, PNG. Tamaño máximo: 2MB
                                    </p>
                                </div>
                            </div>
                            <InputError :message="form.errors.imagen" />
                        </div>

                        <!-- Precio y Stock -->
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="precio_unitario">
                                    Precio Unitario (Bs.)
                                    <span class="text-destructive">*</span>
                                </Label>
                                <Input
                                    id="precio_unitario"
                                    v-model="form.precio_unitario"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    placeholder="0.00"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.precio_unitario" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="stock">
                                    Stock Inicial
                                    <span class="text-destructive">*</span>
                                </Label>
                                <Input
                                    id="stock"
                                    v-model="form.stock"
                                    type="number"
                                    min="0"
                                    required
                                    placeholder="0"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.stock" />
                            </div>
                        </div>

                        <!-- Categoría y Unidad de Medida -->
                        <div class="grid gap-6 md:grid-cols-2">
                            <div class="grid gap-2">
                                <Label for="categoria_codigo">
                                    Categoría
                                </Label>
                                <Select v-model="form.categoria_codigo">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Seleccione una categoría" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="none">Sin categoría</SelectItem>
                                        <SelectItem
                                            v-for="categoria in categorias"
                                            :key="categoria.codigo_categoria"
                                            :value="categoria.codigo_categoria.toString()"
                                        >
                                            {{ categoria.nombre }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.categoria_codigo" />
                            </div>

                            <div class="grid gap-2">
                                <Label for="unidad_codigo">
                                    Unidad de Medida
                                </Label>
                                <Select v-model="form.unidad_codigo">
                                    <SelectTrigger>
                                        <SelectValue placeholder="Seleccione una unidad" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem value="none">Sin unidad</SelectItem>
                                        <SelectItem
                                            v-for="unidad in unidades"
                                            :key="unidad.codigo_medida"
                                            :value="unidad.codigo_medida.toString()"
                                        >
                                            {{ unidad.nombre }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.unidad_codigo" />
                            </div>
                        </div>

                        <div class="flex justify-end gap-4">
                            <Button
                                type="button"
                                variant="outline"
                                @click="router.visit('/productos')"
                                :disabled="form.processing"
                            >
                                Cancelar
                            </Button>
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                class="gap-2"
                            >
                                <Save class="h-4 w-4" />
                                Guardar Producto
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
