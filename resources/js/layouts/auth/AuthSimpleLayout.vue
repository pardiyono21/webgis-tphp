<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { home } from '@/routes';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div class="flex min-h-svh flex-col items-center justify-center gap-6 bg-slate-50 dark:bg-slate-950 p-6 md:p-10 relative overflow-hidden">
        <!-- BACKGROUND ORNAMENTS -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
            <div class="absolute -top-[10%] -left-[5%] w-[40%] h-[40%] bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-[120px]"></div>
            <div class="absolute top-[20%] -right-[10%] w-[35%] h-[35%] bg-teal-500/10 dark:bg-teal-500/5 rounded-full blur-[100px]"></div>
        </div>

        <div class="w-full max-w-md relative z-10">
            <div class="bg-white/70 dark:bg-slate-900/70 backdrop-blur-xl border border-white dark:border-white/10 rounded-[40px] p-8 lg:p-10 shadow-2xl shadow-emerald-500/5">
                <div class="flex flex-col gap-8">
                    <div class="flex flex-col items-center gap-4">
                        <Link :href="home()" class="flex items-center space-x-3 group transition-transform hover:scale-105">
                            <div class="bg-emerald-600 p-2.5 rounded-2xl shadow-lg shadow-emerald-200 dark:shadow-emerald-900/20 group-hover:rotate-12 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-black tracking-tighter text-xl text-slate-900 dark:text-white leading-none">WEBGIS</span>
                                <span class="font-bold text-[10px] text-emerald-700 dark:text-emerald-400 tracking-wider">TPPH BUNGO</span>
                            </div>
                        </Link>
                        
                        <div class="space-y-2 text-center mt-4">
                            <h1 class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">{{ title }}</h1>
                            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium px-4">
                                {{ description }}
                            </p>
                        </div>
                    </div>
                    <slot />
                </div>
            </div>

            <!-- FOOTER -->
            <div class="mt-8 text-center">
                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em]">
                    &copy; 2024 Dinas TPHP Kabupaten Bungo
                </p>
            </div>
        </div>
    </div>
</template>
