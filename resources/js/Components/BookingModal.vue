<script setup>
import { useForm } from '@inertiajs/vue3';

const emit = defineEmits(['close']);

const form = useForm({
    name:           '',
    email:          '',
    phone:          '',
    event_type:     '',
    event_date:     '',
    expected_guests: '',
    location:       '',
    details:        '',
});

function submit() {
    form.post(route('booking.store'), {
        onSuccess: () => {
            form.reset();
            emit('close');
        },
    });
}
</script>

<template>
    <div class="fixed inset-0 z-50 bg-brand-secondary overflow-y-auto">
        <div class="max-w-2xl mx-auto px-6 py-12">

            <!-- Back -->
            <button
                @click="$emit('close')"
                class="flex items-center gap-2 text-gray-400 hover:text-white transition text-sm mb-10"
            >
                <v-icon icon="mdi-arrow-left" size="16"></v-icon>
                Back to Home
            </button>

            <!-- Header -->
            <p class="text-brand-primary font-bold tracking-widest text-sm uppercase flex items-center gap-2 mb-3">
                <v-icon icon="mdi-fire" size="16"></v-icon>
                Events & Bookings
            </p>
            <h2 class="text-white font-black uppercase text-5xl leading-tight">Bring The</h2>
            <h2 class="text-brand-primary font-black uppercase text-5xl leading-tight mb-4">Grill To You</h2>
            <p class="text-gray-400 text-base mb-10">
                Want Big J's Hot Dog Cart at your next event? Fill out the form below and we'll work out the details.
            </p>

            <!-- Form -->
            <div class="bg-white rounded-2xl p-8 flex flex-col gap-5">

                <!-- Row 1: Name + Email -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-brand-secondary">Name *</label>
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Your name"
                            class="border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary"
                            :class="{ 'border-red-400': form.errors.name }"
                        />
                        <span v-if="form.errors.name" class="text-red-500 text-xs">{{ form.errors.name }}</span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-brand-secondary">Email *</label>
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="you@email.com"
                            class="border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary"
                            :class="{ 'border-red-400': form.errors.email }"
                        />
                        <span v-if="form.errors.email" class="text-red-500 text-xs">{{ form.errors.email }}</span>
                    </div>
                </div>

                <!-- Row 2: Phone + Event Type -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-brand-secondary">Phone</label>
                        <input
                            v-model="form.phone"
                            type="tel"
                            placeholder="(555) 123-4567"
                            class="border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary"
                        />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-brand-secondary">Event Type *</label>
                        <select
                            v-model="form.event_type"
                            class="border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-primary"
                            :class="{ 'border-red-400': form.errors.event_type }"
                        >
                            <option value="" disabled>Select type</option>
                            <option>Birthday Party</option>
                            <option>Corporate Event</option>
                            <option>Festival</option>
                            <option>Wedding</option>
                            <option>School Event</option>
                            <option>Other</option>
                        </select>
                        <span v-if="form.errors.event_type" class="text-red-500 text-xs">{{ form.errors.event_type }}</span>
                    </div>
                </div>

                <!-- Row 3: Date + Guests -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-brand-secondary">Event Date</label>
                        <input
                            v-model="form.event_date"
                            type="date"
                            class="border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-500 focus:outline-none focus:ring-2 focus:ring-brand-primary"
                        />
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-sm font-bold text-brand-secondary">Expected Guests</label>
                        <input
                            v-model="form.expected_guests"
                            type="number"
                            placeholder="~50"
                            min="1"
                            class="border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary"
                        />
                    </div>
                </div>

                <!-- Row 4: Location -->
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-bold text-brand-secondary">Event Location / Address</label>
                    <input
                        v-model="form.location"
                        type="text"
                        placeholder="Where's the party?"
                        class="border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary"
                    />
                </div>

                <!-- Row 5: Details -->
                <div class="flex flex-col gap-1">
                    <label class="text-sm font-bold text-brand-secondary">Details *</label>
                    <textarea
                        v-model="form.details"
                        placeholder="Tell us about your event — the more info, the better!"
                        rows="5"
                        class="border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-brand-primary resize-y"
                        :class="{ 'border-red-400': form.errors.details }"
                    ></textarea>
                    <span v-if="form.errors.details" class="text-red-500 text-xs">{{ form.errors.details }}</span>
                </div>

                <!-- Submit -->
                <button
                    @click="submit"
                    :disabled="form.processing"
                    class="w-full bg-brand-primary text-white font-bold uppercase tracking-widest py-4 rounded-xl flex items-center justify-center gap-2 hover:bg-orange-600 transition disabled:opacity-50"
                >
                    <v-icon icon="mdi-send-outline" size="18"></v-icon>
                    {{ form.processing ? 'Submitting...' : 'Submit Request' }}
                </button>

            </div>
        </div>
    </div>
</template>
