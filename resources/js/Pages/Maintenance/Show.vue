<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { 
    Wrench, 
    ArrowLeft, 
    Building2, 
    Calendar, 
    Clock, 
    User, 
    Phone, 
    CheckCircle2, 
    AlertTriangle, 
    DollarSign,
    ShieldAlert,
    Layers,
    FileText,
    Check
} from 'lucide-vue-next';

const props = defineProps({
    ticket: Object,
    technicians: Array,
    statuses: Array,
    priorities: Array,
});

const form = useForm({
    status: props.ticket.status?.value || props.ticket.status,
    technician_id: props.ticket.technician_id || '',
    cost: props.ticket.cost || '',
    resolution_notes: props.ticket.resolution_notes || '',
});

const submitUpdate = () => {
    form.put(route('maintenance.update', props.ticket.id));
};

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const getPriorityBadgeClass = (priority) => {
    const val = typeof priority === 'object' ? priority?.value : priority;
    switch (val) {
        case 'critical':
            return 'bg-rose-100 text-rose-800 border-rose-200';
        case 'high':
            return 'bg-amber-100 text-amber-800 border-amber-200';
        case 'medium':
            return 'bg-blue-100 text-blue-800 border-blue-200';
        default:
            return 'bg-slate-100 text-slate-700 border-slate-200';
    }
};

const steps = [
    { key: 'reported', label: '1. Dilaporkan' },
    { key: 'in_progress', label: '2. Dalam Pengerjaan' },
    { key: 'waiting_parts', label: '3. Tunggu Sparepart' },
    { key: 'completed', label: '4. Selesai' },
];

const isStepActive = (stepKey) => {
    const current = props.ticket.status?.value || props.ticket.status;
    if (current === 'completed') return true;
    if (current === 'waiting_parts' && ['reported', 'in_progress', 'waiting_parts'].includes(stepKey)) return true;
    if (current === 'in_progress' && ['reported', 'in_progress'].includes(stepKey)) return true;
    if (current === 'reported' && stepKey === 'reported') return true;
    return false;
};
</script>

<template>
    <Head :title="`Tiket ${ticket.ticket_code} - SIM-SARPRAS`" />

    <AuthenticatedLayout>
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Header -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('maintenance.index')"
                        class="p-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition shadow-xs"
                    >
                        <ArrowLeft class="w-4 h-4" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                            <span>Kode Tiket Pemeliharaan</span>
                            <span>•</span>
                            <span class="text-slate-400">Dibuat {{ new Date(ticket.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
                        </div>
                        <h1 class="text-2xl font-black text-slate-900 font-mono tracking-tight">{{ ticket.ticket_code }}</h1>
                    </div>
                </div>

                <!-- Priority & Status Badges -->
                <div class="flex items-center gap-2.5">
                    <span 
                        :class="[
                            'px-3 py-1 rounded-full text-xs font-extrabold border',
                            getPriorityBadgeClass(ticket.priority)
                        ]"
                    >
                        Prioritas: {{ ticket.priority?.label || ticket.priority }}
                    </span>
                    <span 
                        class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200"
                    >
                        {{ ticket.status?.label || ticket.status }}
                    </span>
                </div>
            </div>

            <!-- Lifecycle Stepper -->
            <div class="bg-white rounded-2xl border border-slate-200/70 p-5 shadow-sm">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <div 
                        v-for="step in steps" 
                        :key="step.key"
                        :class="[
                            'p-3 rounded-xl border flex items-center gap-2.5 text-xs font-bold transition',
                            isStepActive(step.key)
                                ? 'bg-emerald-50/80 border-emerald-300 text-emerald-800'
                                : 'bg-slate-50 border-slate-100 text-slate-400'
                        ]"
                    >
                        <div 
                            :class="[
                                'w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-black',
                                isStepActive(step.key) ? 'bg-[#1e3a8a] text-white' : 'bg-slate-200 text-slate-500'
                            ]"
                        >
                            <Check v-if="isStepActive(step.key)" class="w-3 h-3 stroke-[3]" />
                            <span v-else>•</span>
                        </div>
                        <span>{{ step.label }}</span>
                    </div>
                </div>
            </div>

            <!-- Main Content 2 Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Cols: Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Asset Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm">
                        <div class="flex items-center gap-2.5 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                            <Layers class="w-4 h-4 text-[#1e3a8a]" />
                            <span>Informasi Fasilitas & Aset</span>
                        </div>
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900">{{ ticket.asset?.name }}</h3>
                                <div class="text-xs text-slate-500 mt-1 flex flex-wrap items-center gap-3">
                                    <span class="font-mono text-emerald-700 font-bold">{{ ticket.asset?.asset_code }}</span>
                                    <span>•</span>
                                    <span>Kategori: {{ ticket.asset?.category?.name || '-' }}</span>
                                    <span>•</span>
                                    <span>Kondisi Awal: {{ ticket.asset?.condition?.label || ticket.asset?.condition }}</span>
                                </div>
                            </div>
                            <Link
                                v-if="ticket.asset"
                                :href="route('assets.show', ticket.asset.id)"
                                class="px-3.5 py-1.5 rounded-lg bg-white border border-slate-200 text-slate-700 text-xs font-bold hover:bg-slate-50 transition shrink-0"
                            >
                                Buku Induk Aset →
                            </Link>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-4 pt-4 border-t border-slate-100 text-xs">
                            <div>
                                <span class="text-slate-400 block">Kampus</span>
                                <span class="font-bold text-slate-800">{{ ticket.asset?.room?.building?.campus?.name || 'Kampus Utama' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Gedung</span>
                                <span class="font-bold text-slate-800">{{ ticket.asset?.room?.building?.name || '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block">Ruangan</span>
                                <span class="font-bold text-slate-800">{{ ticket.asset?.room?.name || 'Gudang Sarpras' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Keluhan & Deskripsi Masalah -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm">
                        <div class="flex items-center gap-2.5 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                            <AlertTriangle class="w-4 h-4 text-amber-500" />
                            <span>Laporan Kerusakan dari Pemohon</span>
                        </div>
                        <p class="text-sm text-slate-800 leading-relaxed bg-amber-50/50 p-4 rounded-xl border border-amber-100">
                            {{ ticket.issue_description }}
                        </p>
                    </div>

                    <!-- Tindakan & Catatan Selesai -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm">
                        <div class="flex items-center gap-2.5 text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">
                            <Wrench class="w-4 h-4 text-[#1e3a8a]" />
                            <span>Rekam Tindakan & Catatan Teknisi</span>
                        </div>
                        <p v-if="ticket.resolution_notes" class="text-sm text-slate-800 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100">
                            {{ ticket.resolution_notes }}
                        </p>
                        <div v-else class="text-xs text-slate-400 italic bg-slate-50 p-4 rounded-xl border border-slate-100">
                            Belum ada catatan teknis penanganan untuk tiket ini.
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-400">Total Biaya Perbaikan (Suku Cadang & Jasa):</span>
                            <span class="text-base font-black text-slate-900">{{ ticket.cost ? formatRupiah(ticket.cost) : 'Rp 0' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right 1 Col: Quick Action Form & Personnel Info -->
                <div class="space-y-6">
                    <!-- Update Status & Dispatch Form (Teknisi & Staff only) -->
                    <div v-if="$page.props.auth?.user?.can_update_maintenance" class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm">
                        <h3 class="text-sm font-extrabold text-slate-900 mb-4 flex items-center gap-2">
                            <Wrench class="w-4 h-4 text-[#1e3a8a]" />
                            <span>Update Progres Penanganan</span>
                        </h3>

                        <form @submit.prevent="submitUpdate" class="space-y-4">
                            <!-- Status -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Status Tiket</label>
                                <select
                                    v-model="form.status"
                                    class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                >
                                    <option v-for="s in statuses" :key="s.value" :value="s.value">
                                        {{ s.label }}
                                    </option>
                                </select>
                            </div>

                            <!-- Teknisi -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Teknisi Penanggung Jawab</label>
                                <select
                                    v-model="form.technician_id"
                                    class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                >
                                    <option value="">-- Pilih Teknisi --</option>
                                    <option v-for="t in technicians" :key="t.id" :value="t.id">
                                        {{ t.name }}
                                    </option>
                                </select>
                            </div>

                            <!-- Biaya Riil -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Biaya Perbaikan (Rp)</label>
                                <input
                                    v-model="form.cost"
                                    type="number"
                                    placeholder="0"
                                    class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                />
                            </div>

                            <!-- Tindakan -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tindakan / Suku Cadang</label>
                                <textarea
                                    v-model="form.resolution_notes"
                                    rows="3"
                                    placeholder="Tindakan yang telah dilakukan..."
                                    class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                ></textarea>
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full py-2.5 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-900/25 transition disabled:opacity-50"
                            >
                                Simpan Perubahan Tiket
                            </button>
                        </form>
                    </div>

                    <!-- Person In Charge Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm space-y-4 text-xs">
                        <div class="font-extrabold text-slate-900 border-b border-slate-100 pb-2">Personil Terkait</div>
                        
                        <!-- Pelapor -->
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-700 flex items-center justify-center font-bold">
                                {{ ticket.reporter?.name?.charAt(0) || 'U' }}
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400">Pelapor Kerusakan</span>
                                <div class="font-bold text-slate-800">{{ ticket.reporter?.name }}</div>
                                <div class="text-slate-400">{{ ticket.reporter?.department || 'Civitas Akademika' }}</div>
                            </div>
                        </div>

                        <!-- Teknisi -->
                        <div class="flex items-start gap-3 pt-3 border-t border-slate-100">
                            <div class="w-8 h-8 rounded-full bg-blue-50 text-[#1e3a8a] flex items-center justify-center font-bold">
                                {{ ticket.technician?.name?.charAt(0) || 'T' }}
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400">Teknisi Bertugas</span>
                                <div class="font-bold text-slate-800">{{ ticket.technician?.name || 'Belum Ditugaskan' }}</div>
                                <div class="text-slate-400">{{ ticket.technician?.phone || 'Unit Perawatan Kampus' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

