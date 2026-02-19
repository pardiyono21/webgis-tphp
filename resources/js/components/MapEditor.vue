<script setup lang="ts">
import { ref, onMounted, onUnmounted, watch, nextTick } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import '@geoman-io/leaflet-geoman-free';
import '@geoman-io/leaflet-geoman-free/dist/leaflet-geoman.css';
import { Maximize2, Minimize2 } from 'lucide-vue-next';

const props = defineProps<{
    modelValue?: string; // GeoJSON string
    height?: string;
}>();

const emit = defineEmits(['update:modelValue', 'fullscreen']);

const mapContainer = ref<HTMLElement | null>(null);
const isFullscreen = ref(false);
let map: L.Map | null = null;
let geoJsonLayer: L.GeoJSON | null = null;

const toggleFullscreen = () => {
    isFullscreen.value = !isFullscreen.value;
    emit('fullscreen', isFullscreen.value);
    // Leaflet needs to know when the container size changes
    nextTick(() => {
        if (map) {
            setTimeout(() => {
                map?.invalidateSize();
            }, 300); // Wait for CSS transitions
        }
    });
};

const updateGeoJSON = () => {
    if (!map) return;
    
    // Menggunakan metode Geoman yang lebih robust untuk mengambil semua layer yang aktif
    // @ts-ignore
    const layers = map.pm.getGeomanLayers();
    
    if (layers.length > 0) {
        // Kita fokus pada satu geometry saja (Polygon/Rectangle)
        // Filter layer yang valid dan bukan temporary Geoman layer
        const validLayer = layers.find((l: any) => 
            l && typeof l.toGeoJSON === 'function' && !l._pmTempLayer
        );

        if (validLayer) {
            const geoJson = (validLayer as any).toGeoJSON();
            // Pastikan yang dikirim ke v-model adalah bagian 'geometry' saja sesuai format DB
            emit('update:modelValue', JSON.stringify(geoJson.geometry));
        }
    } else {
        // Emit string kosong jika tidak ada layer (hapus data spasial)
        emit('update:modelValue', '');
    }
};

const initMap = () => {
    if (!mapContainer.value) return;

    map = L.map(mapContainer.value).setView([-1.48, 102.13], 13); // Default to Bungo area

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    // Initial Geoman Setup
    // @ts-ignore
    map.pm.addControls({
        position: 'topleft',
        drawMarker: false,
        drawCircleMarker: false,
        drawPolyline: false,
        drawRectangle: true,
        drawPolygon: true,
        drawCircle: false,
        editMode: true,
        dragMode: true,
        cutPolygon: false,
        removalMode: true,
    });

    // @ts-ignore
    map.pm.setLang('id');

    // Load initial GeoJSON if exists
    if (props.modelValue) {
        try {
            const data = JSON.parse(props.modelValue);
            geoJsonLayer = L.geoJSON(data, {
                onEachFeature: (_, layer) => {
                    // Sangat penting: tambahkan layer ke peta secara langsung agar Geoman bisa bekerja
                    layer.addTo(map!);
                    
                    // @ts-ignore
                    if (layer.pm) {
                        const events = [
                            'pm:edit', 
                            'pm:dragend', 
                            'pm:rotateend', 
                            'pm:markerdragend', 
                            'pm:vertexadded', 
                            'pm:vertexremoved'
                        ];
                        events.forEach(evt => {
                            // @ts-ignore
                            layer.on(evt, updateGeoJSON);
                        });
                    }
                }
            }).addTo(map);
            
            const bounds = geoJsonLayer.getBounds();
            if (bounds.isValid()) {
                map.fitBounds(bounds);
            }
        } catch (e) {
            console.error('Invalid GeoJSON data', e);
        }
    }

    // Global listeners pada map untuk menangkap event dari toolbar
    // @ts-ignore
    map.on('pm:create', (e: any) => {
        // Hapus layer lama (Singleton Mode)
        // @ts-ignore
        const layers = map!.pm.getGeomanLayers();
        layers.forEach((layer: any) => {
            if (layer !== e.layer) {
                map?.removeLayer(layer);
            }
        });
        
        // Pasang listener pada layer baru
        const events = ['pm:edit', 'pm:dragend', 'pm:rotateend', 'pm:markerdragend', 'pm:vertexadded', 'pm:vertexremoved'];
        events.forEach(evt => {
            e.layer.on(evt, updateGeoJSON);
        });
        
        updateGeoJSON();
    });

    // @ts-ignore
    map.on('pm:remove', () => {
        updateGeoJSON();
    });

    // Tambahan: Sinkronisasi saat mode edit dimatikan (finishing touch)
    // @ts-ignore
    map.on('pm:globaleditmodetoggled', (e: any) => {
        if (!e.enabled) {
            updateGeoJSON();
        }
    });
};

onMounted(() => {
    initMap();
});

onUnmounted(() => {
    if (map) {
        map.remove();
    }
});

watch(() => props.modelValue, (newVal) => {
    // @ts-ignore
    if (!newVal && map && map.pm) {
        // @ts-ignore
        map.pm.getGeomanLayers().forEach((layer: any) => map?.removeLayer(layer));
    }
});
</script>

<template>
    <Teleport :disabled="!isFullscreen" to="body">
        <div 
            :class="[
                'transition-all duration-300 ease-in-out',
                isFullscreen ? 'fixed inset-0 z-[9999] bg-white dark:bg-slate-950 !pointer-events-auto' : 'relative w-full overflow-hidden rounded-2xl border border-slate-200 dark:border-white/10 shadow-inner bg-slate-50 dark:bg-slate-950/50'
            ]"
        >
            <div 
                ref="mapContainer" 
                :style="{ height: isFullscreen ? '100%' : (height || '650px') }"
                class="z-0 w-full"
            ></div>
            
            <!-- Controls Overlay -->
            <div :class="['absolute z-[1000] flex flex-col items-end gap-2', isFullscreen ? 'top-6 right-6' : 'top-4 right-4']">
                <button 
                    type="button"
                    @click.stop="toggleFullscreen"
                    class="p-2.5 bg-white/90 dark:bg-slate-900/90 backdrop-blur-md rounded-xl shadow-xl hover:bg-white dark:hover:bg-slate-800 transition-all active:scale-95 group border border-slate-200/50 dark:border-white/10"
                    title="Toggle Fullscreen"
                >
                    <Minimize2 v-if="isFullscreen" class="h-5 w-5 text-emerald-600" />
                    <Maximize2 v-else class="h-5 w-5 text-slate-600 dark:text-slate-400 group-hover:text-emerald-600" />
                </button>
                
                <div v-if="!isFullscreen" class="pointer-events-none self-end">
                    <span class="px-2.5 py-1 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md rounded-lg text-[9px] font-black text-slate-500 uppercase tracking-widest shadow-sm border border-slate-100 dark:border-white/5">
                        Mode Editor
                    </span>
                </div>
            </div>

            <!-- Fullscreen Close Button Bottom -->
            <div v-if="isFullscreen" class="absolute bottom-10 left-1/2 -translate-x-1/2 z-[1000]">
                <button 
                    @click.stop="toggleFullscreen"
                    type="button"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-2xl font-black text-sm transition-all shadow-2xl active:scale-95 flex items-center gap-2"
                >
                    <Minimize2 class="h-4 w-4" />
                    Selesai Digitasi
                </button>
            </div>
        </div>
    </Teleport>
</template>

<style lang="postcss">
/* Leaflet UI adjustments for premium feel */
.leaflet-control-attribution {
    @apply !text-[10px] !bg-white/50 dark:!bg-black/50 !backdrop-blur-sm !border-none !rounded-tl-lg;
}

.leaflet-pm-toolbar {
    @apply !border-none !shadow-2xl !rounded-2xl !overflow-hidden;
}

.leaflet-pm-toolbar .leaflet-buttons-control-button {
    @apply !bg-white dark:!bg-slate-900 !border-b !border-slate-100 dark:!border-white/5 !w-10 !h-10 !flex !items-center !justify-center hover:!bg-emerald-50 dark:hover:!bg-emerald-500/10 !transition-colors;
}

.leaflet-pm-toolbar .leaflet-buttons-control-button .control-icon {
    @apply !scale-90 opacity-70 group-hover:opacity-100;
}
</style>
