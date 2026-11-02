<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { 
    ChevronLeft, 
    Printer, 
    CheckCircle2, 
    XCircle, 
    Calendar, 
    User, 
    DoorOpen, 
    Building2, 
    Clock, 
    Users, 
    FileText, 
    ShieldCheck, 
    AlertTriangle,
    Ban
} from 'lucide-vue-next';

const props = defineProps({
    booking: Object,
    canManage: Boolean,
});

const showApproveModal = ref(false);
const showRejectModal = ref(false);

const approveForm = useForm({
    approval_notes: 'Jadwal ruangan disetujui. Harap menjaga kebersihan dan ketertiban fasilitas ruangan.',
});

const rejectForm = useForm({
    approval_notes: '',
});

const handleApprove = () => {
    approveForm.post(route('room-bookings.approve', props.booking.id), {
        onSuccess: () => showApproveModal.value = false,
    });
};

const handleReject = () => {
    rejectForm.post(route('room-bookings.reject', props.booking.id), {
        onSuccess: () => showRejectModal.value = false,
    });
};

const handleCancel = () => {
    if (confirm('Apakah Anda yakin ingin membatalkan permohonan reservasi ruangan ini?')) {
        router.post(route('room-bookings.cancel', props.booking.id));
    }
};

const printPermit = () => {
    window.print();
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
    <Head :title="`Izin Pemakaian Ruangan - ${booking.booking_code}`" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <Link
                        :href="route('room-bookings.index')"
                        class="p-2.5 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition print:hidden"
                    >
                        <ChevronLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold text-[#1e3a8a] bg-blue-50 px-2.5 py-0.5 rounded-full border border-blue-100">
                                {{ booking.booking_code }}
                            </span>
                            <span :class="['px-2.5 py-0.5 text-[10px] font-extrabold rounded-full border', getStatusBadge(booking.status).class]">
                                {{ getStatusBadge(booking.status).label }}
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                            Surat Bukti Izin Pemakaian Ruangan
                        </h1>
                    </div>
                </div>

                <!-- Action Controls -->
                <div class="flex items-center gap-2 print:hidden">
                    <button
                        @click="printPermit"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <Printer class="w-4 h-4 text-slate-500" />
                        Cetak Surat Izin
                    </button>

                    <!-- Staff: Approve & Reject if Pending -->
                    <template v-if="canManage && (booking.status?.value === 'pending' || booking.status === 'pending')">
                        <button
                            @click="showApproveModal = true"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-900/25 transition"
                        >
                            <CheckCircle2 class="w-4 h-4" />
                            Setujui
                        </button>
                        <button
                            @click="showRejectModal = true"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2.5 border border-rose-200 bg-white hover:bg-rose-50 text-rose-600 rounded-xl text-xs font-bold transition"
                        >
                            <XCircle class="w-4 h-4" />
                            Tolak
                        </button>
                    </template>

                    <!-- Borrower: Cancel button if pending or approved -->
                    <button
                        v-if="['pending', 'approved'].includes(booking.status?.value || booking.status)"
                        @click="handleCancel"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-3 py-2.5 border border-slate-200 bg-white hover:bg-rose-50 hover:border-rose-200 hover:text-rose-600 text-slate-500 rounded-xl text-xs font-bold transition"
                    >
                        <Ban class="w-4 h-4" />
                        Batalkan
                    </button>
                </div>
            </div>

            <!-- Guidance Alert Box (Tailored for borrower / non-staff) -->
            <div v-if="!canManage" class="rounded-2xl border p-5 transition flex items-start gap-4 shadow-xs"
                :class="[
                    (booking.status?.value === 'pending' || booking.status === 'pending') ? 'bg-amber-50/90 border-amber-200/80 text-amber-950' : '',
                    (booking.status?.value === 'approved' || booking.status === 'approved') ? 'bg-blue-50/90 border-blue-200/80 text-blue-950' : '',
                    (booking.status?.value === 'completed' || booking.status === 'completed') ? 'bg-emerald-50/90 border-emerald-200/80 text-emerald-950' : '',
                    (booking.status?.value === 'rejected' || booking.status === 'rejected') ? 'bg-rose-50/90 border-rose-200/80 text-rose-950' : '',
                    (booking.status?.value === 'cancelled' || booking.status === 'cancelled') ? 'bg-slate-50 border-slate-200 text-slate-800' : '',
                ]"
            >
                <div class="mt-0.5 p-2 rounded-xl"
                    :class="[
                        (booking.status?.value === 'pending' || booking.status === 'pending') ? 'bg-amber-100 text-amber-700' : '',
                        (booking.status?.value === 'approved' || booking.status === 'approved') ? 'bg-blue-100 text-[#1e3a8a]' : '',
                        (booking.status?.value === 'completed' || booking.status === 'completed') ? 'bg-emerald-100 text-emerald-700' : '',
                        (booking.status?.value === 'rejected' || booking.status === 'rejected') ? 'bg-rose-100 text-rose-700' : '',
                        (booking.status?.value === 'cancelled' || booking.status === 'cancelled') ? 'bg-slate-200 text-slate-600' : '',
                    ]"
                >
                    <Clock v-if="(booking.status?.value === 'pending' || booking.status === 'pending')" class="w-5 h-5 shrink-0" />
                    <CheckCircle2 v-else-if="(booking.status?.value === 'approved' || booking.status === 'approved')" class="w-5 h-5 shrink-0" />
                    <ShieldCheck v-else-if="(booking.status?.value === 'completed' || booking.status === 'completed')" class="w-5 h-5 shrink-0" />
                    <XCircle v-else-if="(booking.status?.value === 'rejected' || booking.status === 'rejected')" class="w-5 h-5 shrink-0" />
                    <Ban v-else class="w-5 h-5 shrink-0" />
                </div>
                <div class="space-y-1 text-xs">
                    <h4 class="font-extrabold text-sm text-slate-900">
                        <template v-if="(booking.status?.value === 'pending' || booking.status === 'pending')">Permohonan Ruangan Sedang Ditinjau</template>
                        <template v-else-if="(booking.status?.value === 'approved' || booking.status === 'approved')">Permohonan Disetujui — Ruangan Siap Digunakan!</template>
                        <template v-else-if="(booking.status?.value === 'completed' || booking.status === 'completed')">Kegiatan di Ruangan Telah Selesai</template>
                        <template v-else-if="(booking.status?.value === 'rejected' || booking.status === 'rejected')">Permohonan Pemakaian Ruangan Ditolak</template>
                        <template v-else>Permohonan Ruangan Telah Dibatalkan</template>
                    </h4>
                    <p class="leading-relaxed text-slate-700">
                        <template v-if="(booking.status?.value === 'pending' || booking.status === 'pending')">
                            Pengajuan ruangan ini telah diterima sistem dan sedang menunggu persetujuan verifikator Biro Sarpras / PIC Ruangan. Anda dapat memantau status persetujuan di halaman ini.
                        </template>
                        <template v-else-if="(booking.status?.value === 'approved' || booking.status === 'approved')">
                            Selamat! Pengajuan ruangan telah disetujui. Tunjukkan bukti izin digital ini atau cetak fisik voucher ke petugas jaga / satpam gedung saat hari pelaksanaan kegiatan.
                        </template>
                        <template v-else-if="(booking.status?.value === 'completed' || booking.status === 'completed')">
                            Kegiatan telah selesai dilaksanakan. Terima kasih telah menjaga kebersihan dan kelengkapan fasilitas ruangan kampus.
                        </template>
                        <template v-else-if="(booking.status?.value === 'rejected' || booking.status === 'rejected')">
                            Mohon maaf, permohonan ruangan tidak dapat disetujui. Silakan periksa alasan penolakan pada kolom catatan di bawah ini.
                        </template>
                        <template v-else>
                            Reservasi ruangan ini telah dibatalkan oleh pemohon.
                        </template>
                    </p>
                </div>
            </div>

            <!-- Main 2-Cols -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Cols: Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Room Info Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 space-y-4">
                        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-blue-50 text-[#1e3a8a] flex items-center justify-center">
                                <DoorOpen class="w-4 h-4" />
                            </div>
                            <h2 class="text-sm font-bold text-slate-900">Informasi Ruangan & Lokasi</h2>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <span class="text-[10px] font-mono font-bold text-[#1e3a8a] bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                                    {{ booking.room?.code }}
                                </span>
                                <h3 class="text-base font-extrabold text-slate-900 mt-1">{{ booking.room?.name }}</h3>
                                <div class="text-xs text-slate-500 mt-0.5 flex flex-wrap items-center gap-2">
                                    <span>Gedung: {{ booking.room?.building?.name }} (Lantai {{ booking.room?.floor_number }})</span>
                                    <span>•</span>
                                    <span>Kampus: {{ booking.room?.building?.campus?.name || 'Utama' }}</span>
                                </div>
                            </div>

                            <div class="text-left sm:text-right">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Daya Tampung</span>
                                <span class="text-base font-black text-slate-800 block">{{ booking.room?.capacity }} Orang</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-2">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Fakultas / Unit Pengelola</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">{{ booking.room?.faculty?.name || 'Rektorat / Universitas Al-Khairiyah' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">PIC Ruangan / Laboran</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">{{ booking.room?.pic?.name || 'Biro Sarpras' }} ({{ booking.room?.pic?.phone || '-' }})</span>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Details Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 space-y-4">
                        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-blue-50 text-[#1e3a8a] flex items-center justify-center">
                                <FileText class="w-4 h-4" />
                            </div>
                            <h2 class="text-sm font-bold text-slate-900">Rincian Permohonan & Identitas Pemohon</h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-xs">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Nama Pemohon</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ booking.user?.name }}</span>
                                <span class="text-slate-500 mt-0.5 block">{{ booking.user?.nip_nim ? `NIP/NIM: ${booking.user.nip_nim}` : '' }} • {{ booking.user?.department || 'Civitas' }}</span>
                            </div>

                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Kontak Pemohon</span>
                                <span class="font-semibold text-slate-800 mt-0.5 block">{{ booking.user?.phone || '-' }}</span>
                                <span class="text-slate-400 mt-0.5 block">{{ booking.user?.email }}</span>
                            </div>

                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tanggal Penggunaan</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ booking.booking_date }}</span>
                            </div>

                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Jam Pemakaian</span>
                                <span class="font-black text-[#1e3a8a] text-sm mt-0.5 block">{{ formatTimeRange(booking.start_time, booking.end_time) }}</span>
                            </div>

                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Estimasi Jumlah Peserta</span>
                                <span class="font-bold text-slate-800 text-sm mt-0.5 block">± {{ booking.attendees_count }} Orang</span>
                            </div>

                            <div v-if="booking.approver">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Petugas Verifikator Sarpras</span>
                                <span class="font-bold text-slate-900 mt-0.5 block">{{ booking.approver.name }}</span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Tujuan / Keperluan Kegiatan</span>
                            <p class="text-xs text-slate-800 bg-slate-50 p-3.5 rounded-xl border border-slate-100 leading-relaxed font-medium">
                                {{ booking.purpose }}
                            </p>
                        </div>

                        <div v-if="booking.approval_notes" class="pt-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Catatan Persetujuan / Alasan Penolakan Petugas</span>
                            <p class="text-xs text-slate-700 italic bg-amber-50/60 p-3.5 rounded-xl border border-amber-200/60 leading-relaxed">
                                {{ booking.approval_notes }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Right 1 Col: Stepper & Guidelines -->
                <div class="space-y-6">
                    <!-- Status Progress Stepper -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6">
                        <h3 class="text-sm font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Tahapan Alur Peminjaman Ruang</h3>

                        <div class="space-y-5 text-xs">
                            <!-- Step 1 -->
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full bg-[#1e3a8a] text-white flex items-center justify-center shrink-0 font-bold text-xs shadow-xs">✓</div>
                                <div>
                                    <div class="font-bold text-slate-900">1. Pengajuan Dikirim</div>
                                    <div class="text-slate-500 mt-0.5">Tercatat di sistem oleh {{ booking.user?.name }}</div>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="flex items-start gap-3">
                                <div :class="['w-6 h-6 rounded-full flex items-center justify-center shrink-0 font-bold text-xs', 
                                    ['approved', 'completed'].includes(booking.status?.value || booking.status) 
                                        ? 'bg-[#1e3a8a] text-white' 
                                        : ((booking.status?.value === 'rejected' || booking.status === 'rejected') ? 'bg-rose-500 text-white' : 'bg-slate-200 text-slate-600')
                                ]">
                                    {{ (booking.status?.value === 'rejected' || booking.status === 'rejected') ? '✕' : (['approved', 'completed'].includes(booking.status?.value || booking.status) ? '✓' : '2') }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900">2. Verifikasi PIC & Biro Sarpras</div>
                                    <div class="text-slate-500 mt-0.5">
                                        {{ (booking.status?.value === 'rejected' || booking.status === 'rejected') ? 'Permohonan Ditolak' : (['approved', 'completed'].includes(booking.status?.value || booking.status) ? 'Telah disetujui petugas' : 'Menunggu pemeriksaan jadwal') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="flex items-start gap-3">
                                <div :class="['w-6 h-6 rounded-full flex items-center justify-center shrink-0 font-bold text-xs', 
                                    ['approved', 'completed'].includes(booking.status?.value || booking.status) ? 'bg-[#1e3a8a] text-white' : 'bg-slate-200 text-slate-600'
                                ]">
                                    {{ ['approved', 'completed'].includes(booking.status?.value || booking.status) ? '✓' : '3' }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900">3. Siap Digunakan</div>
                                    <div class="text-slate-500 mt-0.5">
                                        Tunjukkan voucher ke petugas jaga gedung
                                    </div>
                                </div>
                            </div>

                            <!-- Step 4 -->
                            <div class="flex items-start gap-3">
                                <div :class="['w-6 h-6 rounded-full flex items-center justify-center shrink-0 font-bold text-xs', 
                                    (booking.status?.value === 'completed' || booking.status === 'completed') ? 'bg-[#1e3a8a] text-white' : 'bg-slate-200 text-slate-600'
                                ]">
                                    {{ (booking.status?.value === 'completed' || booking.status === 'completed') ? '✓' : '4' }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900">4. Kegiatan Selesai</div>
                                    <div class="text-slate-500 mt-0.5">
                                        Ruangan dirapikan dan ditutup kembali
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campus Rules Guidelines -->
                    <div class="bg-blue-50/70 rounded-2xl border border-blue-200/80 p-5 text-xs text-blue-950 space-y-2.5">
                        <div class="font-bold flex items-center gap-1.5 text-[#1e3a8a]">
                            <ShieldCheck class="w-4 h-4" />
                            <span>Ketentuan Penggunaan Ruangan Kampus:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-1.5 text-[11px] text-blue-900 leading-relaxed">
                            <li>Wajib menjaga fasilitas audio visual, AC, dan kebersihan lantai/meja.</li>
                            <li>Dilarang membawa makanan berat ke dalam laboratorium komputer.</li>
                            <li>Selesai acara, pastikan seluruh saklar listrik dan pintu dalam keadaan terkunci.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Modal Approve -->
            <div v-if="showApproveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
                    <h3 class="text-base font-bold text-slate-900 mb-1">Setujui Peminjaman Ruangan</h3>
                    <p class="text-xs text-slate-500 mb-4">Pastikan ruangan tidak memiliki jadwal bentrok dengan perkuliahan reguler.</p>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Persetujuan (Opsional)</label>
                        <textarea
                            v-model="approveForm.approval_notes"
                            rows="3"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <button
                            @click="showApproveModal = false"
                            type="button"
                            class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50"
                        >
                            Batal
                        </button>
                        <button
                            @click="handleApprove"
                            type="button"
                            :disabled="approveForm.processing"
                            class="px-5 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-900/25 transition"
                        >
                            Ya, Setujui Permohonan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Reject -->
            <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
                    <h3 class="text-base font-bold text-slate-900 mb-1">Tolak Peminjaman Ruangan</h3>
                    <p class="text-xs text-slate-500 mb-4">Berikan alasan penolakan secara jelas kepada pemohon.</p>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Penolakan *</label>
                        <textarea
                            v-model="rejectForm.approval_notes"
                            required
                            rows="3"
                            placeholder="Contoh: Ruangan sedang dialokasikan untuk ujian praktikum semester atau renovasi..."
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-rose-500/30 focus:border-rose-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <button
                            @click="showRejectModal = false"
                            type="button"
                            class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50"
                        >
                            Batal
                        </button>
                        <button
                            @click="handleReject"
                            type="button"
                            :disabled="rejectForm.processing || !rejectForm.approval_notes"
                            class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition disabled:opacity-50"
                        >
                            Tolak Permohonan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

