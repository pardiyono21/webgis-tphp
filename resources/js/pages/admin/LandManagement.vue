<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { Search, Plus, Edit, Trash2, Leaf, Mountain, Trees, Upload, X, Save, AlertCircle } from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';
import MapEditor from '@/components/MapEditor.vue';
import DeleteConfirmModal from '@/components/DeleteConfirmModal.vue';

interface LandData {
    gid: number;
    namobj: string;
    remark: string;
    luas_m2: number;
    geojson: string;
}

interface Paginator {
    data: LandData[];
    links: any[];
    total: number;
    from: number;
    to: number;
}

const props = defineProps<{
    landData: Paginator;
    type: 'sawah' | 'ladang' | 'kebun';
    title: string;
}>();

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: props.title, href: `/admin/lahan-${props.type}` },
];

const searchQuery = ref('');
const activeGeoMode = ref<'upload' | 'digitize'>('upload');
const isMapFullscreen = ref(false);

const filteredData = computed(() => {
    return props.landData.data.filter(d => 
        (d.namobj?.toLowerCase() || '').includes(searchQuery.value.toLowerCase()) ||
        (d.remark?.toLowerCase() || '').includes(searchQuery.value.toLowerCase())
    );
});

const formatArea = (m2: number) => {
    return (m2 / 10000).toLocaleString('id-ID', { maximumFractionDigits: 2 }) + ' Ha';
};

const getIcon = () => {
    if (props.type === 'sawah') return Leaf;
    if (props.type === 'ladang') return Mountain;
    return Trees;
};

const getIconColor = () => {
    if (props.type === 'sawah') return 'text-emerald-600 dark:text-emerald-400 bg-emerald-100 dark:bg-emerald-500/20';
    if (props.type === 'ladang') return 'text-amber-600 dark:text-amber-400 bg-amber-100 dark:bg-amber-500/20';
    return 'text-teal-600 dark:text-teal-400 bg-teal-100 dark:bg-teal-500/20';
};

// MODAL LOGIC (CREATE & EDIT)
const isModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const isEditMode = ref(false);
const selectedGid = ref<number | null>(null);
const itemToDelete = ref<LandData | null>(null);
const isDeleting = ref(false);

const form = useForm({
    namobj: '',
    remark: '',
    geojson: '',
});

const openCreateModal = () => {
    isEditMode.value = false;
    activeGeoMode.value = 'upload';
    selectedGid.value = null;
    form.reset();
    form.clearErrors();
    isModalOpen.value = true;
};

const openEditModal = (item: LandData) => {
    isEditMode.value = true;
    activeGeoMode.value = item.geojson ? 'digitize' : 'upload'; // Default to map if data exists
    selectedGid.value = item.gid;
    form.clearErrors();
    form.namobj = item.namobj || '';
    form.remark = item.remark || '';
    form.geojson = item.geojson || '';
    isModalOpen.value = true;
};

const handleFileUpload = (event: Event) => {
    const file = (event.target as HTMLInputElement).files?.[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            try {
                const content = e.target?.result as string;
                JSON.parse(content);
                form.geojson = content;
            } catch (err) {
                alert('File bukan format JSON/GeoJSON yang valid.');
            }
        };
        reader.readAsText(file);
    }
};

const page = usePage();

const submitForm = () => {
    if (isEditMode.value && selectedGid.value) {
        // UPDATE
        form.put(`/admin/lahan-${props.type}/${selectedGid.value}`, {
            onSuccess: () => {
                if (page.props.flash.success) {
                    isModalOpen.value = false;
                    form.reset();
                }
            },
        });
    } else {
        // CREATE
        form.post(`/admin/lahan-${props.type}`, {
            onSuccess: () => {
                if (page.props.flash.success) {
                    isModalOpen.value = false;
                    form.reset();
                }
            },
        });
    }
};

const deleteItem = (item: LandData) => {
    itemToDelete.value = item;
    isDeleteModalOpen.value = true;
};

const confirmDelete = () => {
    if (!itemToDelete.value) return;
    
    isDeleting.value = true;
    form.delete(`/admin/lahan-${props.type}/${itemToDelete.value.gid}`, {
        onSuccess: () => {
            isDeleteModalOpen.value = false;
            itemToDelete.value = null;
        },
        onFinish: () => {
            isDeleting.value = false;
        }
    });
};

const ucfirst = (s: string) => s.charAt(0).toUpperCase() + s.slice(1);
</script>

<template>
    <Head :title="title" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-8 p-4 pt-0 lg:p-8">
            <!-- HEADER SECTION -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-black text-slate-900 dark:text-white tracking-tight">{{ title }}</h1>
                    <p class="text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">Manajemen data spasial area {{ type }} di Kabupaten Bungo.</p>
                </div>
                <button 
                    @click="openCreateModal"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-3 rounded-2xl font-black text-sm transition-all shadow-lg active:scale-95 flex items-center justify-center gap-2"
                >
                    <Plus class="h-4 w-4" />
                    Tambah Data
                </button>
            </div>

            <!-- FILTERS & SEARCH -->
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-1">
                    <Search class="absolute left-4 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" />
                    <input 
                        v-model="searchQuery"
                        type="text" 
                        placeholder="Cari data lahan..." 
                        class="w-full pl-12 pr-4 py-3 bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-2xl text-sm outline-none transition-all duration-200 ease-out focus:border-emerald-500 focus:ring-emerald-500/25 focus:ring-[3px] focus:bg-accent/30 hover:border-emerald-500/40 hover:bg-emerald-500/5"
                    >
                </div>
            </div>

            <!-- DATA TABLE -->
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-[32px] overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-800/50 border-bottom border-slate-200 dark:border-white/5">
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400">GID</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400">Objek Lahan</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400">Keterangan (SHP)</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400">Luas Estimasi</th>
                                <th class="px-4 py-3 text-[10px] font-black uppercase tracking-widest text-slate-400 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <tr v-for="item in filteredData" :key="item.gid" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition-colors group">
                                <td class="px-4 py-3">
                                    <span class="text-xs font-mono text-slate-400">#{{ item.gid }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div :class="['w-8 h-8 rounded-lg flex items-center justify-center', getIconColor()]">
                                            <component :is="getIcon()" class="h-4 w-4" />
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ item.namobj || 'Tidak Bernama' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400 font-medium">
                                    {{ item.remark }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-3 py-1 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-full text-[10px] font-bold">
                                        {{ formatArea(item.luas_m2) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button 
                                            @click="openEditModal(item)"
                                            class="p-2 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-xl transition-all active:scale-90"
                                        >
                                            <Edit class="h-4 w-4" />
                                        </button>
                                        <button 
                                            @click="deleteItem(item)"
                                            class="p-2 hover:bg-rose-50 dark:hover:bg-rose-500/10 text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 rounded-xl transition-all active:scale-90"
                                        >
                                            <Trash2 class="h-4 w-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- PAGINATION -->
                <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-white/5 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Menampilkan {{ landData.total > 0 ? landData.from : 0 }} - {{ landData.to }} dari {{ landData.total }} Data</p>
                    <Pagination :links="landData.links" />
                </div>
            </div>
        </div>

        <!-- CREATE/EDIT MODAL -->
        <Dialog :open="isModalOpen" @update:open="isModalOpen = $event">
            <DialogContent 
                class="sm:max-w-[600px] max-h-[95vh] flex flex-col rounded-[32px] border-none p-0 overflow-hidden shadow-2xl"
                @pointer-down-outside="(e) => { if (isMapFullscreen) e.preventDefault() }"
                @interact-outside="(e) => { if (isMapFullscreen) e.preventDefault() }"
            >
                <DialogHeader class="px-8 pt-8 pb-6 bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-white/5 flex-shrink-0">
                    <DialogTitle class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                        {{ isEditMode ? `Edit Lahan ${ucfirst(type)}` : `Tambah Lahan ${ucfirst(type)}` }}
                    </DialogTitle>
                    <DialogDescription class="text-slate-500 dark:text-slate-400 font-medium">
                        {{ isEditMode ? 'Perbarui atribut atau geometri lahan.' : 'Masukkan data atribut dan file GeoJSON untuk area lahan baru.' }}
                    </DialogDescription>
                </DialogHeader>

                <form @submit.prevent="submitForm" class="flex flex-col flex-1 overflow-hidden">
                    <div class="flex-1 overflow-y-auto p-8 space-y-6 bg-white dark:bg-slate-900">
                        <div class="grid grid-cols-1 gap-6">
                            <div class="space-y-2">
                                <Label for="namobj" class="text-[10px] font-black uppercase tracking-widest text-slate-400">Nama Objek Lahan</Label>
                                <Input id="namobj" v-model="form.namobj" placeholder="Contoh: Sawah Irigasi A" />
                            </div>
                            <div class="space-y-2">
                                <Label for="remark" class="text-[10px] font-black uppercase tracking-widest text-slate-400">Keterangan (SHP)</Label>
                                <Input id="remark" v-model="form.remark" placeholder="Contoh: Area Pertanian Aktif" />
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <Label class="text-[10px] font-black uppercase tracking-widest text-slate-400">Geometri Poligon (Spasial)</Label>
                                <div class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-lg">
                                    <button 
                                        type="button"
                                        @click="activeGeoMode = 'upload'"
                                        :class="['px-3 py-1 text-[10px] font-bold rounded-md transition-all', activeGeoMode === 'upload' ? 'bg-white dark:bg-slate-700 text-emerald-600 shadow-sm' : 'text-slate-400']"
                                    >
                                        Upload File
                                    </button>
                                    <button 
                                        type="button"
                                        @click="activeGeoMode = 'digitize'"
                                        :class="['px-3 py-1 text-[10px] font-bold rounded-md transition-all', activeGeoMode === 'digitize' ? 'bg-white dark:bg-slate-700 text-emerald-600 shadow-sm' : 'text-slate-400']"
                                    >
                                        Digitasi Peta
                                    </button>
                                </div>
                            </div>

                            <!-- UPLOAD MODE -->
                            <div v-if="activeGeoMode === 'upload'" class="space-y-3 p-6 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl border border-emerald-100 dark:border-emerald-500/20">
                                <div class="flex items-center gap-2">
                                    <Upload class="h-4 w-4 text-emerald-600" />
                                    <h4 class="text-xs font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Update via GeoJSON</h4>
                                </div>
                                <p class="text-[10px] text-emerald-600/70 dark:text-emerald-400/60 font-medium">
                                    {{ isEditMode ? 'Kosongkan jika tidak ingin mengubah poligon.' : 'File GeoJSON wajib diunggah untuk data baru.' }}
                                </p>
                                
                                <div class="mt-2 text-center">
                                    <label class="px-4 py-2 bg-white dark:bg-slate-800 border border-emerald-200 dark:border-emerald-500/30 rounded-lg text-xs font-bold text-emerald-600 cursor-pointer hover:bg-emerald-50 transition-colors inline-block">
                                        <input type="file" @change="handleFileUpload" class="hidden" accept=".geojson,.json">
                                        {{ form.geojson ? 'Ganti File' : 'Pilih File GeoJSON' }}
                                    </label>
                                    <div v-if="form.geojson" class="mt-2 flex items-center justify-center gap-2">
                                        <span class="text-[10px] font-bold text-emerald-600">Terdeteksi ✔</span>
                                        <button type="button" @click="form.geojson = ''" class="text-rose-500"><X class="h-3 w-3" /></button>
                                    </div>
                                </div>
                            </div>

                            <!-- DIGITIZE MODE -->
                            <div v-else class="space-y-3">
                                <MapEditor v-model="form.geojson" height="400px" @fullscreen="(val) => isMapFullscreen = val" />
                                <p class="text-[10px] text-slate-400 font-medium italic text-right">* Gunakan toolbar di kiri peta untuk menggambar atau mengedit poligon.</p>
                            </div>
                        </div>

                        <div v-if="form.errors.geojson" class="p-3 bg-rose-50 dark:bg-rose-500/10 border border-rose-100 dark:border-rose-500/20 rounded-xl flex items-center gap-2 text-rose-600">
                            <AlertCircle class="h-4 w-4" />
                            <span class="text-[10px] font-bold">{{ form.errors.geojson }}</span>
                        </div>
                    </div>

                    <DialogFooter class="px-8 py-6 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-white/5 flex-shrink-0 flex-col sm:flex-row gap-2">
                        <Button type="button" variant="ghost" @click="isModalOpen = false" class="rounded-xl font-bold">Batal</Button>
                        <Button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold px-8 shadow-lg transition-all active:scale-95" :disabled="form.processing">
                            <Save v-if="!form.processing" class="h-4 w-4 mr-2" />
                            <span v-else class="mr-2">...</span>
                            {{ isEditMode ? 'Simpan Data' : 'Tambah Data' }}
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <!-- DELETE CONFIRMATION MODAL -->
        <DeleteConfirmModal
            :open="isDeleteModalOpen"
            :title="`Hapus Lahan ${ucfirst(type)}?`"
            :description="`Apakah Anda yakin ingin menghapus data lahan ${itemToDelete?.namobj || '#'+itemToDelete?.gid}? Seluruh data spasial dan atribut terkait akan hilang permanen.`"
            :loading="isDeleting"
            @close="isDeleteModalOpen = false"
            @confirm="confirmDelete"
        />
    </AppLayout>
</template>
