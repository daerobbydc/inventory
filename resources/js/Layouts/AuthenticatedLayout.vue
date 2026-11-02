<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
    LayoutDashboard, 
    Layers, 
    Repeat, 
    Wrench, 
    FileText, 
    Settings, 
    Search, 
    Building2, 
    Calendar, 
    Bell, 
    CheckCircle2, 
    AlertCircle, 
    X,
    Menu,
    ChevronDown,
    ShieldCheck
} from 'lucide-vue-next';

const showingSidebarMobile = ref(false);
const page = usePage();
const dismissFlash = ref(false);
</script>

<template>
    <div class="min-h-screen bg-[#f4f7fa] flex text-slate-900 font-sans">
        <!-- Backdrop for Mobile Sidebar -->
        <div 
            v-if="showingSidebarMobile" 
            @click="showingSidebarMobile = false"
            class="fixed inset-0 z-40 bg-slate-900/50 md:hidden"
        ></div>

        <!-- Left Sidebar Navigation -->
        <aside 
            :class="[
                'fixed md:sticky top-0 z-50 h-screen w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between transition-transform duration-300 ease-in-out',
                showingSidebarMobile ? 'translate-x-0' : '-translate-x-full md:translate-x-0'
            ]"
        >
            <div>
                <!-- Brand Header -->
                <div class="h-20 px-6 flex items-center gap-3 border-b border-slate-100">
                    <div class="h-10 w-10 rounded-xl bg-[#168a42] flex items-center justify-center text-white shadow-md shadow-emerald-700/20">
                        <ShieldCheck class="h-6 w-6 stroke-[2.2]" />
                    </div>
                    <div>
                        <div class="text-base font-extrabold tracking-tight text-[#168a42] leading-none">SIM-SARPRAS</div>
                        <div class="text-[11px] font-medium text-slate-400 mt-1">Univ. Al-Khairiyah</div>
                    </div>
                </div>

                <!-- Nav Menu Links -->
                <nav class="p-4 space-y-1.5">
                    <!-- Dashboard -->
                    <Link
                        :href="route('dashboard')"
                        :class="[
                            'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition',
                            route().current('dashboard') 
                                ? 'bg-[#168a42] text-white shadow-sm shadow-emerald-700/20' 
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <LayoutDashboard class="w-4 h-4 shrink-0" />
                        <span>Dashboard</span>
                    </Link>

                    <!-- Data Aset & Inventaris -->
                    <Link
                        :href="route('assets.index')"
                        :class="[
                            'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition',
                            route().current('assets.*') 
                                ? 'bg-[#168a42] text-white shadow-sm shadow-emerald-700/20' 
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <Layers class="w-4 h-4 shrink-0" />
                        <span>Data Aset & Inventaris</span>
                    </Link>

                    <!-- Peminjaman & Fasilitas -->
                    <Link
                        :href="route('loans.index')"
                        :class="[
                            'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition',
                            route().current('loans.*') 
                                ? 'bg-[#168a42] text-white shadow-sm shadow-emerald-700/20' 
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <Repeat class="w-4 h-4 shrink-0" />
                        <span>Peminjaman & Fasilitas</span>
                    </Link>

                    <!-- Pemeliharaan & Servis -->
                    <Link
                        :href="route('maintenance.index')"
                        :class="[
                            'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition',
                            route().current('maintenance.*') 
                                ? 'bg-[#168a42] text-white shadow-sm shadow-emerald-700/20' 
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <Wrench class="w-4 h-4 shrink-0" />
                        <span>Pemeliharaan & Servis</span>
                    </Link>

                    <!-- Laporan & Audit -->
                    <Link
                        :href="route('reports.index')"
                        :class="[
                            'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition',
                            route().current('reports.*') 
                                ? 'bg-[#168a42] text-white shadow-sm shadow-emerald-700/20' 
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <FileText class="w-4 h-4 shrink-0" />
                        <span>Laporan & Audit</span>
                    </Link>

                    <!-- Pengaturan Sistem -->
                    <Link
                        :href="route('settings.index')"
                        :class="[
                            'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition',
                            route().current('settings.*') || route().current('profile.*')
                                ? 'bg-[#168a42] text-white shadow-sm shadow-emerald-700/20' 
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <Settings class="w-4 h-4 shrink-0" />
                        <span>Pengaturan Sistem</span>
                    </Link>
                </nav>
            </div>

            <!-- Bottom System Status Card in Sidebar -->
            <div class="p-4 border-t border-slate-100">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <div class="flex items-center justify-between text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                        <span>Status Sistem</span>
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    </div>
                    <div class="text-xs font-semibold text-slate-700 leading-tight">
                        BMN Synchronizer Online
                    </div>
                    <div class="text-[10px] text-slate-400 mt-0.5">(Kemdikbudristek)</div>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Top Header Bar -->
            <header class="h-16 bg-white border-b border-slate-200/80 sticky top-0 z-30 px-4 sm:px-8 flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 flex-1 max-w-xl">
                    <!-- Mobile Menu Hamburger -->
                    <button 
                        @click="showingSidebarMobile = !showingSidebarMobile"
                        class="p-2 text-slate-600 hover:bg-slate-100 rounded-lg md:hidden"
                    >
                        <Menu class="w-5 h-5" />
                    </button>

                    <!-- Search Bar with Pill Input -->
                    <div class="relative w-full max-w-md hidden sm:block">
                        <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
                        <input
                            type="text"
                            placeholder="Cari aset, ruangan, kode BMN, dll..."
                            class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#168a42]/30 focus:border-[#168a42] transition"
                        />
                    </div>
                </div>

                <!-- Right Top Tools -->
                <div class="flex items-center gap-3">
                    <!-- Campus Selector Pill -->
                    <div class="hidden lg:flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-full text-xs font-semibold text-slate-700 cursor-pointer transition">
                        <Building2 class="w-3.5 h-3.5 text-slate-400" />
                        <span>Kampus Utama (Pusat)</span>
                        <ChevronDown class="w-3.5 h-3.5 text-slate-400" />
                    </div>

                    <!-- Academic Year Selector Pill -->
                    <div class="hidden md:flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-full text-xs font-semibold text-slate-700 cursor-pointer transition">
                        <Calendar class="w-3.5 h-3.5 text-slate-400" />
                        <span>TA 2024/2025 Genap</span>
                        <ChevronDown class="w-3.5 h-3.5 text-slate-400" />
                    </div>

                    <!-- Notification Bell -->
                    <button class="relative p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-50 rounded-full transition">
                        <Bell class="w-4 h-4" />
                        <span class="absolute top-1 right-1 w-4 h-4 rounded-full bg-rose-500 text-white font-bold text-[9px] flex items-center justify-center">
                            3
                        </span>
                    </button>

                    <!-- User Profile Dropdown -->
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center gap-2 pl-2 pr-1 py-1 rounded-full hover:bg-slate-50 transition">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 text-[#168a42] border border-emerald-200 flex items-center justify-center font-bold text-xs">
                                    {{ $page.props.auth?.user?.name?.charAt(0) || 'B' }}
                                </div>
                                <div class="hidden sm:block text-left text-xs leading-tight">
                                    <div class="font-bold text-slate-800">{{ $page.props.auth?.user?.name || 'Ir. Bambang S., M.T.' }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $page.props.auth?.user?.department || 'Biro Sarana & Prasarana' }}</div>
                                </div>
                                <ChevronDown class="w-3.5 h-3.5 text-slate-400 hidden sm:block" />
                            </button>
                        </template>

                        <template #content>
                            <div class="px-4 py-2 border-b border-slate-100">
                                <span class="text-[10px] uppercase font-bold text-slate-400">Akun Pengguna</span>
                                <p class="text-xs font-semibold text-slate-800 truncate">{{ $page.props.auth?.user?.email }}</p>
                            </div>
                            <DropdownLink :href="route('profile.edit')">
                                Profil Pengguna
                            </DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">
                                Keluar (Log Out)
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Flash Notification Messages -->
            <div v-if="!dismissFlash && $page.props.flash?.success" class="bg-emerald-50 border-b border-emerald-200 px-6 py-3">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="flex items-center gap-2 text-emerald-800 text-sm font-medium">
                        <CheckCircle2 class="w-5 h-5 text-emerald-600 shrink-0" />
                        <span>{{ $page.props.flash.success }}</span>
                    </div>
                    <button @click="dismissFlash = true" class="text-emerald-600 hover:text-emerald-800">
                        <X class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <div v-if="!dismissFlash && $page.props.flash?.error" class="bg-rose-50 border-b border-rose-200 px-6 py-3">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="flex items-center gap-2 text-rose-800 text-sm font-medium">
                        <AlertCircle class="w-5 h-5 text-rose-600 shrink-0" />
                        <span>{{ $page.props.flash.error }}</span>
                    </div>
                    <button @click="dismissFlash = true" class="text-rose-600 hover:text-rose-800">
                        <X class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <!-- Page Body Slot -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                <slot />
            </main>
        </div>
    </div>
</template>
