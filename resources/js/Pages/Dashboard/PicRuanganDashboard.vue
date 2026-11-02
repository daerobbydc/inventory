<script setup>
import { Link } from '@inertiajs/vue3';
import { 
    DoorOpen, 
    Layers, 
    Clock, 
    Calendar, 
    CheckCircle2, 
    AlertTriangle, 
    Building2, 
    User, 
    ChevronRight, 
    MapPin, 
    Plus, 
    Info, 
    ShieldCheck,
    Check,
    X,
    ExternalLink
} from 'lucide-vue-next';

const props = defineProps({
    picData: {
        type: Object,
        default: () => ({
            myRooms: [],
            pendingLoans: [],
            pendingBookings: [],
            todayBookings: [],
            metrics: {
                roomsCount: 0,
                totalAssets: 0,
                pendingApprovals: 0,
                damagedAssetsCount: 0,
            },
        }),
    },
});

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const formatTimeRange = (start, end) => {
    const cleanStart = start ? start.substring(0, 5) : '';
    const cleanEnd = end ? end.substring(0, 5) : '';
    return `${cleanStart} - ${cleanEnd} WIB`;
};
</script>

<template>
    <div class="space-y-6">
        <!-- 1. Top Welcome Banner Card -->
        <div class="bg-gradient-to-r from-[#0a1628] via-[#0e2752] to-[#1e3a8a] text-white rounded-2xl p-6 sm:p-7 border border-blue-900/60 shadow-lg shadow-blue-950/15 relative overflow-hidden flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Decorative ambient glow -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="space-y-2 max-w-2xl relative z-10">
                <div class="flex items-center gap-2 text-xs font-semibold">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-500/20 text-cyan-200 border border-blue-400/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        Panel Pengelola Ruang & Laboratorium
                    </span>
                    <span class="text-blue-200/60">•</span>
                    <span class="text-blue-200">PIC: {{ $page.props.auth?.user?.name }}</span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight">
                    Pengawasan Ruang & Inventaris Terpadu
                </h1>

                <p class="text-xs sm:text-sm text-blue-100/90 leading-relaxed">
                    Pusat verifikasi peminjaman alat laboratorium, persetujuan reservasi ruang kelas/lab, dan pemantauan kondisi aset binaan Anda di Universitas Al-Khairiyah.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap sm:grid sm:grid-cols-2 lg:flex gap-2.5 shrink-0 relative z-10">
                <Link
                    :href="route('loans.index')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-blue-50 text-[#0c182c] rounded-xl text-xs font-extrabold shadow-md transition"
                >
                    <Layers class="w-4 h-4 stroke-[2.5]" />
                    <span>Verifikasi Alat</span>
                </Link>

                <Link
                    :href="route('room-bookings.index')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md transition"
                >
                    <DoorOpen class="w-4 h-4 stroke-[2.5]" />
                    <span>Verifikasi Ruangan</span>
                </Link>

                <Link
                    :href="route('maintenance.create')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-500/20 hover:bg-rose-500/30 border border-rose-400/30 text-rose-100 rounded-xl text-xs font-bold backdrop-blur-sm shadow-sm transition"
                >
                    <AlertTriangle class="w-4 h-4 text-rose-300" />
                    <span>Lapor Kerusakan</span>
                </Link>
            </div>
        </div>

        <!-- 2. Four Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Ruangan Dikelola -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 border-t-4 border-t-blue-600 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500">Ruangan Dikelola</span>
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                        <DoorOpen class="w-4 h-4" />
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">
                        {{ picData.metrics?.roomsCount || 0 }}
                    </span>
                    <span class="text-xs font-medium text-slate-500">ruang binaan</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Tanggung jawab Anda</span>
                    <span class="text-blue-700 font-bold text-[11px]">Laboratorium & Kelas</span>
                </div>
            </div>

            <!-- Total Aset di Ruangan -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 border-t-4 border-t-indigo-600 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500">Aset di Ruang Anda</span>
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                        <Layers class="w-4 h-4" />
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">
                        {{ picData.metrics?.totalAssets || 0 }}
                    </span>
                    <span class="text-xs font-medium text-slate-500">unit aset</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Terdata di SIM-SARPRAS</span>
                    <Link :href="route('assets.index')" class="text-indigo-700 font-bold hover:underline inline-flex items-center gap-1">
                        Katalog <ChevronRight class="w-3 h-3" />
                    </Link>
                </div>
            </div>

            <!-- Menunggu Persetujuan -->
            <div class="bg-white rounded-2xl p-5 border border-amber-200/80 border-t-4 border-t-amber-500 bg-amber-50/20 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-amber-700">Menunggu Persetujuan</span>
                    <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                        <Clock class="w-4 h-4" />
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-amber-700">
                        {{ picData.metrics?.pendingApprovals || 0 }}
                    </span>
                    <span class="text-xs font-medium text-amber-700/80">permohonan baru</span>
                </div>
                <div class="mt-3 pt-3 border-t border-amber-200 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Alat & reservasi ruang</span>
                    <a href="#pending-approvals" class="text-amber-700 font-extrabold hover:underline inline-flex items-center gap-1">
                        Verifikasi Segera <ChevronRight class="w-3 h-3" />
                    </a>
                </div>
            </div>

            <!-- Aset Rusak / Perlu Servis -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 border-t-4 border-t-rose-600 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-rose-600">Aset Rusak / Servis</span>
                    <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <AlertTriangle class="w-4 h-4" />
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-rose-600">
                        {{ picData.metrics?.damagedAssetsCount || 0 }}
                    </span>
                    <span class="text-xs font-medium text-rose-600/80">unit butuh perbaikan</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Kerusakan ringan/berat</span>
                    <Link :href="route('maintenance.index')" class="text-rose-600 font-bold hover:underline inline-flex items-center gap-1">
                        Cek Servis <ChevronRight class="w-3 h-3" />
                    </Link>
                </div>
            </div>
        </div>

        <!-- 3. Main Content: 2-Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left (2 Cols): Pending Approvals & Today Schedule -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Permohonan Menunggu Verifikasi Anda -->
                <div id="pending-approvals" class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                    <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                                <Clock class="w-4 h-4" />
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Permohonan Menunggu Review Anda</h2>
                                <p class="text-xs text-slate-500">Peminjaman alat dan reservasi untuk ruang binaan Anda</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-800 text-xs font-bold border border-amber-200">
                            {{ (picData.pendingLoans?.length || 0) + (picData.pendingBookings?.length || 0) }} Menunggu
                        </span>
                    </div>

                    <!-- Pending Bookings List -->
                    <div v-if="picData.pendingBookings?.length > 0" class="border-b border-slate-100">
                        <div class="px-6 py-2 bg-slate-50 text-[11px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                            <DoorOpen class="w-3.5 h-3.5 text-emerald-700" />
                            <span>Reservasi Ruangan ({{ picData.pendingBookings.length }})</span>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <div 
                                v-for="booking in picData.pendingBookings" 
                                :key="booking.id"
                                class="p-4 sm:p-5 hover:bg-slate-50/70 transition flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                            >
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-mono font-bold text-slate-900">{{ booking.booking_code }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu Persetujuan
                                        </span>
                                    </div>
                                    <div class="text-xs text-slate-800 font-bold">
                                        {{ booking.room?.name }} • <span class="text-slate-500 font-medium">Pemohon: {{ booking.user?.name }}</span>
                                    </div>
                                    <div class="flex items-center gap-3 text-[11px] text-slate-500">
                                        <span>Tanggal: {{ formatDate(booking.booking_date) }}</span>
                                        <span>Waktu: {{ formatTimeRange(booking.start_time, booking.end_time) }}</span>
                                    </div>
                                    <div v-if="booking.purpose" class="text-xs text-slate-600 italic">
                                        "{{ booking.purpose }}"
                                    </div>
                                </div>

                                <div class="shrink-0 flex items-center gap-2">
                                    <Link
                                        :href="route('room-bookings.show', booking.id)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#1e3a8a] text-white rounded-lg text-xs font-bold hover:bg-[#172554] shadow-sm transition"
                                    >
                                        <span>Review & Setujui</span>
                                        <ChevronRight class="w-3.5 h-3.5" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Loans List -->
                    <div v-if="picData.pendingLoans?.length > 0">
                        <div class="px-6 py-2 bg-slate-50 text-[11px] font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                            <Layers class="w-3.5 h-3.5 text-blue-700" />
                            <span>Peminjaman Alat / Aset ({{ picData.pendingLoans.length }})</span>
                        </div>
                        <div class="divide-y divide-slate-100">
                            <div 
                                v-for="loan in picData.pendingLoans" 
                                :key="loan.id"
                                class="p-4 sm:p-5 hover:bg-slate-50/70 transition flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                            >
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-mono font-bold text-slate-900">{{ loan.transaction_code }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Menunggu Persetujuan
                                        </span>
                                    </div>
                                    <div class="text-xs text-slate-800 font-bold">
                                        Peminjam: {{ loan.borrower?.name }}
                                    </div>
                                    <div class="text-xs text-slate-600">
                                        Barang: {{ loan.loan_items?.map(i => i.asset?.name).join(', ') || '-' }}
                                    </div>
                                    <div class="flex items-center gap-3 text-[11px] text-slate-500">
                                        <span>Pinjam: {{ formatDate(loan.loan_date) }}</span>
                                        <span>Rencana Kembali: {{ formatDate(loan.return_due_date) }}</span>
                                    </div>
                                </div>

                                <div class="shrink-0 flex items-center gap-2">
                                    <Link
                                        :href="route('loans.show', loan.id)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-[#1e3a8a] text-white rounded-lg text-xs font-bold hover:bg-[#172554] shadow-sm transition"
                                    >
                                        <span>Review & Setujui</span>
                                        <ChevronRight class="w-3.5 h-3.5" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- If No Pending Items -->
                    <div v-if="(!picData.pendingBookings || picData.pendingBookings.length === 0) && (!picData.pendingLoans || picData.pendingLoans.length === 0)" class="p-8 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto mb-3">
                            <CheckCircle2 class="w-6 h-6" />
                        </div>
                        <h3 class="text-xs font-bold text-slate-800">Semua Permohonan Telah Ditangani</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            Tidak ada antrean persetujuan alat atau ruangan binaan yang sedang tertunda.
                        </p>
                    </div>
                </div>

                <!-- Jadwal Penggunaan Ruangan Hari Ini -->
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                    <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                                <Calendar class="w-4 h-4" />
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Jadwal Penggunaan Ruang Hari Ini</h2>
                                <p class="text-xs text-slate-500">Agenda kegiatan akademik & praktikum pada ruang binaan Anda</p>
                            </div>
                        </div>
                        <Link :href="route('room-bookings.index')" class="text-xs font-semibold text-emerald-700 hover:underline flex items-center gap-1">
                            Agenda Lengkap <ChevronRight class="w-3 h-3" />
                        </Link>
                    </div>

                    <div v-if="picData.todayBookings?.length > 0" class="divide-y divide-slate-100">
                        <div 
                            v-for="item in picData.todayBookings" 
                            :key="item.id"
                            class="p-4 sm:p-5 hover:bg-slate-50/70 transition flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                        >
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 font-mono text-xs font-bold border border-emerald-200">
                                        {{ formatTimeRange(item.start_time, item.end_time) }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-900">{{ item.room?.name }}</span>
                                </div>
                                <div class="text-xs text-slate-700 font-medium">
                                    {{ item.purpose || 'Kegiatan Perkuliahan / Praktikum' }}
                                </div>
                                <div class="text-[11px] text-slate-500 flex items-center gap-2">
                                    <User class="w-3 h-3 text-slate-400" />
                                    <span>Penanggung Jawab: {{ item.user?.name }}</span>
                                </div>
                            </div>

                            <div class="shrink-0">
                                <Link
                                    :href="route('room-bookings.show', item.id)"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-100 transition"
                                >
                                    <span>Detail</span>
                                    <ChevronRight class="w-3.5 h-3.5 text-slate-400" />
                                </Link>
                            </div>
                        </div>
                    </div>

                    <div v-else class="p-8 text-center">
                        <div class="w-12 h-12 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-3">
                            <Calendar class="w-6 h-6" />
                        </div>
                        <h3 class="text-xs font-bold text-slate-700">Tidak Ada Jadwal Hari Ini</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            Ruangan dan laboratorium di bawah tanggung jawab Anda sedang tidak digunakan hari ini.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Right (1 Col): Managed Rooms & SOP Laboran -->
            <div class="space-y-6">
                <!-- Daftar Ruangan yang Dikelola -->
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <Building2 class="w-4 h-4 text-[#1e3a8a]" />
                            <h2 class="text-xs font-bold text-slate-900">Ruangan Binaan Anda</h2>
                        </div>
                        <span class="text-xs font-bold text-slate-500">
                            {{ picData.myRooms?.length || 0 }} Ruang
                        </span>
                    </div>

                    <div v-if="picData.myRooms?.length > 0" class="divide-y divide-slate-100">
                        <div 
                            v-for="room in picData.myRooms" 
                            :key="room.id" 
                            class="p-4 hover:bg-slate-50 transition space-y-2"
                        >
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-900">{{ room.name }}</span>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-100 text-slate-700">
                                    {{ room.code }}
                                </span>
                            </div>

                            <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                <MapPin class="w-3 h-3 text-slate-400" />
                                <span>{{ room.building?.name || 'Gedung Kampus' }} (Lantai {{ room.floor || '1' }})</span>
                            </div>

                            <div class="flex items-center justify-between pt-1 text-[11px] text-slate-500 border-t border-slate-50">
                                <span>Kapasitas: {{ room.capacity || '-' }} orang</span>
                                <span class="font-semibold text-[#1e3a8a]">{{ room.assets_count || 0 }} Unit Aset</span>
                            </div>
                        </div>
                    </div>

                    <div v-else class="p-6 text-center text-xs text-slate-400">
                        Anda belum ditugaskan sebagai PIC untuk ruangan manapun. Hubungi Administrator Biro Sarpras.
                    </div>
                </div>

                <!-- SOP Pengelolaan Lab & Ruang -->
                <div class="bg-gradient-to-br from-[#1e3a8a] via-blue-900 to-indigo-950 text-white rounded-2xl p-5 shadow-sm space-y-3.5">
                    <div class="flex items-center gap-2 text-blue-200">
                        <ShieldCheck class="w-4 h-4" />
                        <h3 class="text-xs font-bold uppercase tracking-wider">SOP Pengelolaan PIC Ruang</h3>
                    </div>

                    <div class="space-y-2 text-xs text-blue-100/90 leading-relaxed">
                        <div class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-blue-500/30 text-blue-200 flex items-center justify-center shrink-0 text-[10px] font-bold mt-0.5">1</span>
                            <p><strong>Inspeksi Fisik:</strong> Cek kelengkapan dan kondisi alat praktikum sebelum diserahkan dan sesudah dikembalikan.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-blue-500/30 text-blue-200 flex items-center justify-center shrink-0 text-[10px] font-bold mt-0.5">2</span>
                            <p><strong>Konfirmasi Kunci:</strong> Pastikan ruang lab terkunci kembali setelah peminjaman selesai digunakan pemohon.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-blue-500/30 text-blue-200 flex items-center justify-center shrink-0 text-[10px] font-bold mt-0.5">3</span>
                            <p><strong>Lapor Kerusakan Cepat:</strong> Bila ada alat lab rusak, segera buat tiket servis agar dapat segera dikerjakan teknisi.</p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-blue-800 text-[11px] text-blue-300 flex items-center justify-between">
                        <span>Universitas Al-Khairiyah</span>
                        <span>Biro Sarpras</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

