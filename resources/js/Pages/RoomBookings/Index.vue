<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { 
    Search, 
    Plus, 
    DoorOpen, 
    Building2, 
    Clock, 
    CheckCircle2, 
    XCircle, 
    AlertCircle, 
    Eye, 
    Calendar,
    Users,
    Filter,
    ChevronRight,
    MapPin
} from 'lucide-vue-next';

const props = defineProps({
    bookings: Object,
    filters: Object,
    metrics: Object,
    rooms: Array,
    statuses: Array,
    isStaff: Boolean,
});

const search = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || '');
const selectedRoom = ref(props.filters.room_id || '');
const selectedDate = ref(props.filters.date || '');

const applyFilters = (statusVal = null) => {
    if (statusVal !== null) {
        selectedStatus.value = statusVal;
    }
    router.get(route('room-bookings.index'), {
        search: search.value || undefined,
        status: selectedStatus.value || undefined,
        room_id: selectedRoom.value || undefined,
        date: selectedDate.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

const resetFilters = () => {
    search.value = '';
    selectedStatus.value = '';
    selectedRoom.value = '';
    selectedDate.value = '';
    applyFilters('');
};

const getStatusBadge = (st) => {
    const val = typeof st === 'object' ? st?.value : st;
    switch (val) {
        case 'pending':
            return { label: 'Menunggu Persetujuan', class: 'bg-amber-50 text-amber-700 border-amber-200' };
        case 'approved':
            return { label: 'Disetujui', class: 'bg-blue-50 text-[#1e3a8a] border-blue-200' };
        case 'completed':
            return { label: 'Selesai Digunakan', class: 'bg-emerald-50 text-emerald-700 border-emerald-200' };
        case 'rejected':
            return { label: 'Ditolak', class: 'bg-rose-50 text-rose-700 border-rose-200' };
        case 'cancelled':
            return { label: 'Dibatalkan', class: 'bg-slate-50 text-slate-600 border-slate-200' };
        default:
            return { label: val, class: 'bg-slate-50 text-slate-700 border-slate-200' };
    }
};

const formatTimeRange = (start, end) => {
    const cleanStart = start ? start.substring(0, 5) : '';
    const cleanEnd = end ? end.substring(0, 5) : '';
    return `${cleanStart} - ${cleanEnd} WIB`;
};
</script>

<template>
    <Head title="Peminjaman Ruangan & Fasilitas - SIM-SARPRAS" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="h-11 w-11 rounded-xl bg-blue-50 text-[#1e3a8a] flex items-center justify-center shrink-0 border border-blue-100">
                        <DoorOpen class="w-6 h-6 stroke-[2.2]" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#1e3a8a] bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">
                                Layanan Fasilitas Kampus
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5">
                            Peminjaman Ruang Kuliah & Fasilitas
                        </h1>
                        <p class="text-xs text-slate-500">
                            {{ isStaff ? 'Verifikasi dan kelola izin pemakaian auditorium, lab komputer, ruang sidang, dan kelas perkuliahan.' : 'Pengajuan digital izin penggunaan ruangan akademik dan fasilitas kegiatan mahasiswa.' }}
                        </p>
                    </div>
                </div>

                <div>
                    <Link
                        :href="route('room-bookings.create')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-900/25 transition"
                    >
                        <Plus class="w-4 h-4 stroke-[2.5]" />
                        <span>Ajukan Peminjaman Ruangan</span>
                    </Link>
                </div>
            </div>

            <!-- 4 Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 bg-white rounded-2xl border border-slate-200/70 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1e3a8a] flex items-center justify-center">
                            <DoorOpen class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Permohonan</span>
                    </div>
                    <div class="text-2xl font-black text-slate-900 mt-3">{{ metrics.total || 0 }}</div>
                    <p class="text-[11px] text-slate-500 mt-0.5">{{ isStaff ? 'Total seluruh permohonan masuk' : 'Riwayat pengajuan Anda' }}</p>
                </div>

                <div class="p-5 bg-white rounded-2xl border border-slate-200/70 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                            <Clock class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-amber-600">Menunggu Review</span>
                    </div>
                    <div class="text-2xl font-black text-slate-900 mt-3">{{ metrics.pending || 0 }}</div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Memerlukan persetujuan PIC/Sarpras</p>
                </div>

                <div class="p-5 bg-white rounded-2xl border border-slate-200/70 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-[#1e3a8a] flex items-center justify-center">
                            <CheckCircle2 class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#1e3a8a]">Disetujui Hari Ini</span>
                    </div>
                    <div class="text-2xl font-black text-slate-900 mt-3">{{ metrics.approved_today || 0 }}</div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Jadwal kegiatan aktif hari ini</p>
                </div>

                <div class="p-5 bg-white rounded-2xl border border-slate-200/70 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                            <Building2 class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Ruangan Terpakai</span>
                    </div>
                    <div class="text-2xl font-black text-slate-900 mt-3">{{ metrics.active_rooms || 0 }} Ruang</div>
                    <p class="text-[11px] text-slate-500 mt-0.5">Digunakan sepanjang hari ini</p>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="space-y-3">
                <!-- Status Tabs -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                    <button
                        @click="applyFilters('')"
                        :class="[
                            'px-4 py-2 rounded-xl font-bold transition shrink-0',
                            !selectedStatus ? 'bg-[#1e3a8a] text-white shadow-sm shadow-blue-900/25' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'
                        ]"
                    >
                        Semua Status
                    </button>
                    <button
                        v-for="st in statuses"
                        :key="st.value"
                        @click="applyFilters(st.value)"
                        :class="[
                            'px-4 py-2 rounded-xl font-bold transition shrink-0',
                            selectedStatus === st.value ? 'bg-[#1e3a8a] text-white shadow-sm shadow-blue-900/25' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'
                        ]"
                    >
                        {{ st.label }}
                    </button>
                </div>

                <!-- Search and Specific Filter Controls -->
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-3 grid grid-cols-1 md:grid-cols-12 gap-3 text-xs">
                    <div class="md:col-span-5 relative">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5" />
                        <input
                            v-model="search"
                            @keyup.enter="applyFilters(selectedStatus)"
                            type="text"
                            placeholder="Cari nomor permohonan, nama ruangan, atau nama pemohon..."
                            class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] transition"
                        />
                    </div>

                    <div class="md:col-span-3">
                        <select
                            v-model="selectedRoom"
                            @change="applyFilters(selectedStatus)"
                            class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] transition"
                        >
                            <option value="">-- Semua Ruangan --</option>
                            <option v-for="r in rooms" :key="r.id" :value="r.id">
                                {{ r.name }} ({{ r.code }})
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <input
                            v-model="selectedDate"
                            @change="applyFilters(selectedStatus)"
                            type="date"
                            class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] transition"
                        />
                    </div>

                    <div class="md:col-span-1 flex items-center">
                        <button
                            @click="resetFilters"
                            class="w-full py-2 border border-slate-200 text-slate-600 hover:bg-slate-50 rounded-xl font-bold transition text-center"
                            title="Reset Filter"
                        >
                            Reset
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">Kode Permohonan</th>
                                <th class="px-6 py-3.5">Ruangan & Gedung</th>
                                <th class="px-4 py-3.5">Pemohon</th>
                                <th class="px-4 py-3.5">Jadwal Tanggal & Jam</th>
                                <th class="px-4 py-3.5">Peserta & Keperluan</th>
                                <th class="px-4 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="item in bookings.data" :key="item.id" class="hover:bg-slate-50/70 transition">
                                <!-- Code -->
                                <td class="px-6 py-4">
                                    <span class="font-mono text-xs font-bold text-[#1e3a8a] block">{{ item.booking_code }}</span>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">{{ new Date(item.created_at).toLocaleDateString('id-ID') }}</span>
                                </td>

                                <!-- Room -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-sm leading-tight">{{ item.room?.name }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5 flex items-center gap-1.5">
                                        <span>{{ item.room?.building?.name }}</span>
                                        <span v-if="item.room?.faculty" class="px-1.5 py-0.2 bg-blue-50 text-[#1e3a8a] rounded font-semibold text-[10px]">
                                            {{ item.room.faculty.code }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Borrower -->
                                <td class="px-4 py-4">
                                    <div class="font-bold text-slate-900">{{ item.user?.name }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        {{ item.user?.nip_nim ? `NIP/NIM: ${item.user.nip_nim}` : '' }} • {{ item.user?.department || 'Civitas' }}
                                    </div>
                                </td>

                                <!-- Date & Time -->
                                <td class="px-4 py-4">
                                    <div class="font-bold text-slate-800 flex items-center gap-1.5">
                                        <Calendar class="w-3.5 h-3.5 text-slate-400" />
                                        <span>{{ item.booking_date }}</span>
                                    </div>
                                    <div class="text-[11px] text-[#1e3a8a] font-semibold mt-0.5 flex items-center gap-1.5">
                                        <Clock class="w-3.5 h-3.5 text-slate-400" />
                                        <span>{{ formatTimeRange(item.start_time, item.end_time) }}</span>
                                    </div>
                                </td>

                                <!-- Purpose & Attendees -->
                                <td class="px-4 py-4 max-w-xs">
                                    <div class="text-slate-800 font-medium line-clamp-1">{{ item.purpose }}</div>
                                    <div class="text-[11px] text-slate-400 mt-0.5 flex items-center gap-1">
                                        <Users class="w-3.5 h-3.5" />
                                        <span>± {{ item.attendees_count }} Peserta</span>
                                    </div>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-4 py-4">
                                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border', getStatusBadge(item.status).class]">
                                        {{ getStatusBadge(item.status).label }}
                                    </span>
                                </td>

                                <!-- Action Link -->
                                <td class="px-6 py-4 text-right">
                                    <Link
                                        :href="route('room-bookings.show', item.id)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-[#1e3a8a] hover:bg-blue-50 border border-blue-200 transition"
                                    >
                                        <Eye class="w-3.5 h-3.5" />
                                        <span>{{ isStaff ? 'Kelola' : 'Detail Voucher' }}</span>
                                    </Link>
                                </td>
                            </tr>

                            <tr v-if="!bookings.data?.length">
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-400">
                                    <DoorOpen class="w-10 h-10 mx-auto text-slate-300 mb-2 stroke-[1.5]" />
                                    <span>{{ isStaff ? 'Belum ada permohonan peminjaman ruangan yang sesuai dengan filter.' : 'Anda belum memiliki riwayat pengajuan peminjaman ruangan.' }}</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="bookings.links?.length > 3" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <div>
                        Menampilkan <span class="font-bold text-slate-800">{{ bookings.from || 0 }}</span> - <span class="font-bold text-slate-800">{{ bookings.to || 0 }}</span> dari <span class="font-bold text-slate-800">{{ bookings.total }}</span> permohonan
                    </div>
                    <div class="flex items-center gap-1">
                        <Link
                            v-for="(link, idx) in bookings.links"
                            :key="idx"
                            :href="link.url || '#'"
                            v-html="link.label"
                            :class="[
                                'px-3 py-1.5 rounded-lg text-xs font-semibold transition',
                                link.active ? 'bg-[#1e3a8a] text-white' : 'text-slate-600 hover:bg-slate-100',
                                !link.url ? 'opacity-30 pointer-events-none' : ''
                            ]"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
