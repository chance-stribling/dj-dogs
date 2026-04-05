<script setup>
const props = defineProps({
    target: {
        type: String,
        required: true,
    }
});

function scrollTo() {
    document.querySelector(props.target)?.scrollIntoView({ behavior: 'smooth' });
}
</script>

<template>
    <button
        @click="scrollTo"
        class="animate-bounce text-white/60 hover:text-white transition"
        aria-label="Scroll down"
    >
        <v-icon icon="mdi-chevron-down" size="36"></v-icon>
    </button>
</template>
