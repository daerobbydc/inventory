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
    Clock
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
    if (confirm(`Apakah Anda yakin ingin menghapus aset "${props.asset.name}" dari inventaris kampus?`)) {
        router.delete(route('assets.destroy', props.asset.id));
    }
};

const printLabel = () => {
    window.print();
};
</script>

<template>
    <Head :title="`Detail Aset - ${asset.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('assets.index')"
                        class="p-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-100 transition"
                    >
                        <ChevronLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-mono font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100">{{ asset.asset_code }}</span>
                            <span class="text-xs text-slate-400">• {{ asset.category?.name }}</span>
                        </div>
                        <h1 class="text-2xl font-bold text-slate-900 tracking-tight mt-0.5">{{ asset.name }}</h1>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 print:hidden">
                    <button
                        @click="printLabel"
                        type="button"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 rounded-lg text-sm font-semibold shadow-sm transition"
                    >
                        <Printer class="w-4 h-4 text-slate-500" />
                        Cetak Label
                    </button>

                    <Link
                        :href="route('assets.edit', asset.id)"
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm shadow-indigo-100 transition"
                    >
                        <Edit class="w-4 h-4" />
                        Ubah Data
                    </Link>

                    <button
                        @click="deleteAsset"
                        type="button"
                        class="p-2 text-rose-600 hover:bg-rose-50 border border-rose-200 rounded-lg transition"
                        title="Hapus Aset"
                    >
                        <Trash2 class="w-4 h-4" />
                    </button>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Main Info & History Tabs -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Overview Card -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
                    <h2 class="text-base font-bold text-slate-900 mb-4">Informasi Teknis & Pembelian</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-sm">
                        <div>
                            <span class="text-xs text-slate-400 block">Merk & Model</span>
                            <span class="font-medium text-slate-800">{{ asset.brand || '-' }} {{ asset.model ? `(${asset.model})` : '' }}</span>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400 block">Nomor Seri (Serial Number)</span>
                            <span class="font-mono font-medium text-slate-800">{{ asset.serial_number || '-' }}</span>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400 block">Vendor / Rekanan</span>
                            <span class="font-medium text-slate-800">{{ asset.vendor?.name || '-' }}</span>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400 block">Tanggal Pembelian</span>
                            <span class="font-medium text-slate-800">{{ asset.purchase_date || '-' }}</span>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400 block">Harga Perolehan Awal</span>
                            <span class="font-semibold text-slate-900">{{ formatRupiah(asset.purchase_cost) }}</span>
                        </div>

                        <div>
                            <span class="text-xs text-slate-400 block">Estimasi Nilai Buku Saat Ini (Depresiasi)</span>
                            <div class="flex items-center gap-1.5 font-bold text-emerald-600">
                                <TrendingDown class="w-4 h-4" />
                                <span>{{ formatRupiah(currentBookValue) }}</span>
                            </div>
                        </div>
                    </div>

                    <div v-if="asset.specifications" class="mt-5 pt-5 border-t border-slate-100">
                        <span class="text-xs text-slate-400 block mb-1">Spesifikasi Lengkap</span>
                        <p class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-3 rounded-lg border border-slate-100 font-mono whitespace-pre-wrap">{{ asset.specifications }}</p>
                    </div>

                    <div v-if="asset.notes" class="mt-4">
                        <span class="text-xs text-slate-400 block mb-1">Catatan Tambahan</span>
                        <p class="text-xs text-slate-600 italic">{{ asset.notes }}</p>
                    </div>
                </div>

                <!-- History Logs Tab Panel -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="flex border-b border-slate-200 bg-slate-50/50">
                        <button
                            @click="activeTab = 'movements'"
                            :class="[
                                'px-5 py-3 text-xs font-bold border-b-2 transition flex items-center gap-1.5',
                                activeTab === 'movements' ? 'border-indigo-600 text-indigo-600 bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'
                            ]"
                        >
                            <MapPin class="w-4 h-4" />
                            Riwayat Mutasi Ruangan ({{ asset.movements?.length || 0 }})
                        </button>

                        <button
                            @click="activeTab = 'loans'"
                            :class="[
                                'px-5 py-3 text-xs font-bold border-b-2 transition flex items-center gap-1.5',
                                activeTab === 'loans' ? 'border-indigo-600 text-indigo-600 bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'
                            ]"
                        >
                            <Repeat class="w-4 h-4" />
                            Riwayat Peminjaman ({{ asset.loan_items?.length || 0 }})
                        </button>

                        <button
                            @click="activeTab = 'maintenance'"
                            :class="[
                                'px-5 py-3 text-xs font-bold border-b-2 transition flex items-center gap-1.5',
                                activeTab === 'maintenance' ? 'border-indigo-600 text-indigo-600 bg-white' : 'border-transparent text-slate-500 hover:text-slate-800'
                            ]"
                        >
                            <Wrench class="w-4 h-4" />
                            Log Servis & Pemeliharaan ({{ asset.maintenance_tickets?.length || 0 }})
                        </button>
                    </div>

                    <div class="p-5">
                        <!-- Movements Tab -->
                        <div v-if="activeTab === 'movements'" class="space-y-4">
                            <div v-for="m in asset.movements" :key="m.id" class="flex items-start gap-3 text-xs">
                                <div class="w-2 h-2 rounded-full bg-indigo-500 mt-1.5 shrink-0"></div>
                                <div class="flex-1 border-b border-slate-100 pb-3">
                                    <div class="font-semibold text-slate-800">
                                        Dipindahkan dari <span class="text-indigo-600 font-bold">{{ m.from_room?.name }}</span> ke <span class="text-emerald-600 font-bold">{{ m.to_room?.name }}</span>
                                    </div>
                                    <div class="text-slate-500 mt-0.5">Petugas: {{ m.moved_by?.name }} • Waktu: {{ m.moved_at }}</div>
                                    <p v-if="m.reason" class="text-slate-600 italic mt-1">{{ m.reason }}</p>
                                </div>
                            </div>
                            <div v-if="!asset.movements?.length" class="text-center py-6 text-xs text-slate-400">
                                Belum ada riwayat pemindahan ruangan (Aset masih berada di lokasi awal).
                            </div>
                        </div>

                        <!-- Loans Tab -->
                        <div v-if="activeTab === 'loans'" class="space-y-4">
                            <div v-for="item in asset.loan_items" :key="item.id" class="p-3 bg-slate-50 rounded-lg text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold font-mono text-indigo-600">{{ item.loan_transaction?.transaction_code }}</span>
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-blue-100 text-blue-800">{{ item.loan_transaction?.status }}</span>
                                </div>
                                <div class="text-slate-800 font-semibold mt-1">Peminjam: {{ item.loan_transaction?.borrower?.name }}</div>
                                <div class="text-slate-500 mt-0.5">Periode: {{ item.loan_transaction?.loan_date }} s/d {{ item.loan_transaction?.expected_return_date }}</div>
                            </div>
                            <div v-if="!asset.loan_items?.length" class="text-center py-6 text-xs text-slate-400">
                                Belum ada riwayat peminjaman untuk aset ini.
                            </div>
                        </div>

                        <!-- Maintenance Tab -->
                        <div v-if="activeTab === 'maintenance'" class="space-y-4">
                            <div v-for="t in asset.maintenance_tickets" :key="t.id" class="p-3 bg-slate-50 rounded-lg text-xs">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold font-mono text-slate-700">{{ t.ticket_code }}</span>
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-amber-100 text-amber-800 uppercase">{{ t.status }}</span>
                                </div>
                                <div class="text-slate-900 font-medium mt-1">{{ t.issue_description }}</div>
                                <div class="text-slate-500 mt-1 flex justify-between">
                                    <span>Teknisi: {{ t.technician?.name || 'Belum ditugaskan' }}</span>
                                    <span class="font-semibold text-slate-800">Biaya: {{ formatRupiah(t.cost) }}</span>
                                </div>
                            </div>
                            <div v-if="!asset.maintenance_tickets?.length" class="text-center py-6 text-xs text-slate-400">
                                Tidak ada catatan kerusakan atau servis untuk aset ini.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right 1 Col: Physical Location & QR Code Card -->
            <div class="space-y-6">
                <!-- Location Card -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <MapPin class="w-4 h-4 text-indigo-600" />
                        Lokasi Penempatan Fisik
                    </h3>

                    <div class="space-y-3 text-xs">
                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-100">
                            <span class="text-slate-400 block text-[11px]">Ruangan Saat Ini</span>
                            <div class="font-bold text-sm text-slate-900 mt-0.5">{{ asset.room?.name }}</div>
                            <div class="text-slate-500 mt-0.5">Kode Ruangan: <span class="font-mono">{{ asset.room?.code }}</span></div>
                        </div>

                        <div class="space-y-1.5 text-slate-600 pl-1">
                            <div>Gedung: <strong class="text-slate-800">{{ asset.room?.building?.name }} (Lantai {{ asset.room?.floor_number }})</strong></div>
                            <div>Kampus: <strong class="text-slate-800">{{ asset.room?.building?.campus?.name }}</strong></div>
                        </div>
                    </div>
                </div>

                <!-- QR Code Print Preview Card -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 text-center">
                    <div class="text-xs font-bold uppercase tracking-wider text-indigo-600 mb-1">Label Barcode Resmi</div>
                    <div class="font-mono text-xs font-bold text-slate-800">{{ asset.asset_code }}</div>

                    <div class="my-4 inline-block p-3.5 bg-slate-50 border border-slate-200 rounded-xl">
                        <QrcodeVue
                            :value="asset.qr_code_token"
                            :size="160"
                            level="H"
                            render-as="svg"
                        />
                    </div>

                    <div class="text-[11px] text-slate-400 font-mono mb-4">
                        Token: {{ asset.qr_code_token }}
                    </div>

                    <button
                        @click="printLabel"
                        type="button"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-lg text-xs font-semibold shadow-sm transition"
                    >
                        <Printer class="w-3.5 h-3.5" />
                        Cetak Stiker Label
                    </button>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
