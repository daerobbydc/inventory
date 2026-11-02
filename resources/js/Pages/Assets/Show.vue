<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import QrcodeVue from 'qrcode.vue';
import { 
    ChevronLeft, 
    Edit, 
    Trash2, 
    Printer, 
    MapPin, 
    Building2, 
    Calendar, 
    Coins, 
    TrendingDown, 
    Wrench, 
    Repeat, 
    Layers, 
    CheckCircle2,
    ShieldCheck,
    Clock,
    FileText
} from 'lucide-vue-next';

const props = defineProps({
    asset: Object,
    currentBookValue: Number,
});

const activeTab = ref('movements');

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const deleteAsset = () => {
    if (confirm(`Apakah Anda yakin ingin menghapus aset "${props.asset.name}" dari Buku Induk Aset?`)) {
        router.delete(route('assets.destroy', props.asset.id));
    }
};

const printLabel = () => {
    window.print();
};

const getConditionBadge = (cond) => {
    switch (cond) {
        case 'good':
            return { label: 'Baik (Prima)', class: 'bg-emerald-50 text-emerald-700 border-emerald-200' };
        case 'light_damage':
            return { label: 'Rusak Ringan', class: 'bg-amber-50 text-amber-700 border-amber-200' };
        case 'heavy_damage':
            return { label: 'Rusak Berat (Afkir)', class: 'bg-rose-50 text-rose-700 border-rose-200' };
        default:
            return { label: cond, class: 'bg-slate-50 text-slate-700 border-slate-200' };
    }
};

const getStatusBadge = (st) => {
    switch (st) {
        case 'available':
            return { label: 'Tersedia', class: 'bg-blue-50 text-blue-700 border-blue-200' };
        case 'borrowed':
            return { label: 'Sedang Dipinjam', class: 'bg-indigo-50 text-indigo-700 border-indigo-200' };
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
    <Head :title="`Buku Induk Aset - ${asset.name}`" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <Link
                        :href="route('assets.index')"
                        class="p-2.5 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition print:hidden"
                    >
                        <ChevronLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold text-[#1e3a8a] bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">
                                {{ asset.asset_code }}
                            </span>
                            <span class="text-xs text-slate-400">• {{ asset.category?.name }}</span>
                            <span :class="['px-2 py-0.5 text-[10px] font-bold rounded-full border', getConditionBadge(asset.condition).class]">
                                {{ getConditionBadge(asset.condition).label }}
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-1">{{ asset.name }}</h1>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 print:hidden">
                    <button
                        @click="printLabel"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2.5 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <Printer class="w-4 h-4 text-slate-500" />
                        Cetak Label Aset
                    </button>

                    <Link
                        :href="route('assets.edit', asset.id)"
                        class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-900/25 transition"
                    >
                        <Edit class="w-4 h-4" />
                        Ubah Data
                    </Link>

                    <button
                        @click="deleteAsset"
                        type="button"
                        class="p-2.5 text-rose-600 hover:bg-rose-50 border border-rose-200 rounded-xl transition"
                        title="Hapus Aset"
                    >
                        <Trash2 class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <!-- Main Layout 2-Cols -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Cols: Main Dossier & History Tabs -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Technical & Procurement Details -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 space-y-5">
                        <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-blue-50 text-[#1e3a8a] flex items-center justify-center">
                                <FileText class="w-4 h-4" />
                            </div>
                            <h2 class="text-sm font-bold text-slate-900">Spesifikasi Teknis & Informasi Pengadaan Aset</h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-xs">
                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Merk & Model</span>
                                <span class="font-semibold text-slate-800 text-sm mt-0.5 block">{{ asset.brand || '-' }} {{ asset.model ? `(${asset.model})` : '' }}</span>
                            </div>

                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Nomor Seri Pabrikan</span>
                                <span class="font-mono font-bold text-slate-800 text-sm mt-0.5 block">{{ asset.serial_number || '-' }}</span>
                            </div>

                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Vendor / Rekanan Pengadaan</span>
                                <span class="font-semibold text-slate-800 mt-0.5 block">{{ asset.vendor?.name || '-' }}</span>
                            </div>

                            <div>
                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block">Tanggal Pembelian</span>
                                <span class="font-semibold text-slate-800 mt-0.5 block">{{ asset.purchase_date || '-' }}</span>
                            </div>

                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Harga Perolehan Awal</span>
                                <span class="font-black text-slate-900 text-base mt-0.5 block">{{ formatRupiah(asset.purchase_cost) }}</span>
                            </div>

                            <div class="p-3 bg-emerald-50/60 rounded-xl border border-emerald-100">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700 block">Estimasi Nilai Buku Terkini</span>
                                <div class="flex items-center gap-1.5 font-black text-[#1e3a8a] text-base mt-0.5">
                                    <TrendingDown class="w-4 h-4" />
                                    <span>{{ formatRupiah(currentBookValue) }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-if="asset.specifications" class="pt-3 border-t border-slate-100">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5">Spesifikasi Lengkap</span>
                            <p class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-3.5 rounded-xl border border-slate-100 font-mono whitespace-pre-wrap">{{ asset.specifications }}</p>
                        </div>

                        <div v-if="asset.notes" class="pt-1">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">Catatan Tambahan Aset</span>
                            <p class="text-xs text-slate-600 italic bg-amber-50/40 p-2.5 rounded-xl border border-amber-100/60">{{ asset.notes }}</p>
                        </div>
                    </div>

                    <!-- History Timeline Tabs -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                        <div class="flex border-b border-slate-100 bg-slate-50/60">
                            <button
                                @click="activeTab = 'movements'"
                                :class="[
                                    'px-5 py-3.5 text-xs font-bold border-b-2 transition flex items-center gap-1.5',
                                    activeTab === 'movements' ? 'border-[#1e3a8a] text-[#1e3a8a] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'
                                ]"
                            >
                                <MapPin class="w-4 h-4" />
                                Mutasi Ruangan ({{ asset.movements?.length || 0 }})
                            </button>

                            <button
                                @click="activeTab = 'loans'"
                                :class="[
                                    'px-5 py-3.5 text-xs font-bold border-b-2 transition flex items-center gap-1.5',
                                    activeTab === 'loans' ? 'border-[#1e3a8a] text-[#1e3a8a] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'
                                ]"
                            >
                                <Repeat class="w-4 h-4" />
                                Riwayat Peminjaman ({{ asset.loan_items?.length || 0 }})
                            </button>

                            <button
                                @click="activeTab = 'maintenance'"
                                :class="[
                                    'px-5 py-3.5 text-xs font-bold border-b-2 transition flex items-center gap-1.5',
                                    activeTab === 'maintenance' ? 'border-[#1e3a8a] text-[#1e3a8a] bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'
                                ]"
                            >
                                <Wrench class="w-4 h-4" />
                                Log Pemeliharaan ({{ asset.maintenance_tickets?.length || 0 }})
                            </button>
                        </div>

                        <div class="p-6">
                            <!-- Movements -->
                            <div v-if="activeTab === 'movements'" class="space-y-4">
                                <div v-for="m in asset.movements" :key="m.id" class="flex items-start gap-3.5 text-xs">
                                    <div class="w-2.5 h-2.5 rounded-full bg-[#1e3a8a] mt-1 shrink-0"></div>
                                    <div class="flex-1 border-b border-slate-100 pb-3">
                                        <div class="font-bold text-slate-900">
                                            Dipindahkan dari <span class="text-slate-600 font-semibold">{{ m.from_room?.name }}</span> ke <span class="text-[#1e3a8a] font-extrabold">{{ m.to_room?.name }}</span>
                                        </div>
                                        <div class="text-slate-400 text-[11px] mt-0.5">Petugas: {{ m.moved_by?.name }} • {{ m.moved_at }}</div>
                                        <p v-if="m.reason" class="text-slate-600 italic mt-1">{{ m.reason }}</p>
                                    </div>
                                </div>
                                <div v-if="!asset.movements?.length" class="text-center py-6 text-xs text-slate-400">
                                    Aset belum pernah dimutasi dan masih berada di lokasi penempatan awal.
                                </div>
                            </div>

                            <!-- Loans -->
                            <div v-if="activeTab === 'loans'" class="space-y-3">
                                <div v-for="item in asset.loan_items" :key="item.id" class="p-3 bg-slate-50 rounded-xl text-xs border border-slate-100">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold font-mono text-[#1e3a8a]">{{ item.loan_transaction?.transaction_code }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">{{ item.loan_transaction?.status }}</span>
                                    </div>
                                    <div class="text-slate-900 font-bold mt-1">Peminjam: {{ item.loan_transaction?.borrower?.name }}</div>
                                    <div class="text-slate-500 mt-0.5">Periode: {{ item.loan_transaction?.loan_date }} s/d {{ item.loan_transaction?.expected_return_date }}</div>
                                </div>
                                <div v-if="!asset.loan_items?.length" class="text-center py-6 text-xs text-slate-400">
                                    Belum ada catatan peminjaman untuk barang ini.
                                </div>
                            </div>

                            <!-- Maintenance -->
                            <div v-if="activeTab === 'maintenance'" class="space-y-3">
                                <div v-for="t in asset.maintenance_tickets" :key="t.id" class="p-3 bg-slate-50 rounded-xl text-xs border border-slate-100">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold font-mono text-slate-800">{{ t.ticket_code }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 uppercase">{{ t.status }}</span>
                                    </div>
                                    <div class="text-slate-900 font-semibold mt-1">{{ t.issue_description }}</div>
                                    <div class="text-slate-500 mt-1 flex justify-between text-[11px]">
                                        <span>Teknisi: {{ t.technician?.name || 'Belum ditugaskan' }}</span>
                                        <span class="font-bold text-slate-900">Biaya: {{ formatRupiah(t.cost) }}</span>
                                    </div>
                                </div>
                                <div v-if="!asset.maintenance_tickets?.length" class="text-center py-6 text-xs text-slate-400">
                                    Tidak ada tiket perbaikan atau kerusakan tercatat.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right 1 Col: Location Card & Official Label Preview -->
                <div class="space-y-6">
                    <!-- Penempatan Fisik -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-[#1e3a8a] flex items-center justify-center">
                                <MapPin class="w-4 h-4" />
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">Penempatan Fisik Sarana</h3>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-slate-400 block text-[10px] font-bold uppercase tracking-wider">Ruangan / Lab</span>
                                <div class="font-extrabold text-sm text-slate-900 mt-0.5">{{ asset.room?.name }}</div>
                                <div class="text-[11px] text-[#1e3a8a] font-mono mt-0.5 font-semibold">Kode: {{ asset.room?.code }}</div>
                            </div>

                            <div class="space-y-2 text-slate-600 pl-1 text-xs">
                                <div class="flex justify-between border-b border-slate-100 pb-1.5">
                                    <span class="text-slate-400">Gedung:</span>
                                    <strong class="text-slate-800">{{ asset.room?.building?.name }} (Lt. {{ asset.room?.floor_number }})</strong>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-slate-400">Kampus:</span>
                                    <strong class="text-slate-800">{{ asset.room?.building?.campus?.name }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Official Barcode Label Preview -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 text-center">
                        <div class="border-b border-slate-200 pb-2 mb-3">
                            <div class="text-[10px] font-extrabold uppercase tracking-wider text-[#1e3a8a]">UNIVERSITAS AL-KHAIRIYAH</div>
                            <div class="text-[9px] font-bold text-slate-500 tracking-tight">LABEL RESMI INVENTARIS YAYASAN AL-KHAIRIYAH</div>
                        </div>

                        <div class="font-mono text-xs font-bold text-slate-900">{{ asset.asset_code }}</div>

                        <div class="my-4 inline-block p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                            <QrcodeVue
                                :value="asset.qr_code_token"
                                :size="160"
                                level="H"
                                render-as="svg"
                            />
                        </div>

                        <div class="text-[10px] text-slate-400 font-mono mb-4">
                            Token: {{ asset.qr_code_token }}
                        </div>

                        <button
                            @click="printLabel"
                            type="button"
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm shadow-blue-900/25 transition"
                        >
                            <Printer class="w-3.5 h-3.5" />
                            Cetak Stiker Label Fisik
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
