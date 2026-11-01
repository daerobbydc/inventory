<script setup>
import { ref } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
    LayoutDashboard, 
    Box, 
    Layers, 
    Repeat, 
    Wrench, 
    ClipboardCheck, 
    Building2,
    CheckCircle2,
    AlertCircle,
    X
} from 'lucide-vue-next';

const showingNavigationDropdown = ref(false);
const page = usePage();
const dismissFlash = ref(false);
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans">
        <!-- Top Navigation Bar -->
        <nav class="border-b border-slate-200 bg-white sticky top-0 z-30">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <div class="flex items-center gap-8">
                        <!-- Brand Logo -->
                        <Link :href="route('dashboard')" class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white shadow-md shadow-indigo-100">
                                <Box class="h-6 w-6 stroke-[2.2]" />
                            </div>
                            <div>
                                <span class="text-lg font-bold text-slate-800 tracking-tight leading-none block">INVENTARIS KAMPUS</span>
                                <span class="text-[11px] font-semibold text-indigo-600 uppercase tracking-wider block">Sistem Terpadu Sarpras</span>
                            </div>
                        </Link>

                        <!-- Desktop Navigation Links -->
                        <div class="hidden md:flex space-x-1">
                            <NavLink
                                :href="route('dashboard')"
                                :active="route().current('dashboard')"
                                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition"
                            >
                                <LayoutDashboard class="w-4 h-4" />
                                Dashboard
                            </NavLink>

                            <NavLink
                                :href="route('assets.index')"
                                :active="route().current('assets.*')"
                                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition"
                            >
                                <Layers class="w-4 h-4" />
                                Aset Tetap
                            </NavLink>

                            <NavLink
                                :href="route('loans.index')"
                                :active="route().current('loans.*')"
                                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg transition"
                            >
                                <Repeat class="w-4 h-4" />
                                Peminjaman
                            </NavLink>
                        </div>
                    </div>

                    <!-- Right User Menu -->
                    <div class="hidden sm:flex sm:items-center sm:gap-3">
                        <!-- Role Badge -->
                        <span v-if="$page.props.auth?.user?.role_label" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                            {{ $page.props.auth.user.role_label }}
                        </span>

                        <div class="relative ms-2">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 transition"
                                    >
                                        <div class="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center font-bold text-xs text-slate-600">
                                            {{ $page.props.auth?.user?.name?.charAt(0) || 'U' }}
                                        </div>
                                        <span class="max-w-[130px] truncate">{{ $page.props.auth?.user?.name }}</span>
                                        <svg class="h-4 w-4 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="px-4 py-2 border-b border-slate-100">
                                        <p class="text-xs text-slate-500 font-medium">Masuk sebagai:</p>
                                        <p class="text-xs font-semibold text-slate-800 truncate">{{ $page.props.auth?.user?.email }}</p>
                                    </div>
                                    <DropdownLink :href="route('profile.edit')">
                                        Pengaturan Profil
                                    </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">
                                        Keluar (Log Out)
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Mobile Hamburger -->
                    <div class="-me-2 flex items-center md:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center rounded-lg p-2 text-slate-500 hover:bg-slate-100"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Navigation Menu -->
            <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="md:hidden border-t border-slate-200 bg-white">
                <div class="space-y-1 px-4 pb-3 pt-2">
                    <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                        Dashboard
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('assets.index')" :active="route().current('assets.*')">
                        Katalog Aset Tetap
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('loans.index')" :active="route().current('loans.*')">
                        Peminjaman & Pengembalian
                    </ResponsiveNavLink>
                </div>
                <div class="border-t border-slate-200 px-4 py-3">
                    <div class="font-medium text-slate-800">{{ $page.props.auth?.user?.name }}</div>
                    <div class="text-xs text-slate-500">{{ $page.props.auth?.user?.email }}</div>
                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')">Profil</ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out</ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Flash Message Notification -->
        <div v-if="!dismissFlash && $page.props.flash?.success" class="bg-emerald-50 border-b border-emerald-200 px-4 py-3">
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

        <div v-if="!dismissFlash && $page.props.flash?.error" class="bg-rose-50 border-b border-rose-200 px-4 py-3">
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

        <!-- Page Header -->
        <header v-if="$slots.header" class="bg-white border-b border-slate-200 shadow-sm">
            <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Main Content Slot -->
        <main class="py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
    </div>
</template>
