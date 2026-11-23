<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { 
    Building2, 
    Layers, 
    CheckCircle2, 
    Clock, 
    TrendingUp,
    Repeat,
    BarChart3,
    Twitter,
    Store,
    Tag,
    Calendar,
    AlertTriangle,
    Bug,
    Code,
    Cloud,
    Pencil,
    X,
    Check
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

// Active Tab in Tasks Card
const activeTaskTab = ref('website');

// Interactive Checklist Tasks
const tasks = ref([
    {
        id: 1,
        title: 'Sign contract for "What are conference organizers afraid of?"',
        subtitle: 'Persetujuan Peminjaman Proyektor R. Kuliah Bersama FT',
        completed: true,
    },
    {
        id: 2,
        title: 'Lines From Great Russian Literature? Or E-mails From My Boss?',
        subtitle: 'Pemeriksaan Berkala Filter AC Lab Jaringan Gedung C',
        completed: false,
    },
    {
        id: 3,
        title: 'Flooded: One year later, assessing what was lost and what was found',
        subtitle: 'Verifikasi Sensus Barcode QR 32 Aset Gedung Rektorat',
        completed: false,
    },
    {
        id: 4,
        title: 'Create 4 Invisible User Experiences you Never Knew About',
        subtitle: 'Pengajuan Usulan Pengadaan 5 PC Workstation FT',
        completed: true,
    },
]);

const toggleTask = (index) => {
    tasks.value[index].completed = !tasks.value[index].completed;
};

const removeTask = (index) => {
    tasks.value.splice(index, 1);
};

// Employees Stats Table Data matching Creative Tim Spec
const employees = ref([
    {
        id: 1,
        name: "Noelia O'Kon",
        faculty: 'Niger',
        role: 'Mahasiswa TI',
        salary: '13098.00',
        asset: 'Sony Alpha A7 IV',
    },
    {
        id: 2,
        name: 'Mr. Enid Von PhD',
        faculty: 'Curaçao',
        role: 'Dosen Teknik',
        salary: '35978.00',
        asset: 'Proyektor Epson EB-X500',
    },
    {
        id: 3,
        name: 'Colton',
        faculty: 'Netherlands',
        role: 'Admin Fakultas',
        salary: '28650.00',
        asset: 'Server CBT All-in-One',
    },
    {
        id: 4,
        name: 'Dakota',
        faculty: 'Indonesia',
        role: 'Laboran Multimedia',
        salary: '37800.00',
        asset: 'MikroTik Cloud Router',
    },
]);
</script>

<template>
    <div class="space-y-8 pt-2">
        <!-- 1. Four Overhanging Metric Cards (Creative Tim Material Vue Theme) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 pt-3">
            <!-- Card 1: Followers (Cyan / Sky Blue Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-xl bg-gradient-to-tr from-[#00bcd4] to-[#26c6da] shadow-lg shadow-cyan-500/40 flex items-center justify-center text-white">
                    <Twitter class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-slate-400">Followers</span>
                    <div class="text-2xl font-black text-slate-800 mt-0.5 tracking-tight">
                        +245
                    </div>
                    <div class="text-[10px] text-slate-400">
                        Valuasi: {{ formatShortValuation(metrics.totalValuation) }}
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-slate-400 flex items-center gap-1.5">
                    <Clock class="w-3.5 h-3.5" />
                    <span>Just Updated</span>
                </div>
            </div>

            <!-- Card 2: Website Visits (Hot Pink / Magenta Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-xl bg-gradient-to-tr from-[#e91e63] to-[#ec407a] shadow-lg shadow-pink-500/40 flex items-center justify-center text-white">
                    <BarChart3 class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-slate-400">Website Visits</span>
                    <div class="text-2xl font-black text-slate-800 mt-0.5 tracking-tight">
                        75.521
                    </div>
                    <div class="text-[10px] text-slate-400">
                        Total: {{ Number(metrics.totalAssets || 0).toLocaleString('id-ID') }} Aset Kampus
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-slate-400 flex items-center gap-1.5">
                    <Tag class="w-3.5 h-3.5" />
                    <span>Tracked from Google Analytics</span>
                </div>
            </div>

            <!-- Card 3: Revenue (Emerald Green Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-xl bg-gradient-to-tr from-[#4caf50] to-[#66bb6a] shadow-lg shadow-emerald-500/40 flex items-center justify-center text-white">
                    <Store class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-slate-400">Revenue</span>
                    <div class="text-2xl font-black text-slate-800 mt-0.5 tracking-tight">
                        $ 34.245
                    </div>
                    <div class="text-[10px] text-slate-400">
                        {{ Number(conditionCounts?.good || 0).toLocaleString('id-ID') }} Unit Kondisi Prima
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-slate-400 flex items-center gap-1.5">
                    <Calendar class="w-3.5 h-3.5" />
                    <span>Last 24 Hours</span>
                </div>
            </div>

            <!-- Card 4: Bookings (Orange / Amber Gradient) -->
            <div class="relative bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-4">
                <div class="absolute -top-4 left-4 w-14 h-14 rounded-xl bg-gradient-to-tr from-[#ffa726] to-[#fb8c00] shadow-lg shadow-orange-500/40 flex items-center justify-center text-white">
                    <Building2 class="w-6 h-6 stroke-[2]" />
                </div>
                <div class="pt-4 text-right">
                    <span class="text-xs font-semibold text-slate-400">Bookings</span>
                    <div class="text-2xl font-black text-slate-800 mt-0.5 tracking-tight">
                        184
                    </div>
                    <div class="text-[10px] text-slate-400">
                        {{ Number(metrics.activeLoans || 0).toLocaleString('id-ID') }} Sesi Peminjaman
                    </div>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="text-xs text-rose-500 font-semibold flex items-center gap-1.5">
                    <AlertTriangle class="w-3.5 h-3.5" />
                    <span>Get More Space...</span>
                </div>
            </div>
        </div>

        <!-- 2. Three Overhanging Visual Chart Cards (Creative Tim Exact 3 Visual Charts) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pt-2">
            <!-- Chart Card 1: Website Views (Pink / Rose Gradient Bar Chart) -->
            <div class="bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-6">
                <!-- Overhanging Negative-Margin Card Box -->
                <div class="relative -mt-6 mx-2 rounded-xl p-4 text-white shadow-lg bg-gradient-to-tr from-[#e91e63] to-[#f06292] shadow-pink-500/35">
                    <div class="h-44 flex flex-col justify-between">
                        <!-- SVG Bar Chart with 12 Month Bars -->
                        <svg class="w-full h-full overflow-visible" viewBox="0 0 320 150">
                            <!-- Dashed Horizontal Guide Lines -->
                            <line x1="25" y1="15" x2="310" y2="15" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="42" x2="310" y2="42" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="70" x2="310" y2="70" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="98" x2="310" y2="98" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="125" x2="310" y2="125" stroke="rgba(255,255,255,0.4)" stroke-width="1" />

                            <!-- Y-Axis Labels -->
                            <text x="20" y="19" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">800</text>
                            <text x="20" y="46" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">600</text>
                            <text x="20" y="74" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">400</text>
                            <text x="20" y="102" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">200</text>
                            <text x="20" y="128" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">0</text>

                            <!-- Vertical Bars (12 Months: Ja, Fe, Ma, Ap, Mai, Ju, Jul, Au, Se, Oc, No, De) -->
                            <rect x="36" y="80" width="5" height="45" rx="2.5" fill="#ffffff" />
                            <rect x="60" y="60" width="5" height="65" rx="2.5" fill="#ffffff" />
                            <rect x="84" y="95" width="5" height="30" rx="2.5" fill="#ffffff" />
                            <rect x="108" y="70" width="5" height="55" rx="2.5" fill="#ffffff" />
                            <rect x="132" y="85" width="5" height="40" rx="2.5" fill="#ffffff" />
                            <rect x="156" y="65" width="5" height="60" rx="2.5" fill="#ffffff" />
                            <rect x="180" y="90" width="5" height="35" rx="2.5" fill="#ffffff" />
                            <rect x="204" y="55" width="5" height="70" rx="2.5" fill="#ffffff" />
                            <rect x="228" y="75" width="5" height="50" rx="2.5" fill="#ffffff" />
                            <rect x="252" y="40" width="5" height="85" rx="2.5" fill="#ffffff" />
                            <rect x="276" y="70" width="5" height="55" rx="2.5" fill="#ffffff" />
                            <rect x="300" y="30" width="5" height="95" rx="2.5" fill="#ffffff" />

                            <!-- X-Axis Labels -->
                            <text x="38" y="140" fill="rgba(255,255,255,0.85)" font-size="7.5" text-anchor="middle">Ja</text>
                            <text x="62" y="140" fill="rgba(255,255,255,0.85)" font-size="7.5" text-anchor="middle">Fe</text>
                            <text x="86" y="140" fill="rgba(255,255,255,0.85)" font-size="7.5" text-anchor="middle">Ma</text>
                            <text x="110" y="140" fill="rgba(255,255,255,0.85)" font-size="7.5" text-anchor="middle">Ap</text>
                            <text x="134" y="140" fill="rgba(255,255,255,0.85)" font-size="7.5" text-anchor="middle">Mai</text>
                            <text x="158" y="140" fill="rgba(255,255,255,0.85)" font-size="7.5" text-anchor="middle">Ju</text>
                            <text x="182" y="140" fill="rgba(255,255,255,0.85)" font-size="7.5" text-anchor="middle">Jul</text>
                            <text x="206" y="140" fill="rgba(255,255,255,0.85)" font-size="7.5" text-anchor="middle">Au</text>
                            <text x="230" y="140" fill="rgba(255,255,255,0.85)" font-size="7.5" text-anchor="middle">Se</text>
                            <text x="254" y="140" fill="rgba(255,255,255,0.85)" font-size="7.5" text-anchor="middle">Oc</text>
                            <text x="278" y="140" fill="rgba(255,255,255,0.85)" font-size="7.5" text-anchor="middle">No</text>
                            <text x="302" y="140" fill="rgba(255,255,255,0.85)" font-size="7.5" text-anchor="middle">De</text>
                        </svg>
                    </div>
                </div>

                <div class="pt-4 px-2">
                    <h4 class="text-sm font-bold text-slate-800">Website Views</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Last Campaign Performance</p>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="flex items-center gap-1.5 text-xs text-slate-400 px-2 pb-1">
                    <Clock class="w-3.5 h-3.5" />
                    <span>updated 10 days ago</span>
                </div>
            </div>

            <!-- Chart Card 2: Daily Sales (Emerald Green Gradient Line Chart) -->
            <div class="bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-6">
                <!-- Overhanging Negative-Margin Card Box -->
                <div class="relative -mt-6 mx-2 rounded-xl p-4 text-white shadow-lg bg-gradient-to-tr from-[#4caf50] to-[#66bb6a] shadow-emerald-500/35">
                    <div class="h-44 flex flex-col justify-between">
                        <!-- SVG Smooth Line Chart with Days M T W T F S S -->
                        <svg class="w-full h-full overflow-visible" viewBox="0 0 320 150">
                            <!-- Dashed Horizontal Guide Lines -->
                            <line x1="25" y1="18" x2="310" y2="18" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="45" x2="310" y2="45" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="72" x2="310" y2="72" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="99" x2="310" y2="99" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="125" x2="310" y2="125" stroke="rgba(255,255,255,0.4)" stroke-width="1" />

                            <!-- Y-Axis Labels -->
                            <text x="20" y="22" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">40</text>
                            <text x="20" y="49" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">30</text>
                            <text x="20" y="76" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">20</text>
                            <text x="20" y="103" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">10</text>
                            <text x="20" y="128" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">0</text>

                            <!-- Smooth Wave Path -->
                            <path 
                                d="M 45 110 C 70 100, 95 120, 120 90 C 145 60, 170 75, 195 55 C 220 35, 245 80, 270 60 C 285 45, 295 50, 305 40" 
                                fill="none" 
                                stroke="#ffffff" 
                                stroke-width="2.5" 
                                stroke-linecap="round" 
                                stroke-linejoin="round"
                            />

                            <!-- Data Points -->
                            <circle cx="45" cy="110" r="3" fill="#ffffff" />
                            <circle cx="120" cy="90" r="3" fill="#ffffff" />
                            <circle cx="195" cy="55" r="3" fill="#ffffff" />
                            <circle cx="270" cy="60" r="3" fill="#ffffff" />
                            <circle cx="305" cy="40" r="3" fill="#ffffff" />

                            <!-- X-Axis Labels -->
                            <text x="45" y="140" fill="rgba(255,255,255,0.85)" font-size="8" text-anchor="middle">M</text>
                            <text x="88" y="140" fill="rgba(255,255,255,0.85)" font-size="8" text-anchor="middle">T</text>
                            <text x="131" y="140" fill="rgba(255,255,255,0.85)" font-size="8" text-anchor="middle">W</text>
                            <text x="174" y="140" fill="rgba(255,255,255,0.85)" font-size="8" text-anchor="middle">T</text>
                            <text x="217" y="140" fill="rgba(255,255,255,0.85)" font-size="8" text-anchor="middle">F</text>
                            <text x="260" y="140" fill="rgba(255,255,255,0.85)" font-size="8" text-anchor="middle">S</text>
                            <text x="303" y="140" fill="rgba(255,255,255,0.85)" font-size="8" text-anchor="middle">S</text>
                        </svg>
                    </div>
                </div>

                <div class="pt-4 px-2">
                    <h4 class="text-sm font-bold text-slate-800">Daily Sales</h4>
                    <p class="text-xs text-slate-400 mt-0.5">
                        <span class="text-emerald-500 font-bold flex items-center inline-flex">
                            <TrendingUp class="w-3.5 h-3.5 mr-0.5" /> ↑ 55%
                        </span>
                        increase in today sales.
                    </p>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="flex items-center gap-1.5 text-xs text-slate-400 px-2 pb-1">
                    <Clock class="w-3.5 h-3.5" />
                    <span>updated 4 minutes ago</span>
                </div>
            </div>

            <!-- Chart Card 3: Completed Tasks (Cyan / Sky Blue Gradient Peak Wave Chart) -->
            <div class="bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-6">
                <!-- Overhanging Negative-Margin Card Box -->
                <div class="relative -mt-6 mx-2 rounded-xl p-4 text-white shadow-lg bg-gradient-to-tr from-[#00bcd4] to-[#26c6da] shadow-cyan-500/35">
                    <div class="h-44 flex flex-col justify-between">
                        <!-- SVG Peak Wave Line Chart (12am, 3pm, 6pm, 9pm, 12pm, 3am, 6am, 9am) -->
                        <svg class="w-full h-full overflow-visible" viewBox="0 0 320 150">
                            <!-- Dashed Horizontal Guide Lines -->
                            <line x1="25" y1="15" x2="310" y2="15" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="42" x2="310" y2="42" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="70" x2="310" y2="70" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="98" x2="310" y2="98" stroke="rgba(255,255,255,0.25)" stroke-dasharray="3,3" stroke-width="1" />
                            <line x1="25" y1="125" x2="310" y2="125" stroke="rgba(255,255,255,0.4)" stroke-width="1" />

                            <!-- Y-Axis Labels -->
                            <text x="20" y="19" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">800</text>
                            <text x="20" y="46" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">600</text>
                            <text x="20" y="74" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">400</text>
                            <text x="20" y="102" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">200</text>
                            <text x="20" y="128" fill="rgba(255,255,255,0.7)" font-size="8" text-anchor="end">0</text>

                            <!-- Peak Line Path: rises high at 3pm, then slopes down -->
                            <path 
                                d="M 40 100 L 75 40 L 115 80 L 155 90 L 195 95 L 235 98 L 275 92 L 305 96" 
                                fill="none" 
                                stroke="#ffffff" 
                                stroke-width="2.5" 
                                stroke-linecap="round" 
                                stroke-linejoin="round"
                            />

                            <!-- Data Points -->
                            <circle cx="40" cy="100" r="3" fill="#ffffff" />
                            <circle cx="75" cy="40" r="3" fill="#ffffff" />
                            <circle cx="115" cy="80" r="3" fill="#ffffff" />
                            <circle cx="155" cy="90" r="3" fill="#ffffff" />
                            <circle cx="195" cy="95" r="3" fill="#ffffff" />
                            <circle cx="235" cy="98" r="3" fill="#ffffff" />
                            <circle cx="275" cy="92" r="3" fill="#ffffff" />
                            <circle cx="305" cy="96" r="3" fill="#ffffff" />

                            <!-- X-Axis Labels -->
                            <text x="40" y="140" fill="rgba(255,255,255,0.85)" font-size="6.5" text-anchor="middle">12am</text>
                            <text x="75" y="140" fill="rgba(255,255,255,0.85)" font-size="6.5" text-anchor="middle">3pm</text>
                            <text x="115" y="140" fill="rgba(255,255,255,0.85)" font-size="6.5" text-anchor="middle">6pm</text>
                            <text x="155" y="140" fill="rgba(255,255,255,0.85)" font-size="6.5" text-anchor="middle">9pm</text>
                            <text x="195" y="140" fill="rgba(255,255,255,0.85)" font-size="6.5" text-anchor="middle">12pm</text>
                            <text x="235" y="140" fill="rgba(255,255,255,0.85)" font-size="6.5" text-anchor="middle">3am</text>
                            <text x="275" y="140" fill="rgba(255,255,255,0.85)" font-size="6.5" text-anchor="middle">6am</text>
                            <text x="305" y="140" fill="rgba(255,255,255,0.85)" font-size="6.5" text-anchor="middle">9am</text>
                        </svg>
                    </div>
                </div>

                <div class="pt-4 px-2">
                    <h4 class="text-sm font-bold text-slate-800">Completed Tasks</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Last Campaign Performance</p>
                </div>
                <hr class="border-t border-slate-100 my-3" />
                <div class="flex items-center gap-1.5 text-xs text-slate-400 px-2 pb-1">
                    <Clock class="w-3.5 h-3.5" />
                    <span>campaign sent 26 minutes ago</span>
                </div>
            </div>
        </div>

        <!-- 3. Bottom Row: Left Employees Stats Table & Right Tasks Card with Tabs (Exact Material Vue Spec) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-2">
            <!-- Left Card: Employees Stats (Overhanging Orange Box Header) -->
            <div class="bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-6">
                <!-- Overhanging Orange Box -->
                <div class="relative -mt-6 mx-2 rounded-xl p-4 text-white shadow-lg bg-gradient-to-tr from-[#ffa726] to-[#fb8c00] shadow-orange-500/35">
                    <h3 class="text-base font-bold text-white leading-tight">Employees Stats</h3>
                    <p class="text-xs text-white/80 mt-0.5">New employees on 15th September, 2016</p>
                </div>

                <!-- Table Content -->
                <div class="overflow-x-auto pt-4 px-2">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 text-xs">
                                <th class="pb-3 text-[#fb8c00] font-bold">ID</th>
                                <th class="pb-3 font-semibold text-slate-600">Name</th>
                                <th class="pb-3 font-semibold text-slate-600">Salary</th>
                                <th class="pb-3 font-semibold text-slate-600">Country</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr 
                                v-for="emp in employees" 
                                :key="emp.id" 
                                class="hover:bg-slate-50/70 transition"
                            >
                                <td class="py-3.5 text-slate-500 font-medium">{{ emp.id }}</td>
                                <td class="py-3.5">
                                    <div class="font-medium text-slate-800">{{ emp.name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ emp.role }}</div>
                                </td>
                                <td class="py-3.5 font-medium text-slate-700">
                                    {{ emp.salary }}
                                </td>
                                <td class="py-3.5 text-slate-600 font-medium">
                                    {{ emp.faculty }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="pt-4 pb-1 px-2 border-t border-slate-100 mt-3 flex items-center justify-between text-xs text-slate-400">
                    <span>Sirkulasi & Staf Sarpras</span>
                    <Link :href="route('loans.index')" class="font-semibold text-orange-600 hover:text-orange-700">
                        Lihat Seluruh Data &rarr;
                    </Link>
                </div>
            </div>

            <!-- Right Card: Tasks with Tabs (Overhanging Hot Pink / Magenta Box Header) -->
            <div class="bg-white rounded-2xl p-4 pt-0 shadow-sm border border-slate-100 mt-6">
                <!-- Overhanging Pink Box with Tabs -->
                <div class="relative -mt-6 mx-2 rounded-xl p-3 sm:p-4 text-white shadow-lg bg-gradient-to-tr from-[#ec407a] to-[#d81b60] shadow-pink-500/35 flex flex-wrap items-center justify-between gap-2">
                    <div class="font-bold text-sm sm:text-base">
                        Tasks:
                    </div>

                    <!-- 3 Navigation Tabs (BUGS, WEBSITE, SERVER) -->
                    <div class="flex items-center gap-1 text-xs">
                        <button
                            type="button"
                            @click="activeTaskTab = 'bugs'"
                            :class="[
                                'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold transition',
                                activeTaskTab === 'bugs' ? 'bg-white/20 text-white shadow-xs' : 'text-white/80 hover:bg-white/10'
                            ]"
                        >
                            <Bug class="w-3.5 h-3.5" />
                            <span>BUGS</span>
                        </button>

                        <button
                            type="button"
                            @click="activeTaskTab = 'website'"
                            :class="[
                                'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold transition',
                                activeTaskTab === 'website' ? 'bg-white/25 text-white shadow-xs' : 'text-white/80 hover:bg-white/10'
                            ]"
                        >
                            <Code class="w-3.5 h-3.5" />
                            <span>WEBSITE</span>
                        </button>

                        <button
                            type="button"
                            @click="activeTaskTab = 'server'"
                            :class="[
                                'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold transition',
                                activeTaskTab === 'server' ? 'bg-white/20 text-white shadow-xs' : 'text-white/80 hover:bg-white/10'
                            ]"
                        >
                            <Cloud class="w-3.5 h-3.5" />
                            <span>SERVER</span>
                        </button>
                    </div>
                </div>

                <!-- Tasks Checklist Body -->
                <div class="pt-3 px-2 divide-y divide-slate-100">
                    <div 
                        v-for="(task, idx) in tasks" 
                        :key="task.id" 
                        class="py-3 flex items-start gap-3 hover:bg-slate-50/60 p-2 rounded-xl transition group"
                    >
                        <!-- Checkbox -->
                        <div class="pt-0.5">
                            <button 
                                type="button"
                                @click="toggleTask(idx)"
                                :class="[
                                    'w-4 h-4 rounded flex items-center justify-center border transition',
                                    task.completed ? 'bg-[#9c27b0] border-[#9c27b0] text-white' : 'border-slate-300 bg-white hover:border-[#9c27b0]'
                                ]"
                            >
                                <Check v-if="task.completed" class="w-3 h-3 stroke-[3]" />
                            </button>
                        </div>

                        <!-- Task Text -->
                        <div class="flex-1 min-w-0" @click="toggleTask(idx)">
                            <div 
                                :class="[
                                    'text-xs font-medium cursor-pointer transition',
                                    task.completed ? 'line-through text-slate-400' : 'text-slate-700'
                                ]"
                            >
                                {{ task.title }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                {{ task.subtitle }}
                            </div>
                        </div>

                        <!-- Action Icons (Pencil purple & X red) -->
                        <div class="flex items-center gap-2 pt-0.5 opacity-60 group-hover:opacity-100 transition">
                            <button 
                                type="button" 
                                class="text-purple-600 hover:text-purple-800 transition"
                                title="Edit Task"
                            >
                                <Pencil class="w-3.5 h-3.5" />
                            </button>
                            <button 
                                type="button" 
                                @click="removeTask(idx)"
                                class="text-rose-500 hover:text-rose-700 transition"
                                title="Hapus Task"
                            >
                                <X class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-4 pb-1 px-2 border-t border-slate-100 mt-2 flex items-center justify-between text-xs text-slate-400">
                    <span>Task Operasional Sarpras</span>
                    <span class="text-purple-600 font-bold text-[11px]">{{ tasks.filter(t => t.completed).length }} / {{ tasks.length }} Selesai</span>
                </div>
            </div>
        </div>

        <!-- 4. Bottom Center Pill: Vue.js + Laravel Tech Stack Signature Badge (As in Image) -->
        <div class="flex items-center justify-center pt-8 pb-4">
            <div class="inline-flex items-center gap-3 px-6 py-2.5 bg-white rounded-full shadow-lg border border-slate-100 text-sm font-extrabold text-slate-800">
                <!-- Vue.js Icon -->
                <svg class="w-5 h-5" viewBox="0 0 261 226">
                    <path d="M161.096.001l-30.225 52.351L100.647.001H-.005l130.877 226.688L261.749.001z" fill="#41B883"/>
                    <path d="M161.096.001l-30.225 52.351L100.647.001H52.246l78.626 136.181L209.497.001z" fill="#34495E"/>
                </svg>
                <span>Vue.js</span>
                <span class="text-slate-300 font-normal">+</span>
                <!-- Laravel Icon -->
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#FF2D20">
                    <path d="M21.5 6.5l-8.5-4.9c-.6-.3-1.4-.3-2 0L2.5 6.5c-.6.3-1 1-1 1.7v9.6c0 .7.4 1.4 1 1.7l8.5 4.9c.3.2.7.2 1 .2s.7-.1 1-.2l8.5-4.9c.6-.3 1-1 1-1.7V8.2c0-.7-.4-1.4-1-1.7zm-9.5-3.3l7 4-7 4-7-4 7-4zm-8 6l7 4v8l-7-4v-8zm9 12v-8l7-4v8l-7 4z"/>
                </svg>
                <span>Laravel</span>
            </div>
        </div>
    </div>
</template>
