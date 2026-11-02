<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { 
    Search, 
    Plus, 
    Repeat, 
    Clock, 
    CheckCircle2, 
    XCircle, 
    AlertCircle, 
    Eye, 
    Calendar,
    User,
    ChevronRight,
    RotateCcw
} from 'lucide-vue-next';

const props = defineProps({
    loans: Object,
    filters: Object,
    isStaff: Boolean,
    statuses: Array,
});

const search = ref(props.filters.search || '');
const selectedStatus = ref(props.filters.status || '');

const applyFilters = (statusVal = null) => {
    if (statusVal !== null) {
        selectedStatus.value = statusVal;
    }
    router.get(route('loans.index'), {
        search: search.value || undefined,
        status: selectedStatus.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
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
        case 'overdue':
            return { label: 'Terlambat', class: 'bg-purple-50 text-purple-700 border-purple-200' };
        default:
            return { label: st, class: 'bg-slate-50 text-slate-700 border-slate-200' };
    }
};
</script>

<template>
    <Head title="Peminjaman & Fasilitas - SIM-SARPRAS" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="h-11 w-11 rounded-xl bg-blue-50 text-[#1e3a8a] flex items-center justify-center shrink-0 border border-blue-100">
                        <Repeat class="w-6 h-6 stroke-[2.2]" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#1e3a8a] bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">
                                Layanan Peminjaman Aset
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5">
                            Peminjaman Sarana & Fasilitas
                        </h1>
                        <p class="text-xs text-slate-500">
                            Pengajuan digital pemanfaatan laboratorium, perangkat audio visual, dan verifikasi serah terima fisik.
                        </p>
                    </div>
                </div>

                <div>
                    <Link
                        :href="route('loans.create')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-900/25 transition"
                    >
                        <Plus class="w-4 h-4 stroke-[2.5]" />
                        <span>Ajukan Peminjaman</span>
                    </Link>
                </div>
            </div>

            <!-- Status Tabs & Search Bar -->
            <div class="space-y-3">
                <!-- Pills Row -->
                <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                    <button
                        @click="applyFilters('')"
                        :class="[
                            'px-4 py-2 rounded-xl font-bold transition shrink-0',
                            !selectedStatus ? 'bg-[#1e3a8a] text-white shadow-sm shadow-blue-900/25' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'
                        ]"
                    >
                        Semua Transaksi
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

                <!-- Search Input Box -->
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-3">
                    <div class="relative">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                        <input
                            v-model="search"
                            @keyup.enter="applyFilters(selectedStatus)"
                            type="text"
                            placeholder="Cari nomor voucher transaksi, nama peminjam, atau keperluan kegiatan..."
                            class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] transition"
                        />
                    </div>
                </div>
            </div>

            <!-- Loans Table -->
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">Kode Voucher</th>
                                <th class="px-6 py-3.5">Peminjam</th>
                                <th class="px-4 py-3.5">Tanggal Pinjam</th>
                                <th class="px-4 py-3.5">Tenggat Kembali</th>
                                <th class="px-4 py-3.5">Unit Barang</th>
                                <th class="px-4 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="loan in loans.data" :key="loan.id" class="hover:bg-slate-50/70 transition">
                                <!-- Voucher Code -->
                                <td class="px-6 py-4">
                                    <span class="font-mono text-xs font-bold text-[#1e3a8a] block">{{ loan.transaction_code }}</span>
                                    <span class="text-[11px] text-slate-400 block line-clamp-1 max-w-[160px] mt-0.5">{{ loan.purpose }}</span>
                                </td>

                                <!-- Borrower -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-sm leading-tight">{{ loan.borrower?.name }}</div>
                                    <div class="text-[11px] text-slate-500 mt-0.5">
                                        {{ loan.borrower?.nip_nim ? `NIM/NIP: ${loan.borrower.nip_nim}` : '' }} • {{ loan.borrower?.department }}
                                    </div>
                                </td>

                                <!-- Loan Date -->
                                <td class="px-4 py-4 text-slate-700">
                                    <div class="flex items-center gap-1.5 font-medium">
                                        <Calendar class="w-3.5 h-3.5 text-slate-400" />
                                        <span>{{ loan.loan_date }}</span>
                                    </div>
                                </td>

                                <!-- Expected Return Date -->
                                <td class="px-4 py-4">
                                    <div class="font-bold text-slate-800">{{ loan.expected_return_date }}</div>
                                    <div v-if="loan.actual_return_date" class="text-[10px] text-emerald-600 font-bold mt-0.5">
                                        Kembali: {{ loan.actual_return_date }}
                                    </div>
                                </td>

                                <!-- Items Count -->
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-slate-100 text-slate-700">
                                        {{ loan.loan_items?.length || 0 }} Unit
                                    </span>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-4 py-4">
                                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border', getStatusBadge(loan.status).class]">
                                        {{ getStatusBadge(loan.status).label }}
                                    </span>
                                </td>

                                <!-- Action Link -->
                                <td class="px-6 py-4 text-right">
                                    <Link
                                        :href="route('loans.show', loan.id)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-[#1e3a8a] hover:bg-blue-50 border border-blue-200 transition"
                                    >
                                        <Eye class="w-3.5 h-3.5" />
                                        Kelola
                                    </Link>
                                </td>
                            </tr>

                            <tr v-if="!loans.data?.length">
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-400">
                                    Belum ada transaksi peminjaman yang sesuai dengan filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="loans.links?.length > 3" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <div>
                        Menampilkan <span class="font-bold text-slate-800">{{ loans.from || 0 }}</span> - <span class="font-bold text-slate-800">{{ loans.to || 0 }}</span> dari <span class="font-bold text-slate-800">{{ loans.total }}</span> permohonan
                    </div>
                    <div class="flex items-center gap-1">
                        <Link
                            v-for="(link, idx) in loans.links"
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
