<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    leaves: Array,
});

const form = useForm({
    status: ''
});

const updateStatus = (id, newStatus) => {
    form.status = newStatus;
    form.patch(route('leaves.updateStatus', id), {
        preserveScroll: true
    });
};
</script>

<template>
    <Head title="Leave Approvals" />

    <AuthenticatedLayout>
        
        <!-- Premium Header Section -->
        <div class="relative bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800 pb-32 pt-12">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-6 md:mb-0">
                    <h2 class="text-4xl font-extrabold text-white tracking-tight drop-shadow-lg">
                        Leave Approvals
                    </h2>
                    <p class="mt-2 text-indigo-200 text-lg max-w-xl font-medium">
                        Review, approve, or reject team leave requests efficiently.
                    </p>
                </div>
            </div>
        </div>

        <div class="-mt-24 pb-12 relative z-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                
                <div class="bg-white/95 backdrop-blur-xl shadow-2xl sm:rounded-3xl border border-white/40 overflow-hidden">
                    <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">Pending & Processed Requests</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/80 text-xs uppercase font-black tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-8 py-5">Employee</th>
                                    <th class="px-8 py-5">Dates</th>
                                    <th class="px-8 py-5">Reason</th>
                                    <th class="px-8 py-5">Status</th>
                                    <th class="px-8 py-5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="leave in leaves" :key="leave.id" class="hover:bg-gray-50/80 transition-colors group">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-700 font-bold flex items-center justify-center mr-3 shadow-sm border border-indigo-200">
                                                {{ leave.employee_name.charAt(0) }}
                                            </div>
                                            <span class="font-bold text-gray-900">{{ leave.employee_name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-sm font-medium text-gray-600 whitespace-nowrap">{{ leave.start_date }} <br>to {{ leave.end_date }}</td>
                                    <td class="px-8 py-5 text-sm text-gray-500 max-w-[200px] truncate" :title="leave.reason">{{ leave.reason }}</td>
                                    <td class="px-8 py-5">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase border"
                                              :class="{
                                                  'bg-amber-50 text-amber-700 border-amber-200': leave.status === 'pending',
                                                  'bg-green-50 text-green-700 border-green-200': leave.status === 'approved',
                                                  'bg-red-50 text-red-700 border-red-200': leave.status === 'rejected'
                                              }">
                                            <svg v-if="leave.status === 'pending'" class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <svg v-if="leave.status === 'approved'" class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            <svg v-if="leave.status === 'rejected'" class="w-3 h-3 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            {{ leave.status }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-right space-x-2 whitespace-nowrap">
                                        <template v-if="leave.status === 'pending'">
                                            <button @click="updateStatus(leave.id, 'approved')" :disabled="form.processing" class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 font-bold transition-colors disabled:opacity-50">Approve</button>
                                            <button @click="updateStatus(leave.id, 'rejected')" :disabled="form.processing" class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 font-bold transition-colors disabled:opacity-50">Reject</button>
                                        </template>
                                        <template v-else>
                                            <span class="text-gray-400 text-sm font-semibold italic bg-gray-50 px-3 py-1.5 rounded-lg">Processed</span>
                                        </template>
                                    </td>
                                </tr>
                                <tr v-if="leaves.length === 0">
                                    <td colspan="5" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <p class="text-lg font-bold text-gray-900">All Caught Up!</p>
                                            <p class="mt-1 text-sm text-gray-500">There are no pending leave requests to review.</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
