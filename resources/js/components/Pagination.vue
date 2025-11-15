<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Button } from '@/components/ui/button';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { computed } from 'vue';

interface PaginationLink {
    url: string | null;
    label: string;
    active: boolean;
}

interface PaginationData {
    current_page: number;
    first_page_url: string;
    from: number | null;
    last_page: number;
    last_page_url: string;
    links: PaginationLink[];
    next_page_url: string | null;
    path: string;
    per_page: number;
    prev_page_url: string | null;
    to: number | null;
    total: number;
}

interface Props {
    data: PaginationData;
}

const props = defineProps<Props>();

const hasPages = computed(() => props.data.last_page > 1);

const range = computed(() => {
    const { from, to, total } = props.data;
    if (from === null || to === null) return '';
    return `Mostrando ${from} a ${to} de ${total} resultados`;
});

const getPageNumber = (label: string): string => {
    return label.replace('&laquo; Previous', '').replace('Next &raquo;', '').trim();
};
</script>

<template>
    <div
        v-if="hasPages"
        class="flex items-center justify-between px-2"
    >
        <!-- Información de página -->
        <div class="text-muted-foreground text-sm">
            {{ range }}
        </div>

        <!-- Controles de navegación -->
        <div class="flex items-center gap-2">
            <!-- Botón anterior -->
            <Button
                v-if="data.prev_page_url"
                as-child
                variant="outline"
                size="sm"
                class="gap-1"
            >
                <Link
                    :href="data.prev_page_url"
                    preserve-scroll
                >
                    <ChevronLeft class="h-4 w-4" />
                    Anterior
                </Link>
            </Button>
            <Button
                v-else
                variant="outline"
                size="sm"
                disabled
                class="gap-1"
            >
                <ChevronLeft class="h-4 w-4" />
                Anterior
            </Button>

            <!-- Números de página -->
            <div class="hidden items-center gap-1 sm:flex">
                <template
                    v-for="(link, index) in data.links"
                    :key="index"
                >
                    <Button
                        v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                        as-child
                        :variant="link.active ? 'default' : 'outline'"
                        size="sm"
                        class="min-w-9"
                    >
                        <Link
                            :href="link.url"
                            preserve-scroll
                        >
                            {{ getPageNumber(link.label) }}
                        </Link>
                    </Button>
                    <span
                        v-else-if="!link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                        class="text-muted-foreground px-3 text-sm"
                    >
                        {{ getPageNumber(link.label) }}
                    </span>
                </template>
            </div>

            <!-- Indicador de página actual en móvil -->
            <div class="sm:hidden">
                <Button
                    variant="outline"
                    size="sm"
                    disabled
                >
                    {{ data.current_page }} / {{ data.last_page }}
                </Button>
            </div>

            <!-- Botón siguiente -->
            <Button
                v-if="data.next_page_url"
                as-child
                variant="outline"
                size="sm"
                class="gap-1"
            >
                <Link
                    :href="data.next_page_url"
                    preserve-scroll
                >
                    Siguiente
                    <ChevronRight class="h-4 w-4" />
                </Link>
            </Button>
            <Button
                v-else
                variant="outline"
                size="sm"
                disabled
                class="gap-1"
            >
                Siguiente
                <ChevronRight class="h-4 w-4" />
            </Button>
        </div>
    </div>
</template>
