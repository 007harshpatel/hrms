<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    salary_structure: Object,
    payslips: Array,
});
</script>

<template>
    <Head title="My Compensation" />

    <AuthenticatedLayout>
        <!-- Premium Header Section -->
        <div class="relative bg-gradient-to-br from-emerald-900 via-teal-900 to-emerald-800 pb-32 pt-12">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-6 md:mb-0">
                    <h2 class="text-4xl font-extrabold text-white tracking-tight drop-shadow-lg">
                        My Compensation & Payslips
                    </h2>
                    <p class="mt-2 text-emerald-100 text-lg max-w-xl font-medium">
                        View your current salary package and access historical payment records securely.
                    </p>
                </div>
            </div>
        </div>

        <div class="-mt-24 pb-12 relative z-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- Fixed Salary Package Visualization -->
                <div class="bg-white/95 backdrop-blur-xl shadow-2xl sm:rounded-3xl border border-white/40 overflow-hidden p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">Current Compensation Rate</h3>
                        <div class="p-3 bg-emerald-50 rounded-2xl">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                    
                    <div v-if="salary_structure" class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                            <p class="text-xs text-emerald-600 uppercase tracking-widest font-bold">Base Salary</p>
                            <p class="text-3xl font-black text-emerald-900 mt-2">${{ salary_structure.base_salary }}</p>
                        </div>
                        <div class="bg-gray-50 border border-gray-100 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                            <p class="text-xs text-gray-500 uppercase tracking-widest font-bold">HRA Bracket</p>
                            <p class="text-3xl font-black text-gray-900 mt-2">{{ salary_structure.hra_percentage }}%</p>
                        </div>
                        <div class="bg-blue-50 border border-blue-100 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow">
                            <p class="text-xs text-blue-600 uppercase tracking-widest font-bold">Fixed Allowances</p>
                            <p class="text-3xl font-black text-blue-900 mt-2">+${{ salary_structure.allowances }}</p>
                        </div>
                        <div class="bg-red-50 border border-red-100 p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                            <div class="absolute right-0 top-0 w-16 h-16 bg-red-100 rounded-bl-full opacity-50"></div>
                            <p class="text-xs text-red-600 uppercase tracking-widest font-bold">Fixed Deductions</p>
                            <p class="text-3xl font-black text-red-900 mt-2 relative z-10">-${{ salary_structure.deductions }}</p>
                        </div>
                    </div>
                    
                    <div v-else class="py-16 text-center text-gray-500 bg-gray-50 rounded-2xl border border-dashed border-gray-300">
                        <div class="flex justify-center mb-4">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <p class="text-lg font-bold text-gray-900">No formal compensation structure assigned yet.</p>
                        <p class="text-sm mt-2">Please contact HR to have your salary package configured.</p>
                    </div>
                </div>

                <!-- Personal Payslip Ledger -->
                <div class="bg-white/95 backdrop-blur-xl shadow-2xl sm:rounded-3xl border border-white/40 overflow-hidden">
                    <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">Historical Payslips</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/80 text-xs uppercase font-black tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-8 py-5">Period</th>
                                    <th class="px-8 py-5">Basic</th>
                                    <th class="px-8 py-5">+ HRA</th>
                                    <th class="px-8 py-5 text-red-500">- Deductions</th>
                                    <th class="px-8 py-5 text-emerald-600">= Net Disbursed</th>
                                    <th class="px-8 py-5">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="slip in payslips" :key="'ps-'+slip.id" class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-8 py-6 font-bold text-gray-900">{{ slip.period }}</td>
                                    <td class="px-8 py-6 font-medium text-gray-600">${{ slip.basic_pay }}</td>
                                    <td class="px-8 py-6 font-medium text-gray-600">${{ slip.hra }}</td>
                                    <td class="px-8 py-6 font-medium text-red-500">${{ slip.deductions }}</td>
                                    <td class="px-8 py-6 font-black text-emerald-600 text-lg">${{ slip.net_pay }}</td>
                                    <td class="px-8 py-6">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase border"
                                              :class="{
                                                  'bg-amber-50 text-amber-700 border-amber-200': slip.status === 'generated',
                                                  'bg-emerald-50 text-emerald-700 border-emerald-200': slip.status === 'paid',
                                              }">
                                            {{ slip.status }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="payslips.length === 0">
                                    <td colspan="6" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                            </div>
                                            <p class="text-lg font-bold text-gray-900">No Payslips Generated</p>
                                            <p class="mt-1 text-sm text-gray-500">Your historical salary records will appear here.</p>
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
