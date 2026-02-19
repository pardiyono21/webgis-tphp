<script setup lang="ts">
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { AlertTriangle, Trash2, X } from 'lucide-vue-next';

defineProps<{
    open: boolean;
    title: string;
    description: string;
    loading?: boolean;
}>();

const emit = defineEmits(['close', 'confirm']);
</script>

<template>
    <Dialog :open="open" @update:open="$emit('close')">
        <DialogContent class="sm:max-w-[425px] rounded-[32px] border-none p-0 overflow-hidden shadow-2xl">
            <div class="relative p-8 space-y-6 bg-white dark:bg-slate-900">
                <!-- Icon & Content -->
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="w-16 h-16 rounded-full bg-rose-50 dark:bg-rose-500/10 flex items-center justify-center relative">
                        <div class="absolute inset-0 rounded-full bg-rose-500/20 animate-ping opacity-25"></div>
                        <div class="relative w-12 h-12 rounded-full bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center border-4 border-white dark:border-slate-900 shadow-sm">
                            <AlertTriangle class="h-6 w-6 text-rose-600 dark:text-rose-400" />
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <DialogTitle class="text-xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">
                            {{ title }}
                        </DialogTitle>
                        <DialogDescription class="text-sm font-medium text-slate-500 dark:text-slate-400">
                            {{ description }}
                        </DialogDescription>
                    </div>
                </div>

                <!-- Footer / Actions -->
                <div class="flex flex-col sm:flex-row gap-3 pt-2">
                    <Button 
                        type="button" 
                        variant="ghost" 
                        @click="$emit('close')"
                        class="flex-1 rounded-2xl font-bold border-none bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 h-12"
                    >
                        Batal
                    </Button>
                    <Button 
                        type="button"
                        @click="$emit('confirm')"
                        :disabled="loading"
                        class="flex-1 rounded-2xl font-black bg-rose-600 hover:bg-rose-700 text-white shadow-lg shadow-rose-200 dark:shadow-rose-900/20 transition-all active:scale-95 h-12"
                    >
                        <Trash2 v-if="!loading" class="h-4 w-4 mr-2" />
                        <span v-else class="mr-2">...</span>
                        Hapus Sekarang
                    </Button>
                </div>
            </div>

            <!-- Bottom Note -->
            <div class="px-8 py-4 bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-white/5 text-center">
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">
                    Tindakan ini tidak dapat dibatalkan
                </p>
            </div>
        </DialogContent>
    </Dialog>
</template>
