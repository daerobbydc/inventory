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
    X,
    ShieldCheck,
    FileText
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
    approval_notes: 'Pengajuan telah diperiksa kelayakan fisiknya dan disetujui.',
});

const rejectForm = useForm({
    approval_notes: '',
});

// Return Form prefilled
const initialReturnConditions = {};
props.loan.loan_items?.forEach(item => {
    initialReturnConditions[item.id] = 'good';
});

const returnForm = useForm({
    return_conditions: initialReturnConditions,
    return_notes: 'Barang telah diperiksa fisik lengkap saat serah terima kembali.',
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
    <Head :title="`Voucher Peminjaman - ${loan.transaction_code}`" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <Link
                        :href="route('loans.index')"
                        class="p-2.5 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition print:hidden"
                    >
                        <ChevronLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold text-[#168a42] bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-100">
                                {{ loan.transaction_code }}
                            </span>
                            <span :class="['px-2.5 py-0.5 text-[10px] font-extrabold rounded-full border', getStatusBadge(loan.status).class]">
                                {{ getStatusBadge(loan.status).label }}
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">
                            Bukti Voucher Peminjaman Sarpras
                        </h1>
                    </div>
                </div>

                <!-- Action Controls -->
                <div class="flex items-center gap-2 print:hidden">
                    <button
                        @click="printVoucher"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <Printer class="w-4 h-4 text-slate-500" />
                        Cetak Bukti Voucher
                    </button>

                    <!-- Staff: Approve / Reject (If Pending) -->
                    <template v-if="canManage && loan.status === 'pending'">
                        <button
                            @click="showApproveModal = true"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-[#168a42] hover:bg-[#137a3b] text-white rounded-xl text-xs font-bold shadow-sm shadow-emerald-700/20 transition"
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

                    <!-- Staff: Handover (If Approved) -->
                    <template v-if="canManage && loan.status === 'approved'">
                        <button
                            @click="handleHandover"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-[#168a42] hover:bg-[#137a3b] text-white rounded-xl text-xs font-bold shadow-sm shadow-emerald-700/20 transition"
                        >
                            <Repeat class="w-4 h-4" />
                            Konfirmasi Serah Terima Barang
                        </button>
                    </template>

                    <!-- Staff: Return (If Borrowed) -->
                    <template v-if="canManage && loan.status === 'borrowed'">
                        <button
                            @click="showReturnModal = true"
                            type="button"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-[#168a42] hover:bg-[#137a3b] text-white rounded-xl text-xs font-bold shadow-sm shadow-emerald-700/20 transition"
                        >
                            <CheckCircle2 class="w-4 h-4" />
                            Proses Pengembalian
                        </button>
                    </template>
                </div>
            </div>

            <!-- Main 2-Cols -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Cols: Voucher Data & Items -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Voucher Details Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 space-y-5">
                        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-[#168a42] flex items-center justify-center">
                                <FileText class="w-4 h-4" />
                            </div>
                            <h2 class="text-sm font-bold text-slate-900">Rincian Permohonan & Identitas Pemohon</h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-xs">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Nama Peminjam</span>
                                <span class="font-bold text-slate-900 text-sm mt-0.5 block">{{ loan.borrower?.name }}</span>
                                <span class="text-slate-500 mt-0.5 block">{{ loan.borrower?.nip_nim ? `NIM/NIP: ${loan.borrower.nip_nim}` : '' }} • {{ loan.borrower?.department }}</span>
                            </div>

                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Kontak Pemohon</span>
                                <span class="font-semibold text-slate-800 mt-0.5 block">{{ loan.borrower?.phone || '-' }}</span>
                                <span class="text-slate-400 mt-0.5 block">{{ loan.borrower?.email }}</span>
                            </div>

                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tanggal Pinjam</span>
                                <span class="font-bold text-slate-900 mt-0.5 block">{{ loan.loan_date }}</span>
                            </div>

                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tenggat Pengembalian</span>
                                <span class="font-bold text-slate-900 mt-0.5 block">{{ loan.expected_return_date }}</span>
                            </div>

                            <div v-if="loan.actual_return_date">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 block">Tanggal Realisasi Kembali</span>
                                <span class="font-black text-[#168a42] mt-0.5 block">{{ loan.actual_return_date }}</span>
                            </div>

                            <div v-if="loan.approver">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Petugas Verifikator Sarpras</span>
                                <span class="font-bold text-slate-900 mt-0.5 block">{{ loan.approver.name }}</span>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-100">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Tujuan / Keperluan Kegiatan</span>
                            <p class="text-xs text-slate-700 bg-slate-50 p-3.5 rounded-xl border border-slate-100 leading-relaxed font-medium">{{ loan.purpose }}</p>
                        </div>

                        <div v-if="loan.approval_notes" class="pt-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Catatan Persetujuan Petugas</span>
                            <p class="text-xs text-slate-700 italic bg-amber-50/50 p-3 rounded-xl border border-amber-100">{{ loan.approval_notes }}</p>
                        </div>
                    </div>

                    <!-- Items Borrowed Table -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                            <h2 class="text-sm font-bold text-slate-900">Daftar Barang BMN Dipinjam ({{ loan.loan_items?.length || 0 }} Unit)</h2>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                    <tr>
                                        <th class="px-6 py-3.5">Nama & Kode Aset</th>
                                        <th class="px-5 py-3.5">Lokasi Asal</th>
                                        <th class="px-4 py-3.5">Kondisi Awal</th>
                                        <th class="px-4 py-3.5">Kondisi Kembali</th>
                                        <th class="px-6 py-3.5 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="item in loan.loan_items" :key="item.id" class="hover:bg-slate-50/70 transition">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-slate-900">{{ item.asset?.name }}</div>
                                            <div class="text-[11px] font-mono text-[#168a42] font-semibold mt-0.5">{{ item.asset?.asset_code }}</div>
                                        </td>
                                        <td class="px-5 py-4 text-slate-600">
                                            {{ item.asset?.room?.name }} ({{ item.asset?.room?.building?.code }})
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border">
                                                {{ item.initial_condition }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span v-if="item.return_condition" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                {{ item.return_condition }}
                                            </span>
                                            <span v-else class="text-[11px] text-slate-400 italic">Belum dikembalikan</span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <Link :href="route('assets.show', item.asset_id)" class="font-bold text-[#168a42] hover:text-[#137a3b]">
                                                Buku Induk
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
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6">
                        <h3 class="text-sm font-bold text-slate-900 mb-4 pb-2 border-b border-slate-100">Status Alur Peminjaman</h3>

                        <div class="space-y-5 text-xs">
                            <!-- Step 1 -->
                            <div class="flex items-start gap-3">
                                <div class="w-6 h-6 rounded-full bg-[#168a42] text-white flex items-center justify-center shrink-0 font-bold text-xs shadow-xs">✓</div>
                                <div>
                                    <div class="font-bold text-slate-900">1. Pengajuan Dikirim</div>
                                    <div class="text-slate-500 mt-0.5">Tercatat di sistem oleh {{ loan.borrower?.name }}</div>
                                </div>
                            </div>

                            <!-- Step 2 -->
                            <div class="flex items-start gap-3">
                                <div :class="['w-6 h-6 rounded-full flex items-center justify-center shrink-0 font-bold text-xs', 
                                    ['approved', 'borrowed', 'returned'].includes(loan.status) ? 'bg-[#168a42] text-white' : (loan.status === 'rejected' ? 'bg-rose-500 text-white' : 'bg-slate-200 text-slate-600')
                                ]">
                                    {{ loan.status === 'rejected' ? '✕' : (['approved', 'borrowed', 'returned'].includes(loan.status) ? '✓' : '2') }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900">2. Persetujuan PIC Sarpras</div>
                                    <div class="text-slate-500 mt-0.5">
                                        {{ loan.status === 'rejected' ? 'Ditolak' : (['approved', 'borrowed', 'returned'].includes(loan.status) ? 'Telah diverifikasi & disetujui' : 'Menunggu verifikasi wadir/kabag') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Step 3 -->
                            <div class="flex items-start gap-3">
                                <div :class="['w-6 h-6 rounded-full flex items-center justify-center shrink-0 font-bold text-xs', 
                                    ['borrowed', 'returned'].includes(loan.status) ? 'bg-[#168a42] text-white' : 'bg-slate-200 text-slate-600'
                                ]">
                                    {{ ['borrowed', 'returned'].includes(loan.status) ? '✓' : '3' }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900">3. Serah Terima Fisik</div>
                                    <div class="text-slate-500 mt-0.5">
                                        {{ ['borrowed', 'returned'].includes(loan.status) ? 'Barang telah diserahkan ke peminjam' : 'Belum diambil dari gudang/lab' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Step 4 -->
                            <div class="flex items-start gap-3">
                                <div :class="['w-6 h-6 rounded-full flex items-center justify-center shrink-0 font-bold text-xs', 
                                    loan.status === 'returned' ? 'bg-[#168a42] text-white' : 'bg-slate-200 text-slate-600'
                                ]">
                                    {{ loan.status === 'returned' ? '✓' : '4' }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-900">4. Pengembalian & Inspeksi</div>
                                    <div class="text-slate-500 mt-0.5">
                                        {{ loan.status === 'returned' ? `Selesai dikembalikan (${loan.actual_return_date})` : 'Menunggu pengembalian fisik' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campus Sarpras Guidelines -->
                    <div class="bg-emerald-50/70 rounded-2xl border border-emerald-200/80 p-5 text-xs text-emerald-950 space-y-2.5">
                        <div class="font-bold flex items-center gap-1.5 text-[#168a42]">
                            <ShieldCheck class="w-4 h-4" />
                            Ketentuan Peminjaman Sarpras BMN:
                        </div>
                        <ul class="list-disc list-inside space-y-1.5 text-[11px] text-emerald-900 leading-relaxed">
                            <li>Peminjam wajib menjaga keutuhan unit dan seluruh aksesori bawaan.</li>
                            <li>Keterlambatan pengembalian tanpa konfirmasi dapat dikenakan pembatasan hak pinjam.</li>
                            <li>Setiap kerusakan fisik selama masa pinjam wajib dilaporkan pada petugas saat pengembalian.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Approve Modal -->
            <div v-if="showApproveModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
                    <h3 class="text-base font-bold text-slate-900 mb-1">Setujui Peminjaman Sarpras</h3>
                    <p class="text-xs text-slate-500 mb-4">Pastikan stok fisik unit di laboratorium dalam keadaan prima dan siap diambil.</p>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Verifikator</label>
                        <textarea
                            v-model="approveForm.approval_notes"
                            rows="3"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
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
                            class="px-5 py-2 bg-[#168a42] hover:bg-[#137a3b] text-white rounded-xl text-xs font-bold shadow-sm shadow-emerald-700/20 transition"
                        >
                            Ya, Setujui Permohonan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Reject Modal -->
            <div v-if="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
                <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
                    <h3 class="text-base font-bold text-slate-900 mb-1">Tolak Peminjaman</h3>
                    <p class="text-xs text-slate-500 mb-4">Berikan alasan penolakan secara jelas untuk disampaikan kepada pemohon.</p>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Penolakan *</label>
                        <textarea
                            v-model="rejectForm.approval_notes"
                            required
                            rows="3"
                            placeholder="Contoh: Barang sedang dijadwalkan untuk kegiatan visitasi akreditasi..."
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
                            Tolak Pengajuan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Return Inspection Modal -->
            <div v-if="showReturnModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4">
                <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-slate-100 max-h-[90vh] overflow-y-auto">
                    <h3 class="text-base font-bold text-slate-900 mb-1">Inspeksi & Penyelesaian Pengembalian</h3>
                    <p class="text-xs text-slate-500 mb-4">Periksa fisik setiap item sebelum menutup voucher peminjaman.</p>

                    <div class="space-y-3 mb-5">
                        <div v-for="item in loan.loan_items" :key="item.id" class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 text-xs">
                            <div class="font-bold text-slate-900">{{ item.asset?.name }}</div>
                            <div class="font-mono text-[10px] text-slate-400 mb-2">{{ item.asset?.asset_code }}</div>

                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Kondisi Fisik Saat Kembali:</label>
                                <select
                                    v-model="returnForm.return_conditions[item.id]"
                                    class="w-full py-2 px-3 border border-slate-200 rounded-xl text-xs bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                                >
                                    <option v-for="c in conditions" :key="c.value" :value="c.value">{{ c.label }}</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan Petugas</label>
                        <textarea
                            v-model="returnForm.return_notes"
                            rows="2"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-2">
                        <button
                            @click="showReturnModal = false"
                            type="button"
                            class="px-4 py-2 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50"
                        >
                            Batal
                        </button>
                        <button
                            @click="handleReturn"
                            type="button"
                            :disabled="returnForm.processing"
                            class="px-5 py-2 bg-[#168a42] hover:bg-[#137a3b] text-white rounded-xl text-xs font-bold shadow-sm shadow-emerald-700/20 transition disabled:opacity-50"
                        >
                            Selesaikan Pengembalian
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
