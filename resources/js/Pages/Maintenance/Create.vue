<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
    Wrench, 
    ArrowLeft, 
    AlertTriangle, 
    Search, 
    Building2, 
    CheckCircle2,
    ShieldAlert,
    HelpCircle
} from 'lucide-vue-next';

const props = defineProps({
    assets: Array,
    technicians: Array,
    priorities: Array,
});

const assetSearch = ref('');
const selectedAsset = ref(null);

const form = useForm({
    asset_id: '',
    issue_description: '',
    priority: 'medium',
    technician_id: '',
});

const filteredAssets = computed(() => {
    if (!assetSearch.value) return props.assets.slice(0, 8);
    const q = assetSearch.value.toLowerCase();
    return props.assets.filter(a => 
        a.name.toLowerCase().includes(q) || 
        a.asset_code.toLowerCase().includes(q) ||
        (a.room?.name && a.room.name.toLowerCase().includes(q))
    ).slice(0, 10);
});

const selectAsset = (asset) => {
    selectedAsset.value = asset;
    form.asset_id = asset.id;
    assetSearch.value = '';
};

const submit = () => {
    form.post(route('maintenance.store'));
};
</script>

<template>
    <Head title="Lapor Kerusakan Sarpras - SIM-SARPRAS" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('maintenance.index')"
                        class="p-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition shadow-xs"
                    >
                        <ArrowLeft class="w-4 h-4" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                            <span>Pemeliharaan Fasilitas</span>
                            <span>•</span>
                            <span class="text-rose-600 font-bold">Laporan Insiden Kerusakan</span>
                        </div>
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Formulir Lapor Kerusakan Fasilitas</h1>
                    </div>
                </div>
            </div>

            <!-- Form Card -->
            <form @submit.prevent="submit" class="bg-white rounded-2xl border border-slate-200/70 p-6 sm:p-8 shadow-sm space-y-6">
                <!-- Section 1: Pilih Aset / Fasilitas -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        1. Pilih Aset atau Fasilitas yang Rusak <span class="text-rose-500">*</span>
                    </label>

                    <!-- Selected Asset Card Display -->
                    <div v-if="selectedAsset" class="p-4 bg-emerald-50/70 border border-emerald-200 rounded-xl flex items-center justify-between gap-4 mb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold">
                                <CheckCircle2 class="w-5 h-5" />
                            </div>
                            <div>
                                <div class="font-bold text-slate-900 text-sm">{{ selectedAsset.name }}</div>
                                <div class="text-xs text-slate-500 mt-0.5">
                                    <span class="font-mono text-emerald-800 font-semibold">{{ selectedAsset.asset_code }}</span>
                                    <span v-if="selectedAsset.room"> • Lokasi: {{ selectedAsset.room.name }} ({{ selectedAsset.room.building?.name }})</span>
                                </div>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="selectedAsset = null; form.asset_id = ''"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold text-rose-600 hover:bg-rose-50 transition border border-rose-200"
                        >
                            Ganti Aset
                        </button>
                    </div>

                    <!-- Asset Search Selector -->
                    <div v-else class="space-y-2">
                        <div class="relative">
                            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                            <input
                                v-model="assetSearch"
                                type="text"
                                placeholder="Ketik nama aset, kode aset yayasan (misal: AST-2026-...), atau ruangan..."
                                class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] transition"
                            />
                        </div>

                        <!-- Asset Suggestion List -->
                        <div class="border border-slate-200 rounded-xl overflow-hidden divide-y divide-slate-100 max-h-56 overflow-y-auto">
                            <div
                                v-for="asset in filteredAssets"
                                :key="asset.id"
                                @click="selectAsset(asset)"
                                class="p-3 hover:bg-slate-50 cursor-pointer flex items-center justify-between gap-3 text-xs transition"
                            >
                                <div>
                                    <div class="font-bold text-slate-800">{{ asset.name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ asset.asset_code }}</div>
                                </div>
                                <div class="text-right text-[11px] text-slate-500">
                                    {{ asset.room?.name || 'Gudang Pusat' }}
                                </div>
                            </div>
                        </div>
                        <p v-if="form.errors.asset_id" class="text-xs text-rose-600 font-medium">{{ form.errors.asset_id }}</p>
                    </div>
                </div>

                <!-- Section 2: Tingkat Prioritas / Urgensi -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        2. Tingkat Prioritas Penanganan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <!-- Critical -->
                        <div
                            @click="form.priority = 'critical'"
                            :class="[
                                'p-3.5 rounded-xl border-2 cursor-pointer transition flex flex-col justify-between text-left',
                                form.priority === 'critical'
                                    ? 'border-rose-500 bg-rose-50/70'
                                    : 'border-slate-200 hover:border-slate-300'
                            ]"
                        >
                            <span class="text-xs font-black text-rose-700">Kritis / Mendesak</span>
                            <span class="text-[10px] text-slate-500 mt-1">Macet total, KBM/operasional kampus terhenti langsung.</span>
                        </div>

                        <!-- High -->
                        <div
                            @click="form.priority = 'high'"
                            :class="[
                                'p-3.5 rounded-xl border-2 cursor-pointer transition flex flex-col justify-between text-left',
                                form.priority === 'high'
                                    ? 'border-amber-500 bg-amber-50/70'
                                    : 'border-slate-200 hover:border-slate-300'
                            ]"
                        >
                            <span class="text-xs font-black text-amber-700">Tinggi</span>
                            <span class="text-[10px] text-slate-500 mt-1">Fasilitas utama rusak, perlu teknisi hari ini.</span>
                        </div>

                        <!-- Medium -->
                        <div
                            @click="form.priority = 'medium'"
                            :class="[
                                'p-3.5 rounded-xl border-2 cursor-pointer transition flex flex-col justify-between text-left',
                                form.priority === 'medium'
                                    ? 'border-blue-500 bg-blue-50/70'
                                    : 'border-slate-200 hover:border-slate-300'
                            ]"
                        >
                            <span class="text-xs font-black text-blue-700">Sedang</span>
                            <span class="text-[10px] text-slate-500 mt-1">Kerusakan parsial, masih bisa dipakai terbatas.</span>
                        </div>

                        <!-- Low -->
                        <div
                            @click="form.priority = 'low'"
                            :class="[
                                'p-3.5 rounded-xl border-2 cursor-pointer transition flex flex-col justify-between text-left',
                                form.priority === 'low'
                                    ? 'border-emerald-500 bg-emerald-50/70'
                                    : 'border-slate-200 hover:border-slate-300'
                            ]"
                        >
                            <span class="text-xs font-black text-emerald-700">Rendah</span>
                            <span class="text-[10px] text-slate-500 mt-1">Pemeliharaan preventif rutin / kebersihan.</span>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Deskripsi Kerusakan Detail -->
                <div>
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        3. Rincian Gejala & Masalah Kerusakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        v-model="form.issue_description"
                        rows="4"
                        placeholder="Deskripsikan secara detail gejala kerusakan (misal: AC keluar angin panas dan berisik sejak kemarin, lampu indikator merah menyala terus...)"
                        class="w-full p-3 bg-slate-50 border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] transition"
                        required
                    ></textarea>
                    <p v-if="form.errors.issue_description" class="text-xs text-rose-600 font-medium mt-1">{{ form.errors.issue_description }}</p>
                </div>

                <!-- Section 4: Penugasan Teknisi Awal (Hanya Petugas / Teknisi) -->
                <div v-if="$page.props.auth?.user?.can_update_maintenance">
                    <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-2">
                        4. Penugasan Teknisi Langsung (Opsional)
                    </label>
                    <select
                        v-model="form.technician_id"
                        class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                    >
                        <option value="">-- Biarkan Kosong (Akan Didispatch oleh Biro Sarpras) --</option>
                        <option v-for="tech in technicians" :key="tech.id" :value="tech.id">
                            {{ tech.name }} ({{ tech.department || 'Unit Sarpras' }})
                        </option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <Link
                        :href="route('maintenance.index')"
                        class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition"
                    >
                        Batal
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing || !form.asset_id"
                        class="px-6 py-2.5 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-md shadow-blue-900/25 transition disabled:opacity-50"
                    >
                        Kirim Laporan Kerusakan
                    </button>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

