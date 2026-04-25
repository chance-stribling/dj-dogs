<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

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

            <div class="flex flex-col gap-1">
                <label for="password" class="text-white/50 text-xs font-black uppercase tracking-widest">Password</label>
                <input
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    class="bg-[#1a1a1a] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-orange-500/60"
                />
                <span v-if="form.errors.password" class="text-red-400 text-xs">{{ form.errors.password }}</span>
            </div>

            <label class="flex items-center gap-3 cursor-pointer select-none">
                <div class="relative w-10 h-5 flex-shrink-0">
                    <input type="checkbox" v-model="form.remember" class="peer sr-only" />
                    <div class="absolute inset-0 bg-white/10 rounded-full transition-colors peer-checked:bg-orange-500"></div>
                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                </div>
                <span class="text-white/50 text-sm font-bold">Remember me</span>
            </label>

            <div class="flex items-center justify-end gap-4 pt-1">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-xs text-white/30 hover:text-orange-400 uppercase tracking-widest font-bold transition underline"
                >
                    Forgot your password?
                </Link>

                <button
                    type="submit"
                    :disabled="form.processing"
                    :class="{ 'opacity-50': form.processing }"
                    class="bg-orange-500 hover:bg-orange-600 text-white font-black uppercase tracking-widest text-xs px-6 py-3 rounded-full transition"
                >
                    Log In
                </button>
            </div>

        </form>
    </GuestLayout>
</template>
