<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { 
    ShieldCheck, 
    Layers, 
    Repeat, 
    Wrench, 
    FileText, 
    ArrowRight, 
    CheckCircle2, 
    QrCode, 
    Building2, 
    LogIn,
    Calendar,
    Users
} from 'lucide-vue-next';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
});

const demoAccounts = [
    {
        role: 'Super Admin (Biro Sarpras)',
        email: 'sarpras@kampus.ac.id',
        badge: 'Akses Penuh',
        desc: 'Validasi mutasi aset, persetujuan logistik, dan rekap depresiasi aset yayasan.',
        bg: 'bg-blue-50 border-blue-200 text-blue-900'
    },
    {
        role: 'PIC Ruangan & Laboran',
        email: 'laboran.komputer@kampus.ac.id',
        badge: 'Pengelola Lab',
        desc: 'Verifikasi peminjaman alat lab, serah terima, dan stok ATK.',
        bg: 'bg-indigo-50 border-indigo-200 text-indigo-800'
    },
    {
        role: 'Teknisi Pemeliharaan',
        email: 'teknisi@kampus.ac.id',
        badge: 'Teknisi On-Duty',
        desc: 'Menerima dispatch tiket kerusakan, perbaikan, dan input suku cadang.',
        bg: 'bg-amber-50 border-amber-200 text-amber-800'
    },
    {
        role: 'Mahasiswa / BEM',
        email: 'mahasiswa@kampus.ac.id',
        badge: 'Civitas Akademika',
        desc: 'Pengajuan peminjaman auditorium, proyektor, dan sound system.',
        bg: 'bg-purple-50 border-purple-200 text-purple-800'
    }
];
</script>

<template>
    <Head title="SIM-SARPRAS - Universitas Al-Khairiyah" />

    <div class="min-h-screen bg-[#f4f7fa] text-slate-900 font-sans selection:bg-[#1e3a8a] selection:text-white">
        <!-- Top Navbar -->
        <header class="bg-white border-b border-slate-200/80 sticky top-0 z-40">
            <div class="max-w-7xl mx-auto px-4 sm:px-8 h-20 flex items-center justify-between">
                <!-- Brand -->
                <div class="flex items-center gap-3">
                    <div class="h-11 w-11 rounded-2xl bg-[#1e3a8a] flex items-center justify-center text-white shadow-md shadow-blue-900/25">
                        <ShieldCheck class="h-7 w-7 stroke-[2.2]" />
                    </div>
                    <div>
                        <div class="text-lg font-black tracking-tight text-[#1e3a8a] leading-none">SIM-SARPRAS</div>
                        <div class="text-xs font-semibold text-slate-400 mt-1">Universitas Al-Khairiyah</div>
                    </div>
                </div>

                <!-- Right Nav -->
                <div class="flex items-center gap-3">
                    <Link
                        v-if="$page.props.auth?.user"
                        :href="route('dashboard')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-900/25 transition"
                    >
                        <span>Buka Dashboard</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                    <template v-else>
                        <Link
                            :href="route('login')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-900/25 transition"
                        >
                            <LogIn class="w-4 h-4" />
                            <span>Masuk Portal</span>
                        </Link>
                    </template>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="max-w-7xl mx-auto px-4 sm:px-8 py-12 sm:py-16 space-y-12">
            <div class="text-center max-w-3xl mx-auto space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200 text-xs font-bold text-blue-900">
                    <span class="w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    <span>Sistem Operasional Real-Time • Biro Sarpras Terpadu</span>
                </div>

                <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                    Sistem Informasi Sarana & Prasarana Kampus Terpadu
                </h1>

                <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-2xl mx-auto">
                    Tata kelola aset sarana & prasarana Yayasan Al-Khairiyah, pelacakan label QR/RFID, otomasi jadwal peminjaman fasilitas akademik, dan layanan penugasan teknisi perbaikan dalam satu ekosistem enterprise terintegrasi.
                </p>

                <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                    <Link
                        :href="route('login')"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-sm font-bold shadow-md shadow-blue-900/25 transition"
                    >
                        <span>Akses Portal Sekarang</span>
                        <ArrowRight class="w-4 h-4" />
                    </Link>
                    <a
                        href="#demo-accounts"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 rounded-xl text-sm font-bold shadow-xs transition"
                    >
                        <span>Lihat Akun Demo</span>
                    </a>
                </div>
            </div>

            <!-- 4 Pastel Metric Preview Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-[#fff1f2]/80 border border-rose-100 rounded-2xl p-5 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-rose-600">Total Nilai Aset Kampus</span>
                    <div class="text-2xl font-black text-slate-900 mt-1">Rp 48,6 Miliar</div>
                    <div class="text-[11px] text-slate-500 mt-1">+4.2% dari target pengadaan TA 2024</div>
                </div>

                <div class="bg-[#fffbeb]/90 border border-amber-100 rounded-2xl p-5 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-amber-600">Total Unit Terdaftar</span>
                    <div class="text-2xl font-black text-slate-900 mt-1">14.820 Item</div>
                    <div class="text-[11px] text-slate-500 mt-1">Elektronik, Mebeler, Kendaraan & Gedung</div>
                </div>

                <div class="bg-[#f0fdf4]/90 border border-emerald-100 rounded-2xl p-5 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-emerald-700">Kondisi Aset Prima</span>
                    <div class="text-2xl font-black text-slate-900 mt-1">94.2% Laik Pakai</div>
                    <div class="text-[11px] text-slate-500 mt-1">13.960 Normal • 580 Rawat • 280 Rusak</div>
                </div>

                <div class="bg-[#eff6ff]/90 border border-blue-100 rounded-2xl p-5 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-blue-700">Peminjaman Berjalan</span>
                    <div class="text-2xl font-black text-slate-900 mt-1">38 Sesi Aktif</div>
                    <div class="text-[11px] text-slate-500 mt-1">8 Ruang Kuliah • 12 Lab • 18 Perangkat</div>
                </div>
            </div>

            <!-- Interactive Demo Accounts Section -->
            <div id="demo-accounts" class="bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-10 shadow-sm space-y-6">
                <div class="text-center max-w-xl mx-auto space-y-2">
                    <span class="text-xs font-black uppercase tracking-wider text-[#1e3a8a]">Uji Coba Langsung</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                        Coba Berbagai Hak Akses Pengguna
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500">
                        Pilih salah satu peran di bawah untuk login ke sistem. Kata sandi seluruh akun demo: <code class="px-2 py-0.5 rounded bg-slate-100 text-slate-800 font-mono font-bold">password</code>
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        v-for="acc in demoAccounts"
                        :key="acc.email"
                        class="p-5 rounded-2xl border border-slate-200/90 hover:border-[#1e3a8a] hover:shadow-md transition flex flex-col justify-between group"
                    >
                        <div>
                            <span :class="['inline-block px-2.5 py-0.5 rounded-full text-[10px] font-extrabold mb-2', acc.bg]">
                                {{ acc.badge }}
                            </span>
                            <h3 class="font-bold text-slate-900 text-sm group-hover:text-[#1e3a8a] transition">{{ acc.role }}</h3>
                            <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">{{ acc.desc }}</p>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-100">
                            <div class="text-[10px] font-mono text-slate-400 truncate">{{ acc.email }}</div>
                            <Link
                                :href="route('login')"
                                class="mt-2 w-full py-2 bg-slate-50 group-hover:bg-[#1e3a8a] text-slate-700 group-hover:text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-1.5"
                            >
                                <span>Pilih Akun Ini</span>
                                <ArrowRight class="w-3.5 h-3.5" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Feature 1 -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200/70 shadow-xs space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-[#1e3a8a] flex items-center justify-center font-bold">
                        <QrCode class="w-5 h-5" />
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">Label Stiker QR Code Inventaris</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Cetak label stiker inventaris fisik langsung dari browser dengan render client-side SVG instan untuk mempermudah sensus ruangan.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200/70 shadow-xs space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-[#1e3a8a] flex items-center justify-center font-bold">
                        <Repeat class="w-5 h-5" />
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">Alur Peminjaman 4 Tahap</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Transparansi pengajuan peminjaman fasilitas kampus mulai dari verifikasi admin, serah terima alat, hingga inspeksi kondisi saat kembali.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="p-6 bg-white rounded-2xl border border-slate-200/70 shadow-xs space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-[#1e3a8a] flex items-center justify-center font-bold">
                        <Wrench class="w-5 h-5" />
                    </div>
                    <h3 class="font-bold text-slate-900 text-base">Dispatch Teknisi & Pemeliharaan</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Lapor kerusakan sarana dengan indikator prioritas dan pantau tiket perbaikan teknisi siaga secara langsung.
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="mt-16 border-t border-slate-200/80 bg-white py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-lg bg-[#1e3a8a] text-white flex items-center justify-center font-black text-xs">
                        U
                    </div>
                    <span class="font-bold text-slate-700">SIM-SARPRAS Universitas Al-Khairiyah</span>
                </div>
                <div>
                    Sistem Inventaris Sarana & Prasarana Terpadu • Hak Cipta &copy; {{ new Date().getFullYear() }}
                </div>
            </div>
        </footer>
    </div>
</template>
