<script setup>
import { Link } from '@inertiajs/vue3';
import { 
    Building2, 
    Layers, 
    CheckCircle2, 
    Clock, 
    Plus, 
    Download, 
    AlertTriangle, 
    Wrench, 
    Sparkles,
    ChevronRight,
    TrendingUp,
    Check,
    X,
    Repeat
} from 'lucide-vue-next';

const props = defineProps({
    metrics: Object,
    lowStockItems: Array,
    pendingLoans: Array,
    urgentTickets: Array,
    conditionCounts: Object,
    statusCounts: Object,
    distributionByFaculty: Array,
    utilizationData: Array,
});

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};
</script>

<template>
    <div class="space-y-6">
        <!-- 1. Top Welcome Banner Card -->
        <div class="bg-gradient-to-r from-[#0a1628] via-[#0f244a] to-[#1e3a8a] text-white rounded-2xl p-6 sm:p-7 border border-blue-900/60 shadow-lg shadow-blue-950/15 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 relative overflow-hidden">
            <!-- Decorative ambient glow -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="space-y-2 max-w-2xl relative z-10">
                <div class="flex items-center gap-2 text-xs font-semibold">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-500/20 text-cyan-200 border border-blue-400/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        Executive & Sarpras View
                    </span>
                    <span class="text-blue-200/60">•</span>
                    <span class="text-blue-200/80">Sinkronisasi Real-Time</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight">
                    SIM-SARPRAS Universitas Al-Khairiyah
                </h1>

                <p class="text-xs sm:text-sm text-blue-100/90 leading-relaxed">
                    Pusat pemantauan inventaris terintegrasi, pemanfaatan ruang akademik, permohonan logistik, dan jadwal pemeliharaan fasilitas kampus.
                </p>
            </div>

            <div class="flex flex-wrap sm:grid sm:grid-cols-2 gap-2.5 shrink-0 relative z-10">
                <Link
                    v-if="$page.props.auth?.user?.can_manage_assets"
                    :href="route('assets.create')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-blue-50 text-[#0c182c] rounded-xl text-xs font-extrabold shadow-md transition"
                >
                    <Plus class="w-4 h-4 stroke-[2.5]" />
                    <span>Registrasi Aset Baru</span>
                </Link>

                <Link
                    :href="route('loans.create')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl text-xs font-bold backdrop-blur-sm shadow-sm transition"
                >
                    <Plus class="w-4 h-4 text-cyan-300 stroke-[2.5]" />
                    <span>Ajukan Peminjaman</span>
                </Link>

                <Link
                    :href="route('maintenance.create')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-500/20 hover:bg-rose-500/30 border border-rose-400/30 text-rose-100 rounded-xl text-xs font-bold backdrop-blur-sm shadow-sm transition"
                >
                    <AlertTriangle class="w-4 h-4 text-rose-300" />
                    <span>Lapor Kerusakan</span>
                </Link>

                <Link
                    v-if="$page.props.auth?.user?.can_manage_assets"
                    :href="route('reports.print')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-xl text-xs font-bold backdrop-blur-sm shadow-sm transition"
                >
                    <Download class="w-4 h-4 text-blue-200" />
                    <span>Export Rekap</span>
                </Link>
            </div>
        </div>

        <!-- 2. Four Color Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Card 1: Total Nilai Aset Kampus -->
            <div class="bg-gradient-to-br from-[#0c1f3f] to-[#1e3a8a] text-white border border-blue-900 rounded-2xl p-5 shadow-md shadow-blue-950/20 hover:shadow-lg transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="h-9 w-9 rounded-xl bg-white/10 text-cyan-300 flex items-center justify-center">
                            <Building2 class="w-5 h-5" />
                        </div>
                        <span class="inline-flex items-center gap-1 text-[11px] font-bold text-cyan-200 bg-white/10 px-2 py-0.5 rounded-full border border-white/10">
                            <TrendingUp class="w-3 h-3 text-cyan-300" />
                            +4.2%
                        </span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-blue-200">TOTAL NILAI ASET KAMPUS</div>
                    <div class="text-2xl font-black text-white mt-1">{{ formatRupiah(metrics.totalValuation) }}</div>
                    <div class="text-[11px] text-blue-200/80 mt-1">Valuasi perolehan aset kampus</div>
                </div>
                <div class="mt-4 pt-3 border-t border-white/15 flex items-center justify-between text-[11px]">
                    <span class="text-blue-200/80">Nilai Buku: <strong class="text-white">{{ formatRupiah(metrics.totalValuation * 0.75) }}</strong></span>
                    <Link :href="route('reports.index')" class="text-cyan-300 hover:text-white font-bold">Lihat Audit</Link>
                </div>
            </div>

            <!-- Card 2: Total Unit Aset Terdaftar -->
            <div class="bg-[#fffbeb]/80 border border-amber-100 rounded-2xl p-5 shadow-sm hover:shadow transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="h-9 w-9 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                            <Layers class="w-5 h-5" />
                        </div>
                        <span class="inline-flex items-center text-[11px] font-bold text-amber-800 bg-amber-100/80 px-2.5 py-0.5 rounded-full">
                            Terverifikasi
                        </span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-amber-600">TOTAL UNIT ASET TERDAFTAR</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ metrics.totalAssets || 0 }} Item</div>
                    <div class="text-[11px] text-slate-500 mt-1 leading-tight">
                        Elektronik, Perangkat AV, Mebel & Fasilitas
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-amber-200/60 flex items-center justify-between text-[11px]">
                    <span class="text-slate-500">Kondisi Prima: <strong class="text-slate-700">{{ conditionCounts?.good || 0 }} Unit</strong></span>
                    <Link :href="route('assets.index')" class="text-amber-700 hover:text-amber-900 font-bold">Buku Induk</Link>
                </div>
            </div>

            <!-- Card 3: Kondisi Aset Prima -->
            <div class="bg-[#f0fdf4] border border-emerald-100 rounded-2xl p-5 shadow-sm hover:shadow transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="h-9 w-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <CheckCircle2 class="w-5 h-5" />
                        </div>
                        <span class="inline-flex items-center text-[11px] font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full">
                            Status Sehat
                        </span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700">KONDISI ASET PRIMA / LAIK</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">
                        {{ metrics.totalAssets > 0 ? Math.round((conditionCounts.good / metrics.totalAssets) * 100) : 100 }}%
                    </div>
                    <div class="text-[11px] text-slate-500 mt-1">
                        {{ conditionCounts.good }} Normal • {{ conditionCounts.light_damage }} Rawat • {{ conditionCounts.heavy_damage }} Rusak
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-emerald-200/60 flex items-center justify-between text-[11px]">
                    <div class="w-28 bg-emerald-200 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-emerald-600 h-full rounded-full" :style="{ width: `${metrics.totalAssets > 0 ? Math.round((conditionCounts.good / metrics.totalAssets) * 100) : 100}%` }"></div>
                    </div>
                    <Link :href="route('disposals.index')" class="text-emerald-700 hover:text-emerald-900 font-bold">Rincian Pemutihan</Link>
                </div>
            </div>

            <!-- Card 4: Peminjaman Aktif -->
            <div class="bg-[#eff6ff] border border-blue-100 rounded-2xl p-5 shadow-sm hover:shadow transition flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="h-9 w-9 rounded-xl bg-blue-100 text-[#1e3a8a] flex items-center justify-center">
                            <Clock class="w-5 h-5" />
                        </div>
                        <span class="inline-flex items-center text-[11px] font-bold text-blue-900 bg-blue-100 px-2.5 py-0.5 rounded-full">
                            Hari Ini
                        </span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-[#1e3a8a]">PEMINJAMAN AKTIF</div>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ metrics.activeLoans || 0 }} Sesi Aktif</div>
                    <div class="text-[11px] text-slate-500 mt-1">
                        Sirkulasi peminjaman sarana prasarana civitas
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-blue-200/60 flex items-center justify-between text-[11px]">
                    <span class="text-slate-500">Tiket Kerusakan: <strong class="text-slate-700">{{ metrics.activeTickets }} Aktif</strong></span>
                    <Link :href="route('loans.index')" class="text-blue-700 hover:text-blue-900 font-bold">Jadwal Pinjam</Link>
                </div>
            </div>
        </div>

        <!-- 3. Enterprise Info Banner Bar -->
        <div class="bg-gradient-to-r from-blue-50 via-slate-50 to-white rounded-2xl border border-blue-200/70 p-4 flex items-center justify-between gap-4 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-xl bg-[#1e3a8a] text-white flex items-center justify-center shrink-0 shadow-sm shadow-blue-900/25">
                    <Sparkles class="w-5 h-5" />
                </div>
                <div>
                    <div class="text-xs font-bold text-slate-900">Tata Kelola Terpadu Berbasis Standar Akreditasi Pendidikan Tinggi</div>
                    <div class="text-[11px] text-slate-500">Visualisasi sinkronisasi data real-time dengan inventaris fakultas, laboratorium, dan gedung kuliah kampus.</div>
                </div>
            </div>
            <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-blue-200 rounded-full text-xs font-bold text-blue-900 shrink-0 shadow-xs">
                <span>SIM-SARPRAS Enterprise</span>
            </div>
        </div>

        <!-- 4. Middle Section: Utilisasi Ruang & Distribusi Aset (2 Cols) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Utilisasi Ruang Kuliah & Lab -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">Utilisasi Ruang Kuliah & Lab</h2>
                            <p class="text-xs text-slate-500">Persentase okupansi per fakultas pada periode perkuliahan aktif</p>
                        </div>
                        <div class="flex items-center gap-3 text-xs">
                            <span class="px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-slate-600 font-semibold">
                                7 Hari Terakhir
                            </span>
                        </div>
                    </div>

                    <div class="bg-amber-50/70 border border-amber-200/60 rounded-xl p-3 mb-6 flex items-center gap-2.5 text-xs text-amber-900">
                        <Clock class="w-4 h-4 text-amber-600 shrink-0" />
                        <span><strong>Puncak Penggunaan: 10.00 - 14:00 WIB</strong> • Tingkat utilisasi mencapai 89% di seluruh lab & aula.</span>
                    </div>

                    <!-- Bar Chart Graphical Bars -->
                    <div class="grid grid-cols-5 gap-3 sm:gap-6 items-end h-44 px-2 pt-4 border-b border-slate-100">
                        <div 
                            v-for="item in utilizationData" 
                            :key="item.code" 
                            class="flex flex-col items-center gap-2 h-full justify-end group"
                        >
                            <div class="relative w-full max-w-[42px] bg-slate-100 rounded-t-xl overflow-visible h-full flex items-end justify-center">
                                <div 
                                    class="w-full bg-[#1e3a8a] rounded-t-xl transition-all duration-500 group-hover:bg-[#172554]"
                                    :style="{ height: `${item.rate}%` }"
                                ></div>
                            </div>
                            <span class="text-xs font-bold text-slate-700">{{ item.code }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-slate-500">
                    <div>
                        Kampus Terpadu: <strong class="text-slate-800">Universitas Al-Khairiyah</strong>
                    </div>
                    <Link :href="route('room-bookings.index')" class="font-bold text-[#1e3a8a] hover:text-[#172554] flex items-center gap-1">
                        Jadwal Peminjaman Ruangan
                        <ChevronRight class="w-4 h-4" />
                    </Link>
                </div>
            </div>

            <!-- Right 1 Col: Distribusi Aset Donut Chart -->
            <div class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="mb-5">
                        <h2 class="text-base font-bold text-slate-900">Distribusi Aset</h2>
                        <p class="text-xs text-slate-500">Proporsi per Fakultas & Unit Kerja</p>
                    </div>

                    <div class="relative flex items-center justify-center my-6">
                        <div class="w-36 h-36 rounded-full border-[14px] border-[#1e3a8a] flex items-center justify-center">
                            <div class="text-center">
                                <span class="text-xl font-black text-slate-900 block">{{ metrics.totalAssets }}</span>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Unit Total</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3 pt-2">
                        <div 
                            v-for="fac in distributionByFaculty" 
                            :key="fac.name" 
                            class="flex items-center justify-between text-xs"
                        >
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ backgroundColor: fac.color }"></span>
                                <span class="font-medium text-slate-700 truncate max-w-[130px]">{{ fac.name }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="font-bold text-slate-800">{{ fac.percentage }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400">
                    <Link :href="route('settings.index')" class="font-bold text-[#1e3a8a] hover:underline">Kelola Fakultas</Link>
                </div>
            </div>
        </div>

        <!-- 5. Bottom Section: Pending Loans Table & Urgent Maintenance Tickets (2 Cols) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Peminjaman Menunggu Persetujuan -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden flex flex-col justify-between">
                <div>
                    <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="h-9 w-9 rounded-xl bg-blue-50 text-[#1e3a8a] flex items-center justify-center">
                                <Repeat class="w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Peminjaman Menunggu Persetujuan</h2>
                                <p class="text-xs text-slate-500">Permohonan fasilitas & alat yang butuh verifikasi</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            {{ pendingLoans?.length || 0 }} Pengajuan
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-3.5">Pemohon</th>
                                    <th class="px-4 py-3.5">Fasilitas / Alat</th>
                                    <th class="px-4 py-3.5">Jadwal & Waktu</th>
                                    <th class="px-4 py-3.5">Keperluan</th>
                                    <th class="px-6 py-3.5 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="loan in pendingLoans" :key="loan.id" class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-slate-900">{{ loan.borrower?.name }}</div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">{{ loan.borrower?.department }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-slate-800">
                                            {{ loan.loan_items?.[0]?.asset?.name || 'Inventaris Sarpras' }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-0.5">
                                            {{ loan.loan_items?.length }} Unit Barang
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">
                                        <div class="font-bold text-slate-800">{{ loan.loan_date }}</div>
                                        <div class="text-[11px] text-slate-400 mt-0.5">s/d {{ loan.expected_return_date }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-[#1e3a8a] border border-blue-200 line-clamp-1 max-w-[140px]">
                                            {{ loan.purpose }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <Link 
                                            :href="route('loans.show', loan.id)" 
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl text-xs font-bold bg-[#1e3a8a] text-white hover:bg-[#172554] transition"
                                        >
                                            Periksa
                                        </Link>
                                    </td>
                                </tr>

                                <tr v-if="!pendingLoans?.length">
                                    <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                        Tidak ada pengajuan peminjaman menunggu saat ini.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="p-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Menampilkan antrean verifikasi sarpras</span>
                    <Link :href="route('loans.index')" class="font-bold text-[#1e3a8a] hover:text-[#172554]">
                        Kelola Semua Permohonan →
                    </Link>
                </div>
            </div>

            <!-- Right 1 Col: Tiket Perbaikan Mendesak -->
            <div class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="h-8 w-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                                <AlertTriangle class="w-4 h-4" />
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">Tiket Perbaikan Mendesak</h3>
                                <p class="text-[11px] text-slate-500">Penanganan kendala sarana ruang & gedung</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div 
                            v-for="ticket in urgentTickets" 
                            :key="ticket.id" 
                            class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/70 hover:bg-slate-100/70 transition space-y-1.5 text-xs"
                        >
                            <div class="flex items-center justify-between">
                                <span class="font-mono text-[10px] font-bold text-[#1e3a8a]">{{ ticket.ticket_code }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-rose-100 text-rose-800">
                                    {{ ticket.priority?.label || ticket.priority }}
                                </span>
                            </div>
                            <div class="font-bold text-slate-800 line-clamp-1">{{ ticket.asset?.name || 'Fasilitas Kampus' }}</div>
                            <div class="text-[11px] text-slate-500 line-clamp-2 leading-relaxed">
                                {{ ticket.issue_description }}
                            </div>
                        </div>

                        <div v-if="!urgentTickets?.length" class="py-8 text-center text-xs text-slate-400">
                            Semua fasilitas dalam keadaan prima.
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-400">Unit Teknisi Kampus</span>
                    <Link :href="route('maintenance.index')" class="font-bold text-[#1e3a8a] hover:text-[#172554]">
                        Buka Dispatch Teknisi →
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

