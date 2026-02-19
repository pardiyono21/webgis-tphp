<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { CheckCircle2, AlertCircle, X, Info } from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';

const visible = ref(false);
const message = ref('');
const type = ref<'success' | 'error' | 'info'>('success');
const page = usePage();

const showToast = (msg: string, t: 'success' | 'error' | 'info' = 'success') => {
    message.value = msg;
    type.value = t;
    visible.value = true;
    setTimeout(() => {
        visible.value = false;
    }, 5000);
};

// Listen to flash messages from Inertia
watch(() => page.props.flash, (flash: any) => {
    if (flash?.success) {
        showToast(flash.success, 'success');
    }
    if (flash?.error) {
        showToast(flash.error, 'error');
    }
}, { deep: true, immediate: true });

defineExpose({ showToast });
</script>

<template>
    <Transition
        enter-active-class="transform transition ease-out duration-300"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-4"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="visible" class="fixed top-6 right-6 z-99999 w-full max-w-sm pointer-events-auto overflow-hidden rounded-[24px] bg-white/80 dark:bg-slate-900/80 backdrop-blur-2xl border border-white dark:border-white/10 shadow-2xl">
            <div class="p-5">
                <div class="flex items-start gap-4">
                    <div class="shrink-0 pt-0.5">
                        <CheckCircle2 v-if="type === 'success'" class="h-6 w-6 text-emerald-500" />
                        <AlertCircle v-else-if="type === 'error'" class="h-6 w-6 text-rose-500" />
                        <Info v-else class="h-6 w-6 text-blue-500" />
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-tight">
                            {{ type === 'success' ? 'Berhasil' : type === 'error' ? 'Terjadi Kesalahan' : 'Informasi' }}
                        </p>
                        <p class="mt-1 text-xs font-medium text-slate-500 dark:text-slate-400 leading-relaxed">
                            {{ message }}
                        </p>
                    </div>
                    <div class="shrink-0 flex">
                        <button @click="visible = false" class="rounded-lg inline-flex text-slate-400 hover:text-slate-500 dark:hover:text-slate-300 transition-colors">
                            <X class="h-4 w-4" />
                        </button>
                    </div>
                </div>
            </div>
            <!-- Progress bar -->
            <div class="absolute bottom-0 left-0 h-1 bg-slate-100 dark:bg-slate-800 w-full overflow-hidden">
                <div 
                    class="h-full transition-all duration-5000 ease-linear"
                    :class="[
                        type === 'success' ? 'bg-emerald-500' : type === 'error' ? 'bg-rose-500' : 'bg-blue-500',
                        visible ? 'w-full' : 'w-0'
                    ]"
                ></div>
            </div>
        </div>
    </Transition>
</template>
