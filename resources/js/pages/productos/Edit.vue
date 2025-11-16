<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import type { BreadcrumbItem } from '@/types';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Textarea } from '@/components/ui/textarea';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import InputError from '@/components/InputError.vue';
import { ArrowLeft, Save, ImageIcon } from 'lucide-vue-next';
import { ref } from 'vue';

interface Categoria {
    codigo_categoria: number;
    nombre: string;
}

interface UnidadMedida {
    codigo_unidad: number;
    nombre: string;
    abreviatura?: string;
}

interface Producto {
    codigo_producto: number;
    nombre: string;
    descripcion: string | null;
    precio_unitario: number;
    stock: number;
    imagen: string | null;
    categoria_codigo: number | null;
    unidad_codigo: number | null;  // ← Verifica que este sea el nombre correcto
}

interface Props {
    producto: Producto;
    categorias: Categoria[];
    unidades: UnidadMedida[];
}

const props = defineProps<Props>();

// 🔍 DEBUGGING TEMPORAL - Eliminar después
console.log('Producto recibido:', {
    producto: props.producto,
    unidad_codigo: props.producto.unidad_codigo,
    unidades_disponibles: props.unidades,
});

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
        title: 'Editar Producto',
        href: `/productos/${props.producto.codigo_producto}/edit`,
    },
];

const imagePreview = ref<string | null>(
    props.producto.imagen ? `/storage/${props.producto.imagen}` : null
);

const form = useForm<{
    nombre: string;
    descripcion: string | null;
    precio_unitario: number;
    stock: number;
    categoria_codigo: string | undefined;
    unidad_codigo: string | undefined;
    imagen: File | null;
    _method: string;
}>({
    nombre: props.producto.nombre,
    descripcion: props.producto.descripcion,
    precio_unitario: props.producto.precio_unitario,
    stock: props.producto.stock,
    // ✅ Asegurar que siempre sea string válido o undefined
    categoria_codigo: props.producto.categoria_codigo
        ? String(props.producto.categoria_codigo)
        : undefined,
    unidad_codigo: props.producto.unidad_codigo
        ? String(props.producto.unidad_codigo)
        : undefined,
    imagen: null,
    _method: 'PUT',
});

// 🔍 DEBUGGING - Ver valores del formulario
console.log('Form inicializado:', {
    categoria_codigo: form.categoria_codigo,
    unidad_codigo: form.unidad_codigo,
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
    // Helper para convertir string a número de forma segura
    const toNumber = (value: string | undefined | null) => {
        if (!value || value === 'undefined' || value === 'null') return null;
        const num = parseInt(value);
        return isNaN(num) ? null : num;
    };

    // Preparar datos para enviar
    const dataToSubmit = {
        nombre: form.nombre,
        descripcion: form.descripcion,
        precio_unitario: form.precio_unitario,
        stock: form.stock,
        // Convertir de forma segura a números
        categoria_codigo: toNumber(form.categoria_codigo),
        unidad_codigo: toNumber(form.unidad_codigo),
        imagen: form.imagen,
        _method: 'PUT',
    };

    console.log('Datos a enviar:', dataToSubmit); // Debug

    // ✅ Usar .post() con _method: 'PUT' cuando hay archivos
    form.transform(() => dataToSubmit).post(`/productos/${props.producto.codigo_producto}`, {
        forceFormData: true, // Necesario para subir archivos
        preserveScroll: true,
        onSuccess: () => {
            router.visit('/productos');
        },
        onError: (errors) => {
            console.error('Errores de validación:', errors);
        },
    });
};
</script>

<template>
    <Head title="Editar Producto" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Editar Producto</h1>
                    <p class="text-muted-foreground mt-1">
                        Actualiza los datos del producto
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

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Información del Producto</CardTitle>
                    <CardDescription>
                        Modifica los datos del producto
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <form
                        @submit.prevent="submit"
                        class="space-y-6"
                    >
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
                                placeholder="Nombre del producto"
                                :disabled="form.processing"
                            />
                            <InputError :message="form.errors.nombre" />
                        </div>

                        <div class="grid gap-2">
                            <Label for="descripcion">Descripción</Label>
                            <Textarea
                                id="descripcion"
                                v-model="form.descripcion!"
                                placeholder="Descripción del producto"
                                :disabled="form.processing"
                                rows="4"
                            />
                            <InputError :message="form.errors.descripcion" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="precio_unitario">
                                    Precio Unitario
                                    <span class="text-destructive">*</span>
                                </Label>
                                <Input
                                    id="precio_unitario"
                                    v-model.number="form.precio_unitario"
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
                                    v-model.number="form.stock"
                                    type="number"
                                    min="0"
                                    required
                                    placeholder="0"
                                    :disabled="form.processing"
                                />
                                <InputError :message="form.errors.stock" />
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="grid gap-2">
                                <Label for="categoria_codigo">
                                    Categoría
                                    <span class="text-destructive">*</span>
                                </Label>
                                <Select
                                    v-model="form.categoria_codigo"
                                    :disabled="form.processing"
                                >
                                    <SelectTrigger id="categoria_codigo">
                                        <SelectValue placeholder="Selecciona una categoría" />
                                    </SelectTrigger>
                                    <SelectContent>
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
                                    <span class="text-destructive">*</span>
                                </Label>
                                <Select
                                    v-model="form.unidad_codigo"
                                    :disabled="form.processing"
                                >
                                    <SelectTrigger id="unidad_codigo">
                                        <SelectValue placeholder="Selecciona una unidad" />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectItem
                                            v-for="unidad in unidades"
                                            :key="unidad.codigo_unidad"
                                            :value="String(unidad.codigo_unidad)"
                                        >
                                            {{ unidad.nombre }}{{ unidad.abreviatura ? ` (${unidad.abreviatura})` : '' }}
                                        </SelectItem>
                                    </SelectContent>
                                </Select>
                                <InputError :message="form.errors.unidad_codigo" />
                            </div>
                        </div>

                        <div class="grid gap-2">
                            <Label for="imagen">Imagen del Producto</Label>
                            <Input
                                id="imagen"
                                type="file"
                                accept="image/*"
                                @change="handleImageChange"
                                :disabled="form.processing"
                            />
                            <InputError :message="form.errors.imagen" />
                            <div
                                v-if="imagePreview"
                                class="mt-4 relative w-48 h-48 border rounded-lg overflow-hidden"
                            >
                                <img
                                    :src="imagePreview"
                                    alt="Preview"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                            <div
                                v-else
                                class="mt-4 w-48 h-48 border rounded-lg flex items-center justify-center bg-muted"
                            >
                                <ImageIcon class="h-12 w-12 text-muted-foreground" />
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
                                Actualizar
                            </Button>
                        </div>
                    </form>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
