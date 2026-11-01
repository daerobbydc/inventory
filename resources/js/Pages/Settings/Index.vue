<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import { 
    Settings, 
    Building2, 
    Layers, 
    Users, 
    Wifi, 
    CheckCircle2, 
    ShieldCheck, 
    ExternalLink,
    Clock,
    RefreshCw,
    UserCheck,
    Store
} from 'lucide-vue-next';

const props = defineProps({
    stats: Object,
    campuses: Array,
    buildings: Array,
    rooms: Array,
    categories: Array,
    vendors: Array,
    syncStatus: Object,
});

const activeTab = ref('rooms');
const isSyncing = ref(false);

const triggerManualSync = () => {
    isSyncing.value = true;
    setTimeout(() => {
        isSyncing.value = false;
        alert('Sinkronisasi data BMN dengan Kemdikbudristek berhasil diperbarui.');
    }, 1200);
};
</script>

<template>
    <Head title="Pengaturan Sistem & Master Data - SIM-SARPRAS" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header -->
            <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/70 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                            Konfigurasi SIM-SARPRAS Enterprise
                        </span>
                        <span>•</span>
                        <span>Multi-Kampus Terintegrasi</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Pengaturan Sistem & Master Data</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Manajemen struktur gedung, ruang perkuliahan & laboratorium, klasifikasi BMN, dan integrasi server.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('profile.edit')"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs flex items-center gap-2"
                    >
                        <UserCheck class="w-4 h-4 text-emerald-600" />
                        <span>Profil Saya</span>
                    </Link>
                </div>
            </div>

            <!-- Master Stats Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="p-4 bg-white rounded-xl border border-slate-200/70 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400">Kampus</span>
                    <div class="text-xl font-black text-slate-900 mt-0.5">{{ stats.total_campuses }} Unit</div>
                </div>
                <div class="p-4 bg-white rounded-xl border border-slate-200/70 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400">Gedung</span>
                    <div class="text-xl font-black text-slate-900 mt-0.5">{{ stats.total_buildings }} Gedung</div>
                </div>
                <div class="p-4 bg-white rounded-xl border border-slate-200/70 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400">Ruangan & Lab</span>
                    <div class="text-xl font-black text-slate-900 mt-0.5">{{ stats.total_rooms }} Ruangan</div>
                </div>
                <div class="p-4 bg-white rounded-xl border border-slate-200/70 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400">Kategori BMN</span>
                    <div class="text-xl font-black text-slate-900 mt-0.5">{{ stats.total_categories }} Golongan</div>
                </div>
                <div class="p-4 bg-white rounded-xl border border-slate-200/70 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400">Vendor Sarpras</span>
                    <div class="text-xl font-black text-slate-900 mt-0.5">{{ stats.total_vendors }} Rekanan</div>
                </div>
                <div class="p-4 bg-white rounded-xl border border-slate-200/70 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400">Pengguna Aktif</span>
                    <div class="text-xl font-black text-slate-900 mt-0.5">{{ stats.total_users }} User</div>
                </div>
            </div>

            <!-- Tab Pills -->
            <div class="bg-white rounded-2xl border border-slate-200/70 p-2 shadow-sm flex items-center gap-2 overflow-x-auto">
                <button
                    @click="activeTab = 'rooms'"
                    :class="[
                        'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap',
                        activeTab === 'rooms'
                            ? 'bg-[#168a42] text-white shadow-xs'
                            : 'text-slate-600 hover:bg-slate-100'
                    ]"
                >
                    <Building2 class="w-4 h-4" />
                    <span>Master Ruang & Gedung</span>
                </button>
                <button
                    @click="activeTab = 'categories'"
                    :class="[
                        'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap',
                        activeTab === 'categories'
                            ? 'bg-[#168a42] text-white shadow-xs'
                            : 'text-slate-600 hover:bg-slate-100'
                    ]"
                >
                    <Layers class="w-4 h-4" />
                    <span>Golongan Kategori BMN</span>
                </button>
                <button
                    @click="activeTab = 'vendors'"
                    :class="[
                        'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap',
                        activeTab === 'vendors'
                            ? 'bg-[#168a42] text-white shadow-xs'
                            : 'text-slate-600 hover:bg-slate-100'
                    ]"
                >
                    <Store class="w-4 h-4" />
                    <span>Vendor & Rekanan</span>
                </button>
                <button
                    @click="activeTab = 'sync'"
                    :class="[
                        'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap',
                        activeTab === 'sync'
                            ? 'bg-[#168a42] text-white shadow-xs'
                            : 'text-slate-600 hover:bg-slate-100'
                    ]"
                >
                    <Wifi class="w-4 h-4" />
                    <span>Integrasi BMN Kemdikbudristek</span>
                </button>
            </div>

            <!-- Tab 1: Master Ruang & Gedung -->
            <div v-if="activeTab === 'rooms'" class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Daftar Ruang Kuliah & Laboratorium Kampus</h3>
                        <p class="text-[11px] text-slate-500">Master data ruangan untuk peminjaman, sensus aset, dan penempatan inventaris</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">Kode Ruang</th>
                                <th class="px-4 py-3.5">Nama Ruangan</th>
                                <th class="px-4 py-3.5">Gedung & Kampus</th>
                                <th class="px-4 py-3.5 text-center">Kapasitas</th>
                                <th class="px-4 py-3.5">PIC Ruangan / Laboran</th>
                                <th class="px-6 py-3.5 text-center">Total Aset</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="room in rooms" :key="room.id" class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 font-mono font-bold text-[#168a42]">{{ room.code }}</td>
                                <td class="px-4 py-4 font-bold text-slate-900">{{ room.name }}</td>
                                <td class="px-4 py-4 text-slate-600">
                                    {{ room.building?.name }} ({{ room.building?.campus?.name || 'Pusat' }})
                                </td>
                                <td class="px-4 py-4 text-center font-bold text-slate-700">{{ room.capacity || 40 }} Orang</td>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-slate-800">{{ room.pic?.name || 'Staf Sarpras' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ room.pic?.department || 'Biro Sarpras' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-50 text-[#168a42] border border-emerald-200">
                                        {{ room.assets_count }} item
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 2: Golongan Kategori BMN -->
            <div v-if="activeTab === 'categories'" class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Klasifikasi Kategori & Standar Masa Manfaat BMN</h3>
                    <p class="text-[11px] text-slate-500">Masa manfaat ekonomis digunakan untuk perhitungan penyusutan otomatis setiap semester</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">Kode Akun BMN</th>
                                <th class="px-4 py-3.5">Nama Golongan Kategori</th>
                                <th class="px-4 py-3.5 text-center">Masa Manfaat</th>
                                <th class="px-4 py-3.5">Metode Depresiasi</th>
                                <th class="px-6 py-3.5 text-center">Jumlah Aset Terdaftar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="cat in categories" :key="cat.id" class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 font-mono font-bold text-[#168a42]">{{ cat.code }}</td>
                                <td class="px-4 py-4 font-bold text-slate-900">{{ cat.name }}</td>
                                <td class="px-4 py-4 text-center font-bold text-slate-800">{{ cat.useful_life_years }} Tahun</td>
                                <td class="px-4 py-4 text-slate-600">Garis Lurus (Straight Line)</td>
                                <td class="px-6 py-4 text-center font-bold text-slate-800">{{ cat.assets_count }} unit</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 3: Vendor Rekanan -->
            <div v-if="activeTab === 'vendors'" class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Rekanan Pengadaan & Vendor Servis Resmi</h3>
                    <p class="text-[11px] text-slate-500">Daftar penyedia suku cadang, garansi perangkat, dan pemeliharaan gedung</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">Nama Perusahaan / Rekanan</th>
                                <th class="px-4 py-3.5">Kontak Person</th>
                                <th class="px-4 py-3.5">No. Telepon / WhatsApp</th>
                                <th class="px-4 py-3.5">Email</th>
                                <th class="px-6 py-3.5 text-center">Aset Pengadaan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="v in vendors" :key="v.id" class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 font-bold text-slate-900">{{ v.name }}</td>
                                <td class="px-4 py-4 text-slate-700">{{ v.contact_person || '-' }}</td>
                                <td class="px-4 py-4 text-slate-700 font-mono">{{ v.phone || '-' }}</td>
                                <td class="px-4 py-4 text-slate-700">{{ v.email || '-' }}</td>
                                <td class="px-6 py-4 text-center font-bold text-slate-800">{{ v.assets_count }} item</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 4: Integrasi BMN Kemdikbudristek -->
            <div v-if="activeTab === 'sync'" class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-[#168a42] flex items-center justify-center font-bold">
                                <Wifi class="w-6 h-6" />
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-black text-slate-900">BMN Synchronizer Online</h3>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                                        Connected
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">{{ syncStatus.gateway }}</p>
                            </div>
                        </div>

                        <button
                            @click="triggerManualSync"
                            :disabled="isSyncing"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#168a42] hover:bg-[#137a3b] text-white rounded-xl text-xs font-bold shadow-sm transition disabled:opacity-50"
                        >
                            <RefreshCw :class="['w-4 h-4', isSyncing ? 'animate-spin' : '']" />
                            <span>{{ isSyncing ? 'Menghubungkan...' : 'Sinkronkan Sekarang' }}</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 pt-6 text-xs">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 block text-[10px] font-extrabold uppercase">Sinkronisasi Terakhir</span>
                            <span class="text-sm font-bold text-slate-800 mt-1 block">{{ syncStatus.last_sync }}</span>
                            <span class="text-[11px] text-emerald-600 font-semibold mt-0.5 block">Status: {{ syncStatus.sync_health }}</span>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 block text-[10px] font-extrabold uppercase">Frekuensi Auto-Sync</span>
                            <span class="text-sm font-bold text-slate-800 mt-1 block">{{ syncStatus.scheduled_interval }}</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">Cron worker daemon aktif</span>
                        </div>

                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 block text-[10px] font-extrabold uppercase">API Gateway Endpoint</span>
                            <span class="text-xs font-mono font-bold text-slate-800 mt-1 block truncate">{{ syncStatus.endpoint }}</span>
                            <span class="text-[11px] text-slate-500 mt-0.5 block">Protokol HTTPS TLS 1.3</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
