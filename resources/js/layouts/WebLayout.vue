<script setup>
import { Link, Head, usePage } from '@inertiajs/vue3';
import { ref, computed, onMounted, watch } from 'vue';
import { useAppearance } from '@/composables/useAppearance';

const { appearance, updateAppearance } = useAppearance();
const page = usePage();
const currentRoute = computed(() => page.url);
const user = computed(() => page.props?.auth?.user);

const isScrolled = ref(false);

onMounted(() => {
    window.addEventListener('scroll', () => {
        isScrolled.value = window.scrollY > 20;
    });
});

const isMapPage = computed(() => currentRoute.value?.startsWith('/peta'));

// --- MOBILE NAVIGATION STATE ---
const isMobileMenuOpen = ref(false);
const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
    // Prevent scrolling when menu is open
    if (isMobileMenuOpen.value) {
        document.body.style.overflow = 'hidden';
    } else {
        document.body.style.overflow = '';
    }
};

// Close menu when route changes
watch(currentRoute, () => {
    isMobileMenuOpen.value = false;
    document.body.style.overflow = '';
});
</script>

<template>
    <div class="min-h-screen bg-slate-50 dark:bg-slate-950 font-sans selection:bg-emerald-100 selection:text-emerald-900 transition-colors duration-300">
        <!-- GLOBAL NAVBAR -->
        <nav 
            :class="[
                'fixed top-0 left-0 right-0 z-[100] transition-all duration-500 h-20 flex items-center',
                isScrolled || isMapPage ? 'bg-white/90 dark:bg-slate-900/90 backdrop-blur-md shadow-sm dark:shadow-slate-900/50' : 'bg-transparent'
            ]"
        >
            <div class="container mx-auto px-4 lg:px-8 flex items-center justify-between">
                <!-- Brand logo -->
                <Link href="/" class="flex items-center space-x-3 group">
                    <div class="bg-emerald-600 p-2 rounded-xl group-hover:rotate-12 transition-transform duration-300 shadow-emerald-200 dark:shadow-emerald-900/20 shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="font-black tracking-tighter text-xl text-slate-900 dark:text-white">WEBGIS</span>
                        <span :class="['font-black text-xs block -mt-1', isScrolled || isMapPage ? 'text-emerald-700 dark:text-emerald-400' : 'text-emerald-800/80 dark:text-emerald-300/60']">TPPH BUNGO</span>
                    </div>
                </Link>

                <!-- Navigation Links -->
                <div class="hidden md:flex items-center space-x-1 lg:space-x-8">
                    <Link 
                        href="/" 
                        :class="[
                            'text-sm font-bold transition-all px-4 py-2 rounded-lg',
                            currentRoute === '/' 
                                ? 'text-emerald-700 bg-emerald-100/80 border border-emerald-200/50 shadow-sm dark:bg-emerald-500/20 dark:text-emerald-400 dark:border-emerald-500/30' 
                                : 'text-slate-700 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-slate-800'
                        ]"
                    >
                        Home
                    </Link>
                    <Link 
                        href="/peta" 
                        :class="[
                            'text-sm font-bold transition-all px-4 py-2 rounded-lg',
                            currentRoute.startsWith('/peta') 
                                ? 'text-emerald-700 bg-emerald-100/80 border border-emerald-200/50 shadow-sm dark:bg-emerald-500/20 dark:text-emerald-400 dark:border-emerald-500/30' 
                                : 'text-slate-700 dark:text-slate-400 hover:text-emerald-700 dark:hover:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-slate-800'
                        ]"
                    >
                        Peta Interaktif
                    </Link>

                    <Link 
                        v-if="!user"
                        href="/login" 
                        class="text-sm font-black transition-all px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg shadow-emerald-200 dark:shadow-none active:scale-95"
                    >
                        Masuk
                    </Link>
                    <Link 
                        v-else
                        href="/dashboard" 
                        class="text-sm font-black transition-all px-5 py-2.5 rounded-xl bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-700 text-white shadow-lg active:scale-95 flex items-center gap-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Dashboard
                    </Link>
                    
                    <div class="h-6 w-px bg-gray-200 dark:bg-slate-800 mx-2"></div>

                    <!-- Theme Switcher -->
                    <div class="flex items-center bg-slate-200 dark:bg-slate-800 p-1 rounded-xl border border-slate-300 dark:border-slate-700">
                        <button 
                            @click="updateAppearance('light')"
                            :class="['p-1.5 rounded-lg transition-all', appearance === 'light' ? 'bg-white text-yellow-500 shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300']"
                            title="Mode Terang"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" />
                            </svg>
                        </button>
                        <button 
                            @click="updateAppearance('dark')"
                            :class="['p-1.5 rounded-lg transition-all', appearance === 'dark' ? 'bg-white dark:bg-slate-600 text-indigo-500 shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300']"
                            title="Mode Gelap"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
                        <button 
                            @click="updateAppearance('system')"
                            :class="['p-1.5 rounded-lg transition-all', appearance === 'system' ? 'bg-white dark:bg-slate-600 text-emerald-500 shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300']"
                            title="Otomatis"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>

                </div>

                <!-- Mobile Trigger -->
                <button 
                    @click="toggleMobileMenu"
                    class="md:hidden p-2 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-emerald-50 dark:hover:bg-slate-800 transition-colors z-[110] relative"
                >
                    <svg v-if="!isMobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
                    </svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </nav>

        <!-- MOBILE MENU OVERLAY -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-y-[-10px]"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-[-10px]"
        >
            <div 
                v-if="isMobileMenuOpen" 
                class="fixed inset-0 z-[90] bg-white dark:bg-slate-950 pt-24 px-6 overflow-y-auto md:hidden"
            >
                <div class="flex flex-col space-y-6">
                    <!-- Mobile Links -->
                    <div class="flex flex-col space-y-4">
                        <Link 
                            href="/" 
                            :class="[
                                'text-lg font-bold p-4 rounded-xl flex items-center space-x-3 transition-all',
                                currentRoute === '/' 
                                    ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-white/5' 
                                    : 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-900'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                            <span>Halaman Depan</span>
                        </Link>
                        
                        <Link 
                            href="/peta" 
                            :class="[
                                'text-lg font-bold p-4 rounded-xl flex items-center space-x-3 transition-all',
                                currentRoute.startsWith('/peta')
                                    ? 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-white/5' 
                                    : 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-900'
                            ]"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                            <span>Peta Interaktif</span>
                        </Link>
                    </div>

                    <div class="h-px bg-gray-100 dark:bg-slate-900 w-full"></div>

                    <!-- Mobile Theme Switcher -->
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Tampilan</h4>
                        <div class="grid grid-cols-3 gap-3">
                            <button 
                                @click="updateAppearance('light')"
                                :class="[
                                    'flex flex-col items-center justify-center p-3 rounded-xl border transition-all',
                                    appearance === 'light' 
                                        ? 'bg-white border-yellow-400 text-yellow-600 shadow-sm' 
                                        : 'bg-slate-50 border-transparent text-slate-500 dark:bg-slate-900 dark:text-slate-400'
                                ]"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707m12.728 0l-.707-.707M6.343 6.343l-.707-.707M12 5a7 7 0 100 14 7 7 0 000-14z" />
                                </svg>
                                <span class="text-xs font-bold">Terang</span>
                            </button>
                            <button 
                                @click="updateAppearance('dark')"
                                :class="[
                                    'flex flex-col items-center justify-center p-3 rounded-xl border transition-all',
                                    appearance === 'dark' 
                                        ? 'bg-slate-800 border-indigo-500 text-indigo-400 shadow-sm' 
                                        : 'bg-slate-50 border-transparent text-slate-500 dark:bg-slate-900 dark:text-slate-400'
                                ]"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                                <span class="text-xs font-bold">Gelap</span>
                            </button>
                            <button 
                                @click="updateAppearance('system')"
                                :class="[
                                    'flex flex-col items-center justify-center p-3 rounded-xl border transition-all',
                                    appearance === 'system' 
                                        ? 'bg-white border-emerald-500 text-emerald-600 shadow-sm dark:bg-slate-800 dark:text-emerald-400' 
                                        : 'bg-slate-50 border-transparent text-slate-500 dark:bg-slate-900 dark:text-slate-400'
                                ]"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mb-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs font-bold">Auto</span>
                            </button>
                        </div>
                    </div>

                    <div class="h-px bg-gray-100 dark:bg-slate-900 w-full"></div>

                    <!-- Mobile Login/Dashboard -->
                    <Link 
                        v-if="!user"
                        href="/login" 
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold p-4 rounded-xl shadow-lg shadow-emerald-200 dark:shadow-none active:scale-95 transition-all flex items-center justify-center space-x-2"
                    >
                        <span>LOGIN PETUGAS</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                    </Link>
                    <Link 
                        v-else
                        href="/dashboard" 
                        class="w-full bg-slate-900 dark:bg-emerald-600 text-white font-bold p-4 rounded-xl shadow-lg active:scale-95 transition-all flex items-center justify-center space-x-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>DASHBOARD ADMIN</span>
                    </Link>
                    
                    <div class="text-center text-xs text-slate-400 pt-4 pb-8">
                        &copy; 2026 Dinas TPHP Kabupaten Bungo
                    </div>
                </div>
            </div>
        </Transition>

        <!-- MAIN CONTENT WITH TRANSITION -->
        <main class="transition-all duration-500">
            <Transition
                name="page"
                mode="out-in"
                appear
            >
                <div :key="page.url">
                    <slot />
                </div>
            </Transition>
        </main>

        <!-- GLOBAL FOOTER (Optional, hidden on Map Page) -->
        <footer v-if="!isMapPage" class="bg-slate-900 text-white py-12 border-t border-white/5">
            <div class="container mx-auto px-4 text-center">
                <p class="text-slate-400 text-sm italic">© 2026 Dinas Tanaman Pangan, Hortikultura & Perkebunan Kabupaten Bungo.</p>
            </div>
        </footer>
    </div>
</template>

<style>
/* Page Transition: Fade & Slide */
.page-enter-active,
.page-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.page-enter-from {
    opacity: 0;
    transform: translateY(10px);
}

.page-leave-to {
    opacity: 0;
    transform: translateY(-10px);
}

/* Global Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
