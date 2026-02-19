<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import WebLayout from '@/layouts/WebLayout.vue';

const props = defineProps({
    stats: Object
});

const animatedStats = ref({
    kecamatan: 0,
    sawah: 0,
    ladang: 0,
    kebun: 0
});

const statsContainer = ref(null);
const hasAnimated = ref(false);

const animateValue = (key, target, duration = 2000) => {
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        animatedStats.value[key] = Math.floor(progress * target);
        if (progress < 1) {
            window.requestAnimationFrame(step);
        }
    };
    window.requestAnimationFrame(step);
};

onMounted(() => {
    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting && !hasAnimated.value) {
            hasAnimated.value = true;
            animateValue('kecamatan', props.stats.kecamatan);
            animateValue('sawah', props.stats.sawah);
            animateValue('ladang', props.stats.ladang);
            animateValue('kebun', props.stats.kebun);
        }
    }, { threshold: 0.2 });

    if (statsContainer.value) {
        observer.observe(statsContainer.value);
    }
});
</script>

<template>
    <WebLayout>
        <Head title="Selamat Datang" />

        <div class="relative bg-slate-100 dark:bg-slate-950">
            <!-- DYNAMIC BACKGROUND ORNAMENT -->
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none z-0">
                <div class="absolute -top-[10%] -left-[5%] w-[40%] h-[40%] bg-emerald-500/10 dark:bg-emerald-500/5 rounded-full blur-[120px] animate-pulse-slow"></div>
                <div class="absolute top-[20%] -right-[10%] w-[50%] h-[50%] bg-emerald-600/10 dark:bg-emerald-600/5 rounded-full blur-[150px] animate-pulse-delayed"></div>
                <div class="absolute -bottom-[10%] left-[20%] w-[30%] h-[30%] bg-emerald-600/10 dark:bg-emerald-600/5 rounded-full blur-[100px]"></div>
            </div>

            <!-- HERO SECTION -->
            <section class="relative min-h-screen flex flex-col justify-start pt-24 pb-20">
                <div class="container mx-auto px-4 lg:px-8 relative z-10">
                    <div class="grid lg:grid-cols-2 gap-16 items-center">
                        <div class="space-y-8">
                            <div class="inline-flex items-center space-x-2 bg-emerald-50 dark:bg-emerald-500/5 backdrop-blur-md px-4 py-2 rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-widest text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 shadow-sm animate-fade-in-up">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600"></span>
                                </span>
                                <span>WebGIS Pertanian Terintegrasi</span>
                            </div>
                            
                            <h1 class="text-4xl md:text-5xl xl:text-6xl font-black text-slate-900 dark:text-white tracking-tight leading-[1.1] animate-fade-in-up delay-100 pb-2">
                                <span class="inline-block text-transparent bg-clip-text bg-linear-to-r from-emerald-600 to-emerald-800 dark:from-emerald-400 dark:to-emerald-200">Digitalisasi</span> <br />
                                Pertanian <br />
                                <span class="text-slate-500 dark:text-slate-500 uppercase text-xl md:text-2xl xl:text-3xl tracking-widest font-bold mt-2 block">Kabupaten Bungo.</span>
                            </h1>

                            <p class="text-sm md:text-lg text-slate-600 dark:text-slate-400 leading-relaxed max-w-xl font-medium animate-fade-in-up delay-200">
                                Platform analisis sebaran lahan pertanian cerdas. Memantau sawah, ladang, hingga perkebunan secara presisi untuk ketahanan pangan masa depan.
                            </p>

                            <div class="flex flex-col sm:flex-row items-center gap-5 pt-4 animate-fade-in-up delay-300">
                                <Link 
                                    href="/peta" 
                                    class="w-full sm:w-auto relative group overflow-hidden bg-emerald-600 dark:bg-emerald-500 text-white text-lg font-bold px-10 py-5 rounded-3xl transition-all hover:shadow-[0_20px_50px_rgba(16,185,129,0.3)] active:scale-95 flex items-center justify-center space-x-3"
                                >
                                    <div class="absolute inset-0 w-1/2 h-full bg-white/20 skew-x-45 -translate-x-[150%] group-hover:translate-x-[250%] transition-transform duration-1000"></div>
                                    <span class="relative">Eksplorasi Peta</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover:translate-x-1 transition-transform relative" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </Link>

                                <a href="#fitur" class="w-full sm:w-auto flex items-center justify-center space-x-2 text-slate-400 dark:text-slate-500 hover:text-emerald-600 dark:hover:text-emerald-400 font-bold transition-all px-6 py-4 group">
                                    <span>Lihat Fitur</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 group-hover:translate-y-1 transition-transform opacity-50 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </a>
                            </div>
                        </div>

                        <!-- HERO IMAGE/CARD AREA -->
                        <div class="relative hidden lg:block animate-fade-in-up delay-400 max-w-lg ml-auto">
                            <div class="absolute -inset-4 bg-linear-to-tr from-emerald-500/20 to-teal-500/20 blur-3xl opacity-50 rounded-full"></div>
                            <div class="relative bg-white/40 dark:bg-slate-900/40 backdrop-blur-2xl border border-white/20 dark:border-white/10 rounded-[40px] p-6 shadow-2xl overflow-hidden group">
                                <div class="absolute inset-0 bg-linear-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                                <div class="aspect-4/3 rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-800 relative">
                                    <!-- Placeholder or Real Image -->
                                    <img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2000&auto=format&fit=crop" alt="Digital Farming" class="object-cover w-full h-full scale-110 group-hover:scale-100 transition-transform duration-1000 opacity-80 dark:opacity-60" />
                                    <div class="absolute inset-0 bg-linear-to-b from-slate-950/80 to-transparent"></div>
                                    
                                    <div class="absolute top-8 left-8 right-8 space-y-2">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A2 2 0 013 15.487V6.362a2 2 0 011.026-1.75l5.447-2.724L15 4.638l5.447-2.724A2 2 0 0121 3.663v9.125a2 2 0 01-1.026 1.75l-5.447 2.724L9 20z" />
                                                </svg>
                                            </div>
                                            <div class="text-white">
                                                <div class="text-sm font-bold">Pemetaan Digital</div>
                                                <div class="text-[10px] opacity-70 uppercase tracking-tighter">Kabupaten Bungo</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Floating Elements -->
                            <div class="absolute -top-6 -right-6 w-32 h-32 bg-white/90 dark:bg-slate-800/80 backdrop-blur-xl border border-emerald-200 dark:border-white/10 rounded-3xl shadow-2xl shadow-emerald-100/50 dark:shadow-none flex items-center justify-center animate-bounce-slow">
                                <div class="flex flex-col items-center justify-center space-y-2">
                                    <svg class="w-10 h-10 text-emerald-600 dark:text-emerald-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.8 13.938h-.011a7 7 0 1 0-11.464.144h-.016l.14.171c.1.127.2.251.3.371L12 21l5.13-6.248c.194-.209.374-.429.54-.659l.13-.155Z"/>
                                    </svg>
                                    <div class="text-[10px] font-bold text-slate-500 dark:text-white uppercase tracking-tighter">Aset Digital</div>
                                </div>
                            </div>
                            
                            <div class="absolute -bottom-10 -left-10 bg-emerald-800/80 dark:bg-emerald-950/90 backdrop-blur-sm text-white p-6 rounded-3xl shadow-2xl shadow-emerald-500/20 dark:shadow-none space-y-1 animate-float-x border border-white/10">
                                <div class="text-sm font-black uppercase tracking-[0.2em] opacity-60 text-yellow-300">Data Spasial</div>
                                <div class="flex items-center space-x-3">
                                    <div class="w-2.5 h-2.5 bg-green-400 rounded-full shadow-[0_0_10px_rgba(52,211,153,0.5)] animate-pulse"></div>
                                    <span class="text-[12px] font-black italic tracking-wide">Monitoring Lahan</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STATS COUNTER (Inside Hero) -->
                    <div ref="statsContainer" class="mt-20 lg:mt-32 relative z-20">
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/20 rounded-[40px] p-6 lg:p-10 shadow-xl shadow-emerald-500/5 transition-all duration-500 hover:shadow-emerald-500/10">
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 divide-x-0 lg:divide-x divide-slate-100 dark:divide-white/5">
                                <div class="text-center px-4 group p-4 rounded-3xl transition-all duration-500 hover:bg-slate-200/50 dark:hover:bg-white/5 hover:-translate-y-1">
                                    <div class="text-3xl lg:text-4xl font-black text-emerald-600 dark:text-emerald-400 tracking-tighter mb-2 transition-transform duration-500 group-hover:scale-105">{{ animatedStats.kecamatan }}</div>
                                    <div class="text-slate-500 dark:text-slate-400 text-xs font-black uppercase tracking-widest">Wilayah Kecamatan</div>
                                </div>
                                <div class="text-center px-4 group p-4 rounded-3xl transition-all duration-500 hover:bg-slate-200/50 dark:hover:bg-white/5 hover:-translate-y-1">
                                    <div class="text-3xl lg:text-4xl font-black text-amber-500 dark:text-amber-400 tracking-tighter mb-2 transition-transform duration-500 group-hover:scale-105">{{ animatedStats.sawah }}</div>
                                    <div class="text-slate-500 dark:text-slate-400 text-xs font-black uppercase tracking-widest">Lahan Sawah</div>
                                </div>
                                <div class="text-center px-4 group p-4 rounded-3xl transition-all duration-500 hover:bg-slate-200/50 dark:hover:bg-white/5 hover:-translate-y-1">
                                    <div class="text-3xl lg:text-4xl font-black text-rose-500 dark:text-rose-400 tracking-tighter mb-2 transition-transform duration-500 group-hover:scale-105">{{ animatedStats.ladang }}</div>
                                    <div class="text-slate-500 dark:text-slate-400 text-xs font-black uppercase tracking-widest">Lahan Ladang</div>
                                </div>
                                <div class="text-center px-4 group p-4 rounded-3xl transition-all duration-500 hover:bg-slate-200/50 dark:hover:bg-white/5 hover:-translate-y-1">
                                    <div class="text-3xl lg:text-4xl font-black text-emerald-500 dark:text-emerald-300 tracking-tighter mb-2 transition-transform duration-500 group-hover:scale-105">{{ animatedStats.kebun }}</div>
                                    <div class="text-slate-500 dark:text-slate-400 text-xs font-black uppercase tracking-widest">Lahan Kebun</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- FEATURES SECTION -->
            <section id="fitur" class="py-32 relative">
                <div class="container mx-auto px-4 lg:px-8">
                    <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-24 gap-8">
                        <div class="max-w-2xl space-y-6">
                            <h2 class="text-4xl lg:text-6xl font-black text-slate-900 dark:text-white tracking-tight leading-none">
                                Platform 
                                <span class="text-transparent bg-clip-text bg-linear-to-r from-emerald-600 to-emerald-900 dark:from-emerald-400 dark:to-emerald-200">
                                    Informasi 
                                </span>
                                <span class="text-xl lg:text-4xl font-black block mt-2">Geospasial Sebaran Pertanian</span>
                            </h2>
                            <p class="text-lg text-slate-500 dark:text-slate-400 font-medium leading-relaxed max-w-lg">
                                Dirancang untuk mendukung transparansi dan keterbukaan data lahan bagi publik maupun pemerintah.
                            </p>
                        </div>
                        <div class="hidden lg:block">
                            <div class="w-40 h-px bg-slate-200 dark:bg-white/10 mb-8"></div>
                            <div class="flex -space-x-4">
                                <div class="w-12 h-12 rounded-full border-4 border-white dark:border-slate-950 bg-amber-500 shadow-lg shadow-amber-500/30"></div>
                                <div class="w-12 h-12 rounded-full border-4 border-white dark:border-slate-950 bg-rose-500 shadow-lg shadow-rose-500/30"></div>
                                <div class="w-12 h-12 rounded-full border-4 border-white dark:border-slate-950 bg-emerald-500 shadow-lg shadow-emerald-500/30"></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        <div class="group relative p-px rounded-[48px] overflow-hidden transition-all duration-500 hover:-translate-y-1">
                            <div class="absolute inset-0 bg-linear-to-br from-amber-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="relative h-full p-12 bg-white dark:bg-slate-900 rounded-[47px] border border-slate-200 dark:border-white/10 space-y-8 shadow-sm group-hover:shadow-md hover:border-amber-500/20 transition-all duration-500">
                                <div class="w-20 h-20 bg-amber-50 dark:bg-slate-800 shadow-xl dark:shadow-none rounded-3xl flex items-center justify-center group-hover:bg-amber-500 group-hover:rotate-6 transition-all duration-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-amber-600 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A2 2 0 013 15.487V6.362a2 2 0 011.026-1.75l5.447-2.724L15 4.638l5.447-2.724A2 2 0 0121 3.663v9.125a2 2 0 01-1.026 1.75l-5.447 2.724L9 20z" />
                                    </svg>
                                </div>
                                <div class="space-y-4">
                                    <h3 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Peta Cerdas</h3>
                                    <p class="text-lg text-slate-500 dark:text-slate-400 font-medium leading-relaxed">Sistem GIS interaktif dengan layer komoditas Sawah, Ladang, dan Kebun yang presisi.</p>
                                </div>
                            </div>
                        </div>

                        <div class="group relative p-px rounded-[48px] overflow-hidden transition-all duration-500 hover:-translate-y-1">
                            <div class="absolute inset-0 bg-linear-to-br from-rose-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="relative h-full p-12 bg-white dark:bg-slate-900 rounded-[47px] border border-slate-200 dark:border-white/10 space-y-8 shadow-sm group-hover:shadow-md hover:border-rose-500/20 transition-all duration-500">
                                <div class="w-20 h-20 bg-rose-50 dark:bg-slate-800 shadow-xl dark:shadow-none rounded-3xl flex items-center justify-center group-hover:bg-rose-500 group-hover:-rotate-6 transition-all duration-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-rose-600 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                                <div class="space-y-4">
                                    <h3 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Data Presisi</h3>
                                    <p class="text-lg text-slate-500 dark:text-slate-400 font-medium leading-relaxed">Statistik luasan lahan dan sebaran titik koordinat yang diperbarui secara periodik.</p>
                                </div>
                            </div>
                        </div>

                        <div class="group relative p-px rounded-[48px] overflow-hidden transition-all duration-500 hover:-translate-y-1">
                            <div class="absolute inset-0 bg-linear-to-br from-emerald-500/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            <div class="relative h-full p-12 bg-white dark:bg-slate-900 rounded-[47px] border border-slate-200 dark:border-white/10 space-y-8 shadow-sm group-hover:shadow-md hover:border-emerald-500/20 transition-all duration-500">
                                <div class="w-20 h-20 bg-emerald-50 dark:bg-slate-800 shadow-xl dark:shadow-none rounded-3xl flex items-center justify-center group-hover:bg-emerald-600 group-hover:scale-110 transition-all duration-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-emerald-600 group-hover:text-white transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="space-y-4">
                                    <h3 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">Responsive</h3>
                                    <p class="text-lg text-slate-500 dark:text-slate-400 font-medium leading-relaxed">Pengalaman eksplorasi yang optimal dan fleksibel di berbagai ukuran perangkat.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </WebLayout>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&display=swap');

.animate-pulse-slow {
    animation: pulse 6s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.animate-pulse-delayed {
    animation: pulse 6s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    animation-delay: 3s;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

.animate-bounce-slow {
    animation: bounce-slow 4s infinite;
}

@keyframes bounce-slow {
    0%, 100% { transform: translateY(-10%); }
    50% { transform: translateY(0); }
}

.animate-float-x {
    animation: float-x 4s ease-in-out infinite;
}

@keyframes float-x {
    0%, 100% { transform: translateX(-10%); }
    50% { transform: translateX(0); }
}

.animate-fade-in-up {
    animation: fade-in-up 0.8s ease-out forwards;
    opacity: 0;
}

@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-400 { animation-delay: 0.4s; }

.font-serif {
    font-family: 'Instrument Serif', serif;
}
</style>
