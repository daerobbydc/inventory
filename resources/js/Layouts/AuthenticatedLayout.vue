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
    DoorOpen
} from 'lucide-vue-next';

const showingSidebarMobile = ref(false);
const showingNotifications = ref(false);
const page = usePage();
const dismissFlash = ref(false);

const notifications = computed(() => page.props.notifications || []);
const unreadCount = computed(() => page.props.unread_notifications_count || notifications.value.length || 0);
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
                    <div class="h-10 w-10 rounded-xl bg-[#1e3a8a] flex items-center justify-center text-white shadow-md shadow-blue-900/25">
                        <ShieldCheck class="h-6 w-6 stroke-[2.2]" />
                    </div>
                    <div>
                        <div class="text-base font-extrabold tracking-tight text-[#1e3a8a] leading-none">SIM-SARPRAS</div>
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
                                ? 'bg-[#1e3a8a] text-white shadow-sm shadow-blue-900/25' 
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
                                ? 'bg-[#1e3a8a] text-white shadow-sm shadow-blue-900/25' 
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <Layers class="w-4 h-4 shrink-0" />
                        <span>Data Aset & Inventaris</span>
                    </Link>

                    <!-- Barang Habis Pakai / ATK (Staff only) -->
                    <Link
                        v-if="$page.props.auth?.user?.can_manage_consumables"
                        :href="route('consumables.index')"
                        :class="[
                            'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition',
                            route().current('consumables.*') 
                                ? 'bg-[#1e3a8a] text-white shadow-sm shadow-blue-900/25' 
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <Boxes class="w-4 h-4 shrink-0" />
                        <span>Barang Habis Pakai (ATK)</span>
                    </Link>

                    <!-- Peminjaman & Fasilitas -->
                    <Link
                        :href="route('loans.index')"
                        :class="[
                            'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition',
                            route().current('loans.*') 
                                ? 'bg-[#1e3a8a] text-white shadow-sm shadow-blue-900/25' 
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <Repeat class="w-4 h-4 shrink-0" />
                        <span>Peminjaman Aset (Alat)</span>
                    </Link>

                    <!-- Peminjaman Ruangan & Aula -->
                    <Link
                        :href="route('room-bookings.index')"
                        :class="[
                            'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition',
                            route().current('room-bookings.*') 
                                ? 'bg-[#1e3a8a] text-white shadow-sm shadow-blue-900/25' 
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <DoorOpen class="w-4 h-4 shrink-0" />
                        <span>Peminjaman Ruangan</span>
                    </Link>

                    <!-- Scanner QR Sensus (Staff & Teknisi) -->
                    <Link
                        v-if="$page.props.auth?.user?.can_perform_audit"
                        :href="route('stock-opname.scanner')"
                        :class="[
                            'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition',
                            route().current('stock-opname.*') 
                                ? 'bg-[#1e3a8a] text-white shadow-sm shadow-blue-900/25' 
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <QrCode class="w-4 h-4 shrink-0" />
                        <span>Scanner QR Sensus</span>
                    </Link>

                    <!-- Pemeliharaan & Servis -->
                    <Link
                        :href="route('maintenance.index')"
                        :class="[
                            'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition',
                            route().current('maintenance.*') 
                                ? 'bg-[#1e3a8a] text-white shadow-sm shadow-blue-900/25' 
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <Wrench class="w-4 h-4 shrink-0" />
                        <span>Pemeliharaan & Servis</span>
                    </Link>

                    <!-- Penghapusan Aset (Staff only) -->
                    <Link
                        v-if="$page.props.auth?.user?.can_manage_disposals"
                        :href="route('disposals.index')"
                        :class="[
                            'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition',
                            route().current('disposals.*') 
                                ? 'bg-[#1e3a8a] text-white shadow-sm shadow-blue-900/25' 
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <ArchiveX class="w-4 h-4 shrink-0" />
                        <span>Penghapusan Aset</span>
                    </Link>

                    <!-- Laporan & Audit (Staff only) -->
                    <Link
                        v-if="$page.props.auth?.user?.can_manage_assets"
                        :href="route('reports.index')"
                        :class="[
                            'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition',
                            route().current('reports.*') 
                                ? 'bg-[#1e3a8a] text-white shadow-sm shadow-blue-900/25' 
                                : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                        ]"
                    >
                        <FileText class="w-4 h-4 shrink-0" />
                        <span>Laporan & Audit</span>
                    </Link>

                    <!-- Pengaturan Sistem (Super Admin only) -->
                    <Link
                        v-if="$page.props.auth?.user?.can_manage_settings"
                        :href="route('settings.index')"
                        :class="[
                            'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition',
                            route().current('settings.*')
                                ? 'bg-[#1e3a8a] text-white shadow-sm shadow-blue-900/25' 
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
                        <span class="inline-block w-2 h-2 rounded-full bg-blue-600 animate-pulse"></span>
                    </div>
                    <div class="text-xs font-semibold text-slate-700 leading-tight">
                        Sinkronisasi Inventaris Terpadu
                    </div>
                    <div class="text-[10px] text-slate-400 mt-0.5">(Biro Sarpras Unival)</div>
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
                            placeholder="Cari aset, ruangan, kode inventaris..."
                            class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-full text-xs placeholder:text-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1e3a8a]/30 focus:border-[#1e3a8a] transition"
                        />
                    </div>
                </div>

                <!-- Right Top Tools -->
                <div class="flex items-center gap-3">
                    <!-- Notification Bell with Interactive Dropdown -->
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
                                <!-- Header -->
                                <div class="px-4 py-3 bg-slate-50/80 border-b border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <Bell class="w-4 h-4 text-[#1e3a8a]" />
                                        <span class="text-xs font-bold text-slate-900">Pemberitahuan Sistem</span>
                                    </div>
                                    <span v-if="unreadCount > 0" class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-700 text-[10px] font-bold">
                                        {{ unreadCount }} Aktif
                                    </span>
                                </div>

                                <!-- Notification Items List -->
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

                                    <!-- Empty State -->
                                    <div v-else class="p-8 text-center">
                                        <div class="w-10 h-10 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                            <Bell class="w-5 h-5 text-slate-300" />
                                        </div>
                                        <p class="text-xs font-semibold text-slate-700">Tidak ada notifikasi baru</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">Semua permohonan & tugas Anda sudah terbarui.</p>
                                    </div>
                                </div>

                                <!-- Footer -->
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
                            <button class="flex items-center gap-2 pl-2 pr-1 py-1 rounded-full hover:bg-slate-50 transition">
                                <div class="w-8 h-8 rounded-full bg-blue-100 text-[#1e3a8a] border border-blue-200 flex items-center justify-center font-bold text-xs">
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
