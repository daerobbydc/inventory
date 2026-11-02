<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';
import { Printer, ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    assets: Array,
    summary: Object,
});

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const triggerPrint = () => {
    window.print();
};
</script>

<template>
    <Head title="Cetak Laporan Rekapitulasi Aset Yayasan - SIM-SARPRAS" />

    <div class="min-h-screen bg-slate-100 text-slate-900 font-sans print:bg-white p-4 sm:p-8">
        <!-- Floating Print Bar (Hidden on print) -->
        <div class="max-w-5xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-xl border border-slate-200 shadow-sm print:hidden">
            <div class="flex items-center gap-3">
                <button
                    @click="$window ? window.history.back() : null"
                    onclick="window.history.back()"
                    class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition"
                >
                    <ArrowLeft class="w-4 h-4" />
                </button>
                <div class="text-xs font-bold text-slate-700">Pratinjau Dokumen Resmi Aset Yayasan</div>
            </div>

            <button
                @click="triggerPrint"
                class="px-4 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-lg text-xs font-bold flex items-center gap-2 shadow-sm transition"
            >
                <Printer class="w-4 h-4" />
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>

        <!-- Official Printable Paper Container -->
        <div class="max-w-5xl mx-auto bg-white p-8 sm:p-12 border border-slate-200 shadow-md print:border-none print:shadow-none print:p-0">
            <!-- Kop Surat Resmi -->
            <div class="border-b-2 border-slate-900 pb-4 mb-6 flex items-center gap-6">
                <div class="w-16 h-16 rounded-xl bg-[#1e3a8a] text-white flex items-center justify-center font-black text-xs print:border print:border-slate-800">
                    UNIVAL
                </div>
                <div class="flex-1 text-center pr-16">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-slate-600">YAYASAN AL-KHAIRIYAH CITANGKIL</h2>
                    <h1 class="text-lg font-black uppercase text-slate-900 mt-0.5">UNIVERSITAS AL-KHAIRIYAH</h1>
                    <p class="text-xs font-extrabold text-[#1e3a8a]">BIRO SARANA DAN PRASARANA (SIM-SARPRAS)</p>
                    <p class="text-[10px] text-slate-500 mt-0.5">Jl. H. Enggus Arja No. 1, Citangkil, Kota Cilegon • Website: sarpras.unival.ac.id • Email: sarpras@unival.ac.id</p>
                </div>
            </div>

            <!-- Title -->
            <div class="text-center my-6">
                <h2 class="text-base font-black uppercase tracking-wide text-slate-900">
                    BUKU INDUK INVENTARIS ASET TETAP YAYASAN AL-KHAIRIYAH
                </h2>
                <p class="text-xs text-slate-500 mt-1">Dicetak pada: {{ summary.generated_at }}</p>
            </div>

            <!-- Summary Table Box -->
            <div class="grid grid-cols-4 gap-4 p-4 bg-slate-50 border border-slate-200 rounded-lg mb-6 text-xs">
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Total Aset Terdaftar</span>
                    <strong class="text-sm font-black text-slate-900">{{ summary.total_items }} Unit</strong>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Total Nilai Perolehan</span>
                    <strong class="text-sm font-black text-slate-900">{{ formatRupiah(summary.total_acquisition) }}</strong>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Akumulasi Penyusutan</span>
                    <strong class="text-sm font-black text-amber-700">{{ formatRupiah(summary.total_depreciation) }}</strong>
                </div>
                <div>
                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Nilai Buku Bersih (NBV)</span>
                    <strong class="text-sm font-black text-[#1e3a8a]">{{ formatRupiah(summary.total_book_value) }}</strong>
                </div>
            </div>

            <!-- Items Table -->
            <table class="w-full text-left text-[11px] border-collapse border border-slate-300">
                <thead class="bg-slate-100 uppercase text-[9px] font-black tracking-wider text-slate-700 border-b border-slate-300">
                    <tr>
                        <th class="border border-slate-300 px-3 py-2 text-center w-8">No</th>
                        <th class="border border-slate-300 px-3 py-2">Kode Aset</th>
                        <th class="border border-slate-300 px-3 py-2">Nama Aset / Spesifikasi</th>
                        <th class="border border-slate-300 px-3 py-2">Kategori</th>
                        <th class="border border-slate-300 px-3 py-2">Lokasi Penempatan</th>
                        <th class="border border-slate-300 px-3 py-2 text-right">Harga Perolehan</th>
                        <th class="border border-slate-300 px-3 py-2 text-right">Nilai Buku</th>
                        <th class="border border-slate-300 px-3 py-2 text-center">Kondisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    <tr v-for="(item, idx) in assets" :key="item.id" class="hover:bg-slate-50">
                        <td class="border border-slate-300 px-3 py-1.5 text-center font-bold">{{ idx + 1 }}</td>
                        <td class="border border-slate-300 px-3 py-1.5 font-mono font-bold">{{ item.asset_code }}</td>
                        <td class="border border-slate-300 px-3 py-1.5 font-bold">{{ item.name }}</td>
                        <td class="border border-slate-300 px-3 py-1.5">{{ item.category }}</td>
                        <td class="border border-slate-300 px-3 py-1.5">{{ item.location }}</td>
                        <td class="border border-slate-300 px-3 py-1.5 text-right">{{ formatRupiah(item.purchase_price) }}</td>
                        <td class="border border-slate-300 px-3 py-1.5 text-right font-bold text-[#1e3a8a]">{{ formatRupiah(item.book_value) }}</td>
                        <td class="border border-slate-300 px-3 py-1.5 text-center font-semibold">{{ item.condition }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Signatures Section -->
            <div class="mt-12 grid grid-cols-2 text-center text-xs break-inside-avoid">
                <div>
                    <p class="text-slate-500">Mengetahui,</p>
                    <p class="font-bold text-slate-800">Wakil Rektor Bidang Keuangan & Sarpras</p>
                    <div class="h-20"></div>
                    <p class="font-bold text-slate-900 underline">Prof. Dr. Ir. H. Mulyadi, M.Sc.</p>
                    <p class="text-[10px] text-slate-400">NIP 196805121994031001</p>
                </div>
                <div>
                    <p class="text-slate-500">Kota Cilegon, {{ summary.generated_at.split(' ')[0] }} {{ summary.generated_at.split(' ')[1] }} {{ summary.generated_at.split(' ')[2] }}</p>
                    <p class="font-bold text-slate-800">Kepala Biro Sarana & Prasarana</p>
                    <div class="h-20"></div>
                    <p class="font-bold text-slate-900 underline">Ir. Bambang S., M.T.</p>
                    <p class="text-[10px] text-slate-400">NIP 197409202001121002</p>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
@media print {
    body {
        background-color: white !important;
    }
    @page {
        size: A4 portrait;
        margin: 15mm;
    }
}
</style>

