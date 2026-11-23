<script setup>
import { Link } from '@inertiajs/vue3';
import { 
    ShieldCheck, 
    ArrowLeft, 
    Lock, 
    Sparkles, 
    CheckCircle2, 
    Building2,
    QrCode,
    Clock,
    Layers
} from 'lucide-vue-next';
</script>

<template>
    <div class="min-h-screen flex flex-col lg:flex-row bg-[#f8fafc] text-slate-800 font-sans selection:bg-blue-600 selection:text-white">
        <!-- Left Hero / Institutional Brand Showcase Pane (Visible on all screens, expanded on lg) -->
        <div class="relative lg:w-5/12 xl:w-5/12 bg-gradient-to-br from-[#0a1128] via-[#111d4a] to-[#1c2541] text-white p-6 sm:p-10 lg:p-14 flex flex-col justify-between overflow-hidden shadow-2xl z-10">
            <!-- Background Decorative Lighting & Ambient Glow -->
            <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-500/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute inset-0 bg-[radial-gradient(#38bdf8_1px,transparent_1px)] [background-size:28px_28px] opacity-10 pointer-events-none"></div>

            <!-- Top Header with Brand Crest -->
            <div class="relative z-10 flex items-center justify-between">
                <Link href="/" class="inline-flex items-center gap-3.5 group">
                    <div class="h-11 w-11 rounded-2xl bg-gradient-to-tr from-blue-600 via-indigo-600 to-cyan-500 flex items-center justify-center text-white shadow-lg shadow-blue-500/30 group-hover:scale-105 transition transform duration-200">
                        <ShieldCheck class="h-6 w-6 stroke-[2.2]" />
                    </div>
                    <div>
                        <div class="text-lg font-black tracking-tight text-white leading-none">
                            SIM-SARPRAS
                        </div>
                        <div class="text-[11px] font-semibold text-blue-200/80 mt-1">
                            Universitas Al-Khairiyah
                        </div>
                    </div>
                </Link>

                <div class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[11px] font-semibold text-emerald-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Portal Resmi SSO</span>
                </div>
            </div>

            <!-- Middle Institutional Value Proposition -->
            <div class="relative z-10 my-8 lg:my-auto max-w-lg space-y-6">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/20 text-cyan-200 text-xs font-bold border border-blue-400/30 backdrop-blur-sm">
                    <Sparkles class="w-3.5 h-3.5 text-cyan-300" />
                    <span>Biro Sarana & Prasarana Kampus Terpadu</span>
                </div>

                <h1 class="text-2xl sm:text-4xl font-black text-white tracking-tight leading-tight">
                    Pengelolaan Fasilitas & Inventaris Kampus Berstandar Mutu.
                </h1>

                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed font-normal">
                    Akses satu pintu civitas akademika untuk reservasi ruang kuliah, laboratorium, sirkulasi peminjaman alat penunjang Tridharma, serta pelaporan keluhan fasilitas secara real-time dan terintegrasi.
                </p>

                <!-- 3 Pillars Feature Highlights -->
                <div class="space-y-3 pt-2">
                    <div class="flex items-center gap-3 text-xs text-slate-200 bg-white/5 p-2.5 rounded-xl border border-white/10 backdrop-blur-xs">
                        <div class="w-7 h-7 rounded-lg bg-blue-600/30 border border-blue-400/30 flex items-center justify-center text-blue-300 shrink-0">
                            <CheckCircle2 class="w-4 h-4" />
                        </div>
                        <div>
                            <span class="font-bold text-white">Single Sign-On (SSO):</span>
                            <span class="text-slate-300 ml-1">Satu akun civitas untuk akses seluruh fasilitas universitas.</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 text-xs text-slate-200 bg-white/5 p-2.5 rounded-xl border border-white/10 backdrop-blur-xs">
                        <div class="w-7 h-7 rounded-lg bg-emerald-600/30 border border-emerald-400/30 flex items-center justify-center text-emerald-300 shrink-0">
                            <QrCode class="w-4 h-4" />
                        </div>
                        <div>
                            <span class="font-bold text-white">Sensus & Audit Berbasis QR:</span>
                            <span class="text-slate-300 ml-1">Pelacakan fisik aset akurat di seluruh fakultas & gedung.</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 text-xs text-slate-200 bg-white/5 p-2.5 rounded-xl border border-white/10 backdrop-blur-xs">
                        <div class="w-7 h-7 rounded-lg bg-cyan-600/30 border border-cyan-400/30 flex items-center justify-center text-cyan-300 shrink-0">
                            <Clock class="w-4 h-4" />
                        </div>
                        <div>
                            <span class="font-bold text-white">SLA Respons Cepat:</span>
                            <span class="text-slate-300 ml-1">Penanganan tiket pemeliharaan teknisi kampus termonitor.</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Left Trust / Campus Footnote -->
            <div class="relative z-10 pt-6 border-t border-white/10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-slate-400">
                <div class="flex items-center gap-2">
                    <Building2 class="w-4 h-4 text-blue-400 shrink-0" />
                    <span>Gedung Rektorat Lt. 1 • Kampus Utama</span>
                </div>
                <span>SIM-SARPRAS v2.4</span>
            </div>
        </div>

        <!-- Right Authentication Pane -->
        <div class="lg:w-7/12 xl:w-7/12 flex flex-col justify-between p-6 sm:p-10 lg:p-12 bg-gradient-to-b from-[#f8fafc] via-[#f1f5f9] to-[#e2e8f0] min-h-screen overflow-y-auto">
            <!-- Top Nav Back to Home Link -->
            <div class="flex items-center justify-between mb-4 sm:mb-6">
                <Link
                    href="/"
                    class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-blue-700 bg-white hover:bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200 shadow-xs transition group"
                >
                    <ArrowLeft class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform" />
                    <span>Kembali ke Beranda Publik</span>
                </Link>

                <div class="text-[11px] font-semibold text-slate-500 hidden sm:flex items-center gap-1.5 bg-slate-100/80 px-3 py-1.5 rounded-xl border border-slate-200/60">
                    <Lock class="w-3.5 h-3.5 text-emerald-600" />
                    <span>Koneksi Aman Terenkripsi SSL 256-Bit</span>
                </div>
            </div>

            <!-- Centered Auth Card Container -->
            <div class="w-full max-w-lg mx-auto my-auto py-2">
                <div class="bg-white rounded-3xl p-6 sm:p-9 shadow-xl shadow-slate-300/40 border border-slate-200/90">
                    <slot />
                </div>
            </div>

            <!-- Bottom Right Disclaimer -->
            <div class="mt-6 text-center text-xs text-slate-400 space-y-1">
                <div>
                    &copy; {{ new Date().getFullYear() }} Biro Sarana & Prasarana • Universitas Al-Khairiyah.
                </div>
                <div class="text-[11px] text-slate-400">
                    Sistem ini dilindungi oleh regulasi keamanan data civitas akademika & hak cipta universitas.
                </div>
            </div>
        </div>
    </div>
</template>
