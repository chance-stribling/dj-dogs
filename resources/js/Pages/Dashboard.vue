<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

// ══════════════════════════════════════════════════════════════════════════
// TABS
// ══════════════════════════════════════════════════════════════════════════
const activeTab = ref('overview');
const tabs = [
    { key: 'overview',  label: 'Overview',  icon: 'mdi-view-dashboard-outline' },
    { key: 'menu',      label: 'Menu',      icon: 'mdi-food-hot-dog' },
    { key: 'locations', label: 'Locations', icon: 'mdi-map-marker-outline' },
    { key: 'messages',  label: 'Messages',  icon: 'mdi-message-outline' },
    { key: 'bookings',  label: 'Bookings',  icon: 'mdi-calendar-clock-outline' },
];

// ══════════════════════════════════════════════════════════════════════════
// REACTIVE DATA
// ══════════════════════════════════════════════════════════════════════════
const menuItems  = ref([]);
const categories = ref([]);
const messages   = ref([]);
const bookings   = ref([]);

// ══════════════════════════════════════════════════════════════════════════
// COMPUTED
// ══════════════════════════════════════════════════════════════════════════
const menuItemCount      = computed(() => menuItems.value.length);
const standLocationCount = computed(() => locations.value.length);
const messagesCount      = computed(() => messages.value.length);
const bookingsCount      = computed(() => bookings.value.length);
const unreadMessages     = computed(() => messages.value.filter(m => !m.read).length);
const pendingBookings    = computed(() => bookings.value.filter(b => b.status === 'pending').length);
const currentLocation    = computed(() => schedules.value.find(s => s.is_current) ?? null);

// ══════════════════════════════════════════════════════════════════════════
// HELPERS
// ══════════════════════════════════════════════════════════════════════════
function getCsrfToken() {
    const match = document.cookie.match(/XSRF-TOKEN=([^;]+)/);
    return match ? decodeURIComponent(match[1]) : '';
}

function formatDate(date) {
    if (!date) return '—';
    const d = new Date(date);
    return `${d.getMonth() + 1}/${d.getDate()}/${d.getFullYear()}`;
}

// ══════════════════════════════════════════════════════════════════════════
// DATA FETCHING
// ══════════════════════════════════════════════════════════════════════════
async function refreshMenu() {
    const [itemsRes, catsRes] = await Promise.all([
        fetch('/menu-items'),
        fetch('/menu-categories'),
    ]);
    menuItems.value  = (await itemsRes.json()).data;
    categories.value = (await catsRes.json()).data;
}

onMounted(async () => {
    try {
        const [itemsRes, catsRes, msgRes, bkRes] = await Promise.all([
            fetch('/menu-items'),
            fetch('/menu-categories'),
            fetch('/contact-messages'),
            fetch('/booking-requests'),
        ]);
        menuItems.value  = (await itemsRes.json()).data;
        categories.value = (await catsRes.json()).data;
        messages.value   = (await msgRes.json()).data;
        bookings.value   = (await bkRes.json()).data;
    } catch (e) {
        console.error('Failed to load dashboard data', e);
    }
    await refreshLocationsData();
});

// ══════════════════════════════════════════════════════════════════════════
// CATEGORIES
// ══════════════════════════════════════════════════════════════════════════
const showCategoryModal  = ref(false);
const editingCategory    = ref(null);
const categoryName       = ref('');
const categoryError      = ref('');
const categoryProcessing = ref(false);

function openCategoryModal(cat = null) {
    editingCategory.value   = cat;
    categoryName.value      = cat?.name ?? '';
    categoryError.value     = '';
    showCategoryModal.value = true;
}

async function saveCategory() {
    categoryError.value      = '';
    categoryProcessing.value = true;
    const isEditing = Boolean(editingCategory.value);
    const url       = isEditing ? `/menu-categories/${editingCategory.value.id}` : '/menu-categories';
    try {
        const res  = await fetch(url, {
            method:  isEditing ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
            body:    JSON.stringify({ name: categoryName.value }),
        });
        const data = await res.json();
        if (!res.ok) {
            categoryError.value = data.errors?.name?.[0] ?? 'Something went wrong.';
            return;
        }
        showCategoryModal.value = false;
        await refreshMenu();
    } catch (e) {
        console.error('saveCategory failed', e);
    } finally {
        categoryProcessing.value = false;
    }
}

async function deleteCategory(id) {
    if (!confirm('Delete this category? Items in it will move to "Menu Item".')) return;
    try {
        await fetch(`/menu-categories/${id}`, {
            method:  'DELETE',
            headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
        });
        await refreshMenu();
    } catch (e) {
        console.error('deleteCategory failed', e);
    }
}

// ══════════════════════════════════════════════════════════════════════════
// MENU ITEMS
// ══════════════════════════════════════════════════════════════════════════
const showMenuModal   = ref(false);
const editingMenuItem = ref(null);

const menuForm = ref({
    name:        '',
    category_id: null,
    price:       '',
    description: '',
    available:   true,
    processing:  false,
    errors:      {},
});

function openMenuModal(item = null) {
    editingMenuItem.value = item;
    menuForm.value.errors = {};
    if (item) {
        menuForm.value.name        = item.name;
        menuForm.value.category_id = item.category_id ?? null;
        menuForm.value.price       = item.price;
        menuForm.value.description = item.description;
        menuForm.value.available   = Boolean(item.available);
    } else {
        menuForm.value.name        = '';
        menuForm.value.category_id = categories.value[0]?.id ?? null;
        menuForm.value.price       = '';
        menuForm.value.description = '';
        menuForm.value.available   = true;
    }
    showMenuModal.value = true;
}

async function saveMenuItem() {
    menuForm.value.processing = true;
    menuForm.value.errors     = {};
    const isEditing = Boolean(editingMenuItem.value);
    const url       = isEditing ? `/menu-items/${editingMenuItem.value.id}` : '/menu-items';
    try {
        const res  = await fetch(url, {
            method:  isEditing ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
            body:    JSON.stringify({
                name:        menuForm.value.name,
                category_id: menuForm.value.category_id,
                price:       menuForm.value.price,
                description: menuForm.value.description,
                available:   menuForm.value.available,
            }),
        });
        const data = await res.json();
        if (!res.ok) {
            if (res.status === 422) {
                menuForm.value.errors = Object.fromEntries(
                    Object.entries(data.errors).map(([k, v]) => [k, v[0]])
                );
            }
            return;
        }
        showMenuModal.value = false;
        await refreshMenu();
    } catch (e) {
        console.error('saveMenuItem failed', e);
    } finally {
        menuForm.value.processing = false;
    }
}

async function deleteMenuItem(id) {
    if (!confirm('Delete this menu item?')) return;
    try {
        await fetch(`/menu-items/${id}`, {
            method:  'DELETE',
            headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
        });
        await refreshMenu();
    } catch (e) {
        console.error('deleteMenuItem failed', e);
    }
}

async function toggleAvailability(item) {
    try {
        const res  = await fetch(`/menu-items/${item.id}/toggle`, {
            method:  'PATCH',
            headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
        });
        const data = await res.json();
        const found = menuItems.value.find(i => i.id === item.id);
        if (found) found.available = data.data.available;
    } catch (e) {
        console.error('toggleAvailability failed', e);
    }
}

// ══════════════════════════════════════════════════════════════════════════
// LOCATIONS
// ══════════════════════════════════════════════════════════════════════════
const locations         = ref([]);
const schedules         = ref([]);
const showLocationModal = ref(false);
const editingLocation   = ref(null);
const locationForm      = ref({ name: '', address: '', notes: '', errors: {}, processing: false });

const showScheduleModal = ref(false);
const editingSchedule   = ref(null);
const scheduleForm      = ref({
    stand_location_id: null,
    date:              '',
    open_time:         '',
    close_time:        '',
    until_sold_out:    false,
    is_current:        false,
    errors:            {},
    processing:        false,
});

// Calendar
const calendarDate       = ref(new Date());
const calendarYear       = computed(() => calendarDate.value.getFullYear());
const calendarMonth      = computed(() => calendarDate.value.getMonth());
const calendarMonthLabel = computed(() =>
    calendarDate.value.toLocaleString('en-US', { month: 'long', year: 'numeric' })
);

function prevMonth() {
    calendarDate.value = new Date(calendarYear.value, calendarMonth.value - 1, 1);
}
function nextMonth() {
    calendarDate.value = new Date(calendarYear.value, calendarMonth.value + 1, 1);
}

const calendarDays = computed(() => {
    const year  = calendarYear.value;
    const month = calendarMonth.value;
    const first = new Date(year, month, 1).getDay();
    const total = new Date(year, month + 1, 0).getDate();
    const days  = [];
    for (let i = 0; i < first; i++) days.push(null);
    for (let d = 1; d <= total; d++) days.push(d);
    return days;
});

function schedulesForDay(day) {
    if (!day) return [];
    const iso = `${calendarYear.value}-${String(calendarMonth.value + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
    return schedules.value.filter(s => s.date?.substring(0, 10) === iso);
}

function isToday(day) {
    const now = new Date();
    return day === now.getDate()
        && calendarMonth.value === now.getMonth()
        && calendarYear.value  === now.getFullYear();
}

async function refreshLocationsData() {
    const [locRes, schRes] = await Promise.all([
        fetch('/stand-locations'),
        fetch('/stand-location-schedules'),
    ]);
    locations.value = (await locRes.json()).data;
    schedules.value = (await schRes.json()).data;
}

function openLocationModal(loc = null) {
    editingLocation.value      = loc;
    locationForm.value.name    = loc?.name    ?? '';
    locationForm.value.address = loc?.address ?? '';
    locationForm.value.notes   = loc?.notes   ?? '';
    locationForm.value.errors  = {};
    showLocationModal.value    = true;
}

async function saveLocation() {
    locationForm.value.processing = true;
    locationForm.value.errors     = {};
    const isEditing = Boolean(editingLocation.value);
    const url       = isEditing ? `/stand-locations/${editingLocation.value.id}` : '/stand-locations';
    try {
        const res  = await fetch(url, {
            method:  isEditing ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
            body:    JSON.stringify({ name: locationForm.value.name, address: locationForm.value.address, notes: locationForm.value.notes }),
        });
        const data = await res.json();
        if (!res.ok) { locationForm.value.errors = data.errors ?? {}; return; }
        showLocationModal.value = false;
        await refreshLocationsData();
    } finally {
        locationForm.value.processing = false;
    }
}

async function deleteLocation(id) {
    if (!confirm('Delete this location? All scheduled appearances will also be removed.')) return;
    await fetch(`/stand-locations/${id}`, {
        method:  'DELETE',
        headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
    });
    await refreshLocationsData();
}

function openScheduleModal(day = null, schedule = null) {
    editingSchedule.value = schedule;
    scheduleForm.value = {
        stand_location_id: schedule?.stand_location_id ?? (locations.value[0]?.id ?? null),
        date: schedule?.date
            ? schedule.date.substring(0, 10)
            : day
                ? `${calendarYear.value}-${String(calendarMonth.value + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`
                : '',
        open_time:      schedule?.open_time?.substring(0, 5)  ?? '',
        close_time:     schedule?.close_time?.substring(0, 5) ?? '',
        until_sold_out: schedule?.until_sold_out ?? false,
        is_current:     schedule?.is_current ?? false,
        errors:         {},
        processing:     false,
    };
    showScheduleModal.value = true;
}

async function saveSchedule() {
    scheduleForm.value.processing = true;
    scheduleForm.value.errors     = {};
    const isEditing = Boolean(editingSchedule.value);
    const url       = isEditing ? `/stand-location-schedules/${editingSchedule.value.id}` : '/stand-location-schedules';
    try {
        const res  = await fetch(url, {
            method:  isEditing ? 'PUT' : 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
            body:    JSON.stringify({
                stand_location_id: scheduleForm.value.stand_location_id,
                date:              scheduleForm.value.date,
                open_time:         scheduleForm.value.open_time  || null,
                close_time:        scheduleForm.value.until_sold_out ? null : (scheduleForm.value.close_time || null),
                until_sold_out:    scheduleForm.value.until_sold_out,
                is_current:        scheduleForm.value.is_current,
            }),
        });
        const data = await res.json();
        if (!res.ok) { scheduleForm.value.errors = data.errors ?? {}; return; }
        showScheduleModal.value = false;
        await refreshLocationsData();
    } finally {
        scheduleForm.value.processing = false;
    }
}

async function deleteSchedule(id) {
    if (!confirm('Remove this scheduled appearance?')) return;
    await fetch(`/stand-location-schedules/${id}`, {
        method:  'DELETE',
        headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
    });
    await refreshLocationsData();
}

function deleteScheduleFromModal(id) {
    showScheduleModal.value = false;
    deleteSchedule(id);
}

async function setCurrentSchedule(id) {
    await fetch(`/stand-location-schedules/${id}/set-current`, {
        method:  'PATCH',
        headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
    });
    await refreshLocationsData();
}

function formatHours(schedule) {
    if (!schedule) return '';
    const open  = schedule.open_time  ?? '';
    const close = schedule.until_sold_out ? 'Until Sold Out' : (schedule.close_time ?? '');
    return [open, close].filter(Boolean).join(' – ');
}

function setAsToday(loc) {
    const today = new Date();
    const date  = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;
    const existing = schedules.value.find(
        s => s.stand_location_id === loc.id && s.date?.substring(0, 10) === date
    );
    if (existing) {
        setCurrentSchedule(existing.id);
    } else {
        editingSchedule.value = null;
        scheduleForm.value = {
            stand_location_id: loc.id,
            date,
            open_time:      '',
            close_time:     '',
            until_sold_out: false,
            is_current:     true,
            errors:         {},
            processing:     false,
        };
        showScheduleModal.value = true;
    }
}

// ══════════════════════════════════════════════════════════════════════════
// MESSAGES
// ══════════════════════════════════════════════════════════════════════════
const selectedMessage = ref(null);

function openMessage(msg) {
    selectedMessage.value = msg;
    if (!msg.read) {
        fetch(route('admin.messages.markRead', msg.id), {
            method:  'PATCH',
            headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
        }).then(() => {
            const found = messages.value.find(m => m.id === msg.id);
            if (found) found.read = true;
        });
    }
}

function deleteMessage(id) {
    if (!confirm('Are you sure? This message will be permanently deleted and cannot be recovered.')) return;
    fetch(route('admin.messages.destroy', id), {
        method:  'DELETE',
        headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
    }).then(() => {
        messages.value        = messages.value.filter(m => m.id !== id);
        selectedMessage.value = null;
    });
}

// ══════════════════════════════════════════════════════════════════════════
// BOOKINGS
// ══════════════════════════════════════════════════════════════════════════
const selectedBooking = ref(null);

function openBooking(bk) {
    selectedBooking.value = bk;
    if (!bk.read) {
        fetch(route('admin.bookings.markRead', bk.id), {
            method:  'PATCH',
            headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
        }).then(() => {
            const found = bookings.value.find(b => b.id === bk.id);
            if (found) found.read = true;
        });
    }
}

function updateBookingStatus(id, status) {
    fetch(route('admin.bookings.updateStatus', id), {
        method:  'PATCH',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
        body:    JSON.stringify({ status }),
    }).then(() => {
        const found = bookings.value.find(b => b.id === id);
        if (found) found.status = status;
        if (selectedBooking.value?.id === id) {
            selectedBooking.value = { ...selectedBooking.value, status };
        }
    });
}

function deleteBooking(id) {
    if (!confirm('Delete this booking request?')) return;
    fetch(route('admin.bookings.destroy', id), {
        method:  'DELETE',
        headers: { 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
    }).then(() => {
        bookings.value        = bookings.value.filter(b => b.id !== id);
        selectedBooking.value = null;
    });
}

const statusColor = (s) => ({
    pending:  'text-yellow-400 bg-yellow-400/10 border-yellow-400/30',
    approved: 'text-green-400 bg-green-400/10 border-green-400/30',
    declined: 'text-red-400 bg-red-400/10 border-red-400/30',
}[s] ?? 'text-white/40 bg-white/5 border-white/10');
</script>

<template>
    <Head title="Admin Portal" />

    <AuthenticatedLayout>

        <div class="min-h-screen bg-[#1a1a1a]">

            <!-- Tab Bar -->
            <div class="sticky top-0 z-30 bg-[#111] border-b border-white/10">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex gap-1 overflow-x-auto">
                    <button
                        v-for="tab in tabs"
                        :key="tab.key"
                        @click="activeTab = tab.key"
                        class="relative flex items-center gap-2 px-5 py-4 text-xs font-black uppercase tracking-widest transition whitespace-nowrap"
                        :class="activeTab === tab.key ? 'text-orange-500' : 'text-white/40 hover:text-white/70'"
                    >
                        <v-icon :icon="tab.icon" size="16"></v-icon>
                        {{ tab.label }}
                        <span v-if="tab.key === 'messages' && unreadMessages"
                              class="ml-1 bg-orange-500 text-white text-[10px] font-black rounded-full w-4 h-4 flex items-center justify-center">
                            {{ unreadMessages }}
                        </span>
                        <span v-if="tab.key === 'bookings' && pendingBookings"
                              class="ml-1 bg-orange-500 text-white text-[10px] font-black rounded-full w-4 h-4 flex items-center justify-center">
                            {{ pendingBookings }}
                        </span>
                        <span v-if="activeTab === tab.key"
                              class="absolute bottom-0 left-0 right-0 h-0.5 bg-orange-500 rounded-t-full"></span>
                    </button>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

                <!-- ══════════════════════════════════════════════════════ -->
                <!-- OVERVIEW                                               -->
                <!-- ══════════════════════════════════════════════════════ -->
                <div v-if="activeTab === 'overview'" class="flex flex-col gap-6">

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-[#222] border border-white/10 rounded-2xl p-6 flex flex-col gap-2">
                            <v-icon icon="mdi-food-hot-dog" class="text-orange-500" size="28"></v-icon>
                            <p class="text-3xl font-black text-white">{{ menuItemCount }}</p>
                            <p class="text-white/40 text-xs uppercase tracking-widest font-bold">Menu Items</p>
                        </div>
                        <div class="bg-[#222] border border-white/10 rounded-2xl p-6 flex flex-col gap-2">
                            <v-icon icon="mdi-map-marker" class="text-orange-500" size="28"></v-icon>
                            <p class="text-3xl font-black text-white">{{ standLocationCount }}</p>
                            <p class="text-white/40 text-xs uppercase tracking-widest font-bold">Locations</p>
                        </div>
                        <div class="bg-[#222] border border-white/10 rounded-2xl p-6 flex flex-col gap-2">
                            <v-icon icon="mdi-message" class="text-orange-500" size="28"></v-icon>
                            <p class="text-3xl font-black text-white">{{ messagesCount }}</p>
                            <p class="text-white/40 text-xs uppercase tracking-widest font-bold">
                                Messages
                                <span v-if="unreadMessages" class="text-orange-500">({{ unreadMessages }} new)</span>
                            </p>
                        </div>
                        <div class="bg-[#222] border border-white/10 rounded-2xl p-6 flex flex-col gap-2">
                            <v-icon icon="mdi-calendar-clock" class="text-orange-500" size="28"></v-icon>
                            <p class="text-3xl font-black text-white">{{ bookingsCount }}</p>
                            <p class="text-white/40 text-xs uppercase tracking-widest font-bold">
                                Bookings
                                <span v-if="pendingBookings" class="text-orange-500">({{ pendingBookings }} pending)</span>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <!-- Recent Messages -->
                        <div class="bg-[#222] border border-white/10 rounded-2xl p-6 flex flex-col gap-4">
                            <div class="flex items-center justify-between">
                                <p class="text-orange-500 font-black uppercase tracking-widest text-xs flex items-center gap-2">
                                    <v-icon icon="mdi-message-outline" size="14"></v-icon> Recent Messages
                                </p>
                                <button @click="activeTab = 'messages'" class="text-white/30 hover:text-orange-500 text-xs uppercase tracking-widest font-bold transition">View All</button>
                            </div>
                            <div v-if="!messages.length" class="text-white/30 text-sm">No messages yet.</div>
                            <div v-for="msg in messages.slice(0, 4)" :key="msg.id"
                                 @click="activeTab = 'messages'; openMessage(msg)"
                                 class="flex items-start gap-3 p-3 rounded-xl hover:bg-white/5 cursor-pointer transition">
                                <span class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0"
                                      :class="msg.read ? 'bg-white/20' : 'bg-orange-500'"></span>
                                <div class="min-w-0">
                                    <p class="text-white text-sm font-bold truncate">{{ msg.name }}</p>
                                    <p class="text-white/40 text-xs truncate">{{ msg.message }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Pending Bookings -->
                        <div class="bg-[#222] border border-white/10 rounded-2xl p-6 flex flex-col gap-4">
                            <div class="flex items-center justify-between">
                                <p class="text-orange-500 font-black uppercase tracking-widest text-xs flex items-center gap-2">
                                    <v-icon icon="mdi-calendar-clock-outline" size="14"></v-icon> Pending Bookings
                                </p>
                                <button @click="activeTab = 'bookings'" class="text-white/30 hover:text-orange-500 text-xs uppercase tracking-widest font-bold transition">View All</button>
                            </div>
                            <div v-if="!pendingBookings" class="text-white/30 text-sm">No pending bookings.</div>
                            <div v-for="bk in bookings.filter(b => b.status === 'pending').slice(0, 4)" :key="bk.id"
                                 @click="activeTab = 'bookings'; openBooking(bk)"
                                 class="flex items-start gap-3 p-3 rounded-xl hover:bg-white/5 cursor-pointer transition">
                                <v-icon icon="mdi-calendar-blank" class="text-orange-500 mt-0.5 flex-shrink-0" size="16"></v-icon>
                                <div class="min-w-0">
                                    <p class="text-white text-sm font-bold truncate">{{ bk.name }}</p>
                                    <p class="text-white/40 text-xs truncate">{{ formatDate(bk.event_date) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Location -->
                    <div class="bg-[#222] border border-orange-500/30 rounded-2xl p-6">
                        <p class="text-orange-500 font-black uppercase tracking-widest text-xs flex items-center gap-2 mb-4">
                            <v-icon icon="mdi-fire" size="14"></v-icon> Today's Location
                        </p>
                        <div v-if="currentLocation" class="flex flex-col gap-1">
                            <p class="text-white font-black text-xl">{{ currentLocation.stand_location?.name }}</p>
                            <p class="text-white/50 text-sm">{{ currentLocation.stand_location?.address }}</p>
                            <p class="text-white/50 text-sm">{{ formatHours(currentLocation) }}</p>
                        </div>
                        <div v-else class="text-white/30 text-sm">
                            No current location set. Go to
                            <button @click="activeTab = 'locations'" class="text-orange-500 underline">Locations</button>
                            to set one.
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════ -->
                <!-- MENU                                                    -->
                <!-- ══════════════════════════════════════════════════════ -->
                <div v-if="activeTab === 'menu'" class="flex flex-col gap-6">

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-500 font-black uppercase tracking-widest text-xs mb-1">Manage</p>
                            <h3 class="text-white font-black uppercase text-3xl tracking-tight">Menu Items</h3>
                        </div>
                        <div class="flex items-center gap-3">
                            <button @click="openCategoryModal()"
                                    class="border border-white/10 hover:border-orange-500/40 text-white/50 hover:text-orange-500 font-black uppercase tracking-widest text-xs px-4 py-3 rounded-full flex items-center gap-2 transition">
                                <v-icon icon="mdi-tag-plus-outline" size="16"></v-icon> Category
                            </button>
                            <button @click="openMenuModal()"
                                    class="bg-orange-500 hover:bg-orange-600 text-white font-black uppercase tracking-widest text-xs px-5 py-3 rounded-full flex items-center gap-2 transition">
                                <v-icon icon="mdi-plus" size="16"></v-icon> Add Item
                            </button>
                        </div>
                    </div>

                    <!-- Category chips -->
                    <div v-if="categories.length" class="flex flex-wrap gap-2 items-center">
                        <span class="text-white/30 text-xs uppercase tracking-widest font-bold mr-1">Categories:</span>
                        <div v-for="cat in categories" :key="cat.id"
                             class="flex items-center gap-1 bg-white/5 border border-white/10 rounded-full pl-3 pr-1 py-1">
                            <span class="text-white/70 text-xs font-bold">{{ cat.name }}</span>
                            <button @click="openCategoryModal(cat)" class="text-white/30 hover:text-orange-500 transition p-1 rounded-full" title="Rename">
                                <v-icon icon="mdi-pencil-outline" size="12"></v-icon>
                            </button>
                            <button @click="deleteCategory(cat.id)" class="text-white/30 hover:text-red-400 transition p-1 rounded-full" title="Delete category">
                                <v-icon icon="mdi-close" size="12"></v-icon>
                            </button>
                        </div>
                        <span v-if="menuItems.some(i => !i.category_id)"
                              class="flex items-center gap-1 bg-white/5 border border-white/10 rounded-full px-3 py-1">
                            <span class="text-white/30 text-xs italic">Menu Item (uncategorised)</span>
                        </span>
                    </div>

                    <!-- Items grouped by category -->
                    <div v-for="cat in categories" :key="cat.id" class="flex flex-col gap-3">
                        <p class="text-white/40 text-xs uppercase tracking-widest font-black border-b border-white/10 pb-2">{{ cat.name }}</p>
                        <div v-if="!menuItems.filter(i => i.category_id === cat.id).length"
                             class="text-white/20 text-sm italic pl-2">No items in this category.</div>
                        <div v-for="item in menuItems.filter(i => i.category_id === cat.id)" :key="item.id"
                             class="bg-[#222] border border-white/10 rounded-xl p-4 flex items-center gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-white font-black uppercase text-sm">{{ item.name }}</p>
                                    <span class="text-orange-500 font-bold text-sm">${{ Number(item.price).toFixed(2) }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full font-bold uppercase tracking-wide border"
                                          :class="item.available ? 'text-green-400 bg-green-400/10 border-green-400/20' : 'text-white/30 bg-white/5 border-white/10'">
                                        {{ item.available ? 'Available' : 'Hidden' }}
                                    </span>
                                </div>
                                <p class="text-white/40 text-xs truncate">{{ item.description }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <button @click="toggleAvailability(item)" class="text-white/40 hover:text-orange-500 transition p-2 rounded-lg hover:bg-white/5" :title="item.available ? 'Hide item' : 'Show item'">
                                    <v-icon :icon="item.available ? 'mdi-eye-outline' : 'mdi-eye-off-outline'" size="18"></v-icon>
                                </button>
                                <button @click="openMenuModal(item)" class="text-white/40 hover:text-orange-500 transition p-2 rounded-lg hover:bg-white/5">
                                    <v-icon icon="mdi-pencil-outline" size="18"></v-icon>
                                </button>
                                <button @click="deleteMenuItem(item.id)" class="text-white/40 hover:text-red-400 transition p-2 rounded-lg hover:bg-white/5">
                                    <v-icon icon="mdi-trash-can-outline" size="18"></v-icon>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Uncategorised items -->
                    <div v-if="menuItems.some(i => !i.category_id)" class="flex flex-col gap-3">
                        <p class="text-white/20 text-xs uppercase tracking-widest font-black border-b border-white/5 pb-2 italic">Menu Item</p>
                        <div v-for="item in menuItems.filter(i => !i.category_id)" :key="item.id"
                             class="bg-[#222] border border-white/10 rounded-xl p-4 flex items-center gap-4">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <p class="text-white font-black uppercase text-sm">{{ item.name }}</p>
                                    <span class="text-orange-500 font-bold text-sm">${{ Number(item.price).toFixed(2) }}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full font-bold uppercase tracking-wide border"
                                          :class="item.available ? 'text-green-400 bg-green-400/10 border-green-400/20' : 'text-white/30 bg-white/5 border-white/10'">
                                        {{ item.available ? 'Available' : 'Hidden' }}
                                    </span>
                                </div>
                                <p class="text-white/40 text-xs truncate">{{ item.description }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <button @click="toggleAvailability(item)" class="text-white/40 hover:text-orange-500 transition p-2 rounded-lg hover:bg-white/5" :title="item.available ? 'Hide item' : 'Show item'">
                                    <v-icon :icon="item.available ? 'mdi-eye-outline' : 'mdi-eye-off-outline'" size="18"></v-icon>
                                </button>
                                <button @click="openMenuModal(item)" class="text-white/40 hover:text-orange-500 transition p-2 rounded-lg hover:bg-white/5">
                                    <v-icon icon="mdi-pencil-outline" size="18"></v-icon>
                                </button>
                                <button @click="deleteMenuItem(item.id)" class="text-white/40 hover:text-red-400 transition p-2 rounded-lg hover:bg-white/5">
                                    <v-icon icon="mdi-trash-can-outline" size="18"></v-icon>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════ -->
                <!-- LOCATIONS                                              -->
                <!-- ══════════════════════════════════════════════════════ -->
                <div v-if="activeTab === 'locations'" class="flex flex-col gap-8">

                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-500 font-black uppercase tracking-widest text-xs mb-1">Manage</p>
                            <h3 class="text-white font-black uppercase text-3xl tracking-tight">Locations</h3>
                        </div>
                        <button @click="openLocationModal()"
                                class="bg-orange-500 hover:bg-orange-600 text-white font-black uppercase tracking-widest text-xs px-5 py-3 rounded-full flex items-center gap-2 transition">
                            <v-icon icon="mdi-plus" size="16"></v-icon> New Location
                        </button>
                    </div>

                    <!-- Location Library -->
                    <div class="bg-[#222] border border-white/10 rounded-2xl p-6 flex flex-col gap-4">
                        <p class="text-orange-500 font-black uppercase tracking-widest text-xs flex items-center gap-2">
                            <v-icon icon="mdi-map-marker-multiple-outline" size="14"></v-icon>
                            Saved Locations
                        </p>
                        <div v-if="!locations.length" class="text-white/30 text-sm">No locations saved yet.</div>
                        <div class="flex flex-col gap-2">
                            <div v-for="loc in locations" :key="loc.id"
                                 class="flex items-center gap-4 bg-white/5 border border-white/10 rounded-xl px-4 py-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-white font-bold text-sm">{{ loc.name }}</p>
                                    <p class="text-white/40 text-xs truncate">{{ loc.address }}</p>
                                    <p v-if="loc.notes" class="text-white/30 text-xs italic">{{ loc.notes }}</p>
                                </div>
                                <div class="flex items-center gap-1 flex-shrink-0">
                                    <button @click="setAsToday(loc)"
                                            class="text-white/40 hover:text-orange-500 transition px-3 py-2 rounded-lg hover:bg-white/5 text-xs font-bold uppercase tracking-widest">
                                        <v-icon icon="mdi-fire" size="14"></v-icon>
                                        Today
                                    </button>
                                    <button @click="openLocationModal(loc)" class="text-white/40 hover:text-orange-500 transition p-2 rounded-lg hover:bg-white/5">
                                        <v-icon icon="mdi-pencil-outline" size="16"></v-icon>
                                    </button>
                                    <button @click="deleteLocation(loc.id)" class="text-white/40 hover:text-red-400 transition p-2 rounded-lg hover:bg-white/5">
                                        <v-icon icon="mdi-trash-can-outline" size="16"></v-icon>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar Scheduler -->
                    <div class="bg-[#222] border border-white/10 rounded-2xl p-6 flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <p class="text-orange-500 font-black uppercase tracking-widest text-xs flex items-center gap-2">
                                <v-icon icon="mdi-calendar-month-outline" size="14"></v-icon>
                                Schedule
                            </p>
                            <div class="flex items-center gap-3">
                                <button @click="prevMonth()" class="text-white/40 hover:text-white transition p-1">
                                    <v-icon icon="mdi-chevron-left" size="20"></v-icon>
                                </button>
                                <span class="text-white font-black uppercase tracking-widest text-xs w-36 text-center">{{ calendarMonthLabel }}</span>
                                <button @click="nextMonth()" class="text-white/40 hover:text-white transition p-1">
                                    <v-icon icon="mdi-chevron-right" size="20"></v-icon>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-7 gap-1 mb-1">
                            <div v-for="d in ['Sun','Mon','Tue','Wed','Thu','Fri','Sat']" :key="d"
                                 class="text-center text-white/20 text-xs uppercase tracking-widest font-black py-1">
                                {{ d }}
                            </div>
                        </div>

                        <div class="grid grid-cols-7 gap-1">
                            <div v-for="(day, i) in calendarDays" :key="i"
                                 class="min-h-[72px] rounded-xl p-1.5 flex flex-col gap-1 transition"
                                 :class="day ? 'bg-white/5 border border-white/5 hover:border-white/15 cursor-pointer' : 'bg-transparent'"
                                 @click="day && openScheduleModal(day)">
                                <span v-if="day"
                                      class="text-xs font-black w-6 h-6 flex items-center justify-center rounded-full"
                                      :class="isToday(day) ? 'bg-orange-500 text-white' : 'text-white/40'">
                                    {{ day }}
                                </span>
                                <div v-for="sch in schedulesForDay(day)" :key="sch.id"
                                     class="flex items-center gap-1 bg-orange-500/20 border border-orange-500/30 rounded-md px-1.5 py-0.5 group cursor-pointer"
                                     @click.stop="openScheduleModal(null, sch)">
                                    <span class="text-orange-400 text-[10px] font-bold truncate flex-1 leading-tight">
                                        {{ sch.stand_location?.name ?? '?' }}
                                    </span>
                                    <div class="flex gap-0.5 opacity-0 group-hover:opacity-100 transition">
                                        <button @click.stop="openScheduleModal(null, sch)" class="text-orange-400/70 hover:text-orange-400 transition">
                                            <v-icon icon="mdi-pencil-outline" size="11"></v-icon>
                                        </button>
                                        <button @click.stop="deleteSchedule(sch.id)" class="text-orange-400/70 hover:text-red-400 transition">
                                            <v-icon icon="mdi-close" size="11"></v-icon>
                                        </button>
                                    </div>
                                    <span v-if="sch.is_current" class="w-1.5 h-1.5 rounded-full bg-orange-500 flex-shrink-0"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════ -->
                <!-- MESSAGES                                               -->
                <!-- ══════════════════════════════════════════════════════ -->
                <div v-if="activeTab === 'messages'" class="flex flex-col gap-6">
                    <div>
                        <p class="text-orange-500 font-black uppercase tracking-widest text-xs mb-1">Inbox</p>
                        <h3 class="text-white font-black uppercase text-3xl tracking-tight">Messages</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="md:col-span-2 flex flex-col gap-2">
                            <div v-if="!messages.length" class="text-white/30 text-sm">No messages yet.</div>
                            <div v-for="msg in messages" :key="msg.id"
                                 @click="openMessage(msg)"
                                 class="bg-[#222] border rounded-xl p-4 cursor-pointer transition flex items-start gap-3"
                                 :class="selectedMessage?.id === msg.id ? 'border-orange-500/50 bg-orange-500/5' : 'border-white/10 hover:border-white/20'">
                                <span class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0"
                                      :class="msg.read ? 'bg-white/20' : 'bg-orange-500'"></span>
                                <div class="min-w-0">
                                    <p class="text-white text-sm font-bold truncate">{{ msg.name }}</p>
                                    <p class="text-white/40 text-xs truncate">{{ msg.email }}</p>
                                    <p class="text-white/30 text-xs truncate mt-1">{{ msg.message }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="md:col-span-3">
                            <div v-if="!selectedMessage"
                                 class="bg-[#222] border border-white/10 rounded-2xl p-8 text-center text-white/30 text-sm h-full flex items-center justify-center">
                                Select a message to read it
                            </div>
                            <div v-else class="bg-[#222] border border-white/10 rounded-2xl p-6 flex flex-col gap-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-white font-black text-lg">{{ selectedMessage.name }}</p>
                                        <p class="text-white/40 text-sm">{{ selectedMessage.email }}</p>
                                        <p v-if="selectedMessage.phone" class="text-white/40 text-sm">{{ selectedMessage.phone }}</p>
                                    </div>
                                    <button @click="deleteMessage(selectedMessage.id)"
                                            class="text-white/30 hover:text-red-400 transition p-2 rounded-lg hover:bg-white/5 flex-shrink-0">
                                        <v-icon icon="mdi-trash-can-outline" size="18"></v-icon>
                                    </button>
                                </div>
                                <div class="border-t border-white/10 pt-4">
                                    <p class="text-white/70 text-sm leading-relaxed whitespace-pre-wrap">{{ selectedMessage.message }}</p>
                                </div>
                                <p v-if="selectedMessage.created_at" class="text-white/20 text-xs">
                                    Received {{ formatDate(selectedMessage.created_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════════════ -->
                <!-- BOOKINGS                                               -->
                <!-- ══════════════════════════════════════════════════════ -->
                <div v-if="activeTab === 'bookings'" class="flex flex-col gap-6">
                    <div>
                        <p class="text-orange-500 font-black uppercase tracking-widest text-xs mb-1">Event Requests</p>
                        <h3 class="text-white font-black uppercase text-3xl tracking-tight">Bookings</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="md:col-span-2 flex flex-col gap-2">
                            <div v-if="!bookings.length" class="text-white/30 text-sm">No booking requests yet.</div>
                            <div v-for="bk in bookings" :key="bk.id"
                                 @click="openBooking(bk)"
                                 class="bg-[#222] border rounded-xl p-4 cursor-pointer transition"
                                 :class="selectedBooking?.id === bk.id ? 'border-orange-500/50 bg-orange-500/5' : 'border-white/10 hover:border-white/20'">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                                          :class="bk.read ? 'bg-white/20' : 'bg-orange-500'"></span>
                                    <p class="text-white text-sm font-bold truncate flex-1">{{ bk.name }}</p>
                                    <span class="text-xs px-2 py-0.5 rounded-full font-bold uppercase tracking-wide border"
                                          :class="statusColor(bk.status)">
                                        {{ bk.status }}
                                    </span>
                                </div>
                                <p class="text-white/40 text-xs truncate pl-4">{{ bk.event_type }}</p>
                                <p class="text-white/30 text-xs pl-4">{{ formatDate(bk.event_date) }}</p>
                            </div>
                        </div>

                        <div class="md:col-span-3">
                            <div v-if="!selectedBooking"
                                 class="bg-[#222] border border-white/10 rounded-2xl p-8 text-center text-white/30 text-sm h-full flex items-center justify-center">
                                Select a booking to review it
                            </div>
                            <div v-else class="bg-[#222] border border-white/10 rounded-2xl p-6 flex flex-col gap-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-white font-black text-lg">{{ selectedBooking.name }}</p>
                                        <p class="text-white/40 text-sm">{{ selectedBooking.email }}</p>
                                        <p v-if="selectedBooking.phone" class="text-white/40 text-sm">{{ selectedBooking.phone }}</p>
                                    </div>
                                    <button @click="deleteBooking(selectedBooking.id)"
                                            class="text-white/30 hover:text-red-400 transition p-2 rounded-lg hover:bg-white/5 flex-shrink-0">
                                        <v-icon icon="mdi-trash-can-outline" size="18"></v-icon>
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div class="bg-white/5 rounded-xl p-3">
                                        <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Event Type</p>
                                        <p class="text-white font-bold">{{ selectedBooking.event_type ?? '—' }}</p>
                                    </div>
                                    <div class="bg-white/5 rounded-xl p-3">
                                        <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Date</p>
                                        <p class="text-white font-bold">{{ formatDate(selectedBooking.event_date) }}</p>
                                    </div>
                                    <div class="bg-white/5 rounded-xl p-3">
                                        <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Expected Guests</p>
                                        <p class="text-white font-bold">{{ selectedBooking.expected_guests ?? '—' }}</p>
                                    </div>
                                    <div class="bg-white/5 rounded-xl p-3">
                                        <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Location</p>
                                        <p class="text-white font-bold">{{ selectedBooking.location ?? '—' }}</p>
                                    </div>
                                    <div class="bg-white/5 rounded-xl p-3">
                                        <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Status</p>
                                        <p class="font-bold capitalize" :class="statusColor(selectedBooking.status).split(' ')[0]">
                                            {{ selectedBooking.status }}
                                        </p>
                                    </div>
                                </div>

                                <div v-if="selectedBooking.details" class="border-t border-white/10 pt-3">
                                    <p class="text-white/40 text-xs uppercase tracking-widest mb-1">Details</p>
                                    <p class="text-white/70 text-sm leading-relaxed">{{ selectedBooking.details }}</p>
                                </div>

                                <div v-if="selectedBooking.status === 'pending'" class="flex gap-3 pt-2">
                                    <button @click="updateBookingStatus(selectedBooking.id, 'approved')"
                                            class="flex-1 bg-green-500/20 border border-green-500/30 text-green-400 font-black uppercase tracking-widest text-xs py-3 rounded-xl hover:bg-green-500/30 transition flex items-center justify-center gap-2">
                                        <v-icon icon="mdi-check" size="16"></v-icon> Approve
                                    </button>
                                    <button @click="updateBookingStatus(selectedBooking.id, 'declined')"
                                            class="flex-1 bg-red-500/10 border border-red-500/20 text-red-400 font-black uppercase tracking-widest text-xs py-3 rounded-xl hover:bg-red-500/20 transition flex items-center justify-center gap-2">
                                        <v-icon icon="mdi-close" size="16"></v-icon> Decline
                                    </button>
                                </div>
                                <div v-else class="pt-2">
                                    <button @click="updateBookingStatus(selectedBooking.id, 'pending')"
                                            class="text-white/30 hover:text-white/60 text-xs uppercase tracking-widest font-bold transition">
                                        Reset to pending
                                    </button>
                                </div>

                                <p v-if="selectedBooking.created_at" class="text-white/20 text-xs">
                                    Submitted {{ formatDate(selectedBooking.created_at) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- CATEGORY MODAL                                                 -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <Teleport to="body">
            <div v-if="showCategoryModal"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
                 @click.self="showCategoryModal = false">
                <div class="bg-[#1a1a1a] border border-white/10 rounded-2xl p-6 w-full max-w-sm flex flex-col gap-5">
                    <div class="flex items-center justify-between">
                        <p class="text-white font-black uppercase text-lg tracking-wider">
                            {{ editingCategory ? 'Rename Category' : 'New Category' }}
                        </p>
                        <button @click="showCategoryModal = false" class="text-white/40 hover:text-white transition">
                            <v-icon icon="mdi-close" size="20"></v-icon>
                        </button>
                    </div>
                    <div class="flex flex-col gap-1">
                        <label class="text-white/50 text-xs uppercase tracking-widest font-bold">Category Name *</label>
                        <input v-model="categoryName" type="text" placeholder="e.g. Loaded Dogs"
                               class="bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-orange-500/60 placeholder:text-white/20"
                               @keyup.enter="saveCategory" />
                        <span v-if="categoryError" class="text-red-400 text-xs">{{ categoryError }}</span>
                    </div>
                    <div class="flex gap-3">
                        <button @click="showCategoryModal = false"
                                class="flex-1 border border-white/10 text-white/50 font-black uppercase tracking-widest text-xs py-3 rounded-xl hover:bg-white/5 transition">
                            Cancel
                        </button>
                        <button @click="saveCategory" :disabled="categoryProcessing"
                                class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-black uppercase tracking-widest text-xs py-3 rounded-xl transition disabled:opacity-50">
                            {{ categoryProcessing ? 'Saving…' : editingCategory ? 'Rename' : 'Create' }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- LOCATION MODAL                                                 -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <Teleport to="body">
            <div v-if="showLocationModal"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
                 @click.self="showLocationModal = false">
                <div class="bg-[#1a1a1a] border border-white/10 rounded-2xl p-6 w-full max-w-sm flex flex-col gap-5">
                    <div class="flex items-center justify-between">
                        <p class="text-white font-black uppercase text-lg tracking-wider">
                            {{ editingLocation ? 'Edit Location' : 'New Location' }}
                        </p>
                        <button @click="showLocationModal = false" class="text-white/40 hover:text-white transition">
                            <v-icon icon="mdi-close" size="20"></v-icon>
                        </button>
                    </div>
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-white/50 text-xs uppercase tracking-widest font-bold">Name *</label>
                            <input v-model="locationForm.name" type="text" placeholder="e.g. Downtown Farmers Market"
                                   class="bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-orange-500/60 placeholder:text-white/20" />
                            <span v-if="locationForm.errors.name" class="text-red-400 text-xs">{{ locationForm.errors.name }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-white/50 text-xs uppercase tracking-widest font-bold">Address *</label>
                            <input v-model="locationForm.address" type="text" placeholder="123 Main St, Kingsport, TN"
                                   class="bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-orange-500/60 placeholder:text-white/20" />
                            <span v-if="locationForm.errors.address" class="text-red-400 text-xs">{{ locationForm.errors.address }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-white/50 text-xs uppercase tracking-widest font-bold">Notes (optional)</label>
                            <input v-model="locationForm.notes" type="text" placeholder="Park near the fountain"
                                   class="bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-orange-500/60 placeholder:text-white/20" />
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button @click="showLocationModal = false"
                                class="flex-1 border border-white/10 text-white/50 font-black uppercase tracking-widest text-xs py-3 rounded-xl hover:bg-white/5 transition">
                            Cancel
                        </button>
                        <button @click="saveLocation" :disabled="locationForm.processing"
                                class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-black uppercase tracking-widest text-xs py-3 rounded-xl transition disabled:opacity-50">
                            {{ locationForm.processing ? 'Saving…' : editingLocation ? 'Save Changes' : 'Create' }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- SCHEDULE MODAL                                                 -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <Teleport to="body">
            <div v-if="showScheduleModal"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
                 @click.self="showScheduleModal = false">
                <div class="bg-[#1a1a1a] border border-white/10 rounded-2xl p-6 w-full max-w-sm flex flex-col gap-5">
                    <div class="flex items-center justify-between">
                        <p class="text-white font-black uppercase text-lg tracking-wider">
                            {{ editingSchedule ? 'Edit Appearance' : 'Schedule Appearance' }}
                        </p>
                        <button @click="showScheduleModal = false" class="text-white/40 hover:text-white transition">
                            <v-icon icon="mdi-close" size="20"></v-icon>
                        </button>
                    </div>
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-white/50 text-xs uppercase tracking-widest font-bold">Location *</label>
                            <select v-model="scheduleForm.stand_location_id"
                                    class="bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-orange-500/60">
                                <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
                            </select>
                            <span v-if="scheduleForm.errors.stand_location_id" class="text-red-400 text-xs">{{ scheduleForm.errors.stand_location_id }}</span>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-white/50 text-xs uppercase tracking-widest font-bold">Date *</label>
                            <input v-model="scheduleForm.date" type="date"
                                   class="bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-orange-500/60" />
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex flex-col gap-1">
                                <label class="text-white/50 text-xs uppercase tracking-widest font-bold">Opens</label>
                                <input v-model="scheduleForm.open_time" type="time"
                                       class="bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-orange-500/60" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-white/50 text-xs uppercase tracking-widest font-bold">Closes</label>
                                <input v-model="scheduleForm.close_time" type="time"
                                       :disabled="scheduleForm.until_sold_out"
                                       class="bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-orange-500/60 disabled:opacity-30" />
                            </div>
                        </div>
                        <label class="flex items-center gap-3 cursor-pointer select-none">
                            <div class="relative w-10 h-5 flex-shrink-0">
                                <input type="checkbox" v-model="scheduleForm.until_sold_out" class="peer sr-only" />
                                <div class="absolute inset-0 bg-white/10 rounded-full transition-colors peer-checked:bg-orange-500"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <span class="text-white/60 text-sm font-bold">Until Sold Out</span>
                        </label>
                    </div>
                    <div class="flex gap-3">
                        <button @click="showScheduleModal = false"
                                class="flex-1 border border-white/10 text-white/50 font-black uppercase tracking-widest text-xs py-3 rounded-xl hover:bg-white/5 transition">
                            Cancel
                        </button>
                        <button @click="saveSchedule" :disabled="scheduleForm.processing"
                                class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-black uppercase tracking-widest text-xs py-3 rounded-xl transition disabled:opacity-50">
                            {{ scheduleForm.processing ? 'Saving…' : editingSchedule ? 'Save Changes' : 'Schedule' }}
                        </button>
                    </div>
                    <button v-if="editingSchedule"
                            @click="deleteScheduleFromModal(editingSchedule.id)"
                            class="w-full text-red-400/60 hover:text-red-400 text-xs uppercase tracking-widest font-black transition text-center pt-1">
                        <v-icon icon="mdi-trash-can-outline" size="14"></v-icon>
                        Delete
                    </button>
                </div>
            </div>
        </Teleport>

        <!-- ══════════════════════════════════════════════════════════════ -->
        <!-- MENU MODAL                                                     -->
        <!-- ══════════════════════════════════════════════════════════════ -->
        <Teleport to="body">
            <div v-if="showMenuModal"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
                 @click.self="showMenuModal = false">
                <div class="bg-[#1a1a1a] border border-white/10 rounded-2xl p-6 w-full max-w-md flex flex-col gap-5">
                    <div class="flex items-center justify-between">
                        <p class="text-white font-black uppercase text-lg tracking-wider">
                            {{ editingMenuItem ? 'Edit Item' : 'Add Menu Item' }}
                        </p>
                        <button @click="showMenuModal = false" class="text-white/40 hover:text-white transition">
                            <v-icon icon="mdi-close" size="20"></v-icon>
                        </button>
                    </div>
                    <div class="flex flex-col gap-4">
                        <div class="flex flex-col gap-1">
                            <label class="text-white/50 text-xs uppercase tracking-widest font-bold">Name *</label>
                            <input v-model="menuForm.name" type="text" placeholder="e.g. The Fire Dog"
                                   class="bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-orange-500/60 placeholder:text-white/20" />
                            <span v-if="menuForm.errors.name" class="text-red-400 text-xs">{{ menuForm.errors.name }}</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="flex flex-col gap-1">
                                <label class="text-white/50 text-xs uppercase tracking-widest font-bold">Category</label>
                                <select v-model="menuForm.category_id"
                                        class="bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-orange-500/60">
                                    <option :value="null">— None —</option>
                                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                </select>
                            </div>
                            <div class="flex flex-col gap-1">
                                <label class="text-white/50 text-xs uppercase tracking-widest font-bold">Price *</label>
                                <input v-model="menuForm.price" type="number" step="0.01" placeholder="0.00"
                                       class="bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-orange-500/60 placeholder:text-white/20" />
                                <span v-if="menuForm.errors.price" class="text-red-400 text-xs">{{ menuForm.errors.price }}</span>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1">
                            <label class="text-white/50 text-xs uppercase tracking-widest font-bold">Description</label>
                            <textarea v-model="menuForm.description" rows="3" placeholder="What makes it special?"
                                      class="bg-[#222] border border-white/10 rounded-xl px-4 py-3 text-white text-sm focus:outline-none focus:border-orange-500/60 placeholder:text-white/20 resize-none"></textarea>
                        </div>
                        <label class="flex items-center gap-3 cursor-pointer select-none">
                            <div class="relative w-10 h-5 flex-shrink-0">
                                <input type="checkbox" v-model="menuForm.available" class="peer sr-only" />
                                <div class="absolute inset-0 bg-white/10 rounded-full transition-colors peer-checked:bg-orange-500"></div>
                                <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform peer-checked:translate-x-5"></div>
                            </div>
                            <span class="text-white/60 text-sm font-bold">Available on menu</span>
                        </label>
                    </div>
                    <div class="flex gap-3">
                        <button @click="showMenuModal = false"
                                class="flex-1 border border-white/10 text-white/50 font-black uppercase tracking-widest text-xs py-3 rounded-xl hover:bg-white/5 transition">
                            Cancel
                        </button>
                        <button @click="saveMenuItem" :disabled="menuForm.processing"
                                class="flex-1 bg-orange-500 hover:bg-orange-600 text-white font-black uppercase tracking-widest text-xs py-3 rounded-xl transition disabled:opacity-50">
                            {{ menuForm.processing ? 'Saving…' : editingMenuItem ? 'Save Changes' : 'Add Item' }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

    </AuthenticatedLayout>
</template>
