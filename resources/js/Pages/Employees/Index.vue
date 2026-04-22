<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    employees: Array,
    canManage: Boolean,
});

const searchQuery = ref('');

const filteredEmployees = computed(() => {
    if (!searchQuery.value) return props.employees;
    const lowerQuery = searchQuery.value.toLowerCase();
    return props.employees.filter(emp => 
        emp.name.toLowerCase().includes(lowerQuery) || 
        emp.email.toLowerCase().includes(lowerQuery) ||
        emp.department.toLowerCase().includes(lowerQuery) ||
        emp.role.toLowerCase().includes(lowerQuery)
    );
});
</script>

<template>
    <Head title="Employee Directory" />

    <AuthenticatedLayout>
        
        <!-- Premium Header Section -->
        <div class="relative bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800 pb-32 pt-12">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-6 md:mb-0">
                    <h2 class="text-4xl font-extrabold text-white tracking-tight drop-shadow-lg">
                        Employee Directory
                    </h2>
                    <p class="mt-2 text-indigo-200 text-lg max-w-xl font-medium">
                        Manage your team, view profiles, and organize departments all in one place.
                    </p>
                </div>
                <div v-if="canManage" class="flex-shrink-0">
                    <Link :href="route('employees.create')" class="group relative inline-flex items-center justify-center px-8 py-3.5 text-base font-bold text-indigo-900 bg-white border border-transparent rounded-full overflow-hidden shadow-2xl transition-all duration-300 hover:scale-105 hover:shadow-indigo-500/50">
                        <span class="absolute inset-0 w-full h-full -mt-1 rounded-lg opacity-30 bg-gradient-to-b from-transparent via-transparent to-black"></span>
                        <svg class="w-5 h-5 mr-2 -ml-1 text-indigo-600 transition-transform duration-300 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Add Employee
                    </Link>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="-mt-24 pb-12 relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Search Bar -->
                <div class="bg-white/90 backdrop-blur-xl rounded-2xl shadow-xl border border-white/40 p-4 mb-8 flex items-center">
                    <svg class="w-6 h-6 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input v-model="searchQuery" type="text" placeholder="Search by name, email, role, or department..." class="w-full bg-transparent border-0 focus:ring-0 text-gray-900 font-medium placeholder-gray-400 px-4 py-2" />
                </div>

                <div v-if="filteredEmployees.length === 0" class="bg-white/80 backdrop-blur-xl rounded-3xl shadow-xl border border-white/20 p-16 text-center">
                    <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-indigo-50 mb-6 shadow-inner">
                        <svg class="w-12 h-12 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">No Employees Found</h3>
                    <p class="mt-2 text-gray-500 max-w-md mx-auto text-lg">We couldn't find any employees matching your search criteria, or your directory is empty.</p>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    
                    <!-- Employee Profile Cards -->
                    <div v-for="employee in filteredEmployees" :key="employee.id" 
                         class="group relative bg-white dark:bg-gray-800 rounded-3xl p-6 shadow-sm hover:shadow-2xl transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1 overflow-hidden flex flex-col">
                        
                        <!-- Decorative background blob -->
                        <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full opacity-10 transition-transform duration-500 group-hover:scale-150 group-hover:opacity-20"
                             :class="{
                                 'bg-purple-600': employee.role === 'Admin',
                                 'bg-blue-500': employee.role === 'HR',
                                 'bg-green-500': employee.role === 'Employee'
                             }">
                        </div>

                        <div class="flex items-center justify-between relative z-10 mb-6">
                            <div class="flex items-center">
                                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white font-bold text-xl shadow-lg"
                                     :class="{
                                         'bg-gradient-to-br from-purple-500 to-indigo-600 shadow-purple-500/30': employee.role === 'Admin',
                                         'bg-gradient-to-br from-blue-400 to-cyan-500 shadow-blue-500/30': employee.role === 'HR',
                                         'bg-gradient-to-br from-green-400 to-teal-500 shadow-green-500/30': employee.role === 'Employee'
                                     }">
                                    {{ employee.name.charAt(0) }}
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-xl font-extrabold text-gray-900 dark:text-white leading-tight truncate max-w-[150px]">{{ employee.name }}</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold tracking-wide uppercase mt-1"
                                          :class="{
                                              'bg-purple-50 text-purple-700 ring-1 ring-purple-600/20': employee.role === 'Admin',
                                              'bg-blue-50 text-blue-700 ring-1 ring-blue-600/20': employee.role === 'HR',
                                              'bg-green-50 text-green-700 ring-1 ring-green-600/20': employee.role === 'Employee'
                                          }">
                                        {{ employee.role }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-3 relative z-10 mb-6 flex-grow">
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                <span class="truncate">{{ employee.email }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span class="font-medium text-gray-700 dark:text-gray-300">{{ employee.department }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                <span>Reports to: <strong class="text-gray-700 dark:text-gray-300">{{ employee.manager }}</strong></span>
                            </div>
                        </div>

                        <div v-if="canManage" class="mt-auto pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-end items-center relative z-10 space-x-2">
                            <Link :href="route('employees.edit', employee.id)" class="px-4 py-2 bg-indigo-50 text-indigo-600 text-sm font-bold rounded-xl hover:bg-indigo-100 transition-colors" title="Edit">
                                Edit Profile
                            </Link>
                            <Link :href="route('employees.destroy', employee.id)" method="delete" as="button" type="button" class="px-4 py-2 bg-red-50 text-red-600 text-sm font-bold rounded-xl hover:bg-red-100 transition-colors" title="Delete">
                                Delete
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
