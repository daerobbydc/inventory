<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ChevronLeft, Save } from 'lucide-vue-next';

const props = defineProps({
    suggestedCode: String,
    categories: Array,
    rooms: Array,
    vendors: Array,
    conditions: Array,
    statuses: Array,
});

const form = useForm({
    asset_code: props.suggestedCode || '',
    name: '',
    brand: '',
    model: '',
    serial_number: '',
    category_id: '',
    room_id: '',
    vendor_id: '',
    purchase_date: '',
    purchase_cost: '',
    useful_life_years: 5,
    salvage_value: 0,
    condition: 'good',
    status: 'available',
    specifications: '',
    notes: '',
});

const submit = () => {
    form.post(route('assets.store'));
};
</script>

<template>
    <Head title="Tambah Aset Tetap Baru" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <Link
                    :href="route('assets.index')"
                    class="p-2 border border-slate-200 rounded-lg text-slate-600 hover:bg-slate-100 transition"
                >
                    <ChevronLeft class="w-5 h-5" />
                </Link>
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Pendaftaran Aset Baru</h1>
                    <p class="text-sm text-slate-500 mt-0.5">Lengkapi formulir untuk menambahkan unit inventaris baru ke sistem kampus.</p>
                </div>
            </div>
        </template>

        <form @submit.prevent="submit" class="max-w-4xl mx-auto space-y-6">
            <!-- Basic Identification Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">1. Identitas Aset</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kode Aset (Unik) *</label>
                        <input
                            v-model="form.asset_code"
                            type="text"
                            required
                            placeholder="Contoh: AST/2026/0001"
                            class="w-full px-3 py-2 border rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            :class="form.errors.asset_code ? 'border-rose-400' : 'border-slate-200'"
                        />
                        <span v-if="form.errors.asset_code" class="text-xs text-rose-600 mt-1 block">{{ form.errors.asset_code }}</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nomor Seri (Serial Number)</label>
                        <input
                            v-model="form.serial_number"
                            type="text"
                            placeholder="S/N pabrikan perangkat"
                            class="w-full px-3 py-2 border rounded-lg text-sm font-mono focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            :class="form.errors.serial_number ? 'border-rose-400' : 'border-slate-200'"
                        />
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Barang / Aset *</label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="Contoh: Laptop Lenovo ThinkPad T14 Gen 3"
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            :class="form.errors.name ? 'border-rose-400' : 'border-slate-200'"
                        />
                        <span v-if="form.errors.name" class="text-xs text-rose-600 mt-1 block">{{ form.errors.name }}</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Merk / Brand</label>
                        <input
                            v-model="form.brand"
                            type="text"
                            placeholder="Contoh: Dell, Epson, Sony"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Model / Tipe</label>
                        <input
                            v-model="form.model"
                            type="text"
                            placeholder="Contoh: OptiPlex 7000"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        />
                    </div>
                </div>
            </div>

            <!-- Classification & Location Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">2. Klasifikasi & Penempatan</h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kategori Aset *</label>
                        <select
                            v-model="form.category_id"
                            required
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            :class="form.errors.category_id ? 'border-rose-400' : 'border-slate-200'"
                        >
                            <option value="">Pilih Kategori...</option>
                            <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                        <span v-if="form.errors.category_id" class="text-xs text-rose-600 mt-1 block">{{ form.errors.category_id }}</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Ruangan Penempatan *</label>
                        <select
                            v-model="form.room_id"
                            required
                            class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                            :class="form.errors.room_id ? 'border-rose-400' : 'border-slate-200'"
                        >
                            <option value="">Pilih Ruangan...</option>
                            <option v-for="room in rooms" :key="room.id" :value="room.id">
                                {{ room.name }} ({{ room.building?.name }})
                            </option>
                        </select>
                        <span v-if="form.errors.room_id" class="text-xs text-rose-600 mt-1 block">{{ form.errors.room_id }}</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Kondisi Fisik *</label>
                        <select
                            v-model="form.condition"
                            required
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        >
                            <option v-for="c in conditions" :key="c.value" :value="c.value">{{ c.label }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Status Ketersediaan *</label>
                        <select
                            v-model="form.status"
                            required
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        >
                            <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Financial & Procurement Card -->
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                <h2 class="text-base font-bold text-slate-900 pb-2 border-b border-slate-100">3. Pengadaan & Nilai Penyusutan</h2>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Vendor / Rekanan</label>
                        <select
                            v-model="form.vendor_id"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        >
                            <option value="">Pilih Vendor...</option>
                            <option v-for="v in vendors" :key="v.id" :value="v.id">{{ v.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Pembelian</label>
                        <input
                            v-model="form.purchase_date"
                            type="date"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Harga Perolehan (Rp)</label>
                        <input
                            v-model="form.purchase_cost"
                            type="number"
                            min="0"
                            placeholder="0"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Masa Manfaat (Tahun) *</label>
                        <input
                            v-model="form.useful_life_years"
                            type="number"
                            required
                            min="1"
                            max="50"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        />
                        <span class="text-[11px] text-slate-400 mt-1 block">Digunakan untuk hitung nilai buku depresiasi</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nilai Residu / Sisa (Rp)</label>
                        <input
                            v-model="form.salvage_value"
                            type="number"
                            min="0"
                            placeholder="0"
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        />
                    </div>
                </div>

                <div class="space-y-4 pt-3 border-t border-slate-100">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Spesifikasi Detail</label>
                        <textarea
                            v-model="form.specifications"
                            rows="3"
                            placeholder="Rincian RAM, processor, storage, daya listrik, atau fitur khusus..."
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        ></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Tambahan</label>
                        <textarea
                            v-model="form.notes"
                            rows="2"
                            placeholder="Sumber dana, kelengkapan aksesoris, atau instruksi penanganan..."
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:outline-none"
                        ></textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Button Bar -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <Link
                    :href="route('assets.index')"
                    class="px-5 py-2.5 border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-sm font-semibold transition"
                >
                    Batal
                </Link>
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-semibold shadow-sm shadow-indigo-200 transition disabled:opacity-50"
                >
                    <Save class="w-4 h-4" />
                    {{ form.processing ? 'Menyimpan...' : 'Simpan & Daftarkan Aset' }}
                </button>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
