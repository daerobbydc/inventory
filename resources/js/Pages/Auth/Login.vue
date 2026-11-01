<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { LogIn, Key, Mail, ShieldCheck } from 'lucide-vue-next';

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

const fillDemo = (email) => {
    form.email = email;
    form.password = 'password';
};

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Masuk - SIM-SARPRAS" />

        <div class="mb-5 text-center">
            <h2 class="text-xl font-black text-slate-900 tracking-tight">Masuk Portal SIM-SARPRAS</h2>
            <p class="text-xs text-slate-500 mt-1">Gunakan akun civitas akademika atau biro sarana prasarana</p>
        </div>

        <!-- Quick Demo Accounts Box -->
        <div class="mb-6 p-3 bg-slate-50 rounded-xl border border-slate-100">
            <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2">
                Pilih Akun Demo (1-Klik Isi Kredensial):
            </div>
            <div class="grid grid-cols-2 gap-1.5 text-[11px]">
                <button
                    type="button"
                    @click="fillDemo('sarpras@kampus.ac.id')"
                    class="p-2 text-left bg-white hover:bg-emerald-50 hover:border-emerald-300 border border-slate-200 rounded-lg transition"
                >
                    <div class="font-bold text-slate-800">Super Admin</div>
                    <div class="text-[10px] text-slate-400 truncate">sarpras@...</div>
                </button>
                <button
                    type="button"
                    @click="fillDemo('laboran.komputer@kampus.ac.id')"
                    class="p-2 text-left bg-white hover:bg-emerald-50 hover:border-emerald-300 border border-slate-200 rounded-lg transition"
                >
                    <div class="font-bold text-slate-800">PIC Ruangan</div>
                    <div class="text-[10px] text-slate-400 truncate">laboran...@...</div>
                </button>
                <button
                    type="button"
                    @click="fillDemo('teknisi@kampus.ac.id')"
                    class="p-2 text-left bg-white hover:bg-emerald-50 hover:border-emerald-300 border border-slate-200 rounded-lg transition"
                >
                    <div class="font-bold text-slate-800">Teknisi Servis</div>
                    <div class="text-[10px] text-slate-400 truncate">teknisi@...</div>
                </button>
                <button
                    type="button"
                    @click="fillDemo('mahasiswa@kampus.ac.id')"
                    class="p-2 text-left bg-white hover:bg-emerald-50 hover:border-emerald-300 border border-slate-200 rounded-lg transition"
                >
                    <div class="font-bold text-slate-800">Mahasiswa / BEM</div>
                    <div class="text-[10px] text-slate-400 truncate">mahasiswa@...</div>
                </button>
            </div>
        </div>

        <div v-if="status" class="mb-4 text-xs font-semibold text-emerald-700 bg-emerald-50 p-3 rounded-xl border border-emerald-200">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Alamat Email Kampus</label>
                <div class="relative">
                    <Mail class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
                    <input
                        id="email"
                        type="email"
                        v-model="form.email"
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] transition"
                        placeholder="nama@kampus.ac.id"
                        required
                        autofocus
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-bold text-slate-700">Kata Sandi</label>
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-[11px] text-[#168a42] hover:underline font-semibold"
                    >
                        Lupa sandi?
                    </Link>
                </div>
                <div class="relative">
                    <Key class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" />
                    <input
                        id="password"
                        type="password"
                        v-model="form.password"
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] transition"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    />
                </div>
                <InputError class="mt-1" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 cursor-pointer">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="text-xs text-slate-600 font-medium">Ingat Saya di Perangkat Ini</span>
                </label>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full py-2.5 bg-[#168a42] hover:bg-[#137a3b] text-white rounded-xl text-xs font-extrabold shadow-sm shadow-emerald-700/20 transition disabled:opacity-50 flex items-center justify-center gap-2"
            >
                <LogIn class="w-4 h-4" />
                <span>Masuk Sekarang</span>
            </button>

            <div class="pt-4 text-center text-xs text-slate-500 border-t border-slate-100">
                Belum memiliki akun terdaftar?
                <Link :href="route('register')" class="text-[#168a42] font-bold hover:underline ml-1">
                    Registrasi Akun Baru
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
