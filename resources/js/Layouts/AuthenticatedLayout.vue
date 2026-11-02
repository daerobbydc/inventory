<script setup>
import { computed, ref } from 'vue';
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
    AlertTriangle,
    Info,
    Clock,
    X,
    Menu,
    ChevronDown,
    ShieldCheck,
    Boxes,
    QrCode,
    ArchiveX,
    DoorOpen,
    User
} from 'lucide-vue-next';

const showingSidebarMobile = ref(false);
const showingNotifications = ref(false);
const page = usePage();
const dismissFlash = ref(false);

const notifications = computed(() => page.props.notifications || []);
const unreadCount = computed(() => page.props.unread_notifications_count || notifications.value.length || 0);

const currentRouteBreadcrumb = computed(() => {
    if (route().current('dashboard')) return 'Dashboard';
    if (route().current('assets.*')) return 'Data Aset & Inventaris';
    if (route().current('consumables.*')) return 'Barang Habis Pakai';
    if (route().current('loans.*')) return 'Peminjaman Aset';
    if (route().current('room-bookings.*')) return 'Peminjaman Ruangan';
    if (route().current('stock-opname.*')) return 'Sensus QR Code';
    if (route().current('maintenance.*')) return 'Pemeliharaan Fasilitas';
    if (route().current('disposals.*')) return 'Penghapusan Aset';
    if (route().current('reports.*')) return 'Laporan & Audit';
    if (route().current('settings.*')) return 'Pengaturan Sistem';
    if (route().current('profile.*')) return 'Profil Pengguna';
    return 'Halaman';
});

const currentRouteTitle = computed(() => {
    if (route().current('dashboard')) return 'Dashboard';
    if (route().current('assets.*')) return 'Katalog Aset & Inventaris';
    if (route().current('consumables.*')) return 'Barang Habis Pakai (ATK)';
    if (route().current('loans.*')) return 'Peminjaman Aset & Alat';
    if (route().current('room-bookings.*')) return 'Peminjaman Ruangan Kampus';
    if (route().current('stock-opname.*')) return 'Scanner QR Sensus';
    if (route().current('maintenance.*')) return 'Pemeliharaan & Servis';
    if (route().current('disposals.*')) return 'Penghapusan & Pemutihan Aset';
    if (route().current('reports.*')) return 'Laporan & Audit Kampus';
    if (route().current('settings.*')) return 'Pengaturan Sistem & Master Data';
    if (route().current('profile.*')) return 'Pengaturan Akun Pengguna';
    return 'SIM-SARPRAS';
});
</script>

<template>
    <div class="min-h-screen bg-[#f0f2f5] text-slate-800 font-sans antialiased">
        <!-- Backdrop for Mobile Sidebar -->
        <div 
            v-if="showingSidebarMobile" 
            @click="showingSidebarMobile = false"
            class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-xs md:hidden"
        ></div>

        <!-- Material Floating Dark Sidenav -->
        <aside 
            :class="[
                'fixed top-0 bottom-0 left-0 z-50 my-4 ml-4 w-64 rounded-2xl bg-gradient-to-b from-[#1a2035] via-[#161c2e] to-[#0f1422] text-white shadow-2xl flex flex-col justify-between transition-transform duration-300 ease-in-out select-none border border-white/5',
                showingSidebarMobile ? 'translate-x-0' : '-translate-x-80 md:translate-x-0'
            ]"
        >
            <div class="overflow-y-auto px-3 py-4 flex-1">
                <!-- Brand Header -->
                <div class="px-3 py-2 flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white shadow-md shadow-blue-500/30">
                        <ShieldCheck class="h-5 w-5 stroke-[2.2]" />
                    </div>
                    <div>
                        <div class="text-sm font-extrabold tracking-tight text-white leading-tight">SIM-SARPRAS</div>
                        <div class="text-[11px] font-medium text-slate-400">Univ. Al-Khairiyah</div>
                    </div>
                </div>

                <hr class="my-3 border-t border-white/10 mx-1" />

                <!-- Menu Utama -->
                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-3 py-1.5">
                    Menu Utama
                </div>

                <nav class="space-y-1">
                    <!-- Dashboard -->
                    <Link
                        :href="route('dashboard')"
                        :class="[
                            'group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition',
                            route().current('dashboard') 
                                ? 'bg-gradient-to-tr from-emerald-500 to-green-600 text-white font-bold shadow-lg shadow-emerald-600/30' 
                                : 'text-slate-300 hover:bg-white/10 hover:text-white font-medium'
                        ]"
                    >
                        <LayoutDashboard class="w-4 h-4 shrink-0" />
                        <span>Dashboard</span>
                    </Link>

                    <!-- Data Aset & Inventaris -->
                    <Link
                        :href="route('assets.index')"
                        :class="[
                            'group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition',
                            route().current('assets.*') 
                                ? 'bg-gradient-to-tr from-emerald-500 to-green-600 text-white font-bold shadow-lg shadow-emerald-600/30' 
                                : 'text-slate-300 hover:bg-white/10 hover:text-white font-medium'
                        ]"
                    >
                        <Layers class="w-4 h-4 shrink-0" />
                        <span>Data Aset & Inventaris</span>
                    </Link>

                    <!-- Barang Habis Pakai / ATK -->
                    <Link
                        v-if="$page.props.auth?.user?.can_manage_consumables"
                        :href="route('consumables.index')"
                        :class="[
                            'group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition',
                            route().current('consumables.*') 
                                ? 'bg-gradient-to-tr from-emerald-500 to-green-600 text-white font-bold shadow-lg shadow-emerald-600/30' 
                                : 'text-slate-300 hover:bg-white/10 hover:text-white font-medium'
                        ]"
                    >
                        <Boxes class="w-4 h-4 shrink-0" />
                        <span>Barang Habis Pakai (ATK)</span>
                    </Link>

                    <!-- Peminjaman Aset (Alat) -->
                    <Link
                        :href="route('loans.index')"
                        :class="[
                            'group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition',
                            route().current('loans.*') 
                                ? 'bg-gradient-to-tr from-emerald-500 to-green-600 text-white font-bold shadow-lg shadow-emerald-600/30' 
                                : 'text-slate-300 hover:bg-white/10 hover:text-white font-medium'
                        ]"
                    >
                        <Repeat class="w-4 h-4 shrink-0" />
                        <span>Peminjaman Aset (Alat)</span>
                    </Link>

                    <!-- Peminjaman Ruangan -->
                    <Link
                        :href="route('room-bookings.index')"
                        :class="[
                            'group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition',
                            route().current('room-bookings.*') 
                                ? 'bg-gradient-to-tr from-emerald-500 to-green-600 text-white font-bold shadow-lg shadow-emerald-600/30' 
                                : 'text-slate-300 hover:bg-white/10 hover:text-white font-medium'
                        ]"
                    >
                        <DoorOpen class="w-4 h-4 shrink-0" />
                        <span>Peminjaman Ruangan</span>
                    </Link>
                </nav>

                <!-- Fasilitas & Operasional -->
                <div class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400 px-3 pt-4 pb-1.5">
                    Fasilitas & Operasional
                </div>

                <nav class="space-y-1">
                    <!-- Scanner QR Sensus -->
                    <Link
                        v-if="$page.props.auth?.user?.can_perform_audit"
                        :href="route('stock-opname.scanner')"
                        :class="[
                            'group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition',
                            route().current('stock-opname.*') 
                                ? 'bg-gradient-to-tr from-emerald-500 to-green-600 text-white font-bold shadow-lg shadow-emerald-600/30' 
                                : 'text-slate-300 hover:bg-white/10 hover:text-white font-medium'
                        ]"
                    >
                        <QrCode class="w-4 h-4 shrink-0" />
                        <span>Scanner QR Sensus</span>
                    </Link>

                    <!-- Pemeliharaan & Servis -->
                    <Link
                        :href="route('maintenance.index')"
                        :class="[
                            'group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition',
                            route().current('maintenance.*') 
                                ? 'bg-gradient-to-tr from-emerald-500 to-green-600 text-white font-bold shadow-lg shadow-emerald-600/30' 
                                : 'text-slate-300 hover:bg-white/10 hover:text-white font-medium'
                        ]"
                    >
                        <Wrench class="w-4 h-4 shrink-0" />
                        <span>Pemeliharaan & Servis</span>
                    </Link>

                    <!-- Penghapusan Aset -->
                    <Link
                        v-if="$page.props.auth?.user?.can_manage_disposals"
                        :href="route('disposals.index')"
                        :class="[
                            'group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition',
                            route().current('disposals.*') 
                                ? 'bg-gradient-to-tr from-emerald-500 to-green-600 text-white font-bold shadow-lg shadow-emerald-600/30' 
                                : 'text-slate-300 hover:bg-white/10 hover:text-white font-medium'
                        ]"
                    >
                        <ArchiveX class="w-4 h-4 shrink-0" />
                        <span>Penghapusan Aset</span>
                    </Link>

                    <!-- Laporan & Audit -->
                    <Link
                        v-if="$page.props.auth?.user?.can_manage_assets"
                        :href="route('reports.index')"
                        :class="[
                            'group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition',
                            route().current('reports.*') 
                                ? 'bg-gradient-to-tr from-emerald-500 to-green-600 text-white font-bold shadow-lg shadow-emerald-600/30' 
                                : 'text-slate-300 hover:bg-white/10 hover:text-white font-medium'
                        ]"
                    >
                        <FileText class="w-4 h-4 shrink-0" />
                        <span>Laporan & Audit</span>
                    </Link>

                    <!-- Pengaturan Sistem -->
                    <Link
                        v-if="$page.props.auth?.user?.can_manage_settings"
                        :href="route('settings.index')"
                        :class="[
                            'group flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs transition',
                            route().current('settings.*')
                                ? 'bg-gradient-to-tr from-emerald-500 to-green-600 text-white font-bold shadow-lg shadow-emerald-600/30' 
                                : 'text-slate-300 hover:bg-white/10 hover:text-white font-medium'
                        ]"
                    >
                        <Settings class="w-4 h-4 shrink-0" />
                        <span>Pengaturan Sistem</span>
                    </Link>
                </nav>
            </div>

            <!-- Bottom Action Button (Material Dashboard Upgrade to Pro Signature) -->
            <div class="p-3 mx-2 mb-2">
                <Link
                    :href="route('loans.create')"
                    class="w-full flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl bg-gradient-to-tr from-emerald-500 to-green-600 text-white font-black text-xs shadow-lg shadow-emerald-600/30 uppercase tracking-wider hover:brightness-105 transition"
                >
                    <span>AJUKAN PEMINJAMAN</span>
                </Link>
            </div>
        </aside>

        <!-- Main Wrapper with Material Layout -->
        <div class="flex-1 flex flex-col min-w-0 md:ml-72 transition-all duration-300">
            <!-- Top Material Header Bar -->
            <header class="sticky top-4 z-40 mx-4 sm:mx-6 lg:mx-8 mb-6 py-2.5 px-4 bg-white/80 backdrop-blur-md rounded-2xl border border-slate-200/80 shadow-xs flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <button 
                        @click="showingSidebarMobile = !showingSidebarMobile"
                        class="p-2 text-slate-600 hover:bg-slate-100 rounded-lg md:hidden"
                    >
                        <Menu class="w-5 h-5" />
                    </button>

                    <!-- Breadcrumbs & Page Title -->
                    <div>
                        <div class="flex items-center gap-1.5 text-[11px] text-slate-400">
                            <Link :href="route('dashboard')" class="hover:text-slate-700">Pages</Link>
                            <span>/</span>
                            <span class="text-slate-600 font-medium">{{ currentRouteBreadcrumb }}</span>
                        </div>
                        <h1 class="text-sm font-bold text-slate-900 mt-0.5 leading-tight">{{ currentRouteTitle }}</h1>
                    </div>
                </div>

                <!-- Right Top Tools -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <!-- Search Input -->
                    <div class="relative hidden sm:block">
                        <Search class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-2.5" />
                        <input
                            type="text"
                            placeholder="Search here"
                            class="w-40 lg:w-56 pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] transition"
                        />
                    </div>

                    <!-- Notification Bell with Dropdown -->
                    <div class="relative">
                        <button 
                            @click="showingNotifications = !showingNotifications"
                            class="relative p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-full transition"
                            title="Pemberitahuan Sistem"
                        >
                            <Bell class="w-4 h-4" />
                            <span 
                                v-if="unreadCount > 0"
                                class="absolute top-1 right-1 w-4 h-4 rounded-full bg-rose-500 text-white font-bold text-[9px] flex items-center justify-center animate-pulse"
                            >
                                {{ unreadCount > 9 ? '9+' : unreadCount }}
                            </span>
                        </button>

                        <!-- Backdrop to close on click outside -->
                        <div 
                            v-show="showingNotifications" 
                            class="fixed inset-0 z-40" 
                            @click="showingNotifications = false"
                        ></div>

                        <!-- Notification Dropdown Panel -->
                        <Transition
                            enter-active-class="transition ease-out duration-150"
                            enter-from-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-100"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95"
                        >
                            <div 
                                v-show="showingNotifications" 
                                class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-slate-200/90 z-50 overflow-hidden"
                            >
                                <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <Bell class="w-4 h-4 text-[#1e3a8a]" />
                                        <span class="text-xs font-bold text-slate-900">Pemberitahuan Sistem</span>
                                    </div>
                                    <span v-if="unreadCount > 0" class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[10px] font-bold">
                                        {{ unreadCount }} Aktif
                                    </span>
                                </div>

                                <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                                    <div 
                                        v-if="notifications.length > 0"
                                        v-for="notif in notifications" 
                                        :key="notif.id"
                                    >
                                        <Link 
                                            :href="notif.url"
                                            @click="showingNotifications = false"
                                            class="p-3.5 hover:bg-slate-50 transition flex items-start gap-3 block"
                                        >
                                            <div 
                                                :class="[
                                                    'w-7 h-7 rounded-lg shrink-0 flex items-center justify-center mt-0.5',
                                                    notif.type === 'danger' ? 'bg-rose-50 text-rose-600' :
                                                    notif.type === 'warning' ? 'bg-amber-50 text-amber-600' :
                                                    notif.type === 'success' ? 'bg-emerald-50 text-emerald-600' :
                                                    'bg-blue-50 text-[#1e3a8a]'
                                                ]"
                                            >
                                                <AlertTriangle v-if="notif.type === 'danger'" class="w-3.5 h-3.5" />
                                                <Clock v-else-if="notif.type === 'warning'" class="w-3.5 h-3.5" />
                                                <CheckCircle2 v-else-if="notif.type === 'success'" class="w-3.5 h-3.5" />
                                                <Info v-else class="w-3.5 h-3.5" />
                                            </div>

                                            <div class="flex-1 min-w-0 space-y-0.5">
                                                <div class="text-xs font-bold text-slate-900 leading-tight">
                                                    {{ notif.title }}
                                                </div>
                                                <p class="text-[11px] text-slate-500 leading-normal line-clamp-2">
                                                    {{ notif.message }}
                                                </p>
                                                <div class="text-[10px] text-slate-400 pt-0.5 font-medium">
                                                    {{ notif.time }}
                                                </div>
                                            </div>
                                        </Link>
                                    </div>

                                    <div v-else class="p-8 text-center">
                                        <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                            <Bell class="w-5 h-5 text-slate-300" />
                                        </div>
                                        <p class="text-xs font-semibold text-slate-700">Tidak ada notifikasi baru</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Semua permohonan & tugas Anda sudah terbarui.</p>
                                    </div>
                                </div>

                                <div class="px-4 py-2 bg-slate-50/60 border-t border-slate-100 text-center">
                                    <span class="text-[10px] text-slate-400 font-medium">
                                        SIM-SARPRAS Universitas Al-Khairiyah
                                    </span>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- User Profile Dropdown -->
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button class="flex items-center gap-2 p-1.5 rounded-full hover:bg-slate-100 transition">
                                <div class="w-8 h-8 rounded-full bg-slate-900 text-white flex items-center justify-center font-bold text-xs shadow-xs">
                                    {{ $page.props.auth?.user?.name?.charAt(0) || 'U' }}
                                </div>
                                <div class="hidden lg:block text-left text-xs leading-tight pr-1">
                                    <div class="font-bold text-slate-800">{{ $page.props.auth?.user?.name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ $page.props.auth?.user?.role_label || 'Civitas' }}</div>
                                </div>
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
            <div v-if="!dismissFlash && $page.props.flash?.success" class="mx-4 sm:mx-6 lg:mx-8 mb-4 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 shadow-xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-emerald-800 text-xs font-medium">
                        <CheckCircle2 class="w-4 h-4 text-emerald-600 shrink-0" />
                        <span>{{ $page.props.flash.success }}</span>
                    </div>
                    <button @click="dismissFlash = true" class="text-emerald-600 hover:text-emerald-800">
                        <X class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>

            <div v-if="!dismissFlash && $page.props.flash?.error" class="mx-4 sm:mx-6 lg:mx-8 mb-4 bg-rose-50 border border-rose-200 rounded-xl px-4 py-3 shadow-xs">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2 text-rose-800 text-xs font-medium">
                        <AlertCircle class="w-4 h-4 text-rose-600 shrink-0" />
                        <span>{{ $page.props.flash.error }}</span>
                    </div>
                    <button @click="dismissFlash = true" class="text-rose-600 hover:text-rose-800">
                        <X class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>

            <!-- Page Body Slot -->
            <main class="flex-1 px-4 sm:px-6 lg:px-8 pb-12 w-full">
                <slot />
            </main>

            <!-- Floating Settings Gear Wheel Button at Bottom-Right (Material Dashboard Signature) -->
            <Link
                v-if="$page.props.auth?.user?.can_manage_settings"
                :href="route('settings.index')"
                class="fixed bottom-6 right-6 w-11 h-11 bg-white text-slate-700 rounded-full shadow-xl border border-slate-200 flex items-center justify-center hover:text-[#1e3a8a] hover:rotate-90 transition duration-300 z-40"
                title="Pengaturan Sistem"
            >
                <Settings class="w-5 h-5 stroke-[2.2]" />
            </Link>
        </div>
    </div>
</template>
