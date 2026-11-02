<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
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
    Store,
    Phone,
    Plus,
    X,
    UserX,
    Send,
    MessageSquare,
    Lock,
    GraduationCap,
    Pencil,
    Trash2,
    DoorOpen,
    MapPin
} from 'lucide-vue-next';

const props = defineProps({
    stats: Object,
    faculties: Array,
    campuses: Array,
    buildings: Array,
    rooms: Array,
    categories: Array,
    vendors: Array,
    users: Array,
    roles: Array,
    syncStatus: Object,
});

const activeTab = ref('faculties');
const isSyncing = ref(false);

const triggerManualSync = () => {
    isSyncing.value = true;
    setTimeout(() => {
        isSyncing.value = false;
        alert('Sinkronisasi data inventaris aset yayasan berhasil diperbarui.');
    }, 1200);
};

// Faculty Modal State
const showFacultyModal = ref(false);
const editingFaculty = ref(null);
const facultyForm = useForm({
    code: '',
    name: '',
    dean_name: '',
    phone: '',
    email: '',
    description: '',
});

const openCreateFaculty = () => {
    editingFaculty.value = null;
    facultyForm.reset();
    showFacultyModal.value = true;
};

const openEditFaculty = (f) => {
    editingFaculty.value = f;
    facultyForm.code = f.code;
    facultyForm.name = f.name;
    facultyForm.dean_name = f.dean_name || '';
    facultyForm.phone = f.phone || '';
    facultyForm.email = f.email || '';
    facultyForm.description = f.description || '';
    showFacultyModal.value = true;
};

const submitFaculty = () => {
    if (editingFaculty.value) {
        facultyForm.put(route('faculties.update', editingFaculty.value.id), {
            onSuccess: () => {
                showFacultyModal.value = false;
                editingFaculty.value = null;
            },
        });
    } else {
        facultyForm.post(route('faculties.store'), {
            onSuccess: () => {
                showFacultyModal.value = false;
                facultyForm.reset();
            },
        });
    }
};

const deleteFaculty = (f) => {
    if (confirm(`Apakah Anda yakin ingin menghapus fakultas ${f.name} (${f.code})?`)) {
        facultyForm.delete(route('faculties.destroy', f.id));
    }
};

// Sub-Tab Room & Building
const roomSubTab = ref('rooms'); // 'rooms' | 'buildings'

// Building Modal State
const showBuildingModal = ref(false);
const editingBuilding = ref(null);
const buildingForm = useForm({
    campus_id: props.campuses?.[0]?.id || '',
    code: '',
    name: '',
    total_floors: 1,
    description: '',
});

const openCreateBuilding = () => {
    editingBuilding.value = null;
    buildingForm.reset();
    buildingForm.campus_id = props.campuses?.[0]?.id || '';
    buildingForm.total_floors = 1;
    showBuildingModal.value = true;
};

const openEditBuilding = (b) => {
    editingBuilding.value = b;
    buildingForm.campus_id = b.campus_id;
    buildingForm.code = b.code;
    buildingForm.name = b.name;
    buildingForm.total_floors = b.total_floors || 1;
    buildingForm.description = b.description || '';
    showBuildingModal.value = true;
};

const submitBuilding = () => {
    if (editingBuilding.value) {
        buildingForm.put(route('buildings.update', editingBuilding.value.id), {
            onSuccess: () => {
                showBuildingModal.value = false;
                editingBuilding.value = null;
            },
        });
    } else {
        buildingForm.post(route('buildings.store'), {
            onSuccess: () => {
                showBuildingModal.value = false;
                buildingForm.reset();
            },
        });
    }
};

const deleteBuilding = (b) => {
    if (confirm(`Apakah Anda yakin ingin menghapus gedung ${b.name} (${b.code})?`)) {
        buildingForm.delete(route('buildings.destroy', b.id));
    }
};

// Room Modal State
const showRoomModal = ref(false);
const editingRoom = ref(null);
const roomForm = useForm({
    building_id: props.buildings?.[0]?.id || '',
    faculty_id: '',
    code: '',
    name: '',
    floor_number: 1,
    type: 'classroom',
    pic_user_id: '',
    capacity: 40,
    description: '',
});

const openCreateRoom = () => {
    editingRoom.value = null;
    roomForm.reset();
    roomForm.building_id = props.buildings?.[0]?.id || '';
    roomForm.faculty_id = '';
    roomForm.pic_user_id = '';
    roomForm.type = 'classroom';
    roomForm.floor_number = 1;
    roomForm.capacity = 40;
    showRoomModal.value = true;
};

const openEditRoom = (r) => {
    editingRoom.value = r;
    roomForm.building_id = r.building_id;
    roomForm.faculty_id = r.faculty_id || '';
    roomForm.code = r.code;
    roomForm.name = r.name;
    roomForm.floor_number = r.floor_number || 1;
    roomForm.type = r.type || 'classroom';
    roomForm.pic_user_id = r.pic_user_id || '';
    roomForm.capacity = r.capacity || 40;
    roomForm.description = r.description || '';
    showRoomModal.value = true;
};

const submitRoom = () => {
    if (editingRoom.value) {
        roomForm.put(route('rooms.update', editingRoom.value.id), {
            onSuccess: () => {
                showRoomModal.value = false;
                editingRoom.value = null;
            },
        });
    } else {
        roomForm.post(route('rooms.store'), {
            onSuccess: () => {
                showRoomModal.value = false;
                roomForm.reset();
            },
        });
    }
};

const deleteRoom = (r) => {
    if (confirm(`Apakah Anda yakin ingin menghapus ruangan ${r.name} (${r.code})?`)) {
        roomForm.delete(route('rooms.destroy', r.id));
    }
};

const getRoomTypeLabel = (type) => {
    switch (type) {
        case 'classroom':
            return { label: 'Ruang Kelas', class: 'bg-blue-50 text-blue-700 border-blue-200' };
        case 'laboratory':
            return { label: 'Laboratorium', class: 'bg-emerald-50 text-emerald-700 border-emerald-200' };
        case 'office':
            return { label: 'Ruang Kantor/Dosen', class: 'bg-indigo-50 text-indigo-700 border-indigo-200' };
        case 'hall':
            return { label: 'Aula / Auditorium', class: 'bg-purple-50 text-purple-700 border-purple-200' };
        case 'storage':
            return { label: 'Gudang Sarpras', class: 'bg-amber-50 text-amber-700 border-amber-200' };
        default:
            return { label: 'Lainnya', class: 'bg-slate-50 text-slate-700 border-slate-200' };
    }
};

// User Modal
const showUserModal = ref(false);
const editingUser = ref(null);
const userForm = useForm({
    name: '',
    email: '',
    password: '',
    nip_nim: '',
    phone: '',
    role: 'dosen',
    department: '',
    faculty_id: '',
});

const openCreateUser = () => {
    editingUser.value = null;
    userForm.reset();
    userForm.password = 'password';
    userForm.faculty_id = '';
    showUserModal.value = true;
};

const openEditUser = (u) => {
    editingUser.value = u;
    userForm.name = u.name;
    userForm.email = u.email;
    userForm.password = '';
    userForm.nip_nim = u.nip_nim || '';
    userForm.phone = u.phone || '';
    userForm.role = u.role?.value || u.role;
    userForm.department = u.department || '';
    userForm.faculty_id = u.faculty_id || '';
    showUserModal.value = true;
};

const submitUser = () => {
    if (editingUser.value) {
        userForm.put(route('users.update', editingUser.value.id), {
            onSuccess: () => {
                showUserModal.value = false;
                editingUser.value = null;
            },
        });
    } else {
        userForm.post(route('users.store'), {
            onSuccess: () => {
                showUserModal.value = false;
                userForm.reset();
            },
        });
    }
};

const toggleUserStatus = (u) => {
    if (confirm(`Apakah Anda yakin ingin mengubah status aktif pengguna ${u.name}?`)) {
        userForm.post(route('users.toggle-status', u.id));
    }
};

// WhatsApp Gateway Tester
const waForm = useForm({
    phone: '081234567895',
    message: 'Halo, ini pengingat dari Biro Sarpras Universitas Al-Khairiyah bahwa peminjaman proyektor Anda jatuh tempo besok pukul 10:00 WIB. Mohon disiapkan untuk proses pengembalian.',
});

const sendTestWhatsApp = () => {
    waForm.post(route('users.test-whatsapp'));
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
                        Manajemen struktur gedung, ruang perkuliahan, pengguna & hak akses, klasifikasi aset yayasan, dan gateway integrasi.
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        :href="route('profile.edit')"
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs flex items-center gap-2"
                    >
                        <UserCheck class="w-4 h-4 text-[#1e3a8a]" />
                        <span>Profil Saya</span>
                    </Link>
                </div>
            </div>

            <!-- Master Stats Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
                <div class="p-4 bg-white rounded-xl border border-slate-200/70 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-[#1e3a8a]">Fakultas / Unit</span>
                    <div class="text-xl font-black text-slate-900 mt-0.5">{{ stats.total_faculties || faculties?.length || 0 }} Unit</div>
                </div>
                <div class="p-4 bg-white rounded-xl border border-slate-200/70 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400">Kampus</span>
                    <div class="text-xl font-black text-slate-900 mt-0.5">{{ stats.total_campuses }} Kampus</div>
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
                    <span class="text-[10px] font-extrabold uppercase text-slate-400">Kategori Aset</span>
                    <div class="text-xl font-black text-slate-900 mt-0.5">{{ stats.total_categories }} Golongan</div>
                </div>
                <div class="p-4 bg-white rounded-xl border border-slate-200/70 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400">Vendor Sarpras</span>
                    <div class="text-xl font-black text-slate-900 mt-0.5">{{ stats.total_vendors }} Rekanan</div>
                </div>
                <div class="p-4 bg-white rounded-xl border border-slate-200/70 shadow-xs">
                    <span class="text-[10px] font-extrabold uppercase text-slate-400">Pengguna Terdaftar</span>
                    <div class="text-xl font-black text-slate-900 mt-0.5">{{ stats.total_users }} User</div>
                </div>
            </div>

            <!-- Tab Pills -->
            <div class="bg-white rounded-2xl border border-slate-200/70 p-2 shadow-sm flex items-center gap-2 overflow-x-auto">
                <button
                    @click="activeTab = 'faculties'"
                    :class="[
                        'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap',
                        activeTab === 'faculties' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
                    ]"
                >
                    <GraduationCap class="w-4 h-4" />
                    <span>Fakultas & Unit ({{ faculties?.length || 0 }})</span>
                </button>
                <button
                    @click="activeTab = 'rooms'"
                    :class="[
                        'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap',
                        activeTab === 'rooms' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
                    ]"
                >
                    <Building2 class="w-4 h-4" />
                    <span>Master Ruang & Gedung</span>
                </button>
                <button
                    @click="activeTab = 'users'"
                    :class="[
                        'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap',
                        activeTab === 'users' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
                    ]"
                >
                    <Users class="w-4 h-4" />
                    <span>Pengguna & Hak Akses ({{ users?.length || 0 }})</span>
                </button>
                <button
                    @click="activeTab = 'categories'"
                    :class="[
                        'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap',
                        activeTab === 'categories' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
                    ]"
                >
                    <Layers class="w-4 h-4" />
                    <span>Golongan Kategori Aset</span>
                </button>
                <button
                    @click="activeTab = 'vendors'"
                    :class="[
                        'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap',
                        activeTab === 'vendors' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
                    ]"
                >
                    <Store class="w-4 h-4" />
                    <span>Vendor & Rekanan</span>
                </button>
                <button
                    @click="activeTab = 'whatsapp'"
                    :class="[
                        'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap',
                        activeTab === 'whatsapp' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
                    ]"
                >
                    <Phone class="w-4 h-4" />
                    <span>WhatsApp Gateway Notifikasi</span>
                </button>
                <button
                    @click="activeTab = 'sync'"
                    :class="[
                        'px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap',
                        activeTab === 'sync' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
                    ]"
                >
                    <Wifi class="w-4 h-4" />
                    <span>Sinkronisasi Data & Gateway API</span>
                </button>
            </div>

            <!-- Tab: Manajemen Fakultas & Unit Kerja -->
            <div v-if="activeTab === 'faculties'" class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Struktur Fakultas & Unit Kerja Universitas Al-Khairiyah</h3>
                        <p class="text-[11px] text-slate-500">Kelola master data fakultas, kepemimpinan dekan, serta alokasi ruangan dan pengguna sarpras</p>
                    </div>
                    <button
                        @click="openCreateFaculty"
                        class="px-3.5 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Tambah Fakultas Baru</span>
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">Kode</th>
                                <th class="px-4 py-3.5">Nama Fakultas / Unit Kerja</th>
                                <th class="px-4 py-3.5">Dekan / Pimpinan</th>
                                <th class="px-4 py-3.5">Kontak & Email</th>
                                <th class="px-4 py-3.5 text-center">Ruangan</th>
                                <th class="px-4 py-3.5 text-center">Civitas</th>
                                <th class="px-6 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="faculty in faculties" :key="faculty.id" class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 font-mono font-bold text-[#1e3a8a]">{{ faculty.code }}</td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-slate-900">{{ faculty.name }}</div>
                                    <div class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">{{ faculty.description || '-' }}</div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-slate-800">{{ faculty.dean_name || '-' }}</div>
                                </td>
                                <td class="px-4 py-4 text-slate-600">
                                    <div>{{ faculty.phone || '-' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ faculty.email || '-' }}</div>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-[#1e3a8a] border border-blue-200">
                                        {{ faculty.rooms_count || 0 }} Ruang
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                        {{ faculty.users_count || 0 }} User
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button
                                            @click="openEditFaculty(faculty)"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-[#1e3a8a] hover:bg-blue-50 transition"
                                            title="Ubah Data"
                                        >
                                            <Pencil class="w-3.5 h-3.5" />
                                        </button>
                                        <button
                                            @click="deleteFaculty(faculty)"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition"
                                            title="Hapus"
                                        >
                                            <Trash2 class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!faculties?.length">
                                <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                                    Belum ada data fakultas. Klik tombol "+ Tambah Fakultas Baru".
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 1: Master Ruang & Gedung -->
            <div v-if="activeTab === 'rooms'" class="space-y-4">
                <!-- Sub-Tab Switcher: Ruangan vs Gedung -->
                <div class="bg-white rounded-2xl border border-slate-200/70 p-2 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <button
                            @click="roomSubTab = 'rooms'"
                            :class="[
                                'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2',
                                roomSubTab === 'rooms' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
                            ]"
                        >
                            <DoorOpen class="w-4 h-4" />
                            <span>Ruang Kuliah & Lab ({{ rooms?.length || 0 }})</span>
                        </button>
                        <button
                            @click="roomSubTab = 'buildings'"
                            :class="[
                                'px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2',
                                roomSubTab === 'buildings' ? 'bg-[#1e3a8a] text-white shadow-xs' : 'text-slate-600 hover:bg-slate-100'
                            ]"
                        >
                            <Building2 class="w-4 h-4" />
                            <span>Gedung Kampus ({{ buildings?.length || 0 }})</span>
                        </button>
                    </div>

                    <!-- Action Button Depending on Sub-Tab -->
                    <div>
                        <button
                            v-if="roomSubTab === 'rooms'"
                            @click="openCreateRoom"
                            class="px-3.5 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs"
                        >
                            <Plus class="w-4 h-4" />
                            <span>Tambah Ruangan Baru</span>
                        </button>
                        <button
                            v-else
                            @click="openCreateBuilding"
                            class="px-3.5 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs"
                        >
                            <Plus class="w-4 h-4" />
                            <span>Tambah Gedung Baru</span>
                        </button>
                    </div>
                </div>

                <!-- 1. Table Ruangan -->
                <div v-if="roomSubTab === 'rooms'" class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900">Daftar Ruang Kuliah, Laboratorium, & Fasilitas Kampus</h3>
                        <p class="text-[11px] text-slate-500">Kelola nomor ruangan, penetapan PIC/laboran, alokasi fakultas, dan kapasitas</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-3.5">Kode</th>
                                    <th class="px-4 py-3.5">Nama Ruangan</th>
                                    <th class="px-4 py-3.5">Tipe Ruang</th>
                                    <th class="px-4 py-3.5">Fakultas / Unit</th>
                                    <th class="px-4 py-3.5">Gedung & Lantai</th>
                                    <th class="px-4 py-3.5 text-center">Kapasitas</th>
                                    <th class="px-4 py-3.5">PIC / Laboran</th>
                                    <th class="px-4 py-3.5 text-center">Aset</th>
                                    <th class="px-6 py-3.5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="room in rooms" :key="room.id" class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 font-mono font-bold text-[#1e3a8a]">{{ room.code }}</td>
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-slate-900">{{ room.name }}</div>
                                        <div class="text-[11px] text-slate-400 line-clamp-1 mt-0.5">{{ room.description || '-' }}</div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span :class="['px-2 py-0.5 rounded-full text-[10px] font-bold border', getRoomTypeLabel(room.type).class]">
                                            {{ getRoomTypeLabel(room.type).label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span v-if="room.faculty" class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-[#1e3a8a] border border-blue-200">
                                            {{ room.faculty.name }}
                                        </span>
                                        <span v-else class="text-slate-400 text-[11px]">Rektorat / Umum</span>
                                    </td>
                                    <td class="px-4 py-4 text-slate-600">
                                        <div class="font-semibold text-slate-800">{{ room.building?.name || 'Gedung -' }}</div>
                                        <div class="text-[10px] text-slate-400">Lantai {{ room.floor_number || 1 }} ({{ room.building?.campus?.name || 'Pusat' }})</div>
                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-slate-700">
                                        {{ room.capacity ? room.capacity + ' Orang' : '-' }}
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-slate-800">{{ room.pic?.name || 'Belum Ditugaskan' }}</div>
                                        <div class="text-[10px] text-slate-400">{{ room.pic?.department || 'Biro Sarpras' }}</div>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-emerald-50 text-[#1e3a8a] border border-emerald-200">
                                            {{ room.assets_count || 0 }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button
                                                @click="openEditRoom(room)"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-[#1e3a8a] hover:bg-blue-50 transition"
                                                title="Ubah Ruangan"
                                            >
                                                <Pencil class="w-3.5 h-3.5" />
                                            </button>
                                            <button
                                                @click="deleteRoom(room)"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition"
                                                title="Hapus Ruangan"
                                            >
                                                <Trash2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!rooms?.length">
                                    <td colspan="9" class="px-6 py-8 text-center text-slate-400">
                                        Belum ada data ruangan terdaftar. Klik "+ Tambah Ruangan Baru".
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 2. Table Gedung -->
                <div v-else class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h3 class="text-sm font-bold text-slate-900">Daftar Gedung Kampus Universitas Al-Khairiyah</h3>
                        <p class="text-[11px] text-slate-500">Kelola master data gedung perkuliahan, rektorat, dan laboratorium</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                                <tr>
                                    <th class="px-6 py-3.5">Kode Gedung</th>
                                    <th class="px-4 py-3.5">Nama Gedung</th>
                                    <th class="px-4 py-3.5">Kampus</th>
                                    <th class="px-4 py-3.5 text-center">Jumlah Lantai</th>
                                    <th class="px-4 py-3.5 text-center">Total Ruangan</th>
                                    <th class="px-4 py-3.5">Keterangan</th>
                                    <th class="px-6 py-3.5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr v-for="b in buildings" :key="b.id" class="hover:bg-slate-50/70 transition">
                                    <td class="px-6 py-4 font-mono font-bold text-[#1e3a8a]">{{ b.code }}</td>
                                    <td class="px-4 py-4 font-bold text-slate-900">{{ b.name }}</td>
                                    <td class="px-4 py-4 text-slate-600">
                                        {{ b.campus?.name || 'Kampus Utama' }}
                                    </td>
                                    <td class="px-4 py-4 text-center font-bold text-slate-700">
                                        {{ b.total_floors || 1 }} Lantai
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-50 text-[#1e3a8a] border border-blue-200">
                                            {{ b.rooms_count || b.rooms?.length || 0 }} Ruang
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-slate-500 text-[11px]">
                                        {{ b.description || '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button
                                                @click="openEditBuilding(b)"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-[#1e3a8a] hover:bg-blue-50 transition"
                                                title="Ubah Gedung"
                                            >
                                                <Pencil class="w-3.5 h-3.5" />
                                            </button>
                                            <button
                                                @click="deleteBuilding(b)"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition"
                                                title="Hapus Gedung"
                                            >
                                                <Trash2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <tr v-if="!buildings?.length">
                                    <td colspan="7" class="px-6 py-8 text-center text-slate-400">
                                        Belum ada data gedung. Klik "+ Tambah Gedung Baru".
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Tab 2: User Management -->
            <div v-if="activeTab === 'users'" class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Manajemen Pengguna & Hak Akses (RBAC)</h3>
                        <p class="text-[11px] text-slate-500">Kelola akun staf sarpras, laboran, teknisi on-duty, dosen, dan mahasiswa</p>
                    </div>
                    <button
                        @click="openCreateUser"
                        class="px-3.5 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-xs"
                    >
                        <Plus class="w-4 h-4" />
                        <span>Tambah Pengguna</span>
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">Nama & Email</th>
                                <th class="px-4 py-3.5">NIP / NIM</th>
                                <th class="px-4 py-3.5">Peran (Role)</th>
                                <th class="px-4 py-3.5">Unit Kerja / Fakultas</th>
                                <th class="px-4 py-3.5">No. WhatsApp</th>
                                <th class="px-4 py-3.5 text-center">Status</th>
                                <th class="px-6 py-3.5 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="u in users" :key="u.id" class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ u.name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono">{{ u.email }}</div>
                                </td>
                                <td class="px-4 py-4 font-mono font-semibold text-slate-700">
                                    {{ u.nip_nim || '-' }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        {{ roles.find(r => r.value === (u.role?.value || u.role))?.label || u.role }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-slate-600">
                                    {{ u.department || 'Civitas Akademika' }}
                                </td>
                                <td class="px-4 py-4 font-mono text-slate-600">
                                    {{ u.phone || '-' }}
                                </td>
                                <td class="px-4 py-4 text-center">
                                    <span 
                                        :class="[
                                            'px-2 py-0.5 rounded-full text-[9px] font-black',
                                            u.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-500'
                                        ]"
                                    >
                                        {{ u.is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <button
                                            @click="openEditUser(u)"
                                            class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-[11px] font-bold transition"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            @click="toggleUserStatus(u)"
                                            class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-[11px] font-bold transition border border-rose-200"
                                        >
                                            {{ u.is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 3: Golongan Kategori Aset -->
            <div v-if="activeTab === 'categories'" class="bg-white rounded-2xl border border-slate-200/70 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-sm font-bold text-slate-900">Klasifikasi Kategori & Standar Masa Manfaat Aset</h3>
                    <p class="text-[11px] text-slate-500">Masa manfaat ekonomis digunakan untuk perhitungan penyusutan otomatis setiap semester</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-50/80 text-[10px] font-extrabold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                            <tr>
                                <th class="px-6 py-3.5">Kode Akun Aset</th>
                                <th class="px-4 py-3.5">Nama Golongan Kategori</th>
                                <th class="px-4 py-3.5 text-center">Masa Manfaat</th>
                                <th class="px-4 py-3.5">Metode Depresiasi</th>
                                <th class="px-6 py-3.5 text-center">Jumlah Aset Terdaftar</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="cat in categories" :key="cat.id" class="hover:bg-slate-50/70 transition">
                                <td class="px-6 py-4 font-mono font-bold text-[#1e3a8a]">{{ cat.code }}</td>
                                <td class="px-4 py-4 font-bold text-slate-900">{{ cat.name }}</td>
                                <td class="px-4 py-4 text-center font-bold text-slate-800">{{ cat.useful_life_years }} Tahun</td>
                                <td class="px-4 py-4 text-slate-600">Garis Lurus (Straight Line)</td>
                                <td class="px-6 py-4 text-center font-bold text-slate-800">{{ cat.assets_count }} unit</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 4: Vendor Rekanan -->
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

            <!-- Tab 5: WhatsApp Gateway Tester -->
            <div v-if="activeTab === 'whatsapp'" class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm">
                    <div class="flex items-center gap-3 pb-6 border-b border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-[#1e3a8a] flex items-center justify-center font-bold">
                            <Phone class="w-6 h-6" />
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-black text-slate-900">WhatsApp Notification Gateway Simulator</h3>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                    Simulasi Siaga
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Layanan pengiriman notifikasi pengingat otomatis H-1 peminjaman dan notifikasi dispatch tiket kerusakan.
                            </p>
                        </div>
                    </div>

                    <form @submit.prevent="sendTestWhatsApp" class="pt-6 max-w-xl space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp Tujuan</label>
                            <input
                                v-model="waForm.phone"
                                type="text"
                                placeholder="08xxxxxxxxxx"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            />
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Isi Pesan Notifikasi</label>
                            <textarea
                                v-model="waForm.message"
                                rows="3"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            ></textarea>
                        </div>

                        <button
                            type="submit"
                            :disabled="waForm.processing"
                            class="px-5 py-2.5 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm transition flex items-center gap-2 disabled:opacity-50"
                        >
                            <Send class="w-4 h-4" />
                            <span>Kirim Simulasi Pesan WhatsApp</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tab 6: Sinkronisasi Data & Gateway API -->
            <div v-if="activeTab === 'sync'" class="space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/70 p-6 shadow-sm">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-100">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-[#1e3a8a] flex items-center justify-center font-bold">
                                <Wifi class="w-6 h-6" />
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h3 class="text-base font-black text-slate-900">Sistem Sinkronisasi Inventaris Terpadu</h3>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-900">
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                        Connected
                                    </span>
                                </div>
                                <p class="text-xs text-slate-500 mt-0.5">{{ syncStatus.gateway }}</p>
                            </div>
                        </div>

                        <button
                            @click="triggerManualSync"
                            :disabled="isSyncing"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm transition disabled:opacity-50"
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

        <!-- Modal: Create / Edit User -->
        <div v-if="showUserModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ editingUser ? 'Edit Data Pengguna' : 'Tambah Pengguna Baru' }}
                    </h3>
                    <button @click="showUserModal = false" class="text-slate-400 hover:text-slate-600">
                        <X class="w-4 h-4" />
                    </button>
                </div>
                <form @submit.prevent="submitUser" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap</label>
                            <input
                                v-model="userForm.name"
                                type="text"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Email Kampus</label>
                            <input
                                v-model="userForm.email"
                                type="email"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">NIP / NIM</label>
                            <input
                                v-model="userForm.nip_nim"
                                type="text"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp</label>
                            <input
                                v-model="userForm.phone"
                                type="text"
                                placeholder="08xxxxxxxxxx"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Peran (Role)</label>
                            <select
                                v-model="userForm.role"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            >
                                <option v-for="r in roles" :key="r.value" :value="r.value">
                                    {{ r.label }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Fakultas / Unit Kerja</label>
                            <select
                                v-model="userForm.faculty_id"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            >
                                <option value="">-- Pilih Fakultas (Opsional) --</option>
                                <option v-for="f in faculties" :key="f.id" :value="f.id">
                                    {{ f.name }} ({{ f.code }})
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Departemen / Keterangan Spesifik</label>
                        <input
                            v-model="userForm.department"
                            type="text"
                            placeholder="Contoh: Program Studi Informatika / Biro Sarpras"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                        />
                    </div>

                    <div v-if="!editingUser">
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kata Sandi Awal</label>
                        <input
                            v-model="userForm.password"
                            type="password"
                            placeholder="password"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            required
                        />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button
                            type="button"
                            @click="showUserModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="userForm.processing"
                            class="px-5 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm transition disabled:opacity-50"
                        >
                            {{ editingUser ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Create / Edit Faculty -->
        <div v-if="showFacultyModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ editingFaculty ? 'Ubah Data Fakultas' : 'Tambah Fakultas Baru' }}
                    </h3>
                    <button @click="showFacultyModal = false" class="text-slate-400 hover:text-slate-600">
                        <X class="w-4 h-4" />
                    </button>
                </div>
                <form @submit.prevent="submitFaculty" class="p-6 space-y-4">
                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kode *</label>
                            <input
                                v-model="facultyForm.code"
                                type="text"
                                placeholder="FT / FEB"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            />
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Fakultas / Unit Kerja *</label>
                            <input
                                v-model="facultyForm.name"
                                type="text"
                                placeholder="Fakultas Teknik"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Dekan / Pimpinan Unit</label>
                        <input
                            v-model="facultyForm.dean_name"
                            type="text"
                            placeholder="Dr. Ir. H. Ahmad Sudrajat, M.T."
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">No. Telepon / Hotline</label>
                            <input
                                v-model="facultyForm.phone"
                                type="text"
                                placeholder="0254-391234"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Email Resmi Fakultas</label>
                            <input
                                v-model="facultyForm.email"
                                type="email"
                                placeholder="teknik@unival.ac.id"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi & Ruang Lingkup</label>
                        <textarea
                            v-model="facultyForm.description"
                            rows="2"
                            placeholder="Keterangan program studi, laboratorium riset, dll..."
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button
                            type="button"
                            @click="showFacultyModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="facultyForm.processing"
                            class="px-5 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm transition disabled:opacity-50"
                        >
                            {{ editingFaculty ? 'Simpan Perubahan' : 'Daftarkan Fakultas' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Create / Edit Building -->
        <div v-if="showBuildingModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-lg w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ editingBuilding ? 'Ubah Data Gedung Kampus' : 'Tambah Gedung Kampus Baru' }}
                    </h3>
                    <button @click="showBuildingModal = false" class="text-slate-400 hover:text-slate-600">
                        <X class="w-4 h-4" />
                    </button>
                </div>
                <form @submit.prevent="submitBuilding" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kampus Lokasi *</label>
                        <select
                            v-model="buildingForm.campus_id"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            required
                        >
                            <option v-for="c in campuses" :key="c.id" :value="c.id">{{ c.name }} ({{ c.code }})</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kode Gedung *</label>
                            <input
                                v-model="buildingForm.code"
                                type="text"
                                placeholder="G-REK / G-FT"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            />
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Gedung *</label>
                            <input
                                v-model="buildingForm.name"
                                type="text"
                                placeholder="Gedung Rektorat Terpadu"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Lantai *</label>
                        <input
                            v-model.number="buildingForm.total_floors"
                            type="number"
                            min="1"
                            max="50"
                            placeholder="3"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            required
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi & Keterangan</label>
                        <textarea
                            v-model="buildingForm.description"
                            rows="2"
                            placeholder="Keterangan peruntukan gedung, tahun peresmian, dll..."
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100">
                        <button
                            type="button"
                            @click="showBuildingModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="buildingForm.processing"
                            class="px-5 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm transition disabled:opacity-50"
                        >
                            {{ editingBuilding ? 'Simpan Perubahan' : 'Daftarkan Gedung' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal: Create / Edit Room -->
        <div v-if="showRoomModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-xs">
            <div class="bg-white rounded-2xl max-w-xl w-full border border-slate-200 shadow-xl overflow-hidden animate-in fade-in zoom-in duration-150 max-h-[90vh] flex flex-col">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0">
                    <h3 class="text-sm font-bold text-slate-900">
                        {{ editingRoom ? 'Ubah Data Ruangan' : 'Tambah Ruangan Baru' }}
                    </h3>
                    <button @click="showRoomModal = false" class="text-slate-400 hover:text-slate-600">
                        <X class="w-4 h-4" />
                    </button>
                </div>
                <form @submit.prevent="submitRoom" class="p-6 space-y-4 overflow-y-auto">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Gedung Lokasi *</label>
                            <select
                                v-model="roomForm.building_id"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            >
                                <option v-for="b in buildings" :key="b.id" :value="b.id">{{ b.name }} ({{ b.code }})</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Fakultas / Unit Pengampu</label>
                            <select
                                v-model="roomForm.faculty_id"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            >
                                <option value="">-- Rektorat / Fasilitas Umum Kampus --</option>
                                <option v-for="f in faculties" :key="f.id" :value="f.id">{{ f.name }} ({{ f.code }})</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kode Ruang *</label>
                            <input
                                v-model="roomForm.code"
                                type="text"
                                placeholder="LAB-KOMP-1 / RK-201"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            />
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Ruangan *</label>
                            <input
                                v-model="roomForm.name"
                                type="text"
                                placeholder="Lab Komputer & Jaringan"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Tipe Ruangan *</label>
                            <select
                                v-model="roomForm.type"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            >
                                <option value="classroom">Ruang Kelas</option>
                                <option value="laboratory">Laboratorium</option>
                                <option value="office">Ruang Kantor / Dosen</option>
                                <option value="hall">Aula / Auditorium</option>
                                <option value="storage">Gudang Sarpras</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Lantai Ke- *</label>
                            <input
                                v-model.number="roomForm.floor_number"
                                type="number"
                                min="1"
                                max="50"
                                placeholder="2"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                                required
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kapasitas (Orang)</label>
                            <input
                                v-model.number="roomForm.capacity"
                                type="number"
                                min="1"
                                max="2000"
                                placeholder="40"
                                class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">PIC Ruangan / Laboran Penanggung Jawab</label>
                        <select
                            v-model="roomForm.pic_user_id"
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                        >
                            <option value="">-- Belum Ditugaskan / Biro Sarpras Umum --</option>
                            <option v-for="u in users" :key="u.id" :value="u.id">
                                {{ u.name }} ({{ u.role_label || u.role }}) - {{ u.department || 'Sarpras' }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi & Fasilitas Ruangan</label>
                        <textarea
                            v-model="roomForm.description"
                            rows="2"
                            placeholder="Fasilitas AC, Proyektor, 40 PC Client, Smartboard..."
                            class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a]"
                        ></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-100 shrink-0">
                        <button
                            type="button"
                            @click="showRoomModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="roomForm.processing"
                            class="px-5 py-2 bg-[#1e3a8a] hover:bg-[#172554] text-white rounded-xl text-xs font-bold shadow-sm transition disabled:opacity-50"
                        >
                            {{ editingRoom ? 'Simpan Perubahan' : 'Daftarkan Ruangan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
