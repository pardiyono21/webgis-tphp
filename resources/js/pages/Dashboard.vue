<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const breadcrumbs = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
];

// Mock stats for visualization
const stats = [
    { name: 'Total Kecamatan', value: '17', icon: 'map', color: 'bg-emerald-500' },
    { name: 'Lahan Sawah', value: '142', icon: 'crop', color: 'bg-amber-500' },
    { name: 'Lahan Ladang', value: '89', icon: 'mountain', color: 'bg-rose-500' },
    { name: 'Lahan Kebun', value: '256', icon: 'tree', color: 'bg-emerald-600' },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-8 p-4 pt-0 lg:p-8">
            <!-- WELCOME HEADER -->
            <div class="flex flex-col gap-2">
                <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Selamat Datang, Admin</h1>
                <p class="text-slate-500 dark:text-slate-400 font-medium">Ringkasan data spasial pertanian Kabupaten Bungo hari ini.</p>
            </div>

            <!-- STATS GRID -->
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div v-for="stat in stats" :key="stat.name" class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 p-6 rounded-[32px] shadow-sm hover:shadow-md transition-all group overflow-hidden relative">
                    <div class="relative z-10">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-1">{{ stat.name }}</p>
                        <p class="text-4xl font-black text-slate-900 dark:text-white tracking-tighter">{{ stat.value }}</p>
                        <div class="mt-4 flex items-center text-xs font-bold text-emerald-600 dark:text-emerald-400 group-hover:translate-x-1 transition-transform cursor-pointer">
                            Lihat Detail 
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" /></svg>
                        </div>
                    </div>
                    <!-- Background Decoration -->
                    <div :class="['absolute -right-4 -bottom-4 w-24 h-24 rounded-full opacity-5 blur-2xl group-hover:scale-150 transition-transform duration-700', stat.color]"></div>
                </div>
            </div>

            <!-- MAIN DASHBOARD CONTENT -->
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- QUICK ACTIONS -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-emerald-600 rounded-[40px] p-8 text-white relative overflow-hidden shadow-xl shadow-emerald-500/20 group">
                        <div class="relative z-10 flex flex-col h-full justify-between gap-8">
                            <div>
                                <h2 class="text-2xl font-black tracking-tight mb-2">Kelola Data Spasial</h2>
                                <p class="text-emerald-100 font-medium max-w-md">Perbarui data GeoJSON untuk area persawahan, ladang, dan perkebunan untuk hasil analisis yang lebih akurat.</p>
                            </div>
                            <div class="flex flex-wrap gap-4">
                                <Link href="/peta" class="bg-white text-emerald-700 px-6 py-3 rounded-2xl font-black text-sm hover:bg-emerald-50 transition-colors shadow-lg active:scale-95">Preview Peta</Link>
                                <button class="bg-emerald-700/50 text-white border border-emerald-400/30 px-6 py-3 rounded-2xl font-black text-sm hover:bg-emerald-700 transition-colors active:scale-95">Unggah GeoJSON</button>
                            </div>
                        </div>
                        <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/10 rounded-full blur-[80px] group-hover:scale-110 transition-transform duration-1000"></div>
                        <div class="absolute right-10 bottom-10 opacity-10 group-hover:rotate-12 transition-transform duration-700">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-40 w-40" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                        </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 p-6 rounded-[32px] shadow-sm">
                            <h3 class="font-bold text-slate-900 dark:text-white mb-4">Aktivitas Terakhir</h3>
                            <div class="space-y-4">
                                <div v-for="i in 3" :key="i" class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-[10px] font-bold text-slate-500">AD</div>
                                    <div class="flex-1">
                                        <p class="text-xs font-bold text-slate-900 dark:text-white">Admin telah memperbarui data Sawah</p>
                                        <p class="text-[10px] text-slate-500 font-medium">2 jam yang lalu</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                         <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 p-6 rounded-[32px] shadow-sm">
                            <h3 class="font-bold text-slate-900 dark:text-white mb-4">Status Sistem</h3>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-slate-500">Database</span>
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 text-[10px] font-bold rounded-full">Sehat</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-slate-500">Penyimpanan</span>
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400 text-[10px] font-bold rounded-full">85% Kosong</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs font-medium text-slate-500">Analisis Geospasial</span>
                                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400 text-[10px] font-bold rounded-full">Antrean: 2</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR TOOLS -->
                <div class="space-y-6">
                    <div class="bg-slate-900 rounded-[32px] p-6 text-white overflow-hidden relative group">
                        <h3 class="font-bold mb-4 relative z-10">Unduh Laporan</h3>
                        <div class="space-y-2 relative z-10">
                            <button class="w-full text-left p-3 rounded-2xl bg-white/5 hover:bg-white/10 transition-colors flex items-center justify-between group/item">
                                <span class="text-xs font-medium">Rekapitulasi Tahunan (PDF)</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-50 group-hover/item:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            </button>
                            <button class="w-full text-left p-3 rounded-2xl bg-white/5 hover:bg-white/10 transition-colors flex items-center justify-between group/item">
                                <span class="text-xs font-medium">Data Spasial XLS</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-50 group-hover/item:opacity-100 transition-opacity" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                            </button>
                        </div>
                    </div>

                    <div class="bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-[32px] p-6">
                        <div class="flex items-center gap-3 mb-2 text-amber-800 dark:text-amber-400">
                             <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                             <h4 class="font-bold text-sm">Pemberitahuan</h4>
                        </div>
                        <p class="text-xs text-amber-700 dark:text-amber-300 leading-relaxed">
                            Sudah 14 hari sejak update data Sawah di Kecamatan Bungo Dhani. Mohon cek akurasi data di lapangan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
