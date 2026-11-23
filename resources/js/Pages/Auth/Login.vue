<script setup>
import { ref } from 'vue';
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { 
    LogIn, 
    Lock, 
    Mail, 
    Eye, 
    EyeOff, 
    KeyRound, 
    ChevronDown, 
    Check, 
    UserCheck,
    Shield,
    Sparkles,
    HelpCircle
} from 'lucide-vue-next';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);
const showDemoAccounts = ref(false);
const selectedDemo = ref(null);

const demoAccounts = [
    {
        role: 'Super Admin',
        badge: 'Biro Sarpras',
        email: 'sarpras@kampus.ac.id',
        desc: 'Akses penuh seluruh modul, aset, & konfigurasi',
        color: 'from-blue-600 to-indigo-700',
    },
    {
        role: 'Admin Fakultas',
        badge: 'Fak. Teknik',
        email: 'fakultas.teknik@kampus.ac.id',
        desc: 'Kelola aset & persetujuan unit fakultas',
        color: 'from-cyan-600 to-blue-700',
    },
    {
        role: 'PIC Ruangan',
        badge: 'Laboran',
        email: 'laboran.komputer@kampus.ac.id',
        desc: 'Validasi peminjaman ruang & inventaris lab',
        color: 'from-emerald-600 to-teal-700',
    },
    {
        role: 'Teknisi Servis',
        badge: 'Pemeliharaan',
        email: 'teknisi@kampus.ac.id',
        desc: 'Pengerjaan tiket perbaikan & pemeliharaan',
        color: 'from-rose-600 to-pink-700',
    },
    {
        role: 'Dosen Pengajar',
        badge: 'Akademik',
        email: 'dosen@kampus.ac.id',
        desc: 'Peminjaman ruang kuliah & alat praktikum',
        color: 'from-amber-600 to-orange-700',
    },
    {
        role: 'Mahasiswa / BEM',
        badge: 'Civitas',
        email: 'mahasiswa@kampus.ac.id',
        desc: 'Reservasi ruang kegiatan ormawa & sarana',
        color: 'from-violet-600 to-purple-700',
    },
];

const fillDemo = (item) => {
    form.email = item.email;
    form.password = 'password';
    selectedDemo.value = item.role;
};

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Masuk Portal SSO - SIM-SARPRAS" />

        <!-- Card Header -->
        <div class="mb-6 text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-gradient-to-tr from-blue-700 to-indigo-600 text-white shadow-md shadow-blue-600/30 mb-3">
                <Shield class="w-6 h-6 stroke-[2.2]" />
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Masuk ke Portal SIM-SARPRAS</h2>
            <p class="text-xs text-slate-500 mt-1.5 max-w-sm mx-auto">
                Gunakan kredensial akun civitas akademika atau akun pengelola Biro Sarpras
            </p>
        </div>

        <!-- Flash Status Notification -->
        <div 
            v-if="status" 
            class="mb-5 text-xs font-semibold text-emerald-800 bg-emerald-50 p-3.5 rounded-2xl border border-emerald-200 flex items-center gap-2"
        >
            <Check class="w-4 h-4 text-emerald-600 shrink-0" />
            <span>{{ status }}</span>
        </div>

        <!-- Main Authentication Form -->
        <form @submit.prevent="submit" class="space-y-4">
            <!-- Email Field -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">
                    Alamat Email Kampus / Akun SSO
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <Mail class="w-4 h-4" />
                    </div>
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/25 focus:border-blue-600 transition"
                        placeholder="nama@kampus.ac.id"
                        required
                        autofocus
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-1.5" :message="form.errors.email" />
            </div>

            <!-- Password Field -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-bold text-slate-700">
                        Kata Sandi
                    </label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-[11px] text-blue-700 hover:text-blue-900 font-bold hover:underline"
                    >
                        Lupa sandi?
                    </Link>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <Lock class="w-4 h-4" />
                    </div>
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        v-model="form.password"
                        class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-900 placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/25 focus:border-blue-600 transition"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                        tabindex="-1"
                        :aria-label="showPassword ? 'Sembunyikan sandi' : 'Tampilkan sandi'"
                    >
                        <EyeOff v-if="showPassword" class="w-4 h-4" />
                        <Eye v-else class="w-4 h-4" />
                    </button>
                </div>
                <InputError class="mt-1.5" :message="form.errors.password" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-0.5">
                <label class="flex items-center gap-2.5 cursor-pointer select-none">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="text-xs text-slate-600 font-medium">Ingat Saya di Perangkat Ini</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button
                type="submit"
                :disabled="form.processing"
                class="w-full py-3 px-4 bg-gradient-to-r from-blue-700 to-indigo-700 hover:from-blue-800 hover:to-indigo-800 text-white rounded-xl text-xs sm:text-sm font-extrabold shadow-md shadow-blue-700/30 transition transform active:scale-[0.99] disabled:opacity-50 flex items-center justify-center gap-2"
            >
                <LogIn v-if="!form.processing" class="w-4 h-4" />
                <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                <span>{{ form.processing ? 'Memverifikasi...' : 'Masuk ke SIM-SARPRAS' }}</span>
            </button>
        </form>

        <!-- Discreet Collapsible Reviewer / Demo Accounts Drawer -->
        <div class="mt-6 pt-5 border-t border-slate-100">
            <button
                type="button"
                @click="showDemoAccounts = !showDemoAccounts"
                class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200/80 text-xs font-bold text-slate-700 transition"
            >
                <div class="flex items-center gap-2">
                    <KeyRound class="w-4 h-4 text-blue-600" />
                    <span>Akses Cepat Penguji / Reviewer Mode</span>
                    <span class="text-[10px] font-extrabold px-1.5 py-0.5 bg-blue-100 text-blue-800 rounded-md">
                        6 Role
                    </span>
                </div>
                <ChevronDown 
                    :class="['w-4 h-4 text-slate-400 transition-transform duration-200', showDemoAccounts ? 'rotate-180 text-blue-600' : '']" 
                />
            </button>

            <!-- Collapsible Grid of Roles -->
            <div 
                v-show="showDemoAccounts" 
                class="mt-3 p-3 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-2 animate-in fade-in duration-150"
            >
                <div class="text-[11px] text-slate-500 font-medium px-1 flex items-center justify-between">
                    <span>Pilih salah satu role untuk mengisi otomatis:</span>
                    <span v-if="selectedDemo" class="text-blue-700 font-bold flex items-center gap-1">
                        <Check class="w-3 h-3" /> {{ selectedDemo }} dipilih
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                    <button
                        v-for="item in demoAccounts"
                        :key="item.role"
                        type="button"
                        @click="fillDemo(item)"
                        :class="[
                            'p-2.5 text-left rounded-xl border transition flex flex-col justify-between group',
                            selectedDemo === item.role 
                                ? 'bg-blue-50/80 border-blue-400 shadow-xs ring-1 ring-blue-400/40' 
                                : 'bg-white hover:bg-blue-50/50 hover:border-blue-300 border-slate-200'
                        ]"
                    >
                        <div class="flex items-center justify-between gap-1 w-full">
                            <span class="font-bold text-slate-800 text-[11px] group-hover:text-blue-700 transition">
                                {{ item.role }}
                            </span>
                            <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600">
                                {{ item.badge }}
                            </span>
                        </div>
                        <div class="text-[10px] text-slate-400 truncate mt-1">
                            {{ item.email }}
                        </div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Registration Link -->
        <div class="mt-6 pt-5 text-center text-xs text-slate-500 border-t border-slate-100">
            Belum memiliki akun civitas terdaftar?
            <Link :href="route('register')" class="text-blue-700 font-extrabold hover:underline ml-1">
                Registrasi Akun Baru
            </Link>
        </div>
    </GuestLayout>
</template>
