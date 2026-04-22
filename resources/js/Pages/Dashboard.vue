<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;
const isManager = user.roles && (user.roles.includes('Admin') || user.roles.includes('HR'));

defineProps({
    stats: Object,
});
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        
        <!-- Premium Header Section -->
        <div class="relative bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800 pb-32 pt-12">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-6 md:mb-0">
                    <h2 class="text-4xl font-extrabold text-white tracking-tight drop-shadow-lg">
                        Welcome back, {{ user.name }}!
                    </h2>
                    <p class="mt-2 text-indigo-200 text-lg max-w-xl font-medium">
                        Here's an overview of your HRMS workspace today.
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Area -->
        <div class="-mt-24 pb-12 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Stat Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    
                    <!-- Card 1 -->
                    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/40 p-6 flex items-center transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                        <div class="p-4 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-500/30">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Total Team</p>
                            <h3 class="text-2xl font-black text-gray-900 mt-1">{{ stats.totalTeam }}</h3>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/40 p-6 flex items-center transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                        <div class="p-4 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-lg shadow-amber-500/30">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Pending Leaves</p>
                            <h3 class="text-2xl font-black text-gray-900 mt-1">{{ stats.pendingLeaves }}</h3>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/40 p-6 flex items-center transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                        <div class="p-4 rounded-xl bg-gradient-to-br from-green-400 to-teal-500 text-white shadow-lg shadow-green-500/30">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Upcoming Payroll</p>
                            <h3 class="text-2xl font-black text-gray-900 mt-1">{{ stats.upcomingPayroll }}</h3>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/40 p-6 flex items-center transition-all duration-300 hover:-translate-y-1 hover:shadow-2xl">
                        <div class="p-4 rounded-xl bg-gradient-to-br from-purple-500 to-pink-600 text-white shadow-lg shadow-purple-500/30">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Next Holiday</p>
                            <h3 class="text-xl font-black text-gray-900 mt-1 truncate">{{ stats.nextHoliday }}</h3>
                        </div>
                    </div>
                    
                </div>

                <!-- Quick Actions & Info -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Announcements -->
                    <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-xl font-bold text-gray-900">Recent Updates</h3>
                        </div>
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-3 h-3 rounded-full bg-indigo-500 ring-4 ring-indigo-50"></div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-base font-bold text-gray-900">New HRMS Rolled Out!</h4>
                                    <p class="mt-1 text-sm text-gray-500">Welcome to the newly redesigned premium HRMS system. Enjoy the beautiful new UI.</p>
                                    <span class="text-xs font-semibold text-indigo-500 mt-2 block">Just now</span>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-3 h-3 rounded-full bg-green-500 ring-4 ring-green-50"></div>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-base font-bold text-gray-900">Payroll Cycle Complete</h4>
                                    <p class="mt-1 text-sm text-gray-500">All salary slips for the previous month have been generated and distributed.</p>
                                    <span class="text-xs font-semibold text-green-500 mt-2 block">2 days ago</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-3xl shadow-sm border border-indigo-100/50 p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Quick Actions</h3>
                        <div class="space-y-4">
                            <Link :href="route('leaves.index')" class="flex items-center p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-white hover:border-indigo-100 group">
                                <div class="p-2 bg-indigo-50 rounded-lg text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                </div>
                                <span class="ml-4 font-bold text-gray-700 group-hover:text-indigo-700 transition-colors">Request Leave</span>
                            </Link>
                            
                            <Link v-if="isManager" :href="route('employees.index')" class="flex items-center p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-white hover:border-purple-100 group">
                                <div class="p-2 bg-purple-50 rounded-lg text-purple-600 group-hover:bg-purple-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                </div>
                                <span class="ml-4 font-bold text-gray-700 group-hover:text-purple-700 transition-colors">Manage Team</span>
                            </Link>

                            <Link :href="route('holidays.index')" class="flex items-center p-4 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-white hover:border-blue-100 group">
                                <div class="p-2 bg-blue-50 rounded-lg text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <span class="ml-4 font-bold text-gray-700 group-hover:text-blue-700 transition-colors">View Calendar</span>
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
