<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import QrcodeVue from 'qrcode.vue';
import { 
    Search, 
    Plus, 
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
    ChevronRight,
    Layers,
    Filter,
    RotateCcw,
    ShieldCheck,
    Coins,
    Sparkles
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

const getConditionBadge = (cond) => {
    switch (cond) {
        case 'good':
            return { label: 'Baik', class: 'bg-emerald-50 text-emerald-700 border-emerald-200' };
        case 'light_damage':
            return { label: 'Rusak Ringan', class: 'bg-amber-50 text-amber-700 border-amber-200' };
        case 'heavy_damage':
            return { label: 'Rusak Berat', class: 'bg-rose-50 text-rose-700 border-rose-200' };
        default:
            return { label: cond, class: 'bg-slate-50 text-slate-700 border-slate-200' };
    }
};

const getStatusBadge = (st) => {
    switch (st) {
        case 'available':
            return { label: 'Tersedia', class: 'bg-blue-50 text-blue-700 border-blue-200' };
        case 'borrowed':
            return { label: 'Dipinjam', class: 'bg-indigo-50 text-indigo-700 border-indigo-200' };
        case 'maintenance':
            return { label: 'Dalam Servis', class: 'bg-amber-50 text-amber-700 border-amber-200' };
        case 'disposed':
            return { label: 'Dihapuskan', class: 'bg-slate-50 text-slate-700 border-slate-200' };
        default:
            return { label: st, class: 'bg-slate-50 text-slate-700 border-slate-200' };
    }
};
</script>

<template>
    <Head title="Data Aset & Inventaris - SIM-SARPRAS" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Banner Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="h-11 w-11 rounded-xl bg-blue-50 text-[#1e3a8a] flex items-center justify-center shrink-0 border border-blue-100">
                        <Layers class="w-6 h-6 stroke-[2.2]" />
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#1e3a8a] bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">
                                Buku Induk Aset Kampus
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight mt-0.5">
                            Data Aset & Inventaris Terpadu
                        </h1>
                        <p class="text-xs text-slate-500">
                            Pengelolaan inventaris sarana & prasarana yayasan, pelabelan QR Code fisik, dan mutasi ruangan.
                        </p>
                    </div>
                </div>

                <div v-if="$page.props.auth?.user?.can_manage_assets" class="flex items-center gap-2.5 shrink-0">
                    <Link
                        :href="route('assets.create')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-900/25 transition"
                    >
                        <Plus class="w-4 h-4 stroke-[2.5]" />
                        <span>Registrasi Aset Baru</span>
                    </Link>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-4 space-y-3">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <!-- Search Input -->
                    <div class="md:col-span-2 relative">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                        <input
                            v-model="search"
                            @keyup.enter="applyFilters"
                            type="text"
                            placeholder="Cari kode inventaris, nama barang, serial number..."
                            class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] transition"
                        />
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <select
                            v-model="selectedCategory"
                            @change="applyFilters"
                            class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] transition"
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
                            class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] transition"
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
                            class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] transition"
                        >
                            <option value="">Semua Kondisi</option>
                            <option v-for="c in conditions" :key="c.value" :value="c.value">{{ c.label }}</option>
                        </select>
                    </div>
                </div>

                <!-- Active Filters Reset Bar -->
                <div v-if="search || selectedCategory || selectedRoom || selectedCondition || selectedStatus" class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span class="font-medium">Filter aktif diterapkan</span>
                    <button @click="resetFilters" class="font-bold text-[#1e3a8a] hover:text-[#172554] flex items-center gap-1">
                        <RotateCcw class="w-3.5 h-3.5" />
                        Reset Filter
                    </button>
                </div>
            </div>

            <!-- Assets Data Table -->
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">Kode Aset & Seri</th>
                                <th class="px-6 py-3.5">Nama Barang & Spesifikasi</th>
                                <th class="px-4 py-3.5">Kategori</th>
                                <th class="px-5 py-3.5">Lokasi Penempatan</th>
                                <th class="px-4 py-3.5">Kondisi</th>
                                <th class="px-4 py-3.5">Status</th>
                                <th class="px-6 py-3.5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="asset in assets.data" :key="asset.id" class="hover:bg-slate-50/70 transition">
                                <!-- Code -->
                                <td class="px-6 py-4">
                                    <span class="font-mono text-xs font-bold text-slate-900 block">{{ asset.asset_code }}</span>
                                    <span v-if="asset.serial_number" class="text-[10px] text-slate-400 block font-mono mt-0.5">SN: {{ asset.serial_number }}</span>
                                </td>

                                <!-- Name & Brand -->
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900 text-sm leading-snug">{{ asset.name }}</div>
                                    <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
                                        <span v-if="asset.brand" class="font-semibold text-slate-700">{{ asset.brand }}</span>
                                        <span v-if="asset.model" class="text-slate-400">• {{ asset.model }}</span>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td class="px-4 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700">
                                        {{ asset.category?.name || '-' }}
                                    </span>
                                </td>

                                <!-- Location -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-1.5 text-xs text-slate-800 font-semibold">
                                        <MapPin class="w-3.5 h-3.5 text-[#1e3a8a] shrink-0" />
                                        <span>{{ asset.room?.name || '-' }}</span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 pl-5 mt-0.5">
                                        {{ asset.room?.building?.name }} (Lt. {{ asset.room?.floor_number }})
                                    </div>
                                </td>

                                <!-- Condition -->
                                <td class="px-4 py-4">
                                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border', getConditionBadge(asset.condition).class]">
                                        {{ getConditionBadge(asset.condition).label }}
                                    </span>
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-4">
                                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border', getStatusBadge(asset.status).class]">
                                        {{ getStatusBadge(asset.status).label }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 text-right">
                                    <div class="inline-flex items-center gap-1.5">
                                        <!-- QR Modal Trigger (Staff only) -->
                                        <button
                                            v-if="$page.props.auth?.user?.can_manage_assets"
                                            @click="openQrModal(asset)"
                                            type="button"
                                            title="Tampilkan Label QR"
                                            class="w-7 h-7 rounded-lg text-slate-600 hover:text-[#1e3a8a] hover:bg-blue-50 border border-slate-200 flex items-center justify-center transition"
                                        >
                                            <QrCode class="w-3.5 h-3.5" />
                                        </button>

                                        <!-- Detail Link (All) -->
                                        <Link
                                            :href="route('assets.show', asset.id)"
                                            title="Buku Induk Aset"
                                            class="w-7 h-7 rounded-lg text-slate-600 hover:text-[#1e3a8a] hover:bg-blue-50 border border-slate-200 flex items-center justify-center transition"
                                        >
                                            <Eye class="w-3.5 h-3.5" />
                                        </Link>

                                        <!-- Edit Link (Staff only) -->
                                        <Link
                                            v-if="$page.props.auth?.user?.can_manage_assets"
                                            :href="route('assets.edit', asset.id)"
                                            title="Ubah Data"
                                            class="w-7 h-7 rounded-lg text-slate-600 hover:text-[#1e3a8a] hover:bg-blue-50 border border-slate-200 flex items-center justify-center transition"
                                        >
                                            <Edit class="w-3.5 h-3.5" />
                                        </Link>
                                    </div>
                                </td>
                            </tr>

                            <tr v-if="!assets.data?.length">
                                <td colspan="7" class="px-6 py-12 text-center text-sm text-slate-400">
                                    Tidak ada data aset yang cocok dengan filter.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Bar -->
                <div v-if="assets.links?.length > 3" class="px-6 py-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <div>
                        Menampilkan <span class="font-bold text-slate-800">{{ assets.from || 0 }}</span> - <span class="font-bold text-slate-800">{{ assets.to || 0 }}</span> dari <span class="font-bold text-slate-800">{{ assets.total }}</span> aset inventaris
                    </div>
                    <div class="flex items-center gap-1">
                        <Link
                            v-for="(link, idx) in assets.links"
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

            <!-- SIM-SARPRAS Official Label Sticker Modal -->
            <div v-if="showQrModal && selectedAssetForQr" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 p-4 print:p-0 print:bg-white">
                <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl border border-slate-100 relative print:shadow-none print:border-none">
                    <button @click="closeQrModal" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 print:hidden">
                        <X class="w-5 h-5" />
                    </button>

                    <div class="text-center">
                        <!-- University Header in Label -->
                        <div class="border-b border-slate-200 pb-2 mb-3">
                            <div class="text-[10px] font-extrabold uppercase tracking-wider text-[#1e3a8a]">UNIVERSITAS AL-KHAIRIYAH</div>
                            <div class="text-[9px] font-bold text-slate-500 tracking-tight">LABEL RESMI INVENTARIS YAYASAN AL-KHAIRIYAH</div>
                        </div>

                        <h3 class="text-sm font-bold text-slate-900 leading-tight">{{ selectedAssetForQr.name }}</h3>
                        <p class="text-xs font-mono font-bold text-[#1e3a8a] mt-1">{{ selectedAssetForQr.asset_code }}</p>

                        <!-- QR Code Canvas -->
                        <div class="my-4 flex justify-center p-3 bg-slate-50 border border-slate-200 rounded-xl inline-block mx-auto">
                            <QrcodeVue
                                :value="selectedAssetForQr.qr_code_token"
                                :size="160"
                                level="H"
                                render-as="svg"
                            />
                        </div>

                        <div class="text-xs text-slate-600 space-y-1 mb-5 bg-slate-50 p-2.5 rounded-xl border border-slate-100 text-left">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Ruangan:</span>
                                <strong class="text-slate-800">{{ selectedAssetForQr.room?.name }}</strong>
                            </div>
                            <div class="flex justify-between font-mono text-[11px]">
                                <span class="text-slate-400">Security Token:</span>
                                <span class="text-[#1e3a8a] font-semibold">{{ selectedAssetForQr.qr_code_token }}</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 print:hidden">
                            <button
                                @click="printQrLabel"
                                type="button"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold transition shadow-sm"
                            >
                                <Printer class="w-4 h-4" />
                                Cetak Stiker Label
                            </button>
                            <button
                                @click="closeQrModal"
                                type="button"
                                class="px-4 py-2 border border-slate-200 text-slate-700 hover:bg-slate-50 rounded-xl text-xs font-bold transition"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
