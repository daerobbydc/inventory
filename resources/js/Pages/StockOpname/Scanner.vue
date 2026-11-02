<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import axios from 'axios';
import { Html5Qrcode } from 'html5-qrcode';
import { 
    QrCode, 
    Camera, 
    CameraOff, 
    Building2, 
    CheckCircle2, 
    AlertTriangle, 
    Check, 
    X, 
    ArrowRightLeft, 
    Sparkles,
    Search,
    ShieldCheck,
    Layers,
    Clock
} from 'lucide-vue-next';

const props = defineProps({
    session: Object,
    rooms: Array,
    recentItems: Array,
    stats: Object,
});

const selectedRoomId = ref(props.rooms[0]?.id || '');
const isScanning = ref(false);
const scanError = ref('');
const manualInputCode = ref('');
const foundAsset = ref(null);
const isLookupLoading = ref(false);
let html5QrCode = null;

// Form for verifying
const verifyForm = useForm({
    stock_opname_session_id: props.session.id,
    asset_id: '',
    scanned_room_id: selectedRoomId.value,
    condition: 'good',
    status: 'match',
    notes: '',
    relocate_to_here: false,
});

const startCamera = async () => {
    scanError.value = '';
    await nextTick();
    try {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode('qr-reader');
        }

        const config = { fps: 10, qrbox: { width: 240, height: 240 } };

        await html5QrCode.start(
            { facingMode: 'environment' },
            config,
            (decodedText) => {
                handleCodeDetected(decodedText);
            },
            () => {}
        );
        isScanning.value = true;
    } catch (err) {
        console.error('Camera error:', err);
        scanError.value = 'Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan di browser, atau gunakan input kode manual di bawah.';
        isScanning.value = false;
    }
};

const stopCamera = async () => {
    if (html5QrCode && isScanning.value) {
        try {
            await html5QrCode.stop();
            isScanning.value = false;
        } catch (err) {
            console.error('Stop camera error:', err);
        }
    }
};

const handleCodeDetected = async (code) => {
    if (isLookupLoading.value) return;
    isLookupLoading.value = true;
    try {
        const res = await axios.get(route('stock-opname.lookup'), {
            params: {
                code: code,
                room_id: selectedRoomId.value,
            },
        });

        foundAsset.value = res.data;
        verifyForm.asset_id = res.data.asset.id;
        verifyForm.scanned_room_id = selectedRoomId.value;
        verifyForm.condition = res.data.asset.condition === 'Rusak Berat' ? 'heavy_damage' : res.data.asset.condition === 'Rusak Ringan' ? 'light_damage' : 'good';
        verifyForm.status = res.data.verdict;
        verifyForm.relocate_to_here = !res.data.is_match;

        // Play audio confirmation beep
        playBeep();
    } catch (err) {
        alert(err.response?.data?.error || 'Aset tidak ditemukan dalam sistem BMN.');
    } finally {
        isLookupLoading.value = false;
    }
};

const playBeep = () => {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.frequency.value = 880;
        gain.gain.value = 0.1;
        osc.start();
        setTimeout(() => {
            osc.stop();
            ctx.close();
        }, 120);
    } catch (e) {
        // audio context not allowed without interaction
    }
};

const handleManualSubmit = () => {
    if (!manualInputCode.value) return;
    handleCodeDetected(manualInputCode.value);
    manualInputCode.value = '';
};

const submitVerification = () => {
    verifyForm.post(route('stock-opname.verify'), {
        onSuccess: () => {
            foundAsset.value = null;
            verifyForm.reset('notes', 'relocate_to_here');
        },
    });
};

onMounted(() => {
    // Optionally start camera or let user click
});

onUnmounted(() => {
    stopCamera();
});
</script>

<template>
    <Head title="Audit Fisik & Scanner QR BMN - SIM-SARPRAS" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Banner -->
            <div class="bg-white rounded-2xl p-6 sm:p-7 border border-slate-200/70 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-500 mb-1">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                            Sensus BMN Aktif
                        </span>
                        <span>•</span>
                        <span class="font-mono text-emerald-800 font-bold">{{ session.session_code }}</span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Audit Fisik & Scanner Kamera QR BMN</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        Pindai label stiker aset fisik secara langsung menggunakan kamera HP/Laptop untuk verifikasi kesesuaian lokasi ruangan.
                    </p>
                </div>

                <!-- Session Progress Mini Card -->
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl flex items-center gap-4 shrink-0 text-xs">
                    <div>
                        <span class="text-slate-400 block text-[10px] font-extrabold uppercase">Aset Terverifikasi</span>
                        <span class="text-base font-black text-slate-900">{{ stats.total_verified }} Unit</span>
                    </div>
                    <div class="h-8 w-px bg-slate-200"></div>
                    <div>
                        <span class="text-slate-400 block text-[10px] font-extrabold uppercase">Sesuai Ruang</span>
                        <span class="text-base font-black text-[#168a42]">{{ stats.match_count }}</span>
                    </div>
                    <div class="h-8 w-px bg-slate-200"></div>
                    <div>
                        <span class="text-slate-400 block text-[10px] font-extrabold uppercase">Tersasar</span>
                        <span class="text-base font-black text-amber-600">{{ stats.misplaced_count }}</span>
                    </div>
                </div>
            </div>

            <!-- Main Scanner 2 Columns -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left 7 Cols: Camera Viewport & Manual Input -->
                <div class="lg:col-span-7 space-y-4">
                    <div class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm space-y-4">
                        <!-- Step 1: Select Current Audited Room -->
                        <div>
                            <label class="block text-xs font-extrabold uppercase tracking-wider text-slate-700 mb-1.5">
                                1. Pilih Ruangan yang Sedang Diinspeksi
                            </label>
                            <div class="relative">
                                <Building2 class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                                <select
                                    v-model="selectedRoomId"
                                    class="w-full pl-9 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42]"
                                >
                                    <option v-for="room in rooms" :key="room.id" :value="room.id">
                                        {{ room.name }} ({{ room.building?.name }}) - Kode: {{ room.code }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Step 2: Camera Viewport Frame -->
                        <div class="space-y-3 pt-2">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-extrabold uppercase tracking-wider text-slate-700">
                                    2. Arahkan Kamera ke Stiker QR Aset
                                </label>
                                <button
                                    v-if="!isScanning"
                                    @click="startCamera"
                                    class="px-3.5 py-1.5 bg-[#168a42] hover:bg-[#137a3b] text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs"
                                >
                                    <Camera class="w-3.5 h-3.5" />
                                    <span>Buka Kamera</span>
                                </button>
                                <button
                                    v-else
                                    @click="stopCamera"
                                    class="px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 rounded-xl text-xs font-bold transition flex items-center gap-1.5"
                                >
                                    <CameraOff class="w-3.5 h-3.5" />
                                    <span>Tutup Kamera</span>
                                </button>
                            </div>

                            <!-- Camera Box -->
                            <div class="relative rounded-2xl overflow-hidden bg-slate-900 border-2 border-slate-800 min-h-[300px] flex items-center justify-center">
                                <div id="qr-reader" class="w-full h-full"></div>

                                <div v-if="!isScanning" class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 text-slate-400 bg-slate-900/90">
                                    <Camera class="w-12 h-12 text-slate-600 mb-2 stroke-[1.5]" />
                                    <p class="text-xs font-semibold text-slate-300">Kamera Sedang Tidak Aktif</p>
                                    <p class="text-[11px] text-slate-500 mt-1 max-w-xs">
                                        Klik tombol "Buka Kamera" di atas untuk mengaktifkan video scanner langsung, atau gunakan input manual di bawah.
                                    </p>
                                </div>
                            </div>

                            <p v-if="scanError" class="text-xs text-rose-600 bg-rose-50 p-3 rounded-xl border border-rose-200">
                                {{ scanError }}
                            </p>
                        </div>

                        <!-- Manual Code Fallback -->
                        <div class="pt-4 border-t border-slate-100">
                            <label class="block text-xs font-bold text-slate-600 mb-1.5">
                                Atau Ketik / Scan dengan Handheld Barcode USB:
                            </label>
                            <form @submit.prevent="handleManualSubmit" class="flex gap-2">
                                <div class="relative flex-1">
                                    <Search class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
                                    <input
                                        v-model="manualInputCode"
                                        type="text"
                                        placeholder="Ketik kode aset (misal: AST-2026-...) atau paste token QR..."
                                        class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42]"
                                    />
                                </div>
                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition shrink-0"
                                >
                                    Cek Kode
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right 5 Cols: Detected Asset Verification Sheet -->
                <div class="lg:col-span-5 space-y-4">
                    <!-- Found Asset Card -->
                    <div v-if="foundAsset" class="bg-white rounded-2xl border-2 border-emerald-500 p-6 shadow-md space-y-4 animate-in fade-in zoom-in duration-150">
                        <!-- Verdict Banner -->
                        <div 
                            :class="[
                                'p-3.5 rounded-xl border flex items-center justify-between text-xs font-bold',
                                foundAsset.is_match 
                                    ? 'bg-emerald-50 text-emerald-800 border-emerald-200' 
                                    : 'bg-amber-50 text-amber-800 border-amber-200'
                            ]"
                        >
                            <div class="flex items-center gap-2">
                                <CheckCircle2 v-if="foundAsset.is_match" class="w-5 h-5 text-emerald-600" />
                                <AlertTriangle v-else class="w-5 h-5 text-amber-600" />
                                <span>{{ foundAsset.is_match ? 'LOKASI SESUAI DI RUANGAN INI' : 'PERINGATAN: ASET TERSASAR' }}</span>
                            </div>
                            <span class="text-[10px] uppercase font-mono">{{ foundAsset.verdict }}</span>
                        </div>

                        <!-- Asset Details -->
                        <div>
                            <span class="font-mono text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-md">
                                {{ foundAsset.asset.asset_code }}
                            </span>
                            <h3 class="text-base font-extrabold text-slate-900 mt-1">{{ foundAsset.asset.name }}</h3>
                            <div class="text-xs text-slate-500 mt-1 flex items-center gap-2">
                                <span>Kategori: {{ foundAsset.asset.category || '-' }}</span>
                                <span>•</span>
                                <span>Status: {{ foundAsset.asset.status }}</span>
                            </div>
                        </div>

                        <!-- Location Comparison -->
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100 text-xs space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Lokasi Tercatat di Sistem:</span>
                                <span class="font-bold text-slate-800">{{ foundAsset.asset.room_name }} ({{ foundAsset.asset.building_name }})</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Ruangan Ditemukan (Saat Ini):</span>
                                <span class="font-bold text-emerald-700">
                                    {{ rooms.find(r => r.id === selectedRoomId)?.name }}
                                </span>
                            </div>
                        </div>

                        <!-- Form Verification Inputs -->
                        <form @submit.prevent="submitVerification" class="space-y-3 pt-2">
                            <!-- Condition Picker -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kondisi Fisik Terkini</label>
                                <select
                                    v-model="verifyForm.condition"
                                    class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42]"
                                >
                                    <option value="good">Kondisi Baik / Prima (Laik Pakai Penuh)</option>
                                    <option value="light_damage">Rusak Ringan (Perlu Servis Minor)</option>
                                    <option value="heavy_damage">Rusak Berat (Usul Pemutihan/Hapus)</option>
                                </select>
                            </div>

                            <!-- Relocation Checkbox (If misplaced) -->
                            <div v-if="!foundAsset.is_match" class="p-3 bg-amber-50 rounded-xl border border-amber-200 flex items-start gap-2.5">
                                <input
                                    id="relocate"
                                    type="checkbox"
                                    v-model="verifyForm.relocate_to_here"
                                    class="mt-0.5 rounded text-[#168a42] focus:ring-[#168a42]"
                                />
                                <label for="relocate" class="text-xs text-amber-900 leading-snug cursor-pointer font-medium">
                                    <strong>Pindahkan lokasi aset ke ruangan ini</strong> (Sistem akan otomatis mencatat Berita Acara Mutasi Ruangan).
                                </label>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan Auditor</label>
                                <input
                                    v-model="verifyForm.notes"
                                    type="text"
                                    placeholder="Misal: stiker fisik sedikit pudar, posisi di meja lab 04..."
                                    class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42]"
                                />
                            </div>

                            <button
                                type="submit"
                                :disabled="verifyForm.processing"
                                class="w-full py-2.5 bg-[#168a42] hover:bg-[#137a3b] text-white rounded-xl text-xs font-extrabold shadow-sm shadow-emerald-700/20 transition flex items-center justify-center gap-1.5 disabled:opacity-50"
                            >
                                <Check class="w-4 h-4 stroke-[2.5]" />
                                <span>Simpan Verifikasi Aset Ini</span>
                            </button>
                        </form>
                    </div>

                    <!-- Placeholder when no asset scanned -->
                    <div v-else class="bg-white rounded-2xl border border-slate-200/70 p-8 shadow-sm text-center text-slate-400">
                        <QrCode class="w-12 h-12 mx-auto text-slate-300 mb-3 stroke-[1.5]" />
                        <h4 class="text-sm font-bold text-slate-700">Menunggu Pemindaian Stiker QR</h4>
                        <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">
                            Arahkan kamera ke stiker barcode/QR aset di ruangan untuk memunculkan lembar verifikasi instan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Recent Verifications Audit Log Table -->
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Aset yang Telah Terverifikasi dalam Sesi Audit Ini</h3>
                        <p class="text-[11px] text-slate-500">Rekam jejak real-time pemindaian auditor fisik kampus</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3">Waktu Scan</th>
                                <th class="px-4 py-3">Kode BMN</th>
                                <th class="px-4 py-3">Nama Aset</th>
                                <th class="px-4 py-3">Ruangan Seharusnya</th>
                                <th class="px-4 py-3">Ruangan Ditemukan</th>
                                <th class="px-4 py-3">Status Verifikasi</th>
                                <th class="px-4 py-3">Kondisi Fisik</th>
                                <th class="px-6 py-3">Auditor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="item in recentItems" :key="item.id" class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-3.5 whitespace-nowrap text-slate-500 font-medium">
                                    {{ new Date(item.scanned_at || item.updated_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) }} WIB
                                </td>
                                <td class="px-4 py-3.5 font-mono font-bold text-emerald-800">
                                    {{ item.asset?.asset_code }}
                                </td>
                                <td class="px-4 py-3.5 font-bold text-slate-900">
                                    {{ item.asset?.name }}
                                </td>
                                <td class="px-4 py-3.5 text-slate-600">
                                    {{ item.expected_room?.name }}
                                </td>
                                <td class="px-4 py-3.5 font-bold text-slate-800">
                                    {{ item.scanned_room?.name }}
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap">
                                    <span 
                                        :class="[
                                            'px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border',
                                            item.status?.value === 'match' || item.status === 'match'
                                                ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                                                : 'bg-amber-50 text-amber-800 border-amber-200'
                                        ]"
                                    >
                                        {{ item.status?.label || (item.status === 'match' ? 'Sesuai' : 'Tersasar') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 whitespace-nowrap text-slate-700 font-medium">
                                    {{ item.condition?.label || item.condition }}
                                </td>
                                <td class="px-6 py-3.5 text-slate-700 font-medium">
                                    {{ item.scanned_by?.name || 'Auditor Sarpras' }}
                                </td>
                            </tr>
                            <tr v-if="recentItems.length === 0">
                                <td colspan="8" class="px-6 py-10 text-center text-slate-400">
                                    Belum ada aset yang dipindai dalam sesi ini.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
