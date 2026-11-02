<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    Package, 
    Plus, 
    Search, 
    AlertTriangle, 
    CheckCircle2, 
    ArrowDownRight, 
    ArrowUpRight, 
    Layers, 
    DollarSign, 
    X, 
    Edit, 
    Boxes, 
    Clock, 
    Check,
    Archive
} from 'lucide-vue-next';

const props = defineProps({
    consumables: Object,
    metrics: Object,
    recentTransactions: Array,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category_id || 'all');
const selectedStockStatus = ref(props.filters.stock_status || 'all');

const applyFilter = () => {
    router.get(route('consumables.index'), {
        search: search.value || undefined,
        category_id: selectedCategory.value !== 'all' ? selectedCategory.value : undefined,
        stock_status: selectedStockStatus.value !== 'all' ? selectedStockStatus.value : undefined,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const resetFilter = () => {
    search.value = '';
    selectedCategory.value = 'all';
    selectedStockStatus.value = 'all';
    applyFilter();
};

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

// Modal: Create New Item
const showCreateModal = ref(false);
const createForm = useForm({
    item_code: '',
    name: '',
    category_id: '',
    unit: 'rim',
    current_stock: 0,
    minimum_stock: 5,
    unit_price: 0,
    notes: '',
});

const submitCreate = () => {
    createForm.post(route('consumables.store'), {
        onSuccess: () => {
            showCreateModal.value = false;
            createForm.reset();
        },
    });
};

// Modal: Transaction (In / Out)
const showTransactionModal = ref(false);
const selectedConsumable = ref(null);
const transactionForm = useForm({
    type: 'out', // 'in' or 'out'
    quantity: 1,
    reference_number: '',
    notes: '',
});

const openTransaction = (item, type = 'out') => {
    selectedConsumable.value = item;
    transactionForm.type = type;
    transactionForm.quantity = 1;
    transactionForm.reference_number = '';
    transactionForm.notes = '';
    showTransactionModal.value = true;
};

const submitTransaction = () => {
    if (!selectedConsumable.value) return;
    transactionForm.post(route('consumables.transaction', selectedConsumable.value.id), {
        onSuccess: () => {
            showTransactionModal.value = false;
            selectedConsumable.value = null;
        },
    });
};
</script>

<template>
    <Head title="Barang Habis Pakai & ATK - SIM-SARPRAS" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Banner -->
            <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/70 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                            Gudang Logistik Kampus
                        </span>
                        <span>•</span>
                        <span>Barang Habis Pakai & ATK</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pengelolaan Barang Habis Pakai (ATK)</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Kendali persediaan kertas, spidol, reagen praktikum, dan alokasi logistik civitas akademika Universitas Al-Khairiyah.
                    </p>
                </div>

                <div v-if="$page.props.auth?.user?.can_manage_consumables" class="flex items-center gap-2.5 shrink-0">
                    <button
                        @click="showCreateModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-900/25 transition"
                    >
                        <Plus class="w-4 h-4 stroke-[2.5]" />
                        <span>+ Tambah Barang Baru</span>
                    </button>
                </div>
            </div>

            <!-- 4 Metric Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-[#fffbeb]/90 border border-amber-100 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                            <Boxes class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">Katalog</span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 mt-3">TOTAL JENIS BARANG</div>
                    <div class="text-2xl font-black text-slate-900 mt-0.5">{{ metrics.total_items }} Item</div>
                    <div class="text-[11px] text-slate-500 mt-1">Persediaan aktif di gudang</div>
                </div>

                <div class="bg-[#f0fdf4]/90 border border-emerald-100 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-[#1e3a8a] flex items-center justify-center">
                            <DollarSign class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-emerald-100 text-[#1e3a8a]">Nilai Total</span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 mt-3">VALUASI PERSEDIAAN</div>
                    <div class="text-2xl font-black text-slate-900 mt-0.5">{{ formatRupiah(metrics.total_valuation) }}</div>
                    <div class="text-[11px] text-slate-500 mt-1">Total harga persediaan on-hand</div>
                </div>

                <div class="bg-[#fff1f2]/80 border border-rose-100 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center">
                            <AlertTriangle class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-rose-100 text-rose-800">Peringatan</span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-rose-700 mt-3">STOK MENIPIS</div>
                    <div class="text-2xl font-black text-slate-900 mt-0.5">{{ metrics.low_stock_count }} Item</div>
                    <div class="text-[11px] text-slate-500 mt-1">Mendekati batas re-order point</div>
                </div>

                <div class="bg-[#f8fafc] border border-slate-200 rounded-2xl p-5 shadow-xs">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center">
                            <Archive class="w-5 h-5" />
                        </div>
                        <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">Habis</span>
                    </div>
                    <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-500 mt-3">STOK HABIS (0)</div>
                    <div class="text-2xl font-black text-slate-900 mt-0.5">{{ metrics.empty_stock_count }} Item</div>
                    <div class="text-[11px] text-slate-500 mt-1">Perlu pengadaan ulang segera</div>
                </div>
            </div>

            <!-- Filters & Search Bar -->
            <div class="bg-white rounded-2xl border border-slate-200/70 p-4 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 md:pb-0">
                    <button
                        @click="selectedStockStatus = 'all'; applyFilter()"
                        :class="[
                            'px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap',
                            selectedStockStatus === 'all'
                                ? 'bg-[#1e3a8a] text-white shadow-xs'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        ]"
                    >
                        Semua Persediaan
                    </button>
                    <button
                        @click="selectedStockStatus = 'low'; applyFilter()"
                        :class="[
                            'px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap flex items-center gap-1',
                            selectedStockStatus === 'low'
                                ? 'bg-amber-600 text-white shadow-xs'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        ]"
                    >
                        <AlertTriangle class="w-3.5 h-3.5" />
                        <span>Stok Menipis ({{ metrics.low_stock_count }})</span>
                    </button>
                    <button
                        @click="selectedStockStatus = 'safe'; applyFilter()"
                        :class="[
                            'px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap',
                            selectedStockStatus === 'safe'
                                ? 'bg-[#1e3a8a] text-white shadow-xs'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200'
                        ]"
                    >
                        Stok Aman
                    </button>
                </div>

                <div class="flex items-center gap-3">
                    <select
                        v-model="selectedCategory"
                        @change="applyFilter"
                        class="text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                    >
                        <option value="all">Semua Kategori</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                            {{ cat.name }}
                        </option>
                    </select>

                    <div class="relative w-full sm:w-64">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
                        <input
                            v-model="search"
                            @keyup.enter="applyFilter"
                            type="text"
                            placeholder="Cari kode atau nama barang..."
                            class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                        />
                    </div>

                    <button
                        v-if="search || selectedCategory !== 'all' || selectedStockStatus !== 'all'"
                        @click="resetFilter"
                        class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-xl transition"
                        title="Reset Filter"
                    >
                        <X class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <!-- Consumables Grid / Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div 
                    v-for="item in consumables.data" 
                    :key="item.id"
                    class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between"
                >
                    <div>
                        <!-- Top Info -->
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <span class="font-mono text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                                    {{ item.item_code }}
                                </span>
                                <h3 class="font-bold text-slate-900 text-sm mt-1.5 leading-snug">{{ item.name }}</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">{{ item.category?.name || 'ATK Umum' }}</p>
                            </div>

                            <!-- Stock Badge -->
                            <span 
                                v-if="item.current_stock <= 0"
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-rose-100 text-rose-700 shrink-0"
                            >
                                Habis
                            </span>
                            <span 
                                v-else-if="item.current_stock <= item.minimum_stock"
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-100 text-amber-800 shrink-0 flex items-center gap-1"
                            >
                                <AlertTriangle class="w-3 h-3" />
                                Menipis
                            </span>
                            <span 
                                v-else
                                class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-100 text-[#1e3a8a] shrink-0"
                            >
                                Aman
                            </span>
                        </div>

                        <!-- Stock Progress Gauge -->
                        <div class="mt-4 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="text-slate-500 font-medium">Sisa Persediaan:</span>
                                <span class="font-black text-slate-900 text-sm">{{ item.current_stock }} {{ item.unit }}</span>
                            </div>
                            <!-- Bar -->
                            <div class="w-full bg-slate-200 rounded-full h-2 overflow-hidden">
                                <div 
                                    :class="[
                                        'h-full rounded-full transition-all duration-300',
                                        item.current_stock <= item.minimum_stock ? 'bg-amber-500' : 'bg-[#1e3a8a]'
                                    ]"
                                    :style="{ width: `${Math.min(100, Math.max(5, (item.current_stock / (item.minimum_stock * 3)) * 100))}%` }"
                                ></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1">
                                <span>Batas Minimum: {{ item.minimum_stock }} {{ item.unit }}</span>
                                <span>Harga/Unit: {{ formatRupiah(item.unit_price) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div v-if="$page.props.auth?.user?.can_manage_consumables" class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                        <!-- Minta Barang (Out) -->
                        <button
                            @click="openTransaction(item, 'out')"
                            :disabled="item.current_stock <= 0"
                            class="flex-1 py-1.5 px-3 rounded-lg bg-emerald-50 hover:bg-emerald-100 text-[#1e3a8a] text-xs font-bold transition flex items-center justify-center gap-1 disabled:opacity-40"
                        >
                            <ArrowUpRight class="w-3.5 h-3.5" />
                            <span>Minta ATK</span>
                        </button>

                        <!-- Restock (In) -->
                        <button
                            @click="openTransaction(item, 'in')"
                            class="flex-1 py-1.5 px-3 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center justify-center gap-1"
                        >
                            <ArrowDownRight class="w-3.5 h-3.5" />
                            <span>Restock</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions Table -->
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Riwayat Mutasi & Permintaan ATK Terkini</h3>
                        <p class="text-[11px] text-slate-500">Log transaksi masuk barang dari pengadaan dan permintaan keluar oleh unit kerja</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3">Waktu</th>
                                <th class="px-4 py-3">Kode / No. Ref</th>
                                <th class="px-4 py-3">Barang ATK</th>
                                <th class="px-4 py-3">Jenis Mutasi</th>
                                <th class="px-4 py-3 text-right">Jumlah</th>
                                <th class="px-4 py-3">Pemohon / Petugas</th>
                                <th class="px-6 py-3">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="t in recentTransactions" :key="t.id" class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-3.5 whitespace-nowrap text-slate-500 font-medium">
                                    {{ new Date(t.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' }) }}
                                </td>
                                <td class="px-4 py-3.5 font-mono font-bold text-slate-700">
                                    {{ t.reference_number || '-' }}
                                </td>
                                <td class="px-4 py-3.5 font-bold text-slate-900">
                                    {{ t.consumable?.name }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span 
                                        :class="[
                                            'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold flex items-center gap-1 w-max',
                                            t.type?.value === 'in' || t.type === 'in' 
                                                ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                                                : 'bg-rose-50 text-rose-700 border border-rose-200'
                                        ]"
                                    >
                                        <component :is="t.type?.value === 'in' || t.type === 'in' ? ArrowDownRight : ArrowUpRight" class="w-3 h-3" />
                                        {{ t.type?.label || (t.type === 'in' ? 'Stok Masuk' : 'Permintaan Keluar') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-right font-black text-slate-900">
                                    {{ t.quantity }} {{ t.consumable?.unit }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap font-medium text-slate-700">
                                    {{ t.user?.name || 'Sistem' }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-500 max-w-xs truncate">
                                    {{ t.notes || '-' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal: Tambah Barang Baru -->
        <div v-if="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">Tambah Barang Habis Pakai / ATK Baru</h3>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-slate-600">
                        <X class="w-4 h-4" />
                    </button>
                </div>
                <form @submit.prevent="submitCreate" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kode Barang</label>
                            <input
                                v-model="createForm.item_code"
                                type="text"
                                placeholder="Misal: ATK-HVS-F4"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori</label>
                            <select
                                v-model="createForm.category_id"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            >
                                <option value="" disabled>-- Pilih Kategori --</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Barang</label>
                        <input
                            v-model="createForm.name"
                            type="text"
                            placeholder="Misal: Kertas HVS F4 75 GSM Sinar Dunia (Rim)"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            required
                        />
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Satuan</label>
                            <input
                                v-model="createForm.unit"
                                type="text"
                                placeholder="rim, box, botol"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Stok Awal</label>
                            <input
                                v-model="createForm.current_stock"
                                type="number"
                                min="0"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Batas Minimum</label>
                            <input
                                v-model="createForm.minimum_stock"
                                type="number"
                                min="1"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Harga Satuan (Rp)</label>
                        <input
                            v-model="createForm.unit_price"
                            type="number"
                            min="0"
                            placeholder="55000"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            required
                        />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button
                            type="button"
                            @click="showCreateModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="createForm.processing"
                            class="px-5 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm transition disabled:opacity-50"
                        >
                            Simpan Barang
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Catat Transaksi Masuk / Keluar -->
        <div v-if="showTransactionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-md w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ transactionForm.type === 'in' ? 'Restock / Catat Stok Masuk' : 'Form Permintaan Barang (Keluar)' }}
                    </h3>
                    <button @click="showTransactionModal = false" class="text-slate-400 hover:text-slate-600">
                        <X class="w-4 h-4" />
                    </button>
                </div>
                <form @submit.prevent="submitTransaction" class="p-6 space-y-4">
                    <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs">
                        <span class="font-mono text-emerald-700 font-bold">{{ selectedConsumable?.item_code }}</span>
                        <div class="font-bold text-slate-900 mt-0.5">{{ selectedConsumable?.name }}</div>
                        <div class="text-slate-500 mt-1 flex items-center justify-between">
                            <span>Sisa Stok Gudang:</span>
                            <span class="font-black text-slate-800">{{ selectedConsumable?.current_stock }} {{ selectedConsumable?.unit }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Jumlah {{ transactionForm.type === 'in' ? 'Masuk' : 'Diminta' }} ({{ selectedConsumable?.unit }})
                        </label>
                        <input
                            v-model="transactionForm.quantity"
                            type="number"
                            min="1"
                            :max="transactionForm.type === 'out' ? selectedConsumable?.current_stock : 10000"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Referensi / Surat Permintaan</label>
                        <input
                            v-model="transactionForm.reference_number"
                            type="text"
                            placeholder="Misal: REQ/FT/2026/05 atau PO/2026/02"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Keperluan / Keterangan</label>
                        <textarea
                            v-model="transactionForm.notes"
                            rows="2"
                            placeholder="Misal: Kebutuhan cetak lembar ujian semester genap..."
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button
                            type="button"
                            @click="showTransactionModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="transactionForm.processing"
                            class="px-5 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm transition disabled:opacity-50"
                        >
                            Simpan Mutasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

