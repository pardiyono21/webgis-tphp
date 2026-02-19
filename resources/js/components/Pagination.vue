<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface LinkItem {
    url: string | null;
    label: string;
    active: boolean;
}

const props = defineProps<{
    links: LinkItem[];
}>();

const cleanLinks = computed(() => {
    return props.links.map(link => {
        let label = link.label;
        if (label.includes('Previous')) label = 'Sebelumnya';
        if (label.includes('Next')) label = 'Berikutnya';
        return { ...link, label };
    });
});
</script>

<template>
    <div v-if="links.length > 3" class="flex flex-wrap items-center gap-1">
        <template v-for="(link, key) in cleanLinks" :key="key">
            <div
                v-if="link.url === null"
                class="px-4 py-2 text-[10px] font-black uppercase tracking-widest text-slate-400 bg-slate-100 dark:bg-slate-800 rounded-xl cursor-not-allowed opacity-50"
                v-html="link.label"
            />
            <Link
                v-else
                :href="link.url"
                class="px-4 py-2 text-[10px] font-black uppercase tracking-widest rounded-xl transition-all active:scale-95 border"
                :class="[
                    link.active 
                        ? 'bg-emerald-600 text-white border-emerald-600 shadow-lg shadow-emerald-200 dark:shadow-none' 
                        : 'bg-white dark:bg-slate-900 text-slate-600 dark:text-slate-400 border-slate-200 dark:border-white/10 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 hover:text-emerald-600 dark:hover:text-emerald-400'
                ]"
                v-html="link.label"
            />
        </template>
    </div>
</template>
