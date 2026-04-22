<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    holidays: Array,
    canManage: Boolean,
});

const form = useForm({
    id: null,
    name: '',
    date: '',
    type: 'National',
    description: '',
});

const isModalOpen = ref(false);
const isEditing = ref(false);

const openCreateModal = () => {
    isEditing.value = false;
    form.reset();
    form.clearErrors();
    isModalOpen.value = true;
};

const openEditModal = (holiday) => {
    isEditing.value = true;
    form.id = holiday.id;
    form.name = holiday.name;
    form.date = holiday.date;
    form.type = holiday.type;
    form.description = holiday.description || '';
    form.clearErrors();
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('holidays.update', form.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('holidays.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteHoliday = (id) => {
    if (confirm('Are you sure you want to delete this holiday?')) {
        const deleteForm = useForm({});
        deleteForm.delete(route('holidays.destroy', id));
    }
};

const formatFullDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric', weekday: 'long' };
    return new Date(dateString).toLocaleDateString('en-US', options);
};

const getMonth = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', { month: 'short' });
};

const getDay = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', { day: '2-digit' });
};

// Group holidays by month
const groupedHolidays = computed(() => {
    const groups = {};
    props.holidays.forEach(holiday => {
        const monthYear = new Date(holiday.date).toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
        if (!groups[monthYear]) {
            groups[monthYear] = [];
        }
        groups[monthYear].push(holiday);
    });
    return groups;
});
</script>

<template>
    <Head title="Holiday Calendar" />

    <AuthenticatedLayout>
        
        <!-- Premium Header Section -->
        <div class="relative bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800 pb-32 pt-12">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-6 md:mb-0">
                    <h2 class="text-4xl font-extrabold text-white tracking-tight drop-shadow-lg">
                        Company Calendar
                    </h2>
                    <p class="mt-2 text-indigo-200 text-lg max-w-xl font-medium">
                        Explore upcoming national, regional, and optional holidays. Plan your time off efficiently.
                    </p>
                </div>
                <div v-if="canManage" class="flex-shrink-0">
                    <button @click="openCreateModal" class="group relative inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-indigo-900 bg-white border border-transparent rounded-full overflow-hidden shadow-2xl transition-all duration-300 hover:scale-105 hover:shadow-indigo-500/50">
                        <span class="absolute inset-0 w-full h-full -mt-1 rounded-lg opacity-30 bg-gradient-to-b from-transparent via-transparent to-black"></span>
                        <svg class="w-5 h-5 mr-2 -ml-1 text-indigo-600 transition-transform duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Holiday
                    </button>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="-mt-24 pb-12 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div v-if="holidays.length === 0" class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-16 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-indigo-50 mb-6 shadow-inner">
                        <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">No Holidays Scheduled</h3>
                    <p class="mt-2 text-gray-500 max-w-md mx-auto text-lg">The calendar is completely clear. Get started by adding upcoming holidays for the team.</p>
                </div>

                <div v-else class="space-y-12">
                    <!-- Grouped by Month -->
                    <div v-for="(monthHolidays, monthName) in groupedHolidays" :key="monthName" class="animate-fade-in-up">
                        
                        <div class="flex items-center mb-6">
                            <h3 class="text-2xl font-black text-gray-800 dark:text-white tracking-tight">{{ monthName }}</h3>
                            <div class="ml-4 flex-grow h-px bg-gradient-to-r from-gray-200 to-transparent dark:from-gray-700"></div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="holiday in monthHolidays" :key="holiday.id" 
                                class="group relative bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-sm hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden flex flex-col h-full">
                                
                                <!-- Decorative background blob -->
                                <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full opacity-10 transition-transform duration-500 group-hover:scale-150 group-hover:opacity-20"
                                     :class="{
                                         'bg-purple-600': holiday.type === 'National',
                                         'bg-amber-500': holiday.type === 'Optional',
                                         'bg-blue-500': holiday.type === 'Regional'
                                     }">
                                </div>

                                <div class="flex items-start justify-between relative z-10 mb-4">
                                    <div class="flex flex-col items-center justify-center w-16 h-16 rounded-xl text-white font-bold shadow-lg"
                                         :class="{
                                             'bg-gradient-to-br from-purple-500 to-indigo-600 shadow-purple-500/30': holiday.type === 'National',
                                             'bg-gradient-to-br from-amber-400 to-orange-500 shadow-amber-500/30': holiday.type === 'Optional',
                                             'bg-gradient-to-br from-blue-400 to-cyan-500 shadow-blue-500/30': holiday.type === 'Regional'
                                         }">
                                        <span class="text-xs uppercase tracking-wider opacity-90">{{ getMonth(holiday.date) }}</span>
                                        <span class="text-2xl leading-none mt-0.5">{{ getDay(holiday.date) }}</span>
                                    </div>
                                    
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase"
                                          :class="{
                                              'bg-purple-50 text-purple-700 ring-1 ring-purple-600/20': holiday.type === 'National',
                                              'bg-amber-50 text-amber-700 ring-1 ring-amber-600/20': holiday.type === 'Optional',
                                              'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20': holiday.type === 'Regional'
                                          }">
                                        {{ holiday.type }}
                                    </span>
                                </div>

                                <h4 class="text-xl font-bold text-gray-900 dark:text-white mb-2 relative z-10">{{ holiday.name }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 flex-grow relative z-10 line-clamp-2">
                                    {{ holiday.description || 'No additional details provided.' }}
                                </p>

                                <div class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center relative z-10">
                                    <div class="text-xs font-medium text-gray-400">
                                        {{ formatFullDate(holiday.date) }}
                                    </div>
                                    
                                    <div v-if="canManage" class="flex space-x-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <button @click="openEditModal(holiday)" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                        </button>
                                        <button @click="deleteHoliday(holiday.id)" class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors" title="Delete">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Glassmorphism Modal -->
                <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6 sm:px-0">
                    <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" @click="closeModal"></div>

                    <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden transform transition-all">
                        <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-750 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="text-2xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                                {{ isEditing ? 'Update Holiday' : 'Create Holiday' }}
                            </h3>
                            <button @click="closeModal" class="p-2 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors focus:outline-none">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        
                        <form @submit.prevent="submit" class="p-8 space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Holiday Name</label>
                                <input v-model="form.name" type="text" class="block w-full rounded-xl border-gray-200 bg-gray-50 dark:bg-gray-900 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow shadow-sm" placeholder="e.g. Independence Day" required />
                                <span class="text-red-500 text-sm mt-1 block font-medium" v-if="form.errors.name">{{ form.errors.name }}</span>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Date</label>
                                    <input v-model="form.date" type="date" class="block w-full rounded-xl border-gray-200 bg-gray-50 dark:bg-gray-900 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow shadow-sm" required />
                                    <span class="text-red-500 text-sm mt-1 block font-medium" v-if="form.errors.date">{{ form.errors.date }}</span>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Classification</label>
                                    <select v-model="form.type" class="block w-full rounded-xl border-gray-200 bg-gray-50 dark:bg-gray-900 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow shadow-sm">
                                        <option value="National">National</option>
                                        <option value="Optional">Optional</option>
                                        <option value="Regional">Regional</option>
                                    </select>
                                    <span class="text-red-500 text-sm mt-1 block font-medium" v-if="form.errors.type">{{ form.errors.type }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Additional Details</label>
                                <textarea v-model="form.description" rows="3" class="block w-full rounded-xl border-gray-200 bg-gray-50 dark:bg-gray-900 dark:border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-shadow shadow-sm resize-none" placeholder="Provide any additional context or rules for this holiday..."></textarea>
                                <span class="text-red-500 text-sm mt-1 block font-medium" v-if="form.errors.description">{{ form.errors.description }}</span>
                            </div>

                            <div class="pt-6 flex justify-end space-x-4 border-t border-gray-100 dark:border-gray-700">
                                <button type="button" @click="closeModal" class="px-6 py-3 rounded-xl text-sm font-bold text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" :disabled="form.processing" class="px-8 py-3 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 disabled:opacity-50 disabled:hover:translate-y-0">
                                    {{ isEditing ? 'Save Changes' : 'Confirm & Add' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up {
    animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
