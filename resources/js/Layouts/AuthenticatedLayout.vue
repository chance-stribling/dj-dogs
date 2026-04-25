<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-[#1a1a1a]">

            <!-- ── Top Nav ───────────────────────────────────────────── -->
            <nav class="border-b border-white/10 bg-[#111]">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between items-center">

                        <!-- Logo -->
                        <div class="flex items-center gap-8">
                            <Link :href="route('dashboard')" class="flex shrink-0 items-center">
                                <span class="text-brand-primary font-bold">Big J Admin Portal</span>
                            </Link>


                        </div>

                        <!-- Desktop right side -->
                        <div class="hidden sm:flex sm:items-center gap-3">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <button
                                        type="button"
                                        class="inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-xs font-black uppercase tracking-widest text-white transition hover:border-orange-500/40 hover:text-white focus:outline-none"
                                    >
                                        <!-- avatar initial -->
                                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-orange-500 text-xs font-black text-white leading-none pb-px">
    {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
</span>
                                        {{ $page.props.auth.user.name }}
                                        <svg
                                            class="h-3 w-3 text-white"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </template>

                                <template #content>
                                    <div class="rounded-xl border border-white/10 bg-[#111] py-1 shadow-2xl shadow-black/60 overflow-hidden min-w-[160px]">
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                            class="flex items-center gap-2 px-4 py-2.5 text-xs font-black uppercase tracking-widest text-white/50 transition hover:bg-orange-500/10 hover:text-orange-400"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            Profile
                                        </DropdownLink>

                                        <div class="my-1 border-t border-white/10"></div>

                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                            class="flex items-center gap-2 px-4 py-2.5 text-xs font-black uppercase tracking-widest !text-white/50 transition hover:!bg-orange-500/10 hover:!text-orange-400"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Log Out
                                        </DropdownLink>
                                    </div>
                                </template>
                            </Dropdown>
                        </div>

                        <!-- Hamburger (mobile) -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-lg p-2 text-white/40 transition hover:bg-white/5 hover:text-white/70 focus:outline-none"
                            >
                                <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ── Mobile Menu ───────────────────────────────────── -->
                <div
                    :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                    class="sm:hidden border-t border-white/10"
                >
                    <div class="space-y-1 px-3 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                            class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-xs font-black uppercase tracking-widest transition"
                            :class="route().current('dashboard')
                                ? 'bg-orange-500/10 text-orange-500'
                                : 'text-white/40 hover:bg-white/5 hover:text-white'"
                        >
                            Dashboard
                        </ResponsiveNavLink>
                    </div>

                    <!-- Mobile user info + actions -->
                    <div class="border-t border-white/10 pb-3 pt-4">
                        <div class="flex items-center gap-3 px-4 pb-3">
                            <span class="flex h-9 w-9 items-center justify-center rounded-full bg-orange-500 text-sm font-black text-white">
                                {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                            </span>
                            <div>
                                <p class="text-sm font-black text-white">{{ $page.props.auth.user.name }}</p>
                                <p class="text-xs text-white/40">{{ $page.props.auth.user.email }}</p>
                            </div>
                        </div>

                        <div class="space-y-1 px-3">
                            <ResponsiveNavLink
                                :href="route('profile.edit')"
                                class="flex items-center gap-2 rounded-lg px-3 py-2.5 text-xs font-black uppercase tracking-widest text-white/40 transition hover:bg-white/5 hover:text-white"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profile
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="flex w-full items-center gap-2 rounded-lg px-3 py-2.5 text-xs font-black uppercase tracking-widest text-red-400/60 transition hover:bg-red-500/10 hover:text-red-400"
                            >
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Log Out
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- ── Page Heading (optional slot) ─────────────────────── -->
            <header
                v-if="$slots.header"
                class="border-b border-white/10 bg-[#111]"
            >
                <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- ── Page Content ──────────────────────────────────────── -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
