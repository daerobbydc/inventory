<script setup>
import { Head } from '@inertiajs/vue3';
import { Printer, ArrowLeft } from 'lucide-vue-next';

const props = defineProps({
    asset: Object,
    meta: Object,
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
    <Head :title="`Berita Acara Penghapusan Aset - ${asset.asset_code}`" />

    <div class="min-h-screen bg-slate-100 text-slate-900 font-sans print:bg-white p-4 sm:p-8">
        <!-- Print Toolbar -->
        <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-xl border border-slate-200 shadow-sm print:hidden">
            <div class="flex items-center gap-3">
                <button
                    onclick="window.history.back()"
                    class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition"
                >
                    <ArrowLeft class="w-4 h-4" />
                </button>
                <div class="text-xs font-bold text-slate-700">Pratinjau Berita Acara Penghapusan Aset Yayasan</div>
            </div>

            <button
                @click="triggerPrint"
                class="px-4 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-lg text-xs font-bold flex items-center gap-2 shadow-sm transition"
            >
                <Printer class="w-4 h-4" />
                <span>Cetak / Simpan PDF</span>
            </button>
        </div>

        <!-- Official Document Paper -->
        <div class="max-w-4xl mx-auto bg-white p-8 sm:p-12 border border-slate-200 shadow-md print:border-none print:shadow-none print:p-0">
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
                <h2 class="text-base font-black uppercase tracking-wide text-slate-900 underline">
                    BERITA ACARA PENGHAPUSAN ASET TETAP YAYASAN
                </h2>
                <p class="text-xs font-mono font-bold text-slate-700 mt-1">Nomor: {{ meta.ba_code }}</p>
            </div>

            <div class="text-xs text-slate-700 leading-relaxed space-y-4">
                <p>
                    Pada hari ini, tanggal <strong>{{ meta.date }}</strong>, bertempat di Kantor Biro Sarana dan Prasarana Universitas Al-Khairiyah, telah dilakukan pemeriksaan fisik dan verifikasi administrasi terhadap Aset Inventaris Yayasan di bawah ini:
                </p>

                <!-- Asset Detail Table -->
                <div class="border border-slate-300 rounded-lg overflow-hidden my-4">
                    <table class="w-full text-xs">
                        <tbody class="divide-y divide-slate-200">
                            <tr>
                                <td class="px-4 py-2 bg-slate-50 font-bold w-48">Kode Register Aset</td>
                                <td class="px-4 py-2 font-mono font-bold">{{ asset.asset_code }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 bg-slate-50 font-bold">Nama Barang / Spesifikasi</td>
                                <td class="px-4 py-2 font-bold">{{ asset.name }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 bg-slate-50 font-bold">Golongan Kategori</td>
                                <td class="px-4 py-2">{{ asset.category }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 bg-slate-50 font-bold">Lokasi Penempatan Terakhir</td>
                                <td class="px-4 py-2">{{ asset.location }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 bg-slate-50 font-bold">Harga Perolehan Historis</td>
                                <td class="px-4 py-2 font-semibold">{{ formatRupiah(asset.purchase_cost) }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 bg-slate-50 font-bold">Nilai Residu / Scrap Terjual</td>
                                <td class="px-4 py-2 font-bold text-emerald-800">{{ formatRupiah(asset.salvage_value) }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 bg-slate-50 font-bold">Kondisi Terakhir Fisik</td>
                                <td class="px-4 py-2 font-bold text-rose-700">{{ asset.condition }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-2 bg-slate-50 font-bold">Catatan & Justifikasi Teknis</td>
                                <td class="px-4 py-2">{{ asset.notes }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <p>
                    Berdasarkan hasil pemeriksaan fisik, aset tersebut dinyatakan secara teknis dan ekonomis <strong>tidak dapat dipergunakan kembali</strong> untuk menunjang kegiatan operasional tridharma perguruan tinggi. Oleh karena itu, direkomendasikan dan disetujui untuk <strong>dihapuskan dari Buku Induk Inventaris Universitas Al-Khairiyah</strong>.
                </p>
                <p>
                    Demikian Berita Acara ini dibuat dengan sebenarnya dan ditandatangani untuk dipergunakan sebagaimana mestinya.
                </p>
            </div>

            <!-- Signatures Section -->
            <div class="mt-12 grid grid-cols-2 text-center text-xs break-inside-avoid">
                <div>
                    <p class="text-slate-500">Menyetujui,</p>
                    <p class="font-bold text-slate-800">Wakil Rektor Bidang Keuangan & Sarpras</p>
                    <div class="h-20"></div>
                    <p class="font-bold text-slate-900 underline">Prof. Dr. Ir. H. Mulyadi, M.Sc.</p>
                    <p class="text-[10px] text-slate-400">NIP 196805121994031001</p>
                </div>
                <div>
                    <p class="text-slate-500">Kota Cilegon, {{ meta.date }}</p>
                    <p class="font-bold text-slate-800">Kepala Biro Sarana & Prasarana</p>
                    <div class="h-20"></div>
                    <p class="font-bold text-slate-900 underline">Ir. Bambang S., M.T.</p>
                    <p class="text-[10px] text-slate-400">NIP 197409202001121002</p>
                </div>
            </div>
        </div>
    </div>
</template>

