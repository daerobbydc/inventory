<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { 
    Building2, 
    Layers, 
    CheckCircle2, 
    Clock, 
    Wrench, 
    TrendingUp,
    MoreVertical,
    Check,
    Repeat,
    ArrowUpRight,
    Bell,
    Box,
    Key,
    DoorOpen,
    ShoppingBag
} from 'lucide-vue-next';

const props = defineProps({
    metrics: {
        type: Object,
        default: () => ({
            totalValuation: 0,
            totalAssets: 0,
            activeLoans: 0,
            activeTickets: 0,
        }),
    },
    lowStockItems: {
        type: Array,
        default: () => [],
    },
    pendingLoans: {
        type: Array,
        default: () => [],
    },
    recentCirculations: {
        type: Array,
        default: () => [],
    },
    urgentTickets: {
        type: Array,
        default: () => [],
    },
    conditionCounts: {
        type: Object,
        default: () => ({ good: 0, light_damage: 0, heavy_damage: 0 }),
    },
    statusCounts: {
        type: Object,
        default: () => ({ available: 0, borrowed: 0, maintenance: 0, disposed: 0 }),
    },
    distributionByFaculty: {
        type: Array,
        default: () => [],
    },
    utilizationData: {
        type: Array,
        default: () => [],
    },
});

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const formatShortValuation = (val) => {
    const num = Number(val || 0);
    if (num >= 1000000000) {
        return 'Rp ' + (num / 1000000000).toFixed(1).replace('.0', '') + ' M';
    }
    if (num >= 1000000) {
        return 'Rp ' + (num / 1000000).toFixed(1).replace('.0', '') + ' Jt';
    }
    return formatRupiah(num);
};

// 6 Projects Rows matching Material Dashboard 2 XD Spec
const defaultProjects = [
    {
        id: 'p1',
        title: 'Material XD Version',
        company: 'Lab Multimedia & Komputer FIK',
        initials: 'XD',
        avatarBg: 'bg-gradient-to-tr from-pink-600 to-rose-500',
        members: ['A', 'D', 'R'],
        budget: 'Rp 14.000.000',
        progress: 60,
        progressColor: 'bg-blue-600',
    },
    {
        id: 'p2',
        title: 'Add Progress Track',
        company: 'Biro Administrasi Sarpras',
        initials: 'AT',
        avatarBg: 'bg-gradient-to-tr from-blue-600 to-indigo-600',
        members: ['B', 'S'],
        budget: 'Rp 3.000.000',
        progress: 10,
        progressColor: 'bg-blue-600',
    },
    {
        id: 'p3',
        title: 'Fix Platform Errors',
        company: 'Lab Jaringan & Keamanan FT',
        initials: 'SL',
        avatarBg: 'bg-gradient-to-tr from-emerald-600 to-teal-500',
        members: ['F', 'S'],
        budget: 'Not set',
        progress: 100,
        progressColor: 'bg-emerald-500',
    },
    {
        id: 'p4',
        title: 'Launch our Mobile App',
        company: 'Himpunan Mahasiswa Informatika',
        initials: 'SP',
        avatarBg: 'bg-gradient-to-tr from-emerald-500 to-green-600',
        members: ['R', 'M', 'D', 'T'],
        budget: 'Rp 20.500.000',
        progress: 100,
        progressColor: 'bg-emerald-500',
    },
    {
        id: 'p5',
        title: 'Add the New Pricing Page',
        company: 'Fakultas Ekonomi & Bisnis',
        initials: 'JR',
        avatarBg: 'bg-gradient-to-tr from-amber-500 to-orange-500',
        members: ['D', 'R', 'S'],
        budget: 'Rp 500.000',
        progress: 25,
        progressColor: 'bg-amber-500',
    },
    {
        id: 'p6',
        title: 'Redesign New Online Shop',
        company: 'Unit Sarana Prasarana Kampus',
        initials: 'IN',
        avatarBg: 'bg-gradient-to-tr from-rose-500 to-pink-600',
        members: ['H', 'G'],
        budget: 'Rp 2.000.000',
        progress: 40,
        progressColor: 'bg-blue-600',
    },
];

const projectRows = computed(() => {
    if (props.recentCirculations && props.recentCirculations.length > 0) {
        return props.recentCirculations.slice(0, 6).map((item, idx) => {
            const fallback = defaultProjects[idx % defaultProjects.length];
            const borrowerName = item.borrower?.name || fallback.company;
            const assetName = item.loan_items?.[0]?.asset?.name || fallback.title;
            const statusStr = typeof item.status === 'object' ? item.status?.value : item.status;

            let progress = 50;
            let progressColor = 'bg-blue-600';
            if (statusStr === 'returned') {
                progress = 100;
                progressColor = 'bg-emerald-500';
            } else if (statusStr === 'borrowed') {
                progress = 60 + (idx * 10);
                progressColor = 'bg-blue-600';
            } else if (statusStr === 'approved') {
                progress = 40;
                progressColor = 'bg-blue-500';
            } else {
                progress = 15;
                progressColor = 'bg-amber-500';
            }

            return {
                id: item.id,
                title: assetName,
                company: `${borrowerName} • ${item.purpose ? item.purpose.substring(0, 38) + '...' : fallback.company}`,
                initials: fallback.initials,
                avatarBg: fallback.avatarBg,
                members: [borrowerName.charAt(0) || 'U', 'P', 'S'],
                budget: fallback.budget,
                progress: progress,
                progressColor: progressColor,
            };
        });
    }

    return defaultProjects;
});
</script>

<template>
    <div class="space-y-8 pt-2">
        <!-- 1. Four Overhanging Metric Cards (Signature Material Dashboard 2 Style) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 pt-3">
            <!-- Card 1: Valuasi Total Aset (Dark Charcoal Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#1e293b] to-[#0f172a] shadow-lg shadow-slate-900/30 flex items-center justify-center text-white">
                    <Building2 class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-slate-500">Valuasi Total Aset</span>
                    <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                        {{ formatShortValuation(metrics.totalValuation) }}
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-slate-500 flex items-center gap-1">
                    <span class="text-emerald-500 font-bold flex items-center">
                        <TrendingUp class="w-3.5 h-3.5 mr-0.5" /> +55%
                    </span>
                    <span>than last week</span>
                </div>
            </div>

            <!-- Card 2: Total Unit Aset (Vibrant Blue Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#1a73e8] to-[#4285f4] shadow-lg shadow-blue-500/30 flex items-center justify-center text-white">
                    <Layers class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-slate-500">Total Unit Terdaftar</span>
                    <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                        {{ Number(metrics.totalAssets || 0).toLocaleString('id-ID') }}
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-slate-500 flex items-center gap-1">
                    <span class="text-emerald-500 font-bold">+3%</span>
                    <span>than last month</span>
                </div>
            </div>

            <!-- Card 3: Kondisi Prima / Laik (Emerald Green Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#10b981] to-[#059669] shadow-lg shadow-emerald-500/30 flex items-center justify-center text-white">
                    <CheckCircle2 class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-slate-500">Unit Kondisi Prima</span>
                    <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                        {{ Number(conditionCounts?.good || 0).toLocaleString('id-ID') }}
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-slate-500 flex items-center gap-1">
                    <span class="text-emerald-500 font-bold">
                        {{ metrics.totalAssets > 0 ? Math.round((conditionCounts.good / metrics.totalAssets) * 100) : 100 }}%
                    </span>
                    <span>kondisi laik operasional</span>
                </div>
            </div>

            <!-- Card 4: Peminjaman & Sirkulasi (Hot Pink / Rose Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-2xl bg-gradient-to-tr from-[#ec4899] to-[#e11d48] shadow-lg shadow-pink-500/30 flex items-center justify-center text-white">
                    <Repeat class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-slate-500">Peminjaman Aktif</span>
                    <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                        {{ Number(metrics.activeLoans || 0).toLocaleString('id-ID') }} Sesi
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-slate-500 flex items-center gap-1">
                    <span class="text-emerald-500 font-bold">+5%</span>
                    <span>Just updated</span>
                </div>
            </div>
        </div>

        <!-- 2. Three Overhanging Visual Chart Cards (Material Dashboard Signature Visual Cards) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-2">
            <!-- Chart Card 1: Distribusi Aset Fakultas (Blue Gradient Bar Chart) -->
            <div class="bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-6">
                <!-- Overhanging Negative-Margin Card Box -->
                <div class="relative -mt-6 mx-2 rounded-2xl p-4 text-white shadow-lg bg-gradient-to-tr from-[#1a73e8] to-[#4285f4] shadow-blue-500/35">
                    <div class="h-44 flex flex-col justify-between">
                        <!-- SVG Bar Chart -->
                        <svg class="w-full h-full overflow-visible" viewBox="0 0 320 150">
                            <!-- Dashed Horizontal Guide Lines -->
                            <line x1="25" y1="20" x2="310" y2="20" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="55" x2="310" y2="55" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="90" x2="310" y2="90" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="125" x2="310" y2="125" stroke="rgba(255,255,255,0.4)" stroke-width="1" />

                            <!-- Y-Axis Labels -->
                            <text x="18" y="24" fill="rgba(255,255,255,0.7)" font-size="10" text-anchor="end">60</text>
                            <text x="18" y="59" fill="rgba(255,255,255,0.7)" font-size="10" text-anchor="end">40</text>
                            <text x="18" y="94" fill="rgba(255,255,255,0.7)" font-size="10" text-anchor="end">20</text>
                            <text x="18" y="128" fill="rgba(255,255,255,0.7)" font-size="10" text-anchor="end">0</text>

                            <!-- Vertical Bars (7 Days / Units) -->
                            <rect x="52" y="35" width="6" height="90" rx="3" fill="#ffffff" />
                            <rect x="92" y="70" width="6" height="55" rx="3" fill="#ffffff" />
                            <rect x="132" y="90" width="6" height="35" rx="3" fill="#ffffff" />
                            <rect x="172" y="50" width="6" height="75" rx="3" fill="#ffffff" />
                            <rect x="212" y="25" width="6" height="100" rx="3" fill="#ffffff" />
                            <rect x="252" y="95" width="6" height="30" rx="3" fill="#ffffff" />
                            <rect x="292" y="45" width="6" height="80" rx="3" fill="#ffffff" />

                            <!-- X-Axis Labels -->
                            <text x="55" y="142" fill="rgba(255,255,255,0.9)" font-size="10" text-anchor="middle">M</text>
                            <text x="95" y="142" fill="rgba(255,255,255,0.9)" font-size="10" text-anchor="middle">T</text>
                            <text x="135" y="142" fill="rgba(255,255,255,0.9)" font-size="10" text-anchor="middle">W</text>
                            <text x="175" y="142" fill="rgba(255,255,255,0.9)" font-size="10" text-anchor="middle">T</text>
                            <text x="215" y="142" fill="rgba(255,255,255,0.9)" font-size="10" text-anchor="middle">F</text>
                            <text x="255" y="142" fill="rgba(255,255,255,0.9)" font-size="10" text-anchor="middle">S</text>
                            <text x="295" y="142" fill="rgba(255,255,255,0.9)" font-size="10" text-anchor="middle">S</text>
                        </svg>
                    </div>
                </div>

                <div class="pt-4 px-2">
                    <h4 class="text-sm font-bold text-slate-900">Distribusi Aset Fakultas</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Sebaran sarana laboratorium dan akademik</p>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="flex items-center gap-1.5 text-xs text-slate-400 px-2 pb-1">
                    <Clock class="w-3.5 h-3.5" />
                    <span>campaign sent 2 days ago</span>
                </div>
            </div>

            <!-- Chart Card 2: Utilisasi Ruang & Lab (Emerald Green Gradient Smooth Line Chart) -->
            <div class="bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-6">
                <!-- Overhanging Negative-Margin Card Box -->
                <div class="relative -mt-6 mx-2 rounded-2xl p-4 text-white shadow-lg bg-gradient-to-tr from-[#10b981] to-[#059669] shadow-emerald-500/35">
                    <div class="h-44 flex flex-col justify-between">
                        <!-- SVG Curved Line Chart -->
                        <svg class="w-full h-full overflow-visible" viewBox="0 0 320 150">
                            <!-- Dashed Horizontal Guide Lines -->
                            <line x1="28" y1="20" x2="310" y2="20" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="28" y1="55" x2="310" y2="55" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="28" y1="90" x2="310" y2="90" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="28" y1="125" x2="310" y2="125" stroke="rgba(255,255,255,0.4)" stroke-width="1" />

                            <!-- Y-Axis Labels -->
                            <text x="22" y="24" fill="rgba(255,255,255,0.7)" font-size="9" text-anchor="end">600</text>
                            <text x="22" y="59" fill="rgba(255,255,255,0.7)" font-size="9" text-anchor="end">400</text>
                            <text x="22" y="94" fill="rgba(255,255,255,0.7)" font-size="9" text-anchor="end">200</text>
                            <text x="22" y="128" fill="rgba(255,255,255,0.7)" font-size="9" text-anchor="end">0</text>

                            <!-- Smooth Line Path -->
                            <path 
                                d="M 45 120 C 65 115, 80 75, 100 65 C 125 50, 145 95, 175 100 C 205 105, 225 60, 250 40 C 275 20, 290 80, 305 60" 
                                fill="none" 
                                stroke="#ffffff" 
                                stroke-width="3" 
                                stroke-linecap="round" 
                                stroke-linejoin="round"
                            />

                            <!-- Data Points -->
                            <circle cx="45" cy="120" r="3.5" fill="#ffffff" />
                            <circle cx="100" cy="65" r="3.5" fill="#ffffff" />
                            <circle cx="175" cy="100" r="3.5" fill="#ffffff" />
                            <circle cx="250" cy="40" r="3.5" fill="#ffffff" />
                            <circle cx="305" cy="60" r="3.5" fill="#ffffff" />

                            <!-- X-Axis Labels -->
                            <text x="45" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Apr</text>
                            <text x="75" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">May</text>
                            <text x="105" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Jun</text>
                            <text x="140" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Jul</text>
                            <text x="175" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Aug</text>
                            <text x="210" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Sep</text>
                            <text x="245" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Oct</text>
                            <text x="275" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Nov</text>
                            <text x="305" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Dec</text>
                        </svg>
                    </div>
                </div>

                <div class="pt-4 px-2">
                    <h4 class="text-sm font-bold text-slate-900">Utilisasi Ruang & Lab</h4>
                    <p class="text-xs text-slate-500 mt-0.5"><span class="text-emerald-600 font-bold">(+15%)</span> peningkatan okupansi semester ini</p>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="flex items-center gap-1.5 text-xs text-slate-400 px-2 pb-1">
                    <Clock class="w-3.5 h-3.5" />
                    <span>updated 4 min ago</span>
                </div>
            </div>

            <!-- Chart Card 3: Ketuntasan Servis Fasilitas (Charcoal Dark Gradient Wave Chart) -->
            <div class="bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-6">
                <!-- Overhanging Negative-Margin Card Box -->
                <div class="relative -mt-6 mx-2 rounded-2xl p-4 text-white shadow-lg bg-gradient-to-tr from-[#1e293b] to-[#0f172a] shadow-slate-900/40">
                    <div class="h-44 flex flex-col justify-between">
                        <!-- SVG Jagged / Wave Line Chart -->
                        <svg class="w-full h-full overflow-visible" viewBox="0 0 320 150">
                            <!-- Dashed Horizontal Guide Lines -->
                            <line x1="28" y1="20" x2="310" y2="20" stroke="rgba(255,255,255,0.2)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="28" y1="55" x2="310" y2="55" stroke="rgba(255,255,255,0.2)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="28" y1="90" x2="310" y2="90" stroke="rgba(255,255,255,0.2)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="28" y1="125" x2="310" y2="125" stroke="rgba(255,255,255,0.3)" stroke-width="1" />

                            <!-- Y-Axis Labels -->
                            <text x="22" y="24" fill="rgba(255,255,255,0.6)" font-size="9" text-anchor="end">600</text>
                            <text x="22" y="59" fill="rgba(255,255,255,0.6)" font-size="9" text-anchor="end">400</text>
                            <text x="22" y="94" fill="rgba(255,255,255,0.6)" font-size="9" text-anchor="end">200</text>
                            <text x="22" y="128" fill="rgba(255,255,255,0.6)" font-size="9" text-anchor="end">0</text>

                            <!-- Wave Line Path -->
                            <path 
                                d="M 45 125 L 80 90 L 115 110 L 150 45 L 185 85 L 220 50 L 255 95 L 305 40" 
                                fill="none" 
                                stroke="#ffffff" 
                                stroke-width="3" 
                                stroke-linecap="round" 
                                stroke-linejoin="round"
                            />

                            <!-- Data Points -->
                            <circle cx="45" cy="125" r="3.5" fill="#ffffff" />
                            <circle cx="80" cy="90" r="3.5" fill="#ffffff" />
                            <circle cx="115" cy="110" r="3.5" fill="#ffffff" />
                            <circle cx="150" cy="45" r="3.5" fill="#ffffff" />
                            <circle cx="185" cy="85" r="3.5" fill="#ffffff" />
                            <circle cx="220" cy="50" r="3.5" fill="#ffffff" />
                            <circle cx="255" cy="95" r="3.5" fill="#ffffff" />
                            <circle cx="305" cy="40" r="3.5" fill="#ffffff" />

                            <!-- X-Axis Labels -->
                            <text x="45" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Apr</text>
                            <text x="75" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">May</text>
                            <text x="105" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Jun</text>
                            <text x="140" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Jul</text>
                            <text x="175" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Aug</text>
                            <text x="210" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Sep</text>
                            <text x="245" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Oct</text>
                            <text x="275" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Nov</text>
                            <text x="305" y="142" fill="rgba(255,255,255,0.9)" font-size="8.5" text-anchor="middle">Dec</text>
                        </svg>
                    </div>
                </div>

                <div class="pt-4 px-2">
                    <h4 class="text-sm font-bold text-slate-900">Ketuntasan Servis Fasilitas</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Tingkat respons dan penanganan teknisi kampus</p>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="flex items-center gap-1.5 text-xs text-slate-400 px-2 pb-1">
                    <Clock class="w-3.5 h-3.5" />
                    <span>just updated</span>
                </div>
            </div>
        </div>

        <!-- 3. Bottom Row: Projects Table & Orders Overview Timeline (Material 2-Column Split) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Projects Card (Signature Material XD Table) -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
                <div>
                    <!-- Card Header -->
                    <div class="flex items-start justify-between mb-5">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Projects</h3>
                            <div class="flex items-center gap-1.5 text-xs text-slate-500 mt-0.5">
                                <Check class="w-4 h-4 text-emerald-500 stroke-[3]" />
                                <span><strong class="text-slate-800">30 done</strong> this month</span>
                            </div>
                        </div>
                        <Link :href="route('loans.index')" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                            <MoreVertical class="w-5 h-5" />
                        </Link>
                    </div>

                    <!-- Material Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                    <th class="pb-3 pr-4">COMPANIES</th>
                                    <th class="pb-3 px-4">MEMBERS</th>
                                    <th class="pb-3 px-4">BUDGET</th>
                                    <th class="pb-3 pl-4">COMPLETION</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr 
                                    v-for="row in projectRows" 
                                    :key="row.id" 
                                    class="hover:bg-slate-50/70 transition"
                                >
                                    <!-- Companies Column with icon chip -->
                                    <td class="py-3.5 pr-4">
                                        <div class="flex items-center gap-3">
                                            <div 
                                                :class="[
                                                    'w-9 h-9 rounded-xl flex items-center justify-center font-black text-xs text-white shadow-xs shrink-0',
                                                    row.avatarBg
                                                ]"
                                            >
                                                {{ row.initials }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-slate-800 text-xs">{{ row.title }}</div>
                                                <div class="text-[11px] text-slate-400 truncate max-w-xs">
                                                    {{ row.company }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Stacked Member Avatars -->
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-center -space-x-2">
                                            <div 
                                                v-for="(member, mIdx) in row.members" 
                                                :key="mIdx"
                                                :class="[
                                                    'w-6 h-6 rounded-full text-white flex items-center justify-center text-[10px] font-bold border-2 border-white shadow-xs',
                                                    mIdx === 0 ? 'bg-slate-800' :
                                                    mIdx === 1 ? 'bg-blue-600' : 'bg-emerald-600'
                                                ]"
                                            >
                                                {{ member }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Budget -->
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-800">
                                            {{ row.budget }}
                                        </div>
                                    </td>

                                    <!-- Progress Completion Bar -->
                                    <td class="py-3.5 pl-4">
                                        <div class="space-y-1 max-w-[120px]">
                                            <div class="text-[11px] font-bold text-slate-700">
                                                {{ row.progress }}%
                                            </div>
                                            <div class="w-full bg-slate-100 rounded-full h-1 overflow-hidden">
                                                <div 
                                                    :class="['h-full rounded-full transition-all', row.progressColor]"
                                                    :style="{ width: `${row.progress}%` }"
                                                ></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Sirkulasi Inventaris & Logistik</span>
                    <Link :href="route('loans.index')" class="font-bold text-slate-900 hover:text-blue-700 flex items-center gap-1">
                        Buka Seluruh Data
                        <ArrowUpRight class="w-3.5 h-3.5" />
                    </Link>
                </div>
            </div>

            <!-- Right 1 Col: Orders Overview Timeline Card -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 flex flex-col justify-between">
                <div>
                    <!-- Card Header -->
                    <div class="mb-6">
                        <h3 class="text-base font-bold text-slate-900">Orders overview</h3>
                        <div class="flex items-center gap-1 text-xs text-slate-500 mt-0.5">
                            <span class="text-emerald-500 font-bold">↑ 24%</span>
                            <span>this month</span>
                        </div>
                    </div>

                    <!-- Vertical Timeline -->
                    <div class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                        <!-- Timeline Item 1 -->
                        <div class="relative group">
                            <div class="absolute -left-6 top-0.5 w-5 h-5 rounded-full bg-emerald-500 text-white flex items-center justify-center shadow-xs">
                                <Bell class="w-3 h-3 stroke-[2.5]" />
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">
                                    $2400, Design changes
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5 font-medium">
                                    22 DEC 7:20 PM
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Item 2 -->
                        <div class="relative group">
                            <div class="absolute -left-6 top-0.5 w-5 h-5 rounded-full bg-rose-500 text-white flex items-center justify-center shadow-xs">
                                <Wrench class="w-3 h-3 stroke-[2.5]" />
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">
                                    New order #1832412
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5 font-medium">
                                    21 DEC 11:21 PM
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Item 3 -->
                        <div class="relative group">
                            <div class="absolute -left-6 top-0.5 w-5 h-5 rounded-full bg-blue-500 text-white flex items-center justify-center shadow-xs">
                                <ShoppingBag class="w-3 h-3 stroke-[2.5]" />
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">
                                    Server payments for April
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5 font-medium">
                                    21 DEC 9:34 PM
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Item 4 -->
                        <div class="relative group">
                            <div class="absolute -left-6 top-0.5 w-5 h-5 rounded-full bg-amber-500 text-white flex items-center justify-center shadow-xs">
                                <Key class="w-3 h-3 stroke-[2.5]" />
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">
                                    New card added for order #4395132
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5 font-medium">
                                    20 DEC 2:20 AM
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Item 5 -->
                        <div class="relative group">
                            <div class="absolute -left-6 top-0.5 w-5 h-5 rounded-full bg-indigo-500 text-white flex items-center justify-center shadow-xs">
                                <DoorOpen class="w-3 h-3 stroke-[2.5]" />
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">
                                    Unlock packages for dev
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5 font-medium">
                                    18 DEC 4:54 AM
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Item 6 -->
                        <div class="relative group">
                            <div class="absolute -left-6 top-0.5 w-5 h-5 rounded-full bg-slate-900 text-white flex items-center justify-center shadow-xs">
                                <Check class="w-3 h-3 stroke-[2.5]" />
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">
                                    New order #958312
                                </div>
                                <div class="text-[11px] text-slate-400 mt-0.5 font-medium">
                                    17 DEC
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-3 border-t border-slate-100 text-center">
                    <span class="text-[11px] text-slate-400">
                        Log Aktivitas Sarpras Otomatis
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
