<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { 
    ArrowLeft, 
    FileText, 
    Layers, 
    CheckCircle2, 
    Clock, 
    AlertTriangle, 
    Building2, 
    DollarSign, 
    User, 
    Calendar, 
    Check, 
    X, 
    Truck, 
    PackageCheck, 
    Sparkles, 
    ExternalLink, 
    Printer,
    BadgeAlert
} from 'lucide-vue-next';

const props = defineProps({
    procurement: Object,
    vendors: Array,
    canApprove: Boolean,
    canEditPurchase: Boolean,
});

const approvalForm = useForm({
    status: 'approved',
    approval_notes: '',
});

const purchaseForm = useForm({
    vendor_id: props.procurement.vendor_id || '',
    purchase_order_number: props.procurement.purchase_order_number || '',
    invoice_number: props.procurement.invoice_number || '',
    receipt_date: props.procurement.receipt_date || new Date().toISOString().substring(0, 10),
    status: 'ordered',
});

const submitApproval = (newStatus) => {
    approvalForm.status = newStatus;
    approvalForm.post(route('procurements.status', props.procurement.id), {
        preserveScroll: true,
    });
};

const submitPurchase = (newStatus) => {
    purchaseForm.status = newStatus;
    purchaseForm.post(route('procurements.purchase', props.procurement.id), {
        preserveScroll: true,
    });
};

const convertToAssets = () => {
    if (confirm('Konfirmasi: Terbitkan seluruh barang yang diterima ke Buku Induk Aset & cetak kode otomatis?')) {
        router.post(route('procurements.convert-to-assets', props.procurement.id));
    }
};

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const formatDate = (dateStr) => {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
    });
};

const getStatusBadge = (st) => {
    switch (st) {
        case 'draft':
            return { label: 'Draf Usulan', class: 'bg-slate-100 text-slate-700 border-slate-200' };
        case 'submitted':
            return { label: 'Diajukan ke Sarpras', class: 'bg-blue-50 text-blue-700 border-blue-200 font-bold' };
        case 'under_review':
            return { label: 'Dalam Telaah Teknis', class: 'bg-amber-50 text-amber-800 border-amber-200 font-bold' };
        case 'approved':
            return { label: 'Disetujui (Alokasi Dana)', class: 'bg-emerald-50 text-emerald-700 border-emerald-200 font-bold' };
        case 'rejected':
            return { label: 'Ditolak', class: 'bg-rose-50 text-rose-700 border-rose-200 font-bold' };
        case 'ordered':
            return { label: 'Proses Pemesanan (PO)', class: 'bg-indigo-50 text-indigo-700 border-indigo-200 font-bold' };
        case 'received':
            return { label: 'Barang Diterima Fisik', class: 'bg-cyan-50 text-cyan-800 border-cyan-200 font-bold' };
        case 'completed':
            return { label: 'Tercatat di Master Aset', class: 'bg-emerald-100 text-emerald-800 border-emerald-300 font-bold' };
        default:
            return { label: st || '-', class: 'bg-slate-100 text-slate-700 border-slate-200' };
    }
};

const getUrgencyBadge = (urgency) => {
    switch (urgency) {
        case 'critical':
            return { label: 'Kritis / Akreditasi', class: 'bg-rose-100 text-rose-800 border-rose-200 font-bold' };
        case 'urgent':
            return { label: 'Mendesak', class: 'bg-amber-100 text-amber-800 border-amber-200 font-semibold' };
        default:
            return { label: 'Reguler', class: 'bg-slate-100 text-slate-700 border-slate-200' };
    }
};
</script>

<template>
    <Head :title="`Detail Usulan ${procurement.request_number} - SIM-SARPRAS`" />

    <AuthenticatedLayout>
        <div class="max-w-6xl mx-auto space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('procurements.index')"
                        class="p-2 text-slate-500 hover:text-slate-800 hover:bg-white rounded-xl transition border border-slate-200 shadow-xs"
                    >
                        <ArrowLeft class="w-4 h-4" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs font-black text-[#1a73e8]">
                                {{ procurement.request_number }}
                            </span>
                            <span :class="['inline-block px-2.5 py-0.5 rounded-full text-[10px] border', getStatusBadge(procurement.status).class]">
                                {{ getStatusBadge(procurement.status).label }}
                            </span>
                            <span :class="['inline-block px-2 py-0.5 rounded-full text-[10px] border', getUrgencyBadge(procurement.urgency).class]">
                                {{ getUrgencyBadge(procurement.urgency).label }}
                            </span>
                        </div>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight mt-1">
                            {{ procurement.title }}
                        </h1>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold shadow-xs">
                        <Building2 class="w-3.5 h-3.5 text-blue-600" />
                        <span>{{ procurement.faculty?.name || 'Rektorat / Universitas' }}</span>
                    </span>
                </div>
            </div>

            <!-- Main 2-Column Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Cols: Items Table & Details -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Table: Rincian Barang yang Diusulkan -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
                        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#1a73e8] flex items-center justify-center font-bold">
                                    <Layers class="w-4 h-4" />
                                </div>
                                <div>
                                    <h2 class="text-sm font-bold text-slate-900">Daftar Barang & Rincian Anggaran (RAB)</h2>
                                    <p class="text-[11px] text-slate-500">Total {{ procurement.items?.length || 0 }} macam barang usulan</p>
                                </div>
                            </div>

                            <span class="font-mono text-sm font-black text-slate-900">
                                {{ formatRupiah(procurement.total_estimated_cost) }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse text-xs">
                                <thead>
                                    <tr class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                        <th class="px-5 py-3.5">BARANG & SPESIFIKASI</th>
                                        <th class="px-4 py-3.5">KATEGORI</th>
                                        <th class="px-4 py-3.5 text-center">QTY</th>
                                        <th class="px-4 py-3.5">ESTIMASI SATUAN</th>
                                        <th class="px-5 py-3.5 text-right">SUBTOTAL</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr v-for="item in procurement.items" :key="item.id" class="hover:bg-slate-50/70 transition">
                                        <td class="px-5 py-3.5">
                                            <div class="font-bold text-slate-900">{{ item.item_name }}</div>
                                            <div v-if="item.brand || item.model" class="text-[11px] text-slate-500 mt-0.5">
                                                {{ [item.brand, item.model].filter(Boolean).join(' • ') }}
                                            </div>
                                            <div v-if="item.specifications" class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">
                                                Spek: {{ item.specifications }}
                                            </div>
                                            <div v-if="item.target_room" class="text-[10px] text-blue-700 font-medium mt-1">
                                                Penempatan: {{ item.target_room?.name }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5 text-slate-600">
                                            {{ item.category?.name || '-' }}
                                        </td>
                                        <td class="px-4 py-3.5 text-center font-bold text-slate-800">
                                            {{ item.quantity }} Unit
                                        </td>
                                        <td class="px-4 py-3.5 font-mono text-slate-700">
                                            {{ formatRupiah(item.unit_price_estimate) }}
                                        </td>
                                        <td class="px-5 py-3.5 text-right font-mono font-bold text-slate-900">
                                            {{ formatRupiah(item.total_price_estimate) }}
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="bg-slate-50 font-bold border-t border-slate-200">
                                        <td colspan="4" class="px-5 py-3 text-right text-slate-600">
                                            Total Estimasi Kebutuhan Biaya (RAB):
                                        </td>
                                        <td class="px-5 py-3 text-right font-mono text-sm text-[#1a73e8]">
                                            {{ formatRupiah(procurement.total_estimated_cost) }}
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Justifikasi & Keterangan Proposal -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">
                            Justifikasi & Parameter Anggaran
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tahun Anggaran</span>
                                <span class="font-bold text-slate-800">TA {{ procurement.fiscal_year }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Rencana Sumber Dana</span>
                                <span class="font-bold text-slate-800">{{ procurement.budget_source }}</span>
                            </div>
                        </div>

                        <div class="space-y-1 pt-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Latar Belakang & Urgensi Kebutuhan</span>
                            <p class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-xl border border-slate-100">
                                {{ procurement.justification || 'Tidak ada catatan tambahan.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Informasi Pembelian & Rekanan (Vendor) jika sudah dipesan/diterima -->
                    <div v-if="['ordered', 'received', 'completed'].includes(procurement.status)" class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                            <Truck class="w-4 h-4 text-indigo-600" />
                            <h3 class="text-sm font-bold text-slate-900">Informasi Pemesanan & Serah Terima Fisik</h3>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Rekanan / Vendor</span>
                                <span class="font-bold text-slate-800">{{ procurement.vendor?.name || '-' }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">No. Purchase Order (PO)</span>
                                <span class="font-bold text-slate-800 font-mono">{{ procurement.purchase_order_number || '-' }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">No. Invoice</span>
                                <span class="font-bold text-slate-800 font-mono">{{ procurement.invoice_number || '-' }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tanggal Penerimaan Fisik</span>
                                <span class="font-bold text-slate-800">{{ formatDate(procurement.receipt_date) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right 1 Col: Status, Approval Actions, 1-Click Convert -->
                <div class="space-y-6">
                    <!-- Status & Metadata Card -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">
                            Status & Jejak Pengajuan
                        </h3>

                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Pemohon</span>
                                <span class="font-bold text-slate-800">{{ procurement.user?.name }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Tanggal Diajukan</span>
                                <span class="font-bold text-slate-800">{{ formatDate(procurement.created_at) }}</span>
                            </div>
                            <div v-if="procurement.approver" class="flex items-center justify-between">
                                <span class="text-slate-400">Divalidasi Oleh</span>
                                <span class="font-bold text-slate-800">{{ procurement.approver?.name }}</span>
                            </div>
                            <div v-if="procurement.approved_at" class="flex items-center justify-between">
                                <span class="text-slate-400">Waktu Verifikasi</span>
                                <span class="font-bold text-slate-800">{{ formatDate(procurement.approved_at) }}</span>
                            </div>
                        </div>

                        <!-- Catatan Verifikasi jika ada -->
                        <div v-if="procurement.approval_notes" class="pt-3 border-t border-slate-100 space-y-1">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Catatan Verifikator</span>
                            <p class="text-xs text-slate-600 bg-amber-50/70 p-3 rounded-xl border border-amber-200/60 leading-relaxed">
                                {{ procurement.approval_notes }}
                            </p>
                        </div>
                    </div>

                    <!-- Panel Approval Khusus Super Admin / Biro Sarpras -->
                    <div 
                        v-if="canApprove && ['submitted', 'under_review'].includes(procurement.status)" 
                        class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4"
                    >
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <CheckCircle2 class="w-4 h-4 text-emerald-600" />
                            <h3 class="text-sm font-bold text-slate-900">Verifikasi Biro Sarpras</h3>
                        </div>

                        <div class="space-y-2">
                            <label class="text-xs font-bold text-slate-700">Catatan Rekomendasi / Arahan Anggaran</label>
                            <textarea
                                v-model="approvalForm.approval_notes"
                                rows="3"
                                placeholder="Tuliskan catatan teknis atau rekomendasi alokasi dana..."
                                class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-2">
                            <button
                                type="button"
                                @click="submitApproval('approved')"
                                :disabled="approvalForm.processing"
                                class="w-full flex items-center justify-center gap-1.5 py-2.5 px-3 bg-gradient-to-tr from-emerald-600 to-green-500 hover:opacity-95 text-white rounded-xl text-xs font-bold shadow-xs transition"
                            >
                                <Check class="w-4 h-4 stroke-[2.5]" />
                                <span>Setujui Proposal</span>
                            </button>

                            <button
                                type="button"
                                @click="submitApproval('rejected')"
                                :disabled="approvalForm.processing"
                                class="w-full flex items-center justify-center gap-1.5 py-2.5 px-3 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition"
                            >
                                <X class="w-4 h-4 stroke-[2.5]" />
                                <span>Tolak Usulan</span>
                            </button>

                            <button
                                v-if="procurement.status === 'submitted'"
                                type="button"
                                @click="submitApproval('under_review')"
                                :disabled="approvalForm.processing"
                                class="col-span-2 flex items-center justify-center gap-1.5 py-2 px-3 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 rounded-xl text-xs font-bold transition"
                            >
                                <Clock class="w-4 h-4" />
                                <span>Tandai Dalam Telaah Teknis</span>
                            </button>
                        </div>
                    </div>

                    <!-- Panel Update Pembelian / PO & Penerimaan Barang -->
                    <div 
                        v-if="canEditPurchase && ['approved', 'ordered'].includes(procurement.status)" 
                        class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4"
                    >
                        <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                            <Truck class="w-4 h-4 text-[#1a73e8]" />
                            <h3 class="text-sm font-bold text-slate-900">Realisasi PO & Serah Terima</h3>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">Vendor / Rekanan Penyedia</label>
                                <select
                                    v-model="purchaseForm.vendor_id"
                                    class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                                >
                                    <option value="">Pilih Vendor</option>
                                    <option v-for="v in vendors" :key="v.id" :value="v.id">
                                        {{ v.name }}
                                    </option>
                                </select>
                            </div>

                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">No. Surat Pesanan (PO)</label>
                                <input
                                    v-model="purchaseForm.purchase_order_number"
                                    type="text"
                                    placeholder="PO-2026-XXXX"
                                    class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl font-mono focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                                />
                            </div>

                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">No. Invoice</label>
                                <input
                                    v-model="purchaseForm.invoice_number"
                                    type="text"
                                    placeholder="INV-XXXX"
                                    class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl font-mono focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                                />
                            </div>

                            <div class="space-y-1">
                                <label class="font-bold text-slate-700">Tanggal Penerimaan Fisik di Kampus</label>
                                <input
                                    v-model="purchaseForm.receipt_date"
                                    type="date"
                                    class="w-full px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                                />
                            </div>
                        </div>

                        <div class="space-y-2 pt-2">
                            <button
                                v-if="procurement.status === 'approved'"
                                type="button"
                                @click="submitPurchase('ordered')"
                                :disabled="purchaseForm.processing"
                                class="w-full py-2.5 px-4 bg-gradient-to-tr from-[#1a73e8] to-[#4285f4] text-white rounded-xl text-xs font-bold shadow-xs hover:opacity-95 transition"
                            >
                                Simpan PO & Tandai Dalam Pemesanan
                            </button>

                            <button
                                type="button"
                                @click="submitPurchase('received')"
                                :disabled="purchaseForm.processing"
                                class="w-full py-2.5 px-4 bg-gradient-to-tr from-cyan-600 to-blue-600 text-white rounded-xl text-xs font-bold shadow-xs hover:opacity-95 transition"
                            >
                                Konfirmasi Barang Telah Diterima Fisik
                            </button>
                        </div>
                    </div>

                    <!-- 1-Click Convert to Master Assets (Otomasikan ke Master Aset) -->
                    <div 
                        v-if="canApprove && ['received', 'ordered', 'approved'].includes(procurement.status) && procurement.status !== 'completed'"
                        class="bg-gradient-to-br from-[#0c1f3f] to-[#1e3a8a] text-white rounded-2xl p-6 shadow-md shadow-blue-950/20 space-y-4"
                    >
                        <div class="flex items-center gap-2">
                            <Sparkles class="w-5 h-5 text-cyan-300" />
                            <h3 class="text-sm font-bold text-white">Otomasi ke Buku Induk Aset</h3>
                        </div>

                        <p class="text-xs text-blue-100/80 leading-relaxed">
                            Terbitkan seluruh barang dalam usulan ini secara otomatis ke dalam Master Katalog Aset lengkap dengan kode inventaris resmi dan token QR Code siap cetak.
                        </p>

                        <button
                            type="button"
                            @click="convertToAssets"
                            class="w-full py-3 px-4 bg-white hover:bg-blue-50 text-[#0c182c] rounded-xl text-xs font-black shadow-md transition flex items-center justify-center gap-2"
                        >
                            <PackageCheck class="w-4 h-4 text-[#1a73e8] stroke-[2.5]" />
                            <span>Terbitkan ke Master Aset (1-Klik)</span>
                        </button>
                    </div>

                    <!-- Completed Status Banner -->
                    <div 
                        v-if="procurement.status === 'completed'"
                        class="bg-emerald-50 rounded-2xl p-6 border border-emerald-200 text-center space-y-3"
                    >
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto">
                            <CheckCircle2 class="w-6 h-6 stroke-[2.5]" />
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-emerald-900">Pengadaan Aset Tuntas</h4>
                            <p class="text-xs text-emerald-700 mt-1">
                                Seluruh unit barang telah otomatis dicatatkan ke Buku Induk Aset dan telah memiliki kode QR inventaris kampus.
                            </p>
                        </div>
                        <div class="pt-2">
                            <Link
                                :href="route('assets.index')"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-xs transition"
                            >
                                <span>Lihat di Katalog Aset</span>
                                <ExternalLink class="w-3.5 h-3.5" />
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
