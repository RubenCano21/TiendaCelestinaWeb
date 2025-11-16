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

interface UnidadMedida {
    codigo_medida: number;
    nombre: string;
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
        title: 'Editar Unidad',
        href: `/unidades/${props.unidad.codigo_medida}/edit`,
    },
];

const form = useForm({
    nombre: props.unidad.nombre,
});

const submit = () => {
    form.put(`/unidades/${props.unidad.codigo_medida}`, {
        onSuccess: () => {
            router.visit('/unidades');
        },
    });
};
</script>

<template>
    <Head title="Editar Unidad de Medida" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold tracking-tight">Editar Unidad de Medida</h1>
                    <p class="text-muted-foreground mt-1">
                        Actualiza los datos de la unidad de medida
                    </p>
                </div>
                <Button
                    variant="outline"
                    as-child
                    class="gap-2"
                >
                    <a href="/unidades">
                        <ArrowLeft class="h-4 w-4" />
                        Volver
                    </a>
                </Button>
            </div>

            <Card class="max-w-2xl">
                <CardHeader>
                    <CardTitle>Información de la Unidad de Medida</CardTitle>
                    <CardDescription>
                        Modifica los datos de la unidad de medida
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
                                placeholder="Ej: Kilogramo, Litro, Unidad, etc."
                                :disabled="form.processing"
                            />
                            <InputError :message="form.errors.nombre" />
                        </div>

                        <div class="flex justify-end gap-4">
                            <Button
                                type="button"
                                variant="outline"
                                @click="router.visit('/unidades')"
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
