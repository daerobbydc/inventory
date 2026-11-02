<script setup>
import { Link } from '@inertiajs/vue3';
import { 
    Layers, 
    Calendar, 
    Wrench, 
    Building2, 
    Plus, 
    ArrowRight, 
    Clock, 
    CheckCircle2, 
    AlertCircle, 
    DoorOpen, 
    Info, 
    FileText,
    ChevronRight,
    MapPin,
    AlertTriangle
} from 'lucide-vue-next';

const props = defineProps({
    civitasData: {
        type: Object,
        default: () => ({
            myLoans: [],
            activeLoansCount: 0,
            myBookings: [],
            activeBookingsCount: 0,
            myTickets: [],
            activeTicketsCount: 0,
            availableRoomsCount: 0,
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

const getLoanStatusBadge = (st) => {
    const val = typeof st === 'object' ? st?.value : st;
    switch (val) {
        case 'pending':
            return { label: 'Menunggu Persetujuan', class: 'bg-amber-50 text-amber-700 border-amber-200' };
        case 'approved':
            return { label: 'Disetujui (Siap Ambil)', class: 'bg-blue-50 text-[#1e3a8a] border-blue-200' };
        case 'borrowed':
            return { label: 'Sedang Digunakan', class: 'bg-indigo-50 text-indigo-700 border-indigo-200' };
        case 'returned':
            return { label: 'Selesai / Dikembalikan', class: 'bg-emerald-50 text-emerald-700 border-emerald-200' };
        case 'rejected':
            return { label: 'Ditolak', class: 'bg-rose-50 text-rose-700 border-rose-200' };
        case 'overdue':
            return { label: 'Melewati Batas Waktu', class: 'bg-rose-100 text-rose-800 border-rose-300 font-bold' };
        default:
            return { label: val || '-', class: 'bg-slate-50 text-slate-700 border-slate-200' };
    }
};

const getBookingStatusBadge = (st) => {
    const val = typeof st === 'object' ? st?.value : st;
    switch (val) {
        case 'pending':
            return { label: 'Menunggu Review', class: 'bg-amber-50 text-amber-700 border-amber-200' };
        case 'approved':
            return { label: 'Disetujui', class: 'bg-blue-50 text-[#1e3a8a] border-blue-200' };
        case 'completed':
            return { label: 'Selesai Digunakan', class: 'bg-emerald-50 text-emerald-700 border-emerald-200' };
        case 'rejected':
            return { label: 'Ditolak', class: 'bg-rose-50 text-rose-700 border-rose-200' };
        case 'cancelled':
            return { label: 'Dibatalkan', class: 'bg-slate-50 text-slate-600 border-slate-200' };
        default:
            return { label: val || '-', class: 'bg-slate-50 text-slate-700 border-slate-200' };
    }
};

const getTicketStatusBadge = (st) => {
    const val = typeof st === 'object' ? st?.value : st;
    switch (val) {
        case 'reported':
            return { label: 'Dilaporkan', class: 'bg-amber-50 text-amber-700 border-amber-200' };
        case 'assigned':
            return { label: 'Teknisi Ditugaskan', class: 'bg-blue-50 text-[#1e3a8a] border-blue-200' };
        case 'in_progress':
            return { label: 'Sedang Diperbaiki', class: 'bg-indigo-50 text-indigo-700 border-indigo-200' };
        case 'waiting_parts':
            return { label: 'Menunggu Suku Cadang', class: 'bg-purple-50 text-purple-700 border-purple-200' };
        case 'completed':
            return { label: 'Selesai Diperbaiki', class: 'bg-emerald-50 text-emerald-700 border-emerald-200' };
        case 'cancelled':
            return { label: 'Dibatalkan', class: 'bg-slate-50 text-slate-600 border-slate-200' };
        default:
            return { label: val || '-', class: 'bg-slate-50 text-slate-700 border-slate-200' };
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- 1. Top Welcome Banner Card -->
        <div class="bg-gradient-to-r from-[#0a182c] via-[#0e2752] to-[#1e3a8a] text-white rounded-2xl p-6 sm:p-7 border border-blue-900/60 shadow-lg shadow-blue-950/15 relative overflow-hidden flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <!-- Decorative ambient glow -->
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="space-y-2 max-w-2xl relative z-10">
                <div class="flex flex-wrap items-center gap-2 text-xs font-semibold">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-blue-500/20 text-cyan-200 border border-blue-400/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        Portal Layanan Civitas Akademika
                    </span>
                    <span class="text-blue-200/60">•</span>
                    <span class="text-blue-200 font-medium">
                        {{ $page.props.auth?.user?.role_label || 'Civitas Kampus' }}
                    </span>
                    <span v-if="$page.props.auth?.user?.faculty" class="text-blue-300">
                        • {{ $page.props.auth?.user?.faculty?.name }}
                    </span>
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight">
                    Selamat Datang, {{ $page.props.auth?.user?.name }}!
                </h1>

                <p class="text-xs sm:text-sm text-blue-100/90 leading-relaxed">
                    Akses peminjaman alat penunjang kuliah/riset, reservasi ruangan kelas & laboratorium, serta pelaporan keluhan fasilitas kampus Universitas Al-Khairiyah.
                </p>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap sm:grid sm:grid-cols-2 lg:flex gap-2.5 shrink-0 relative z-10">
                <Link
                    :href="route('loans.create')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white hover:bg-blue-50 text-[#0c182c] rounded-xl text-xs font-extrabold shadow-md transition"
                >
                    <Plus class="w-4 h-4 stroke-[2.5]" />
                    <span>Pinjam Aset / Alat</span>
                </Link>

                <Link
                    :href="route('room-bookings.create')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md transition"
                >
                    <DoorOpen class="w-4 h-4 stroke-[2.5]" />
                    <span>Reservasi Ruangan</span>
                </Link>

                <Link
                    :href="route('maintenance.create')"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-rose-500/20 hover:bg-rose-500/30 border border-rose-400/30 text-rose-100 rounded-xl text-xs font-bold backdrop-blur-sm shadow-sm transition"
                >
                    <AlertTriangle class="w-4 h-4 text-rose-300" />
                    <span>Lapor Fasilitas</span>
                </Link>
            </div>
        </div>

        <!-- 2. Four Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Peminjaman Aktif -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 border-t-4 border-t-blue-600 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500">Peminjaman Aset Saya</span>
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                        <Layers class="w-4 h-4" />
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">
                        {{ civitasData.activeLoansCount || 0 }}
                    </span>
                    <span class="text-xs font-medium text-slate-500">transaksi aktif</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Barang sedang Anda bawa</span>
                    <Link :href="route('loans.index')" class="text-blue-700 font-bold hover:underline inline-flex items-center gap-1">
                        Riwayat <ChevronRight class="w-3 h-3" />
                    </Link>
                </div>
            </div>

            <!-- Reservasi Ruangan Aktif -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 border-t-4 border-t-emerald-600 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500">Reservasi Ruangan</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <Calendar class="w-4 h-4" />
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">
                        {{ civitasData.activeBookingsCount || 0 }}
                    </span>
                    <span class="text-xs font-medium text-slate-500">jadwal disetujui/pending</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Ruangan kelas & lab</span>
                    <Link :href="route('room-bookings.index')" class="text-emerald-700 font-bold hover:underline inline-flex items-center gap-1">
                        Jadwal Saya <ChevronRight class="w-3 h-3" />
                    </Link>
                </div>
            </div>

            <!-- Laporan Kerusakan -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 border-t-4 border-t-amber-500 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500">Laporan Kerusakan Saya</span>
                    <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                        <Wrench class="w-4 h-4" />
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">
                        {{ civitasData.activeTicketsCount || 0 }}
                    </span>
                    <span class="text-xs font-medium text-slate-500">tiket dalam proses</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Penanganan oleh teknisi</span>
                    <Link :href="route('maintenance.index')" class="text-amber-700 font-bold hover:underline inline-flex items-center gap-1">
                        Cek Status <ChevronRight class="w-3 h-3" />
                    </Link>
                </div>
            </div>

            <!-- Ruang Kampus Siap Pakai -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 border-t-4 border-t-indigo-600 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-500">Fasilitas Ruang Kampus</span>
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center">
                        <Building2 class="w-4 h-4" />
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-slate-900">
                        {{ civitasData.availableRoomsCount || 0 }}
                    </span>
                    <span class="text-xs font-medium text-slate-500">ruang terdaftar</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                    <span class="text-slate-500">Terbuka untuk kegiatan</span>
                    <Link :href="route('room-bookings.index')" class="text-indigo-700 font-bold hover:underline inline-flex items-center gap-1">
                        Pesan Ruang <ChevronRight class="w-3 h-3" />
                    </Link>
                </div>
            </div>
        </div>

        <!-- 3. Main Content: 2-Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left (2 Cols): Active Loans & Room Bookings -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Section 1: Peminjaman Aset Saya Terkini -->
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                    <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1e3a8a] flex items-center justify-center">
                                <Layers class="w-4 h-4" />
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Peminjaman Aset & Alat Saya</h2>
                                <p class="text-xs text-slate-500">Daftar peralatan praktikum/kegiatan yang sedang Anda pinjam</p>
                            </div>
                        </div>
                        <Link :href="route('loans.index')" class="text-xs font-semibold text-[#1e3a8a] hover:underline flex items-center gap-1">
                            Lihat Semua <ChevronRight class="w-3 h-3" />
                        </Link>
                    </div>

                    <div v-if="civitasData.myLoans?.length > 0" class="divide-y divide-slate-100">
                        <div 
                            v-for="loan in civitasData.myLoans" 
                            :key="loan.id" 
                            class="p-4 sm:p-5 hover:bg-slate-50/70 transition flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                        >
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-mono font-bold text-slate-900">{{ loan.transaction_code }}</span>
                                    <span 
                                        :class="['px-2 py-0.5 rounded-full text-[10px] font-semibold border', getLoanStatusBadge(loan.status).class]"
                                    >
                                        {{ getLoanStatusBadge(loan.status).label }}
                                    </span>
                                </div>
                                <div class="text-xs text-slate-700 font-medium">
                                    <span v-if="loan.loan_items && loan.loan_items.length > 0">
                                        {{ loan.loan_items.map(i => i.asset?.name || 'Aset').join(', ') }}
                                    </span>
                                    <span v-else class="text-slate-400">Tidak ada detail aset</span>
                                </div>
                                <div class="flex items-center gap-4 text-[11px] text-slate-500">
                                    <span class="flex items-center gap-1">
                                        <Clock class="w-3 h-3" /> Pinjam: {{ formatDate(loan.loan_date) }}
                                    </span>
                                    <span class="flex items-center gap-1 font-medium text-slate-700">
                                        Batas Kembali: {{ formatDate(loan.return_due_date) }}
                                    </span>
                                </div>
                            </div>

                            <div class="shrink-0 flex items-center gap-2">
                                <Link
                                    :href="route('loans.show', loan.id)"
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
                            <Layers class="w-6 h-6" />
                        </div>
                        <h3 class="text-xs font-bold text-slate-700">Tidak Ada Peminjaman Aktif</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            Anda belum memiliki riwayat peminjaman alat atau peralatan laboratorium yang sedang berjalan.
                        </p>
                        <Link
                            :href="route('loans.create')"
                            class="inline-flex items-center gap-1.5 mt-3 px-3.5 py-2 bg-[#1e3a8a] text-white text-xs font-bold rounded-xl shadow-sm hover:bg-[#172554] transition"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Ajukan Peminjaman Sekarang</span>
                        </Link>
                    </div>
                </div>

                <!-- Section 2: Jadwal Reservasi Ruangan Saya -->
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                    <div class="px-6 py-4.5 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                                <DoorOpen class="w-4 h-4" />
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Reservasi Ruangan Terjadwal</h2>
                                <p class="text-xs text-slate-500">Ruangan kelas, lab, dan aula yang Anda ajukan</p>
                            </div>
                        </div>
                        <Link :href="route('room-bookings.index')" class="text-xs font-semibold text-emerald-700 hover:underline flex items-center gap-1">
                            Lihat Semua <ChevronRight class="w-3 h-3" />
                        </Link>
                    </div>

                    <div v-if="civitasData.myBookings?.length > 0" class="divide-y divide-slate-100">
                        <div 
                            v-for="booking in civitasData.myBookings" 
                            :key="booking.id" 
                            class="p-4 sm:p-5 hover:bg-slate-50/70 transition flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4"
                        >
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-mono font-bold text-slate-900">{{ booking.booking_code }}</span>
                                    <span 
                                        :class="['px-2 py-0.5 rounded-full text-[10px] font-semibold border', getBookingStatusBadge(booking.status).class]"
                                    >
                                        {{ getBookingStatusBadge(booking.status).label }}
                                    </span>
                                </div>
                                <div class="text-xs text-slate-800 font-bold flex items-center gap-2">
                                    <span>{{ booking.room?.name || 'Ruangan' }}</span>
                                    <span v-if="booking.room?.building" class="text-slate-400 font-normal">
                                        ({{ booking.room.building.name }})
                                    </span>
                                </div>
                                <div class="flex flex-wrap items-center gap-3 text-[11px] text-slate-500">
                                    <span class="flex items-center gap-1">
                                        <Calendar class="w-3 h-3 text-slate-400" />
                                        {{ formatDate(booking.booking_date) }}
                                    </span>
                                    <span class="flex items-center gap-1 font-medium text-slate-700">
                                        <Clock class="w-3 h-3 text-slate-400" />
                                        {{ formatTimeRange(booking.start_time, booking.end_time) }}
                                    </span>
                                    <span v-if="booking.purpose" class="text-slate-500 italic truncate max-w-xs">
                                        "{{ booking.purpose }}"
                                    </span>
                                </div>
                            </div>

                            <div class="shrink-0 flex items-center gap-2">
                                <Link
                                    :href="route('room-bookings.show', booking.id)"
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
                            <DoorOpen class="w-6 h-6" />
                        </div>
                        <h3 class="text-xs font-bold text-slate-700">Belum Ada Reservasi Ruangan</h3>
                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                            Ingin menggunakan ruang kelas atau laboratorium untuk kegiatan perkuliahan, praktikum mandiri, atau organisasi?
                        </p>
                        <Link
                            :href="route('room-bookings.create')"
                            class="inline-flex items-center gap-1.5 mt-3 px-3.5 py-2 bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-sm hover:bg-emerald-800 transition"
                        >
                            <Plus class="w-3.5 h-3.5" />
                            <span>Reservasi Ruangan Sekarang</span>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Right (1 Col): Ticket Status & Campus Rules -->
            <div class="space-y-6">
                <!-- Status Keluhan Fasilitas -->
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-amber-50 text-amber-700 flex items-center justify-center">
                                <Wrench class="w-3.5 h-3.5" />
                            </div>
                            <h2 class="text-xs font-bold text-slate-900">Keluhan Fasilitas Anda</h2>
                        </div>
                        <Link :href="route('maintenance.index')" class="text-[11px] font-semibold text-amber-700 hover:underline">
                            Lihat Semua
                        </Link>
                    </div>

                    <div v-if="civitasData.myTickets?.length > 0" class="divide-y divide-slate-100">
                        <div 
                            v-for="ticket in civitasData.myTickets" 
                            :key="ticket.id" 
                            class="p-4 hover:bg-slate-50/70 transition space-y-2"
                        >
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-mono font-bold text-slate-900">{{ ticket.ticket_code }}</span>
                                <span 
                                    :class="['px-2 py-0.5 rounded-full text-[10px] font-semibold border', getTicketStatusBadge(ticket.status).class]"
                                >
                                    {{ getTicketStatusBadge(ticket.status).label }}
                                </span>
                            </div>
                            <div class="text-xs text-slate-800 font-semibold truncate">
                                {{ ticket.asset?.name || 'Fasilitas Kampus' }}
                            </div>
                            <p class="text-[11px] text-slate-500 line-clamp-2">
                                {{ ticket.issue_description }}
                            </p>
                            <div class="flex items-center justify-between pt-1 text-[10px] text-slate-400">
                                <span>{{ formatDate(ticket.reported_at || ticket.created_at) }}</span>
                                <span v-if="ticket.technician" class="text-slate-600 font-medium">
                                    Teknisi: {{ ticket.technician.name }}
                                </span>
                                <span v-else class="text-amber-600 font-medium">
                                    Menunggu disposisi teknisi
                                </span>
                            </div>
                        </div>
                    </div>

                    <div v-else class="p-6 text-center">
                        <p class="text-xs text-slate-400">Tidak ada tiket laporan kerusakan yang aktif.</p>
                        <Link
                            :href="route('maintenance.create')"
                            class="mt-2 inline-flex items-center gap-1 text-xs font-semibold text-[#1e3a8a] hover:underline"
                        >
                            <Plus class="w-3 h-3" /> Lapor Kendala Fasilitas
                        </Link>
                    </div>
                </div>

                <!-- Panduan & Tata Tertib Fasilitas Kampus -->
                <div class="bg-gradient-to-br from-blue-900 via-blue-950 to-slate-900 text-white rounded-2xl p-5 shadow-sm space-y-3.5">
                    <div class="flex items-center gap-2 text-blue-200">
                        <Info class="w-4 h-4" />
                        <h3 class="text-xs font-bold uppercase tracking-wider">Tata Tertib Layanan Sarpras</h3>
                    </div>

                    <div class="space-y-2.5 text-xs text-blue-100/90 leading-relaxed">
                        <div class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-blue-500/30 text-blue-200 flex items-center justify-center shrink-0 text-[10px] font-bold mt-0.5">1</span>
                            <p><strong>Peminjaman Aset:</strong> Pengambilan alat wajib membawa identitas kampus (KTM/NIP) dan dikembalikan maksimal 7 hari kerja.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-blue-500/30 text-blue-200 flex items-center justify-center shrink-0 text-[10px] font-bold mt-0.5">2</span>
                            <p><strong>Reservasi Ruangan:</strong> Pengajuan wajib dilakukan minimal H-2 sebelum kegiatan untuk persetujuan PIC Ruang / Dekanat.</p>
                        </div>
                        <div class="flex items-start gap-2">
                            <span class="w-4 h-4 rounded-full bg-blue-500/30 text-blue-200 flex items-center justify-center shrink-0 text-[10px] font-bold mt-0.5">3</span>
                            <p><strong>Lapor Kerusakan:</strong> Laporkan jika menemukan fasilitas proyektor, AC, atau kelistrikan rusak agar segera ditangani tim teknisi.</p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-blue-800/60 text-[11px] text-blue-300 flex items-center justify-between">
                        <span>Universitas Al-Khairiyah</span>
                        <span>Biro Sarana & Prasarana</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

