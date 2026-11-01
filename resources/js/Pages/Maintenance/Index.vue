<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    Wrench, 
    Plus, 
    Search, 
    AlertTriangle, 
    CheckCircle2, 
    Clock, 
    UserCheck, 
    ChevronRight,
    ArrowRight,
    Building2,
    Calendar,
    Filter,
    X,
    Eye,
    ShieldAlert
} from 'lucide-vue-next';

const props = defineProps({
    tickets: Object,
    metrics: Object,
    filters: Object,
    technicians: Array,
    statuses: Array,
    priorities: Array,
});

const search = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || 'all');
const selectedPriority = ref(props.filters.priority || 'all');

const applyFilter = () => {
    router.get(route('maintenance.index'), {
        search: search.value || undefined,
        status: selectedStatus.value !== 'all' ? selectedStatus.value : undefined,
        priority: selectedPriority.value !== 'all' ? selectedPriority.value : undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilter = () => {
    search.value = '';
    selectedStatus.value = 'all';
    selectedPriority.value = 'all';
    applyFilter();
};

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

// Dispatch Modal State
const showDispatchModal = ref(false);
const dispatchTicket = ref(null);
const dispatchForm = useForm({
    technician_id: '',
    status: 'in_progress',
    resolution_notes: '',
});

const openDispatch = (ticket) => {
    dispatchTicket.value = ticket;
    dispatchForm.technician_id = ticket.technician_id || '';
    dispatchForm.status = ticket.status?.value || ticket.status || 'in_progress';
    dispatchForm.resolution_notes = ticket.resolution_notes || '';
    showDispatchModal.value = true;
};

const submitDispatch = () => {
    if (!dispatchTicket.value) return;
    dispatchForm.put(route('maintenance.update', dispatchTicket.value.id), {
        onSuccess: () => {
            showDispatchModal.value = false;
            dispatchTicket.value = null;
        },
    });
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

const getStatusBadgeClass = (status) => {
    const val = typeof status === 'object' ? status?.value : status;
    switch (val) {
        case 'reported':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'in_progress':
            return 'bg-teal-50 text-teal-700 border-teal-200';
        case 'waiting_parts':
            return 'bg-orange-50 text-orange-700 border-orange-200';
        case 'completed':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        default:
            return 'bg-slate-50 text-slate-600 border-slate-200';
    }
};
</script>

<template>
    <Head title="Pemeliharaan & Servis - SIM-SARPRAS" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Section -->
            <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/70 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                            Unit Perawatan Sarpras
                        </span>
                        <span>•</span>
                        <span>Dispatch Teknisi Siaga</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pemeliharaan & Servis Sarpras</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Pusat pemantauan tiket kerusakan, penugasan teknisi on-duty, dan rekam perbaikan fasilitas kampus.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <Link
                        :href="route('maintenance.create')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#168a42] hover:bg-[#137a3b] text-white rounded-xl text-xs font-bold shadow-sm shadow-emerald-700/20 transition"
                    >
                        <Plus class="w-4 h-4 stroke-[2.5]" />
                        <span>+ Lapor Kerusakan Baru</span>
                    </Link>
                </div>
            </div>

            <!-- 4 Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1: Dilaporkan / Pending -->
                <div class="bg-[#fffbeb]/90 border border-amber-100 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                            <Clock class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">Perlu Tindakan</span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 mt-3">TIKET DILAPORKAN</div>
                    <div class="text-2xl font-black text-slate-900 mt-0.5">{{ metrics.reported || 0 }} Tiket</div>
                    <div class="text-[11px] text-slate-500 mt-1">Menunggu penugasan teknisi</div>
                </div>

                <!-- Card 2: Sedang Dikerjakan -->
                <div class="bg-[#f0fdfa]/90 border border-teal-100 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-teal-100 text-teal-700 flex items-center justify-center">
                            <Wrench class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-teal-100 text-teal-800">Sedang Proses</span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-teal-700 mt-3">SEDANG DIKERJAKAN</div>
                    <div class="text-2xl font-black text-slate-900 mt-0.5">{{ metrics.in_progress || 0 }} Tiket</div>
                    <div class="text-[11px] text-slate-500 mt-1">Teknisi aktif di lapangan</div>
                </div>

                <!-- Card 3: Menunggu Suku Cadang -->
                <div class="bg-[#fff7ed]/90 border border-orange-100 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center">
                            <AlertTriangle class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-orange-100 text-orange-800">PO Sparepart</span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-orange-700 mt-3">MENUNGGU SPAREPART</div>
                    <div class="text-2xl font-black text-slate-900 mt-0.5">{{ metrics.waiting_parts || 0 }} Tiket</div>
                    <div class="text-[11px] text-slate-500 mt-1">Proses pengadaan suku cadang</div>
                </div>

                <!-- Card 4: Selesai Diperbaiki -->
                <div class="bg-[#f0fdf4]/90 border border-emerald-100 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#168a42] flex items-center justify-center">
                            <CheckCircle2 class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-[#168a42]">Selesai</span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 mt-3">SELESAI DIPERBAIKI</div>
                    <div class="text-2xl font-black text-slate-900 mt-0.5">{{ metrics.completed || 0 }} Tiket</div>
                    <div class="text-[11px] text-slate-500 mt-1">Fasilitas laik beroperasi kembali</div>
                </div>
            </div>

            <!-- Filters & Search Bar -->
            <div class="bg-white rounded-2xl border border-slate-200/70 p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <!-- Status Tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0">
                    <button
                        @click="selectedStatus = 'all'; applyFilter()"
                        :class="[
                            'px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap',
                            selectedStatus === 'all'
                                ? 'bg-[#168a42] text-white shadow-xs'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        ]"
                    >
                        Semua ({{ metrics.total || 0 }})
                    </button>
                    <button
                        @click="selectedStatus = 'reported'; applyFilter()"
                        :class="[
                            'px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap',
                            selectedStatus === 'reported'
                                ? 'bg-[#168a42] text-white shadow-xs'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        ]"
                    >
                        Dilaporkan ({{ metrics.reported || 0 }})
                    </button>
                    <button
                        @click="selectedStatus = 'in_progress'; applyFilter()"
                        :class="[
                            'px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap',
                            selectedStatus === 'in_progress'
                                ? 'bg-[#168a42] text-white shadow-xs'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        ]"
                    >
                        Sedang Dikerjakan ({{ metrics.in_progress || 0 }})
                    </button>
                    <button
                        @click="selectedStatus = 'waiting_parts'; applyFilter()"
                        :class="[
                            'px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap',
                            selectedStatus === 'waiting_parts'
                                ? 'bg-[#168a42] text-white shadow-xs'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        ]"
                    >
                        Menunggu Part ({{ metrics.waiting_parts || 0 }})
                    </button>
                    <button
                        @click="selectedStatus = 'completed'; applyFilter()"
                        :class="[
                            'px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap',
                            selectedStatus === 'completed'
                                ? 'bg-[#168a42] text-white shadow-xs'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        ]"
                    >
                        Selesai ({{ metrics.completed || 0 }})
                    </button>
                </div>

                <!-- Search and Priority Filter -->
                <div class="flex items-center gap-3">
                    <select
                        v-model="selectedPriority"
                        @change="applyFilter"
                        class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42]"
                    >
                        <option value="all">Semua Prioritas</option>
                        <option value="critical">Kritis / Mendesak</option>
                        <option value="high">Tinggi</option>
                        <option value="medium">Sedang</option>
                        <option value="low">Rendah</option>
                    </select>

                    <div class="relative w-full sm:w-64">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
                        <input
                            v-model="search"
                            @keyup.enter="applyFilter"
                            type="text"
                            placeholder="Cari tiket, aset, pelapor..."
                            class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42]"
                        />
                    </div>

                    <button
                        v-if="search || selectedStatus !== 'all' || selectedPriority !== 'all'"
                        @click="resetFilter"
                        class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition"
                        title="Reset Filter"
                    >
                        <X class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <!-- Tickets Table Card -->
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">Kode Tiket</th>
                                <th class="px-4 py-3.5">Aset & Lokasi</th>
                                <th class="px-4 py-3.5">Keluhan / Masalah</th>
                                <th class="px-4 py-3.5">Prioritas</th>
                                <th class="px-4 py-3.5">Teknisi / PIC</th>
                                <th class="px-4 py-3.5">Status</th>
                                <th class="px-4 py-3.5 text-right">Biaya Riil</th>
                                <th class="px-6 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-slate-50/70 transition">
                                <!-- Kode Tiket -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-mono font-bold text-slate-900">{{ ticket.ticket_code }}</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">
                                        {{ new Date(ticket.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                                    </div>
                                </td>

                                <!-- Aset & Lokasi -->
                                <td class="px-4 py-4">
                                    <div class="font-bold text-slate-900">{{ ticket.asset?.name || 'Fasilitas Umum' }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        <span class="font-mono text-emerald-700 font-semibold">{{ ticket.asset?.asset_code }}</span>
                                        <span v-if="ticket.asset?.room"> • {{ ticket.asset?.room?.name }} ({{ ticket.asset?.room?.building?.name }})</span>
                                    </div>
                                </td>

                                <!-- Keluhan / Masalah -->
                                <td class="px-4 py-4 max-w-xs">
                                    <div class="font-medium text-slate-800 line-clamp-2 leading-relaxed">
                                        {{ ticket.issue_description }}
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-1">
                                        Pelapor: {{ ticket.reporter?.name || 'Sistem' }}
                                    </div>
                                </td>

                                <!-- Prioritas -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span 
                                        :class="[
                                            'px-2.5 py-1 rounded-full text-[10px] font-extrabold border',
                                            getPriorityBadgeClass(ticket.priority)
                                        ]"
                                    >
                                        {{ ticket.priority?.label || ticket.priority }}
                                    </span>
                                </td>

                                <!-- Teknisi -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div v-if="ticket.technician" class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-emerald-100 text-[#168a42] flex items-center justify-center font-bold text-[10px]">
                                            {{ ticket.technician.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800">{{ ticket.technician.name }}</div>
                                            <div class="text-[10px] text-slate-400">{{ ticket.technician.department || 'Teknisi Sarpras' }}</div>
                                        </div>
                                    </div>
                                    <div v-else class="text-amber-600 font-medium text-[11px] italic">
                                        Belum Ditugaskan
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span 
                                        :class="[
                                            'px-2.5 py-1 rounded-full text-[10px] font-bold border',
                                            getStatusBadgeClass(ticket.status)
                                        ]"
                                    >
                                        {{ ticket.status?.label || ticket.status }}
                                    </span>
                                </td>

                                <!-- Biaya -->
                                <td class="px-4 py-4 whitespace-nowrap text-right font-medium text-slate-700">
                                    {{ ticket.cost ? formatRupiah(ticket.cost) : '-' }}
                                </td>

                                <!-- Aksi -->
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-2">
                                        <!-- Quick Dispatch Button -->
                                        <button
                                            v-if="ticket.status?.value !== 'completed'"
                                            @click="openDispatch(ticket)"
                                            class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#168a42] border border-emerald-200 text-[11px] font-bold transition flex items-center gap-1"
                                            title="Update & Dispatch"
                                        >
                                            <UserCheck class="w-3.5 h-3.5" />
                                            <span>Dispatch</span>
                                        </button>

                                        <!-- Detail Link -->
                                        <Link
                                            :href="route('maintenance.show', ticket.id)"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100 transition"
                                            title="Lihat Detail Tiket"
                                        >
                                            <Eye class="w-4 h-4" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="tickets.data.length === 0">
                                <td colspan="8" class="px-6 py-12 text-center text-slate-400">
                                    <Wrench class="w-10 h-10 mx-auto text-slate-300 mb-2 stroke-[1.5]" />
                                    <p class="text-sm font-semibold text-slate-600">Tidak ada tiket pemeliharaan yang cocok</p>
                                    <p class="text-xs text-slate-400 mt-1">Coba sesuaikan filter status atau kata kunci pencarian Anda.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="tickets.links && tickets.links.length > 3" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                    <div class="text-xs text-slate-500">
                        Menampilkan <span class="font-bold text-slate-700">{{ tickets.from || 0 }}</span> sampai <span class="font-bold text-slate-700">{{ tickets.to || 0 }}</span> dari <span class="font-bold text-slate-700">{{ tickets.total || 0 }}</span> tiket
                    </div>
                    <div class="flex items-center gap-1">
                        <Link
                            v-for="(link, i) in tickets.links"
                            :key="i"
                            :href="link.url || '#'"
                            :class="[
                                'px-3 py-1.5 rounded-lg text-xs font-semibold transition',
                                link.active
                                    ? 'bg-[#168a42] text-white shadow-xs'
                                    : link.url
                                        ? 'text-slate-600 hover:bg-slate-100'
                                        : 'text-slate-300 pointer-events-none'
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>

        <!-- Dispatch Modal -->
        <div v-if="showDispatchModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-slate-900 font-bold text-sm">
                        <Wrench class="w-4 h-4 text-[#168a42]" />
                        <span>Dispatch Teknisi & Update Progres Tiket</span>
                    </div>
                    <button @click="showDispatchModal = false" class="text-slate-400 hover:text-slate-600">
                        <X class="w-4 h-4" />
                    </button>
                </div>

                <form @submit.prevent="submitDispatch" class="p-6 space-y-4">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs">
                        <div class="font-mono font-bold text-[#168a42]">{{ dispatchTicket?.ticket_code }}</div>
                        <div class="font-semibold text-slate-800 mt-1">{{ dispatchTicket?.asset?.name }}</div>
                        <div class="text-slate-500 mt-0.5 line-clamp-2">{{ dispatchTicket?.issue_description }}</div>
                    </div>

                    <!-- Assign Technician -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Teknisi Penanggung Jawab</label>
                        <select
                            v-model="dispatchForm.technician_id"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42]"
                            required
                        >
                            <option value="" disabled>-- Pilih Teknisi On-Duty --</option>
                            <option v-for="tech in technicians" :key="tech.id" :value="tech.id">
                                {{ tech.name }} ({{ tech.department || 'Unit Sarpras' }})
                            </option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Penanganan</label>
                        <select
                            v-model="dispatchForm.status"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42]"
                            required
                        >
                            <option value="in_progress">Sedang Dikerjakan</option>
                            <option value="waiting_parts">Menunggu Suku Cadang / PO Sparepart</option>
                            <option value="completed">Selesai Diperbaiki (Aset Siap Kembali)</option>
                        </select>
                    </div>

                    <!-- Resolution Notes -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tindakan / Instruksi Kerja</label>
                        <textarea
                            v-model="dispatchForm.resolution_notes"
                            rows="3"
                            placeholder="Tuliskan instruksi penanganan teknisi atau catatan suku cadang..."
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42]"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button
                            type="button"
                            @click="showDispatchModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="dispatchForm.processing"
                            class="px-4 py-2 bg-[#168a42] hover:bg-[#137a3b] text-white rounded-xl text-xs font-bold shadow-sm transition disabled:opacity-50"
                        >
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
