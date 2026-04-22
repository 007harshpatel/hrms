<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    employee: Object,
});

// We map existing arrays, or fall back to an empty initial row if none exist
const initialQuants = props.employee?.qualifications?.length > 0 
    ? props.employee.qualifications 
    : [{ degree: '', institution: '', year: '' }];

const initialOrgs = props.employee?.previous_organizations?.length > 0 
    ? props.employee.previous_organizations 
    : [{ company_name: '', designation: '', duration: '' }];

const form = useForm({
    first_name: props.employee?.first_name || '',
    middle_name: props.employee?.middle_name || '',
    last_name: props.employee?.last_name || '',
    contact_no: props.employee?.contact_no || '',
    alternate_no: props.employee?.alternate_no || '',
    current_address: props.employee?.current_address || '',
    permanent_address: props.employee?.permanent_address || '',
    skills: props.employee?.skills || '',
    experience: props.employee?.experience || '',
    qualifications: [...initialQuants],
    previous_organizations: [...initialOrgs],
    professional_photo: null,
    casual_photo: null,
    adhar_upload: null,
    salary_slips: []
});

const addQualification = () => {
    form.qualifications.push({ degree: '', institution: '', year: '' });
};
const removeQualification = (index) => {
    form.qualifications.splice(index, 1);
};

const addOrganization = () => {
    form.previous_organizations.push({ company_name: '', designation: '', duration: '' });
};
const removeOrganization = (index) => {
    form.previous_organizations.splice(index, 1);
};

const handleSalarySlip = (event, index) => {
    form.salary_slips[index] = event.target.files[0];
};

const submit = () => {
    // We use post to correctly support multipart/form-data with Inertia/Laravel
    form.post(route('profile.employee.update'), {
        preserveScroll: true,
        preserveState: true,
    });
};
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                HR Profile Details
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Update your comprehensive personal details, contact info, and professional history.
            </p>
        </header>

        <form @submit.prevent="submit" class="mt-6 space-y-8" enctype="multipart/form-data">
            
            <!-- Personal Info Grid -->
            <div>
                <h3 class="text-md font-bold mb-3 border-b pb-2 dark:text-white">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">First Name</label>
                        <input v-model="form.first_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Middle Name</label>
                        <input v-model="form.middle_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Last Name</label>
                        <input v-model="form.last_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Contact Number</label>
                        <input v-model="form.contact_no" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Alternate Number</label>
                        <input v-model="form.alternate_no" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm" />
                    </div>
                </div>
            </div>

            <!-- Addresses Details -->
            <div>
                 <h3 class="text-md font-bold mb-3 border-b pb-2 dark:text-white">Addresses</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Current Address</label>
                        <textarea v-model="form.current_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm" required></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Permanent Address</label>
                        <textarea v-model="form.permanent_address" rows="3" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm" required></textarea>
                    </div>
                 </div>
            </div>

            <!-- Professional Summary -->
            <div>
                 <h3 class="text-md font-bold mb-3 border-b pb-2 dark:text-white">Professional Summary</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Skills (Comma separated)</label>
                        <textarea v-model="form.skills" rows="2" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Total Experience (Years)</label>
                        <input v-model="form.experience" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm" />
                    </div>
                 </div>
            </div>

            <!-- Documents Update -->
            <div>
                <h3 class="text-md font-bold mb-3 border-b pb-2 dark:text-white">Update Core Documents</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Professional Photo</label>
                        <input type="file" @change="e => form.professional_photo = e.target.files[0]" class="mt-1 block w-full text-xs dark:text-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Casual Photo</label>
                        <input type="file" @change="e => form.casual_photo = e.target.files[0]" class="mt-1 block w-full text-xs dark:text-gray-400" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium dark:text-gray-300">Adhar ID Upload</label>
                        <input type="file" @change="e => form.adhar_upload = e.target.files[0]" class="mt-1 block w-full text-xs dark:text-gray-400" />
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-2 italic">Note: Uploading a new document will override your previous file.</p>
            </div>

            <!-- Qualifications Repeater -->
            <div>
                <div class="flex justify-between items-center mb-3 border-b pb-2">
                    <h3 class="text-md font-bold dark:text-white">Qualifications</h3>
                    <button type="button" @click="addQualification" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">+ Add More</button>
                </div>
                <div v-for="(qual, index) in form.qualifications" :key="'qual-'+index" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end mb-4 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <div>
                        <label class="block text-xs font-medium dark:text-gray-300">Degree / Qualification</label>
                        <input v-model="qual.degree" type="text" class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white h-9" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium dark:text-gray-300">Institution</label>
                        <input v-model="qual.institution" type="text" class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white h-9" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium dark:text-gray-300">Year</label>
                        <input v-model="qual.year" type="number" class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white h-9" />
                    </div>
                    <div>
                        <button type="button" @click="removeQualification(index)" class="text-red-500 text-sm hover:underline font-bold h-9 bg-red-50 dark:bg-red-900/30 px-4 rounded w-full">Remove</button>
                    </div>
                </div>
            </div>

            <!-- Previous Organizations Repeater -->
            <div>
                <div class="flex justify-between items-center mb-3 border-b pb-2">
                    <h3 class="text-md font-bold dark:text-white">Previous Organizations</h3>
                    <button type="button" @click="addOrganization" class="text-sm bg-blue-100 text-blue-700 px-3 py-1 rounded hover:bg-blue-200">+ Add Org</button>
                </div>
                <div v-for="(org, index) in form.previous_organizations" :key="'org-'+index" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end mb-4 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <div>
                        <label class="block text-xs font-medium dark:text-gray-300">Company Name</label>
                        <input v-model="org.company_name" type="text" class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white h-9" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium dark:text-gray-300">Designation</label>
                        <input v-model="org.designation" type="text" class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white h-9" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium dark:text-gray-300">Duration (Yrs/Mos)</label>
                        <input v-model="org.duration" type="text" class="mt-1 block w-full text-sm rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white h-9" />
                    </div>
                    <div>
                        <label class="block text-xs font-medium dark:text-gray-300">Salary Slip (Optional)</label>
                        <input type="file" @change="e => handleSalarySlip(e, index)" class="mt-1 block w-full text-[10px] dark:text-gray-400" />
                    </div>
                    <div>
                        <button type="button" @click="removeOrganization(index)" class="text-red-500 text-sm hover:underline font-bold h-9 bg-red-50 dark:bg-red-900/30 px-4 rounded w-full">Remove</button>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex items-center gap-4">
                <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150 disabled:opacity-50">
                    Save HR Profile
                </button>
                <div v-show="form.recentlySuccessful">
                    <p class="text-sm text-green-600 dark:text-green-400">Saved successfully.</p>
                </div>
                <div v-if="Object.keys(form.errors).length > 0">
                    <p class="text-sm text-red-600 dark:text-red-400">Please fix the errors above.</p>
                </div>
            </div>

        </form>
    </section>
</template>
