<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    balance: Object,
    history: Array
});

const calculatePercentage = (balance, allocated) => {
    if (allocated === 0) return 0;
    return Math.round((balance / allocated) * 100);
};

</script>

<template>
    <Head title="My Leaves" />

    <AuthenticatedLayout>
        
        <!-- Premium Header Section -->
        <div class="relative bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800 pb-32 pt-12">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-6 md:mb-0">
                    <h2 class="text-4xl font-extrabold text-white tracking-tight drop-shadow-lg">
                        My Leave Dashboard
                    </h2>
                    <p class="mt-2 text-indigo-200 text-lg max-w-xl font-medium">
                        Track your time off, monitor balances, and request new leaves seamlessly.
                    </p>
                </div>
                <div class="flex-shrink-0">
                    <Link :href="route('leaves.create')" class="group relative inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-indigo-900 bg-white border border-transparent rounded-full overflow-hidden shadow-2xl transition-all duration-300 hover:scale-105 hover:shadow-indigo-500/50">
                        <span class="absolute inset-0 w-full h-full -mt-1 rounded-lg opacity-30 bg-gradient-to-b from-transparent via-transparent to-black"></span>
                        <svg class="w-5 h-5 mr-2 -ml-1 text-indigo-600 transition-transform duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Apply for Leave
                    </Link>
                </div>
            </div>
        </div>

        <div class="-mt-24 pb-12 relative z-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- Leave Balances Card -->
                <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                    <div class="bg-white/95 backdrop-blur-xl rounded-3xl p-8 shadow-xl border border-white/40 flex flex-col justify-between transition-transform hover:-translate-y-1 duration-300 max-w-2xl mx-auto w-full">
                        <div class="flex justify-between items-start mb-4">
                            <div class="p-3 bg-indigo-50 rounded-2xl">
                                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <span class="px-3 py-1 bg-green-50 text-green-700 text-sm font-bold rounded-full border border-green-200">
                                Annual Leave
                            </span>
                        </div>
                        
                        <div>
                            <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Available Balance</p>
                            <div class="flex items-baseline">
                                <span class="text-5xl font-black text-gray-900">{{ balance.balance }}</span>
                                <span class="ml-2 text-xl font-medium text-gray-400">/ {{ balance.allocated }} Days</span>
                            </div>
                        </div>

                        <div class="mt-8">
                            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 h-3 rounded-full" :style="{ width: calculatePercentage(balance.balance, balance.allocated) + '%' }"></div>
                            </div>
                            <p class="mt-2 text-sm font-medium text-gray-400 text-right">{{ calculatePercentage(balance.balance, balance.allocated) }}% remaining</p>
                        </div>
                    </div>
                </div>

                <!-- History Table -->
                <div class="bg-white/95 backdrop-blur-xl shadow-2xl sm:rounded-3xl border border-white/40 overflow-hidden">
                    <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">Leave History</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/80 text-xs uppercase font-black tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-8 py-5">Applied On</th>
                                    <th class="px-8 py-5">Duration</th>
                                    <th class="px-8 py-5">Reason</th>
                                    <th class="px-8 py-5">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="leave in history" :key="leave.id" class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-8 py-5 text-sm font-medium text-gray-900">{{ new Date(leave.created_at).toLocaleDateString() }}</td>
                                    <td class="px-8 py-5 text-sm font-medium text-gray-600 whitespace-nowrap">{{ leave.start_date }} to {{ leave.end_date }}</td>
                                    <td class="px-8 py-5 text-sm text-gray-500 max-w-xs truncate">{{ leave.reason }}</td>
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
                                </tr>
                                <tr v-if="history.length === 0">
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                            </div>
                                            <p class="text-lg font-bold text-gray-900">No Leave History</p>
                                            <p class="mt-1 text-sm text-gray-500">You haven't requested any time off yet.</p>
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
