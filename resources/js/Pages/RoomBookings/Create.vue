<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { 
    ChevronLeft, 
    DoorOpen, 
    Building2, 
    Users, 
    Calendar, 
    Clock, 
    CheckCircle2, 
    AlertTriangle, 
    ShieldCheck, 
    FileText,
    Info
} from 'lucide-vue-next';

const props = defineProps({
    rooms: Array,
    today: String,
    todaySchedule: Array,
});

const form = useForm({
    room_id: '',
    booking_date: props.today || new Date().toISOString().split('T')[0],
    start_time: '09:00',
    end_time: '12:00',
    attendees_count: 30,
    purpose: '',
});

const selectedRoom = computed(() => {
    if (!form.room_id) return null;
    return props.rooms.find(r => r.id === parseInt(form.room_id)) || null;
});

const isOverCapacity = computed(() => {
    if (!selectedRoom.value || !form.attendees_count) return false;
    return form.attendees_count > selectedRoom.value.capacity;
});

// Bookings that match selected room and selected date from todaySchedule
const existingBookingsForRoom = computed(() => {
    if (!selectedRoom.value || !props.todaySchedule) return [];
    return props.todaySchedule.filter(b => 
        b.room_id === selectedRoom.value.id && 
        b.booking_date === form.booking_date
    );
});

const submit = () => {
    form.post(route('room-bookings.store'));
};
</script>

<template>
    <Head title="Formulir Peminjaman Ruangan - SIM-SARPRAS" />

    <AuthenticatedLayout>
        <div class="space-y-6">
            <!-- Header Card -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <Link
                        :href="route('room-bookings.index')"
                        class="p-2.5 border border-slate-200 rounded-xl text-slate-600 hover:bg-slate-50 transition"
                    >
                        <ChevronLeft class="w-5 h-5" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-[#1e3a8a] bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100">
                                Permohonan Ruangan
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight mt-0.5">
                            Formulir Reservasi Ruangan & Laboratorium
                        </h1>
                        <p class="text-xs text-slate-500">
                            Pengajuan digital pemanfaatan ruang kelas, aula seminar, laboratorium riset, dan ruang rapat Universitas Al-Khairiyah.
                        </p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left 2 Cols: Form Fields -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Section 1: Pilih Ruangan -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 space-y-4">
                        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                            <span class="w-6 h-6 rounded-full bg-[#1e3a8a] text-white flex items-center justify-center text-xs font-bold">1</span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Pilih Ruangan atau Fasilitas Kampus</h2>
                                <p class="text-[11px] text-slate-400">Pilih ruangan yang sesuai dengan kapasitas dan jenis kegiatan</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Ruangan / Laboratorium *</label>
                            <select
                                v-model="form.room_id"
                                required
                                class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs font-medium focus:bg-white focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] focus:outline-none"
                                :class="form.errors.room_id ? 'border-rose-400' : 'border-slate-200'"
                            >
                                <option value="">-- Pilih Ruangan / Laboratorium --</option>
                                <option v-for="room in rooms" :key="room.id" :value="room.id">
                                    {{ room.name }} ({{ room.code }}) - Kapasitas: {{ room.capacity }} Orang • {{ room.building?.name }}
                                </option>
                            </select>
                            <span v-if="form.errors.room_id" class="text-xs text-rose-600 mt-1 block">{{ form.errors.room_id }}</span>
                        </div>

                        <!-- Selected Room Detail Box -->
                        <div v-if="selectedRoom" class="p-4 bg-blue-50/60 rounded-xl border border-blue-200/80 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Gedung / Lokasi</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">{{ selectedRoom.building?.name }} (Lantai {{ selectedRoom.floor_number }})</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Fakultas / Unit</span>
                                <span class="font-bold text-[#1e3a8a] mt-0.5 block">{{ selectedRoom.faculty?.name || 'Rektorat / Umum' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">Kapasitas Maksimal</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">{{ selectedRoom.capacity }} Kursi</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-slate-400 block">PIC Ruangan</span>
                                <span class="font-bold text-slate-800 mt-0.5 block">{{ selectedRoom.pic?.name || 'Biro Sarpras' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Jadwal & Waktu -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 space-y-4">
                        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                            <span class="w-6 h-6 rounded-full bg-[#1e3a8a] text-white flex items-center justify-center text-xs font-bold">2</span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Jadwal Tanggal & Waktu Penggunaan</h2>
                                <p class="text-[11px] text-slate-400">Tentukan tanggal dan rentang jam kegiatan secara presisi</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pemakaian *</label>
                                <input
                                    v-model="form.booking_date"
                                    type="date"
                                    :min="today"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs font-medium focus:bg-white focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] focus:outline-none"
                                    :class="form.errors.booking_date ? 'border-rose-400' : 'border-slate-200'"
                                />
                                <span v-if="form.errors.booking_date" class="text-xs text-rose-600 mt-1 block">{{ form.errors.booking_date }}</span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Jam Mulai (WIB) *</label>
                                <input
                                    v-model="form.start_time"
                                    type="time"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs font-medium focus:bg-white focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] focus:outline-none"
                                    :class="form.errors.start_time ? 'border-rose-400' : 'border-slate-200'"
                                />
                                <span v-if="form.errors.start_time" class="text-xs text-rose-600 mt-1 block">{{ form.errors.start_time }}</span>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Jam Selesai (WIB) *</label>
                                <input
                                    v-model="form.end_time"
                                    type="time"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs font-medium focus:bg-white focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] focus:outline-none"
                                    :class="form.errors.end_time ? 'border-rose-400' : 'border-slate-200'"
                                />
                                <span v-if="form.errors.end_time" class="text-xs text-rose-600 mt-1 block">{{ form.errors.end_time }}</span>
                            </div>
                        </div>

                        <!-- Existing Bookings for this room on this date Notice -->
                        <div v-if="existingBookingsForRoom.length" class="p-3.5 bg-amber-50 rounded-xl border border-amber-200 text-xs space-y-1">
                            <div class="flex items-center gap-1.5 font-bold text-amber-800">
                                <AlertTriangle class="w-4 h-4 shrink-0 text-amber-600" />
                                <span>Perhatian: Jadwal Ruangan Terisi Pada Tanggal Ini</span>
                            </div>
                            <p class="text-[11px] text-amber-900 leading-relaxed">
                                Ruangan ini telah memiliki permohonan pada jam berikut:
                            </p>
                            <ul class="list-disc list-inside text-[11px] text-amber-950 font-medium">
                                <li v-for="eb in existingBookingsForRoom" :key="eb.id">
                                    {{ eb.start_time?.substring(0, 5) }} - {{ eb.end_time?.substring(0, 5) }} WIB: {{ eb.purpose }} ({{ eb.user?.name }})
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Section 3: Peserta & Keperluan -->
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 space-y-4">
                        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                            <span class="w-6 h-6 rounded-full bg-[#1e3a8a] text-white flex items-center justify-center text-xs font-bold">3</span>
                            <div>
                                <h2 class="text-sm font-bold text-slate-900">Rincian Kegiatan & Estimasi Peserta</h2>
                                <p class="text-[11px] text-slate-400">Informasi peruntukan acara untuk verifikasi kesesuaian oleh Biro Sarpras</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Estimasi Jumlah Peserta (Orang) *</label>
                            <input
                                v-model.number="form.attendees_count"
                                type="number"
                                min="1"
                                required
                                class="w-full sm:w-48 px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs font-medium focus:bg-white focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] focus:outline-none"
                                :class="[
                                    form.errors.attendees_count ? 'border-rose-400' : 'border-slate-200',
                                    isOverCapacity ? 'border-amber-400 bg-amber-50/50' : ''
                                ]"
                            />
                            <span v-if="isOverCapacity" class="text-xs text-amber-600 font-semibold mt-1 block">
                                Peringatan: Jumlah peserta ({{ form.attendees_count }}) melebihi daya tampung normal ruangan ({{ selectedRoom?.capacity }} orang).
                            </span>
                            <span v-if="form.errors.attendees_count" class="text-xs text-rose-600 mt-1 block">{{ form.errors.attendees_count }}</span>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tujuan / Nama Kegiatan *</label>
                            <textarea
                                v-model="form.purpose"
                                required
                                rows="3"
                                placeholder="Contoh: Kuliah Pengganti Matakuliah Pemrograman Web / Rapat Pleno BEM / Seminar Nasional Informatika..."
                                class="w-full px-3.5 py-2.5 bg-slate-50 border rounded-xl text-xs focus:bg-white focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] focus:outline-none"
                                :class="form.errors.purpose ? 'border-rose-400' : 'border-slate-200'"
                            ></textarea>
                            <span v-if="form.errors.purpose" class="text-xs text-rose-600 mt-1 block">{{ form.errors.purpose }}</span>
                        </div>
                    </div>
                </div>

                <!-- Right 1 Col: Summary & Rules -->
                <div class="space-y-6">
                    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-sm p-6 sticky top-24 space-y-5">
                        <div class="flex items-center gap-2 pb-3 border-b border-slate-100">
                            <div class="w-7 h-7 rounded-lg bg-blue-50 text-[#1e3a8a] flex items-center justify-center">
                                <DoorOpen class="w-4 h-4" />
                            </div>
                            <h3 class="text-sm font-bold text-slate-900">Ringkasan Reservasi</h3>
                        </div>

                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <span class="text-slate-400">Ruangan Target:</span>
                                <span class="font-bold text-slate-800 text-right">{{ selectedRoom ? selectedRoom.name : 'Belum dipilih' }}</span>
                            </div>

                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <span class="text-slate-400">Gedung:</span>
                                <span class="font-bold text-slate-800">{{ selectedRoom?.building?.name || '-' }}</span>
                            </div>

                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <span class="text-slate-400">Tanggal:</span>
                                <span class="font-bold text-slate-800">{{ form.booking_date || '-' }}</span>
                            </div>

                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <span class="text-slate-400">Rentang Waktu:</span>
                                <span class="font-bold text-[#1e3a8a]">{{ form.start_time }} - {{ form.end_time }} WIB</span>
                            </div>

                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <span class="text-slate-400">Jumlah Peserta:</span>
                                <span class="font-bold text-slate-800">± {{ form.attendees_count }} Orang</span>
                            </div>
                        </div>

                        <!-- Guidelines Alert -->
                        <div class="p-3.5 bg-blue-50/70 rounded-xl border border-blue-200/70 text-xs text-blue-950 space-y-1.5">
                            <div class="font-bold flex items-center gap-1.5 text-[#1e3a8a]">
                                <ShieldCheck class="w-4 h-4" />
                                <span>Ketentuan Reservasi Ruang:</span>
                            </div>
                            <ul class="list-disc list-inside space-y-1 text-[11px] text-blue-900 leading-relaxed">
                                <li>Pengajuan minimal H-1 sebelum kegiatan.</li>
                                <li>Pemohon wajib mematikan AC, lampu, dan proyektor setelah selesai.</li>
                                <li>Dilarang merusak meja, kursi, dan fasilitas audio visual ruangan.</li>
                            </ul>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing || !form.room_id"
                            class="w-full py-3 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-md shadow-blue-900/25 transition disabled:opacity-50"
                        >
                            Ajukan Permohonan Ruangan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>
