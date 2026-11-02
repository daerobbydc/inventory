<script setup>
import { ref } from 'vue';
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
    ArrowUpRight
} from 'lucide-vue-next';

const props = defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    stats: {
        type: Object,
        default: () => ({
            totalAssets: 0,
            totalRooms: 0,
            totalBuildings: 0,
            totalFaculties: 0,
            activeLoans: 0,
        }),
    },
});

const showingMobileMenu = ref(false);
const activeFaq = ref(null);

const toggleFaq = (index) => {
    activeFaq.value = activeFaq.value === index ? null : index;
};

const services = [
    {
        title: 'Peminjaman Ruangan & Lab',
        desc: 'Sistem reservasi ruang kelas, ruang seminar, dan laboratorium secara digital dengan kalender anti-bentrok serta approval langsung PIC ruangan.',
        icon: DoorOpen,
        color: 'from-[#1a73e8] to-[#4285f4]',
        badge: 'Reservasi Online',
        route: 'room-bookings.index',
    },
    {
        title: 'Peminjaman Aset & Alat',
        desc: 'Peminjaman perangkat penunjang Tridharma seperti proyektor portable, audio system, dan instrumen laboratorium dengan berita acara digital.',
        icon: Repeat,
        color: 'from-[#10b981] to-[#059669]',
        badge: 'Sirkulasi Alat',
        route: 'loans.index',
    },
    {
        title: 'Helpdesk & Servis Fasilitas',
        desc: 'Pelaporan kendala kerusakan AC, proyektor, kelistrikan, dan mebeler dengan SLA respons teknisi kampus secara transparan dan termonitor.',
        icon: Wrench,
        color: 'from-[#ec4899] to-[#e11d48]',
        badge: 'Respon Cepat',
        route: 'maintenance.create',
    },
    {
        title: 'Sensus & Audit QR Code',
        desc: 'Pelabelan QR Code unik per unit inventaris di seluruh gedung universitas guna akurasi pelacakan lokasi, kondisi aset, dan audit akreditasi.',
        icon: QrCode,
        color: 'from-[#1e293b] to-[#0f172a]',
        badge: 'Standar Akreditasi',
        route: 'assets.index',
    },
];

const procedures = [
    {
        step: '01',
        title: 'Pengajuan Layanan Digital',
        desc: 'Pilih jenis layanan (pinjam ruang, pinjam alat, atau lapor servis), lengkapi formulir kebutuhan dan jadwal kegiatan.',
    },
    {
        step: '02',
        title: 'Verifikasi & Validasi PIC',
        desc: 'Pengelola laboratorium atau penanggung jawab gedung memvalidasi ketersediaan dan menyetujui secara berjenjang.',
    },
    {
        step: '03',
        title: 'Serah Terima & Pemakaian',
        desc: 'Penerimaan alat atau akses ruangan dengan verifikasi kode reservasi / scan QR di lokasi kampus.',
    },
    {
        step: '04',
        title: 'Inspeksi Kembali & Selesai',
        desc: 'Pengecekan kelengkapan kondisi fisik fasilitas setelah penggunaan, dan penutupan tiket secara otomatis.',
    },
];

const facultiesList = [
    { name: 'Fakultas Teknik', code: 'FT', desc: 'Lab Komputasi, Mesin, Sipil & Elektro' },
    { name: 'Fakultas Ekonomi & Bisnis', code: 'FEB', desc: 'Ruang Seminar, Lab Manajemen & Akuntansi' },
    { name: 'Fakultas Agama Islam', code: 'FAI', desc: 'Aula Syariah, Tarbiyah & Studi Islam' },
    { name: 'Fakultas Ilmu Komputer', code: 'FIK', desc: 'Pusat Riset AI, Jaringan & Multimedia' },
    { name: 'Program Pascasarjana', code: 'PPs', desc: 'Ruang Kolokium & Teater Akademik' },
    { name: 'Gedung Rektorat & Layanan', code: 'REK', desc: 'Auditorium Utama & Ruang Pertemuan' },
];

const faqs = [
    {
        q: 'Siapa saja yang berhak mengajukan peminjaman sarana prasarana?',
        a: 'Seluruh civitas akademika Universitas Al-Khairiyah yang aktif (Dosen, Mahasiswa, Organisasi Mahasiswa/BEM/HIMA, dan Tenaga Kependidikan) dengan login menggunakan akun SSO kampus.',
    },
    {
        q: 'Berapa hari sebelum acara permohonan ruangan harus diajukan?',
        a: 'Pengajuan permohonan ruangan disarankan diajukan minimal H-3 sebelum tanggal kegiatan agar PIC Ruangan dan Tim Sarpras dapat memverifikasi ketersediaan dan kesiapan fasilitas pendukung.',
    },
    {
        q: 'Bagaimana cara memantau status peminjaman atau tiket servis yang telah diajukan?',
        a: 'Setelah mengajukan, Anda dapat melihat pembaruan status secara langsung di Dashboard akun Anda pada tab Peminjaman Aset, Reservasi Ruangan, atau Riwayat Tiket Pemeliharaan.',
    },
    {
        q: 'Apakah peminjaman alat laboratorium memerlukan rekomendasi dosen pembimbing?',
        a: 'Untuk kegiatan riset atau praktikum mandiri mahasiswa di luar jadwal mata kuliah reguler, peminjaman instrumen lab khusus memerlukan persetujuan Dosen Pembimbing atau Kepala Laboratorium terkait.',
    },
];
</script>

<template>
    <Head title="Portal Resmi Sarana & Prasarana - Universitas Al-Khairiyah" />

    <div class="min-h-screen bg-[#f0f2f5] text-slate-800 font-sans antialiased selection:bg-[#1a73e8] selection:text-white flex flex-col justify-between">
        <!-- 1. Floating Production Navigation Bar -->
        <header class="sticky top-4 z-50 mx-4 sm:mx-8 lg:mx-12">
            <nav class="bg-white/90 backdrop-blur-md rounded-2xl border border-slate-200/80 shadow-md px-5 py-3 flex items-center justify-between transition-all">
                <!-- Brand Logo & University Title -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#1a73e8] to-[#4285f4] flex items-center justify-center text-white shadow-md shadow-blue-500/25 shrink-0">
                        <ShieldCheck class="w-6 h-6 stroke-[2.2]" />
                    </div>
                    <div>
                        <div class="text-sm font-extrabold tracking-tight text-slate-900 leading-tight">
                            SIM-SARPRAS
                        </div>
                        <div class="text-[11px] font-semibold text-slate-400">
                            Universitas Al-Khairiyah
                        </div>
                    </div>
                </div>

                <!-- Desktop Nav Anchors -->
                <div class="hidden md:flex items-center gap-6 text-xs font-bold text-slate-600">
                    <a href="#layanan" class="hover:text-[#1a73e8] transition">Layanan</a>
                    <a href="#prosedur" class="hover:text-[#1a73e8] transition">Alur Pengajuan</a>
                    <a href="#fakultas" class="hover:text-[#1a73e8] transition">Fakultas & Fasilitas</a>
                    <a href="#faq" class="hover:text-[#1a73e8] transition">FAQ</a>
                    <a href="#kontak" class="hover:text-[#1a73e8] transition">Kontak Helpdesk</a>
                </div>

                <!-- Right Auth Action -->
                <div class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-tr from-[#1a73e8] to-[#4285f4] hover:opacity-95 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/25 transition"
                    >
                        <span>Dashboard Saya</span>
                        <ArrowRight class="w-3.5 h-3.5" />
                    </Link>

                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-tr from-[#1a73e8] to-[#4285f4] hover:opacity-95 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-500/25 transition"
                        >
                            <LogIn class="w-3.5 h-3.5" />
                            <span>Masuk Portal SSO</span>
                        </Link>
                    </template>

                    <!-- Mobile Hamburger -->
                    <button
                        @click="showingMobileMenu = !showingMobileMenu"
                        class="p-2 text-slate-600 hover:bg-slate-100 rounded-lg md:hidden"
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
                class="md:hidden mt-2 p-4 bg-white rounded-2xl border border-slate-200/90 shadow-xl space-y-3"
            >
                <a 
                    href="#layanan" 
                    @click="showingMobileMenu = false"
                    class="block px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 rounded-lg"
                >
                    Layanan Sarpras
                </a>
                <a 
                    href="#prosedur" 
                    @click="showingMobileMenu = false"
                    class="block px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 rounded-lg"
                >
                    Alur & SOP Pengajuan
                </a>
                <a 
                    href="#fakultas" 
                    @click="showingMobileMenu = false"
                    class="block px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50 rounded-lg"
                >
                    Fakultas & Fasilitas
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
                    Kontak Biro Sarpras
                </a>
            </div>
        </header>

        <!-- 2. Hero Section (Production Campus Portal) -->
        <section class="max-w-7xl mx-auto px-4 sm:px-8 pt-10 sm:pt-16 pb-12 w-full space-y-10">
            <!-- Hero Banner Box with Royal Navy Deep Gradient -->
            <div class="relative bg-gradient-to-r from-[#0a1628] via-[#0f244a] to-[#1e3a8a] text-white rounded-3xl p-8 sm:p-14 shadow-xl border border-blue-900/60 overflow-hidden">
                <!-- Ambient Glow Effect -->
                <div class="absolute -right-20 -top-20 w-96 h-96 bg-blue-500/15 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

                <div class="max-w-3xl space-y-6 relative z-10">
                    <!-- Status Badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-cyan-200 border border-blue-400/30 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Portal Resmi Biro Sarana & Prasarana Kampus Terpadu</span>
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-black text-white tracking-tight leading-tight sm:leading-tight">
                        Layanan Digital Sarana & Prasarana Kampus Terintegrasi
                    </h1>

                    <p class="text-xs sm:text-base text-blue-100/90 leading-relaxed font-normal">
                        Akses satu pintu untuk seluruh civitas akademika Universitas Al-Khairiyah dalam reservasi ruang kuliah, peminjaman peralatan penunjang akademik, pelacakan inventaris berlabel QR, serta pelaporan keluhan fasilitas secara real-time.
                    </p>

                    <!-- Primary Action Buttons -->
                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <Link
                            :href="$page.props.auth?.user ? route('dashboard') : route('login')"
                            class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-gradient-to-tr from-[#1a73e8] to-[#4285f4] hover:opacity-95 text-white rounded-2xl text-xs sm:text-sm font-extrabold shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-0.5"
                        >
                            <span>{{ $page.props.auth?.user ? 'Buka Dashboard Saya' : 'Masuk Portal Civitas (SSO)' }}</span>
                            <ArrowRight class="w-4 h-4" />
                        </Link>

                        <a
                            href="#layanan"
                            class="inline-flex items-center gap-2 px-5 py-3.5 bg-white/10 hover:bg-white/15 border border-white/20 text-white rounded-2xl text-xs sm:text-sm font-bold backdrop-blur-sm transition"
                        >
                            <span>Jelajahi Layanan</span>
                        </a>

                        <a
                            href="#prosedur"
                            class="inline-flex items-center gap-2 px-5 py-3.5 text-blue-200 hover:text-white text-xs sm:text-sm font-bold transition"
                        >
                            <span>SOP Pengajuan →</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Four Live Campus Metrics (Overhanging Material Style) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 pt-4">
                <!-- Metric 1: Total Aset Terdaftar -->
                <div class="relative bg-white rounded-2xl p-5 pt-0 shadow-sm border border-slate-100 mt-4">
                    <div class="absolute -top-4 left-4 w-13 h-13 rounded-2xl bg-gradient-to-tr from-[#1e293b] to-[#0f172a] shadow-lg shadow-slate-900/30 flex items-center justify-center text-white">
                        <Layers class="w-5 h-5 stroke-[2]" />
                    </div>
                    <div class="pt-4 text-right">
                        <span class="text-xs font-semibold text-slate-500">Unit Aset Terdata</span>
                        <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                            {{ Number(stats.totalAssets || 14820).toLocaleString('id-ID') }}
                        </div>
                    </div>
                    <hr class="border-t border-slate-100 my-3" />
                    <div class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="text-emerald-500 font-bold">100%</span>
                        <span>Berlabel Stiker QR Terverifikasi</span>
                    </div>
                </div>

                <!-- Metric 2: Ruangan Kuliah & Lab -->
                <div class="relative bg-white rounded-2xl p-5 pt-0 shadow-sm border border-slate-100 mt-4">
                    <div class="absolute -top-4 left-4 w-13 h-13 rounded-2xl bg-gradient-to-tr from-[#1a73e8] to-[#4285f4] shadow-lg shadow-blue-500/30 flex items-center justify-center text-white">
                        <DoorOpen class="w-5 h-5 stroke-[2]" />
                    </div>
                    <div class="pt-4 text-right">
                        <span class="text-xs font-semibold text-slate-500">Ruangan & Lab</span>
                        <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                            {{ Number(stats.totalRooms || 64).toLocaleString('id-ID') }} Ruang
                        </div>
                    </div>
                    <hr class="border-t border-slate-100 my-3" />
                    <div class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="text-blue-600 font-bold">{{ stats.totalBuildings || 8 }} Gedung</span>
                        <span>Kuliah & Laboratorium Terpadu</span>
                    </div>
                </div>

                <!-- Metric 3: Sirkulasi & Peminjaman Aktif -->
                <div class="relative bg-white rounded-2xl p-5 pt-0 shadow-sm border border-slate-100 mt-4">
                    <div class="absolute -top-4 left-4 w-13 h-13 rounded-2xl bg-gradient-to-tr from-[#10b981] to-[#059669] shadow-lg shadow-emerald-500/30 flex items-center justify-center text-white">
                        <Repeat class="w-5 h-5 stroke-[2]" />
                    </div>
                    <div class="pt-4 text-right">
                        <span class="text-xs font-semibold text-slate-500">Peminjaman Berjalan</span>
                        <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                            {{ Number(stats.activeLoans || 18).toLocaleString('id-ID') }} Sesi
                        </div>
                    </div>
                    <hr class="border-t border-slate-100 my-3" />
                    <div class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="text-emerald-500 font-bold">Real-time</span>
                        <span>Sirkulasi Fasilitas Civitas</span>
                    </div>
                </div>

                <!-- Metric 4: Respon Pemeliharaan Fasilitas -->
                <div class="relative bg-white rounded-2xl p-5 pt-0 shadow-sm border border-slate-100 mt-4">
                    <div class="absolute -top-4 left-4 w-13 h-13 rounded-2xl bg-gradient-to-tr from-[#ec4899] to-[#e11d48] shadow-lg shadow-pink-500/30 flex items-center justify-center text-white">
                        <Wrench class="w-5 h-5 stroke-[2]" />
                    </div>
                    <div class="pt-4 text-right">
                        <span class="text-xs font-semibold text-slate-500">SLA Penanganan Servis</span>
                        <div class="text-2xl font-black text-slate-900 mt-0.5 tracking-tight">
                            &lt; 24 Jam
                        </div>
                    </div>
                    <hr class="border-t border-slate-100 my-3" />
                    <div class="text-xs text-slate-500 flex items-center gap-1">
                        <span class="text-rose-600 font-bold">Siaga Teknisi</span>
                        <span>Dukungan Tim Pemeliharaan</span>
                    </div>
                </div>
            </div>

            <!-- 4. Layanan Utama Portal (Services Section) -->
            <section id="layanan" class="pt-10 space-y-6">
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-[#1a73e8]">
                        Layanan Mandiri Terpadu
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Fasilitas Digital untuk Civitas Akademika
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Memudahkan mahasiswa, dosen, serta staf pengelola dalam pemanfaatan seluruh sarana universitas.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div
                        v-for="service in services"
                        :key="service.title"
                        class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 hover:shadow-md hover:border-blue-200 transition flex flex-col justify-between group"
                    >
                        <div class="space-y-4">
                            <div :class="['w-12 h-12 rounded-2xl bg-gradient-to-tr text-white flex items-center justify-center shadow-md', service.color]">
                                <component :is="service.icon" class="w-6 h-6 stroke-[2]" />
                            </div>

                            <div>
                                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 mb-2">
                                    {{ service.badge }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 group-hover:text-[#1a73e8] transition">
                                    {{ service.title }}
                                </h3>
                                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                    {{ service.desc }}
                                </p>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-slate-100 mt-6">
                            <Link
                                :href="route('login')"
                                class="inline-flex items-center gap-1.5 text-xs font-bold text-[#1a73e8] hover:text-blue-800 transition"
                            >
                                <span>Akses Layanan Ini</span>
                                <ArrowUpRight class="w-3.5 h-3.5" />
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 5. Alur SOP Prosedur Pengajuan (Step-by-step SOP Timeline) -->
            <section id="prosedur" class="pt-10">
                <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-sm border border-slate-100 space-y-8">
                    <div class="text-center max-w-2xl mx-auto space-y-2">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-[#1a73e8]">
                            Standar Operasional Prosedur (SOP)
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            4 Langkah Mudah Alur Peminjaman Fasilitas
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500">
                            Proses transparan, cepat, dan tanpa birokrasi manual kertas yang rumit.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 pt-4">
                        <div
                            v-for="(item, idx) in procedures"
                            :key="item.step"
                            class="relative p-5 rounded-2xl bg-slate-50/70 border border-slate-100 flex flex-col justify-between"
                        >
                            <div>
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-[#1a73e8] to-[#4285f4] text-white flex items-center justify-center font-black text-xs shadow-sm mb-4">
                                    {{ item.step }}
                                </div>
                                <h4 class="text-sm font-bold text-slate-900">{{ item.title }}</h4>
                                <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                    {{ item.desc }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50/70 rounded-2xl p-4 border border-blue-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5 text-blue-900">
                            <Sparkles class="w-4 h-4 text-[#1a73e8] shrink-0" />
                            <span><strong>Pemberitahuan Otomatis:</strong> Setiap perubahan status pengajuan akan langsung dikirimkan melalui notifikasi sistem di akun Anda.</span>
                        </div>
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center gap-1 font-bold text-[#1a73e8] hover:underline shrink-0"
                        >
                            <span>Ajukan Sekarang</span>
                            <ArrowRight class="w-3.5 h-3.5" />
                        </Link>
                    </div>
                </div>
            </section>

            <!-- 6. Fakultas & Gedung Terintegrasi -->
            <section id="fakultas" class="pt-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                    <div>
                        <span class="text-xs font-extrabold uppercase tracking-wider text-[#1a73e8]">
                            Cakupan Infrastruktur Kampus
                        </span>
                        <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5">
                            Fakultas & Unit Layanan Terdaftar
                        </h2>
                    </div>
                    <span class="text-xs text-slate-400">
                        Sinkronisasi Seluruh Gedung Kampus Universitas Al-Khairiyah
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    <div
                        v-for="fac in facultiesList"
                        :key="fac.code"
                        class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-start gap-4 hover:border-blue-200 transition"
                    >
                        <div class="w-11 h-11 rounded-xl bg-slate-100 text-[#1a73e8] font-black text-xs flex items-center justify-center shrink-0">
                            {{ fac.code }}
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-sm font-bold text-slate-900 leading-snug">{{ fac.name }}</h4>
                            <p class="text-xs text-slate-500 leading-relaxed">{{ fac.desc }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 7. Tanya Jawab (FAQ) Section -->
            <section id="faq" class="pt-8">
                <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-sm border border-slate-100 space-y-6">
                    <div class="text-center max-w-2xl mx-auto space-y-2">
                        <span class="text-xs font-extrabold uppercase tracking-wider text-[#1a73e8]">
                            Pusat Informasi Civitas
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                            Pertanyaan yang Sering Diajukan (FAQ)
                        </h2>
                    </div>

                    <div class="max-w-3xl mx-auto divide-y divide-slate-100">
                        <div
                            v-for="(item, idx) in faqs"
                            :key="idx"
                            class="py-4"
                        >
                            <button
                                @click="toggleFaq(idx)"
                                class="w-full flex items-center justify-between text-left gap-4 font-bold text-sm text-slate-800 hover:text-[#1a73e8] transition"
                            >
                                <span>{{ item.q }}</span>
                                <ChevronDown 
                                    :class="['w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0', activeFaq === idx ? 'rotate-180 text-[#1a73e8]' : '']" 
                                />
                            </button>
                            <div 
                                v-show="activeFaq === idx" 
                                class="mt-2.5 text-xs text-slate-500 leading-relaxed pr-6"
                            >
                                {{ item.a }}
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- 8. Kontak Biro Sarana & Prasarana -->
            <section id="kontak" class="pt-8">
                <div class="bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white rounded-3xl p-8 sm:p-12 shadow-xl border border-blue-800/60 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-8">
                    <div class="space-y-4 max-w-xl">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-cyan-200 text-xs font-bold">
                            <Phone class="w-3.5 h-3.5" />
                            <span>Helpdesk & Konsultasi Fasilitas</span>
                        </span>
                        <h3 class="text-2xl sm:text-3xl font-black text-white tracking-tight">
                            Butuh Bantuan atau Koordinasi Khusus Acara Besar?
                        </h3>
                        <p class="text-xs sm:text-sm text-blue-100/90 leading-relaxed">
                            Hubungi Biro Sarana dan Prasarana (Sarpras) Universitas Al-Khairiyah untuk permohonan aula utama, koordinasi teknis dies natalis, wisuda, atau seminar nasional.
                        </p>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-6 border border-white/15 space-y-3.5 shrink-0 min-w-[280px]">
                        <div class="flex items-center gap-3 text-xs text-blue-100">
                            <MapPin class="w-4 h-4 text-cyan-300 shrink-0" />
                            <span>Gedung Rektorat Lt. 1, Kampus Utama</span>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-blue-100">
                            <Mail class="w-4 h-4 text-cyan-300 shrink-0" />
                            <span>sarpras@unikha.ac.id</span>
                        </div>
                        <div class="flex items-center gap-3 text-xs text-blue-100">
                            <Clock class="w-4 h-4 text-cyan-300 shrink-0" />
                            <span>Senin - Jumat: 08.00 - 16.00 WIB</span>
                        </div>

                        <div class="pt-3 border-t border-white/10">
                            <Link
                                :href="route('login')"
                                class="w-full flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-gradient-to-tr from-[#1a73e8] to-[#4285f4] text-white font-extrabold text-xs shadow-md shadow-blue-500/25 transition"
                            >
                                <span>Masuk ke Akun Anda</span>
                                <ArrowRight class="w-3.5 h-3.5" />
                            </Link>
                        </div>
                    </div>
                </div>
            </section>
        </section>

        <!-- 9. Production Official Footer -->
        <footer class="mt-16 bg-white border-t border-slate-200/80 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-8 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <div class="flex items-center gap-3">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-tr from-[#1a73e8] to-[#4285f4] text-white flex items-center justify-center font-bold text-xs shadow-xs">
                        <ShieldCheck class="w-4 h-4" />
                    </div>
                    <div>
                        <span class="font-bold text-slate-800">SIM-SARPRAS Universitas Al-Khairiyah</span>
                        <span class="text-slate-400 ml-2">Sistem Informasi Manajemen Sarana & Prasarana</span>
                    </div>
                </div>

                <div class="text-center md:text-right text-[11px] text-slate-400">
                    &copy; {{ new Date().getFullYear() }} Universitas Al-Khairiyah. Seluruh hak cipta dilindungi undang-undang.
                </div>
            </div>
        </footer>
    </div>
</template>
