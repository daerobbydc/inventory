<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { 
    ChevronLeft, 
    Printer, 
    CheckCircle2, 
    XCircle, 
    ArrowRight, 
    Calendar, 
    User, 
    Repeat, 
    Box, 
    AlertTriangle,
    Clock,
    X
} from 'lucide-vue-next';

const props = defineProps({
    loan: Object,
    canManage: Boolean,
    conditions: Array,
});

// Modals
const showApproveModal = ref(false);
const showRejectModal = ref(false);
const showReturnModal = ref(false);

// Forms
const approveForm = useForm({
    approval_notes: 'Pengajuan telah diperiksa dan disetujui.',
});

const rejectForm = useForm({
    approval_notes: '',
});

// Return Form prefilled with good condition for all items
const initialReturnConditions = {};
props.loan.loan_items?.forEach(item => {
    initialReturnConditions[item.id] = 'good';
});

const returnForm = useForm({
    return_conditions: initialReturnConditions,
    return_notes: 'Barang telah diperiksa fisik saat dikembalikan.',
});

const handleApprove = () => {
    approveForm.post(route('loans.approve', props.loan.id), {
        onSuccess: () => showApproveModal.value = false,
    });
};

const handleReject = () => {
    rejectForm.post(route('loans.reject', props.loan.id), {
        onSuccess: () => showRejectModal.value = false,
    });
};

const handleHandover = () => {
    if (confirm('Konfirmasi bahwa barang fisik telah diserahkan langsung kepada peminjam?')) {
        router.post(route('loans.handover', props.loan.id));
    }
};

const handleReturn = () => {
    returnForm.post(route('loans.return', props.loan.id), {
        onSuccess: () => showReturnModal.value = false,
    });
};

const printVoucher = () => {
    window.print();
};

const getStatusBadge = (st) => {
    switch (st) {
        case 'pending':
            return { label: 'Menunggu Persetujuan', class: 'bg-amber-50 text-amber-700 border-amber-200' };
        case 'approved':
            return { label: 'Disetujui (Siap Ambil)', class: 'bg-blue-50 text-blue-700 border-blue-200' };
        case 'borrowed':
            return { label: 'Sedang Dipinjam', class: 'bg-indigo-50 text-indigo-700 border-indigo-200' };
        case 'returned':
            return { label: 'Sudah Dikembalikan', class: 'bg-emerald-50 text-emerald-700 border-emerald-200' };
        case 'rejected':
            return { label: 'Ditolak', class: 'bg-rose-50 text-rose-700 border-rose-200' };
        default:
            return { label: st, class: 'bg-slate-50 text-slate-700 border-slate-200' };
    }
};
</script>

<template>
    <Head :title="`Detail Peminjaman ${loan.transaction_code}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('loans.index')"
                        class="p-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-100 transition print:hidden"
                    >
                        <ChevronLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">{{ loan.transaction_code }}</span>
                            <span :class="['px-2.5 py-0.5 text-xs font-medium rounded-full border', getStatusBadge(loan.status).class]">
                                {{ getStatusBadge(loan.status).label }}
                            </span>
                        </div>
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-0.5">Voucher Peminjaman Sarpras</h1>
                    </div>
                </div>

                <!-- Print & Management Actions -->
                <div class="flex items-center gap-2 print:hidden">
                    <button
                        @click="printVoucher"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 rounded-lg text-sm font-semibold shadow-sm transition"
                    >
                        <Printer class="w-4 h-4 text-slate-500" />
                        Cetak Bukti
                    </button>

                    <!-- Staff Action: Approve / Reject (If Pending) -->
                    <template v-if="canManage && loan.status === 'pending'">
                        <button
                            @click="showApproveModal = true"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition"
                        >
                            <CheckCircle2 class="w-4 h-4" />
                            Setujui
                        </button>
                        <button
                            @click="showRejectModal = true"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-3 py-2 border border-rose-200 bg-white hover:bg-rose-50 text-rose-600 rounded-lg text-sm font-semibold transition"
                        >
                            <XCircle class="w-4 h-4" />
                            Tolak
                        </button>
                    </template>

                    <!-- Staff Action: Handover (If Approved) -->
                    <template v-if="canManage && loan.status === 'approved'">
                        <button
                            @click="handleHandover"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm shadow-indigo-100 transition"
                        >
                            <Repeat class="w-4 h-4" />
                            Konfirmasi Serah Terima Barang
                        </button>
                    </template>

                    <!-- Staff Action: Return (If Borrowed) -->
                    <template v-if="canManage && loan.status === 'borrowed'">
                        <button
                            @click="showReturnModal = true"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold shadow-sm transition"
                        >
                            <CheckCircle2 class="w-4 h-4" />
                            Proses Pengembalian
                        </button>
                    </template>
                </div>
            </div>
        </template>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Details & Items -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Voucher Info Card -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">Informasi Peminjaman</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-xs text-slate-400 block">Nama Peminjam</span>
                            <span class="font-semibold text-slate-900">{{ loan.borrower?.name }}</span>
                            <span class="text-xs text-slate-500 block">{{ loan.borrower?.nip_nim ? `NIM/NIP: ${loan.borrower.nip_nim}` : '' }} • {{ loan.borrower?.department }}</span>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400 block">Kontak Peminjam</span>
                            <span class="text-slate-800">{{ loan.borrower?.phone || '-' }}</span>
                            <span class="text-xs text-slate-500 block">{{ loan.borrower?.email }}</span>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400 block">Tanggal Pinjam</span>
                            <span class="font-medium text-slate-900">{{ loan.loan_date }}</span>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400 block">Tenggat Pengembalian</span>
                            <span class="font-medium text-slate-900">{{ loan.expected_return_date }}</span>
                        </div>

                        <div v-if="loan.actual_return_date">
                            <span class="text-xs text-slate-400 block">Tanggal Realisasi Kembali</span>
                            <span class="font-bold text-emerald-600">{{ loan.actual_return_date }}</span>
                        </div>

                        <div v-if="loan.approver">
                            <span class="text-xs text-slate-400 block">Petugas Verifikator / Sarpras</span>
                            <span class="font-medium text-slate-800">{{ loan.approver.name }}</span>
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <span class="text-xs text-slate-400 block mb-1">Keperluan Peminjaman</span>
                        <p class="text-xs text-slate-700 bg-slate-50 p-3 rounded-lg border border-slate-100 leading-relaxed">{{ loan.purpose }}</p>
                    </div>

                    <div v-if="loan.approval_notes" class="pt-2">
                        <span class="text-xs text-slate-400 block mb-1">Catatan Persetujuan / Petugas</span>
                        <p class="text-xs text-slate-600 italic bg-amber-50/50 p-3 rounded-lg border border-amber-100">{{ loan.approval_notes }}</p>
                    </div>
                </div>

                <!-- Items Borrowed Table -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200">
                        <h2 class="text-base font-bold text-slate-900">Daftar Barang yang Dipinjam ({{ loan.loan_items?.length || 0 }} Unit)</h2>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                                <tr>
                                    <th class="px-5 py-3">Barang & Kode</th>
                                    <th class="px-5 py-3">Lokasi Asal</th>
                                    <th class="px-5 py-3">Kondisi Awal</th>
                                    <th class="px-5 py-3">Kondisi Kembali</th>
                                    <th class="px-5 py-3 text-right">Detail</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="item in loan.loan_items" :key="item.id">
                                    <td class="px-5 py-3.5">
                                        <div class="font-semibold text-slate-900">{{ item.asset?.name }}</div>
                                        <div class="text-xs font-mono text-indigo-600 mt-0.5">{{ item.asset?.asset_code }}</div>
                                    </td>
                                    <td class="px-5 py-3.5 text-xs text-slate-600">
                                        {{ item.asset?.room?.name }} ({{ item.asset?.room?.building?.code }})
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-slate-100 text-slate-700 border">
                                            {{ item.initial_condition }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span v-if="item.return_condition" class="px-2 py-0.5 text-xs font-medium rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            {{ item.return_condition }}
                                        </span>
                                        <span v-else class="text-xs text-slate-400 italic">Belum kembali</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <Link :href="route('assets.show', item.asset_id)" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                                            Lihat Aset
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Status Progress Stepper & Rules -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <h3 class="text-sm font-bold text-slate-900 mb-4">Status Alur Peminjaman</h3>

                    <div class="space-y-4 text-xs">
                        <!-- Step 1: Diajukan -->
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-500 text-white flex items-center justify-center shrink-0 font-bold">✓</div>
                            <div>
                                <div class="font-bold text-slate-900">1. Pengajuan Dikirim</div>
                                <div class="text-slate-500">Tercatat di sistem oleh {{ loan.borrower?.name }}</div>
                            </div>
                        </div>

                        <!-- Step 2: Persetujuan -->
                        <div class="flex items-start gap-3">
                            <div :class="['w-6 h-6 rounded-full flex items-center justify-center shrink-0 font-bold', 
                                ['approved', 'borrowed', 'returned'].includes(loan.status) ? 'bg-emerald-500 text-white' : (loan.status === 'rejected' ? 'bg-rose-500 text-white' : 'bg-slate-200 text-slate-600')
                            ]">
                                {{ loan.status === 'rejected' ? '✕' : (['approved', 'borrowed', 'returned'].includes(loan.status) ? '✓' : '2') }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-900">2. Persetujuan PIC Sarpras</div>
                                <div class="text-slate-500">
                                    {{ loan.status === 'rejected' ? 'Ditolak' : (['approved', 'borrowed', 'returned'].includes(loan.status) ? 'Telah disetujui' : 'Menunggu verifikasi') }}
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Serah Terima Fisik -->
                        <div class="flex items-start gap-3">
                            <div :class="['w-6 h-6 rounded-full flex items-center justify-center shrink-0 font-bold', 
                                ['borrowed', 'returned'].includes(loan.status) ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600'
                            ]">
                                {{ ['borrowed', 'returned'].includes(loan.status) ? '✓' : '3' }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-900">3. Serah Terima Barang</div>
                                <div class="text-slate-500">
                                    {{ ['borrowed', 'returned'].includes(loan.status) ? 'Barang fisik telah diambil peminjam' : 'Belum diambil' }}
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Pengembalian -->
                        <div class="flex items-start gap-3">
                            <div :class="['w-6 h-6 rounded-full flex items-center justify-center shrink-0 font-bold', 
                                loan.status === 'returned' ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-600'
                            ]">
                                {{ loan.status === 'returned' ? '✓' : '4' }}
                            </div>
                            <div>
                                <div class="font-bold text-slate-900">4. Pengembalian & Inspeksi</div>
                                <div class="text-slate-500">
                                    {{ loan.status === 'returned' ? `Telah dikembalikan (${loan.actual_return_date})` : 'Belum dikembalikan' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terms & Notice -->
                <div class="bg-indigo-50/60 rounded-xl border border-indigo-100 p-4 text-xs text-indigo-900 space-y-2">
                    <div class="font-bold">Ketentuan Peminjaman Sarana:</div>
                    <ul class="list-disc list-inside space-y-1 text-[11px] text-indigo-800">
                        <li>Peminjam wajib menjaga keutuhan seluruh unit dan aksesori.</li>
                        <li>Keterlambatan tanpa konfirmasi dapat dikenakan pembatasan peminjaman berikutnya.</li>
                        <li>Kerusakan fisik selama masa pinjam menjadi tanggung jawab peminjam untuk dilaporkan.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Approve Modal -->
        <div v-if="showApproveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
                <h3 class="text-base font-bold text-slate-900 mb-2">Setujui Peminjaman</h3>
                <p class="text-xs text-slate-500 mb-4">Pastikan stok fisik barang di ruangan dalam kondisi baik dan siap diserahkan.</p>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Persetujuan (Opsional)</label>
                    <textarea
                        v-model="approveForm.approval_notes"
                        rows="3"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    ></textarea>
                </div>

                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="showApproveModal = false"
                        type="button"
                        class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        @click="handleApprove"
                        type="button"
                        :disabled="approveForm.processing"
                        class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition"
                    >
                        Ya, Setujui Peminjaman
                    </button>
                </div>
            </div>
        </div>

        <!-- Reject Modal -->
        <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
                <h3 class="text-base font-bold text-slate-900 mb-2">Tolak Peminjaman</h3>
                <p class="text-xs text-slate-500 mb-4">Berikan alasan penolakan agar peminjam mengetahui alasan permintaan tidak disetujui.</p>

                <div class="mb-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Penolakan *</label>
                    <textarea
                        v-model="rejectForm.approval_notes"
                        required
                        rows="3"
                        placeholder="Contoh: Barang sedang dijadwalkan untuk kegiatan akreditasi..."
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-rose-500 focus:outline-none"
                    ></textarea>
                </div>

                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="showRejectModal = false"
                        type="button"
                        class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        @click="handleReject"
                        type="button"
                        :disabled="rejectForm.processing || !rejectForm.approval_notes"
                        class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg text-xs font-semibold transition disabled:opacity-50"
                    >
                        Tolak Pengajuan
                    </button>
                </div>
            </div>
        </div>

        <!-- Return Inspection Modal -->
        <div v-if="showReturnModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
                <h3 class="text-base font-bold text-slate-900 mb-1">Proses Pengembalian Barang</h3>
                <p class="text-xs text-slate-500 mb-4">Periksa fisik setiap item sebelum menyelesaikan pengembalian.</p>

                <div class="space-y-3 mb-5">
                    <div v-for="item in loan.loan_items" :key="item.id" class="p-3 bg-slate-50 rounded-lg border border-slate-200 text-xs">
                        <div class="font-semibold text-slate-900 mb-0.5">{{ item.asset?.name }}</div>
                        <div class="font-mono text-[11px] text-slate-400 mb-2">{{ item.asset?.asset_code }}</div>

                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Kondisi Saat Dikembalikan:</label>
                            <select
                                v-model="returnForm.return_conditions[item.id]"
                                class="w-full py-1.5 px-2 border border-slate-200 rounded-lg text-xs bg-white focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                            >
                                <option v-for="c in conditions" :key="c.value" :value="c.value">{{ c.label }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan Petugas</label>
                    <textarea
                        v-model="returnForm.return_notes"
                        rows="2"
                        class="w-full px-3 py-2 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none"
                    ></textarea>
                </div>

                <div class="flex items-center justify-end gap-2">
                    <button
                        @click="showReturnModal = false"
                        type="button"
                        class="px-4 py-2 border border-slate-200 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50"
                    >
                        Batal
                    </button>
                    <button
                        @click="handleReturn"
                        type="button"
                        :disabled="returnForm.processing"
                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition disabled:opacity-50"
                    >
                        Selesaikan Pengembalian
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

