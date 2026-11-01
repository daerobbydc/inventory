<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { 
    Search, 
    PlusCircle, 
    Repeat, 
    Clock, 
    CheckCircle2, 
    XCircle, 
    AlertCircle, 
    Eye, 
    Calendar,
    User,
    ChevronRight
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
    <Head title="Peminjaman & Pengembalian Aset" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Peminjaman & Pengembalian Aset</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Pengajuan digital, verifikasi serah terima, dan pemantauan pengembalian inventaris.</p>
                </div>
                <div>
                    <Link
                        :href="route('loans.create')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm shadow-indigo-200 transition"
                    >
                        <PlusCircle class="w-4 h-4" />
                        Ajukan Peminjaman
                    </Link>
                </div>
            </div>
        </template>

        <!-- Search & Status Filter Tabs -->
        <div class="space-y-4 mb-6">
            <!-- Filter Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                <button
                    @click="applyFilters('')"
                    :class="[
                        'px-3.5 py-1.5 rounded-lg font-semibold transition shrink-0',
                        !selectedStatus ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'
                    ]"
                >
                    Semua Transaksi
                </button>
                <button
                    v-for="st in statuses"
                    :key="st.value"
                    @click="applyFilters(st.value)"
                    :class="[
                        'px-3.5 py-1.5 rounded-lg font-semibold transition shrink-0',
                        selectedStatus === st.value ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50'
                    ]"
                >
                    {{ st.label }}
                </button>
            </div>

            <!-- Search Input -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-3">
                <div class="relative">
                    <Search class="w-4 h-4 text-slate-400 absolute left-3 top-3" />
                    <input
                        v-model="search"
                        @keyup.enter="applyFilters(selectedStatus)"
                        type="text"
                        placeholder="Cari kode transaksi, nama peminjam, atau keperluan kegiatan..."
                        class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    />
                </div>
            </div>
        </div>

        <!-- Loans Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Kode Transaksi</th>
                            <th class="px-5 py-3.5">Peminjam</th>
                            <th class="px-5 py-3.5">Tanggal Peminjaman</th>
                            <th class="px-5 py-3.5">Tenggat Pengembalian</th>
                            <th class="px-5 py-3.5">Barang Dipinjam</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="loan in loans.data" :key="loan.id" class="hover:bg-slate-50/80 transition">
                            <!-- Code -->
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs font-bold text-indigo-600 block">{{ loan.transaction_code }}</span>
                                <span class="text-[11px] text-slate-400 block line-clamp-1 max-w-[150px]">{{ loan.purpose }}</span>
                            </td>

                            <!-- Borrower -->
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-900">{{ loan.borrower?.name }}</div>
                                <div class="text-xs text-slate-500">
                                    {{ loan.borrower?.nip_nim ? `NIM/NIP: ${loan.borrower.nip_nim}` : loan.borrower?.department }}
                                </div>
                            </td>

                            <!-- Loan Date -->
                            <td class="px-5 py-3.5 text-xs text-slate-700">
                                <div class="flex items-center gap-1.5">
                                    <Calendar class="w-3.5 h-3.5 text-slate-400" />
                                    <span>{{ loan.loan_date }}</span>
                                </div>
                            </td>

                            <!-- Return Date -->
                            <td class="px-5 py-3.5 text-xs">
                                <div class="font-medium text-slate-800">{{ loan.expected_return_date }}</div>
                                <div v-if="loan.actual_return_date" class="text-[11px] text-emerald-600 mt-0.5">
                                    Kembali: {{ loan.actual_return_date }}
                                </div>
                            </td>

                            <!-- Items -->
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ loan.loan_items?.length || 0 }} Unit
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-3.5">
                                <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border', getStatusBadge(loan.status).class]">
                                    {{ getStatusBadge(loan.status).label }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-3.5 text-right">
                                <Link
                                    :href="route('loans.show', loan.id)"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-600 hover:bg-indigo-50 border border-indigo-200 transition"
                                >
                                    <Eye class="w-3.5 h-3.5" />
                                    Kelola
                                </Link>
                            </td>
                        </tr>

                        <tr v-if="!loans.data?.length">
                            <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">
                                Belum ada riwayat peminjaman yang sesuai dengan filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div v-if="loans.links?.length > 3" class="px-5 py-4 border-t border-slate-200 flex items-center justify-between">
                <div class="text-xs text-slate-500">
                    Menampilkan <span class="font-semibold">{{ loans.from || 0 }}</span> - <span class="font-semibold">{{ loans.to || 0 }}</span> dari <span class="font-semibold">{{ loans.total }}</span> transaksi
                </div>
                <div class="flex items-center gap-1">
                    <Link
                        v-for="(link, idx) in loans.links"
                        :key="idx"
                        :href="link.url || '#'"
                        v-html="link.label"
                        :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-medium transition',
                            link.active ? 'bg-indigo-600 text-white font-bold' : 'text-slate-600 hover:bg-slate-100',
                            !link.url ? 'opacity-40 pointer-events-none' : ''
                        ]"
                    />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

