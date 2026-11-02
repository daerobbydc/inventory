<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';
import { User, ShieldCheck, ArrowLeft, KeyRound, UserX } from 'lucide-vue-next';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profil Pengguna - SIM-SARPRAS" />

    <AuthenticatedLayout>
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Header Banner -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200/70 shadow-sm flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('settings.index')"
                        class="p-2 bg-white border border-slate-200 rounded-xl text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition shadow-xs"
                    >
                        <ArrowLeft class="w-4 h-4" />
                    </Link>
                    <div>
                        <div class="flex items-center gap-2 text-xs font-semibold text-slate-500">
                            <span>Pengaturan Akun</span>
                            <span>•</span>
                            <span class="text-[#1e3a8a] font-bold">SIM-SARPRAS Universitas Al-Khairiyah</span>
                        </div>
                        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Profil & Keamanan Pengguna</h1>
                    </div>
                </div>
            </div>

            <!-- Profile Info Form Card -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/70 shadow-sm">
                <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-[#1e3a8a] mb-4">
                    <User class="w-4 h-4" />
                    <span>Informasi Data Diri</span>
                </div>
                <UpdateProfileInformationForm
                    :must-verify-email="mustVerifyEmail"
                    :status="status"
                    class="max-w-2xl"
                />
            </div>

            <!-- Update Password Card -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/70 shadow-sm">
                <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-amber-600 mb-4">
                    <KeyRound class="w-4 h-4" />
                    <span>Perbarui Kata Sandi</span>
                </div>
                <UpdatePasswordForm class="max-w-2xl" />
            </div>

            <!-- Delete User Card -->
            <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/70 shadow-sm">
                <div class="flex items-center gap-2 text-xs font-extrabold uppercase tracking-wider text-rose-600 mb-4">
                    <UserX class="w-4 h-4" />
                    <span>Hapus Akun Pengguna</span>
                </div>
                <DeleteUserForm class="max-w-2xl" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
