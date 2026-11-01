<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronLeft, Plus, Trash2, CheckCircle2, Search, Calendar, Box } from 'lucide-vue-next';

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
    <Head title="Formulir Pengajuan Peminjaman Aset" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('loans.index')"
                    class="p-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-100 transition"
                >
                    <ChevronLeft class="w-5 h-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pengajuan Peminjaman Aset</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Pilih barang inventaris yang tersedia dan tentukan durasi peminjaman.</p>
                </div>
            </div>
        </template>

        <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left 2 Cols: Form Dates, Purpose, Asset Picker -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Date & Purpose Card -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">1. Waktu & Keperluan Peminjaman</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Mulai Pinjam *</label>
                            <input
                                v-model="form.loan_date"
                                type="date"
                                required
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                :class="form.errors.loan_date ? 'border-rose-400' : 'border-slate-200'"
                            />
                            <span v-if="form.errors.loan_date" class="text-xs text-rose-600 mt-1 block">{{ form.errors.loan_date }}</span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tenggat Pengembalian *</label>
                            <input
                                v-model="form.expected_return_date"
                                type="date"
                                required
                                :min="form.loan_date"
                                class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                                :class="form.errors.expected_return_date ? 'border-rose-400' : 'border-slate-200'"
                            />
                            <span v-if="form.errors.expected_return_date" class="text-xs text-rose-600 mt-1 block">{{ form.errors.expected_return_date }}</span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tujuan / Keperluan Kegiatan *</label>
                        <textarea
                            v-model="form.purpose"
                            required
                            rows="3"
                            placeholder="Contoh: Praktikum Pemrograman Web / Dokumentasi Acara Himpunan Mahasiswa..."
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            :class="form.errors.purpose ? 'border-rose-400' : 'border-slate-200'"
                        ></textarea>
                        <span v-if="form.errors.purpose" class="text-xs text-rose-600 mt-1 block">{{ form.errors.purpose }}</span>
                    </div>
                </div>

                <!-- Asset Selection Card -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-2 border-b border-slate-100">
                        <div>
                            <h2 class="text-base font-bold text-slate-900">2. Pilih Aset yang Akan Dipinjam</h2>
                            <p class="text-xs text-slate-500">Hanya menampilkan aset berstatus "Tersedia"</p>
                        </div>
                        <div class="relative w-full sm:w-64">
                            <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
                            <input
                                v-model="assetSearch"
                                type="text"
                                placeholder="Cari nama atau kode..."
                                class="w-full pl-9 pr-3 py-1.5 border border-slate-200 rounded-lg text-xs focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            />
                        </div>
                    </div>

                    <div class="divide-y divide-slate-100 max-h-96 overflow-y-auto pr-1">
                        <div
                            v-for="asset in filteredAvailableAssets"
                            :key="asset.id"
                            @click="toggleAssetSelection(asset.id)"
                            :class="[
                                'p-3 rounded-lg flex items-center justify-between cursor-pointer transition mb-1.5',
                                isAssetSelected(asset.id) ? 'bg-indigo-50/70 border border-indigo-200' : 'hover:bg-slate-50 border border-transparent'
                            ]"
                        >
                            <div class="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    :checked="isAssetSelected(asset.id)"
                                    @click.stop="toggleAssetSelection(asset.id)"
                                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">{{ asset.name }}</div>
                                    <div class="text-xs text-slate-500 flex items-center gap-2 mt-0.5">
                                        <span class="font-mono text-indigo-600 font-medium">{{ asset.asset_code }}</span>
                                        <span>• {{ asset.category?.name }}</span>
                                        <span>• Ruang: {{ asset.room?.name }}</span>
                                    </div>
                                </div>
                            </div>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                Tersedia
                            </span>
                        </div>

                        <div v-if="!filteredAvailableAssets.length" class="text-center py-8 text-xs text-slate-400">
                            Tidak ada aset tersedia yang cocok dengan pencarian.
                        </div>
                    </div>

                    <span v-if="form.errors.asset_ids" class="text-xs text-rose-600 block">{{ form.errors.asset_ids }}</span>
                </div>
            </div>

            <!-- Right 1 Col: Summary & Checkout Card -->
            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5 sticky top-24">
                    <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
                        <Box class="w-4 h-4 text-indigo-600" />
                        Ringkasan Barang Dipinjam
                    </h3>

                    <div class="space-y-2 mb-4">
                        <div v-for="item in selectedAssetDetails" :key="item.id" class="p-2.5 bg-slate-50 rounded-lg border border-slate-100 flex items-center justify-between text-xs">
                            <div class="truncate pr-2">
                                <div class="font-semibold text-slate-900 truncate">{{ item.name }}</div>
                                <div class="font-mono text-[11px] text-slate-400">{{ item.asset_code }}</div>
                            </div>
                            <button
                                @click="toggleAssetSelection(item.id)"
                                type="button"
                                class="text-slate-400 hover:text-rose-600 p-1"
                                title="Batal Pilih"
                            >
                                <Trash2 class="w-3.5 h-3.5" />
                            </button>
                        </div>

                        <div v-if="!selectedAssetDetails.length" class="text-center py-6 text-xs text-slate-400 italic">
                            Belum ada aset yang dipilih. Silakan centang barang di sebelah kiri.
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 text-xs text-slate-600 space-y-2 mb-5">
                        <div class="flex justify-between">
                            <span>Total Unit Dipinjam:</span>
                            <span class="font-bold text-slate-900">{{ form.asset_ids.length }} Barang</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Peminjam:</span>
                            <span class="font-medium text-slate-900">{{ $page.props.auth?.user?.name }}</span>
                        </div>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing || form.asset_ids.length === 0"
                        class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm shadow-indigo-200 transition disabled:opacity-50"
                    >
                        <CheckCircle2 class="w-4 h-4" />
                        {{ form.processing ? 'Mengirim...' : 'Kirim Pengajuan Peminjaman' }}
                    </button>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
