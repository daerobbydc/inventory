<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronLeft, Plus, Trash2, CheckCircle2, Search, Calendar, Box, Repeat } from 'lucide-vue-next';

const props = defineProps({
    suggestedCode: String,
    availableAssets: Array,
});

const form = useForm({
    loan_date: new Date().toISOString().split('T')[0],
    expected_return_date: '',
    purpose: '',
    asset_ids: [],
});

const assetSearch = ref('');

const filteredAvailableAssets = computed(() => {
    if (!assetSearch.value) return props.availableAssets;
    const q = assetSearch.value.toLowerCase();
    return props.availableAssets.filter(a => 
        a.name.toLowerCase().includes(q) || 
        a.asset_code.toLowerCase().includes(q) ||
        (a.category?.name && a.category.name.toLowerCase().includes(q))
    );
});

const isAssetSelected = (id) => form.asset_ids.includes(id);

const toggleAssetSelection = (id) => {
    const idx = form.asset_ids.indexOf(id);
    if (idx > -1) {
        form.asset_ids.splice(idx, 1);
    } else {
        form.asset_ids.push(id);
    }
};

const selectedAssetDetails = computed(() => {
    return props.availableAssets.filter(a => form.asset_ids.includes(a.id));
});

const submit = () => {
    form.post(route('loans.store'));
};
</script>

<template>
    <Head title="Pengajuan Peminjaman - SIM-SARPRAS" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <Link
                        :href="route('loans.index')"
                        class="p-2.5 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition"
                    >
                        <ChevronLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#168a42] bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                                Permohonan Fasilitas
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5">
                            Formulir Peminjaman Sarana & Prasarana
                        </h1>
                        <p class="text-xs text-slate-500">
                            Pilih inventaris yang berstatus tersedia untuk kegiatan akademik, praktikum, atau kemahasiswaan.
                        </p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Cols: Schedule & Asset Picker -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Schedule & Purpose Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 space-y-4">
                        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                            <span class="w-6 h-6 rounded-full bg-[#168a42] text-white flex items-center justify-center text-xs font-bold">1</span>
                            <h2 class="text-sm font-bold text-slate-900">Jadwal & Tujuan Penggunaan</h2>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Mulai Pinjam *</label>
                                <input
                                    v-model="form.loan_date"
                                    type="date"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs font-medium focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                                    :class="form.errors.loan_date ? 'border-rose-400' : 'border-slate-200'"
                                />
                                <span v-if="form.errors.loan_date" class="text-xs text-rose-600 mt-1 block">{{ form.errors.loan_date }}</span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Tenggat Pengembalian *</label>
                                <input
                                    v-model="form.expected_return_date"
                                    type="date"
                                    required
                                    :min="form.loan_date"
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs font-medium focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                                    :class="form.errors.expected_return_date ? 'border-rose-400' : 'border-slate-200'"
                                />
                                <span v-if="form.errors.expected_return_date" class="text-xs text-rose-600 mt-1 block">{{ form.errors.expected_return_date }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Keperluan / Nama Kegiatan Akademik *</label>
                            <textarea
                                v-model="form.purpose"
                                required
                                rows="3"
                                placeholder="Contoh: Praktikum Jaringan Komputer / Seminar Nasional Himpunan Mahasiswa..."
                                class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                                :class="form.errors.purpose ? 'border-rose-400' : 'border-slate-200'"
                            ></textarea>
                            <span v-if="form.errors.purpose" class="text-xs text-rose-600 mt-1 block">{{ form.errors.purpose }}</span>
                        </div>
                    </div>

                    <!-- Available Assets Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-[#168a42] text-white flex items-center justify-center text-xs font-bold">2</span>
                                <div>
                                    <h2 class="text-sm font-bold text-slate-900">Pilih Unit Barang yang Dipinjam</h2>
                                    <p class="text-[11px] text-slate-400">Aset dengan status prima & tersedia di kampus</p>
                                </div>
                            </div>
                            <div class="relative w-full sm:w-64">
                                <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-2.5" />
                                <input
                                    v-model="assetSearch"
                                    type="text"
                                    placeholder="Cari nama atau kode BMN..."
                                    class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                                />
                            </div>
                        </div>

                        <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto pr-1">
                            <div
                                v-for="asset in filteredAvailableAssets"
                                :key="asset.id"
                                @click="toggleAssetSelection(asset.id)"
                                :class="[
                                    'p-3.5 rounded-xl flex items-center justify-between cursor-pointer transition mb-1.5',
                                    isAssetSelected(asset.id) ? 'bg-emerald-50/70 border border-emerald-300' : 'hover:bg-slate-50 border border-transparent'
                                ]"
                            >
                                <div class="flex items-center gap-3">
                                    <input
                                        type="checkbox"
                                        :checked="isAssetSelected(asset.id)"
                                        @click.stop="toggleAssetSelection(asset.id)"
                                        class="rounded-md border-slate-300 text-[#168a42] focus:ring-[#168a42]"
                                    />
                                    <div>
                                        <div class="text-xs font-bold text-slate-900">{{ asset.name }}</div>
                                        <div class="text-[11px] text-slate-500 flex items-center gap-2 mt-0.5">
                                            <span class="font-mono text-[#168a42] font-semibold">{{ asset.asset_code }}</span>
                                            <span>• {{ asset.category?.name }}</span>
                                            <span>• Ruang: {{ asset.room?.name }}</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Tersedia
                                </span>
                            </div>

                            <div v-if="!filteredAvailableAssets.length" class="text-center py-8 text-xs text-slate-400">
                                Tidak ada aset yang cocok dengan kata kunci pencarian.
                            </div>
                        </div>

                        <span v-if="form.errors.asset_ids" class="text-xs text-rose-600 block">{{ form.errors.asset_ids }}</span>
                    </div>
                </div>

                <!-- Right 1 Col: Summary Card -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 sticky top-24">
                        <div class="flex items-center gap-2 pb-3 border-b border-slate-100 mb-4">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 text-[#168a42] flex items-center justify-center">
                                <Box class="w-4 h-4" />
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">Ringkasan Peminjaman</h3>
                        </div>

                        <div class="space-y-2 mb-5">
                            <div v-for="item in selectedAssetDetails" :key="item.id" class="p-3 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between text-xs">
                                <div class="truncate pr-2">
                                    <div class="font-bold text-slate-900 truncate">{{ item.name }}</div>
                                    <div class="font-mono text-[10px] text-slate-400">{{ item.asset_code }}</div>
                                </div>
                                <button
                                    @click="toggleAssetSelection(item.id)"
                                    type="button"
                                    class="text-slate-400 hover:text-rose-600 p-1"
                                    title="Hapus dari daftar"
                                >
                                    <Trash2 class="w-3.5 h-3.5" />
                                </button>
                            </div>

                            <div v-if="!selectedAssetDetails.length" class="text-center py-6 text-xs text-slate-400 italic">
                                Belum ada unit yang dipilih. Silakan centang barang di panel kiri.
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 text-xs text-slate-600 space-y-2 mb-6">
                            <div class="flex justify-between">
                                <span class="text-slate-400">Total Unit Dipinjam:</span>
                                <strong class="text-slate-900 text-sm">{{ form.asset_ids.length }} Barang</strong>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-400">Nama Pemohon:</span>
                                <strong class="text-slate-900">{{ $page.props.auth?.user?.name }}</strong>
                            </div>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing || form.asset_ids.length === 0"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#168a42] hover:bg-[#137a3b] text-white rounded-xl text-xs font-bold shadow-sm shadow-emerald-700/20 transition disabled:opacity-50"
                        >
                            <CheckCircle2 class="w-4 h-4" />
                            {{ form.processing ? 'Mengirim...' : 'Kirim Pengajuan Peminjaman' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
