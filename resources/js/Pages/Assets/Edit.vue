<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronLeft, Save, AlertCircle } from 'lucide-vue-next';

const props = defineProps({
    asset: Object,
    categories: Array,
    rooms: Array,
    vendors: Array,
    conditions: Array,
    statuses: Array,
});

const form = useForm({
    asset_code: props.asset.asset_code || '',
    name: props.asset.name || '',
    brand: props.asset.brand || '',
    model: props.asset.model || '',
    serial_number: props.asset.serial_number || '',
    category_id: props.asset.category_id || '',
    room_id: props.asset.room_id || '',
    vendor_id: props.asset.vendor_id || '',
    purchase_date: props.asset.purchase_date || '',
    purchase_cost: props.asset.purchase_cost || '',
    useful_life_years: props.asset.useful_life_years || 5,
    salvage_value: props.asset.salvage_value || 0,
    condition: props.asset.condition || 'good',
    status: props.asset.status || 'available',
    specifications: props.asset.specifications || '',
    notes: props.asset.notes || '',
});

const submit = () => {
    form.put(route('assets.update', props.asset.id));
};
</script>

<template>
    <Head :title="`Ubah Data BMN - ${asset.name}`" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <Link
                        :href="route('assets.show', asset.id)"
                        class="p-2.5 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition"
                    >
                        <ChevronLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#168a42] bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                                {{ asset.asset_code }}
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5">
                            Pembaruan Data Aset & Inventaris
                        </h1>
                        <p class="text-xs text-slate-500">
                            Perubahan spesifikasi teknis, kondisi kelayakan fisik, atau pemindahan lokasi penempatan ruangan.
                        </p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="max-w-4xl mx-auto space-y-6">
                <!-- Section 1 -->
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <span class="w-6 h-6 rounded-full bg-[#168a42] text-white flex items-center justify-center text-xs font-bold">1</span>
                        <h2 class="text-sm font-bold text-slate-900">Identitas & Spesifikasi Barang</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kode Aset (Unik / BMN) *</label>
                            <input
                                v-model="form.asset_code"
                                type="text"
                                required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs font-mono font-bold focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                                :class="form.errors.asset_code ? 'border-rose-400' : 'border-slate-200'"
                            />
                            <span v-if="form.errors.asset_code" class="text-xs text-rose-600 mt-1 block">{{ form.errors.asset_code }}</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Seri Pabrikan (Serial Number)</label>
                            <input
                                v-model="form.serial_number"
                                type="text"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs font-mono focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                            />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Barang / Aset *</label>
                            <input
                                v-model="form.name"
                                type="text"
                                required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs font-semibold focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                                :class="form.errors.name ? 'border-rose-400' : 'border-slate-200'"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Merk / Brand</label>
                            <input
                                v-model="form.brand"
                                type="text"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Model / Tipe</label>
                            <input
                                v-model="form.model"
                                type="text"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                            />
                        </div>
                    </div>
                </div>

                <!-- Section 2 -->
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-[#168a42] text-white flex items-center justify-center text-xs font-bold">2</span>
                            <h2 class="text-sm font-bold text-slate-900">Klasifikasi & Penempatan Ruangan</h2>
                        </div>
                        <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-100 flex items-center gap-1">
                            <AlertCircle class="w-3.5 h-3.5" />
                            Mutasi otomatis tercatat jika ruangan berganti
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Barang *</label>
                            <select
                                v-model="form.category_id"
                                required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                            >
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Ruangan Penempatan *</label>
                            <select
                                v-model="form.room_id"
                                required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                            >
                                <option v-for="room in rooms" :key="room.id" :value="room.id">
                                    {{ room.name }} ({{ room.building?.name }})
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kondisi Fisik *</label>
                            <select
                                v-model="form.condition"
                                required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                            >
                                <option v-for="c in conditions" :key="c.value" :value="c.value">{{ c.label }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Status Ketersediaan *</label>
                            <select
                                v-model="form.status"
                                required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                            >
                                <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 3 -->
                <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 space-y-4">
                    <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                        <span class="w-6 h-6 rounded-full bg-[#168a42] text-white flex items-center justify-center text-xs font-bold">3</span>
                        <h2 class="text-sm font-bold text-slate-900">Pengadaan & Perhitungan Depresiasi BMN</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Vendor / Rekanan</label>
                            <select
                                v-model="form.vendor_id"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                            >
                                <option value="">Pilih Vendor...</option>
                                <option v-for="v in vendors" :key="v.id" :value="v.id">{{ v.name }}</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pembelian</label>
                            <input
                                v-model="form.purchase_date"
                                type="date"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Harga Perolehan (Rp)</label>
                            <input
                                v-model="form.purchase_cost"
                                type="number"
                                min="0"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Masa Manfaat (Tahun) *</label>
                            <input
                                v-model="form.useful_life_years"
                                type="number"
                                required
                                min="1"
                                max="50"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nilai Residu / Sisa (Rp)</label>
                            <input
                                v-model="form.salvage_value"
                                type="number"
                                min="0"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                            />
                        </div>
                    </div>

                    <div class="space-y-4 pt-3 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Spesifikasi Detail Teknis</label>
                            <textarea
                                v-model="form.specifications"
                                rows="3"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan BMN</label>
                            <textarea
                                v-model="form.notes"
                                rows="2"
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] focus:outline-none"
                            ></textarea>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <Link
                        :href="route('assets.show', asset.id)"
                        class="px-5 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#168a42] hover:bg-[#137a3b] text-white rounded-xl text-xs font-bold shadow-sm shadow-emerald-700/20 transition disabled:opacity-50"
                    >
                        <Save class="w-4 h-4" />
                        {{ form.processing ? 'Menyimpan...' : 'Perbarui Data Aset' }}
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
