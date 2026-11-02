<script setup>
import { Link } from '@inertiajs/vue3';
import { ShieldCheck } from 'lucide-vue-next';
</script>

<template>
    <div class="min-h-screen flex flex-col items-center justify-center bg-[#f4f7fa] px-4 py-8 font-sans text-slate-900">
        <!-- Brand Header -->
        <div class="mb-6 text-center">
            <Link href="/" class="inline-flex items-center gap-3 group">
                <div class="h-12 w-12 rounded-2xl bg-[#1e3a8a] flex items-center justify-center text-white shadow-md shadow-blue-900/25 group-hover:scale-105 transition">
                    <ShieldCheck class="h-7 w-7 stroke-[2.2]" />
                </div>
                <div class="text-left">
                    <div class="text-xl font-extrabold tracking-tight text-[#1e3a8a] leading-none">SIM-SARPRAS</div>
                    <div class="text-xs font-semibold text-slate-500 mt-1">Universitas Al-Khairiyah</div>
                </div>
            </Link>
        </div>

        <!-- Auth Card Container -->
        <div class="w-full max-w-md bg-white border border-slate-200/80 rounded-2xl p-6 sm:p-8 shadow-sm">
            <slot />
        </div>

        <!-- Footer -->
        <div class="mt-6 text-center text-xs text-slate-400">
            &copy; {{ new Date().getFullYear() }} Biro Sarana & Prasarana • Universitas Al-Khairiyah
        </div>
    </div>
</template>
