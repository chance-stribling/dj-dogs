<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: { type: String },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <p class="mb-5 text-sm text-white/40 leading-relaxed">
            Forgot your password? No problem. Just let us know your email
            address and we will email you a password reset link that will allow
            you to choose a new one.
        </p>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="flex flex-col gap-5">

            <div class="flex flex-col gap-1">
                <label for="email" class="text-white/50 text-xs font-black uppercase tracking-widest">Email</label>
                <input
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    class="bg-[#1a1a1a] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-orange-500/60 placeholder:text-white/20"
                />
                <span v-if="form.errors.email" class="text-red-400 text-xs">{{ form.errors.email }}</span>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    :disabled="form.processing"
                    :class="{ 'opacity-50': form.processing }"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-black uppercase tracking-widest text-xs px-6 py-3 rounded-full transition"
                >
                    Email Reset Link
                </button>
            </div>

        </form>
    </GuestLayout>
</template>
