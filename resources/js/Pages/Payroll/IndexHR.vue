<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    employees: Array,
    payslips: Array,
});

const showStructureModal = ref(false);
const structureForm = useForm({
    base_salary: 0,
    hra_percentage: 0,
    allowances: 0,
    deductions: 0,
    employee_id: null,
});

const showGenerateModal = ref(false);
const generateForm = useForm({
    employee_id: null,
    month: new Date().getMonth() + 1,
    year: new Date().getFullYear(),
});

const openStructureModal = (emp) => {
    structureForm.employee_id = emp.id;
    if (emp.salary_structure) {
        structureForm.base_salary = emp.salary_structure.base_salary;
        structureForm.hra_percentage = emp.salary_structure.hra_percentage;
        structureForm.allowances = emp.salary_structure.allowances;
        structureForm.deductions = emp.salary_structure.deductions;
    } else {
        structureForm.base_salary = 0;
        structureForm.hra_percentage = 0;
        structureForm.allowances = 0;
        structureForm.deductions = 0;
    }
    showStructureModal.value = true;
};

const openGenerateModal = (emp) => {
    generateForm.employee_id = emp.id;
    showGenerateModal.value = true;
};

const saveStructure = () => {
    structureForm.patch(route('payroll.structure.update', structureForm.employee_id), {
        onSuccess: () => {
            showStructureModal.value = false;
        }
    });
};

const executeGenerate = () => {
    generateForm.post(route('payroll.generate', generateForm.employee_id), {
        onSuccess: () => {
            showGenerateModal.value = false;
        }
    });
};

const markPaidForm = useForm({});
const markPaid = (id) => {
    markPaidForm.patch(route('payroll.payslips.pay', id));
};
</script>

<template>
    <Head title="Payroll Management" />

    <AuthenticatedLayout>
        
        <!-- Premium Header Section -->
        <div class="relative bg-gradient-to-br from-emerald-900 via-teal-900 to-emerald-800 pb-32 pt-12">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-6 md:mb-0">
                    <h2 class="text-4xl font-extrabold text-white tracking-tight drop-shadow-lg">
                        Payroll Control Center
                    </h2>
                    <p class="mt-2 text-emerald-100 text-lg max-w-xl font-medium">
                        Configure compensation packages and process monthly disbursements.
                    </p>
                </div>
            </div>
        </div>

        <div class="-mt-24 pb-12 relative z-10">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-8">
                
                <!-- Errors -->
                <div v-if="$page.props.errors && Object.keys($page.props.errors).length > 0" class="bg-red-50 text-red-700 border border-red-200 p-6 rounded-2xl font-bold shadow-lg shadow-red-500/10 flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <div>
                        <p v-for="(err, key) in $page.props.errors" :key="key">{{ err }}</p>
                    </div>
                </div>

                <!-- Salary Structures Grid -->
                <div class="bg-white/95 backdrop-blur-xl shadow-2xl sm:rounded-3xl border border-white/40 overflow-hidden">
                    <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">Employee Salary Packages</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/80 text-xs uppercase font-black tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-8 py-5">Employee</th>
                                    <th class="px-8 py-5">Base Salary</th>
                                    <th class="px-8 py-5">HRA %</th>
                                    <th class="px-8 py-5 text-blue-600">+ Allowances</th>
                                    <th class="px-8 py-5 text-red-500">- Deductions</th>
                                    <th class="px-8 py-5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="emp in employees" :key="emp.id" class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center mr-3 shadow-sm border border-emerald-200">
                                                {{ emp.name.charAt(0) }}
                                            </div>
                                            <div>
                                                <span class="font-bold text-gray-900 block">{{ emp.name }}</span>
                                                <span class="text-xs font-semibold text-gray-500">{{ emp.department }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 font-bold text-emerald-600">
                                        ${{ emp.salary_structure ? emp.salary_structure.base_salary : '0.00' }}
                                    </td>
                                    <td class="px-8 py-5 font-medium text-gray-600">{{ emp.salary_structure ? emp.salary_structure.hra_percentage : '0' }}%</td>
                                    <td class="px-8 py-5 text-blue-600 font-medium">+${{ emp.salary_structure ? emp.salary_structure.allowances : '0.00' }}</td>
                                    <td class="px-8 py-5 text-red-500 font-medium">-${{ emp.salary_structure ? emp.salary_structure.deductions : '0.00' }}</td>
                                    <td class="px-8 py-5 text-right space-x-2 whitespace-nowrap">
                                        <button @click="openStructureModal(emp)" class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 font-bold transition-colors">Edit Setup</button>
                                        <button @click="openGenerateModal(emp)" class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg hover:bg-emerald-100 font-bold transition-colors shadow-sm shadow-emerald-500/20">Generate Payslip</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payslips Ledger -->
                <div class="bg-white/95 backdrop-blur-xl shadow-2xl sm:rounded-3xl border border-white/40 overflow-hidden">
                    <div class="p-8 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight">Historical Payslips</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/80 text-xs uppercase font-black tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-8 py-5">Employee</th>
                                    <th class="px-8 py-5">Period</th>
                                    <th class="px-8 py-5">Generated On</th>
                                    <th class="px-8 py-5 text-emerald-600">Net Pay</th>
                                    <th class="px-8 py-5">Status</th>
                                    <th class="px-8 py-5 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="slip in payslips" :key="'slip-'+slip.id" class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-8 py-5 font-bold text-gray-900">{{ slip.employee_name }}</td>
                                    <td class="px-8 py-5 font-bold text-gray-600">{{ slip.period }}</td>
                                    <td class="px-8 py-5 text-sm text-gray-500">{{ new Date(slip.created_at).toLocaleDateString() }}</td>
                                    <td class="px-8 py-5 font-black text-emerald-600 text-lg">${{ slip.net_pay }}</td>
                                    <td class="px-8 py-5">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase border"
                                              :class="{
                                                  'bg-amber-50 text-amber-700 border-amber-200': slip.status === 'generated',
                                                  'bg-emerald-50 text-emerald-700 border-emerald-200': slip.status === 'paid',
                                              }">
                                            {{ slip.status }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5 text-right space-x-2">
                                        <template v-if="slip.status === 'generated'">
                                            <button @click="markPaid(slip.id)" :disabled="markPaidForm.processing" class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 font-bold transition-colors disabled:opacity-50 shadow-md shadow-emerald-500/30">
                                                Mark Paid
                                            </button>
                                        </template>
                                        <template v-else>
                                            <span class="text-gray-400 text-sm font-semibold italic bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100">Cleared</span>
                                        </template>
                                    </td>
                                </tr>
                                <tr v-if="payslips.length === 0">
                                    <td colspan="6" class="px-8 py-16 text-center text-gray-500">
                                        No payslips have been generated in the system.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>

        <!-- Glassmorphic Structure Modal -->
        <div v-if="showStructureModal" class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" @click="showStructureModal = false"></div>
            <div class="relative w-full max-w-lg bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden transform transition-all border border-white/20">
                <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-750 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight">Define Salary Rules</h3>
                    <button @click="showStructureModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form @submit.prevent="saveStructure" class="p-8 space-y-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-1">Base Salary ($)</label>
                            <input type="number" step="0.01" v-model="structureForm.base_salary" class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm transition-shadow" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">HRA Percentage (%)</label>
                            <input type="number" step="0.01" v-model="structureForm.hra_percentage" class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm transition-shadow" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Allowances ($)</label>
                            <input type="number" step="0.01" v-model="structureForm.allowances" class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm transition-shadow" required>
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-red-600 mb-1">Deductions ($)</label>
                            <input type="number" step="0.01" v-model="structureForm.deductions" class="block w-full rounded-xl border-red-200 bg-red-50 focus:ring-2 focus:ring-red-500 focus:border-red-500 shadow-sm transition-shadow text-red-700 font-bold" required>
                        </div>
                    </div>
                    <div class="pt-6 flex justify-end space-x-4 border-t border-gray-100">
                        <button type="button" @click="showStructureModal = false" class="px-6 py-3 rounded-xl text-sm font-bold text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">Cancel</button>
                        <button type="submit" :disabled="structureForm.processing" class="px-8 py-3 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 shadow-lg shadow-emerald-500/30 transition-all hover:-translate-y-0.5 disabled:opacity-50">Save Package</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Glassmorphic Generate Modal -->
        <div v-if="showGenerateModal" class="fixed inset-0 z-50 flex items-center justify-center px-4 py-6">
            <div class="fixed inset-0 transition-opacity bg-gray-900/60 backdrop-blur-sm" @click="showGenerateModal = false"></div>
            <div class="relative w-full max-w-md bg-white dark:bg-gray-800 rounded-3xl shadow-2xl overflow-hidden transform transition-all border border-white/20">
                <div class="px-8 py-6 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-2xl font-extrabold text-gray-900 tracking-tight">Run Payroll</h3>
                    <button @click="showGenerateModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <form @submit.prevent="executeGenerate" class="p-8 space-y-6">
                    <p class="text-sm font-medium text-gray-500">Specify the target period. The system will compile net payment based on current active compensation rules.</p>
                    <div class="grid grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Month (1-12)</label>
                            <input type="number" min="1" max="12" v-model="generateForm.month" class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm transition-shadow" required>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Year</label>
                            <input type="number" min="2000" v-model="generateForm.year" class="block w-full rounded-xl border-gray-200 bg-gray-50 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-sm transition-shadow" required>
                        </div>
                    </div>
                    <div class="pt-6 flex justify-end space-x-4 border-t border-gray-100">
                        <button type="button" @click="showGenerateModal = false" class="px-6 py-3 rounded-xl text-sm font-bold text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors">Cancel</button>
                        <button type="submit" :disabled="generateForm.processing" class="px-8 py-3 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 shadow-lg shadow-blue-500/30 transition-all hover:-translate-y-0.5 disabled:opacity-50 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Compile Run
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
