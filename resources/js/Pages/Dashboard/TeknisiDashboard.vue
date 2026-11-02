<script setup>
import { Link } from '@inertiajs/vue3';
import { 
    Wrench, 
    AlertTriangle, 
    Clock, 
    CheckCircle2, 
    Package, 
    ChevronRight, 
    MapPin, 
    User, 
    Calendar, 
    ShieldAlert, 
    ArrowRight,
    Plus,
    ExternalLink,
    Layers
} from 'lucide-vue-next';

const props = defineProps({
    teknisiData: {
        type: Object,
        default: () => ({
            myAssignedTickets: [],
            urgentTickets: [],
            metrics: {
                assignedCount: 0,
                urgentCount: 0,
                waitingPartsCount: 0,
                completedThisMonth: 0,
            },
        }),
    },
});

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const getPriorityBadgeClass = (priority) => {
    const val = typeof priority === 'object' ? priority?.value : priority;
    switch (val) {
        case 'critical':
            return 'bg-rose-100 text-rose-800 border-rose-200 font-bold';
        case 'high':
            return 'bg-amber-100 text-amber-800 border-amber-200 font-semibold';
        case 'medium':
            return 'bg-blue-100 text-blue-800 border-blue-200';
        case 'low':
            return 'bg-slate-100 text-slate-700 border-slate-200';
        default:
            return 'bg-slate-100 text-slate-700 border-slate-200';
    }
};

const getStatusBadge = (st) => {
    const val = typeof st === 'object' ? st?.value : st;
    switch (val) {
        case 'reported':
            return { label: 'Menunggu Disposisi', class: 'bg-amber-50 text-amber-700 border-amber-200' };
        case 'assigned':
            return { label: 'Ditugaskan ke Anda', class: 'bg-blue-50 text-[#1e3a8a] border-blue-200' };
        case 'in_progress':
            return { label: 'Sedang Dikerjakan', class: 'bg-indigo-50 text-indigo-700 border-indigo-200' };
        case 'waiting_parts':
            return { label: 'Menunggu Sparepart', class: 'bg-purple-50 text-purple-700 border-purple-200' };
        case 'completed':
            return { label: 'Selesai', class: 'bg-emerald-50 text-emerald-700 border-emerald-200' };
        case 'cancelled':
            return { label: 'Dibatalkan', class: 'bg-slate-50 text-slate-600 border-slate-200' };
        default:
            return { label: val || '-', class: 'bg-slate-50 text-slate-700 border-slate-200' };
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- 1. Top Welcome Banner Card -->
        <div class="bg-gradient-to-r from-[#0a1628] via-[#0f244a] to-[#1e3a8a] text-white rounded-2xl p-6 sm:p-7 border border-blue-900/60 shadow-lg shadow-blue-950/15 relative overflow-hidden flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Decorative ambient glow -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="space-y-2 max-w-2xl relative z-10">
                <div class="flex items-center gap-2 text-xs font-semibold">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-500/20 text-cyan-200 border border-blue-400/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        Panel Operasional Lapangan & Servis
                    </span>
                    <span class="text-blue-200/60">•</span>
                    <span class="text-blue-200">Teknisi Bertugas: {{ $page.props.auth?.user?.name }}</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight">
                    Pusat Penanganan Tiket Pemeliharaan
                </h1>

                <p class="text-xs sm:text-sm text-blue-100/90 leading-relaxed">
                    Kelola disposisi servis fasilitas, perbaikan peralatan laboratorium, serta pembaruan status pengerjaan sarpras kampus Universitas Al-Khairiyah.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap sm:grid sm:grid-cols-2 lg:flex gap-2.5 shrink-0 relative z-10">
                <Link
                    :href="route('maintenance.index')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-blue-50 text-[#0c182c] rounded-xl text-xs font-extrabold shadow-md transition"
                >
                    <Wrench class="w-4 h-4 stroke-[2.5]" />
                    <span>Daftar Seluruh Tiket</span>
                </Link>

                <Link
                    :href="route('maintenance.create')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md transition"
                >
                    <Plus class="w-4 h-4" />
                    <span>Lapor Temuan Kerusakan</span>
                </Link>

                <Link
                    :href="route('consumables.index')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl text-xs font-bold backdrop-blur-sm shadow-sm transition"
                >
                    <Package class="w-4 h-4 text-cyan-200" />
                    <span>Katalog Suku Cadang</span>
                </Link>
            </div>
        </div>

        <!-- 2. Four Overhanging Metric Cards (Signature Material Dashboard 2 Style) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-2">
            <!-- Tugas Ditugaskan ke Saya (Blue / Cyan Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#0284c7] to-[#0369a1] shadow-lg shadow-blue-500/30 flex items-center justify-center text-white">
                    <Wrench class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-slate-500">Tugas Servis Saya</span>
                    <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                        {{ teknisiData.metrics?.assignedCount || 0 }} Tiket
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-slate-500 flex items-center justify-between">
                    <span>Perlu penanganan Anda</span>
                    <a href="#my-tasks" class="text-blue-600 font-bold hover:underline inline-flex items-center gap-0.5">
                        Antrean <ChevronRight class="w-3 h-3" />
                    </a>
                </div>
            </div>

            <!-- Tiket Urgent / Kritis (Hot Pink / Rose Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#ec4899] to-[#e11d48] shadow-lg shadow-pink-500/30 flex items-center justify-center text-white">
                    <AlertTriangle class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-rose-600">Prioritas Darurat</span>
                    <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                        {{ teknisiData.metrics?.urgentCount || 0 }} Tiket
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-slate-500 flex items-center justify-between">
                    <span>Fasilitas vital kampus</span>
                    <span class="text-rose-600 font-bold text-[11px]">Respon Cepat</span>
                </div>
            </div>

            <!-- Menunggu Suku Cadang (Dark Charcoal Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#1e293b] to-[#0f172a] shadow-lg shadow-slate-900/30 flex items-center justify-center text-white">
                    <Package class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-slate-500">Menunggu Sparepart</span>
                    <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                        {{ teknisiData.metrics?.waitingPartsCount || 0 }} Tiket
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-slate-500 flex items-center justify-between">
                    <span>Pengadaan logistik</span>
                    <Link :href="route('consumables.index')" class="text-slate-800 font-bold hover:underline inline-flex items-center gap-0.5">
                        Cek Stok <ChevronRight class="w-3 h-3" />
                    </Link>
                </div>
            </div>

            <!-- Selesai Bulan Ini (Emerald Green Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#10b981] to-[#059669] shadow-lg shadow-emerald-500/30 flex items-center justify-center text-white">
                    <CheckCircle2 class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-slate-500">Selesai Bulan Ini</span>
                    <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                        {{ teknisiData.metrics?.completedThisMonth || 0 }} Unit
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-slate-500 flex items-center justify-between">
                    <span class="text-emerald-600 font-bold">100% Tercatat</span>
                    <span class="text-slate-400 text-[11px]">Kinerja Baik</span>
                </div>
            </div>
        </div>

        <!-- 3. Main Content: 2-Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left (2 Cols): Antrean Tugas Servis Saya -->
            <div id="my-tasks" class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                    <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1e3a8a] flex items-center justify-center">
                                <Wrench class="w-4 h-4" />
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Daftar Tugas Servis Saya (My Work Orders)</h2>
                                <p class="text-xs text-slate-500">Tiket perbaikan yang ditugaskan secara personal kepada Anda</p>
                            </div>
                        </div>
                        <Link :href="route('maintenance.index')" class="text-xs font-semibold text-[#1e3a8a] hover:underline flex items-center gap-1">
                            Semua Tiket <ChevronRight class="w-3 h-3" />
                        </Link>
                    </div>

                    <div v-if="teknisiData.myAssignedTickets?.length > 0" class="divide-y divide-slate-100">
                        <div 
                            v-for="ticket in teknisiData.myAssignedTickets" 
                            :key="ticket.id" 
                            class="p-4 sm:p-5 hover:bg-slate-50/70 transition flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4"
                        >
                            <div class="space-y-2 flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-xs font-mono font-bold text-slate-900">{{ ticket.ticket_code }}</span>
                                    <span 
                                        :class="['px-2 py-0.5 rounded-full text-[10px] uppercase font-bold border', getPriorityBadgeClass(ticket.priority)]"
                                    >
                                        {{ ticket.priority?.value || ticket.priority }}
                                    </span>
                                    <span 
                                        :class="['px-2 py-0.5 rounded-full text-[10px] font-semibold border', getStatusBadge(ticket.status).class]"
                                    >
                                        {{ getStatusBadge(ticket.status).label }}
                                    </span>
                                </div>

                                <div>
                                    <h3 class="text-xs sm:text-sm font-bold text-slate-900">
                                        {{ ticket.asset?.name || 'Aset / Fasilitas Tidak Terdaftar' }}
                                    </h3>
                                    <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-0.5">
                                        <MapPin class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                                        <span>
                                            {{ ticket.asset?.room?.name || 'Lokasi Belum Ditentukan' }}
                                            <span v-if="ticket.asset?.room?.building" class="text-slate-400">
                                                • {{ ticket.asset.room.building.name }}
                                            </span>
                                        </span>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-600 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                    <strong>Keluhan:</strong> {{ ticket.issue_description }}
                                </p>

                                <div class="flex flex-wrap items-center gap-4 text-[11px] text-slate-400 pt-1">
                                    <span v-if="ticket.reporter" class="flex items-center gap-1">
                                        <User class="w-3 h-3 text-slate-400" />
                                        Pelapor: {{ ticket.reporter.name }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <Clock class="w-3 h-3 text-slate-400" />
                                        Masuk: {{ formatDate(ticket.reported_at || ticket.created_at) }}
                                    </span>
                                </div>
                            </div>

                            <div class="shrink-0 flex items-center sm:flex-col gap-2">
                                <Link
                                    :href="route('maintenance.show', ticket.id)"
                                    class="w-full inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#1e3a8a] text-white text-xs font-bold hover:bg-[#172554] shadow-sm transition"
                                >
                                    <span>Buka & Update</span>
                                    <ChevronRight class="w-3.5 h-3.5" />
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div v-else class="p-10 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                            <CheckCircle2 class="w-6 h-6" />
                        </div>
                        <h3 class="text-xs font-bold text-slate-800">Tidak Ada Tugas Tertunda</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            Hebat! Seluruh tiket servis yang ditugaskan ke Anda telah selesai dikerjakan atau belum ada penugasan baru.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right (1 Col): Tiket Urgent Kampus & Keselamatan Kerja -->
            <div class="space-y-6">
                <!-- Tiket Kritis Kampus -->
                <div class="bg-white rounded-2xl border border-rose-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 bg-rose-50/60 border-b border-rose-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <AlertTriangle class="w-4 h-4 text-rose-600" />
                            <h2 class="text-xs font-bold text-rose-900">Tiket Kritis / Darurat Kampus</h2>
                        </div>
                        <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-800 text-[10px] font-bold">
                            {{ teknisiData.urgentTickets?.length || 0 }} Perhatian
                        </span>
                    </div>

                    <div v-if="teknisiData.urgentTickets?.length > 0" class="divide-y divide-slate-100">
                        <div 
                            v-for="urgent in teknisiData.urgentTickets" 
                            :key="urgent.id" 
                            class="p-4 hover:bg-slate-50 transition space-y-2"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-mono font-bold text-slate-900">{{ urgent.ticket_code }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-rose-100 text-rose-800 border border-rose-200">
                                    {{ urgent.priority?.value || urgent.priority }}
                                </span>
                            </div>

                            <div class="text-xs font-bold text-slate-800">
                                {{ urgent.asset?.name || 'Fasilitas Kampus' }}
                            </div>

                            <p class="text-[11px] text-slate-500 line-clamp-2">
                                {{ urgent.issue_description }}
                            </p>

                            <div class="flex items-center justify-between pt-1 text-[10px] text-slate-400">
                                <span>{{ urgent.asset?.room?.name || 'Ruangan -' }}</span>
                                <Link :href="route('maintenance.show', urgent.id)" class="text-[#1e3a8a] font-semibold hover:underline flex items-center gap-0.5">
                                    Lihat <ChevronRight class="w-3 h-3" />
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div v-else class="p-6 text-center text-xs text-slate-400">
                        Tidak ada kerusakan kritis yang dilaporkan saat ini.
                    </div>
                </div>

                <!-- SOP Keselamatan Kerja & Prosedur Servis -->
                <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-blue-950 text-white rounded-2xl p-5 shadow-sm space-y-3.5">
                    <div class="flex items-center gap-2 text-amber-300">
                        <ShieldAlert class="w-4 h-4" />
                        <h3 class="text-xs font-bold uppercase tracking-wider">SOP Keselamatan Teknisi</h3>
                    </div>

                    <div class="space-y-2 text-xs text-slate-200/90 leading-relaxed">
                        <div class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-amber-400/20 text-amber-300 flex items-center justify-center shrink-0 text-[10px] font-bold mt-0.5">1</span>
                            <p><strong>Isolasi Daya:</strong> Selalu putuskan MCB / sumber listrik sebelum membuka casing perangkat elektronik.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-amber-400/20 text-amber-300 flex items-center justify-center shrink-0 text-[10px] font-bold mt-0.5">2</span>
                            <p><strong>Pencatatan Part:</strong> Catat setiap baut, kabel, dan suku cadang habis pakai yang diambil dari gudang logistik.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-amber-400/20 text-amber-300 flex items-center justify-center shrink-0 text-[10px] font-bold mt-0.5">3</span>
                            <p><strong>Verifikasi Pelapor:</strong> Setelah perbaikan selesai, lakukan uji fungsi bersama PIC ruangan sebelum menutup tiket.</p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-700 text-[11px] text-slate-400 flex items-center justify-between">
                        <span>Unit Pemeliharaan Fasilitas</span>
                        <span>Univ. Al-Khairiyah</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

