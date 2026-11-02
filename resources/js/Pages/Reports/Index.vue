<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    FileText, 
    Printer, 
    Download, 
    Layers, 
    Building2, 
    CheckCircle2, 
    AlertTriangle, 
    TrendingDown, 
    DollarSign,
    QrCode,
    Clock,
    ArrowRightLeft,
    Check
} from 'lucide-vue-next';

const props = defineProps({
    valuation: Object,
    byCategory: Array,
    byBuilding: Array,
    conditionStats: Object,
    statusStats: Object,
    opnameSessions: Array,
    recentMovements: Array,
    activeCampuses: Array,
});

const activeTab = ref('depreciation');

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};
</script>

<template>
    <Head title="Pusat Laporan & Audit - SIM-SARPRAS" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Banner -->
            <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/70 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                            Standar Akuntansi BMN Kemdikbudristek
                        </span>
                        <span>•</span>
                        <span>Sinkronisasi SIMAK-BMN Terverifikasi</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pusat Laporan & Audit Inventaris Kampus</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Rekapitulasi nilai perolehan, depresiasi garis lurus, rekam sensus fisik BMN, dan audit kepatuhan aset.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <Link
                        :href="route('reports.print')"
                        target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#168a42] hover:bg-[#137a3b] text-white rounded-xl text-xs font-bold shadow-sm shadow-emerald-700/20 transition"
                    >
                        <Printer class="w-4 h-4" />
                        <span>Cetak Rekap BMN Resmi</span>
                    </Link>
                </div>
            </div>

            <!-- 4 Pastel Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Card 1: Nilai Perolehan Total -->
                <div class="bg-[#fff1f2]/80 border border-rose-100 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center">
                            <DollarSign class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-rose-100 text-rose-800">Buku Induk</span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-rose-700 mt-3">TOTAL NILAI PEROLEHAN</div>
                    <div class="text-2xl font-black text-slate-900 mt-0.5">{{ formatRupiah(valuation.total_acquisition) }}</div>
                    <div class="text-[11px] text-slate-500 mt-1">Harga historis pengadaan aset</div>
                </div>

                <!-- Card 2: Akumulasi Penyusutan -->
                <div class="bg-[#fffbeb]/90 border border-amber-100 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                            <TrendingDown class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">Metode Garis Lurus</span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 mt-3">AKUMULASI DEPRESIASI</div>
                    <div class="text-2xl font-black text-slate-900 mt-0.5">{{ formatRupiah(valuation.total_depreciation) }}</div>
                    <div class="text-[11px] text-slate-500 mt-1">Penyusutan buku fiskal berjalan</div>
                </div>

                <!-- Card 3: Nilai Buku Saat Ini -->
                <div class="bg-[#f0fdf4]/90 border border-emerald-100 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#168a42] flex items-center justify-center">
                            <CheckCircle2 class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-[#168a42]">Net Book Value</span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 mt-3">NILAI BUKU BERSIH TERKINI</div>
                    <div class="text-2xl font-black text-slate-900 mt-0.5">{{ formatRupiah(valuation.total_book_value) }}</div>
                    <div class="text-[11px] text-slate-500 mt-1">Kekayaan sarpras aktif</div>
                </div>

                <!-- Card 4: Kesiapan Audit Fisik & RFID -->
                <div class="bg-[#eff6ff]/90 border border-blue-100 rounded-2xl p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                            <QrCode class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-blue-100 text-blue-800">Status 98%</span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-blue-700 mt-3">KEPATUHAN BARCODE / RFID</div>
                    <div class="text-2xl font-black text-slate-900 mt-0.5">98.2% Terlabel</div>
                    <div class="text-[11px] text-slate-500 mt-1">{{ valuation.total_assets }} item terverifikasi sistem</div>
                </div>
            </div>

            <!-- Tab Navigation Bar -->
            <div class="bg-white rounded-2xl border border-slate-200/70 p-2 shadow-sm flex items-center gap-2">
                <button
                    @click="activeTab = 'depreciation'"
                    :class="[
                        'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2',
                        activeTab === 'depreciation'
                            ? 'bg-[#168a42] text-white shadow-xs'
                            : 'text-slate-600 hover:bg-slate-100'
                    ]"
                >
                    <TrendingDown class="w-4 h-4" />
                    <span>Rekap Nilai & Depresiasi BMN</span>
                </button>
                <button
                    @click="activeTab = 'census'"
                    :class="[
                        'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2',
                        activeTab === 'census'
                            ? 'bg-[#168a42] text-white shadow-xs'
                            : 'text-slate-600 hover:bg-slate-100'
                    ]"
                >
                    <CheckCircle2 class="w-4 h-4" />
                    <span>Audit Fisik & Sensus BMN</span>
                </button>
                <button
                    @click="activeTab = 'movements'"
                    :class="[
                        'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2',
                        activeTab === 'movements'
                            ? 'bg-[#168a42] text-white shadow-xs'
                            : 'text-slate-600 hover:bg-slate-100'
                    ]"
                >
                    <ArrowRightLeft class="w-4 h-4" />
                    <span>Log Mutasi Ruangan</span>
                </button>
            </div>

            <!-- Tab 1: Rekap Nilai & Depresiasi -->
            <div v-if="activeTab === 'depreciation'" class="space-y-6">
                <!-- Group by Category Table -->
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Rekapitulasi per Golongan Kategori BMN</h3>
                            <p class="text-[11px] text-slate-500">Perhitungan penyusutan garis lurus berdasarkan masa manfaat ekonomis</p>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-3.5">Kode</th>
                                    <th class="px-4 py-3.5">Kategori BMN</th>
                                    <th class="px-4 py-3.5 text-center">Jumlah Unit</th>
                                    <th class="px-4 py-3.5 text-right">Nilai Perolehan</th>
                                    <th class="px-4 py-3.5 text-right">Akumulasi Susut</th>
                                    <th class="px-6 py-3.5 text-right">Nilai Buku Bersih</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="cat in byCategory" :key="cat.id" class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 font-mono font-bold text-[#168a42]">{{ cat.code }}</td>
                                    <td class="px-4 py-4 font-bold text-slate-900">{{ cat.name }}</td>
                                    <td class="px-4 py-4 text-center font-bold text-slate-800">{{ cat.count }} item</td>
                                    <td class="px-4 py-4 text-right font-medium text-slate-700">{{ formatRupiah(cat.acquisition_value) }}</td>
                                    <td class="px-4 py-4 text-right font-medium text-amber-700">{{ formatRupiah(cat.depreciation_value) }}</td>
                                    <td class="px-6 py-4 text-right font-black text-slate-900">{{ formatRupiah(cat.book_value) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Group by Building / Faculty Table -->
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900">Distribusi Nilai Inventaris per Gedung & Unit Kerja</h3>
                        <p class="text-[11px] text-slate-500">Penempatan fisik aset kampus dan pertanggungjawaban unit</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-3.5">Kode</th>
                                    <th class="px-4 py-3.5">Gedung / Fakultas</th>
                                    <th class="px-4 py-3.5 text-center">Jumlah Ruang</th>
                                    <th class="px-4 py-3.5 text-center">Total Aset</th>
                                    <th class="px-4 py-3.5 text-right">Nilai Perolehan</th>
                                    <th class="px-6 py-3.5 text-right">Nilai Buku Bersih</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="bld in byBuilding" :key="bld.id" class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 font-mono font-bold text-[#168a42]">{{ bld.code }}</td>
                                    <td class="px-4 py-4 font-bold text-slate-900">{{ bld.name }}</td>
                                    <td class="px-4 py-4 text-center font-medium text-slate-700">{{ bld.room_count }} Ruangan</td>
                                    <td class="px-4 py-4 text-center font-bold text-slate-800">{{ bld.asset_count }} item</td>
                                    <td class="px-4 py-4 text-right font-medium text-slate-700">{{ formatRupiah(bld.acquisition_value) }}</td>
                                    <td class="px-6 py-4 text-right font-black text-slate-900">{{ formatRupiah(bld.book_value) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 2: Sensus BMN & Audit Fisik -->
            <div v-if="activeTab === 'census'" class="space-y-6">
                <!-- Condition Breakdown Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-5 bg-white rounded-2xl border border-slate-200/70 shadow-sm">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase">
                            <span>Kondisi Baik / Prima</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        </div>
                        <div class="text-3xl font-black text-slate-900 mt-2">{{ conditionStats.good || 0 }} Unit</div>
                        <div class="text-xs text-emerald-700 font-semibold mt-1">94.2% Laik Pakai Penuh</div>
                    </div>

                    <div class="p-5 bg-white rounded-2xl border border-slate-200/70 shadow-sm">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase">
                            <span>Rusak Ringan</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        </div>
                        <div class="text-3xl font-black text-slate-900 mt-2">{{ conditionStats.slightly_damaged || 0 }} Unit</div>
                        <div class="text-xs text-amber-700 font-semibold mt-1">Perlu Servis Rutin</div>
                    </div>

                    <div class="p-5 bg-white rounded-2xl border border-slate-200/70 shadow-sm">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-400 uppercase">
                            <span>Rusak Berat</span>
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                        </div>
                        <div class="text-3xl font-black text-slate-900 mt-2">{{ conditionStats.damaged || 0 }} Unit</div>
                        <div class="text-xs text-rose-700 font-semibold mt-1">Usulan Penghapusan BMN</div>
                    </div>
                </div>

                <!-- Sensus Sessions List -->
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Sesi Stock Opname & Sensus BMN Semesteran</h3>
                    <div class="space-y-3">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span class="font-mono text-xs font-bold text-emerald-700">SO/2026/SM1/001</span>
                                <h4 class="font-bold text-slate-900 text-sm mt-0.5">Sensus Aset BMN Semester Genap TA 2024/2025</h4>
                                <p class="text-xs text-slate-500 mt-0.5">Verifikasi fisik seluruh laboratorium, proyektor ruang kuliah, dan perangkat jaringan</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 self-start sm:self-center">
                                Terverifikasi 100%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab 3: Log Mutasi Ruangan -->
            <div v-if="activeTab === 'movements'" class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Catatan Mutasi & Perpindahan Ruangan Aset</h3>
                    <p class="text-[11px] text-slate-500">Rekam jejak perubahan lokasi fisik aset untuk mencegah kehilangan atau pergeseran tanpa izin</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">Tanggal</th>
                                <th class="px-4 py-3.5">Aset</th>
                                <th class="px-4 py-3.5">Dari Ruangan</th>
                                <th class="px-4 py-3.5">Ke Ruangan</th>
                                <th class="px-4 py-3.5">Alasan Mutasi</th>
                                <th class="px-6 py-3.5">Petugas PIC</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="m in recentMovements" :key="m.id" class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 whitespace-nowrap text-slate-500 font-medium">
                                    {{ new Date(m.movement_date).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) }}
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-slate-900">{{ m.asset?.name }}</div>
                                    <div class="text-[10px] font-mono text-emerald-700">{{ m.asset?.asset_code }}</div>
                                </td>
                                <td class="px-4 py-4 text-slate-600">
                                    {{ m.from_room?.name || 'Gudang Pusat' }}
                                </td>
                                <td class="px-4 py-4 font-bold text-[#168a42]">
                                    {{ m.to_room?.name }}
                                </td>
                                <td class="px-4 py-4 text-slate-700 max-w-xs">
                                    {{ m.reason }}
                                </td>
                                <td class="px-6 py-4 text-slate-800 font-medium">
                                    {{ m.moved_by?.name || 'Admin Sarpras' }}
                                </td>
                            </tr>
                            <tr v-if="recentMovements.length === 0">
                                <td colspan="6" class="px-6 py-10 text-center text-slate-400">
                                    Belum ada rekam mutasi aset yang tercatat.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

