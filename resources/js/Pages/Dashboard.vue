<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Building2, 
    Layers, 
    CheckCircle2, 
    Clock, 
    Plus, 
    ArrowUpRight, 
    Download, 
    AlertTriangle, 
    Check, 
    X, 
    Wrench, 
    Sparkles,
    ChevronRight,
    TrendingUp,
    Info,
    Calendar,
    ArrowRight
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
    <Head title="SIM-SARPRAS Dashboard" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- 1. Top Welcome / Hero Banner Card -->
            <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/70 shadow-sm flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <!-- Left Title & Status -->
                <div class="space-y-2 max-w-2xl">
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-800 border border-blue-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                            Sistem Operasional Real-Time
                        </span>
                        <span>•</span>
                        <span class="text-slate-400">Sinkronisasi Inventaris Pukul 08:30 WIB</span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Selamat Datang di SIM-SARPRAS Kampus Terpadu
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Pusat pemantauan inventaris terintegrasi, pemanfaatan ruang akademik, permohonan logistik, dan jadwal pemeliharaan fasilitas Universitas Al-Khairiyah.
                    </p>
                </div>

                <!-- Right Quick Action Buttons -->
                <div class="flex flex-wrap sm:grid sm:grid-cols-2 gap-2.5 shrink-0">
                    <!-- Registrasi Aset Baru (Staff only) -->
                    <Link
                        v-if="$page.props.auth?.user?.can_manage_assets"
                        :href="route('assets.create')"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-900/25 transition"
                    >
                        <Plus class="w-4 h-4 stroke-[2.5]" />
                        <span>Registrasi Aset Baru</span>
                    </Link>

                    <!-- Ajukan Peminjaman -->
                    <Link
                        :href="route('loans.create')"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <Plus class="w-4 h-4 text-[#1e3a8a] stroke-[2.5]" />
                        <span>Ajukan Peminjaman</span>
                    </Link>

                    <!-- Lapor Kerusakan -->
                    <Link
                        :href="route('maintenance.create')"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-rose-50/50 border border-slate-200 text-rose-600 rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <AlertTriangle class="w-4 h-4 text-rose-500" />
                        <span>Lapor Kerusakan</span>
                    </Link>

                    <!-- Export Rekap (Staff only) -->
                    <Link
                        v-if="$page.props.auth?.user?.can_manage_assets"
                        :href="route('reports.print')"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <Download class="w-4 h-4 text-slate-500" />
                        <span>Export Rekap</span>
                    </Link>
                </div>
            </div>

            <!-- 2. Four Color Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- Card 1: Total Nilai Aset Kampus (Pink/Red tint) -->
                <div class="bg-[#fff1f2]/70 border border-rose-100 rounded-2xl p-5 shadow-sm hover:shadow transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="h-9 w-9 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                                <Building2 class="w-5 h-5" />
                            </div>
                            <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 bg-rose-100/70 px-2 py-0.5 rounded-full">
                                <TrendingUp class="w-3 h-3" />
                                +4.2%
                            </span>
                        </div>
                        <div class="text-[10px] font-extrabold uppercase tracking-wider text-rose-600">TOTAL NILAI ASET KAMPUS</div>
                        <div class="text-2xl font-black text-slate-900 mt-1">Rp 48,6 Miliar</div>
                        <div class="text-[11px] text-slate-500 mt-1">+4.2% dari target pengadaan TA 2024</div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-rose-200/60 flex items-center justify-between text-[11px]">
                        <span class="text-slate-500">Nilai Susut: <strong class="text-slate-700">Rp 3.2 M</strong></span>
                        <Link :href="route('reports.index')" class="text-rose-600 hover:text-rose-800 font-bold">Lihat Audit Aset</Link>
                    </div>
                </div>

                <!-- Card 2: Total Unit Aset Terdaftar (Amber/Yellow tint) -->
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
                        <div class="text-2xl font-black text-slate-900 mt-1">14.820 Item</div>
                        <div class="text-[11px] text-slate-500 mt-1 leading-tight">
                            Elektronik: 6.420 • Mebeler: 5.100 • Kendaraan: 82 • Gedung: 45
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-amber-200/60 flex items-center justify-between text-[11px]">
                        <span class="text-slate-500">Terlabel Barcode/RFID: <strong class="text-slate-700">98%</strong></span>
                        <Link :href="route('stock-opname.scanner')" class="text-amber-700 hover:text-amber-900 font-bold">Scan Fisik Aset</Link>
                    </div>
                </div>

                <!-- Card 3: Kondisi Aset Prima (Blue tint) -->
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
                        <div class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700">KONDISI ASET PRIMA / LAIK PAKAI</div>
                        <div class="text-2xl font-black text-slate-900 mt-1">94.2%</div>
                        <div class="text-[11px] text-slate-500 mt-1">
                            13.960 Normal • 580 Rawat • 280 Rusak
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-emerald-200/60 flex items-center justify-between text-[11px]">
                        <div class="w-28 bg-emerald-200 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-emerald-600 h-full w-[94.2%] rounded-full"></div>
                        </div>
                        <Link :href="route('disposals.index')" class="text-emerald-700 hover:text-emerald-900 font-bold">Rincian Pemutihan</Link>
                    </div>
                </div>

                <!-- Card 4: Peminjaman Aktif (Blue tint) -->
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
                        <div class="text-2xl font-black text-slate-900 mt-1">38 Sesi Aktif</div>
                        <div class="text-[11px] text-slate-500 mt-1">
                            8 Ruang Kuliah • 12 Lab • 18 Perangkat AV
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-blue-200/60 flex items-center justify-between text-[11px]">
                        <span class="text-slate-500">Okupansi Rata-rata: <strong class="text-slate-700">78%</strong></span>
                        <Link :href="route('loans.index')" class="text-blue-700 hover:text-blue-900 font-bold">Jadwal Ruang</Link>
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
                        <div class="text-xs font-bold text-slate-900">Tata Kelola Terpadu Berbasis Modul Dabang Enterprise</div>
                        <div class="text-[11px] text-slate-500">Visualisasi sinkronisasi data real-time dengan inventaris laboratorium, perpustakaan, dan gedung kuliah bersama.</div>
                    </div>
                </div>
                <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-blue-200 rounded-full text-xs font-bold text-blue-900 shrink-0 shadow-xs">
                    <span>Layout Terstandarisasi Dikti</span>
                </div>
            </div>

            <!-- 4. Middle Section: Utilisasi Ruang & Distribusi Aset (2 Cols) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Cols: Utilisasi Ruang Kuliah & Lab -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <!-- Header -->
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Utilisasi Ruang Kuliah & Lab</h2>
                                <p class="text-xs text-slate-500">Persentase okupansi per fakultas pada periode perkuliahan aktif</p>
                            </div>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="px-2.5 py-1 bg-slate-50 border border-slate-200 rounded-lg text-slate-600 font-semibold">
                                    7 Hari Terakhir
                                </span>
                                <div class="flex items-center gap-3 text-[11px] text-slate-500 font-medium">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-[#1e3a8a]"></span>
                                        Rata-rata Kapasitas
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                                        Jam Puncak
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Peak Hour Notice -->
                        <div class="bg-amber-50/70 border border-amber-200/60 rounded-xl p-3 mb-6 flex items-center gap-2.5 text-xs text-amber-900">
                            <Clock class="w-4 h-4 text-amber-600 shrink-0" />
                            <span><strong>Puncak Penggunaan: 10.00 - 14:00 WIB</strong> • Tingkat utilisasi mencapai 91% di seluruh lab komputer.</span>
                        </div>

                        <!-- Bar Chart Graphical Bars -->
                        <div class="grid grid-cols-6 gap-3 sm:gap-6 items-end h-44 px-2 pt-4 border-b border-slate-100">
                            <div 
                                v-for="item in utilizationData" 
                                :key="item.code" 
                                class="flex flex-col items-center gap-2 h-full justify-end group"
                            >
                                <div class="relative w-full max-w-[42px] bg-slate-100 rounded-t-xl overflow-visible h-full flex items-end justify-center">
                                    <!-- Peak indicator dot -->
                                    <span 
                                        class="absolute -top-1 w-2 h-2 rounded-full bg-slate-400 border border-white"
                                        :style="{ bottom: `${item.peak}%` }"
                                    ></span>
                                    <!-- Blue filled bar -->
                                    <div 
                                        class="w-full bg-[#1e3a8a] rounded-t-xl transition-all duration-500 group-hover:bg-[#172554]"
                                        :style="{ height: `${item.rate}%` }"
                                    ></div>
                                </div>
                                <span class="text-xs font-bold text-slate-700">{{ item.code }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Details -->
                    <div class="mt-4 pt-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 text-xs text-slate-500">
                        <div>
                            Total Kelas: <strong class="text-slate-800">124 Ruang</strong> • Lab Khusus: <strong class="text-slate-800">42 Unit</strong>
                        </div>
                        <Link :href="route('settings.index')" class="font-bold text-[#1e3a8a] hover:text-[#172554] flex items-center gap-1">
                            Lihat Denah Ruang & Jadwal Lengkap
                            <ChevronRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>

                <!-- Right 1 Col: Distribusi Aset Donut Chart -->
                <div class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <!-- Header -->
                        <div class="mb-5">
                            <h2 class="text-base font-bold text-slate-900">Distribusi Aset</h2>
                            <p class="text-xs text-slate-500">Proporsi per Fakultas & Unit Kerja</p>
                        </div>

                        <!-- Donut Graphic + Legend -->
                        <div class="flex items-center justify-center gap-6 my-3">
                            <!-- SVG Donut Chart with Center Text -->
                            <div class="relative w-36 h-36 shrink-0 flex items-center justify-center">
                                <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                                    <!-- Background Circle -->
                                    <circle cx="50" cy="50" r="38" stroke="#f1f5f9" stroke-width="12" fill="none" />
                                    <!-- Segments -->
                                    <!-- FT: 32% (dash: 76.4, offset: 0) -->
                                    <circle cx="50" cy="50" r="38" stroke="#0f766e" stroke-width="12" stroke-dasharray="76.4 162.3" stroke-dashoffset="0" fill="none" />
                                    <!-- FK: 28% (dash: 66.8, offset: -76.4) -->
                                    <circle cx="50" cy="50" r="38" stroke="#16a34a" stroke-width="12" stroke-dasharray="66.8 171.9" stroke-dashoffset="-76.4" fill="none" />
                                    <!-- Rektorat: 18% (dash: 43.0, offset: -143.2) -->
                                    <circle cx="50" cy="50" r="38" stroke="#d97706" stroke-width="12" stroke-dasharray="43.0 195.7" stroke-dashoffset="-143.2" fill="none" />
                                    <!-- Lainnya: 22% (dash: 52.5, offset: -186.2) -->
                                    <circle cx="50" cy="50" r="38" stroke="#84cc16" stroke-width="12" stroke-dasharray="52.5 186.2" stroke-dashoffset="-186.2" fill="none" />
                                </svg>
                                <!-- Center Label -->
                                <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                                    <span class="text-lg font-black text-slate-900 leading-tight">14.8k</span>
                                    <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Unit Total</span>
                                </div>
                            </div>

                            <!-- Legend List -->
                            <div class="space-y-2.5 flex-1 text-xs">
                                <div v-for="item in distributionByFaculty" :key="item.name" class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ backgroundColor: item.color }"></span>
                                        <span class="font-medium text-slate-700 truncate max-w-[100px]">{{ item.name }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold text-slate-900">{{ item.percentage }}%</span>
                                        <span class="text-[10px] text-slate-400 block">{{ item.items.toLocaleString('id-ID') }} item</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Pembaruan Buku Induk: <strong class="text-slate-700">Kemarin</strong></span>
                        <Link :href="route('reports.index')" class="font-bold text-[#1e3a8a] hover:text-[#172554]">Detail Sensus Aset</Link>
                    </div>
                </div>
            </div>

            <!-- 5. Bottom Section: Peminjaman Menunggu & Tiket Perbaikan Mendesak (2 Cols) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Cols: Peminjaman Menunggu Persetujuan Table -->
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden flex flex-col justify-between">
                    <div>
                        <!-- Header -->
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-blue-50 text-[#1e3a8a] flex items-center justify-center">
                                    <CheckCircle2 class="w-4 h-4" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Peminjaman Menunggu Persetujuan</h3>
                                    <p class="text-[11px] text-slate-500">3 permohonan butuh verifikasi wadir/kabag hari ini</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 border border-rose-200">
                                Pending: 3 Pengajuan
                            </span>
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-3">Pemohon</th>
                                        <th class="px-4 py-3">Fasilitas / Alat</th>
                                        <th class="px-4 py-3">Jadwal & Waktu</th>
                                        <th class="px-4 py-3">Keperluan</th>
                                        <th class="px-6 py-3 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <!-- Row 1 -->
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="px-6 py-3.5">
                                            <div class="font-bold text-slate-900">Dr. Hendra Wijaya</div>
                                            <div class="text-[11px] text-slate-400">NIP 19820412 • Teknik Elektro</div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <div class="font-semibold text-slate-800">Auditorium Gd. A</div>
                                            <div class="text-[11px] text-slate-400">Sound System + 2 Mic Wireless</div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <div class="font-medium text-slate-800">Besok, 09:00</div>
                                            <div class="text-[10px] text-slate-400">Durasi: 4 Jam</div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-900 border border-blue-200">
                                                Seminar Nasional
                                            </span>
                                        </td>
                                        <td class="px-6 py-3.5 text-center">
                                            <div v-if="$page.props.auth?.user?.can_manage_loans" class="inline-flex items-center gap-1.5">
                                                <button class="w-7 h-7 rounded-lg bg-[#1e3a8a] hover:bg-[#172554] text-white flex items-center justify-center transition shadow-xs">
                                                    <Check class="w-3.5 h-3.5 stroke-[3]" />
                                                </button>
                                                <button class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition">
                                                    <X class="w-3.5 h-3.5 stroke-[3]" />
                                                </button>
                                            </div>
                                            <span v-else class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Menunggu
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Row 2 -->
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="px-6 py-3.5">
                                            <div class="font-bold text-slate-900">Nabila Putri (BEM)</div>
                                            <div class="text-[11px] text-slate-400">NIM 21051204 • Kedokteran</div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <div class="font-semibold text-slate-800">Lab Anatomi 2</div>
                                            <div class="text-[11px] text-slate-400">1 Set Proyektor Laser 4K</div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <div class="font-medium text-slate-800">24 Okt, 13:00</div>
                                            <div class="text-[10px] text-slate-400">Durasi: 3 Jam</div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-900 border border-blue-200">
                                                Praktikum Mandiri
                                            </span>
                                        </td>
                                        <td class="px-6 py-3.5 text-center">
                                            <div v-if="$page.props.auth?.user?.can_manage_loans" class="inline-flex items-center gap-1.5">
                                                <button class="w-7 h-7 rounded-lg bg-[#1e3a8a] hover:bg-[#172554] text-white flex items-center justify-center transition shadow-xs">
                                                    <Check class="w-3.5 h-3.5 stroke-[3]" />
                                                </button>
                                                <button class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition">
                                                    <X class="w-3.5 h-3.5 stroke-[3]" />
                                                </button>
                                            </div>
                                            <span v-else class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Menunggu
                                            </span>
                                        </td>
                                    </tr>

                                    <!-- Row 3 -->
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="px-6 py-3.5">
                                            <div class="font-bold text-slate-900">Fahmi Ramadhan</div>
                                            <div class="text-[11px] text-slate-400">NIM 20042019 • Akuntansi FEB</div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <div class="font-semibold text-slate-800">Ruang Sidang 304</div>
                                            <div class="text-[11px] text-slate-400">Kamera Hybrid & TV Monitor</div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <div class="font-medium text-slate-800">25 Okt, 10:00</div>
                                            <div class="text-[10px] text-slate-400">Durasi: 2.5 Jam</div>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Sidang Skripsi
                                            </span>
                                        </td>
                                        <td class="px-6 py-3.5 text-center">
                                            <div v-if="$page.props.auth?.user?.can_manage_loans" class="inline-flex items-center gap-1.5">
                                                <button class="w-7 h-7 rounded-lg bg-[#1e3a8a] hover:bg-[#172554] text-white flex items-center justify-center transition shadow-xs">
                                                    <Check class="w-3.5 h-3.5 stroke-[3]" />
                                                </button>
                                                <button class="w-7 h-7 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 flex items-center justify-center transition">
                                                    <X class="w-3.5 h-3.5 stroke-[3]" />
                                                </button>
                                            </div>
                                            <span v-else class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Menunggu
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-3.5 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Menampilkan 3 dari 18 permohonan aktif</span>
                        <Link :href="route('loans.index')" class="font-bold text-[#1e3a8a] hover:text-[#172554] flex items-center gap-1">
                            Kelola Semua Permohonan
                            <ChevronRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>

                <!-- Right 1 Col: Tiket Perbaikan Mendesak -->
                <div class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <!-- Header -->
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2.5">
                                <div class="w-7 h-7 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                                    <Wrench class="w-4 h-4" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-slate-900">Tiket Perbaikan Mendesak</h3>
                                    <p class="text-[11px] text-slate-500">Penanganan kendala sarana ruang & gedung</p>
                                </div>
                            </div>
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-ping"></span>
                        </div>

                        <!-- Ticket Items List -->
                        <div class="space-y-3">
                            <template v-if="urgentTickets && urgentTickets.length > 0">
                                <Link
                                    v-for="ticket in urgentTickets.slice(0, 4)"
                                    :key="ticket.id"
                                    :href="route('maintenance.show', ticket.id)"
                                    class="block p-3 bg-slate-50/80 hover:bg-slate-100/80 rounded-xl border border-slate-100 hover:border-slate-200 transition"
                                >
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="font-bold text-xs text-slate-900 line-clamp-1">{{ ticket.issue_description.split('.')[0] }}</div>
                                        <span 
                                            :class="[
                                                'px-2 py-0.5 rounded-full text-[9px] font-extrabold shrink-0',
                                                ticket.priority?.value === 'critical' || ticket.priority === 'critical' ? 'bg-rose-100 text-rose-700' :
                                                ticket.status?.value === 'waiting_parts' || ticket.status === 'waiting_parts' ? 'bg-amber-100 text-amber-800' :
                                                ticket.status?.value === 'completed' || ticket.status === 'completed' ? 'bg-emerald-100 text-emerald-800' :
                                                'bg-teal-100 text-teal-800'
                                            ]"
                                        >
                                            {{ ticket.status?.label || (ticket.priority === 'critical' ? 'Deadline Hari Ini' : ticket.status) }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-1 line-clamp-1">
                                        {{ ticket.resolution_notes || (ticket.technician ? `Teknisi: ${ticket.technician.name}` : 'Menunggu penugasan teknisi') }}
                                    </div>
                                </Link>
                            </template>
                            <template v-else>
                                <!-- Ticket 1 -->
                                <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100 hover:border-slate-200 transition">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="font-bold text-xs text-slate-900">AC Lab Jaringan Gedung C Mati Total</div>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-rose-100 text-rose-700 shrink-0">
                                            Deadline Hari Ini
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-1">
                                        Teknisi: Pak Sugeng (Vendor Daikin) • Estimasi: Hari ini 17:00
                                    </div>
                                </div>

                                <!-- Ticket 2 -->
                                <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100 hover:border-slate-200 transition">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="font-bold text-xs text-slate-900">Proyektor R. 302 Gd. Bersama Buram</div>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-amber-100 text-amber-800 shrink-0">
                                            Menunggu Part
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-1">
                                        Lampu proyektor aus • Status: PO Sparepart INV-204
                                    </div>
                                </div>

                                <!-- Ticket 3 -->
                                <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100 hover:border-slate-200 transition">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="font-bold text-xs text-slate-900">Lift Gedung Rektorat Pemeliharaan</div>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-blue-100 text-blue-800 shrink-0">
                                            Selesai 80%
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-1">
                                        Pemeriksaan kabel seling berkala semesteran
                                    </div>
                                </div>

                                <!-- Ticket 4 -->
                                <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-100 hover:border-slate-200 transition">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="font-bold text-xs text-slate-900">Pompa Air Asrama Mahasiswa Putra</div>
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold bg-teal-100 text-teal-800 shrink-0">
                                            Sedang Dikerjakan
                                        </span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 mt-1">
                                        Pipa distribusi blok B bocor • Tim Internal Sarpras
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>8 Teknisi On-Duty di Kampus</span>
                        <Link :href="route('maintenance.index')" class="font-bold text-[#1e3a8a] hover:text-[#172554] flex items-center gap-1">
                            <span>{{ $page.props.auth?.user?.can_update_maintenance ? 'Buka Dispatch Teknisi' : 'Lihat Tiket Pemeliharaan' }}</span>
                            <ChevronRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
