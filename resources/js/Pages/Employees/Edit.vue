<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import { ref } from 'vue';

const props = defineProps({
    employee: Object,
    managers: Array,
});

const form = useForm({
    _method: 'put',
    first_name: props.employee.first_name || '',
    middle_name: props.employee.middle_name || '',
    last_name: props.employee.last_name || '',
    email: props.employee.user?.email || '',
    password: '',
    role: props.employee.user?.roles?.[0]?.name || 'Employee',
    department: props.employee.department || '',
    joining_date: props.employee.joining_date || '',
    manager_id: props.employee.manager_id || '',
    contact_no: props.employee.contact_no || '',
    alternate_no: props.employee.alternate_no || '',
    current_address: props.employee.current_address || '',
    permanent_address: props.employee.permanent_address || '',
    skills: props.employee.skills || '',
    experience: props.employee.experience || '',
    
    qualifications: props.employee.qualifications?.map(q => ({
        degree: q.qualification,
        institution: q.institution,
        year: q.year
    })) || [],
    previous_organizations: props.employee.previous_organizations?.map(o => ({
        company_name: o.company_name,
        designation: o.designation,
        duration: o.duration
    })) || [],
    
    professional_photo: null,
    casual_photo: null,
    adhar_upload: null,
    salary_slips: [] // Index mapping array
});

const addQualification = () => {
    form.qualifications.push({ degree: '', institution: '', year: '' });
};

const removeQualification = (index) => {
    form.qualifications.splice(index, 1);
};

const addPreviousOrg = () => {
    form.previous_organizations.push({ company_name: '', designation: '', duration: '' });
};

const removePreviousOrg = (index) => {
    form.previous_organizations.splice(index, 1);
    form.salary_slips.splice(index, 1); // remove associated file array
};

const handleSalarySlip = (event, index) => {
    form.salary_slips[index] = event.target.files[0];
};

const submit = () => {
    form.post(route('employees.update', props.employee.id));
};
</script>

<template>
    <Head title="Edit Employee Profile" />

    <AuthenticatedLayout>
        <!-- Premium Header Section -->
        <div class="relative bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800 pb-32 pt-12">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-6 md:mb-0 flex items-center space-x-4">
                    <Link :href="route('employees.index')" class="text-indigo-200 hover:text-white transition-colors bg-white/10 p-2 rounded-full backdrop-blur-md" title="Go Back">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </Link>
                    <div>
                        <h2 class="text-4xl font-extrabold text-white tracking-tight drop-shadow-lg">
                            Edit Employee Details
                        </h2>
                        <p class="mt-2 text-indigo-200 text-lg max-w-xl font-medium">
                            Update the personal, account, and professional information.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="-mt-24 pb-12 relative z-10">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
                <div class="bg-white/95 backdrop-blur-xl shadow-2xl sm:rounded-3xl border border-white/40 overflow-hidden">
                    <form @submit.prevent="submit" class="divide-y divide-gray-100">

                        <!-- Personal Information -->
                        <div class="p-8 space-y-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">1. Personal Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">First Name</label>
                                    <input v-model="form.first_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500" required />
                                    <span class="text-red-500 text-sm" v-if="form.errors.first_name">{{ form.errors.first_name }}</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Middle Name</label>
                                    <input v-model="form.middle_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Last Name</label>
                                    <input v-model="form.last_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-blue-500" required />
                                    <span class="text-red-500 text-sm" v-if="form.errors.last_name">{{ form.errors.last_name }}</span>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contact No</label>
                                    <input v-model="form.contact_no" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm" required />
                                    <span class="text-red-500 text-sm" v-if="form.errors.contact_no">{{ form.errors.contact_no }}</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Alternate Contact</label>
                                    <input v-model="form.alternate_no" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Address</label>
                                    <textarea v-model="form.current_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm" required></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Permanent Address</label>
                                    <textarea v-model="form.permanent_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm" required></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Account & Identity details -->
                        <div class="p-8 space-y-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">2. Account & Role Alignment</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email Address (Login ID)</label>
                                    <input v-model="form.email" type="email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm" required />
                                    <span class="text-red-500 text-sm" v-if="form.errors.email">{{ form.errors.email }}</span>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password (Leave blank to keep current)</label>
                                    <TextInput v-model="form.password" type="password" class="mt-1 block w-full" />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">System Role</label>
                                    <select v-model="form.role" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm">
                                        <option value="Employee">Employee</option>
                                        <option value="HR">HR Manager</option>
                                        <option value="Admin">Administrator</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Department</label>
                                    <input v-model="form.department" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm" required />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Joining Date</label>
                                    <input v-model="form.joining_date" type="date" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm" required />
                                </div>
                            </div>
                        </div>

                        <!-- Documents Upload -->
                        <div class="p-8 space-y-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">3. Identifications & Photos</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Professional Photo</label>
                                    <input type="file" @change="e => form.professional_photo = e.target.files[0]" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Casual Photo</label>
                                    <input type="file" @change="e => form.casual_photo = e.target.files[0]" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Aadhar Upload</label>
                                    <input type="file" @change="e => form.adhar_upload = e.target.files[0]" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200" />
                                </div>
                            </div>
                            <span class="text-red-500 text-sm" v-if="form.errors.professional_photo || form.errors.casual_photo || form.errors.adhar_upload">Verify file types/sizes (Max 2MB/5MB).</span>
                        </div>

                        <!-- Skills and Qualifications -->
                        <div class="p-8 space-y-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">4. Core Skills & Qualifications</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Applicable Skills (Comma separated)</label>
                                <input v-model="form.skills" type="text" placeholder="e.g. PHP, Vue.js, Management" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm" />
                            </div>

                            <div class="pt-4">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Qualifications Matrix</label>
                                    <button type="button" @click="addQualification" class="text-sm bg-indigo-100 hover:bg-indigo-200 text-indigo-800 py-1 px-3 rounded">+ Add More</button>
                                </div>
                                <div v-for="(q, index) in form.qualifications" :key="index" class="flex gap-4 items-center mt-2">
                                    <input v-model="q.degree" type="text" placeholder="Degree / Course" class="block w-1/3 rounded-md border-gray-300 dark:bg-gray-700" required />
                                    <input v-model="q.institution" type="text" placeholder="Institution" class="block w-1/3 rounded-md border-gray-300 dark:bg-gray-700" required />
                                    <input v-model="q.year" type="number" placeholder="Year" class="block w-1/4 rounded-md border-gray-300 dark:bg-gray-700" required />
                                    <button type="button" @click="removeQualification(index)" class="text-red-500 hover:text-red-700 w-8">X</button>
                                </div>
                                <p class="text-gray-400 text-xs mt-2" v-if="form.qualifications.length === 0">No qualifications added yet.</p>
                            </div>
                        </div>

                        <!-- Previous Organizations -->
                        <div class="p-8 space-y-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">5. Past Experience Tracker</h3>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Total Experience (Years)</label>
                                <input v-model="form.experience" type="text" placeholder="e.g. 5 Years" class="mt-1 block w-md rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm" />
                            </div>

                            <div class="pt-4">
                                <div class="flex justify-between items-center mb-2">
                                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300">Previous Organizations</label>
                                    <button type="button" @click="addPreviousOrg" class="text-sm bg-teal-100 hover:bg-teal-200 text-teal-800 py-1 px-3 rounded">+ Add Org</button>
                                </div>
                                <div v-for="(org, index) in form.previous_organizations" :key="index" class="p-4 border border-teal-100 dark:border-teal-900 rounded-lg bg-teal-50 dark:bg-gray-800 mt-2 relative">
                                    <button type="button" @click="removePreviousOrg(index)" class="absolute top-2 right-2 text-red-500 hover:text-red-700 font-bold">&#215;</button>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <input v-model="org.company_name" type="text" placeholder="Company Name" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700" required />
                                        </div>
                                        <div>
                                            <input v-model="org.designation" type="text" placeholder="Designation" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700" required />
                                        </div>
                                        <div>
                                            <input v-model="org.duration" type="text" placeholder="Duration (e.g. 2018-2022)" class="mt-1 block w-full rounded-md border-gray-300 dark:bg-gray-700" required />
                                        </div>
                                        <div>
                                            <label class="block text-xs font-semibold mt-1">Upload Salary Slip (Optional)</label>
                                            <input type="file" @change="e => handleSalarySlip(e, index)" class="block w-full text-xs text-gray-500" />
                                        </div>
                                    </div>
                                </div>
                                <p class="text-gray-400 text-xs mt-2" v-if="form.previous_organizations.length === 0">No prior organizations recorded.</p>
                            </div>
                        </div>

                        <div class="p-8 bg-gray-50/50 flex justify-end rounded-b-3xl">
                            <button type="submit" :disabled="form.processing" class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-indigo-500/30 transition-all hover:-translate-y-0.5 disabled:opacity-50">
                                Update Profile &rarr;
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
