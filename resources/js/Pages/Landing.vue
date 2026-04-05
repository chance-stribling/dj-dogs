<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import ScrollDown from '@/Components/ScrollDown.vue';
import { computed, onMounted, ref } from 'vue';
import BookingModal from '@/Components/BookingModal.vue';

const showBooking = ref(false);

const props = defineProps({
    menuItems: {
        type: Array,
        default: () => []
    },
    currentLocation: {
        type: Object,
        default: null,
    },
    upcomingLocations: {
        type: Array,
        default: () => [],
    },
});
const orangePin = () => L.divIcon({
    className: '',
    html: `<div style="background:#F97316;width:34px;height:34px;border-radius:50%;display:flex;align-items:center;justify-content:center;border:2px solid white;box-shadow:0 2px 6px rgba(0,0,0,0.4);">
        <span class="mdi mdi-fire" style="color:white;font-size:18px;"></span>
    </div>`,
    iconSize: [34, 34],
    iconAnchor: [17, 17],
});

const groupedMenu = computed(() => {
    return props.menuItems.reduce((groups, item) => {
        if (!item.available) return groups;
        if (!groups[item.category]) groups[item.category] = [];
        groups[item.category].push(item);
        return groups;
    }, {});
});
let leafletMap = null;
let leafletMarkers = [];

function flyToLocation(lat, lng) {
    if (!leafletMap) return;
    leafletMap.flyTo([lat, lng], 15, { duration: 1 });
}
const categoryIcons = {
    'Signature Dogs': 'mdi-fire',
    'Classics':       'mdi-food-hot-dog',
    'Sides':          'mdi-french-fries',
};

const form = useForm({
    name:    '',
    email:   '',
    phone:   '',
    message: '',
});

const mobileMenuOpen = ref(false);
const scrolled = ref(false);
function submitForm() {
    form.post(route('contact.store'), {
        onSuccess: () => form.reset(),
    });
}

onMounted(() => {
    window.addEventListener('scroll', () => {
        scrolled.value = window.scrollY > 50;
    });

    if (!props.currentLocation) return;

    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
    document.head.appendChild(link);

    const script = document.createElement('script');
    script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
    script.onload = () => {
        leafletMap = L.map('leaflet-map', {
            zoomControl:     false,
            dragging:        false,
            scrollWheelZoom: false,
            doubleClickZoom: false,
            touchZoom:       false,
            keyboard:        false,
        }).setView([props.currentLocation.lat, props.currentLocation.lng], 14);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
        }).addTo(leafletMap);

        // Current location marker
        L.marker([props.currentLocation.lat, props.currentLocation.lng], { icon: orangePin() })
            .addTo(leafletMap)
            .bindPopup(`<b>${props.currentLocation.name}</b><br>${props.currentLocation.hours}`)
            .openPopup();

        // Upcoming markers
        props.upcomingLocations.forEach((location, index) => {
            L.marker([location.lat, location.lng], { icon: orangePin() })
                .addTo(leafletMap)
                .bindPopup(`<b>${location.name}</b><br>${location.hours ?? ''}`);
        });
    };
    document.head.appendChild(script);
});
</script>

<template>
    <Head title="Welcome" />

    <div class="relative flex min-h-screen w-full flex-col items-center selection:bg-brand-primary selection:text-white">

        <!-- ── HEADER ── -->
        <header class="flex items-center justify-between px-8 py-2 w-full fixed top-0 z-50 transition-colors duration-300"
                :class="scrolled ? 'bg-brand-secondary/90 backdrop-blur-sm' : 'bg-brand-secondary'">
            <a href="#landing" class="h-[54px] flex items-center gap-2 text-2xl font-bold">
                <v-icon icon="mdi-fire" class="text-brand-primary"></v-icon>
                <span class="text-brand-primary">DJ's Hot Dog Cart</span>
            </a>

            <!-- Desktop Nav -->
            <nav class="hidden md:flex gap-1">
                <a href="#menu"    class="px-4 py-2 text-sm font-bold text-white/70 hover:text-white transition uppercase tracking-wide">Menu</a>
                <a href="#map"     class="px-4 py-2 text-sm font-bold text-white/70 hover:text-white transition uppercase tracking-wide">Find Us</a>
                <a href="#about"   class="px-4 py-2 text-sm font-bold text-white/70 hover:text-white transition uppercase tracking-wide">Our Story</a>
                <a href="#contact" class="px-4 py-2 text-sm font-bold text-white/70 hover:text-white transition uppercase tracking-wide">Contact</a>
            </nav>

            <!-- Mobile Hamburger -->
            <button
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden text-white/70 hover:text-white transition"
                aria-label="Toggle menu"
            >
                <v-icon :icon="mobileMenuOpen ? 'mdi-close' : 'mdi-menu'" size="28"></v-icon>
            </button>

            <!-- Mobile Dropdown -->
            <div
                v-if="mobileMenuOpen"
                class="absolute top-full left-0 w-full bg-brand-secondary border-t border-white/10 flex flex-col py-2 md:hidden z-50"
            >
                <a href="#menu"    @click="mobileMenuOpen = false" class="px-8 py-4 text-sm font-bold text-white/70 hover:text-white hover:bg-white/5 transition uppercase tracking-wide">Menu</a>
                <a href="#map"     @click="mobileMenuOpen = false" class="px-8 py-4 text-sm font-bold text-white/70 hover:text-white hover:bg-white/5 transition uppercase tracking-wide">Find Us</a>
                <a href="#about"   @click="mobileMenuOpen = false" class="px-8 py-4 text-sm font-bold text-white/70 hover:text-white hover:bg-white/5 transition uppercase tracking-wide">Our Story</a>
                <a href="#contact" @click="mobileMenuOpen = false" class="px-8 py-4 text-sm font-bold text-white/70 hover:text-white hover:bg-white/5 transition uppercase tracking-wide">Contact</a>
            </div>
        </header>

        <main class="w-full">

            <!-- ── HERO ── -->
            <section id="landing" class="h-screen scroll-mt-0 pt-24 bg-brand-secondary flex items-center justify-center relative overflow-hidden">
                <div class="relative z-10 flex flex-col items-center text-center px-4">
                    <p class="text-brand-primary font-bold tracking-widest text-sm uppercase mb-6">
                        Kingsport's Finest Street Dogs
                    </p>
                    <h1 class="text-white font-black uppercase leading-none text-6xl md:text-8xl mb-2">
                        DJ's
                    </h1>
                    <h1 class="text-brand-primary font-black uppercase leading-none text-5xl md:text-7xl mb-8">
                        Hot Dog Cart
                    </h1>
                    <p class="text-white/70 text-lg md:text-xl max-w-xl mb-10">
                        Florida roots, Kingsport soul. Hand-crafted gourmet hot dogs brought straight
                        from the Sunshine State to the heart of Tennessee.
                    </p>
                    <div class="flex gap-4 mb-16">
                        <a href="#menu"
                           class="bg-brand-primary text-white font-bold uppercase tracking-widest px-8 py-4 rounded-full hover:bg-orange-600 transition">
                            View Menu
                        </a>
                        <a href="#map"
                           class="bg-brand-primary text-white font-bold uppercase tracking-widest px-8 py-4 rounded-full hover:bg-orange-600 transition">
                            <v-icon icon="mdi-map-marker" size="18"></v-icon>
                            Find Us Today
                        </a>
                    </div>
                    <ScrollDown target="#menu" />
                </div>
            </section>

            <!-- ── MENU ── -->
            <section id="menu" class="scroll-mt-[54px] bg-[#F5F4F0] py-8 px-6">
                <div class="max-w-5xl mx-auto">

                    <div class="text-center mb-16">
                        <p class="text-brand-primary font-bold tracking-widest text-sm uppercase flex items-center justify-center gap-2 mb-3">
                            <v-icon icon="mdi-silverware-fork-knife" size="16"></v-icon>
                            What We Serve
                        </p>
                        <h2 class="text-brand-secondary font-black uppercase text-5xl md:text-6xl tracking-tight">
                            The Menu
                        </h2>
                    </div>

                    <div class="flex flex-col gap-14">
                        <div v-for="(items, category) in groupedMenu" :key="category">
                            <div class="flex items-center gap-3 mb-6">
                                <v-icon :icon="categoryIcons[category] ?? 'mdi-star'" class="text-brand-primary" size="28"></v-icon>
                                <h3 class="text-brand-secondary font-black uppercase text-2xl tracking-wider">
                                    {{ category }}
                                </h3>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div
                                    v-for="item in items"
                                    :key="item.id"
                                    class="bg-white border border-gray-200 rounded-xl p-5 flex flex-col gap-2"
                                >
                                    <div class="flex items-start justify-between gap-4">
                                        <span class="font-black uppercase text-brand-secondary tracking-wide text-sm">{{ item.name }}</span>
                                        <span class="text-brand-primary font-bold text-sm whitespace-nowrap">${{ Number(item.price).toFixed(2) }}</span>
                                    </div>
                                    <p class="text-gray-500 text-sm leading-relaxed">{{ item.description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center mt-16">
                        <ScrollDown target="#map" />
                    </div>
                </div>
            </section>

            <!-- ── MAP ── -->
            <section id="map" class="scroll-mt-[54px] bg-brand-secondary py-8 px-6">
                <div class="max-w-4xl mx-auto">

                    <div class="text-center mb-16">
                        <p class="text-brand-primary font-bold tracking-widest text-sm uppercase flex items-center justify-center gap-2 mb-3">
                            <v-icon icon="mdi-map-marker-circle" size="16"></v-icon>
                            Find The Truck
                        </p>
                        <h2 class="text-white font-black uppercase text-5xl md:text-6xl tracking-tight">
                            Where We're At
                        </h2>
                    </div>

                    <div class="flex flex-col gap-6">
                        <div id="leaflet-map" class="w-full h-[400px] rounded-2xl overflow-hidden border border-white/10 z-0"></div>

                        <div
                            v-if="props.currentLocation"
                            @click="flyToLocation(props.currentLocation.lat, props.currentLocation.lng)"
                            class="border border-brand-primary bg-brand-primary/10 rounded-2xl p-6 flex flex-col gap-2 cursor-pointer transition-transform duration-200 hover:scale-[1.02]"
                        >
                            <p class="text-brand-primary font-bold uppercase tracking-widest text-sm flex items-center gap-2">
                                <v-icon icon="mdi-fire" size="16"></v-icon>
                                Today
                            </p>
                            <p class="font-bold text-white text-lg">{{ props.currentLocation.name }}</p>
                            <p class="text-white/60 flex items-center gap-2 text-sm">
                                <v-icon icon="mdi-clock-outline" size="16"></v-icon>
                                {{ props.currentLocation.hours }}
                            </p>
                            <p class=" text-white text-sm">{{ props.currentLocation.address }}</p>
                            <p v-if="props.currentLocation.notes" class="text-white text-sm">
                                {{ props.currentLocation.notes }}
                            </p>
                        </div>

                        <!-- Upcoming -->
                        <div
                            v-if="props.upcomingLocations.length"
                            class="bg-white/5 border border-white/10 rounded-2xl p-6"
                        >
                            <p class="text-brand-primary font-bold uppercase tracking-widest text-sm flex items-center gap-2 mb-4">
                                <v-icon icon="mdi-calendar" size="16"></v-icon>
                                Upcoming
                            </p>
                            <div class="flex flex-col gap-3">
                                <div
                                    v-for="location in props.upcomingLocations"
                                    :key="location.id"
                                    @click="flyToLocation(location.lat, location.lng)"
                                    class="bg-white/5 border border-white/10 rounded-2xl p-5 cursor-pointer transition-transform duration-200 hover:scale-[1.02]"
                                >
                                    <p class="font-bold text-white text-lg mb-1">
                                        {{ new Date(location.date).toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' }) }}
                                    </p>
                                    <p class="text-white text-sm">{{ location.name }}</p>
                                    <p class="text-white text-sm">{{ location.address }}</p>
                                    <p class="text-white text-sm">{{ location.hours }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-center mt-16">
                        <ScrollDown target="#about" />
                    </div>
                </div>
            </section>

            <!-- ── ABOUT ── -->
            <section id="about" class="scroll-mt-[54px] bg-brand-secondary py-8 px-6 overflow-hidden relative">
                <div class="max-w-5xl mx-auto flex flex-col gap-10">

                    <!-- Two column grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
                        <!-- Text -->
                        <div>
                            <p class="text-brand-primary font-bold tracking-widest text-sm uppercase flex items-center gap-2 mb-4">
                                <v-icon icon="mdi-heart-outline" size="16"></v-icon>
                                Our Story
                            </p>
                            <h2 class="text-white font-black uppercase text-5xl md:text-6xl leading-tight mb-2">Family. Fire.</h2>
                            <h2 class="text-brand-primary font-black uppercase text-5xl md:text-6xl leading-tight mb-8">Flavor.</h2>
                            <div class="flex flex-col gap-5 text-white/60 text-base leading-relaxed">
                                <p>
                                    What started as a backyard grill and a big dream in the Sunshine State became
                                    DJ's Hot Dog Cart — now proudly serving Kingsport, TN. After making the move
                                    from Florida, DJ and his mom brought their bold flavors and love for real food
                                    with them, and this corner of East Tennessee has never been the same.
                                </p>
                                <p>
                                    Behind the grill, you'll find DJ himself — a burly, bearded, shades-wearing
                                    grill master with hands built for charcoal and a heart of gold. By his side,
                                    the real boss — his mom, who keeps the recipes sacred and the customers smiling.
                                </p>
                                <p>
                                    Every dog we serve is hand-crafted with premium ingredients, loaded with bold
                                    toppings, and grilled to perfection over real flame. No shortcuts. No microwaves.
                                    Just honest, fire-kissed food from people who give a damn.
                                </p>
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="rounded-2xl overflow-hidden border-4 border-white/10">
                            <img src="/img/yes.png" alt="DJ and his mom at the grill" class="w-full h-full object-cover object-top" />
                        </div>


                    </div>

                    <!-- Stats — full width below both columns -->
                    <div class="grid grid-cols-3 gap-4 text-center pt-6 border-t border-white/10">
                        <div>
                            <p class="text-brand-primary font-black uppercase text-2xl tracking-wider">100%</p>
                            <p class="text-white/40 text-xs uppercase tracking-widest mt-1">All-Beef Dogs</p>
                        </div>
                        <div>
                            <p class="text-brand-primary font-black uppercase text-2xl tracking-wider">Family</p>
                            <p class="text-white/40 text-xs uppercase tracking-widest mt-1">Owned & Operated</p>
                        </div>
                        <div>
                            <p class="text-brand-primary font-black uppercase text-2xl tracking-wider">Daily</p>
                            <p class="text-white/40 text-xs uppercase tracking-widest mt-1">Fresh Ingredients</p>
                        </div>
                    </div>

                </div>

                <div class="flex justify-center mt-16">
                    <ScrollDown target="#contact" />
                </div>
            </section>

            <!-- ── CONTACT ── -->
            <section id="contact" class="scroll-mt-[54px] bg-[#F5F4F0] py-8 px-6">
                <div class="max-w-3xl mx-auto flex flex-col gap-8">

                    <div>
                        <p class="text-brand-primary font-bold tracking-widest text-sm uppercase flex items-center gap-2 mb-3">
                            <v-icon icon="mdi-message-outline" size="16"></v-icon>
                            Get In Touch
                        </p>
                        <h2 class="text-brand-secondary font-black uppercase text-5xl md:text-6xl leading-tight">Drop Us</h2>
                        <h2 class="text-brand-primary font-black uppercase text-5xl md:text-6xl leading-tight mb-4">A Line</h2>
                        <p class="text-gray-500 text-base leading-relaxed">
                            Got a question? Wanna tell us how much you love our dogs? Just wanna say hey?
                            We're all ears. Fill out the form and we'll get back to you faster than we can grill a dog.
                        </p>
                    </div>

                    <div class="bg-[#FAF0E6] border border-[#E8D5C0] rounded-2xl p-6 flex flex-col gap-3">
                        <p class="text-brand-secondary font-black uppercase tracking-wide">Want Us At Your Event?</p>
                        <p class="text-gray-500 text-sm">Planning a party, corporate event, or festival? We'll bring the grill to you.</p>
                        <div>
                            <button
                                @click="showBooking = true"
                                class="bg-brand-primary text-white font-bold uppercase tracking-widest text-sm px-6 py-3 rounded-full hover:bg-orange-600 transition"
                            >
                                Book An Event
                            </button>
                        </div>
                    </div>

                    <div v-if="$page.props.flash?.success"
                         class="bg-green-50 border border-green-200 text-green-700 rounded-2xl px-6 py-4 text-sm font-medium">
                        {{ $page.props.flash.success }}
                    </div>

                    <div class="bg-white border border-gray-200 rounded-2xl p-8 flex flex-col gap-6">
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-bold text-brand-secondary">Name *</label>
                            <input v-model="form.name" type="text" placeholder="Your name"
                                   class="border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-primary"
                                   :class="{ 'border-red-400': form.errors.name }" />
                            <span v-if="form.errors.name" class="text-red-500 text-xs">{{ form.errors.name }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-bold text-brand-secondary">Email *</label>
                            <input v-model="form.email" type="email" placeholder="you@email.com"
                                   class="border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-primary"
                                   :class="{ 'border-red-400': form.errors.email }" />
                            <span v-if="form.errors.email" class="text-red-500 text-xs">{{ form.errors.email }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-bold text-brand-secondary">Phone (optional)</label>
                            <input v-model="form.phone" type="tel" placeholder="(555) 123-4567"
                                   class="border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-primary" />
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-sm font-bold text-brand-secondary">Message *</label>
                            <textarea v-model="form.message" placeholder="What's on your mind?" rows="5"
                                      class="border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-brand-primary resize-y"
                                      :class="{ 'border-red-400': form.errors.message }"></textarea>
                            <span v-if="form.errors.message" class="text-red-500 text-xs">{{ form.errors.message }}</span>
                        </div>
                        <button @click="submitForm" :disabled="form.processing"
                                class="w-full bg-brand-primary text-white font-bold uppercase tracking-widest py-4 rounded-xl flex items-center justify-center gap-2 hover:bg-orange-600 transition disabled:opacity-50">
                            <v-icon icon="mdi-send-outline" size="18"></v-icon>
                            {{ form.processing ? 'Sending...' : 'Send Message' }}
                        </button>
                    </div>

                </div>
            </section>

        </main>

        <footer class="py-8 text-center text-sm text-white/40 bg-brand-secondary w-full">
            A Stribbles And Bits Creation
        </footer>

    </div>

    <!-- Booking Modal — outside main flow so it overlays everything -->
    <BookingModal v-if="showBooking" @close="showBooking = false" />
</template>
