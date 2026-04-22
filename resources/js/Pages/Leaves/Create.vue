<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    availableBalance: Number,
});

const form = useForm({
    start_date: '',
    end_date: '',
    reason: '',
});

// Calculate requested days for visual feedback
const requestedDays = computed(() => {
    if (form.start_date && form.end_date) {
        const start = new Date(form.start_date);
        const end = new Date(form.end_date);
        const diffTime = end - start;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        return diffDays > 0 ? diffDays : 0;
    }
    return 0;
});

const submit = () => {
    form.post(route('leaves.store'));
};
</script>

<template>
    <Head title="Apply for Leave" />

    <AuthenticatedLayout>
        <!-- Premium Header Section -->
        <div class="relative bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800 pb-32 pt-12">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-6 md:mb-0 flex items-center space-x-4">
                    <Link :href="route('leaves.index')" class="text-indigo-200 hover:text-white transition-colors bg-white/10 p-2 rounded-full backdrop-blur-md" title="Go Back">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </Link>
                    <div>
                        <h2 class="text-4xl font-extrabold text-white tracking-tight drop-shadow-lg">
                            Request Time Off
                        </h2>
                        <p class="mt-2 text-indigo-200 text-lg max-w-xl font-medium">
                            Submit a new leave application to your manager.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="-mt-24 pb-12 relative z-10">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white/95 backdrop-blur-xl shadow-2xl sm:rounded-3xl border border-white/40 overflow-hidden">
                    <form @submit.prevent="submit" class="p-8 space-y-6">
                        
                        <!-- Error Message Alert -->
                        <div v-if="form.errors.start_date && form.errors.start_date.includes('Insufficient')" class="bg-red-50 text-red-600 p-4 rounded-lg font-semibold text-sm">
                            {{ form.errors.start_date }}
                        </div>

                        <!-- Available Balance Alert -->
                        <div class="bg-indigo-50 text-indigo-700 p-4 rounded-lg text-sm border border-indigo-100 flex items-center justify-between">
                            <span>You have <strong>{{ availableBalance }}</strong> annual leave days available.</span>
                        </div>

                        <!-- Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Start Date</label>
                                <input v-model="form.start_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500" required />
                                <span class="text-red-500 text-sm" v-if="form.errors.start_date">{{ form.errors.start_date }}</span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">End Date</label>
                                <input v-model="form.end_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500" required />
                                <span class="text-red-500 text-sm" v-if="form.errors.end_date">{{ form.errors.end_date }}</span>
                            </div>
                        </div>

                        <div v-if="requestedDays > 0" class="bg-blue-50 text-blue-700 p-4 rounded-lg text-sm border border-blue-100">
                            You are requesting exactly <strong>{{ requestedDays }}</strong> day(s) of leave.
                        </div>

                        <!-- Reason -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reason for Leave</label>
                            <textarea v-model="form.reason" rows="4" placeholder="Briefly describe why you are taking leave..." class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-blue-500" required></textarea>
                            <span class="text-red-500 text-sm" v-if="form.errors.reason">{{ form.errors.reason }}</span>
                        </div>

                        <div class="pt-6 border-t border-gray-100 flex justify-end">
                            <button type="submit" :disabled="form.processing" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 disabled:opacity-50">
                                Submit Request &rarr;
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
