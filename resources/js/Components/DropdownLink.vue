<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: { type: String, required: true },
    method: { type: String, default: 'get' },
    as: { type: String, default: 'a' },
});
</script>

<template>
    <Link
        :href="href"
        :method="method"
        :as="as"
        class="block w-full text-start leading-5 transition duration-150 ease-in-out focus:outline-none"
    >
        <slot />
    </Link>
</template>
