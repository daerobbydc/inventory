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
    Search, 
    MapPin, 
    Sparkles, 
    FileText, 
    Users, 
    Award,
    TrendingUp,
    Tv,
    Monitor,
    Smartphone,
    Check,
    Cpu,
    Video,
    Briefcase,
    GraduationCap,
    SlidersHorizontal,
    PhoneCall,
    Mail,
    ChevronDown,
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
const searchQuery = ref('');
const selectedBuilding = ref('all');
const selectedType = ref('all');

const popularCategories = [
    {
        title: 'Laboratorium & Komputasi',
        desc: '45 Workstation & Server Riset AI',
        icon: Monitor,
        color: 'text-blue-600 bg-blue-50',
    },
    {
        title: 'Studio Multimedia & Audio',
        desc: 'Kamera, Mic, Audio DSP & Mixer',
        icon: Video,
        color: 'text-indigo-600 bg-indigo-50',
    },
    {
        title: 'Ruang Kuliah & Teater',
        desc: '64 Ruangan Kuliah Terjadwal',
        icon: DoorOpen,
        color: 'text-sky-600 bg-sky-50',
    },
    {
        title: 'Helpdesk & Servis Fasilitas',
        desc: 'Respons Teknisi Cepat < 24 Jam',
        icon: Wrench,
        color: 'text-rose-600 bg-rose-50',
    },
    {
        title: 'Sensus & Audit QR Barcode',
        desc: '14.800+ Unit Fisik Terverifikasi',
        icon: QrCode,
        color: 'text-emerald-600 bg-emerald-50',
    },
    {
        title: 'Auditorium & Aula Utama',
        desc: 'Kapasitas 600 Kursi & Videowall',
        icon: Building2,
        color: 'text-amber-600 bg-amber-50',
    },
    {
        title: 'Lab Mekatronika & Mesin',
        desc: 'Bench Power Supply & CNC Unit',
        icon: Cpu,
        color: 'text-cyan-600 bg-cyan-50',
    },
    {
        title: 'Sarana Olahraga & Seni',
        desc: 'GOR Kampus & Lapangan Futsal',
        icon: Award,
        color: 'text-purple-600 bg-purple-50',
    },
];

const facilitiesList = [
    {
        id: 1,
        featured: true,
        faculty: 'Fakultas Ilmu Komputer',
        title: 'Laboratorium Riset AI & Komputasi',
        desc: 'Workstation high-end Intel i7/RTX, Dual AC, Proyektor Laser, Gigabit LAN & Wi-Fi 6.',
        location: 'Gedung FT & FIK, Lantai 3',
        capacity: '45 Kursi Workstation',
        duration: 'Tersedia Semester Ini',
        time: 'Pagi - Sore',
    },
    {
        id: 2,
        featured: false,
        faculty: 'Gedung Rektorat & Layanan',
        title: 'Auditorium Utama KH. Syamun',
        desc: 'Videowall LED P2.5, Sound System Digital DSP, Panggung Utama, Ruang Transit VIP & Full AC.',
        location: 'Gedung Rektorat, Lantai 4',
        capacity: '600 Kursi Audiens',
        duration: 'Reservasi Min. H-7',
        time: 'Sesuai Agenda',
    },
    {
        id: 3,
        featured: false,
        faculty: 'Program Pascasarjana',
        title: 'Ruang Kolokium & Teater Pascasarjana',
        desc: 'Smart Interactive Display, Kamera Teleconference Hybrid, Wireless Mic & Full AC.',
        location: 'Gedung Pascasarjana, Lantai 2',
        capacity: '70 Kursi Mahasiswa',
        duration: 'Tersedia Setiap Hari',
        time: 'Jadwal Kuliah',
    },
    {
        id: 4,
        featured: false,
        faculty: 'Fakultas Ekonomi & Bisnis',
        title: 'Ruang Teater Kuliah Umum FEB',
        desc: 'Kursi bertingkat (tiered seating), Dual Projector Screens, Audio System & Podium Digital.',
        location: 'Gedung FEB, Lantai 2',
        capacity: '120 Kursi Bertingkat',
        duration: 'Tersedia Sesi Kuliah',
        time: '08.00 - 17.00',
    },
    {
        id: 5,
        featured: false,
        faculty: 'Fakultas Teknik',
        title: 'Laboratorium Robotika & Otomasi',
        desc: 'PLC Training Kit, Osiloskop Digital, Power Supply Multichannel & Exhaust Safety System.',
        location: 'Gedung Lab Teknik, Lantai 1',
        capacity: '35 Meja Praktikum',
        duration: 'Praktikum & Riset',
        time: '08.00 - 16.00',
    },
    {
        id: 6,
        featured: false,
        faculty: 'Sarana Bersama Universitas',
        title: 'Gelanggang Olahraga (GOR) Mahasiswa',
        desc: 'Lapangan Futsal & Basket berstandar, Tribun penonton, Lampu Sorot LED & Ruang Ganti.',
        location: 'Kompleks Sarana Kampus',
        capacity: '400 Penonton',
        duration: 'Kegiatan Ormawa / UKM',
        time: '07.00 - 21.00',
    },
];

const filteredFacilities = computed(() => {
    return facilitiesList.filter((item) => {
        const matchesSearch = !searchQuery.value ||
            item.title.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.location.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            item.faculty.toLowerCase().includes(searchQuery.value.toLowerCase());
        const matchesBuilding = selectedBuilding.value === 'all' || item.location.toLowerCase().includes(selectedBuilding.value.toLowerCase());
        return matchesSearch && matchesBuilding;
    });
});

const scrollToCatalog = () => {
    const el = document.getElementById('katalog');
    if (el) {
        el.scrollIntoView({ behavior: 'smooth' });
    }
};
</script>

<template>
    <Head title="SIM-SARPRAS - Universitas Al-Khairiyah" />

    <div class="min-h-screen bg-white text-slate-800 font-sans selection:bg-[#1d64ec] selection:text-white flex flex-col justify-between">
        <!-- 1. Header / Navbar (Clean White with Solid Blue Action Button) -->
        <header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-100 shadow-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-8 py-3.5 flex items-center justify-between">
                <!-- Brand Logo & Name -->
                <Link href="/" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-[#1d64ec] text-white flex items-center justify-center shadow-md shadow-blue-500/25 group-hover:scale-105 transition transform">
                        <ShieldCheck class="w-6 h-6 stroke-[2.2]" />
                    </div>
                    <div>
                        <div class="text-base font-black tracking-tight text-slate-900 leading-tight">
                            SIM-SARPRAS
                        </div>
                        <div class="text-[11px] font-semibold text-slate-400">
                            Universitas Al-Khairiyah
                        </div>
                    </div>
                </Link>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-xs font-bold text-slate-600">
                    <a href="#beranda" class="hover:text-[#1d64ec] transition">Beranda</a>
                    <a href="#kategori" class="hover:text-[#1d64ec] transition">Kategori Fasilitas</a>
                    <a href="#mobile" class="hover:text-[#1d64ec] transition">Fitur Mobile</a>
                    <a href="#katalog" class="hover:text-[#1d64ec] transition">Katalog Peminjaman</a>
                    <a href="#kontak" class="hover:text-[#1d64ec] transition">Kontak Sarpras</a>
                </nav>

                <!-- Right Action Button (Solid Vibrant Blue) -->
                <div class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1d64ec] hover:bg-blue-700 text-white rounded-xl text-xs font-extrabold shadow-md shadow-blue-500/25 transition transform hover:-translate-y-0.5"
                    >
                        <span>Dashboard Saya</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1d64ec] hover:bg-blue-700 text-white rounded-xl text-xs font-extrabold shadow-md shadow-blue-500/25 transition transform hover:-translate-y-0.5"
                        >
                            <LogIn class="w-3.5 h-3.5" />
                            <span>Masuk Portal SSO</span>
                        </Link>
                    </template>

                    <!-- Mobile Menu Hamburger -->
                    <button
                        @click="showingMobileMenu = !showingMobileMenu"
                        class="p-2 text-slate-600 hover:bg-slate-100 rounded-xl md:hidden focus:outline-none"
                        aria-label="Menu"
                    >
                        <Menu v-if="!showingMobileMenu" class="w-5 h-5" />
                        <X v-else class="w-5 h-5" />
                    </button>
                </div>
            </div>

            <!-- Mobile Menu Dropdown -->
            <div 
                v-if="showingMobileMenu" 
                class="md:hidden px-4 py-3 bg-white border-t border-slate-100 space-y-2 text-xs font-bold"
            >
                <a href="#beranda" @click="showingMobileMenu = false" class="block p-2 text-slate-700 hover:bg-slate-50 rounded-lg">Beranda</a>
                <a href="#kategori" @click="showingMobileMenu = false" class="block p-2 text-slate-700 hover:bg-slate-50 rounded-lg">Kategori Fasilitas</a>
                <a href="#mobile" @click="showingMobileMenu = false" class="block p-2 text-slate-700 hover:bg-slate-50 rounded-lg">Fitur Mobile</a>
                <a href="#katalog" @click="showingMobileMenu = false" class="block p-2 text-slate-700 hover:bg-slate-50 rounded-lg">Katalog Peminjaman</a>
                <a href="#kontak" @click="showingMobileMenu = false" class="block p-2 text-slate-700 hover:bg-slate-50 rounded-lg">Kontak Sarpras</a>
            </div>
        </header>

        <!-- 2. Hero Section (Split Layout with Blue-bordered Search Widget & Rounded Photo Arch) -->
        <section id="beranda" class="relative overflow-hidden bg-gradient-to-b from-blue-50/60 via-white to-white pt-8 sm:pt-14 pb-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-8 grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <!-- Left Column: Headline, Description & Distinctive Search Box Widget -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100/80 text-[#1d64ec] text-xs font-bold">
                        <Sparkles class="w-3.5 h-3.5" />
                        <span>Sistem Informasi Manajemen Sarana & Prasarana Kampus</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-tight">
                        Kelola Sarana & <br class="hidden sm:inline" />
                        <span class="text-[#1d64ec]">Fasilitas Kampus</span> Terpadu
                    </h1>

                    <p class="text-sm sm:text-base text-slate-500 leading-relaxed max-w-xl font-normal">
                        Akses satu pintu untuk seluruh civitas akademika Universitas Al-Khairiyah dalam reservasi ruang kuliah, peminjaman alat Tridharma, pelaporan kendala teknisi, serta sensus inventaris bersertifikat QR Code.
                    </p>

                    <!-- The Distinctive Blue-Bordered Search Box Widget (Matches Reference Model) -->
                    <div class="bg-white border-2 border-[#1d64ec] rounded-2xl p-3 sm:p-4 shadow-xl shadow-blue-500/10 space-y-3 max-w-xl">
                        <!-- Top Row: 2 Dropdown / Select Inputs -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <MapPin class="w-4 h-4 text-[#1d64ec]" />
                                </div>
                                <select
                                    v-model="selectedBuilding"
                                    class="w-full pl-9 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#1d64ec] appearance-none"
                                >
                                    <option value="all">Semua Gedung Kampus</option>
                                    <option value="FT & FIK">Gedung FT & FIK</option>
                                    <option value="Rektorat">Gedung Rektorat</option>
                                    <option value="Pascasarjana">Gedung Pascasarjana</option>
                                    <option value="FEB">Gedung FEB</option>
                                    <option value="Lab Teknik">Gedung Lab Teknik</option>
                                    <option value="GOR">Kompleks GOR</option>
                                </select>
                                <ChevronDown class="w-4 h-4 text-slate-400 absolute right-3 top-3 pointer-events-none" />
                            </div>

                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                    <Building2 class="w-4 h-4 text-[#1d64ec]" />
                                </div>
                                <select
                                    v-model="selectedType"
                                    class="w-full pl-9 pr-8 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#1d64ec] appearance-none"
                                >
                                    <option value="all">Semua Jenis Fasilitas</option>
                                    <option value="lab">Laboratorium & Komputer</option>
                                    <option value="ruang">Ruang Perkuliahan</option>
                                    <option value="aula">Auditorium & Aula</option>
                                    <option value="alat">Alat Praktikum & Proyektor</option>
                                </select>
                                <ChevronDown class="w-4 h-4 text-slate-400 absolute right-3 top-3 pointer-events-none" />
                            </div>
                        </div>

                        <!-- Bottom Row: Search Text Input + Vibrant Orange Search Button -->
                        <div class="flex flex-col sm:flex-row items-center gap-2 pt-1">
                            <div class="relative w-full">
                                <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <input
                                    type="text"
                                    v-model="searchQuery"
                                    placeholder="Ketik nama ruangan, lab, proyektor, atau alat..."
                                    class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium text-slate-800 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-1 focus:ring-[#1d64ec]"
                                    @keyup.enter="scrollToCatalog"
                                />
                            </div>
                            <button
                                type="button"
                                @click="scrollToCatalog"
                                class="w-full sm:w-auto px-6 py-2.5 bg-[#ff6514] hover:bg-[#ea580c] text-white rounded-xl text-xs font-black shadow-md shadow-orange-500/25 transition whitespace-nowrap flex items-center justify-center gap-1.5"
                            >
                                <Search class="w-3.5 h-3.5" />
                                <span>Cari Fasilitas</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Campus Staff/Student Visual with Rounded Arch & Floating Badges -->
                <div class="lg:col-span-5 relative flex items-center justify-center">
                    <!-- The Background Blue Arch (Matches Reference Model) -->
                    <div class="relative w-72 sm:w-84 h-84 sm:h-96 rounded-t-full bg-gradient-to-b from-[#1d64ec] to-[#0d47a1] shadow-2xl flex items-end justify-center overflow-hidden">
                        <!-- Stylized Campus Officers Illustration -->
                        <div class="relative z-10 w-full h-full flex items-end justify-center">
                            <!-- SVG Silhouette / Illustrative Person -->
                            <div class="text-white/90 text-center pb-8 px-4 space-y-3">
                                <div class="w-24 h-24 mx-auto rounded-full bg-white/20 backdrop-blur-md border border-white/30 flex items-center justify-center shadow-lg">
                                    <GraduationCap class="w-12 h-12 text-white" />
                                </div>
                                <div class="space-y-1">
                                    <div class="font-black text-lg text-white">Civitas Terpadu</div>
                                    <div class="text-xs text-blue-100 max-w-[200px] mx-auto">
                                        Pelayanan Cepat, Tepat & Bebas-Bentrok
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Inner ambient light -->
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none"></div>
                    </div>

                    <!-- Floating Badge Top-Left (Verified Biro Sarpras Badge) -->
                    <div class="absolute -top-3 left-2 sm:left-4 bg-white/95 backdrop-blur-md rounded-2xl p-3 border border-slate-200/80 shadow-lg shadow-slate-200/50 flex items-center gap-2.5 animate-bounce-slow">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#1d64ec] flex items-center justify-center shrink-0">
                            <ShieldCheck class="w-5 h-5 stroke-[2.2]" />
                        </div>
                        <div>
                            <div class="text-[11px] font-black text-slate-900 leading-none">Biro Sarpras Terverifikasi</div>
                            <div class="text-[9px] font-semibold text-emerald-600 mt-0.5">Akreditasi Mutu Kampus</div>
                        </div>
                    </div>

                    <!-- Floating Badge Bottom-Right (Active Civitas Avatars) -->
                    <div class="absolute -bottom-4 right-2 sm:right-4 bg-white/95 backdrop-blur-md rounded-2xl p-3 border border-slate-200/80 shadow-lg shadow-slate-200/50 flex items-center gap-3">
                        <div class="flex -space-x-2 overflow-hidden">
                            <span class="inline-block h-7 w-7 rounded-full ring-2 ring-white bg-blue-600 text-white text-[9px] font-black flex items-center justify-center">DS</span>
                            <span class="inline-block h-7 w-7 rounded-full ring-2 ring-white bg-indigo-600 text-white text-[9px] font-black flex items-center justify-center">MH</span>
                            <span class="inline-block h-7 w-7 rounded-full ring-2 ring-white bg-emerald-600 text-white text-[9px] font-black flex items-center justify-center">TK</span>
                            <span class="inline-block h-7 w-7 rounded-full ring-2 ring-white bg-amber-600 text-white text-[9px] font-black flex items-center justify-center">LB</span>
                        </div>
                        <div>
                            <div class="text-[11px] font-black text-slate-900 leading-none">5.000+ Civitas</div>
                            <div class="text-[9px] text-slate-400 mt-0.5">Pengguna Aktif Kampus</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. Ribbon / Logo Marquee Bar (Solid Royal Blue Bar Across Full Width) -->
        <section class="bg-[#1d64ec] text-white py-4 shadow-md overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-8">
                <div class="flex flex-wrap items-center justify-between gap-4 sm:gap-8 text-xs font-black tracking-widest uppercase opacity-95 text-center">
                    <span class="hover:text-cyan-200 transition">Universitas Al-Khairiyah</span>
                    <span class="hidden sm:inline text-blue-300">•</span>
                    <span class="hover:text-cyan-200 transition">Fakultas Teknik</span>
                    <span class="hidden sm:inline text-blue-300">•</span>
                    <span class="hover:text-cyan-200 transition">Fakultas Ilmu Komputer</span>
                    <span class="hidden sm:inline text-blue-300">•</span>
                    <span class="hover:text-cyan-200 transition">Fakultas Ekonomi & Bisnis</span>
                    <span class="hidden sm:inline text-blue-300">•</span>
                    <span class="hover:text-cyan-200 transition">Program Pascasarjana</span>
                    <span class="hidden sm:inline text-blue-300">•</span>
                    <span class="hover:text-cyan-200 transition">Biro Sarana & Prasarana</span>
                </div>
            </div>
        </section>

        <!-- 4. Popular Categories Grid (Matches "Popular Jobs Categories" in Reference) -->
        <section id="kategori" class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-8 space-y-10">
                <!-- Section Header -->
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Kategori Sarana & <span class="text-[#1d64ec]">Prasarana Populer</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-normal">
                        Jelajahi seluruh fasilitas penunjang akademik, laboratorium, dan sirkulasi logistik universitas.
                    </p>
                </div>

                <!-- 8 Category Cards (4 cols x 2 rows) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <div
                        v-for="cat in popularCategories"
                        :key="cat.title"
                        class="p-5 rounded-2xl border border-slate-100 hover:border-blue-200 hover:shadow-lg hover:shadow-blue-500/5 transition duration-200 bg-white group cursor-pointer"
                        @click="searchQuery = cat.title.split(' ')[0]; scrollToCatalog()"
                    >
                        <div class="flex items-center gap-4">
                            <div :class="['w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform', cat.color]">
                                <component :is="cat.icon" class="w-6 h-6 stroke-[2]" />
                            </div>
                            <div>
                                <h3 class="text-xs sm:text-sm font-bold text-slate-900 group-hover:text-[#1d64ec] transition">
                                    {{ cat.title }}
                                </h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    {{ cat.desc }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. App / Mobile Feature Banner (Matches "Your dream job is just one click away" in Reference) -->
        <section id="mobile" class="py-10 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-8">
                <!-- The Wide Royal Blue Curved Card with Smartphone Mockups -->
                <div class="relative bg-gradient-to-r from-[#1d64ec] via-[#1a56cc] to-[#1242a8] rounded-3xl p-8 sm:p-12 lg:p-14 text-white shadow-2xl overflow-hidden grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- Left Content -->
                    <div class="lg:col-span-7 space-y-5 relative z-10">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-cyan-200 text-xs font-bold border border-white/15">
                            <Smartphone class="w-3.5 h-3.5" />
                            <span>Akses Mobile & PWA Responsive</span>
                        </span>

                        <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                            Layanan Sarpras Kini <br />
                            Hadir Dalam Genggaman.
                        </h2>

                        <p class="text-xs sm:text-sm text-blue-100/90 leading-relaxed max-w-lg font-normal">
                            Reservasi ruangan kelas, peminjaman instrumen praktikum, dan pemindaian QR Code sensus inventaris kini dapat dilakukan langsung melalui perangkat seluler mahasiswa dan dosen tanpa birokrasi manual.
                        </p>

                        <!-- Vibrant Orange Button -->
                        <div class="pt-2">
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-[#ff6514] hover:bg-[#ea580c] text-white rounded-xl text-xs sm:text-sm font-black shadow-lg shadow-orange-500/30 transition transform hover:-translate-y-0.5"
                            >
                                <span>Akses Portal Sekarang</span>
                                <ArrowRight class="w-4 h-4" />
                            </Link>
                        </div>
                    </div>

                    <!-- Right Mockup of 3 Smartphone Frames (Matches Reference) -->
                    <div class="lg:col-span-5 relative flex items-center justify-center gap-2 sm:gap-3 py-4">
                        <!-- Phone 1 (QR Scanner) -->
                        <div class="w-28 sm:w-32 h-52 sm:h-60 rounded-2xl bg-slate-900 border-2 border-white/40 shadow-xl p-2 flex flex-col justify-between transform -rotate-3 scale-95 opacity-80 sm:opacity-90">
                            <div class="w-8 h-1 bg-white/40 rounded-full mx-auto"></div>
                            <div class="bg-blue-900/60 rounded-xl p-2 text-center my-auto space-y-1">
                                <QrCode class="w-8 h-8 text-cyan-300 mx-auto" />
                                <div class="text-[8px] font-bold text-white">Scan QR Aset</div>
                            </div>
                            <div class="text-[7px] text-center text-blue-200">SIM-SARPRAS</div>
                        </div>

                        <!-- Phone 2 (Center Primary Screen - Booking Dashboard) -->
                        <div class="w-32 sm:w-36 h-60 sm:h-72 rounded-2xl bg-white text-slate-900 shadow-2xl p-2.5 flex flex-col justify-between border-2 border-white z-10 transform scale-105">
                            <div class="flex items-center justify-between text-[8px] font-bold text-slate-400">
                                <span>09:41</span>
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            </div>
                            <div class="space-y-1.5 my-auto">
                                <div class="h-6 rounded-lg bg-blue-50 text-[#1d64ec] flex items-center px-2 text-[9px] font-bold">
                                    Reservasi Ruangan
                                </div>
                                <div class="h-10 rounded-lg bg-slate-50 border border-slate-100 p-1.5 space-y-1">
                                    <div class="h-2 w-16 bg-slate-200 rounded"></div>
                                    <div class="h-2 w-10 bg-blue-300 rounded"></div>
                                </div>
                                <div class="h-10 rounded-lg bg-slate-50 border border-slate-100 p-1.5 space-y-1">
                                    <div class="h-2 w-14 bg-slate-200 rounded"></div>
                                    <div class="h-2 w-8 bg-emerald-300 rounded"></div>
                                </div>
                            </div>
                            <div class="h-5 rounded-lg bg-[#1d64ec] text-white text-[8px] font-bold flex items-center justify-center">
                                Booking Cepat
                            </div>
                        </div>

                        <!-- Phone 3 (Ticket Detail) -->
                        <div class="w-28 sm:w-32 h-52 sm:h-60 rounded-2xl bg-slate-900 border-2 border-white/40 shadow-xl p-2 flex flex-col justify-between transform rotate-3 scale-95 opacity-80 sm:opacity-90">
                            <div class="w-8 h-1 bg-white/40 rounded-full mx-auto"></div>
                            <div class="bg-blue-900/60 rounded-xl p-2 text-center my-auto space-y-1">
                                <CheckCircle2 class="w-8 h-8 text-emerald-400 mx-auto" />
                                <div class="text-[8px] font-bold text-white">BAP Digital Disetujui</div>
                            </div>
                            <div class="text-[7px] text-center text-blue-200">Selesai</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. Card Grid Showcase ("Explore Popular Jobs" style in Reference) -->
        <section id="katalog" class="py-16 bg-slate-50/60">
            <div class="max-w-7xl mx-auto px-4 sm:px-8 space-y-10">
                <!-- Section Header -->
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                        Jelajahi <span class="text-[#1d64ec]">Fasilitas & Ruangan</span> Terfavorit
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-normal">
                        Pilih fasilitas kampus yang siap digunakan untuk perkuliahan, riset, atau kegiatan kemahasiswaan.
                    </p>
                </div>

                <!-- 6 Cards Grid (3 columns x 2 rows) -->
                <!-- Note: The FIRST card is HIGHLIGHTED IN SOLID BLUE with ORANGE BUTTON, exactly like the reference template! -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="(item, idx) in filteredFacilities"
                        :key="item.id"
                        :class="[
                            'rounded-2xl p-6 transition-all duration-200 flex flex-col justify-between',
                            item.featured 
                                ? 'bg-[#1d64ec] text-white shadow-xl shadow-blue-500/25 ring-2 ring-blue-600' 
                                : 'bg-white text-slate-800 border border-slate-200/80 hover:shadow-lg hover:border-blue-200'
                        ]"
                    >
                        <div class="space-y-4">
                            <!-- Card Header: Faculty Badge & Category -->
                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-2">
                                    <div :class="['w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs', item.featured ? 'bg-white/20 text-white' : 'bg-blue-50 text-[#1d64ec]']">
                                        <DoorOpen class="w-4 h-4" />
                                    </div>
                                    <span :class="['text-[11px] font-bold truncate max-w-[170px]', item.featured ? 'text-blue-100' : 'text-slate-500']">
                                        {{ item.faculty }}
                                    </span>
                                </div>
                                <span :class="['text-[9px] font-extrabold uppercase px-2 py-0.5 rounded-full', item.featured ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-600']">
                                    {{ item.time }}
                                </span>
                            </div>

                            <!-- Title & Location -->
                            <div>
                                <h3 :class="['text-base font-black leading-snug', item.featured ? 'text-white' : 'text-slate-900']">
                                    {{ item.title }}
                                </h3>
                                <p :class="['text-xs mt-1.5 leading-relaxed', item.featured ? 'text-blue-100' : 'text-slate-500']">
                                    {{ item.desc }}
                                </p>
                            </div>

                            <!-- Location Specs -->
                            <div :class="['flex items-center gap-2 text-xs pt-1', item.featured ? 'text-blue-200' : 'text-slate-400']">
                                <MapPin class="w-3.5 h-3.5 shrink-0" />
                                <span class="truncate">{{ item.location }}</span>
                            </div>
                        </div>

                        <!-- Card Footer: Capacity Tag & Action Button -->
                        <div :class="['pt-5 mt-5 border-t flex items-center justify-between gap-2', item.featured ? 'border-white/15' : 'border-slate-100']">
                            <div>
                                <div :class="['text-[11px] font-black', item.featured ? 'text-white' : 'text-[#1d64ec]']">
                                    {{ item.capacity }}
                                </div>
                                <div :class="['text-[10px]', item.featured ? 'text-blue-200' : 'text-slate-400']">
                                    {{ item.duration }}
                                </div>
                            </div>

                            <!-- Button: Solid Orange for featured card, Solid Blue for standard cards -->
                            <Link
                                :href="route('login')"
                                :class="[
                                    'px-4 py-2 rounded-xl text-xs font-bold transition transform active:scale-95 shrink-0',
                                    item.featured 
                                        ? 'bg-[#ff6514] hover:bg-[#ea580c] text-white shadow-md shadow-orange-500/30' 
                                        : 'bg-[#1d64ec] hover:bg-blue-700 text-white'
                                ]"
                            >
                                <span>Ajukan Pinjam</span>
                            </Link>
                        </div>
                    </div>
                </div>

                <div v-if="filteredFacilities.length === 0" class="p-8 text-center bg-white rounded-2xl border border-slate-200">
                    <p class="text-sm text-slate-500 font-medium">Tidak ada fasilitas yang sesuai dengan pencarian Anda.</p>
                    <button @click="searchQuery = ''; selectedBuilding = 'all'" class="mt-2 text-xs font-bold text-[#1d64ec] hover:underline">
                        Tampilkan Semua Fasilitas
                    </button>
                </div>
            </div>
        </section>

        <!-- 7. Bottom CTA Banner (Matches "Start Your Career by Upload Your Awesome Resume" in Reference) -->
        <section class="py-14 bg-[#1d64ec] text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- Left Side: Headline, Paragraph, Orange CTA Button -->
                    <div class="lg:col-span-7 space-y-6">
                        <h2 class="text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                            Mulai Pengajuan Fasilitas <br class="hidden sm:inline" />
                            Kampus Anda Sekarang.
                        </h2>

                        <p class="text-xs sm:text-sm text-blue-100/90 leading-relaxed max-w-lg font-normal">
                            Nikmati kemudahan proses perizinan sarana prasarana 100% digital tanpa dokumen fisik dan tanpa birokrasi manual yang rumit. Hubungi helpdesk jika memerlukan penanganan acara berskala universitas.
                        </p>

                        <!-- Orange CTA Button (Matches Reference) -->
                        <div class="pt-2">
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-[#ff6514] hover:bg-[#ea580c] text-white rounded-xl text-xs sm:text-sm font-black shadow-lg shadow-orange-500/30 transition transform hover:-translate-y-0.5"
                            >
                                <span>Masuk ke Akun Anda</span>
                                <ArrowRight class="w-4 h-4" />
                            </Link>
                        </div>
                    </div>

                    <!-- Right Side: Framed Visual Card with Floating Badge (Matches Reference) -->
                    <div class="lg:col-span-5 relative flex items-center justify-center">
                        <div class="relative w-full max-w-md h-64 sm:h-72 rounded-3xl bg-gradient-to-tr from-[#0f2d7a] to-[#1a56cc] border-2 border-white/30 shadow-2xl p-6 flex flex-col justify-between overflow-hidden">
                            <!-- Ambient Light -->
                            <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-cyan-400/20 rounded-full blur-2xl pointer-events-none"></div>

                            <div class="space-y-3 relative z-10">
                                <span class="inline-block px-3 py-1 rounded-full bg-white/20 text-cyan-200 text-xs font-bold">
                                    Biro Sarana & Prasarana
                                </span>
                                <div class="text-xl font-black text-white leading-tight">
                                    Komitmen Layanan Cepat, Transparan & Akuntabel.
                                </div>
                                <p class="text-xs text-blue-100 leading-relaxed">
                                    Melayani kebutuhan 6 fakultas, 64 ruang kuliah terpadu, dan 14.800+ aset inventaris kampus.
                                </p>
                            </div>

                            <!-- Overlapping Floating Card at bottom (Matches Reference Badge) -->
                            <div class="relative z-10 bg-white text-slate-800 rounded-2xl p-3 shadow-lg flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1d64ec] flex items-center justify-center shrink-0">
                                    <ShieldCheck class="w-5 h-5 stroke-[2.2]" />
                                </div>
                                <div>
                                    <div class="text-xs font-black text-slate-900">SOP Terstandar Mutu</div>
                                    <div class="text-[10px] text-slate-400">Universitas Al-Khairiyah</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 8. Clean University Footer -->
        <footer id="kontak" class="bg-white border-t border-slate-100 py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-8 space-y-8 text-xs text-slate-500">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                    <!-- Brand info -->
                    <div class="md:col-span-2 space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#1d64ec] text-white flex items-center justify-center font-bold text-xs shadow-xs">
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
                            <li><a href="#katalog" class="hover:text-[#1d64ec] transition">Peminjaman Ruang Kuliah</a></li>
                            <li><a href="#katalog" class="hover:text-[#1d64ec] transition">Sirkulasi Alat Praktikum</a></li>
                            <li><a href="#kategori" class="hover:text-[#1d64ec] transition">Helpdesk Pemeliharaan</a></li>
                            <li><a href="#kategori" class="hover:text-[#1d64ec] transition">Sensus QR Barcode Aset</a></li>
                        </ul>
                    </div>

                    <!-- University Address -->
                    <div class="space-y-2.5">
                        <div class="font-bold text-slate-800 text-xs">Kampus Utama</div>
                        <div class="text-xs text-slate-500 space-y-1">
                            <div>Gedung Rektorat Lt. 1</div>
                            <div>Jl. H. Enggus Arja No. 1, Citangkil, Kota Cilegon</div>
                            <div class="pt-1 text-[#1d64ec] font-semibold">sarpras@kampus.ac.id</div>
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3 text-[11px] text-slate-400">
                    <div>
                        &copy; {{ new Date().getFullYear() }} Universitas Al-Khairiyah. Seluruh hak cipta dilindungi undang-undang.
                    </div>
                    <div class="flex items-center gap-4">
                        <span>Standar Akreditasi BAN-PT</span>
                        <span>•</span>
                        <span>Single Sign-On (SSO) Terintegrasi</span>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
@keyframes bounceSlow {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-6px);
    }
}
.animate-bounce-slow {
    animation: bounceSlow 4s ease-in-out infinite;
}
</style>
