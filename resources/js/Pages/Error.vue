<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import {
    AlertTriangle,
    ShieldAlert,
    FileQuestion,
    ServerCrash,
    Construction,
    ArrowLeft,
    Home,
    LayoutDashboard,
} from 'lucide-vue-next';

const props = defineProps({
    status: {
        type: Number,
        required: true,
    },
});

const details = computed(() => {
    return {
        503: {
            title: '503: Layanan Sedang Pemeliharaan',
            heading: 'Sistem Dalam Pemeliharaan Berkala',
            description: 'Layanan SIM-SARPRAS Universitas Al-Khairiyah sedang dalam proses pemeliharaan sistem terencana untuk peningkatan keandalan dan keamanan data. Silakan kembali dalam beberapa saat.',
            icon: Construction,
            color: 'text-amber-500',
            bg: 'bg-amber-500/10',
            badge: 'bg-amber-100 text-amber-800 border-amber-200',
        },
        500: {
            title: '500: Terjadi Kesalahan Server',
            heading: 'Kendala Internal Server',
            description: 'Terjadi kendala teknis saat memproses permintaan Anda. Administrator dan tim IT Sarpras telah menerima laporan ini untuk investigasi lebih lanjut.',
            icon: ServerCrash,
            color: 'text-rose-500',
            bg: 'bg-rose-500/10',
            badge: 'bg-rose-100 text-rose-800 border-rose-200',
        },
        404: {
            title: '404: Halaman Tidak Ditemukan',
            heading: 'Halaman Tidak Ditemukan',
            description: 'Alamat URL atau data inventaris yang Anda tuju tidak tersedia, telah dipindahkan, atau Anda tidak memiliki tautan yang valid.',
            icon: FileQuestion,
            color: 'text-blue-500',
            bg: 'bg-blue-500/10',
            badge: 'bg-blue-100 text-blue-800 border-blue-200',
        },
        403: {
            title: '403: Akses Ditolak',
            heading: 'Akses Tidak Diizinkan',
            description: 'Hak akses akun Anda tidak mencakup modul atau tindakan ini. Silakan hubungi Biro Sarpras jika memerlukan eskalasi peran atau otorisasi tambahan.',
            icon: ShieldAlert,
            color: 'text-rose-500',
            bg: 'bg-rose-500/10',
            badge: 'bg-rose-100 text-rose-800 border-rose-200',
        },
    }[props.status] || {
        title: `${props.status}: Kesalahan Permintaan`,
        heading: 'Terjadi Kesalahan',
        description: 'Permintaan tidak dapat diproses. Silakan kembali ke dasbor utama atau periksa kembali instruksi Anda.',
        icon: AlertTriangle,
        color: 'text-slate-500',
        bg: 'bg-slate-500/10',
        badge: 'bg-slate-100 text-slate-800 border-slate-200',
    };
});

const goBack = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        window.location.href = '/dashboard';
    }
};
</script>

<template>
    <Head :title="details.title" />

    <div class="min-h-screen bg-[#f8fafc] flex flex-col justify-between text-slate-800 font-sans antialiased">
        <!-- Top Navigation Bar -->
        <header class="bg-white/90 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 to-sky-500 flex items-center justify-center text-white shadow-md shadow-blue-500/20 font-black text-sm">
                        AK
                    </div>
                    <div>
                        <div class="text-sm font-black text-slate-900 leading-tight">SIM-SARPRAS</div>
                        <div class="text-[10px] font-semibold text-slate-400">Universitas Al-Khairiyah</div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <Link
                        :href="route('dashboard')"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-bold rounded-lg text-slate-700 hover:text-blue-600 hover:bg-blue-50 transition"
                    >
                        <LayoutDashboard class="w-4 h-4 text-blue-600" />
                        <span>Dashboard</span>
                    </Link>
                    <Link
                        href="/"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 text-xs font-bold rounded-lg text-slate-700 hover:text-blue-600 hover:bg-blue-50 transition"
                    >
                        <Home class="w-4 h-4 text-slate-500" />
                        <span>Portal Depan</span>
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content Hero -->
        <main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-auto">
            <div class="max-w-xl w-full bg-white rounded-3xl p-8 sm:p-12 shadow-xl shadow-slate-200/50 border border-slate-100 text-center relative overflow-hidden">
                <!-- Decorative background elements -->
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-blue-100/40 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-sky-100/40 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Status Badge -->
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold border mb-6" :class="details.badge">
                    <span>STATUS {{ status }}</span>
                </div>

                <!-- Icon Illustration -->
                <div class="mx-auto w-20 h-20 rounded-2xl flex items-center justify-center mb-6 shadow-inner" :class="[details.bg, details.color]">
                    <component :is="details.icon" class="w-10 h-10 stroke-[2]" />
                </div>

                <!-- Title & Description -->
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mb-3">
                    {{ details.heading }}
                </h1>
                <p class="text-sm text-slate-500 leading-relaxed mb-8 max-w-md mx-auto">
                    {{ details.description }}
                </p>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                    <button
                        type="button"
                        @click="goBack"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition active:scale-95"
                    >
                        <ArrowLeft class="w-4 h-4 text-slate-500" />
                        <span>Halaman Sebelumnya</span>
                    </button>

                    <Link
                        :href="route('dashboard')"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-tr from-blue-700 to-sky-600 hover:from-blue-800 hover:to-sky-700 text-white text-xs font-black shadow-md shadow-blue-500/25 transition active:scale-95"
                    >
                        <LayoutDashboard class="w-4 h-4" />
                        <span>Ke Beranda Dashboard</span>
                    </Link>
                </div>

                <!-- Help Desk Footnote -->
                <div class="mt-10 pt-6 border-t border-slate-100 text-xs text-slate-400">
                    Perlu bantuan operasional sarpras? Hubungi Biro Sarana Prasarana Kampus Terpadu:
                    <a href="mailto:sarpras@kampus.ac.id" class="text-blue-600 font-bold hover:underline ml-1">sarpras@kampus.ac.id</a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-4 border-t border-slate-200/60 bg-white/50 text-center text-[11px] text-slate-400">
            &copy; 2026 Universitas Al-Khairiyah &bull; Sistem Informasi Manajemen Sarana & Prasarana
        </footer>
    </div>
</template>
