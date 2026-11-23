<script setup>
import { ref, computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { 
    ShieldCheck, 
    Layers, 
    DoorOpen, 
    Repeat, 
    Wrench, 
    QrCode, 
    Building2, 
    Clock, 
    Calendar, 
    CheckCircle2, 
    ArrowRight, 
    LogIn, 
    Menu, 
    X, 
    ChevronDown, 
    HelpCircle, 
    Phone, 
    Mail, 
    MapPin, 
    Sparkles, 
    FileText, 
    Users, 
    Search, 
    BookOpen, 
    Award,
    TrendingUp,
    Check,
    ArrowUpRight,
    Wifi,
    Tv,
    SlidersHorizontal,
    Monitor,
    Airplay,
    ShieldAlert,
    ExternalLink
} from 'lucide-vue-next';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    stats: {
        type: Object,
        default: () => ({
            totalAssets: 14820,
            totalRooms: 64,
            totalBuildings: 8,
            totalFaculties: 6,
            activeLoans: 18,
        }),
    },
});

const showingMobileMenu = ref(false);
const activeFaq = ref(null);
const searchQuery = ref('');
const selectedCategory = ref('all');

const toggleFaq = (index) => {
    activeFaq.value = activeFaq.value === index ? null : index;
};

const services = [
    {
        title: 'Reservasi Ruangan & Laboratorium',
        desc: 'Sistem peminjaman ruang kelas, auditorium, dan laboratorium digital dengan kalender anti-bentrok serta persetujuan berjenjang langsung oleh PIC ruangan.',
        icon: DoorOpen,
        color: 'from-blue-600 to-indigo-600',
        badge: 'Reservasi Mandiri',
        route: 'login',
    },
    {
        title: 'Sirkulasi Peminjaman Alat & Aset',
        desc: 'Peminjaman perangkat praktikum dan penunjang kegiatan seperti proyektor laser, sound system portable, dan kamera dengan Berita Acara Digital (BAP).',
        icon: Repeat,
        color: 'from-emerald-600 to-teal-600',
        badge: 'Sirkulasi Terpadu',
        route: 'login',
    },
    {
        title: 'Helpdesk & Tiket Perbaikan Fasilitas',
        desc: 'Pelaporan kendala kerusakan AC, kelistrikan, mebeler, dan perangkat lab dengan jaminan respons teknisi kampus termonitor dan transparan.',
        icon: Wrench,
        color: 'from-rose-600 to-pink-600',
        badge: 'Respons Cepat',
        route: 'login',
    },
    {
        title: 'Sensus & Audit Berbasis QR Code',
        desc: 'Pelabelan QR Code unik pada setiap unit inventaris di seluruh gedung guna menjamin akurasi fisik, riwayat pergerakan, dan pemenuhan audit akreditasi.',
        icon: QrCode,
        color: 'from-slate-800 to-slate-950',
        badge: 'Standar Akreditasi',
        route: 'login',
    },
];

const procedures = [
    {
        step: '01',
        title: 'Pengajuan Layanan Digital',
        desc: 'Civitas masuk menggunakan akun SSO, memilih ruangan/alat yang dibutuhkan, serta melengkapi waktu pemakaian dan deskripsi kegiatan.',
        icon: FileText,
    },
    {
        step: '02',
        title: 'Verifikasi & Validasi PIC',
        desc: 'Pengelola gedung atau kepala laboratorium memverifikasi jadwal bebas-bentrok dan menyetujui secara digital tanpa disposisi kertas.',
        icon: CheckCircle2,
    },
    {
        step: '03',
        title: 'Serah Terima & Pemakaian',
        desc: 'Akses ruangan dibuka atau alat diserahkan kepada pemohon dengan pencocokan kode tiket / QR barcode resmi di lokasi kampus.',
        icon: Repeat,
    },
    {
        step: '04',
        title: 'Inspeksi Kembali & Selesai',
        desc: 'Pemeriksaan kondisi kelayakan fisik pasca-pemakaian oleh staf sarpras, penutupan tiket sistem, dan perekaman histori otomatis.',
        icon: Award,
    },
];

const facilitiesCatalog = [
    {
        id: 1,
        name: 'Laboratorium Komputer & AI Terpadu',
        category: 'lab',
        faculty: 'Fakultas Ilmu Komputer',
        building: 'Gedung FT & FIK, Lantai 3',
        capacity: '45 Kursi',
        features: ['Workstation Intel i7/RTX', 'AC Dual Unit', 'Proyektor Laser HD', 'Wi-Fi 6', 'Audio DSP'],
        status: 'Tersedia',
    },
    {
        id: 2,
        name: 'Auditorium Utama KH. Syamun',
        category: 'aula',
        faculty: 'Gedung Rektorat & Aula',
        building: 'Gedung Rektorat, Lantai 4',
        capacity: '600 Kursi',
        features: ['Sound System Digital', 'Videowall LED P2.5', 'Panggung Utama', 'Ruang Transit VIP', 'Full AC'],
        status: 'Tersedia',
    },
    {
        id: 3,
        name: 'Ruang Seminar & Kolokium Pascasarjana',
        category: 'seminar',
        faculty: 'Program Pascasarjana',
        building: 'Gedung Pascasarjana, Lantai 2',
        capacity: '70 Kursi',
        features: ['Smart Interactive Display', 'Hybrid Teleconference Cam', 'Wi-Fi 6', 'Podium Digital'],
        status: 'Tersedia',
    },
    {
        id: 4,
        name: 'Laboratorium Mekatronika & Robotika',
        category: 'lab',
        faculty: 'Fakultas Teknik',
        building: 'Gedung Laboratorium Teknik, Lantai 1',
        capacity: '35 Kursi',
        features: ['Bench Power Supply', 'Oscilloscope Digital', 'PLC Training Kit', 'Sistem Exhaust Fan'],
        status: 'Tersedia',
    },
    {
        id: 5,
        name: 'Ruang Teater Kuliah Umum',
        category: 'seminar',
        faculty: 'Fakultas Ekonomi & Bisnis',
        building: 'Gedung FEB, Lantai 2',
        capacity: '120 Kursi',
        features: ['Kursi Bertingkat Tiered', 'Dual Projector Screens', 'Wireless Clip-on Mic', 'Full AC'],
        status: 'Tersedia',
    },
    {
        id: 6,
        name: 'Gelanggang Olahraga & Seni Mahasiswa',
        category: 'olahraga',
        faculty: 'Sarana Bersama Universitas',
        building: 'Kompleks GOR Universitas',
        capacity: '400 Penonton',
        features: ['Lapangan Futsal & Basket', 'Tribun Penonton', 'Lampu Sorot LED', 'Ruang Ganti Atlet'],
        status: 'Tersedia',
    },
];

const filteredFacilities = computed(() => {
    return facilitiesCatalog.filter((item) => {
        const matchesCategory = selectedCategory.value === 'all' || item.category === selectedCategory.value;
        const matchesSearch = !searchQuery.value || 
            item.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.faculty.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.building.toLowerCase().includes(searchQuery.value.toLowerCase());
        return matchesCategory && matchesSearch;
    });
});

const faqs = [
    {
        q: 'Siapa saja yang memiliki hak untuk meminjam sarana & prasarana kampus?',
        a: 'Seluruh civitas akademika aktif Universitas Al-Khairiyah, mencakup Dosen, Mahasiswa, Tenaga Kependidikan, serta Organisasi Mahasiswa resmi (BEM, DPM, HIMA, dan UKM) yang memiliki akun SSO terverifikasi.',
    },
    {
        q: 'Berapa hari sebelum pelaksanaan kegiatan peminjaman ruangan harus diajukan?',
        a: 'Pengajuan reservasi ruangan perkuliahan atau seminar disarankan diajukan minimal H-3 sebelum kegiatan, sedangkan untuk aula utama/auditorium berkapasitas besar minimal H-7 agar Biro Sarpras dapat mengoordinasikan tim teknis dan fasilitas pendukung.',
    },
    {
        q: 'Bagaimana prosedur pelaporan kerusakan sarana kampus seperti AC atau proyektor?',
        a: 'Civitas dapat langsung masuk ke portal SIM-SARPRAS, membuka menu "Pemeliharaan & Servis", lalu membuat tiket baru dengan melampirkan foto kendala dan lokasi ruangan. Teknisi sarpras akan merespons dalam waktu kurang dari 24 jam kerja.',
    },
    {
        q: 'Apakah peminjaman alat penunjang praktikum memerlukan surat pengantar fisik?',
        a: 'Tidak perlu. SIM-SARPRAS telah menerapkan Berita Acara Digital (BAP Digital) 100% paperless. Cukup isi form peminjaman di sistem, mintakan persetujuan dosen pembimbing/laboran di sistem, dan tunjukkan kode reservasi saat pengambilan alat.',
    },
    {
        q: 'Bagaimana cara memeriksa ketersediaan ruangan sebelum mengajukan?',
        a: 'Anda dapat melihat jadwal dan status keterisian ruangan secara transparan di portal ini atau langsung pada menu Reservasi Ruangan di akun Anda untuk menghindari bentrok jadwal kegiatan.',
    },
];

const setFilter = (chip) => {
    searchQuery.value = chip;
};
</script>

<template>
    <Head title="Portal Resmi Sarana & Prasarana - Universitas Al-Khairiyah" />

    <div class="min-h-screen bg-[#f8fafc] text-slate-800 font-sans antialiased selection:bg-blue-600 selection:text-white flex flex-col justify-between">
        <!-- 1. Floating Sticky Navigation Bar -->
        <header class="sticky top-4 z-50 mx-4 sm:mx-8 lg:mx-12">
            <nav class="bg-white/95 backdrop-blur-md rounded-2xl border border-slate-200/90 shadow-lg shadow-slate-200/50 px-5 sm:px-6 py-3 flex items-center justify-between transition-all">
                <!-- Brand Logo & University Title -->
                <div class="flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 via-indigo-600 to-cyan-500 flex items-center justify-center text-white shadow-md shadow-blue-600/30 shrink-0">
                        <ShieldCheck class="w-6 h-6 stroke-[2.2]" />
                    </div>
                    <div>
                        <div class="text-sm font-black tracking-tight text-slate-900 leading-tight">
                            SIM-SARPRAS
                        </div>
                        <div class="text-[11px] font-semibold text-slate-500">
                            Universitas Al-Khairiyah
                        </div>
                    </div>
                </div>

                <!-- Desktop Nav Anchors -->
                <div class="hidden lg:flex items-center gap-7 text-xs font-bold text-slate-600">
                    <a href="#layanan" class="hover:text-blue-700 transition">Layanan Utama</a>
                    <a href="#fasilitas" class="hover:text-blue-700 transition">Katalog Fasilitas</a>
                    <a href="#prosedur" class="hover:text-blue-700 transition">Alur SOP</a>
                    <a href="#sensus" class="hover:text-blue-700 transition">Standar Sensus</a>
                    <a href="#faq" class="hover:text-blue-700 transition">Tanya Jawab</a>
                    <a href="#kontak" class="hover:text-blue-700 transition">Helpdesk</a>
                </div>

                <!-- Right Auth Action -->
                <div class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 text-white rounded-xl text-xs font-extrabold shadow-md shadow-blue-700/25 transition transform hover:-translate-y-0.5"
                    >
                        <span>Dashboard Saya</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 text-white rounded-xl text-xs font-extrabold shadow-md shadow-blue-700/25 transition transform hover:-translate-y-0.5"
                        >
                            <LogIn class="w-3.5 h-3.5" />
                            <span>Masuk Portal SSO</span>
                        </Link>
                    </template>

                    <!-- Mobile Hamburger Button -->
                    <button
                        @click="showingMobileMenu = !showingMobileMenu"
                        class="p-2 text-slate-600 hover:bg-slate-100 rounded-xl lg:hidden focus:outline-none"
                        aria-label="Menu"
                    >
                        <Menu v-if="!showingMobileMenu" class="w-5 h-5" />
                        <X v-else class="w-5 h-5" />
                    </button>
                </div>
            </nav>

            <!-- Mobile Dropdown Nav Menu -->
            <div 
                v-if="showingMobileMenu" 
                class="lg:hidden mt-2 p-4 bg-white rounded-2xl border border-slate-200 shadow-xl space-y-2 animate-in fade-in duration-150"
            >
                <a 
                    href="#layanan" 
                    @click="showingMobileMenu = false"
                    class="block px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 rounded-lg"
                >
                    Layanan Utama
                </a>
                <a 
                    href="#fasilitas" 
                    @click="showingMobileMenu = false"
                    class="block px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 rounded-lg"
                >
                    Katalog Fasilitas & Lab
                </a>
                <a 
                    href="#prosedur" 
                    @click="showingMobileMenu = false"
                    class="block px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 rounded-lg"
                >
                    Alur & SOP Pengajuan
                </a>
                <a 
                    href="#sensus" 
                    @click="showingMobileMenu = false"
                    class="block px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 rounded-lg"
                >
                    Standar Sensus & Audit QR
                </a>
                <a 
                    href="#faq" 
                    @click="showingMobileMenu = false"
                    class="block px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 rounded-lg"
                >
                    Tanya Jawab (FAQ)
                </a>
                <a 
                    href="#kontak" 
                    @click="showingMobileMenu = false"
                    class="block px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 rounded-lg"
                >
                    Kontak Helpdesk Sarpras
                </a>
            </div>
        </header>

        <!-- 2. Hero Section (Enterprise Campus Presentation) -->
        <main class="max-w-7xl mx-auto px-4 sm:px-8 pt-8 sm:pt-12 pb-16 w-full space-y-12">
            <!-- Hero Container with Deep Royal Navy Gradient -->
            <div class="relative bg-gradient-to-br from-[#0a1128] via-[#101f42] to-[#1e3a8a] text-white rounded-3xl p-8 sm:p-14 lg:p-16 shadow-2xl border border-blue-900/60 overflow-hidden">
                <!-- Ambient Glow Effect & Blueprint Mesh -->
                <div class="absolute -right-24 -top-24 w-[30rem] h-[30rem] bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-24 -bottom-24 w-[26rem] h-[26rem] bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute inset-0 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:32px_32px] opacity-10 pointer-events-none"></div>

                <div class="max-w-3xl space-y-6 relative z-10">
                    <!-- Institutional Announcement Badge -->
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/20 text-cyan-200 border border-blue-400/30 text-xs font-bold backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>SIM-SARPRAS v2.4 • Portal Terpadu Biro Sarana & Prasarana</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-white tracking-tight leading-tight sm:leading-tight">
                        Pengelolaan Fasilitas & Aset Kampus Terpadu
                    </h1>

                    <p class="text-xs sm:text-base text-blue-100/90 leading-relaxed font-normal">
                        Akses satu pintu seluruh civitas akademika Universitas Al-Khairiyah untuk reservasi ruang kuliah & laboratorium, sirkulasi peminjaman alat praktikum, pemeliharaan sarana secara real-time, serta pencatatan inventaris berlabel stiker QR Code.
                    </p>

                    <!-- Primary Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <Link
                            :href="$page.props.auth?.user ? route('dashboard') : route('login')"
                            class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white rounded-2xl text-xs sm:text-sm font-extrabold shadow-lg shadow-blue-600/30 transition transform hover:-translate-y-0.5"
                        >
                            <span>{{ $page.props.auth?.user ? 'Buka Dashboard Saya' : 'Masuk Portal Civitas (SSO)' }}</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>

                        <a
                            href="#fasilitas"
                            class="inline-flex items-center gap-2 px-5 py-3.5 bg-white/10 hover:bg-white/15 border border-white/20 text-white rounded-2xl text-xs sm:text-sm font-bold backdrop-blur-sm transition"
                        >
                            <DoorOpen class="w-4 h-4 text-cyan-300" />
                            <span>Katalog Fasilitas</span>
                        </a>

                        <a
                            href="#prosedur"
                            class="inline-flex items-center gap-2 px-5 py-3.5 text-blue-200 hover:text-white text-xs sm:text-sm font-bold transition"
                        >
                            <span>Alur SOP Pengajuan →</span>
                        </a>
                    </div>

                    <!-- Interactive Quick Search Bar Inside Hero -->
                    <div class="pt-4 border-t border-white/10 max-w-2xl">
                        <div class="text-xs font-semibold text-blue-200 mb-2 flex items-center gap-1.5">
                            <Search class="w-3.5 h-3.5 text-cyan-300" />
                            <span>Pencarian Cepat Ruangan / Fasilitas Kampus:</span>
                        </div>
                        <div class="relative">
                            <input
                                type="text"
                                v-model="searchQuery"
                                placeholder="Ketik nama laboratorium, aula, gedung, atau alat..."
                                class="w-full pl-10 pr-4 py-3 bg-white/10 border border-white/20 rounded-2xl text-xs sm:text-sm text-white placeholder:text-blue-200/60 focus:bg-white/15 focus:outline-none focus:border-cyan-400 focus:ring-2 focus:ring-cyan-400/20 backdrop-blur-md transition"
                            />
                            <Search class="w-4 h-4 text-blue-200/70 absolute left-3.5 top-3.5" />
                        </div>

                        <!-- Quick Category / Filter Chips -->
                        <div class="flex flex-wrap items-center gap-1.5 mt-2.5">
                            <span class="text-[11px] text-blue-200/80 mr-1">Rekomendasi:</span>
                            <button
                                v-for="chip in ['Lab Komputer', 'Auditorium KH. Syamun', 'Ruang Seminar', 'Robotika', 'GOR Kampus']"
                                :key="chip"
                                type="button"
                                @click="setFilter(chip)"
                                class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-[11px] font-semibold text-cyan-200 border border-white/10 transition"
                            >
                                {{ chip }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Four Live Campus Metrics (Overhanging Material Design Elevation) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-2">
                <!-- Metric 1: Total Unit Aset -->
                <div class="relative bg-white rounded-3xl p-5 pt-0 shadow-sm border border-slate-200/80 mt-5">
                    <div class="absolute -top-5 left-5 w-12 h-12 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-950 shadow-lg shadow-slate-900/30 flex items-center justify-center text-white">
                        <Layers class="w-6 h-6 stroke-[2]" />
                    </div>
                    <div class="pt-4 text-right">
                        <span class="text-xs font-bold text-slate-500">Unit Aset Terdata</span>
                        <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                            {{ Number(stats.totalAssets || 14820).toLocaleString('id-ID') }}
                        </div>
                    </div>
                    <hr class="border-t border-slate-100 my-3" />
                    <div class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="text-emerald-600 font-bold">100% Terverifikasi</span>
                        <span>Label Stiker QR Unik</span>
                    </div>
                </div>

                <!-- Metric 2: Ruangan & Lab -->
                <div class="relative bg-white rounded-3xl p-5 pt-0 shadow-sm border border-slate-200/80 mt-5">
                    <div class="absolute -top-5 left-5 w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 shadow-lg shadow-blue-600/30 flex items-center justify-center text-white">
                        <DoorOpen class="w-6 h-6 stroke-[2]" />
                    </div>
                    <div class="pt-4 text-right">
                        <span class="text-xs font-bold text-slate-500">Ruang & Lab Terjadwal</span>
                        <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                            {{ Number(stats.totalRooms || 64).toLocaleString('id-ID') }} Ruang
                        </div>
                    </div>
                    <hr class="border-t border-slate-100 my-3" />
                    <div class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="text-blue-700 font-bold">{{ stats.totalBuildings || 8 }} Gedung</span>
                        <span>Terintegrasi Sistem Bebas-Bentrok</span>
                    </div>
                </div>

                <!-- Metric 3: Sirkulasi Peminjaman -->
                <div class="relative bg-white rounded-3xl p-5 pt-0 shadow-sm border border-slate-200/80 mt-5">
                    <div class="absolute -top-5 left-5 w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-600 shadow-lg shadow-emerald-600/30 flex items-center justify-center text-white">
                        <Repeat class="w-6 h-6 stroke-[2]" />
                    </div>
                    <div class="pt-4 text-right">
                        <span class="text-xs font-bold text-slate-500">Peminjaman Berjalan</span>
                        <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                            {{ Number(stats.activeLoans || 18).toLocaleString('id-ID') }} Sesi
                        </div>
                    </div>
                    <hr class="border-t border-slate-100 my-3" />
                    <div class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="text-emerald-600 font-bold">Real-time</span>
                        <span>Berita Acara Digital (BAP)</span>
                    </div>
                </div>

                <!-- Metric 4: SLA Pemeliharaan -->
                <div class="relative bg-white rounded-3xl p-5 pt-0 shadow-sm border border-slate-200/80 mt-5">
                    <div class="absolute -top-5 left-5 w-12 h-12 rounded-2xl bg-gradient-to-tr from-rose-600 to-pink-600 shadow-lg shadow-rose-600/30 flex items-center justify-center text-white">
                        <Wrench class="w-6 h-6 stroke-[2]" />
                    </div>
                    <div class="pt-4 text-right">
                        <span class="text-xs font-bold text-slate-500">SLA Respons Servis</span>
                        <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                            &lt; 24 Jam
                        </div>
                    </div>
                    <hr class="border-t border-slate-100 my-3" />
                    <div class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="text-rose-600 font-bold">Siaga Teknisi</span>
                        <span>Pemeliharaan Fasilitas Kampus</span>
                    </div>
                </div>
            </div>

            <!-- 4. Layanan Utama Portal (Services Section) -->
            <section id="layanan" class="pt-8 space-y-6">
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                        Layanan Mandiri Civitas
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Fasilitas Digital Biro Sarana & Prasarana
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Dirancang untuk memberikan kemudahan bagi mahasiswa, dosen, serta unit kerja dalam memanfaatkan sarana kampus secara bertanggung jawab.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        v-for="service in services"
                        :key="service.title"
                        class="bg-white rounded-3xl p-6 shadow-sm border border-slate-200/80 hover:shadow-xl hover:border-blue-300 transition-all duration-200 flex flex-col justify-between group"
                    >
                        <div class="space-y-4">
                            <div :class="['w-12 h-12 rounded-2xl bg-gradient-to-tr text-white flex items-center justify-center shadow-md', service.color]">
                                <component :is="service.icon" class="w-6 h-6 stroke-[2.2]" />
                            </div>

                            <div>
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 mb-2">
                                    {{ service.badge }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 group-hover:text-blue-700 transition">
                                    {{ service.title }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                    {{ service.desc }}
                                </p>
                            </div>
                        </div>

                        <div class="pt-5 border-t border-slate-100 mt-5">
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 group-hover:text-blue-900 transition"
                            >
                                <span>Akses Layanan Ini</span>
                                <ArrowUpRight class="w-3.5 h-3.5 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5. Katalog Fasilitas & Laboratorium (Interactive Directory) -->
            <section id="fasilitas" class="pt-8 space-y-6">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                    <div>
                        <span class="text-xs font-extrabold uppercase tracking-wider text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                            Direktori Fasilitas
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1.5">
                            Ruang Perkuliahan, Lab & Fasilitas Utama
                        </h2>
                    </div>

                    <!-- Category Tab Pills -->
                    <div class="flex flex-wrap items-center gap-2">
                        <button
                            v-for="cat in [
                                { id: 'all', label: 'Semua Fasilitas' },
                                { id: 'lab', label: 'Laboratorium' },
                                { id: 'seminar', label: 'Ruang Seminar' },
                                { id: 'aula', label: 'Auditorium' },
                                { id: 'olahraga', label: 'Sarana Olahraga' }
                            ]"
                            :key="cat.id"
                            type="button"
                            @click="selectedCategory = cat.id"
                            :class="[
                                'px-3.5 py-1.5 rounded-xl text-xs font-bold transition',
                                selectedCategory === cat.id 
                                    ? 'bg-blue-700 text-white shadow-xs' 
                                    : 'bg-white hover:bg-slate-100 text-slate-600 border border-slate-200'
                            ]"
                        >
                            {{ cat.label }}
                        </button>
                    </div>
                </div>

                <!-- Facility Cards Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="item in filteredFacilities"
                        :key="item.id"
                        class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm hover:shadow-lg hover:border-blue-200 transition-all flex flex-col justify-between"
                    >
                        <div class="space-y-4">
                            <!-- Header with Faculty Badge & Status -->
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-[10px] font-extrabold px-2.5 py-1 bg-slate-100 text-slate-700 rounded-lg">
                                    {{ item.faculty }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>{{ item.status }}</span>
                                </span>
                            </div>

                            <div>
                                <h3 class="text-base font-bold text-slate-900 leading-snug">
                                    {{ item.name }}
                                </h3>
                                <div class="flex items-center gap-2 text-xs text-slate-400 mt-1">
                                    <MapPin class="w-3.5 h-3.5 shrink-0" />
                                    <span>{{ item.building }}</span>
                                </div>
                            </div>

                            <!-- Amenities Tags -->
                            <div class="space-y-1.5 pt-1">
                                <div class="text-[11px] font-bold text-slate-500">Kapasitas & Fasilitas:</div>
                                <div class="flex flex-wrap gap-1.5">
                                    <span class="px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-bold border border-blue-100">
                                        {{ item.capacity }}
                                    </span>
                                    <span
                                        v-for="feat in item.features"
                                        :key="feat"
                                        class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-medium"
                                    >
                                        {{ feat }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Button -->
                        <div class="pt-5 border-t border-slate-100 mt-5 flex items-center justify-between">
                            <span class="text-xs text-slate-400">Verifikasi PIC Ruangan</span>
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold transition"
                            >
                                <span>Ajukan Reservasi</span>
                                <ArrowRight class="w-3.5 h-3.5" />
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-if="filteredFacilities.length === 0" class="p-8 text-center bg-white rounded-3xl border border-slate-200">
                    <p class="text-sm text-slate-500 font-medium">Tidak ada fasilitas yang cocok dengan kata kunci pencarian Anda.</p>
                    <button @click="searchQuery = ''; selectedCategory = 'all'" class="mt-2 text-xs font-bold text-blue-700 hover:underline">
                        Reset Filter & Pencarian
                    </button>
                </div>
            </section>

            <!-- 6. Alur SOP Prosedur Pengajuan (Step-by-step SOP Timeline) -->
            <section id="prosedur" class="pt-8">
                <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-sm border border-slate-200/80 space-y-8">
                    <div class="text-center max-w-2xl mx-auto space-y-2">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                            Standar Operasional Prosedur (SOP)
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                            4 Langkah Mudah Alur Peminjaman Fasilitas
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500">
                            Proses serba digital, transparan, cepat, dan tanpa birokrasi formulir kertas manual.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 pt-2">
                        <div
                            v-for="(item, idx) in procedures"
                            :key="item.step"
                            class="relative p-6 rounded-2xl bg-slate-50/80 border border-slate-200/80 flex flex-col justify-between"
                        >
                            <div>
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 to-indigo-700 text-white flex items-center justify-center font-black text-xs shadow-md shadow-blue-700/20 mb-4">
                                    {{ item.step }}
                                </div>
                                <h4 class="text-sm font-bold text-slate-900">{{ item.title }}</h4>
                                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                    {{ item.desc }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Notification Banner -->
                    <div class="bg-blue-50/80 rounded-2xl p-4 sm:p-5 border border-blue-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 text-xs">
                        <div class="flex items-center gap-3 text-blue-900">
                            <Sparkles class="w-5 h-5 text-blue-700 shrink-0" />
                            <span><strong>Notifikasi Real-time & Berita Acara Digital:</strong> Setiap perubahan status permohonan akan terkirim langsung ke akun Anda lengkap dengan QR Code validasi resmi.</span>
                        </div>
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center gap-1 font-bold text-blue-700 hover:text-blue-900 shrink-0 hover:underline"
                        >
                            <span>Ajukan Sekarang</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>
                    </div>
                </div>
            </section>

            <!-- 7. Standar Sensus & Audit Mutu Kampus (Accreditation Section) -->
            <section id="sensus" class="pt-8 space-y-6">
                <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 text-white rounded-3xl p-8 sm:p-12 shadow-xl border border-slate-700/60">
                    <div class="max-w-3xl space-y-3 mb-8">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-500/20 text-cyan-200 text-xs font-bold border border-blue-400/30">
                            <Award class="w-3.5 h-3.5" />
                            <span>Standar Penjaminan Mutu & Akreditasi BAN-PT</span>
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                            Transparansi Tata Kelola Sarana & Prasarana
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                            SIM-SARPRAS dirancang untuk memenuhi standar instrumen penilaian sarana prasarana perguruan tinggi (BAN-PT / LAM), memastikan setiap rupiah investasi fasilitas dapat dipertanggungjawabkan dan termanfaatkan optimal.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-xs space-y-2">
                            <QrCode class="w-6 h-6 text-cyan-300" />
                            <h4 class="text-sm font-bold text-white">Sensus QR Barcode</h4>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                Seluruh inventaris fisik memiliki stiker QR unik untuk inspeksi berkala di tempat (on-site audit).
                            </p>
                        </div>

                        <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-xs space-y-2">
                            <FileText class="w-6 h-6 text-emerald-300" />
                            <h4 class="text-sm font-bold text-white">100% Paperless</h4>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                Surat izin, persetujuan dekanat/rektorat, dan BAP serah terima tercatat tanpa tumpukan arsip fisik.
                            </p>
                        </div>

                        <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-xs space-y-2">
                            <Clock class="w-6 h-6 text-rose-300" />
                            <h4 class="text-sm font-bold text-white">SLA Respons Cepat</h4>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                Komitmen penanganan kendala darurat fasilitas perkuliahan dalam waktu maksimal 24 jam kerja.
                            </p>
                        </div>

                        <div class="bg-white/5 border border-white/10 rounded-2xl p-5 backdrop-blur-xs space-y-2">
                            <ShieldCheck class="w-6 h-6 text-blue-300" />
                            <h4 class="text-sm font-bold text-white">Audit Trail Permanen</h4>
                            <p class="text-xs text-slate-300 leading-relaxed">
                                Riwayat pergerakan aset, mutasi antar-gedung, serta log perbaikan tersimpan rapi untuk audit tahunan.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 8. Tanya Jawab (FAQ) Section -->
            <section id="faq" class="pt-8">
                <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-sm border border-slate-200/80 space-y-6">
                    <div class="text-center max-w-2xl mx-auto space-y-2">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-blue-700 bg-blue-50 px-3 py-1 rounded-full border border-blue-100">
                            Pusat Bantuan Civitas
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                            Pertanyaan yang Sering Diajukan (FAQ)
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500">
                            Temukan jawaban cepat seputar peminjaman fasilitas dan pelaporan pemeliharaan sarpras.
                        </p>
                    </div>

                    <div class="max-w-3xl mx-auto divide-y divide-slate-100">
                        <div
                            v-for="(item, idx) in faqs"
                            :key="idx"
                            class="py-4.5"
                        >
                            <button
                                @click="toggleFaq(idx)"
                                class="w-full flex items-center justify-between text-left gap-4 font-bold text-sm text-slate-800 hover:text-blue-700 transition"
                            >
                                <span>{{ item.q }}</span>
                                <ChevronDown 
                                    :class="['w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0', activeFaq === idx ? 'rotate-180 text-blue-700' : '']" 
                                />
                            </button>
                            <div 
                                v-show="activeFaq === idx" 
                                class="mt-3 text-xs text-slate-500 leading-relaxed pr-6 animate-in fade-in duration-150"
                            >
                                {{ item.a }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 9. Kontak Helpdesk & Konsultasi Biro Sarana & Prasarana -->
            <section id="kontak" class="pt-8">
                <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white rounded-3xl p-8 sm:p-12 shadow-xl border border-blue-800/60 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
                    <div class="space-y-4 max-w-xl">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-cyan-200 text-xs font-bold">
                            <Phone class="w-3.5 h-3.5" />
                            <span>Helpdesk & Konsultasi Fasilitas</span>
                        </span>
                        <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                            Butuh Fasilitas Khusus Acara Besar Kampus?
                        </h3>
                        <p class="text-xs sm:text-sm text-blue-100/90 leading-relaxed">
                            Hubungi langsung Biro Sarana dan Prasarana (Sarpras) Universitas Al-Khairiyah untuk permohonan aula utama, koordinasi teknis dies natalis, wisuda sarjana, atau seminar internasional.
                        </p>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/15 space-y-3.5 shrink-0 min-w-[280px]">
                        <div class="flex items-center gap-3 text-xs text-blue-100">
                            <MapPin class="w-4 h-4 text-cyan-300 shrink-0" />
                            <span>Gedung Rektorat Lt. 1, Kampus Citangkil</span>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-blue-100">
                            <Mail class="w-4 h-4 text-cyan-300 shrink-0" />
                            <span>sarpras@kampus.ac.id</span>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-blue-100">
                            <Clock class="w-4 h-4 text-cyan-300 shrink-0" />
                            <span>Senin - Jumat: 08.00 - 16.00 WIB</span>
                        </div>

                        <div class="pt-3 border-t border-white/10">
                            <Link
                                :href="route('login')"
                                class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-extrabold text-xs shadow-md shadow-blue-600/30 transition"
                            >
                                <span>Masuk ke Akun Anda</span>
                                <ArrowRight class="w-3.5 h-3.5" />
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- 10. Official University Footer -->
        <footer class="bg-white border-t border-slate-200/90 py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-8 space-y-8 text-xs text-slate-500">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Brand info -->
                    <div class="md:col-span-2 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-700 to-indigo-700 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                <ShieldCheck class="w-5 h-5" />
                            </div>
                            <div>
                                <div class="font-extrabold text-slate-900 text-sm">SIM-SARPRAS</div>
                                <div class="text-[11px] text-slate-400">Universitas Al-Khairiyah</div>
                            </div>
                        </div>
                        <p class="text-xs text-slate-500 max-w-sm leading-relaxed">
                            Sistem Informasi Manajemen Sarana dan Prasarana Universitas Al-Khairiyah adalah platform resmi terpadu dalam pengelolaan aset akademik, sirkulasi fasilitas, reservasi ruangan, dan pemeliharaan gedung kampus.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div class="space-y-2.5">
                        <div class="font-bold text-slate-800 text-xs">Layanan Kampus</div>
                        <ul class="space-y-1.5 text-xs text-slate-500">
                            <li><a href="#layanan" class="hover:text-blue-700 transition">Peminjaman Ruang Kuliah</a></li>
                            <li><a href="#layanan" class="hover:text-blue-700 transition">Sirkulasi Alat Praktikum</a></li>
                            <li><a href="#layanan" class="hover:text-blue-700 transition">Helpdesk Perbaikan AC & Listrik</a></li>
                            <li><a href="#sensus" class="hover:text-blue-700 transition">Sensus Barcode QR Aset</a></li>
                        </ul>
                    </div>

                    <!-- University Address -->
                    <div class="space-y-2.5">
                        <div class="font-bold text-slate-800 text-xs">Kampus Utama</div>
                        <div class="text-xs text-slate-500 space-y-1">
                            <div>Jl. H. Enggus Arja No. 1, Citangkil</div>
                            <div>Kota Cilegon, Banten 42441</div>
                            <div class="pt-1">Telp: (0254) 372-xxx</div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-400">
                    <div>
                        &copy; {{ new Date().getFullYear() }} Universitas Al-Khairiyah. Seluruh hak cipta dilindungi undang-undang.
                    </div>
                    <div class="flex items-center gap-4">
                        <span>Standar Mutu BAN-PT</span>
                        <span>•</span>
                        <span>Privasi & Keamanan Data</span>
                        <span>•</span>
                        <span>SSO Terintegrasi</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>
