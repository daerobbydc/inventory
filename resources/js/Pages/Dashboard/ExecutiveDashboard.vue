<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { 
    Building2, 
    Layers, 
    CheckCircle2, 
    Clock, 
    TrendingUp,
    Repeat,
    Calendar,
    AlertTriangle,
    Wrench,
    Boxes,
    DoorOpen,
    Eye,
    Tag,
    ChevronRight,
    ArrowUpRight,
    AlertCircle
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
    pendingBookings: {
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
    weeklyActivity: {
        type: Array,
        default: () => [],
    },
    serviceTrend: {
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
        return 'Rp ' + (num / 1000000000).toFixed(1).replace('.0', '') + ' Miliar';
    }
    if (num >= 1000000) {
        return 'Rp ' + (num / 1000000).toFixed(1).replace('.0', '') + ' Juta';
    }
    return formatRupiah(num);
};

// Active Tab in the Tasks / Operational Card
const activeTaskTab = ref('loans');

const getLoanBadge = (status) => {
    const val = typeof status === 'object' ? status?.value : status;
    switch (val) {
        case 'borrowed':
            return { label: 'Sedang Dipakai', class: 'bg-blue-50 text-blue-700 border-blue-200' };
        case 'approved':
            return { label: 'Siap Diambil', class: 'bg-emerald-50 text-emerald-700 border-emerald-200' };
        case 'pending':
            return { label: 'Menunggu Review', class: 'bg-amber-50 text-amber-700 border-amber-200' };
        case 'returned':
            return { label: 'Telah Kembali', class: 'bg-slate-100 text-slate-700 border-slate-200' };
        default:
            return { label: val || 'Tercatat', class: 'bg-slate-50 text-slate-600 border-slate-200' };
    }
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

// --- Chart 1: Dynamic Sebaran Sarana per Fakultas (Pink Bar Chart) ---
const maxAssetInFaculty = computed(() => {
    const list = props.distributionByFaculty || [];
    if (list.length === 0) return 5;
    const max = Math.max(...list.map(f => f.assets_count ?? f.items ?? 0));
    return max > 0 ? max : 5;
});

const facultyBars = computed(() => {
    const list = props.distributionByFaculty || [];
    if (list.length === 0) {
        return [
            { id: 1, code: 'FT', assets_count: 0, x: 55, y: 120, width: 20, height: 5 },
            { id: 2, code: 'FEB', assets_count: 0, x: 110, y: 120, width: 20, height: 5 },
            { id: 3, code: 'FAI', assets_count: 0, x: 165, y: 120, width: 20, height: 5 },
            { id: 4, code: 'FIK', assets_count: 0, x: 220, y: 120, width: 20, height: 5 },
            { id: 5, code: 'REK', assets_count: 0, x: 275, y: 120, width: 20, height: 5 },
        ];
    }
    const count = list.length;
    const barWidth = 20;
    const startX = 45;
    const availableWidth = 250;
    const step = count > 1 ? availableWidth / (count - 1) : 0;
    const maxVal = maxAssetInFaculty.value;
    const maxHeight = 85;
    const baseY = 125;

    return list.map((item, i) => {
        const val = item.assets_count ?? item.items ?? 0;
        const height = val > 0 ? Math.max(10, Math.round((val / maxVal) * maxHeight)) : 5;
        const x = count > 1 ? Math.round(startX + (i * step) - (barWidth / 2)) : 150;
        const y = baseY - height;

        return {
            ...item,
            x,
            y,
            width: barWidth,
            height,
            val,
        };
    });
});

// --- Chart 2: Dynamic Aktivitas Peminjaman & Okupansi (Green Line Chart) ---
const maxWeeklyActivity = computed(() => {
    const list = props.weeklyActivity || [];
    if (list.length === 0) return 5;
    const max = Math.max(...list.map(d => d.total || 0));
    return max > 0 ? max : 5;
});

const totalWeeklySessions = computed(() => {
    const list = props.weeklyActivity || [];
    return list.reduce((acc, curr) => acc + (curr.total || 0), 0);
});

const weeklyPoints = computed(() => {
    const list = props.weeklyActivity || [];
    const dayFallbacks = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
    const activeList = list.length === 7 ? list : dayFallbacks.map((d, i) => ({ day: d, total: 0 }));
    
    const startX = 40;
    const stepX = 43;
    const baseY = 125;
    const maxHeight = 85;
    const maxVal = maxWeeklyActivity.value;

    return activeList.map((item, i) => {
        const val = item.total || 0;
        const height = val > 0 ? Math.round((val / maxVal) * maxHeight) : 0;
        const x = startX + (i * stepX);
        const y = baseY - height;
        return {
            ...item,
            x,
            y,
            val,
        };
    });
});

const weeklyPathD = computed(() => {
    const pts = weeklyPoints.value;
    if (pts.length === 0) return '';
    return pts.reduce((acc, pt, i) => {
        if (i === 0) return `M ${pt.x} ${pt.y}`;
        const prev = pts[i - 1];
        const cp1x = prev.x + (pt.x - prev.x) / 2;
        const cp1y = prev.y;
        const cp2x = prev.x + (pt.x - prev.x) / 2;
        const cp2y = pt.y;
        return `${acc} C ${cp1x} ${cp1y}, ${cp2x} ${cp2y}, ${pt.x} ${pt.y}`;
    }, '');
});

// --- Chart 3: Dynamic Ketuntasan Servis Fasilitas (Cyan Peak Wave Chart) ---
const maxServiceTrend = computed(() => {
    const list = props.serviceTrend || [];
    if (list.length === 0) return 5;
    const max = Math.max(...list.map(d => Math.max(d.completed || 0, d.incoming || 0)));
    return max > 0 ? max : 5;
});

const servicePoints = computed(() => {
    const list = props.serviceTrend || [];
    const fallbackLabels = ['H-6', 'H-5', 'H-4', 'H-3', 'H-2', 'H-1', 'Hari Ini'];
    const activeList = list.length === 7 ? list : fallbackLabels.map(l => ({ label: l, completed: 0, incoming: 0 }));

    const startX = 40;
    const stepX = 43;
    const baseY = 125;
    const maxHeight = 85;
    const maxVal = maxServiceTrend.value;

    return activeList.map((item, i) => {
        const val = (item.completed || 0) + (item.incoming || 0);
        const height = val > 0 ? Math.round((val / maxVal) * maxHeight) : 0;
        const x = startX + (i * stepX);
        const y = baseY - height;
        return {
            ...item,
            x,
            y,
            val,
        };
    });
});

const servicePathD = computed(() => {
    const pts = servicePoints.value;
    if (pts.length === 0) return '';
    return pts.reduce((acc, pt, i) => {
        return i === 0 ? `M ${pt.x} ${pt.y}` : `${acc} L ${pt.x} ${pt.y}`;
    }, '');
});
</script>

<template>
    <div class="space-y-8 pt-2">
        <!-- 1. Four Overhanging Metric Cards (Creative Tim Material Style with Real University Sarpras Data) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 pt-3">
            <!-- Card 1: Valuasi Total Aset (Cyan / Sky Blue Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-xl bg-gradient-to-tr from-[#00bcd4] to-[#26c6da] shadow-lg shadow-cyan-500/40 flex items-center justify-center text-white">
                    <Building2 class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-slate-400">Valuasi Total Aset</span>
                    <div class="text-2xl font-black text-slate-800 mt-0.5 tracking-tight">
                        {{ formatShortValuation(metrics.totalValuation) }}
                    </div>
                    <div class="text-[10px] text-slate-400">
                        {{ formatRupiah(metrics.totalValuation) }}
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-slate-400 flex items-center gap-1.5">
                    <Clock class="w-3.5 h-3.5 text-cyan-600" />
                    <span>Akumulasi nilai inventaris aktif</span>
                </div>
            </div>

            <!-- Card 2: Total Unit Aset (Hot Pink / Magenta Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-xl bg-gradient-to-tr from-[#e91e63] to-[#ec407a] shadow-lg shadow-pink-500/40 flex items-center justify-center text-white">
                    <Layers class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-slate-400">Total Unit Terdaftar</span>
                    <div class="text-2xl font-black text-slate-800 mt-0.5 tracking-tight">
                        {{ Number(metrics.totalAssets || 0).toLocaleString('id-ID') }} Unit
                    </div>
                    <div class="text-[10px] text-slate-400">
                        Terdata dengan Token Barcode QR
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-slate-400 flex items-center gap-1.5">
                    <Tag class="w-3.5 h-3.5 text-pink-600" />
                    <span>Tersebar di {{ distributionByFaculty?.length || 5 }} Fakultas & Unit</span>
                </div>
            </div>

            <!-- Card 3: Aset Kondisi Prima (Emerald Green Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-xl bg-gradient-to-tr from-[#4caf50] to-[#66bb6a] shadow-lg shadow-emerald-500/40 flex items-center justify-center text-white">
                    <CheckCircle2 class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-slate-400">Unit Kondisi Prima</span>
                    <div class="text-2xl font-black text-slate-800 mt-0.5 tracking-tight">
                        {{ Number(conditionCounts?.good || 0).toLocaleString('id-ID') }} Unit
                    </div>
                    <div class="text-[10px] text-emerald-600 font-bold">
                        {{ metrics.totalAssets > 0 ? Math.round((conditionCounts.good / metrics.totalAssets) * 100) : 100 }}% Laik Operasional
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-slate-400 flex items-center gap-1.5">
                    <Calendar class="w-3.5 h-3.5 text-emerald-600" />
                    <span>{{ Number(conditionCounts?.light_damage || 0) + Number(conditionCounts?.heavy_damage || 0) }} unit butuh pemeliharaan</span>
                </div>
            </div>

            <!-- Card 4: Peminjaman & Sirkulasi (Orange / Amber Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-xl bg-gradient-to-tr from-[#ffa726] to-[#fb8c00] shadow-lg shadow-orange-500/40 flex items-center justify-center text-white">
                    <Repeat class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-slate-400">Peminjaman Aktif</span>
                    <div class="text-2xl font-black text-slate-800 mt-0.5 tracking-tight">
                        {{ Number(metrics.activeLoans || 0).toLocaleString('id-ID') }} Sesi
                    </div>
                    <div class="text-[10px] text-slate-400">
                        {{ pendingLoans?.length || 0 }} usulan menunggu review
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-amber-600 font-semibold flex items-center gap-1.5">
                    <AlertTriangle class="w-3.5 h-3.5" />
                    <span>{{ metrics.activeTickets || 0 }} tiket servis dalam pengerjaan</span>
                </div>
            </div>
        </div>

        <!-- 2. Three Overhanging Visual Chart Cards (Sebaran Fakultas, Utilisasi Ruang, Ketuntasan Servis) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-2">
            <!-- Chart Card 1: Sebaran Sarana per Fakultas (Pink Gradient Dynamic Bar Chart) -->
            <div class="bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-6">
                <div class="relative -mt-6 mx-2 rounded-xl p-4 text-white shadow-lg bg-gradient-to-tr from-[#e91e63] to-[#f06292] shadow-pink-500/35">
                    <div class="h-44 flex flex-col justify-between">
                        <!-- Dynamic SVG Bar Chart per Fakultas -->
                        <svg class="w-full h-full overflow-visible" viewBox="0 0 320 150">
                            <!-- Dashed Horizontal Guide Lines -->
                            <line x1="25" y1="20" x2="310" y2="20" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="55" x2="310" y2="55" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="90" x2="310" y2="90" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="125" x2="310" y2="125" stroke="rgba(255,255,255,0.4)" stroke-width="1" />

                            <!-- Y-Axis Labels -->
                            <text x="20" y="24" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">{{ maxAssetInFaculty }}</text>
                            <text x="20" y="59" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">{{ Math.round(maxAssetInFaculty * 0.66) }}</text>
                            <text x="20" y="94" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">{{ Math.round(maxAssetInFaculty * 0.33) }}</text>
                            <text x="20" y="128" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">0</text>

                            <!-- Dynamic Bars per faculty -->
                            <g v-for="(fac, idx) in facultyBars" :key="fac.id || idx">
                                <rect 
                                    :x="fac.x" 
                                    :y="fac.y" 
                                    :width="fac.width" 
                                    :height="fac.height" 
                                    rx="4" 
                                    fill="#ffffff" 
                                    :fill-opacity="fac.val > 0 ? 0.95 : 0.35"
                                />
                                <!-- Value above bar -->
                                <text 
                                    v-if="fac.val > 0"
                                    :x="fac.x + (fac.width / 2)" 
                                    :y="fac.y - 4" 
                                    fill="#ffffff" 
                                    font-size="8" 
                                    font-weight="bold" 
                                    text-anchor="middle"
                                >
                                    {{ fac.val }}
                                </text>
                                <!-- X-Axis Faculty Code -->
                                <text 
                                    :x="fac.x + (fac.width / 2)" 
                                    y="142" 
                                    fill="rgba(255,255,255,0.95)" 
                                    font-size="9" 
                                    font-weight="bold" 
                                    text-anchor="middle"
                                >
                                    {{ fac.code }}
                                </text>
                            </g>
                        </svg>
                    </div>
                </div>

                <div class="pt-4 px-2">
                    <h4 class="text-sm font-bold text-slate-800">Sebaran Sarana per Fakultas</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Proporsi inventaris dan alat praktikum tiap fakultas</p>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="flex items-center gap-1.5 text-xs text-slate-400 px-2 pb-1">
                    <Clock class="w-3.5 h-3.5" />
                    <span>Terdata di {{ distributionByFaculty?.length || 0 }} Fakultas & Unit (Total: {{ metrics.totalAssets || 0 }} Unit)</span>
                </div>
            </div>

            <!-- Chart Card 2: Aktivitas Peminjaman & Okupansi (Green Emerald Dynamic Smooth Line Chart) -->
            <div class="bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-6">
                <div class="relative -mt-6 mx-2 rounded-xl p-4 text-white shadow-lg bg-gradient-to-tr from-[#4caf50] to-[#66bb6a] shadow-emerald-500/35">
                    <div class="h-44 flex flex-col justify-between">
                        <!-- SVG Smooth Line Chart across academic weekdays (Sen - Min) -->
                        <svg class="w-full h-full overflow-visible" viewBox="0 0 320 150">
                            <!-- Dashed Horizontal Guide Lines -->
                            <line x1="25" y1="20" x2="310" y2="20" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="55" x2="310" y2="55" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="90" x2="310" y2="90" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="125" x2="310" y2="125" stroke="rgba(255,255,255,0.4)" stroke-width="1" />

                            <!-- Y-Axis Labels -->
                            <text x="20" y="24" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">{{ maxWeeklyActivity }}</text>
                            <text x="20" y="59" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">{{ Math.round(maxWeeklyActivity * 0.66) }}</text>
                            <text x="20" y="94" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">{{ Math.round(maxWeeklyActivity * 0.33) }}</text>
                            <text x="20" y="128" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">0</text>

                            <!-- Smooth Wave Path -->
                            <path 
                                v-if="weeklyPathD"
                                :d="weeklyPathD" 
                                fill="none" 
                                stroke="#ffffff" 
                                stroke-width="2.5" 
                                stroke-linecap="round" 
                                stroke-linejoin="round"
                            />

                            <!-- Data Points -->
                            <g v-for="pt in weeklyPoints" :key="pt.day">
                                <circle :cx="pt.x" :cy="pt.y" r="3" fill="#ffffff" />
                                <text 
                                    v-if="pt.val > 0"
                                    :x="pt.x" 
                                    :y="pt.y - 6" 
                                    fill="#ffffff" 
                                    font-size="8" 
                                    font-weight="bold" 
                                    text-anchor="middle"
                                >
                                    {{ pt.val }}
                                </text>
                                <text :x="pt.x" y="142" fill="rgba(255,255,255,0.9)" font-size="8" font-weight="bold" text-anchor="middle">
                                    {{ pt.day }}
                                </text>
                            </g>
                        </svg>
                    </div>
                </div>

                <div class="pt-4 px-2">
                    <h4 class="text-sm font-bold text-slate-800">Aktivitas Peminjaman & Okupansi</h4>
                    <p class="text-xs text-slate-400 mt-0.5">
                        <span class="text-emerald-500 font-bold inline-flex items-center">
                            <TrendingUp class="w-3.5 h-3.5 mr-0.5" /> {{ totalWeeklySessions }} Sesi
                        </span>
                        reservasi ruangan & peminjaman alat minggu ini
                    </p>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="flex items-center gap-1.5 text-xs text-slate-400 px-2 pb-1">
                    <Clock class="w-3.5 h-3.5" />
                    <span>Terhubung langsung dengan jadwal perkuliahan & kegiatan kampus</span>
                </div>
            </div>

            <!-- Chart Card 3: Ketuntasan Servis Fasilitas (Cyan Sky Blue Gradient Dynamic Peak Wave Chart) -->
            <div class="bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-6">
                <div class="relative -mt-6 mx-2 rounded-xl p-4 text-white shadow-lg bg-gradient-to-tr from-[#00bcd4] to-[#26c6da] shadow-cyan-500/35">
                    <div class="h-44 flex flex-col justify-between">
                        <!-- SVG Peak Wave Line Chart across Days H-6 s.d Hari Ini -->
                        <svg class="w-full h-full overflow-visible" viewBox="0 0 320 150">
                            <!-- Dashed Horizontal Guide Lines -->
                            <line x1="25" y1="20" x2="310" y2="20" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="55" x2="310" y2="55" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="90" x2="310" y2="90" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="125" x2="310" y2="125" stroke="rgba(255,255,255,0.4)" stroke-width="1" />

                            <!-- Y-Axis Labels -->
                            <text x="20" y="24" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">{{ maxServiceTrend }}</text>
                            <text x="20" y="128" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">0</text>

                            <!-- Peak Line Path -->
                            <path 
                                v-if="servicePathD"
                                :d="servicePathD" 
                                fill="none" 
                                stroke="#ffffff" 
                                stroke-width="2.5" 
                                stroke-linecap="round" 
                                stroke-linejoin="round"
                            />

                            <!-- Data Points -->
                            <g v-for="pt in servicePoints" :key="pt.label">
                                <circle :cx="pt.x" :cy="pt.y" r="3" fill="#ffffff" />
                                <text 
                                    v-if="pt.val > 0"
                                    :x="pt.x" 
                                    :y="pt.y - 6" 
                                    fill="#ffffff" 
                                    font-size="8" 
                                    font-weight="bold" 
                                    text-anchor="middle"
                                >
                                    {{ pt.val }}
                                </text>
                                <text :x="pt.x" y="142" fill="rgba(255,255,255,0.85)" font-size="8" font-weight="bold" text-anchor="middle">
                                    {{ pt.label }}
                                </text>
                            </g>
                        </svg>
                    </div>
                </div>

                <div class="pt-4 px-2">
                    <h4 class="text-sm font-bold text-slate-800">Ketuntasan Servis Fasilitas</h4>
                    <p class="text-xs text-slate-400 mt-0.5">
                        <span class="text-cyan-600 font-bold">
                            {{ metrics.activeTickets || 0 }} tiket
                        </span>
                        servis aktif dalam penanganan teknisi sarpras
                    </p>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="flex items-center gap-1.5 text-xs text-slate-400 px-2 pb-1">
                    <Clock class="w-3.5 h-3.5" />
                    <span>Monitoring respon & kecepatan penyelesaian kerusakan</span>
                </div>
            </div>
        </div>

        <!-- 3. Bottom Row: 2 Functional Cards (Left: Sirkulasi Aset | Right: Antrean Layanan & Tugas Sarpras) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-2">
            <!-- Left Card: Sirkulasi Peminjaman Aset Terkini (Overhanging Orange Box Header) -->
            <div class="bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-6 flex flex-col justify-between">
                <div>
                    <!-- Overhanging Orange Box -->
                    <div class="relative -mt-6 mx-2 rounded-xl p-4 text-white shadow-lg bg-gradient-to-tr from-[#ffa726] to-[#fb8c00] shadow-orange-500/35">
                        <h3 class="text-base font-bold text-white leading-tight">Sirkulasi Peminjaman Aset Terkini</h3>
                        <p class="text-xs text-white/80 mt-0.5">Data sirkulasi peminjaman alat & inventaris civitas kampus</p>
                    </div>

                    <!-- Table Content -->
                    <div class="overflow-x-auto pt-4 px-2">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th class="pb-3 text-[#fb8c00]">KODE</th>
                                    <th class="pb-3">PEMOHON & FAKULTAS</th>
                                    <th class="pb-3">FASILITAS / ASET</th>
                                    <th class="pb-3 text-right">STATUS</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr 
                                    v-if="recentCirculations && recentCirculations.length > 0"
                                    v-for="loan in recentCirculations" 
                                    :key="loan.id" 
                                    class="hover:bg-slate-50/70 transition"
                                >
                                    <td class="py-3 font-mono font-bold text-slate-700">
                                        <Link :href="route('loans.show', loan.id)" class="text-orange-600 hover:underline">
                                            {{ loan.transaction_code }}
                                        </Link>
                                    </td>
                                    <td class="py-3">
                                        <div class="font-bold text-slate-800">{{ loan.borrower?.name }}</div>
                                        <div class="text-[10px] text-slate-400">{{ loan.borrower?.department || 'Civitas Akademika' }}</div>
                                    </td>
                                    <td class="py-3">
                                        <div class="font-medium text-slate-700">
                                            {{ loan.loan_items?.[0]?.asset?.name || 'Unit Sarpras' }}
                                        </div>
                                        <div class="text-[10px] text-slate-400">
                                            {{ loan.loan_items?.length || 1 }} Unit • Kembali: {{ formatDate(loan.expected_return_date) }}
                                        </div>
                                    </td>
                                    <td class="py-3 text-right">
                                        <span 
                                            class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border"
                                            :class="getLoanBadge(loan.status).class"
                                        >
                                            {{ getLoanBadge(loan.status).label }}
                                        </span>
                                    </td>
                                </tr>

                                <tr v-else>
                                    <td colspan="4" class="py-8 text-center text-slate-400">
                                        Belum ada data sirkulasi peminjaman.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pt-4 pb-1 px-2 border-t border-slate-100 mt-3 flex items-center justify-between text-xs text-slate-400">
                    <span>Sirkulasi Inventaris & Logistik</span>
                    <Link :href="route('loans.index')" class="font-bold text-orange-600 hover:text-orange-700 flex items-center gap-1">
                        Buka Seluruh Peminjaman
                        <ArrowUpRight class="w-3.5 h-3.5" />
                    </Link>
                </div>
            </div>

            <!-- Right Card: Antrean Layanan & Tugas Sarpras (Overhanging Pink Box with 3 Tabs) -->
            <div class="bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-6 flex flex-col justify-between">
                <div>
                    <!-- Overhanging Pink Box with 3 Tabs -->
                    <div class="relative -mt-6 mx-2 rounded-xl p-3 sm:p-4 text-white shadow-lg bg-gradient-to-tr from-[#ec407a] to-[#d81b60] shadow-pink-500/35 flex flex-wrap items-center justify-between gap-2">
                        <div class="font-bold text-sm sm:text-base">
                            Antrean Layanan:
                        </div>

                        <!-- 3 Functional Tabs (PEMINJAMAN, SERVIS & TIKET, LOGISTIK ATK) -->
                        <div class="flex items-center gap-1 text-xs">
                            <button
                                type="button"
                                @click="activeTaskTab = 'loans'"
                                :class="[
                                    'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold transition',
                                    activeTaskTab === 'loans' ? 'bg-white/25 text-white shadow-xs' : 'text-white/80 hover:bg-white/10'
                                ]"
                            >
                                <Repeat class="w-3.5 h-3.5" />
                                <span>PEMINJAMAN ({{ (pendingLoans?.length || 0) + (pendingBookings?.length || 0) }})</span>
                            </button>

                            <button
                                type="button"
                                @click="activeTaskTab = 'tickets'"
                                :class="[
                                    'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold transition',
                                    activeTaskTab === 'tickets' ? 'bg-white/25 text-white shadow-xs' : 'text-white/80 hover:bg-white/10'
                                ]"
                            >
                                <Wrench class="w-3.5 h-3.5" />
                                <span>SERVIS ({{ urgentTickets?.length || 0 }})</span>
                            </button>

                            <button
                                type="button"
                                @click="activeTaskTab = 'stock'"
                                :class="[
                                    'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold transition',
                                    activeTaskTab === 'stock' ? 'bg-white/25 text-white shadow-xs' : 'text-white/80 hover:bg-white/10'
                                ]"
                            >
                                <Boxes class="w-3.5 h-3.5" />
                                <span>ATK ({{ lowStockItems?.length || 0 }})</span>
                            </button>
                        </div>
                    </div>

                    <!-- Tab 1: Peminjaman Antrean Review -->
                    <div v-if="activeTaskTab === 'loans'" class="pt-3 px-2 divide-y divide-slate-100">
                        <div 
                            v-if="pendingLoans && pendingLoans.length > 0"
                            v-for="loan in pendingLoans" 
                            :key="loan.id" 
                            class="py-3 flex items-center justify-between gap-3 hover:bg-slate-50/70 p-2 rounded-xl transition"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-xs shrink-0">
                                    <Repeat class="w-4 h-4" />
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 truncate">
                                        {{ loan.transaction_code }} • {{ loan.borrower?.name }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 truncate">
                                        {{ loan.purpose || 'Penggunaan sarana perkuliahan/riset' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ loan.loan_items?.[0]?.asset?.name || 'Unit Sarpras' }} ({{ loan.loan_items?.length || 1 }} Unit)
                                    </div>
                                </div>
                            </div>
                            <Link 
                                :href="route('loans.show', loan.id)" 
                                class="px-3 py-1 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold shadow-xs shrink-0 transition"
                            >
                                Review
                            </Link>
                        </div>

                        <!-- Pending Room Bookings -->
                        <div 
                            v-if="pendingBookings && pendingBookings.length > 0"
                            v-for="booking in pendingBookings" 
                            :key="booking.id" 
                            class="py-3 flex items-center justify-between gap-3 hover:bg-slate-50/70 p-2 rounded-xl transition"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1e3a8a] flex items-center justify-center font-bold text-xs shrink-0">
                                    <DoorOpen class="w-4 h-4" />
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 truncate">
                                        {{ booking.booking_code }} • {{ booking.user?.name }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 truncate">
                                        {{ booking.room?.name }} • {{ formatDate(booking.booking_date) }} ({{ booking.start_time }}-{{ booking.end_time }})
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ booking.purpose }}
                                    </div>
                                </div>
                            </div>
                            <Link 
                                :href="route('room-bookings.show', booking.id)" 
                                class="px-3 py-1 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold shadow-xs shrink-0 transition"
                            >
                                Review
                            </Link>
                        </div>

                        <div v-if="(!pendingLoans?.length) && (!pendingBookings?.length)" class="py-8 text-center text-slate-400">
                            <CheckCircle2 class="w-8 h-8 text-emerald-500 mx-auto mb-2" />
                            <div class="text-xs font-semibold text-slate-700">Semua usulan telah disetujui!</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Tidak ada antrean persetujuan peminjaman alat atau ruangan.</div>
                        </div>
                    </div>

                    <!-- Tab 2: Tiket Servis Mendesak -->
                    <div v-else-if="activeTaskTab === 'tickets'" class="pt-3 px-2 divide-y divide-slate-100">
                        <div 
                            v-if="urgentTickets && urgentTickets.length > 0"
                            v-for="ticket in urgentTickets" 
                            :key="ticket.id" 
                            class="py-3 flex items-center justify-between gap-3 hover:bg-slate-50/70 p-2 rounded-xl transition"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <div 
                                    :class="[
                                        'w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0',
                                        ticket.priority === 'critical' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700'
                                    ]"
                                >
                                    <Wrench class="w-4 h-4" />
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 truncate">
                                        {{ ticket.ticket_code }} • {{ ticket.asset?.name }}
                                    </div>
                                    <div class="text-[11px] text-slate-500 truncate">
                                        {{ ticket.issue_description }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        Teknisi: {{ ticket.technician?.name || 'Belum ditugaskan' }}
                                    </div>
                                </div>
                            </div>
                            <Link 
                                :href="route('maintenance.show', ticket.id)" 
                                class="px-3 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white text-[11px] font-bold shadow-xs shrink-0 transition"
                            >
                                Detail
                            </Link>
                        </div>

                        <div v-else class="py-8 text-center text-slate-400">
                            <CheckCircle2 class="w-8 h-8 text-emerald-500 mx-auto mb-2" />
                            <div class="text-xs font-semibold text-slate-700">Tidak ada tiket servis mendesak</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Seluruh laporan pemeliharaan telah ditangani teknisi.</div>
                        </div>
                    </div>

                    <!-- Tab 3: Stok Logistik ATK Kritis -->
                    <div v-else-if="activeTaskTab === 'stock'" class="pt-3 px-2 divide-y divide-slate-100">
                        <div 
                            v-if="lowStockItems && lowStockItems.length > 0"
                            v-for="item in lowStockItems" 
                            :key="item.id" 
                            class="py-3 flex items-center justify-between gap-3 hover:bg-slate-50/70 p-2 rounded-xl transition"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center font-bold text-xs shrink-0">
                                    <AlertCircle class="w-4 h-4" />
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-slate-800 truncate">
                                        {{ item.name }}
                                    </div>
                                    <div class="text-[11px] text-rose-600 font-semibold">
                                        Sisa: {{ item.current_stock }} {{ item.unit }} (Batas Min: {{ item.minimum_stock }})
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        Kategori: {{ item.category?.name || 'ATK / Habis Pakai' }}
                                    </div>
                                </div>
                            </div>
                            <Link 
                                :href="route('consumables.index')" 
                                class="px-3 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold shadow-xs shrink-0 transition"
                            >
                                Restock
                            </Link>
                        </div>

                        <div v-else class="py-8 text-center text-slate-400">
                            <CheckCircle2 class="w-8 h-8 text-emerald-500 mx-auto mb-2" />
                            <div class="text-xs font-semibold text-slate-700">Stok logistik aman</div>
                            <div class="text-[11px] text-slate-400 mt-0.5">Semua barang habis pakai berada di atas kuota minimum.</div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 pb-1 px-2 border-t border-slate-100 mt-2 flex items-center justify-between text-xs text-slate-400">
                    <span>Operasional Fasilitas & Logistik</span>
                    <Link :href="route('maintenance.index')" class="text-pink-600 font-bold hover:underline">
                        Kelola Seluruh Tugas &rarr;
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
