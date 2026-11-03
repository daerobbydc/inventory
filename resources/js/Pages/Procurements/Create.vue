<script setup>
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { 
    Plus, 
    Trash2, 
    ArrowLeft, 
    Send, 
    Building2, 
    FileText, 
    Layers, 
    DollarSign, 
    Info, 
    AlertCircle,
    CheckCircle2
} from 'lucide-vue-next';

const props = defineProps({
    faculties: Array,
    categories: Array,
    rooms: Array,
    suggestedNumber: String,
    userFacultyId: [Number, String],
});

const form = useForm({
    title: '',
    faculty_id: props.userFacultyId || (props.faculties[0]?.id || ''),
    fiscal_year: '2026',
    budget_source: 'Dana Yayasan Al-Khairiyah',
    urgency: 'reguler',
    justification: '',
    items: [
        {
            item_name: '',
            category_id: '',
            brand: '',
            model: '',
            quantity: 1,
            unit_price_estimate: 0,
            target_room_id: '',
            specifications: '',
        }
    ],
});

const addItem = () => {
    form.items.push({
        item_name: '',
        category_id: '',
        brand: '',
        model: '',
        quantity: 1,
        unit_price_estimate: 0,
        target_room_id: '',
        specifications: '',
    });
};

const removeItem = (index) => {
    if (form.items.length > 1) {
        form.items.splice(index, 1);
    }
};

const totalEstimatedBudget = computed(() => {
    return form.items.reduce((sum, item) => {
        const qty = Number(item.quantity) || 0;
        const price = Number(item.unit_price_estimate) || 0;
        return sum + (qty * price);
    }, 0);
});

const formatRupiah = (val) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        maximumFractionDigits: 0,
    }).format(val || 0);
};

const submit = () => {
    form.post(route('procurements.store'));
};
</script>

<template>
    <Head title="Ajukan Usulan Pengadaan Aset - SIM-SARPRAS" />

    <AuthenticatedLayout>
        <div class="max-w-5xl mx-auto space-y-6">
            <!-- Header & Back Button -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('procurements.index')"
                        class="p-2 text-slate-500 hover:text-slate-800 hover:bg-white rounded-xl transition border border-slate-200 shadow-xs"
                    >
                        <ArrowLeft class="w-4 h-4" />
                    </Link>
                    <div>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">
                            Formulir Usulan Pengadaan Aset Baru
                        </h1>
                        <p class="text-xs text-slate-500">
                            Ajukan perencanaan kebutuhan fasilitas & inventaris dari fakultas ke Biro Sarpras Kampus.
                        </p>
                    </div>
                </div>

                <div class="hidden sm:inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-blue-50 text-blue-900 border border-blue-200 text-xs font-mono font-bold">
                    <span>No. Usulan: {{ suggestedNumber }}</span>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <!-- Section 1: Informasi Umum Usulan -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-5">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#1a73e8] flex items-center justify-center font-bold">
                            <FileText class="w-4 h-4" />
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-900">1. Informasi Umum Pengajuan</h2>
                            <p class="text-[11px] text-slate-500">Identitas proposal pengadaan dan alokasi fakultas</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <!-- Judul Usulan -->
                        <div class="md:col-span-2 space-y-1">
                            <label class="text-xs font-bold text-slate-700">
                                Judul Proposal Pengadaan <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                placeholder="Contoh: Pengadaan 30 Unit PC Desktop Lab Komputer & 2 Proyektor Portable"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 focus:border-[#1a73e8] transition"
                                required
                            />
                            <p v-if="form.errors.title" class="text-rose-600 text-[11px]">{{ form.errors.title }}</p>
                        </div>

                        <!-- Fakultas Pemohon -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">
                                Fakultas / Unit Pemohon <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.faculty_id"
                                :disabled="!$page.props.auth?.user?.can_manage_settings && userFacultyId"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition disabled:opacity-75 disabled:bg-slate-100"
                                required
                            >
                                <option v-for="fac in faculties" :key="fac.id" :value="fac.id">
                                    {{ fac.name }} ({{ fac.code }})
                                </option>
                            </select>
                            <p v-if="form.errors.faculty_id" class="text-rose-600 text-[11px]">{{ form.errors.faculty_id }}</p>
                        </div>

                        <!-- Tahun Anggaran -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">
                                Periode Tahun Anggaran <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.fiscal_year"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                                required
                            >
                                <option value="2026">TA 2026</option>
                                <option value="2026/2027">TA 2026/2027 (Ganjil)</option>
                                <option value="2027">TA 2027</option>
                            </select>
                            <p v-if="form.errors.fiscal_year" class="text-rose-600 text-[11px]">{{ form.errors.fiscal_year }}</p>
                        </div>

                        <!-- Asal Sumber Anggaran -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">
                                Rencana Sumber Dana <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.budget_source"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                                required
                            >
                                <option value="Dana Yayasan Al-Khairiyah">Dana Yayasan Al-Khairiyah</option>
                                <option value="BOPTN / DIPA">BOPTN / DIPA Kementerian</option>
                                <option value="Hibah Riset Tridharma">Hibah Riset & Pengabdian</option>
                                <option value="CSR Mitra Industri / Alumni">CSR Mitra Industri / Donasi</option>
                            </select>
                            <p v-if="form.errors.budget_source" class="text-rose-600 text-[11px]">{{ form.errors.budget_source }}</p>
                        </div>

                        <!-- Tingkat Urgensi -->
                        <div class="space-y-1">
                            <label class="text-xs font-bold text-slate-700">
                                Tingkat Urgensi Kebutuhan <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.urgency"
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                                required
                            >
                                <option value="reguler">Reguler (Kebutuhan Rutin Semesteran)</option>
                                <option value="urgent">Mendesak (Unit Rusak / Kelas Baru)</option>
                                <option value="critical">Kritis / Instrumen Akreditasi BAN-PT</option>
                            </select>
                            <p v-if="form.errors.urgency" class="text-rose-600 text-[11px]">{{ form.errors.urgency }}</p>
                        </div>

                        <!-- Justifikasi Urgensi Kebutuhan -->
                        <div class="md:col-span-2 space-y-1">
                            <label class="text-xs font-bold text-slate-700">
                                Justifikasi & Alasan Kebutuhan <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                v-model="form.justification"
                                rows="3"
                                placeholder="Jelaskan urgensi permohonan, rasio keterbutuhan mahasiswa, atau syarat visitasi akreditasi prodi..."
                                class="w-full px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 focus:border-[#1a73e8] transition"
                                required
                            ></textarea>
                            <p v-if="form.errors.justification" class="text-rose-600 text-[11px]">{{ form.errors.justification }}</p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Rincian Barang yang Diusulkan (Dynamic Items) -->
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 text-[#1a73e8] flex items-center justify-center font-bold">
                                <Layers class="w-4 h-4" />
                            </div>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">2. Rincian Barang yang Diusulkan</h2>
                                <p class="text-[11px] text-slate-500">Daftar item sarana, kuantitas, dan estimasi biaya per unit</p>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="addItem"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-[#1a73e8] hover:bg-blue-100 rounded-xl text-xs font-bold transition"
                        >
                            <Plus class="w-3.5 h-3.5 stroke-[2.5]" />
                            <span>Tambah Baris Barang</span>
                        </button>
                    </div>

                    <!-- Item Rows -->
                    <div class="space-y-4">
                        <div
                            v-for="(item, idx) in form.items"
                            :key="idx"
                            class="p-4 rounded-2xl border border-slate-200/90 bg-slate-50/50 space-y-4 relative"
                        >
                            <div class="flex items-center justify-between">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-slate-800 text-white font-bold text-xs">
                                    {{ idx + 1 }}
                                </span>

                                <button
                                    v-if="form.items.length > 1"
                                    type="button"
                                    @click="removeItem(idx)"
                                    class="text-rose-500 hover:text-rose-700 p-1 rounded-lg hover:bg-rose-50 transition"
                                    title="Hapus baris ini"
                                >
                                    <Trash2 class="w-4 h-4" />
                                </button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 text-xs">
                                <!-- Nama Barang -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Nama Barang / Fasilitas *</label>
                                    <input
                                        v-model="item.item_name"
                                        type="text"
                                        placeholder="Contoh: PC Desktop Core i7"
                                        class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                                        required
                                    />
                                </div>

                                <!-- Kategori Aset -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Kategori Aset</label>
                                    <select
                                        v-model="item.category_id"
                                        class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                                    >
                                        <option value="">Pilih Kategori</option>
                                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                            {{ cat.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Target Ruangan Penempatan -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Ruangan Penempatan</label>
                                    <select
                                        v-model="item.target_room_id"
                                        class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                                    >
                                        <option value="">Pilih Target Ruangan</option>
                                        <option v-for="r in rooms" :key="r.id" :value="r.id">
                                            {{ r.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Merek -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Merek / Brand (Opsional)</label>
                                    <input
                                        v-model="item.brand"
                                        type="text"
                                        placeholder="Contoh: Lenovo / Dell / Epson"
                                        class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                                    />
                                </div>

                                <!-- Jumlah Unit -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Jumlah Unit (Qty) *</label>
                                    <input
                                        v-model.number="item.quantity"
                                        type="number"
                                        min="1"
                                        class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition font-bold"
                                        required
                                    />
                                </div>

                                <!-- Estimasi Harga Satuan -->
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-700">Estimasi Harga Satuan (Rp) *</label>
                                    <input
                                        v-model.number="item.unit_price_estimate"
                                        type="number"
                                        min="0"
                                        step="1000"
                                        placeholder="0"
                                        class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition font-mono"
                                        required
                                    />
                                </div>

                                <!-- Spesifikasi Minimal -->
                                <div class="md:col-span-3 space-y-1">
                                    <label class="font-bold text-slate-700">Spesifikasi Teknis Minimal</label>
                                    <input
                                        v-model="item.specifications"
                                        type="text"
                                        placeholder="Contoh: RAM 16GB, SSD 512GB, Layar 24 Inch IPS, OS Windows 11 Pro"
                                        class="w-full px-3 py-1.5 bg-white border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#1a73e8]/30 transition"
                                    />
                                </div>
                            </div>

                            <!-- Baris Subtotal -->
                            <div class="pt-2 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                                <span>Subtotal baris ini:</span>
                                <span class="font-bold text-slate-800">
                                    {{ formatRupiah((Number(item.quantity) || 0) * (Number(item.unit_price_estimate) || 0)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Total Estimasi Anggaran Card -->
                    <div class="bg-gradient-to-r from-blue-900 to-[#1a73e8] text-white rounded-2xl p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 shadow-md">
                        <div>
                            <span class="text-[11px] font-bold text-blue-200 uppercase tracking-wider block">
                                TOTAL REKAPITULASI USULAN ANGGARAN (RAB)
                            </span>
                            <div class="text-2xl sm:text-3xl font-black text-white mt-1">
                                {{ formatRupiah(totalEstimatedBudget) }}
                            </div>
                            <span class="text-xs text-blue-100/80">
                                Total {{ form.items.length }} jenis barang pengadaan
                            </span>
                        </div>

                        <div class="flex items-center gap-3 shrink-0">
                            <Link
                                :href="route('procurements.index')"
                                class="px-4 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition"
                            >
                                Batal
                            </Link>

                            <button
                                type="submit"
                                :disabled="form.processing || !form.items.length"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white hover:bg-blue-50 text-[#0c182c] rounded-xl text-xs font-extrabold shadow-md transition disabled:opacity-50"
                            >
                                <Send class="w-4 h-4 text-[#1a73e8]" />
                                <span>{{ form.processing ? 'Mengirimkan...' : 'Kirim Usulan ke Sarpras' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
