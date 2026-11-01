<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Layers, 
    Coins, 
    ArrowUpRight, 
    Repeat, 
    Wrench, 
    AlertTriangle, 
    PlusCircle,
    Eye,
    ChevronRight,
    MapPin,
    Tag
} from 'lucide-vue-next';

const props = defineProps({
    metrics: Object,
    lowStockItems: Array,
    recentAssets: Array,
    recentLoans: Array,
    recentTickets: Array,
    conditionCounts: Object,
    statusCounts: Object,
});

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const getConditionBadgeClass = (cond) => {
    switch (cond) {
        case 'good':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'light_damage':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'heavy_damage':
            return 'bg-rose-50 text-rose-700 border-rose-200';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-200';
    }
};

const getConditionLabel = (cond) => {
    switch (cond) {
        case 'good': return 'Baik';
        case 'light_damage': return 'Rusak Ringan';
        case 'heavy_damage': return 'Rusak Berat';
        default: return cond;
    }
};

const getStatusBadgeClass = (st) => {
    switch (st) {
        case 'available':
            return 'bg-blue-50 text-blue-700 border-blue-200';
        case 'borrowed':
            return 'bg-indigo-50 text-indigo-700 border-indigo-200';
        case 'maintenance':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'disposed':
            return 'bg-slate-50 text-slate-700 border-slate-200';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-200';
    }
};

const getStatusLabel = (st) => {
    switch (st) {
        case 'available': return 'Tersedia';
        case 'borrowed': return 'Dipinjam';
        case 'maintenance': return 'Dalam Servis';
        case 'disposed': return 'Dihapuskan';
        default: return st;
    }
};
</script>

<template>
    <Head title="Dashboard Sarpras" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Dashboard Ringkasan Inventaris</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Monitoring sarana, prasarana, peminjaman, dan status logistik kampus secara terpadu.</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('assets.create')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm shadow-indigo-200 transition"
                    >
                        <PlusCircle class="w-4 h-4" />
                        Tambah Aset Baru
                    </Link>
                </div>
            </div>
        </template>

        <!-- Low Stock Warning Banner -->
        <div v-if="lowStockItems?.length" class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
            <div class="flex items-start gap-3">
                <AlertTriangle class="h-5 w-5 text-amber-600 shrink-0 mt-0.5" />
                <div class="flex-1">
                    <h3 class="text-sm font-semibold text-amber-900">Peringatan: Stok Barang Habis Pakai Menipis</h3>
                    <div class="mt-1 text-xs text-amber-800 space-y-1">
                        <p>Terdapat <strong>{{ lowStockItems.length }} item</strong> yang telah mencapai atau di bawah batas minimum stok:</p>
                        <ul class="list-disc list-inside font-medium mt-1">
                            <li v-for="item in lowStockItems" :key="item.id">
                                {{ item.name }} — Sisa: <strong class="text-rose-600">{{ item.current_stock }} {{ item.unit }}</strong> (Min: {{ item.minimum_stock }})
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metric Stat Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- Total Valuasi -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Nilai Perolehan</span>
                    <div class="h-9 w-9 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <Coins class="w-5 h-5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-slate-900">{{ formatRupiah(metrics?.totalValuation) }}</div>
                    <div class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                        <span>Dari total seluruh aset kampus terdaftar</span>
                    </div>
                </div>
            </div>

            <!-- Total Aset -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Unit Aset</span>
                    <div class="h-9 w-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <Layers class="w-5 h-5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-slate-900">{{ metrics?.totalAssets || 0 }} <span class="text-sm font-normal text-slate-500">Unit</span></div>
                    <div class="text-xs text-slate-500 mt-1">
                        {{ statusCounts?.available || 0 }} Tersedia • {{ statusCounts?.borrowed || 0 }} Dipinjam
                    </div>
                </div>
            </div>

            <!-- Peminjaman Aktif -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Peminjaman Berlangsung</span>
                    <div class="h-9 w-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <Repeat class="w-5 h-5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-slate-900">{{ metrics?.activeLoans || 0 }} <span class="text-sm font-normal text-slate-500">Transaksi</span></div>
                    <div class="text-xs text-slate-500 mt-1">
                        Oleh Dosen, Mahasiswa, atau Tendik
                    </div>
                </div>
            </div>

            <!-- Tiket Servis -->
            <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm hover:shadow transition">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tiket Kerusakan / Servis</span>
                    <div class="h-9 w-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <Wrench class="w-5 h-5" />
                    </div>
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold text-slate-900">{{ metrics?.activeTickets || 0 }} <span class="text-sm font-normal text-slate-500">Tiket</span></div>
                    <div class="text-xs text-amber-600 font-medium mt-1">
                        Dalam proses pemeriksaan / perbaikan
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content 2-Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left: Recent Assets Table (2 cols) -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between">
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Aset Tetap Terbaru</h2>
                            <p class="text-xs text-slate-500">Aset yang baru didaftarkan ke dalam sistem</p>
                        </div>
                        <Link :href="route('assets.index')" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                            Lihat Semua
                            <ChevronRight class="w-4 h-4" />
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                                <tr>
                                    <th class="px-5 py-3">Kode & Nama Aset</th>
                                    <th class="px-5 py-3">Lokasi Ruangan</th>
                                    <th class="px-5 py-3">Kondisi</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="asset in recentAssets" :key="asset.id" class="hover:bg-slate-50/80 transition">
                                    <td class="px-5 py-3.5">
                                        <div class="font-semibold text-slate-900">{{ asset.name }}</div>
                                        <div class="text-xs text-slate-400 font-mono mt-0.5">{{ asset.asset_code }}</div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center gap-1.5 text-xs text-slate-700">
                                            <MapPin class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                                            <span>{{ asset.room?.name || '-' }}</span>
                                        </div>
                                        <div class="text-[11px] text-slate-400 pl-5">{{ asset.room?.building?.name }}</div>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span :class="['inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border', getConditionBadgeClass(asset.condition)]">
                                            {{ getConditionLabel(asset.condition) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span :class="['inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium border', getStatusBadgeClass(asset.status)]">
                                            {{ getStatusLabel(asset.status) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <Link :href="route('assets.show', asset.id)" class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                            <Eye class="w-3.5 h-3.5" />
                                            Detail
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="!recentAssets?.length">
                                    <td colspan="5" class="px-5 py-8 text-center text-sm text-slate-400">
                                        Belum ada aset tetap yang didaftarkan.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Loan Activity -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Aktivitas Peminjaman Terkini</h2>
                            <p class="text-xs text-slate-500">Monitoring pengembalian barang pinjaman</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div v-for="loan in recentLoans" :key="loan.id" class="p-3.5 rounded-lg border border-slate-100 bg-slate-50 flex items-center justify-between">
                            <div>
                                <div class="text-xs font-mono font-semibold text-indigo-600">{{ loan.transaction_code }}</div>
                                <div class="text-sm font-semibold text-slate-900 mt-0.5">{{ loan.borrower?.name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    {{ loan.loan_items?.length || 0 }} barang • Tenggat Kembali: <strong class="text-slate-700">{{ loan.expected_return_date }}</strong>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ loan.status }}
                            </span>
                        </div>
                        <div v-if="!recentLoans?.length" class="text-xs text-center py-4 text-slate-400">
                            Tidak ada data peminjaman saat ini.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Health Status & Maintenance (1 col) -->
            <div class="space-y-6">
                <!-- Condition Breakdown -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <h3 class="text-sm font-bold text-slate-800">Kondisi Fisik Aset</h3>
                    <p class="text-xs text-slate-500 mb-4">Persentase kelayakan sarana kampus</p>

                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-emerald-700">Kondisi Baik</span>
                                <span class="text-slate-700">{{ conditionCounts?.good || 0 }} Unit</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-emerald-500 h-2 rounded-full" :style="{ width: `${metrics?.totalAssets ? (conditionCounts?.good / metrics?.totalAssets * 100) : 0}%` }"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-amber-700">Rusak Ringan</span>
                                <span class="text-slate-700">{{ conditionCounts?.light_damage || 0 }} Unit</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-amber-500 h-2 rounded-full" :style="{ width: `${metrics?.totalAssets ? (conditionCounts?.light_damage / metrics?.totalAssets * 100) : 0}%` }"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-semibold mb-1">
                                <span class="text-rose-700">Rusak Berat / Afkir</span>
                                <span class="text-slate-700">{{ conditionCounts?.heavy_damage || 0 }} Unit</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-rose-500 h-2 rounded-full" :style="{ width: `${metrics?.totalAssets ? (conditionCounts?.heavy_damage / metrics?.totalAssets * 100) : 0}%` }"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Maintenance Tickets -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <h3 class="text-sm font-bold text-slate-800">Tiket Perbaikan Terbuka</h3>
                    <p class="text-xs text-slate-500 mb-4">Laporan kendala fisik alat yang sedang ditangani</p>

                    <div class="space-y-3">
                        <div v-for="ticket in recentTickets" :key="ticket.id" class="p-3 rounded-lg border border-slate-100 bg-slate-50">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-mono font-bold text-slate-600">{{ ticket.ticket_code }}</span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-800 uppercase">
                                    {{ ticket.priority }}
                                </span>
                            </div>
                            <div class="text-xs font-semibold text-slate-900 mt-1">{{ ticket.asset?.name }}</div>
                            <p class="text-xs text-slate-500 mt-1 line-clamp-2">{{ ticket.issue_description }}</p>
                            <div class="text-[11px] text-slate-400 mt-2 flex items-center justify-between">
                                <span>Pelapor: {{ ticket.reporter?.name }}</span>
                                <span class="font-medium text-indigo-600">{{ ticket.status }}</span>
                            </div>
                        </div>
                        <div v-if="!recentTickets?.length" class="text-xs text-center py-4 text-slate-400">
                            Tidak ada tiket perbaikan yang aktif.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
