<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import ExecutiveDashboard from './Dashboard/ExecutiveDashboard.vue';
import CivitasDashboard from './Dashboard/CivitasDashboard.vue';
import TeknisiDashboard from './Dashboard/TeknisiDashboard.vue';
import PicRuanganDashboard from './Dashboard/PicRuanganDashboard.vue';

const props = defineProps({
    dashboardType: {
        type: String,
        default: 'executive',
    },
    // Civitas Data
    civitasData: {
        type: Object,
        default: () => ({}),
    },
    // Teknisi Data
    teknisiData: {
        type: Object,
        default: () => ({}),
    },
    // PIC Ruangan Data
    picData: {
        type: Object,
        default: () => ({}),
    },
    // Executive / Sarpras Data
    metrics: {
        type: Object,
        default: () => ({}),
    },
    lowStockItems: {
        type: Array,
        default: () => [],
    },
    pendingLoans: {
        type: Array,
        default: () => [],
    },
    pendingBookings: {
        type: Array,
        default: () => [],
    },
    recentCirculations: {
        type: Array,
        default: () => [],
    },
    urgentTickets: {
        type: Array,
        default: () => [],
    },
    conditionCounts: {
        type: Object,
        default: () => ({}),
    },
    statusCounts: {
        type: Object,
        default: () => ({}),
    },
    distributionByFaculty: {
        type: Array,
        default: () => [],
    },
    utilizationData: {
        type: Array,
        default: () => [],
    },
    weeklyActivity: {
        type: Array,
        default: () => [],
    },
    serviceTrend: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="SIM-SARPRAS Dashboard" />

    <AuthenticatedLayout>
        <!-- Civitas Akademika (Mahasiswa, Dosen, Tendik) -->
        <CivitasDashboard 
            v-if="dashboardType === 'civitas'" 
            :civitas-data="civitasData" 
        />

        <!-- Teknisi Lapangan -->
        <TeknisiDashboard 
            v-else-if="dashboardType === 'teknisi'" 
            :teknisi-data="teknisiData" 
        />

        <!-- PIC Ruangan & Laboran -->
        <PicRuanganDashboard 
            v-else-if="dashboardType === 'pic_ruangan'" 
            :pic-data="picData" 
        />

        <!-- Executive & Sarpras (Super Admin & Admin Fakultas) -->
        <ExecutiveDashboard 
            v-else
            :metrics="metrics"
            :low-stock-items="lowStockItems"
            :pending-loans="pendingLoans"
            :pending-bookings="pendingBookings"
            :recent-circulations="recentCirculations"
            :urgent-tickets="urgentTickets"
            :condition-counts="conditionCounts"
            :status-counts="statusCounts"
            :distribution-by-faculty="distributionByFaculty"
            :utilization-data="utilizationData"
            :weekly-activity="weeklyActivity"
            :service-trend="serviceTrend"
        />
    </AuthenticatedLayout>
</template>
