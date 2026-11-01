<script setup>
import { ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import QrcodeVue from 'qrcode.vue';
import { 
    Search, 
    Filter, 
    PlusCircle, 
    QrCode, 
    Eye, 
    Edit, 
    Trash2, 
    MapPin, 
    Printer, 
    X,
    Building2,
    Check,
    ChevronLeft,
    ChevronRight
} from 'lucide-vue-next';

const props = defineProps({
    assets: Object,
    filters: Object,
    categories: Array,
    rooms: Array,
    conditions: Array,
    statuses: Array,
});

// Search & Filter state
const search = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category_id || '');
const selectedRoom = ref(props.filters.room_id || '');
const selectedCondition = ref(props.filters.condition || '');
const selectedStatus = ref(props.filters.status || '');

// QR Modal State
const selectedAssetForQr = ref(null);
const showQrModal = ref(false);

const openQrModal = (asset) => {
    selectedAssetForQr.value = asset;
    showQrModal.value = true;
};

const closeQrModal = () => {
    showQrModal.value = false;
    selectedAssetForQr.value = null;
};

const printQrLabel = () => {
    window.print();
};

const applyFilters = () => {
    router.get(route('assets.index'), {
        search: search.value || undefined,
        category_id: selectedCategory.value || undefined,
        room_id: selectedRoom.value || undefined,
        condition: selectedCondition.value || undefined,
        status: selectedStatus.value || undefined,
    }, {
        preserveState: true,
        replace: true,
    });
};

const resetFilters = () => {
    search.value = '';
    selectedCategory.value = '';
    selectedRoom.value = '';
    selectedCondition.value = '';
    selectedStatus.value = '';
    applyFilters();
};

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const getConditionBadgeClass = (cond) => {
    switch (cond) {
        case 'good':
            return 'bg-emerald-50 text-emerald-700 border-emerald-200';
        case 'light_damage':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'heavy_damage':
            return 'bg-rose-50 text-rose-700 border-rose-200';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-200';
    }
};

const getConditionLabel = (cond) => {
    switch (cond) {
        case 'good': return 'Baik';
        case 'light_damage': return 'Rusak Ringan';
        case 'heavy_damage': return 'Rusak Berat';
        default: return cond;
    }
};

const getStatusBadgeClass = (st) => {
    switch (st) {
        case 'available':
            return 'bg-blue-50 text-blue-700 border-blue-200';
        case 'borrowed':
            return 'bg-indigo-50 text-indigo-700 border-indigo-200';
        case 'maintenance':
            return 'bg-amber-50 text-amber-700 border-amber-200';
        case 'disposed':
            return 'bg-slate-50 text-slate-700 border-slate-200';
        default:
            return 'bg-slate-50 text-slate-700 border-slate-200';
    }
};

const getStatusLabel = (st) => {
    switch (st) {
        case 'available': return 'Tersedia';
        case 'borrowed': return 'Dipinjam';
        case 'maintenance': return 'Dalam Servis';
        case 'disposed': return 'Dihapuskan';
        default: return st;
    }
};
</script>

<template>
    <Head title="Katalog Aset Tetap" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Katalog Aset Tetap Kampus</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Daftar lengkap inventaris, pelacakan barcode/QR, dan manajemen lokasi sarana.</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('assets.create')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm shadow-indigo-200 transition"
                    >
                        <PlusCircle class="w-4 h-4" />
                        Tambah Aset
                    </Link>
                </div>
            </div>
        </template>

        <!-- Filters & Search Bar -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <!-- Search Input -->
                <div class="md:col-span-2 relative">
                    <Search class="w-4 h-4 text-slate-400 absolute left-3 top-3" />
                    <input
                        v-model="search"
                        @keyup.enter="applyFilters"
                        type="text"
                        placeholder="Cari kode aset, nama, serial number..."
                        class="w-full pl-9 pr-3 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    />
                </div>

                <!-- Category Filter -->
                <div>
                    <select
                        v-model="selectedCategory"
                        @change="applyFilters"
                        class="w-full py-2 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">Semua Kategori</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>

                <!-- Room Filter -->
                <div>
                    <select
                        v-model="selectedRoom"
                        @change="applyFilters"
                        class="w-full py-2 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">Semua Ruangan</option>
                        <option v-for="room in rooms" :key="room.id" :value="room.id">
                            {{ room.name }} ({{ room.building?.code }})
                        </option>
                    </select>
                </div>

                <!-- Condition Filter -->
                <div>
                    <select
                        v-model="selectedCondition"
                        @change="applyFilters"
                        class="w-full py-2 px-3 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                        <option value="">Semua Kondisi</option>
                        <option v-for="c in conditions" :key="c.value" :value="c.value">{{ c.label }}</option>
                    </select>
                </div>
            </div>

            <!-- Active Filters Reset -->
            <div v-if="search || selectedCategory || selectedRoom || selectedCondition || selectedStatus" class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <span>Filter aktif diterapkan</span>
                <button @click="resetFilters" class="font-medium text-indigo-600 hover:text-indigo-800">
                    Reset Semua Filter
                </button>
            </div>
        </div>

        <!-- Assets Data Table -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead class="bg-slate-50 text-xs font-semibold uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3.5">Kode Aset</th>
                            <th class="px-5 py-3.5">Nama & Spesifikasi</th>
                            <th class="px-5 py-3.5">Kategori</th>
                            <th class="px-5 py-3.5">Lokasi Ruangan</th>
                            <th class="px-5 py-3.5">Kondisi</th>
                            <th class="px-5 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="asset in assets.data" :key="asset.id" class="hover:bg-slate-50/80 transition">
                            <!-- Code -->
                            <td class="px-5 py-3.5">
                                <span class="font-mono text-xs font-bold text-slate-800 block">{{ asset.asset_code }}</span>
                                <span v-if="asset.serial_number" class="text-[11px] text-slate-400 block font-mono">SN: {{ asset.serial_number }}</span>
                            </td>

                            <!-- Name & Brand -->
                            <td class="px-5 py-3.5">
                                <div class="font-semibold text-slate-900">{{ asset.name }}</div>
                                <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                    <span v-if="asset.brand" class="font-medium text-slate-600">{{ asset.brand }}</span>
                                    <span v-if="asset.model" class="text-slate-400">• {{ asset.model }}</span>
                                </div>
                            </td>

                            <!-- Category -->
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-700">
                                    {{ asset.category?.name || '-' }}
                                </span>
                            </td>

                            <!-- Location -->
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-1.5 text-xs text-slate-800 font-medium">
                                    <MapPin class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                                    <span>{{ asset.room?.name || '-' }}</span>
                                </div>
                                <div class="text-[11px] text-slate-400 pl-5">
                                    {{ asset.room?.building?.name }} (Lt. {{ asset.room?.floor_number }})
                                </div>
                            </td>

                            <!-- Condition -->
                            <td class="px-5 py-3.5">
                                <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border', getConditionBadgeClass(asset.condition)]">
                                    {{ getConditionLabel(asset.condition) }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-5 py-3.5">
                                <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border', getStatusBadgeClass(asset.status)]">
                                    {{ getStatusLabel(asset.status) }}
                                </span>
                            </td>

                            <!-- Actions -->
                            <td class="px-5 py-3.5 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- QR Modal Trigger -->
                                    <button
                                        @click="openQrModal(asset)"
                                        type="button"
                                        title="Tampilkan QR Code"
                                        class="p-1.5 text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                    >
                                        <QrCode class="w-4 h-4" />
                                    </button>

                                    <!-- Detail Link -->
                                    <Link
                                        :href="route('assets.show', asset.id)"
                                        title="Detail Aset"
                                        class="p-1.5 text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                    >
                                        <Eye class="w-4 h-4" />
                                    </Link>

                                    <!-- Edit Link -->
                                    <Link
                                        :href="route('assets.edit', asset.id)"
                                        title="Ubah Aset"
                                        class="p-1.5 text-slate-600 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                    >
                                        <Edit class="w-4 h-4" />
                                    </Link>
                                </div>
                            </td>
                        </tr>

                        <tr v-if="!assets.data?.length">
                            <td colspan="7" class="px-5 py-12 text-center text-sm text-slate-400">
                                Tidak ada data aset yang sesuai dengan kriteria filter.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Bar -->
            <div v-if="assets.links?.length > 3" class="px-5 py-4 border-t border-slate-200 flex items-center justify-between">
                <div class="text-xs text-slate-500">
                    Menampilkan <span class="font-semibold">{{ assets.from || 0 }}</span> - <span class="font-semibold">{{ assets.to || 0 }}</span> dari <span class="font-semibold">{{ assets.total }}</span> aset
                </div>
                <div class="flex items-center gap-1">
                    <Link
                        v-for="(link, idx) in assets.links"
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

        <!-- QR Code Modal Dialog -->
        <div v-if="showQrModal && selectedAssetForQr" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 print:p-0 print:bg-white">
            <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 relative print:shadow-none print:border-none">
                <button @click="closeQrModal" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 print:hidden">
                    <X class="w-5 h-5" />
                </button>

                <div class="text-center">
                    <div class="text-xs font-bold uppercase tracking-wider text-indigo-600 mb-1">Label Inventaris Resmi</div>
                    <h3 class="text-base font-bold text-slate-900">{{ selectedAssetForQr.name }}</h3>
                    <p class="text-xs font-mono text-slate-500 mt-0.5">{{ selectedAssetForQr.asset_code }}</p>

                    <!-- QR Code Canvas -->
                    <div class="my-5 flex justify-center p-4 bg-slate-50 border border-slate-200 rounded-xl inline-block mx-auto">
                        <QrcodeVue
                            :value="selectedAssetForQr.qr_code_token"
                            :size="180"
                            level="H"
                            render-as="svg"
                        />
                    </div>

                    <div class="text-xs text-slate-500 space-y-1 mb-5">
                        <div>Ruangan: <strong class="text-slate-800">{{ selectedAssetForQr.room?.name }}</strong></div>
                        <div class="font-mono text-[11px] text-slate-400">Token: {{ selectedAssetForQr.qr_code_token }}</div>
                    </div>

                    <div class="flex items-center gap-2 print:hidden">
                        <button
                            @click="printQrLabel"
                            type="button"
                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold transition"
                        >
                            <Printer class="w-4 h-4" />
                            Cetak Label
                        </button>
                        <button
                            @click="closeQrModal"
                            type="button"
                            class="px-4 py-2 border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-lg text-sm font-semibold transition"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
