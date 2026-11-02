<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    ArchiveX, 
    Plus, 
    AlertTriangle, 
    FileText, 
    Printer, 
    DollarSign, 
    X, 
    Layers, 
    Check,
    Building2,
    Calendar
} from 'lucide-vue-next';

const props = defineProps({
    disposedAssets: Object,
    eligibleAssets: Array,
    metrics: Object,
});

const showModal = ref(false);

const form = useForm({
    asset_id: '',
    disposal_method: 'musnah',
    salvage_value: 0,
    disposal_reason: '',
});

const submitDisposal = () => {
    form.post(route('disposals.store'), {
        onSuccess: () => {
            showModal.value = false;
            form.reset();
        },
    });
};

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};
</script>

<template>
    <Head title="Penghapusan Aset Yayasan - SIM-SARPRAS" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Banner -->
            <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/70 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                            Tata Kelola Aset Siklus Akhir
                        </span>
                        <span>•</span>
                        <span>Berita Acara Penghapusan</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Penghapusan & Pemutihan Aset Yayasan</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Pencatatan resmi pelepasan aset yang rusak berat, hilang, atau melewati masa manfaat ekonomis dengan Berita Acara resmi.
                    </p>
                </div>

                <div class="flex items-center gap-3 shrink-0">
                    <button
                        @click="showModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm transition"
                    >
                        <Plus class="w-4 h-4 stroke-[2.5]" />
                        <span>+ Ajukan Pemutihan Aset</span>
                    </button>
                </div>
            </div>

            <!-- 3 Stat Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-[#fff1f2]/80 border border-rose-100 rounded-2xl p-5 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-rose-700">TOTAL ASET DIHAPUSKAN</span>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ metrics.total_disposed }} Unit</div>
                    <div class="text-[11px] text-slate-500 mt-1">Telah diterbitkan Berita Acara Penghapusan</div>
                </div>

                <div class="bg-[#fffbeb]/90 border border-amber-100 rounded-2xl p-5 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-amber-700">ASET MEMENUHI KRITERIA PEMUTIHAN</span>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ metrics.eligible_count }} Unit</div>
                    <div class="text-[11px] text-slate-500 mt-1">Kondisi rusak berat atau dalam pemeliharaan</div>
                </div>

                <div class="bg-[#f0fdf4]/90 border border-emerald-100 rounded-2xl p-5 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-emerald-700">NILAI RESIDU / SCRAP TERTAMPUNG</span>
                    <div class="text-2xl font-black text-slate-900 mt-1">{{ formatRupiah(metrics.total_salvage_recovered) }}</div>
                    <div class="text-[11px] text-slate-500 mt-1">Hasil lelang / pelepasan besi tua</div>
                </div>
            </div>

            <!-- Table of Disposed Assets -->
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Daftar Aset yang Telah Dihapuskan (Disposed)</h3>
                        <p class="text-[11px] text-slate-500">Aset yang telah dilepas dari neraca buku induk universitas</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">Kode Aset</th>
                                <th class="px-4 py-3.5">Nama Aset</th>
                                <th class="px-4 py-3.5">Kategori</th>
                                <th class="px-4 py-3.5 text-right">Harga Perolehan</th>
                                <th class="px-4 py-3.5 text-right">Nilai Residu</th>
                                <th class="px-4 py-3.5">Alasan / Catatan Berita Acara</th>
                                <th class="px-6 py-3.5 text-center">Cetak Berita Acara</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="asset in disposedAssets.data" :key="asset.id" class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 font-mono font-bold text-rose-800">{{ asset.asset_code }}</td>
                                <td class="px-4 py-4 font-bold text-slate-900">{{ asset.name }}</td>
                                <td class="px-4 py-4 text-slate-600">{{ asset.category?.name }}</td>
                                <td class="px-4 py-4 text-right font-medium text-slate-700">{{ formatRupiah(asset.purchase_cost) }}</td>
                                <td class="px-4 py-4 text-right font-bold text-emerald-700">{{ formatRupiah(asset.salvage_value) }}</td>
                                <td class="px-4 py-4 text-slate-600 max-w-xs truncate">{{ asset.notes || '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    <Link
                                        :href="route('disposals.print', asset.id)"
                                        target="_blank"
                                        class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition inline-flex items-center gap-1.5"
                                    >
                                        <Printer class="w-3.5 h-3.5" />
                                        <span>Cetak BA</span>
                                    </Link>
                                </td>
                            </tr>
                            <tr v-if="disposedAssets.data.length === 0">
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                    <ArchiveX class="w-10 h-10 mx-auto text-slate-300 mb-2 stroke-[1.5]" />
                                    <p class="text-sm font-semibold text-slate-600">Belum ada aset yang dihapuskan</p>
                                    <p class="text-xs text-slate-400 mt-1">Gunakan tombol "Ajukan Pemutihan Aset" untuk memproses barang rusak berat.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal: Ajukan Pemutihan -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">Form Pengajuan Berita Acara Pemutihan Aset</h3>
                    <button @click="showModal = false" class="text-slate-400 hover:text-slate-600">
                        <X class="w-4 h-4" />
                    </button>
                </div>
                <form @submit.prevent="submitDisposal" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Aset yang Diusulkan Hapus</label>
                        <select
                            v-model="form.asset_id"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            required
                        >
                            <option value="" disabled>-- Pilih Aset Rusak Berat / Maintenance --</option>
                            <option v-for="a in eligibleAssets" :key="a.id" :value="a.id">
                                {{ a.name }} ({{ a.asset_code }}) - Kondisi: {{ a.condition?.label || a.condition }}
                            </option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pelepasan</label>
                            <select
                                v-model="form.disposal_method"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            >
                                <option value="musnah">Dimusnahkan (Rusak Total)</option>
                                <option value="lelang">Lelang Publik / Besi Tua</option>
                                <option value="hibah">Hibah / Donasi Sosial</option>
                                <option value="daur_ulang">Daur Ulang / Kanibalisasi</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Residu / Scrap (Rp)</label>
                            <input
                                v-model="form.salvage_value"
                                type="number"
                                min="0"
                                placeholder="0"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Detail Penghapusan</label>
                        <textarea
                            v-model="form.disposal_reason"
                            rows="3"
                            placeholder="Tuliskan justifikasi teknis (misal: Kompresor AC meledak, biaya servis melebihi 80% harga beli baru...)"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            required
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button
                            type="button"
                            @click="showModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm transition disabled:opacity-50"
                        >
                            Terbitkan Berita Acara Hapus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

