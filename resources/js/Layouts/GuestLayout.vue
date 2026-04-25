<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="flex min-h-screen flex-col items-center bg-[#1a1a1a] pt-6 sm:justify-center sm:pt-0">

        <div class="mb-6">
            <Link href="/">
                <span class="text-orange-500 font-black text-xl tracking-widest uppercase">Big J Admin Portal</span>
            </Link>
        </div>

        <div class="w-full overflow-hidden bg-[#111] border border-white/10 rounded-2xl px-8 py-6 shadow-2xl shadow-black/60 sm:max-w-md">
            <slot />
        </div>

    </div>
</template>
