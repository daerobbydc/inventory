<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { 
    Plus, 
    Search, 
    FileText, 
    CheckCircle2, 
    Clock, 
    AlertTriangle, 
    Building2, 
    Eye, 
    ChevronRight,
    TrendingUp,
    DollarSign,
    Filter,
    Layers,
    PackageCheck,
    Send
} from 'lucide-vue-next';

const props = defineProps({
    procurements: Object,
    metrics: Object,
    filters: Object,
    faculties: Array,
    statuses: Array,
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const urgency = ref(props.filters.urgency || '');
const facultyId = ref(props.filters.faculty_id || '');

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const formatShortRupiah = (val) => {
    const num = Number(val || 0);
    if (num >= 1000000000) {
        return 'Rp ' + (num / 1000000000).toFixed(1).replace('.0', '') + ' M';
    }
    if (num >= 1000000) {
        return 'Rp ' + (num / 1000000).toFixed(1).replace('.0', '') + ' Jt';
    }
    return formatRupiah(num);
};

const getStatusBadge = (st) => {
    const val = typeof st === 'object' ? st?.value : st;
    const match = props.statuses.find(s => s.value === val);
    return {
        label: match ? match.label : val,
        badgeClass: match ? match.badgeClass : 'bg-slate-100 text-slate-700 border-slate-200'
    };
};

const getUrgencyBadge = (urgency) => {
    switch (urgency) {
        case 'critical':
            return { label: 'Kritis / Akreditasi', class: 'bg-rose-100 text-rose-800 border-rose-200 font-bold' };
        case 'urgent':
            return { label: 'Mendesak', class: 'bg-amber-100 text-amber-800 border-amber-200 font-semibold' };
        default:
            return { label: 'Reguler', class: 'bg-slate-100 text-slate-700 border-slate-200' };
    }
};

let searchTimeout = null;
watch([search, status, urgency, facultyId], () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('procurements.index'), {
            search: search.value || undefined,
            status: status.value || undefined,
            urgency: urgency.value || undefined,
            faculty_id: facultyId.value || undefined,
        }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});
</script>

<template>
    <Head title="Usulan Pengadaan Aset - SIM-SARPRAS" />

    <AuthenticatedLayout>
        <div class="space-y-8">
            <!-- Header Section with Material Style Action -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                        Usulan Pengadaan Aset Kampus
                    </h1>
                    <p class="text-xs text-slate-500 mt-1">
                        Sistem perencanaan, verifikasi anggaran, dan penerimaan aset baru antar-fakultas Universitas Al-Khairiyah.
                    </p>
                </div>

                <div v-if="$page.props.auth?.user?.can_create_procurement" class="shrink-0">
                    <Link
                        :href="route('procurements.create')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-tr from-[#1a73e8] to-[#4285f4] hover:opacity-95 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/25 transition"
                    >
                        <Plus class="w-4 h-4 stroke-[2.5]" />
                        <span>Ajukan Usulan Baru</span>
                    </Link>
                </div>
            </div>

            <!-- 4 Overhanging Metric Cards (Material Blue Edition) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 pt-2">
                <!-- Metric 1: Total Usulan (Dark Charcoal) -->
                <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                    <div class="absolute -top-4 left-4 w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#1e293b] to-[#0f172a] shadow-lg shadow-slate-900/30 flex items-center justify-center text-white">
                        <FileText class="w-6 h-6 stroke-[2]" />
                    </div>
                    <div class="pt-4 text-right">
                        <span class="text-xs font-semibold text-slate-500">Total Pengajuan</span>
                        <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                            {{ metrics.totalRequests || 0 }} Proposal
                        </div>
                    </div>
                    <hr class="border-t border-slate-100 my-3" />
                    <div class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="text-blue-600 font-bold">Tercatat</span>
                        <span>Usulan semester berjalan</span>
                    </div>
                </div>

                <!-- Metric 2: Menunggu Telaah (Vibrant Blue) -->
                <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                    <div class="absolute -top-4 left-4 w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#1a73e8] to-[#4285f4] shadow-lg shadow-blue-500/30 flex items-center justify-center text-white">
                        <Clock class="w-6 h-6 stroke-[2]" />
                    </div>
                    <div class="pt-4 text-right">
                        <span class="text-xs font-semibold text-slate-500">Menunggu Review</span>
                        <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                            {{ metrics.pendingReview || 0 }} Berkas
                        </div>
                    </div>
                    <hr class="border-t border-slate-100 my-3" />
                    <div class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="text-amber-600 font-bold">Verifikasi</span>
                        <span>Telaah teknis Biro Sarpras</span>
                    </div>
                </div>

                <!-- Metric 3: Usulan Disetujui (Emerald Green) -->
                <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                    <div class="absolute -top-4 left-4 w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#10b981] to-[#059669] shadow-lg shadow-emerald-500/30 flex items-center justify-center text-white">
                        <CheckCircle2 class="w-6 h-6 stroke-[2]" />
                    </div>
                    <div class="pt-4 text-right">
                        <span class="text-xs font-semibold text-slate-500">Disetujui / PO</span>
                        <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                            {{ metrics.approvedCount || 0 }} Berkas
                        </div>
                    </div>
                    <hr class="border-t border-slate-100 my-3" />
                    <div class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="text-emerald-500 font-bold">Siap Beli</span>
                        <span>Dalam alokasi pengadaan</span>
                    </div>
                </div>

                <!-- Metric 4: Total Alokasi Anggaran (Rose/Pink Gradient) -->
                <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                    <div class="absolute -top-4 left-4 w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#ec4899] to-[#e11d48] shadow-lg shadow-pink-500/30 flex items-center justify-center text-white">
                        <TrendingUp class="w-6 h-6 stroke-[2]" />
                    </div>
                    <div class="pt-4 text-right">
                        <span class="text-xs font-semibold text-slate-500">Total Anggaran Disetujui</span>
                        <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                            {{ formatShortRupiah(metrics.totalApprovedBudget) }}
                        </div>
                    </div>
                    <hr class="border-t border-slate-100 my-3" />
                    <div class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="text-rose-600 font-bold">CapEx</span>
                        <span>RAB belanja modal sarana</span>
                    </div>
                </div>
            </div>

            <!-- Filters Bar & Search -->
            <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                <div class="relative flex-1 max-w-md">
                    <Search class="w-4 h-4 text-slate-400 absolute left-3 top-3" />
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Cari judul proposal, no. usulan, atau tahun..."
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 focus:border-[#1a73e8] transition"
                    />
                </div>

                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Faculty Filter (Super Admin) -->
                    <select
                        v-if="$page.props.auth?.user?.can_manage_settings && faculties.length"
                        v-model="facultyId"
                        class="py-2 pl-3 pr-8 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                    >
                        <option value="">Semua Fakultas</option>
                        <option v-for="fac in faculties" :key="fac.id" :value="fac.id">
                            {{ fac.name }} ({{ fac.code }})
                        </option>
                    </select>

                    <!-- Status Filter -->
                    <select
                        v-model="status"
                        class="py-2 pl-3 pr-8 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                    >
                        <option value="">Semua Status</option>
                        <option v-for="st in statuses" :key="st.value" :value="st.value">
                            {{ st.label }}
                        </option>
                    </select>

                    <!-- Urgency Filter -->
                    <select
                        v-model="urgency"
                        class="py-2 pl-3 pr-8 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                    >
                        <option value="">Semua Tingkat Urgensi</option>
                        <option value="reguler">Reguler</option>
                        <option value="urgent">Mendesak</option>
                        <option value="critical">Kritis / Akreditasi</option>
                    </select>
                </div>
            </div>

            <!-- Material Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                <th class="px-6 py-4">NO. USULAN & JUDUL</th>
                                <th class="px-5 py-4">FAKULTAS / PEMOHON</th>
                                <th class="px-5 py-4">URGENSI & TAHUN</th>
                                <th class="px-5 py-4">ESTIMASI BIAYA</th>
                                <th class="px-5 py-4">STATUS</th>
                                <th class="px-6 py-4 text-right">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="item in procurements.data"
                                :key="item.id"
                                class="hover:bg-slate-50/70 transition"
                            >
                                <!-- No. Usulan & Title -->
                                <td class="px-6 py-4">
                                    <div class="font-mono text-[11px] font-extrabold text-[#1a73e8]">
                                        {{ item.request_number }}
                                    </div>
                                    <div class="font-bold text-slate-900 mt-0.5 text-xs line-clamp-1 max-w-[280px]">
                                        {{ item.title }}
                                    </div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ item.items?.length || 0 }} Rincian Barang Usulan
                                    </div>
                                </td>

                                <!-- Faculty & Requester -->
                                <td class="px-5 py-4">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-800 border border-blue-200">
                                        <Building2 class="w-3 h-3" />
                                        <span>{{ item.faculty?.name || 'Rektorat / Universitas' }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-600 font-medium mt-1">
                                        Diajukan oleh: <span class="font-bold text-slate-800">{{ item.user?.name }}</span>
                                    </div>
                                </td>

                                <!-- Urgency & Fiscal Year -->
                                <td class="px-5 py-4">
                                    <div>
                                        <span :class="['inline-block px-2 py-0.5 rounded-full text-[10px] border', getUrgencyBadge(item.urgency).class]">
                                            {{ getUrgencyBadge(item.urgency).label }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-1">
                                        TA {{ item.fiscal_year }} • {{ item.budget_source }}
                                    </div>
                                </td>

                                <!-- Estimated Cost -->
                                <td class="px-5 py-4 font-bold text-slate-900 text-xs">
                                    {{ formatRupiah(item.total_estimated_cost) }}
                                </td>

                                <!-- Status Badge -->
                                <td class="px-5 py-4">
                                    <span :class="['inline-block px-2.5 py-1 rounded-full text-[10px] border', getStatusBadge(item.status).badgeClass]">
                                        {{ getStatusBadge(item.status).label }}
                                    </span>
                                </td>

                                <!-- Action Buttons -->
                                <td class="px-6 py-4 text-right">
                                    <Link
                                        :href="route('procurements.show', item.id)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-[#1a73e8] hover:text-white text-slate-700 rounded-xl text-xs font-bold transition"
                                    >
                                        <Eye class="w-3.5 h-3.5" />
                                        <span>Detail Usulan</span>
                                    </Link>
                                </td>
                            </tr>

                            <tr v-if="!procurements.data?.length">
                                <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-2">
                                        <FileText class="w-6 h-6" />
                                    </div>
                                    <p class="text-xs font-bold text-slate-600">Belum ada usulan pengadaan aset</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Silakan ajukan kebutuhan sarana prasarana baru untuk fakultas Anda.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div v-if="procurements.links && procurements.links.length > 3" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <div>
                        Menampilkan <strong class="text-slate-800">{{ procurements.from || 0 }}</strong> - <strong class="text-slate-800">{{ procurements.to || 0 }}</strong> dari <strong class="text-slate-800">{{ procurements.total }}</strong> usulan
                    </div>
                    <div class="flex items-center gap-1">
                        <Component
                            :is="link.url ? Link : 'span'"
                            v-for="link in procurements.links"
                            :key="link.label"
                            :href="link.url"
                            v-html="link.label"
                            :class="[
                                'px-3 py-1.5 rounded-lg text-xs font-medium transition',
                                link.active ? 'bg-[#1a73e8] text-white font-bold' : link.url ? 'hover:bg-slate-100 text-slate-700' : 'text-slate-300 pointer-events-none'
                            ]"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
