<template>
    <WebLayout>
        <div class="flex h-dvh bg-gray-100 dark:bg-slate-950 font-sans overflow-hidden relative pt-20 transition-colors duration-300">
            <!-- BACKDROP SIDEBAR (Latar belakang gelap saat sidebar mobile terbuka) -->
            <div 
                v-if="sidebarOpen" 
                @click="sidebarOpen = false"
                class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 transition-opacity duration-300"
            >
                <!-- Tombol Close Sidebar (Mobile Only) -->
                <button 
                    @click="sidebarOpen = false"
                    class="lg:hidden absolute top-20 right-3 z-50 p-2 bg-gray-100 dark:bg-slate-800 text-gray-500 dark:text-slate-400 rounded-full hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 transition-colors shadow-sm"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
            </div>

            <!-- SIDEBAR KIRI (Daftar Kecamatan & Pertanian) -->
            <div 
                :class="[
                    'fixed lg:relative top-20 lg:top-0 bottom-0 left-0  w-[85vw] sm:w-80 md:w-80 lg:w-80 bg-white dark:bg-slate-900 shadow-2xl flex flex-col z-50 lg:z-10 border-r border-gray-200 dark:border-white/5 transition-transform duration-300 transform',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
                ]"
            >
                <!-- Konten Utama Sidebar (Bisa di-scroll) -->
                <div class="flex-1 flex flex-col min-h-0 bg-gray-50 dark:bg-slate-950 overflow-hidden relative">
                
                    <!-- Overlay Loading saat data pertanian sedang dimuat (Hanya tampil jika kecamatan terpilih tapi data belum ada) -->
                    <div v-if="selectedKecamatan && (!agriSawah || !agriLadang || !agriKebun)" class="absolute inset-0 z-10 bg-white/50 dark:bg-slate-900/50 backdrop-blur-[2px] flex items-center justify-center">
                        <div class="flex flex-col items-center space-y-4">
                            <div class="w-10 h-10 border-4 border-emerald-500/20 border-t-emerald-500 rounded-full animate-spin"></div>
                            <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 animate-pulse">Memuat Data Pertanian...</span>
                        </div>
                    </div>

                    <!-- LEVEL 1: DAFTAR KECAMATAN (Halaman awal sidebar - Instan) -->
                    <div v-if="!selectedKecamatan" class="p-4 lg:pt-4 overflow-y-auto flex-1">
                    <h2 class="text-xs font-bold text-gray-400 dark:text-slate-500 uppercase tracking-widest mb-4">Wilayah Pertanian</h2>
                    
                    <!-- Loop menampilkan semua kecamatan -->
                    
                    <div v-for="kecamatan in petaBungo" :key="'kec-' + kecamatan.gid" class="mb-2">
                        <!-- Card Kecamatan: Versi lebih lembut, tidak terlalu mencolok -->
                        <div 
                            @click="() => { selectKecamatanForCategories(kecamatan); if(windowWidth < 1024) sidebarOpen = false; }"
                            class="p-3 bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-white/5 hover:border-slate-300 dark:hover:border-emerald-500/30 hover:shadow-sm transition-all cursor-pointer group mb-1.5"
                        >
                            <div class="flex flex-col">
                                <!-- Baris Atas: Nama & Panah -->
                                <div class="flex justify-between items-start mb-2.5">
                                    <h3 class="font-bold text-[14px] text-gray-700 dark:text-slate-200 group-hover:text-cyan-700 dark:group-hover:text-cyan-400 transition-colors leading-snug pr-2">
                                        {{ kecamatan.namobj }}
                                    </h3>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300 dark:text-slate-600 group-hover:text-cyan-400 transition-colors shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                                
                                <!-- Baris Bawah: Statistik -->
                                <div class="flex items-end justify-between">
                                    <!-- Categories (Left) -->
                                    <div class="flex flex-wrap gap-1">
                                        <div v-if="getAgriCount('sawah', kecamatan.gid) > 0" 
                                             class="flex items-center px-1.5 py-0.5 rounded
                                                    bg-yellow-50 dark:bg-yellow-900/25
                                                    border border-yellow-200 dark:border-yellow-500/30
                                                    text-[9px] font-bold
                                                    text-yellow-700 dark:text-yellow-300">
                                            <span class="w-1 h-1 rounded-full bg-yellow-400 mr-1"></span>
                                            {{ getAgriCount('sawah', kecamatan.gid) }} S
                                        </div>
                                        <div v-if="getAgriCount('ladang', kecamatan.gid) > 0" 
                                             class="flex items-center px-1.5 py-0.5 rounded
                                                        bg-red-50 dark:bg-red-900/25
                                                        border border-red-200 dark:border-red-500/30
                                                        text-[9px] font-bold
                                                        text-red-700 dark:text-red-300">
                                            <span class="w-1 h-1 rounded-full bg-red-500 mr-1"></span>
                                            {{ getAgriCount('ladang', kecamatan.gid) }} L
                                        </div>
                                        <div v-if="getAgriCount('kebun', kecamatan.gid) > 0" 
                                             class="flex items-center px-1.5 py-0.5 rounded
                                                    bg-indigo-50 dark:bg-indigo-900/25
                                                    border border-indigo-200 dark:border-indigo-500/30
                                                    text-[9px] font-bold
                                                    text-indigo-700 dark:text-indigo-300">
                                            <span class="w-1 h-1 rounded-full bg-emerald-600 mr-1"></span>
                                            {{ getAgriCount('kebun', kecamatan.gid) }} K
                                        </div>
                                    </div>

                                    <!-- Area (Kanan) -->
                                    <div class="flex items-center text-[10px] text-slate-500 dark:text-slate-400 font-bold italic bg-slate-100/80 dark:bg-white/5 px-2 py-0.5 rounded border border-slate-200 dark:border-white/5">
                                        {{ (kecamatan.luas_m2 / 1000000).toFixed(2) }} km²
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                    <!-- LEVEL 2 & 3 (Tampil jika kecamatan dipilih) -->
                    <template v-else>
                        <div v-if="selectedKecamatan && !selectedCategory" class="p-4 lg:pt-4 overflow-y-auto flex-1">
                            <!-- Tombol Kembali ke Level 1 -->
                            <button 
                                @click="backToKecamatanList"
                                class="flex items-center cursor-pointer space-x-2 text-sm text-slate-500 dark:text-slate-400 hover:text-cyan-600 dark:hover:text-cyan-400 mb-4 transition-colors"
                            >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="font-medium">Kembali ke Daftar</span>
                    </button>

                    <!-- Header Nama Kecamatan: Versi lebih elegan (Slate-800) -->
                    <div class="bg-linear-to-r from-slate-800 to-indigo-900 text-white p-4 py-5 rounded-2xl mb-5 shadow-md relative overflow-hidden">
                        <div class="relative z-10">
                            <h2 class="font-bold text-lg tracking-tight">{{ selectedKecamatan.namobj }}</h2>
                            <p class="text-[10px] opacity-70 mt-1 uppercase tracking-widest font-semibold">{{ (selectedKecamatan.luas_m2 / 1000000).toFixed(2) }} km² Area</p>
                        </div>
                        <!-- Dekorasi halus belakang -->
                        <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/5 rounded-full blur-2xl"></div>
                    </div>

                    <h3 class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-4">Kategori Pertanian</h3>

                    <!-- Card Sawah -->
                    <div 
                        @click="selectCategory('sawah')"
                        class="p-4 bg-white dark:bg-slate-900 rounded-xl border-2 border-yellow-200 dark:border-yellow-500/20 hover:border-yellow-400 dark:hover:border-yellow-500/40 hover:shadow-md transition-all cursor-pointer group mb-3 relative overflow-hidden"
                    >
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <div class="bg-yellow-100 dark:bg-yellow-500/10 p-2 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600 dark:text-yellow-500" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M5,2c.82.82,5.61,2.88,6,18,0,.65,0,1.31,0,2h3C14,2.12,7,2,5,2ZM19,5a7,7,0,0,0-5.56,3,16.3,16.3,0,0,1,1.12,4C15.74,6.79,18.39,5.61,19,5ZM2,9c.77.77,4.72,3.09,5,12,0,.32,0,.66,0,1h3C10,13.52,7.5,9.22,2,9ZM22,9c-2,0-7,2.25-7,13h3C18,13,21.12,9.88,22,9Z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-gray-800 dark:text-slate-200 group-hover:text-yellow-700 dark:group-hover:text-yellow-400 transition-colors">Lahan Sawah</div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">{{ getAgriCount('sawah', selectedKecamatan.gid) }} lokasi</div>
                                </div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 dark:text-slate-600 group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 6v2h8.59L5 17.59 6.41 19 16 9.41V18h2V6z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Card Ladang -->
                    <div 
                        @click="selectCategory('ladang')"
                        class="p-4 bg-white dark:bg-slate-900 rounded-xl border-2 border-red-200 dark:border-red-500/20 hover:border-red-400 dark:hover:border-red-500/40 hover:shadow-md transition-all cursor-pointer group mb-3 relative overflow-hidden"
                    >
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <div class="bg-red-100 dark:bg-red-500/10 p-2 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600 dark:text-red-500" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09c1.358-2.473 1.231-5.069.17-7.42-3.376 0-6.155 2.115-6.91 5.097 2.185.076 4.195.952 5.918 2.274a15.77 15.77 0 0 1 .494.398ZM12 11c0 3.517 1.009 6.799 2.753 9.571m3.44-2.04l-.054-.09c-1.358-2.473-1.231-5.069-.17-7.42 3.376 0 6.155 2.115 6.91 5.097-2.185.076-4.195.952-5.918 2.274a15.77 15.77 0 0 0-.494.398Z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-gray-800 dark:text-slate-200 group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors">Lahan Ladang</div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">{{ getAgriCount('ladang', selectedKecamatan.gid) }} lokasi</div>
                                </div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 dark:text-slate-600 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 6v2h8.59L5 17.59 6.41 19 16 9.41V18h2V6z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Card Kebun -->
                    <div 
                        @click="selectCategory('kebun')"
                        class="p-4 bg-white dark:bg-slate-900 rounded-xl border-2 border-indigo-200 dark:border-indigo-500/20 hover:border-indigo-400 dark:hover:border-indigo-500/40 hover:shadow-md transition-all cursor-pointer group mb-3 relative overflow-hidden"
                    >
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-3">
                                <div class="bg-indigo-100 dark:bg-indigo-500/10 p-2 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-500" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M17,10c0-4.42-3.58-8-8-8S1,5.58,1,10c0,2.94,1.6,5.51,3.99,6.92V22h2.02v-3.56c0.64,0.19,1.32,0.3,2,0.30c0.69,0,1.36-0.11,2-0.3V22h2V16.92C15.4,15.51,17,12.94,17,10z M9,16.73V13h1v3.74C9.67,16.78,9.34,16.78,9,16.73z M10,11h-1V7h1V11z M9,5.16C7.57,5.56,6.34,6.62,5.65,8H5C5,7.72,5.05,7.45,5.13,7.2C5.9,4.28,8.57,2.16,11.75,2.02 C10.74,2.83,10,4.06,10,5.43V6H9V5.16z M14.88,10c0,1.96-1.03,3.68-2.6,4.64l-0.28-0.16V10c0-1.65-1.35-3-3-3h-1V5.43 c0-1.61,1.11-2.95,2.62-3.32C13.25,2.65,14.88,4.12,14.88,6V10z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-gray-800 dark:text-slate-200 group-hover:text-indigo-700 dark:group-hover:text-indigo-400 transition-colors">Lahan Kebun</div>
                                    <div class="text-xs text-gray-500 dark:text-slate-400 mt-0.5">{{ getAgriCount('kebun', selectedKecamatan.gid) }} lokasi</div>
                                </div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 dark:text-slate-600 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M6 6v2h8.59L5 17.59 6.41 19 16 9.41V18h2V6z" />
                            </svg>
                        </div>
                    </div>

                    <!-- Placeholder untuk kategori lain -->
                    <div class="p-4 bg-gray-100 dark:bg-slate-900 rounded-xl border-2 border-dashed border-gray-300 dark:border-slate-700 opacity-50">
                        <div class="flex items-center space-x-3">
                                <div class="bg-gray-200 dark:bg-slate-800 p-2 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-400 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-bold text-sm text-gray-500 dark:text-slate-400">Kategori Lainnya</div>
                                    <div class="text-xs text-gray-400 dark:text-slate-500">Segera hadir</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- LEVEL 3-S: DAFTAR ITEM SAWAH -->
                        <div v-else-if="selectedKecamatan && selectedCategory === 'sawah'" class="flex flex-col flex-1 min-h-0">
                            <div class="p-4 bg-gray-50 dark:bg-slate-950 z-10 shrink-0">
                                <!-- Tombol Kembali ke Level 2 -->
                                <button @click="backToCategories" class="flex items-center cursor-pointer space-x-2 text-sm text-gray-600 dark:text-slate-400 hover:text-green-700 dark:hover:text-emerald-400 mb-4 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    <span class="font-medium">Kembali ke Kategori</span>
                                </button>

                                <div class="bg-linear-to-r from-yellow-500 to-yellow-600 text-white p-4 rounded-xl shadow-lg relative overflow-hidden group">
                                    <div class="relative z-10">
                                        <div class="text-xs opacity-90 mb-1">{{ selectedKecamatan.namobj }}</div>
                                        <h2 class="font-bold text-lg">Lahan Sawah</h2>
                                        <p class="text-xs opacity-90 mt-1">{{ getAgriCount('sawah', selectedKecamatan.gid) }} lokasi terdaftar</p>
                                    </div>
                                    <div class="absolute -bottom-4 -right-4 opacity-20 pointer-events-none group-hover:opacity-30 transition-opacity">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32 text-white transform rotate-12" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M5,2c.82.82,5.61,2.88,6,18,0,.65,0,1.31,0,2h3C14,2.12,7,2,5,2ZM19,5a7,7,0,0,0-5.56,3,16.3,16.3,0,0,1,1.12,4C15.74,6.79,18.39,5.61,19,5ZM2,9c.77.77,4.72,3.09,5,12,0,.32,0,.66,0,1h3C10,13.52,7.5,9.22,2,9ZM22,9c-2,0-7,2.25-7,13h3C18,13,21.12,9.88,22,9Z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <div class="overflow-y-auto flex-1 p-4 pt-0 space-y-2">
                                <div v-if="getAgriCount('sawah', selectedKecamatan.gid) === 0" class="text-sm text-gray-400 italic py-4 text-center">
                                    Tidak ada data lahan sawah di kecamatan ini.
                                </div>
                                <div v-for="item in getAgriByKecamatan('sawah', selectedKecamatan.gid)" :key="'saw-' + item.gid" @click="selectAgriItem(item, 'sawah')" class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-white/5 hover:border-yellow-400 dark:hover:border-yellow-500/30 hover:bg-yellow-50 dark:hover:bg-yellow-500/5 hover:shadow-md cursor-pointer transition-all group">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-semibold text-sm text-gray-800 dark:text-slate-200 group-hover:text-yellow-700 dark:group-hover:text-yellow-400 transition-colors">{{ item.namobj }}</div>
                                            <div class="text-xs text-gray-700 dark:text-slate-400 flex items-center space-x-2">
                                                <span class="bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-white/5 px-2 py-1 rounded">{{ item.remark }}</span>
                                                <span class="font-mono font-bold text-yellow-600">{{ (item.luas_m2 / 10000).toFixed(2) }} Ha</span>
                                            </div>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 dark:text-slate-600 group-hover:text-yellow-600 transition-colors mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- LEVEL 3-L: DAFTAR ITEM LADANG -->
                        <div v-else-if="selectedKecamatan && selectedCategory === 'ladang'" class="flex flex-col flex-1 min-h-0">
                            <div class="p-4 bg-gray-50 dark:bg-slate-950 z-10 shrink-0">
                                <button @click="backToCategories" class="flex items-center cursor-pointer space-x-2 text-sm text-gray-600 dark:text-slate-400 hover:text-green-700 dark:hover:text-emerald-400 mb-4 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    <span class="font-medium">Kembali ke Kategori</span>
                                </button>
                                <div class="bg-linear-to-r from-red-500 to-red-600 text-white p-4 rounded-xl shadow-lg relative overflow-hidden group">
                                    <div class="relative z-10">
                                        <div class="text-xs opacity-90 mb-1">{{ selectedKecamatan.namobj }}</div>
                                        <h2 class="font-bold text-lg">Lahan Ladang</h2>
                                        <p class="text-xs opacity-90 mt-1">{{ getAgriCount('ladang', selectedKecamatan.gid) }} lokasi terdaftar</p>
                                    </div>
                                    <div class="absolute -bottom-4 -right-4 opacity-20 pointer-events-none group-hover:opacity-30 transition-opacity">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32 text-white transform rotate-12" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09c1.358-2.473 1.231-5.069.17-7.42-3.376 0-6.155 2.115-6.91 5.097 2.185.076 4.195.952 5.918 2.274a15.77 15.77 0 0 1 .494.398ZM12 11c0 3.517 1.009 6.799 2.753 9.571m3.44-2.04l-.054-.09c-1.358-2.473-1.231-5.069-.17-7.42 3.376 0 6.155 2.115 6.91 5.097-2.185.076-4.195.952-5.918 2.274a15.77 15.77 0 0 0-.494.398Z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="overflow-y-auto flex-1 p-4 pt-0 space-y-2">
                                <div v-if="getAgriCount('ladang', selectedKecamatan.gid) === 0" class="text-sm text-gray-400 italic py-4 text-center">
                                    Tidak ada data lahan ladang di kecamatan ini.
                                </div>
                                <div v-for="item in getAgriByKecamatan('ladang', selectedKecamatan.gid)" :key="'lad-' + item.gid" @click="selectAgriItem(item, 'ladang')" class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-white/5 hover:border-red-400 dark:hover:border-red-500/30 hover:bg-red-50 dark:hover:bg-red-500/5 hover:shadow-md cursor-pointer transition-all group">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-semibold text-sm text-gray-800 dark:text-slate-200 group-hover:text-red-700 dark:group-hover:text-red-400 transition-colors">{{ item.namobj }}</div>
                                            <div class="text-xs text-gray-700 dark:text-slate-400 flex items-center space-x-2">
                                                <span class="bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-white/5 px-2 py-1 rounded">{{ item.remark }}</span>
                                                <span class="font-mono font-bold text-red-600 dark:text-red-400">{{ (item.luas_m2 / 10000).toFixed(2) }} Ha</span>
                                            </div>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 dark:text-slate-600 group-hover:text-red-600 transition-colors mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- LEVEL 3-K: DAFTAR ITEM KEBUN -->
                        <div v-else-if="selectedKecamatan && selectedCategory === 'kebun'" class="flex flex-col flex-1 min-h-0">
                            <div class="p-4 bg-gray-50 dark:bg-slate-950 z-10 shrink-0">
                                <button @click="backToCategories" class="flex items-center cursor-pointer space-x-2 text-sm text-gray-600 dark:text-slate-400 hover:text-green-700 dark:hover:text-emerald-400 mb-4 transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                    </svg>
                                    <span class="font-medium">Kembali ke Kategori</span>
                                </button>
                                <div class="bg-linear-to-r from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-800 text-white p-4 rounded-xl shadow-lg relative overflow-hidden group">
                                    <div class="relative z-10">
                                        <div class="text-xs opacity-90 mb-1">{{ selectedKecamatan.namobj }}</div>
                                        <h2 class="font-bold text-lg">Lahan Kebun</h2>
                                        <p class="text-xs opacity-90 mt-1">{{ getAgriCount('kebun', selectedKecamatan.gid) }} lokasi terdaftar</p>
                                    </div>
                                    <div class="absolute -bottom-4 -right-4 opacity-20 pointer-events-none group-hover:opacity-30 transition-opacity">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-32 w-32 text-white transform rotate-12" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M17,10c0-4.42-3.58-8-8-8S1,5.58,1,10c0,2.94,1.6,5.51,3.99,6.92V22h2.02v-3.56c0.64,0.19,1.32,0.3,2,0.30c0.69,0,1.36-0.11,2-0.3V22h2V16.92C15.4,15.51,17,12.94,17,10z M9,16.73V13h1v3.74C9.67,16.78,9.34,16.78,9,16.73z M10,11h-1V7h1V11z M9,5.16C7.57,5.56,6.34,6.62,5.65,8H5C5,7.72,5.05,7.45,5.13,7.2C5.9,4.28,8.57,2.16,11.75,2.02 C10.74,2.83,10,4.06,10,5.43V6H9V5.16z M14.88,10c0,1.96-1.03,3.68-2.6,4.64l-0.28-0.16V10c0-1.65-1.35-3-3-3h-1V5.43 c0-1.61,1.11-2.95,2.62-3.32C13.25,2.65,14.88,4.12,14.88,6V10z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="overflow-y-auto flex-1 p-4 pt-0 space-y-2">
                                <div v-if="getAgriCount('kebun', selectedKecamatan.gid) === 0" class="text-sm text-gray-400 italic py-4 text-center">
                                    Tidak ada data lahan kebun di kecamatan ini.
                                </div>
                                <div v-for="item in getAgriByKecamatan('kebun', selectedKecamatan.gid)" :key="'keb-' + item.gid" @click="selectAgriItem(item, 'kebun')" class="p-4 bg-white dark:bg-slate-900 rounded-xl border border-gray-200 dark:border-white/5 hover:border-indigo-400 dark:hover:border-indigo-500/40 hover:bg-indigo-50 dark:hover:bg-indigo-500/5 hover:shadow-md cursor-pointer transition-all group">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-semibold text-sm text-gray-800 dark:text-slate-200 group-hover:text-indigo-700 dark:group-hover:text-indigo-400 transition-colors">{{ item.namobj }}</div>
                                            <div class="text-xs text-gray-700 dark:text-slate-400 flex items-center space-x-2">
                                                <span class="bg-gray-100 dark:bg-slate-800 border border-gray-200 dark:border-white/5 px-2 py-1 rounded">{{ item.remark }}</span>
                                                <span class="font-mono font-bold text-indigo-600 dark:text-indigo-400">{{ (item.luas_m2 / 10000).toFixed(2) }} Ha</span>
                                            </div>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 dark:text-slate-600 group-hover:text-indigo-600 transition-colors mt-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="flex-1 relative">
            <!-- Tombol Toggle Sidebar (Mobile Only) -->
            <button 
                @click="toggleSidebar"
                class="lg:hidden absolute top-4 left-4 z-40 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 p-2.5 rounded-xl shadow-lg border border-gray-200 dark:border-white/10 active:scale-95 transition-transform"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                </svg>
            </button>

            <!-- Kontainer ID 'map' untuk Leaflet -->
            <div id="map" class="h-full w-full z-0"></div>
            
            <!-- PANEL DETAIL (Muncul saat item diklik) -->
            <div 
                v-if="detailPanelOpen && selectedDetail"
                ref="detailPanelRef"
                :class="[
                    'absolute z-40 transition-all duration-300 overflow-hidden backdrop-blur-xl bg-white/95 dark:bg-slate-900/95 border border-green-500 dark:border-emerald-500/50 shadow-2xl',
                    'bottom-4 left-4 right-4 rounded-3xl max-h-[70vh]', 
                    'lg:top-4 lg:bottom-auto lg:left-4 lg:right-auto lg:w-96 lg:rounded-2xl lg:max-h-[65vh]'
                ]"
            >
                <!-- Header Panel (Warna Hijau Muda) -->
                <div class="relative bg-linear-to-r from-green-50/90 to-emerald-50/90 dark:from-emerald-950/80 dark:to-slate-900/80 backdrop-blur-sm p-4 lg:p-5 border-b border-green-200 dark:border-white/5">
                    <!-- Garis Penarik (Hanya di Mobile) -->
                    <div class="lg:hidden w-12 h-1.5 bg-gray-300 dark:bg-slate-700 rounded-full mx-auto mb-4 opacity-50"></div>
                    
                    <!-- Tombol Tutup Panel -->
                    <button 
                        @click.prevent.stop="closeDetailPanel"
                        type="button"
                        class="absolute top-4 lg:top-4 z-1 right-4 bg-white dark:bg-slate-800 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg p-1.5 transition-all duration-200 hover:scale-110 active:scale-95 cursor-pointer border border-green-200 dark:border-white/10 hover:border-red-200 group"
                        title="Tutup Detail"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-700 dark:text-emerald-400 group-hover:text-red-600 group-hover:rotate-90 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    
                    <!-- Title Section -->
                    <div class="pr-14">
                        <div class="flex items-center space-x-2 mb-2">
                            <!-- Label Badge -->
                            <div class="bg-white/80 dark:bg-emerald-500/10 backdrop-blur-sm rounded-lg px-2 py-0 border border-green-200 dark:border-emerald-500/30 shadow-sm">
                                <span class="text-xs font-bold uppercase tracking-wider text-green-700 dark:text-emerald-400">{{ selectedDetail.type }}</span>
                            </div>
                        </div>
                        <h3 class="font-bold text-sm leading-tight text-green-900 dark:text-emerald-50">{{ selectedDetail.title }}</h3>
                    </div>
                </div>
                
                <!-- Content with glassmorphism styling -->
                <div class="p-6 overflow-y-auto bg-linear-to-b from-green-50/50 to-white dark:from-slate-900 dark:to-slate-950" style="max-height: calc(65vh - 140px);">
                    <div class="space-y-3">
                        <div 
                            v-for="(value, key) in selectedDetail.data" 
                            :key="key" 
                            class="group flex justify-between items-center p-3 rounded-xl bg-white dark:bg-slate-800 backdrop-blur-sm border border-green-200 dark:border-white/5 hover:border-green-200 hover:bg-white transition-all duration-200"
                        >
                            <span class="text-xs font-semibold text-gray-700 dark:text-slate-400 group-hover:text-green-700 dark:group-hover:text-emerald-400 transition-colors">{{ key }}</span>
                            <span class="text-xs text-gray-900 dark:text-white font-bold">{{ value }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Footer Badge -->
                <div class="px-6 py-3 bg-linear-to-r from-green-50/80 to-emerald-50/80 dark:from-emerald-950/80 dark:to-slate-950 border-t border-green-300 dark:border-white/5">
                    <div class="flex items-center justify-center space-x-2 text-xs text-green-700 dark:text-emerald-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="font-semibold">GIS Pertanian Bungo</span>
                    </div>
                </div>
            </div>
            
            <!-- Toggle Button untuk Detail Panel (jika ada detail tapi panel tertutup) -->
            <button
                v-if="!detailPanelOpen && selectedDetail"
                @click="detailPanelOpen = true"
                :class="[
                    'absolute z-30 bg-white dark:bg-slate-900 dark:text-emerald-400 hover:bg-green-50 dark:hover:bg-slate-800 text-green-700 p-3.5 rounded-xl shadow-xl border border-green-100 dark:border-white/10 transition-all duration-300 hover:scale-110 active:scale-95 group',
                    'bottom-4 left-4',
                    'lg:top-4 lg:bottom-auto lg:left-4'
                ]"
                title="Lihat Detail"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>

            <!-- KONTROL LAYER KUSTOM -->
            <!-- Tombol Buka Panel Layer -->
            <button 
                @click="layerPanelOpen = !layerPanelOpen"
                :class="[
                    'absolute z-40 bg-white dark:bg-slate-900 dark:text-emerald-400 hover:bg-green-50 dark:hover:bg-slate-800 text-green-700 p-3.5 rounded-xl shadow-xl border border-green-100 dark:border-white/10 transition-all duration-300 hover:scale-110 active:scale-95 group',
                    'top-4 right-4'
                ]"
                title="Atur Layer Peta"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 group-hover:rotate-12 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </button>

            <!-- Layer Panel -->
            <div 
                v-if="layerPanelOpen"
                :class="[
                    'absolute z-40 w-72 shadow-2xl rounded-2xl transition-all duration-300 overflow-hidden backdrop-blur-xl bg-white/95 dark:bg-slate-900/95 border border-green-500 dark:border-emerald-500/50 text-sm',
                    'top-4 right-4'
                ]"
            >
                <!-- Header with Light Theme matching Footer -->
                <div class="relative bg-linear-to-r from-green-50/90 to-emerald-50/90 dark:from-emerald-950/80 dark:to-slate-900/80 backdrop-blur-sm p-4 border-b border-green-200 dark:border-white/5 flex justify-between items-center">
                    <div class="flex items-center space-x-2">
                        <div class="bg-white/80 dark:bg-emerald-500/10 backdrop-blur-sm rounded-lg p-1.5 border border-green-200 dark:border-emerald-500/30 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-sm tracking-wide text-green-800 dark:text-emerald-50">Layer Control</h3>
                    </div>
                    
                    <button 
                        @click="layerPanelOpen = false" 
                        class="absolute top-4 z-1 right-4 bg-white dark:bg-slate-800 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg p-1.5 transition-all duration-200 hover:scale-110 active:scale-95 cursor-pointer border border-green-200 dark:border-white/10 hover:border-red-200 group"
                        title="Tutup Panel"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-700 dark:text-emerald-400 group-hover:text-red-600 group-hover:rotate-90 transition-transform duration-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-5 space-y-6 bg-linear-to-b from-green-50/50 to-white dark:from-slate-900 dark:to-slate-950">
                    <!-- Base Map Selection -->
                    <div>
                        <h4 class="text-xs font-bold text-green-800 dark:text-emerald-400 uppercase tracking-widest mb-3 flex items-center">
                            <span class="w-1 h-3 bg-green-500 rounded-full mr-2"></span>
                            Base Map
                        </h4>
                        <div class="space-y-2.5">
                            <label class="flex items-center space-x-3 cursor-pointer group bg-white/50 dark:bg-slate-800/50 p-2 rounded-lg border border-green-200 dark:border-white/5 hover:border-green-200 dark:hover:border-emerald-500/30 hover:bg-white dark:hover:bg-slate-800 transition-all">
                                <input type="radio" v-model="selectedBaseMap" value="osm" class="form-radio text-green-600 dark:text-emerald-500 focus:ring-green-500 h-4 w-4">
                                <span class="text-gray-700 dark:text-slate-300 font-medium group-hover:text-green-700 dark:group-hover:text-emerald-400 transition-colors">Peta Jalan (OSM)</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer group bg-white/50 dark:bg-slate-800/50 p-2 rounded-lg border border-green-200 dark:border-white/5 hover:border-green-200 dark:hover:border-emerald-500/30 hover:bg-white dark:hover:bg-slate-800 transition-all">
                                <input type="radio" v-model="selectedBaseMap" value="satellite" class="form-radio text-green-600 dark:text-emerald-500 focus:ring-green-500 h-4 w-4">
                                <span class="text-gray-700 dark:text-slate-300 font-medium group-hover:text-green-700 dark:group-hover:text-emerald-400 transition-colors">Satelit (Esri)</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer group bg-white/50 dark:bg-slate-800/50 p-2 rounded-lg border border-green-200 dark:border-white/5 hover:border-green-200 dark:hover:border-emerald-500/30 hover:bg-white dark:hover:bg-slate-800 transition-all">
                                <input type="radio" v-model="selectedBaseMap" value="googleStreet" class="form-radio text-green-600 dark:text-emerald-500 focus:ring-green-500 h-4 w-4">
                                <span class="text-gray-700 dark:text-slate-300 font-medium group-hover:text-green-700 dark:group-hover:text-emerald-400 transition-colors">Street (Google)</span>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer group bg-white/50 dark:bg-slate-800/50 p-2 rounded-lg border border-green-200 dark:border-white/5 hover:border-green-200 dark:hover:border-emerald-500/30 hover:bg-white dark:hover:bg-slate-800 transition-all">
                                <input type="radio" v-model="selectedBaseMap" value="google" class="form-radio text-green-600 dark:text-emerald-500 focus:ring-green-500 h-4 w-4">
                                <span class="text-gray-700 dark:text-slate-300 font-medium group-hover:text-green-700 dark:group-hover:text-emerald-400 transition-colors">Satelit (Google)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Category Toggles -->
                    <div>
                        <h4 class="text-xs font-bold text-green-800 dark:text-emerald-400 uppercase tracking-widest mb-3 flex items-center">
                            <span class="w-1 h-3 bg-green-500 rounded-full mr-2"></span>
                            Layer Kategori
                        </h4>
                        <div class="space-y-2.5">
                            <label class="flex items-center space-x-3 cursor-pointer group bg-white/50 dark:bg-slate-800/50 p-2 rounded-lg border border-green-200 dark:border-white/5 hover:border-yellow-200 dark:hover:border-yellow-500/30 hover:bg-white dark:hover:bg-slate-800 transition-all">
                                <input type="checkbox" v-model="showSawah" class="form-checkbox rounded text-yellow-500 focus:ring-yellow-400 border-gray-300 h-4 w-4">
                                <div class="flex items-center space-x-2">
                                    <span class="w-3 h-3 rounded-full bg-yellow-400 shadow-sm"></span>
                                    <span class="text-gray-700 dark:text-slate-300 font-medium group-hover:text-yellow-600 dark:group-hover:text-yellow-500 transition-colors">Lahan Sawah</span>
                                </div>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer group bg-white/50 dark:bg-slate-800/50 p-2 rounded-lg border border-green-200 dark:border-white/5 hover:border-red-200 dark:hover:border-red-500/30 hover:bg-white dark:hover:bg-slate-800 transition-all">
                                <input type="checkbox" v-model="showLadang" class="form-checkbox rounded text-red-500 focus:ring-red-400 border-gray-300 h-4 w-4">
                                <div class="flex items-center space-x-2">
                                    <span class="w-3 h-3 rounded-full bg-red-500 shadow-sm"></span>
                                    <span class="text-gray-700 dark:text-slate-300 font-medium group-hover:text-red-600 dark:group-hover:text-red-500 transition-colors">Lahan Ladang</span>
                                </div>
                            </label>
                            <label class="flex items-center space-x-3 cursor-pointer group bg-white/50 dark:bg-slate-800/50 p-2 rounded-lg border border-green-200 dark:border-white/5 hover:border-indigo-200 dark:hover:border-indigo-500/30 hover:bg-white dark:hover:bg-slate-800 transition-all">
                                <input type="checkbox" v-model="showKebun" class="form-checkbox rounded text-indigo-600 focus:ring-indigo-500 border-gray-300 h-4 w-4">
                                <div class="flex items-center space-x-2">
                                    <span class="w-3 h-3 rounded-full bg-indigo-600 shadow-sm"></span>
                                    <span class="text-gray-700 dark:text-slate-300 font-medium group-hover:text-indigo-700 dark:group-hover:text-indigo-500 transition-colors">Lahan Kebun</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Footer Badge -->
                <div class="px-4 py-2 bg-linear-to-r from-green-50/80 to-emerald-50/80 dark:from-emerald-950/80 dark:to-slate-950/80 backdrop-blur-sm border-t border-green-200 dark:border-white/5">
                    <div class="flex items-center justify-center space-x-1.5 text-[10px] text-green-700 dark:text-emerald-400 opacity-80">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        <span class="font-semibold uppercase tracking-wider">GIS Pertanian Bungo</span>
                    </div>
                </div>
            </div>

            <!-- STATS BAR WRAPPER -->
            <div v-if="!agriSawah || !agriLadang || !agriKebun" 
                class="absolute left-4 right-4 z-40 pointer-events-none transition-all duration-300"
                :style="statsBarMobileStyle"
            >
                <div class="flex space-x-2 overflow-x-auto no-scrollbar pb-2">
                    <div v-for="i in 5" :key="i" class="min-w-[130px] lg:min-w-[150px] h-14 bg-white/80 dark:bg-slate-900/80 backdrop-blur-sm rounded-xl border border-gray-200 dark:border-white/5 animate-pulse"></div>
                </div>
            </div>

            <div 
                v-if="agriSawah && agriLadang && agriKebun"
                class="absolute left-4 right-4 z-40 cursor-grab pointer-events-none transition-all duration-300"
                :style="statsBarMobileStyle"
            >
                <div class="overflow-x-auto no-scrollbar pb-2 mask-fade-right">
                    <div class="flex space-x-2 pointer-events-auto min-w-max">
                        <!-- Stat 1: Kecamatan -->
                    <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md p-2 rounded-xl border border-cyan-400 dark:border-cyan-500/30 shadow-md flex items-center space-x-2.5 min-w-[120px] lg:min-w-[160px] lg:p-2.5 lg:rounded-2xl">
                        <div class="bg-cyan-100 dark:bg-cyan-500/10 p-1.5 rounded-lg lg:p-2 lg:rounded-xl border border-cyan-300 dark:border-cyan-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-cyan-700 dark:text-cyan-400 lg:h-5 lg:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <div class="text-[8px] dark:text-cyan-500/80 font-bold text-cyan-800 uppercase tracking-tight leading-none mb-1 lg:text-[9px]">Wilayah</div>
                            <div class="flex items-baseline space-x-1.5 leading-none">
                                <span class="text-sm font-black text-gray-900 dark:text-white lg:text-base">{{ summaryStats.kecamatanCount }}</span>
                                <span class="text-[8px] font-bold text-gray-500 dark:text-slate-400 uppercase">Kec</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 2: Sawah -->
                    <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md p-2 rounded-xl border border-yellow-400 dark:border-yellow-500/30 shadow-md flex items-center space-x-2.5 min-w-[130px] lg:min-w-[150px] lg:p-2.5 lg:rounded-2xl relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-yellow-400"></div>
                        <div class="w-7 h-7 flex items-center justify-center bg-yellow-100 dark:bg-yellow-500/10 rounded-lg text-yellow-700 dark:text-yellow-500 font-black text-xs lg:w-8 lg:h-8 lg:rounded-xl lg:text-sm border border-yellow-300 dark:border-yellow-500/30">S</div>
                        <div>
                            <div class="text-[8px] font-bold text-yellow-950 dark:text-yellow-500/80 uppercase tracking-tight leading-none mb-1 lg:text-[9px]">Lahan Sawah</div>
                            <div class="flex flex-col leading-tight">
                                <div class="text-[11px] font-black text-gray-900 dark:text-white lg:text-[13px]">{{ summaryStats.sawah.count }} <small class="text-[7px] font-normal dark:text-slate-400 lg:text-[8px]">TITIK</small></div>
                                <div class="text-[9px] font-bold text-yellow-700 dark:text-yellow-400 lg:text-[10px]">{{ summaryStats.sawah.area.toFixed(1) }} <small class="text-[7px] dark:text-slate-400 lg:text-[8px]">HA</small></div>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 3: Ladang -->
                    <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md p-2 rounded-xl border border-red-400 dark:border-red-500/30 shadow-md flex items-center space-x-2.5 min-w-[130px] lg:min-w-[150px] lg:p-2.5 lg:rounded-2xl relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-500"></div>
                        <div class="w-7 h-7 flex items-center justify-center bg-red-100 dark:bg-red-500/10 rounded-lg text-red-700 dark:text-red-500 font-black text-xs lg:w-8 lg:h-8 lg:rounded-xl lg:text-sm border border-red-300 dark:border-red-500/30">L</div>
                        <div>
                            <div class="text-[8px] font-bold text-red-950 dark:text-red-500/80 uppercase tracking-tight leading-none mb-1 lg:text-[9px]">Lahan Ladang</div>
                            <div class="flex flex-col leading-tight">
                                <div class="text-[11px] font-black text-gray-900 dark:text-white lg:text-[13px]">{{ summaryStats.ladang.count }} <small class="text-[7px] font-normal dark:text-slate-400 lg:text-[8px]">TITIK</small></div>
                                <div class="text-[9px] font-bold text-red-700 dark:text-red-400 lg:text-[10px]">{{ summaryStats.ladang.area.toFixed(1) }} <small class="text-[7px] dark:text-slate-400 lg:text-[8px]">HA</small></div>
                            </div>
                        </div>
                    </div>

                    <!-- Stat 4: Kebun -->
                    <div class="bg-white/95 dark:bg-slate-900/95 backdrop-blur-md p-2 rounded-xl border border-indigo-400 dark:border-indigo-500/30 shadow-md flex items-center space-x-2.5 min-w-[130px] lg:min-w-[150px] lg:p-2.5 lg:rounded-2xl relative overflow-hidden">
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500"></div>
                        <div class="w-7 h-7 flex items-center justify-center bg-indigo-100 dark:bg-indigo-500/10 rounded-lg text-indigo-700 dark:text-indigo-400 font-black text-xs lg:w-8 lg:h-8 lg:rounded-xl lg:text-sm border border-indigo-300 dark:border-indigo-500/30">K</div>
                        <div>
                            <div class="text-[8px] font-bold text-indigo-950 dark:text-indigo-400 uppercase tracking-tight leading-none mb-1 lg:text-[9px]">Lahan Kebun</div>
                            <div class="flex flex-col leading-tight">
                                <div class="text-[11px] font-black text-white lg:text-[13px]">{{ summaryStats.kebun.count }} <small class="text-[7px] font-normal dark:text-slate-400 lg:text-[8px]">TITIK</small></div>
                                <div class="text-[9px] font-bold text-indigo-700 dark:text-indigo-400 lg:text-[10px]">{{ summaryStats.kebun.area.toFixed(1) }} <small class="text-[7px] dark:text-slate-400 lg:text-[8px]">HA</small></div>
                            </div>
                        </div>
                        </div>

                        <!-- Stat 5: Grand Total -->
                        <div class="bg-gray-900/95 dark:bg-slate-900/95 backdrop-blur-md p-2 rounded-xl border border-gray-700 dark:border-emerald-500/30 shadow-lg flex items-center space-x-2.5 min-w-[160px] lg:min-w-[180px] lg:p-2.5 lg:rounded-2xl">
                            <div class="bg-white/10 p-1.5 rounded-lg text-white dark:text-emerald-400 lg:p-2 lg:rounded-xl">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 lg:h-5 lg:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-[8px] font-bold text-gray-400 dark:text-slate-500 uppercase tracking-tight leading-none mb-1 lg:text-[9px]">Total Pertanian</div>
                                <div class="flex flex-col leading-tight">
                                    <div class="text-[11px] font-black text-white lg:text-[13px]">{{ summaryStats.total.count }} <small class="text-[7px] font-normal text-gray-400 lg:text-[8px]">LOKASI</small></div>
                                    <div class="text-[9px] font-bold text-green-400 dark:text-emerald-400 lg:text-[10px]">{{ summaryStats.total.area.toFixed(1) }} <small class="text-[7px] lg:text-[8px]">HA</small></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</WebLayout>
</template>

<script setup>
import WebLayout from '@/layouts/WebLayout.vue';
import { onMounted, ref, watch, onUnmounted, computed } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({ 
    petaBungo: Array,
    agriSawah: Array,
    agriLadang: Array,
    agriKebun: Array
});

// --- STATE & VARIABEL ---
const mapInstance = ref(null);         // Menyimpan instance peta Leaflet
const windowWidth = ref(window.innerWidth); // Pantau lebar jendela untuk responsivitas
const detailPanelRef = ref(null);      // Referensi ke elemen panel detail
const detailPanelHeight = ref(0);      // Tinggi panel detail untuk penempatan stats
const selectedKecamatan = ref(null);   // Kecamatan yang sedang dipilih
const selectedCategory = ref(null);    // Kategori yang sedang dipilih (sawah, ladang, kebun)
const detailPanelOpen = ref(false);    // Status buka/tutup panel detail
const selectedDetail = ref(null);      // Data detail yang akan ditampilkan di panel
const kecamatanLayers = {};            // Referensi layer poligon kecamatan di peta
const sawahLayers = {};                // Referensi layer poligon sawah
const ladangLayers = {};               // Referensi layer poligon ladang
const kebunLayers = {};                // Referensi layer poligon kebun

// --- STATE UNTUK KONTROL LAYER ---
const layerPanelOpen = ref(false);     // Status buka/tutup panel kontrol layer
const selectedBaseMap = ref('osm');    // Jenis peta dasar yang dipilih (OSM, Satelit, dll)
const showSawah = ref(true);           // Status tampilan layer sawah (on/off)
const showLadang = ref(true);          // Status tampilan layer ladang (on/off)
const showKebun = ref(true);           // Status tampilan layer kebun (on/off)

// Daftar layer peta dasar (Base Maps)
const baseMapLayers = {
    osm: null,
    satellite: null,
    googleStreet: null,
    google: null
};

// Grup layer untuk mempermudah nyala-mati layer secara massal
const sawahGroup = L.layerGroup();
const ladangGroup = L.layerGroup();
const kebunGroup = L.layerGroup();

// --- KOMPUTASI STATISTIK RINGKASAN ---
const summaryStats = computed(() => {
    const sawahArea = props.agriSawah?.reduce((acc, item) => acc + (parseFloat(item.luas_m2) || 0), 0) || 0;
    const ladangArea = props.agriLadang?.reduce((acc, item) => acc + (parseFloat(item.luas_m2) || 0), 0) || 0;
    const kebunArea = props.agriKebun?.reduce((acc, item) => acc + (parseFloat(item.luas_m2) || 0), 0) || 0;
    
    const sawahCount = props.agriSawah?.length || 0;
    const ladangCount = props.agriLadang?.length || 0;
    const kebunCount = props.agriKebun?.length || 0;
    
    return {
        kecamatanCount: props.petaBungo?.length || 0,
        sawah: { count: sawahCount, area: sawahArea / 10000 },
        ladang: { count: ladangCount, area: ladangArea / 10000 },
        kebun: { count: kebunCount, area: kebunArea / 10000 },
        total: {
            count: sawahCount + ladangCount + kebunCount,
            area: (sawahArea + ladangArea + kebunArea) / 10000
        }
    };
});

// --- STATE UNTUK MOBILE ---
const sidebarOpen = ref(false);        // Status buka/tutup sidebar di mobile
const toggleSidebar = () => {          // Fungsi untuk buka/tutup sidebar
    sidebarOpen.value = !sidebarOpen.value;
};

// --- KONFIGURASI WARNA PETA ---
const MAP_COLORS = {
    kecamatan: {
        base: '#475569',        // Warna standar (Slate-600)
        active: '#06b6d4',      // Warna saat dipilih (Cyan-500) - Agar terlihat di satelit
        fillOpacity: 0.1,       // Transparansi standar
        activeFillOpacity: 0.4  // Transparansi saat dipilih
    },
    sawah: {
        base: '#facc15',        // Kuning (Yellow-400)
        border: '#eab308',      // Border Kuning (Yellow-500)
        active: '#ca8a04',      // Kuning saat aktif (Yellow-600)
        activeBorder: '#a16207',// Border aktif (Yellow-700)
        fillOpacity: 0.15,      // Transparansi standar
        activeFillOpacity: 0.6  // Transparansi saat aktif
    },
    ladang: {
        base: '#ef4444',        // Merah (Red-500)
        border: '#dc2626',      // Border Merah (Red-600)
        active: '#dc2626',      // Merah saat aktif
        activeBorder: '#b91c1c',// Border aktif (Red-700)
        fillOpacity: 0.15,
        activeFillOpacity: 0.6
    },
    kebun: {
        base: '#6366f1',        // Indigo (Indigo-500)
        border: '#4f46e5',      // Border Indigo (Indigo-600)
        active: '#4338ca',      // Indigo saat aktif (Indigo-700)
        activeBorder: '#3730a3',// Border aktif (Indigo-800)
        fillOpacity: 0.18,     
        activeFillOpacity: 0.7
    }
};

// --- PENGAWAS (WATCHERS) UNTUK KONTROL LAYER ---
// Mengawasi perubahan peta dasar
watch(selectedBaseMap, (newVal) => {
    if (!mapInstance.value) return;
    
    // Hapus semua peta dasar yang ada
    Object.values(baseMapLayers).forEach(layer => {
        if (layer) mapInstance.value.removeLayer(layer);
    });

    // Tambahkan peta dasar yang dipilih
    if (baseMapLayers[newVal]) {
        baseMapLayers[newVal].addTo(mapInstance.value);
    }
});

// Mengawasi tombol toggle layer sawah
watch(showSawah, (val) => {
    if (!mapInstance.value) return;
    if (val) sawahGroup.addTo(mapInstance.value);
    else mapInstance.value.removeLayer(sawahGroup);
});

// Mengawasi tombol toggle layer ladang
watch(showLadang, (val) => {
    if (!mapInstance.value) return;
    if (val) ladangGroup.addTo(mapInstance.value);
    else mapInstance.value.removeLayer(ladangGroup);
});

// Mengawasi tombol toggle layer kebun
watch(showKebun, (val) => {
    if (!mapInstance.value) return;
    if (val) kebunGroup.addTo(mapInstance.value);
    else mapInstance.value.removeLayer(kebunGroup);
});

// --- FUNGSI BANTU (HELPERS) ---
// Mengambil data农业 per kategori
const getAgriProp = (category) => {
    const mapping = {
        sawah: props.agriSawah,
        ladang: props.agriLadang,
        kebun: props.agriKebun
    };
    return mapping[category] || [];
};

// Mengambil variabel layer per kategori
const getAgriLayers = (category) => {
    const mapping = {
        sawah: sawahLayers,
        ladang: ladangLayers,
        kebun: kebunLayers
    };
    return mapping[category] || {};
};

// Menghitung jumlah lokasi pertanian dalam suatu kecamatan
const getAgriCount = (category, kecId) => {
    const data = getAgriProp(category);
    return data.filter(item => item.kecamatan_gid === kecId).length;
};

// Mengambil daftar data pertanian untuk kecamatan tertentu
const getAgriByKecamatan = (category, kecId) => {
    const data = getAgriProp(category);
    return data.filter(item => item.kecamatan_gid === kecId);
};

// --- FUNGSI AKSI & NAVIGASI ---
// Mereset semua gaya poligon kecamatan ke kondisi awal
const resetKecamatanStyles = () => {
    Object.values(kecamatanLayers).forEach(l => l.setStyle({ 
        fillOpacity: MAP_COLORS.kecamatan.fillOpacity, 
        weight: 1.5,
        color: MAP_COLORS.kecamatan.base
    }));
};

// Memberikan highlight (efek sorot) pada kecamatan tertentu
const applyKecamatanHighlight = (kecId) => {
    resetKecamatanStyles();
    const layer = kecamatanLayers[kecId];
    if (layer) {
        layer.setStyle({ 
            fillOpacity: MAP_COLORS.kecamatan.activeFillOpacity, 
            weight: 3, 
            color: MAP_COLORS.kecamatan.active 
        });
    }
};

// MEMILIH KECAMATAN: Menampilkan kategori (Level 1 -> Level 2)
const selectKecamatanForCategories = (kecamatan) => {
    selectedKecamatan.value = kecamatan;
    selectedCategory.value = null; // Reset pilihan kategori
    
    // Zoom ke poligon kecamatan dan berikan efek highlight
    const layer = kecamatanLayers[kecamatan.gid];
    if (layer) {
        const isMobile = window.innerWidth < 1024;
        const fitOptions = {
            // Sesuaikan padding peta agar poligon tidak tertutup panel (responsif)
            paddingTopLeft: isMobile ? [10, 10] : [250, 50],
            paddingBottomRight: isMobile ? [10, isMobile ? window.innerHeight * 0.45 : 50] : [50, 50]
        };
        
        mapInstance.value.fitBounds(layer.getBounds(), fitOptions);
        applyKecamatanHighlight(kecamatan.gid);
    }
    
    // Reset gaya poligon pertanian lainnya
    resetAllAgriStyles();
    
    // Tampilkan informasi kecamatan di panel detail
    selectedDetail.value = {
        title: kecamatan.namobj,
        type: 'Kecamatan',
        data: {
            'Nama': kecamatan.namobj,
            'Luas': `${(kecamatan.luas_m2 / 1000000).toFixed(2)} km²`,
            'Luas (m²)': kecamatan.luas_m2.toLocaleString('id-ID')
        }
    };
    detailPanelOpen.value = true;
};

// MEMILIH KATEGORI: Menampilkan list lokasi (Level 2 -> Level 3)
const selectCategory = (category) => {
    selectedCategory.value = category;
};

// KEMBALI KE LIST KECAMATAN (Level 2 -> Level 1)
const backToKecamatanList = () => {
    selectedKecamatan.value = null;
    selectedCategory.value = null;
    detailPanelOpen.value = false;
    resetKecamatanStyles(); // Kembalikan warna peta kecamatan ke normal
};

// KEMBALI KE KATEGORI (Level 3 -> Level 2)
const backToCategories = () => {
    selectedCategory.value = null;
};

// Mereset semua gaya poligon pertanian ke bentuk awal
const resetAllAgriStyles = () => {
    // Reset poligon Sawah
    Object.values(sawahLayers).forEach(l => l.setStyle({ 
        fillColor: MAP_COLORS.sawah.base,
        color: MAP_COLORS.sawah.border,
        weight: 1,
        fillOpacity: MAP_COLORS.sawah.fillOpacity
    }));

    // Reset poligon Ladang
    Object.values(ladangLayers).forEach(l => l.setStyle({ 
        fillColor: MAP_COLORS.ladang.base,
        color: MAP_COLORS.ladang.border,
        weight: 1,
        fillOpacity: MAP_COLORS.ladang.fillOpacity
    }));

    // Reset poligon Kebun
    Object.values(kebunLayers).forEach(l => l.setStyle({ 
        fillColor: MAP_COLORS.kebun.base,
        color: MAP_COLORS.kebun.border,
        weight: 1,
        fillOpacity: MAP_COLORS.kebun.fillOpacity
    }));
};

// Handler saat poligon kecamatan di peta diklik
const toggleKecamatan = (kecamatan) => {
    selectKecamatanForCategories(kecamatan); // Pindah navigasi
    resetAllAgriStyles();                    // Bersihkan sorotan pertanian lain
};

// MEMILIH ITEM PERTANIAN: Menyorot poligon dan membuka panel detail
const selectAgriItem = (item, category, fromMap = false) => {
    const layers = getAgriLayers(category);
    const layer = layers[item.gid];
    
    // Otomatis sinkronkan konteks navigasi di sidebar
    const kecamatan = props.petaBungo.find(k => k.gid === item.kecamatan_gid);
    if (kecamatan) {
        selectedKecamatan.value = kecamatan;
        if (fromMap) selectedCategory.value = category;
        applyKecamatanHighlight(kecamatan.gid);
    }
    
    resetAllAgriStyles();
    
    if (layer) {
        const isMobile = window.innerWidth < 1024;
        const fitOptions = {
            maxZoom: 16,
            animate: true,
            duration: 0.5
        };

        // Atur padding peta agar item tidak tertutup oleh panel detail
        if (isMobile) {
            fitOptions.paddingBottomRight = [10, window.innerHeight * 0.45]; 
            fitOptions.paddingTopLeft = [10, 10];
        } else {
            fitOptions.paddingTopLeft = [250, 50];
            fitOptions.paddingBottomRight = [50, 50];
        }

        mapInstance.value.fitBounds(layer.getBounds(), fitOptions);
        
        // Ubah gaya poligon yang dipilih agar terlihat menyala
        layer.setStyle({ 
            fillColor: MAP_COLORS[category].active,
            color: MAP_COLORS[category].activeBorder,
            weight: 3,
            fillOpacity: MAP_COLORS[category].activeFillOpacity
        });
    }
    
    // Siapkan data untuk ditampilkan di panel detail
    const labels = { sawah: 'Lahan Sawah', ladang: 'Lahan Ladang', kebun: 'Lahan Kebun' };
    selectedDetail.value = {
        gid: item.gid,
        title: item.namobj,
        type: labels[category] || 'Pertanian',
        data: {
            'Jenis': item.remark,
            'Luas': `${(item.luas_m2 / 10000).toFixed(2)} Ha`,
            'Luas (m²)': item.luas_m2.toLocaleString('id-ID')
        }
    };
    detailPanelOpen.value = true;
};

// MENGGAMBAR LAYER PERTANIAN: Fungsi untuk me-render data poligon ke peta
const renderAgriLayers = (data, category, group, layerMap) => {
    if (!data) return;
    
    data.forEach(item => {
        if (!item.geojson) return;
        try {
            const geojson = JSON.parse(item.geojson);
            L.geoJSON(geojson, {
                style: {
                    color: MAP_COLORS[category].border,
                    weight: 1,
                    fillColor: MAP_COLORS[category].base,
                    fillOpacity: MAP_COLORS[category].fillOpacity
                },
                onEachFeature: (feature, layer) => {
                    layerMap[item.gid] = layer; // Simpan referensi layer berdasarkan GID
                    layer.on({
                        // Saat diklik di peta
                        click: (e) => {
                            L.DomEvent.stopPropagation(e);
                            selectAgriItem(item, category, true);
                        },
                        // Saat mouse di atas poligon (hover)
                        mouseover: (e) => {
                            if (selectedDetail.value?.gid !== item.gid) {
                                layer.setStyle({ fillOpacity: 0.4 });
                            }
                        },
                        // Saat mouse meninggalkan poligon
                        mouseout: (e) => {
                            if (selectedDetail.value?.gid !== item.gid) {
                                layer.setStyle({ fillOpacity: MAP_COLORS[category].fillOpacity });
                            }
                        }
                    });
                }
            }).addTo(group);
        } catch (e) {
            console.error(`Error parsing geojson for ${category} GID ${item.gid}`, e);
        }
    });
};

// Menutup panel detail
const closeDetailPanel = () => {
    detailPanelOpen.value = false;
};

const isLargeScreen = computed(() => windowWidth.value >= 1024);

// Gaya dinamis untuk stats bar agar posisi pas di mobile & desktop
const statsBarMobileStyle = computed(() => {
    // Desktop: Bottom 16px, Left 16px, Right 16px (bottom-4 left-4 right-4)
    if (isLargeScreen.value) {
        return { 
            bottom: '16px',
            left: '45px',
            right: '45px'
        };
    }
    
    // Mobile: Left/Right 16px per default dari class, Bottom dinamis
    if (detailPanelOpen.value && detailPanelHeight.value > 0) {
        return { bottom: `${detailPanelHeight.value + 15}px` };
    }
    
    return { bottom: '10px' };
});

// Pantau perubahan tinggi panel detail
watch([detailPanelOpen, detailPanelRef], () => {
    if (detailPanelOpen.value) {
        // Beri sedikit jeda agar DOM ter-render
        setTimeout(() => {
            if (detailPanelRef.value) {
                detailPanelHeight.value = detailPanelRef.value.offsetHeight;
            }
        }, 50);
    } else {
        detailPanelHeight.value = 0;
    }
}, { deep: true });
const handleResize = () => {
    windowWidth.value = window.innerWidth;
};

// --- INISIALISASI SAAT HALAMAN DIMUAT ---
onMounted(() => {
    window.addEventListener('resize', handleResize);
    // Buat objek peta dan arahkan ke Kabupaten Bungo
    const map = L.map('map', { 
        zoomControl: false,
        preferCanvas: true,
        zoomSnap: 0.1,
        zoomDelta: 0.1
    }).setView([-1.52, 101.9], 10.7);
    mapInstance.value = map;
    
    // Tambahkan kontrol zoom kustom (Atas kiri untuk mobile agar tidak tumpang tindih)
    L.control.zoom({ 
        position: windowWidth.value < 1024 ? 'topleft' : 'bottomright' 
    }).addTo(map);

    // Konfigurasi Peta Dasar (OSM, Satellite, Google)
    baseMapLayers.osm = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap',
        maxZoom: 19
    });

    baseMapLayers.satellite = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri',
        maxZoom: 17
    });

    baseMapLayers.googleStreet = L.tileLayer('http://{s}.google.com/vt/lyrs=m&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0','mt1','mt2','mt3'],
        attribution: '&copy; Google Maps'
    });

    baseMapLayers.google = L.tileLayer('http://{s}.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', {
        maxZoom: 20,
        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
        attribution: '&copy; Google Maps'
    });

    // Pasang OSM sebagai peta standar
    baseMapLayers.osm.addTo(map);

    // Tambahkan semua grup layer ke peta agar siap ditampilkan
    sawahGroup.addTo(map);
    ladangGroup.addTo(map);
    kebunGroup.addTo(map);

    // Rend Poligon Kecamatan
    props.petaBungo.forEach(item => {
        if(item.geojson) {
            const geojsonFeature = JSON.parse(item.geojson);
            const layer = L.geoJSON(geojsonFeature, {
                style: {
                    color: MAP_COLORS.kecamatan.base,
                    weight: 1.5,
                    fillColor: MAP_COLORS.kecamatan.base,
                    fillOpacity: MAP_COLORS.kecamatan.fillOpacity
                },
                onEachFeature: (feature, layer) => {
                    // Klik kecamatan di peta -> Hubungkan ke sidebar/detail
                    layer.on('click', () => {
                        toggleKecamatan(item);
                    });

                    // Sorotan saat mouse lewat (hover)
                    layer.on('mouseover', () => {
                        if(selectedKecamatan.value?.gid !== item.gid) layer.setStyle({ fillOpacity: 0.2 });
                    });
                    layer.on('mouseout', () => {
                        if(selectedKecamatan.value?.gid !== item.gid) layer.setStyle({ fillOpacity: 0.1 });
                    });
                }
            }).addTo(map);
            
            kecamatanLayers[item.gid] = layer; // Simpan untuk referensi nanti
        }
    });

    // Render Data Pertanian saat tersedia (Deferred)
    watch(() => props.agriSawah, (newData) => {
        if (newData) renderAgriLayers(newData, 'sawah', sawahGroup, sawahLayers);
    }, { immediate: true });

    watch(() => props.agriLadang, (newData) => {
        if (newData) renderAgriLayers(newData, 'ladang', ladangGroup, ladangLayers);
    }, { immediate: true });

    watch(() => props.agriKebun, (newData) => {
        if (newData) renderAgriLayers(newData, 'kebun', kebunGroup, kebunLayers);
    }, { immediate: true });
});

// PEMBERSIHAN (MANAJEMEN MEMORI): Hapus peta saat halaman ditinggalkan
onUnmounted(() => {
    window.removeEventListener('resize', handleResize);
    if (mapInstance.value) {
        mapInstance.value.remove(); // Hapus instance peta dari memori
        mapInstance.value = null;
    }
});
</script>

<style>
/* Kostumisasi gaya Leaflet */
#map { z-index: 1; outline: none; }
.leaflet-popup-content-wrapper { border-radius: 12px; }

/* Scrollbar halus untuk sidebar & stats */
.overflow-y-auto::-webkit-scrollbar,
.overflow-x-auto::-webkit-scrollbar {
    width: 4px;
    height: 4px;
}
.overflow-y-auto::-webkit-scrollbar-thumb,
.overflow-x-auto::-webkit-scrollbar-thumb {
    background-color: #e2e8f0;
    border-radius: 10px;
}
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Tambahkan efek pudar di kanan pada mobile untuk menunjukkan masih ada konten */
@media (max-width: 1023px) {
    .mask-fade-right {
        -webkit-mask-image: linear-gradient(to right, black 85%, transparent 100%);
        mask-image: linear-gradient(to right, black 85%, transparent 100%);
    }
    
    /* Geser tombol zoom agar tidak tertutup navbar fixed */
    .leaflet-top.leaflet-left {
        margin-top: 85px !important;
        margin-left: 10px !important;
    }

    /* Attribution (kanan bawah) */
    .leaflet-bottom.leaflet-right {
        margin-bottom: 20px !important;
        margin-right: 10px !important;
    }
}

/* Desktop styles specifically for avoiding navbar overlay */
@media (min-width: 1024px) {
    .leaflet-top.leaflet-left {
        margin-top: 20px !important;
        margin-left: 20px !important;
    }
}
</style>
