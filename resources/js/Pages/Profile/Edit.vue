<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdateEmployeeDetailsForm from './Partials/UpdateEmployeeDetailsForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
    employee: {
        type: Object,
    }
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout>
        
        <!-- Premium Header Section -->
        <div class="relative bg-gradient-to-br from-indigo-900 via-purple-900 to-indigo-800 pb-32 pt-12">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
                <div class="text-center md:text-left mb-6 md:mb-0">
                    <h2 class="text-4xl font-extrabold text-white tracking-tight drop-shadow-lg">
                        Account Settings
                    </h2>
                    <p class="mt-2 text-indigo-200 text-lg max-w-xl font-medium">
                        Manage your personal profile, security preferences, and professional details.
                    </p>
                </div>
            </div>
        </div>

        <div class="-mt-24 pb-12 relative z-10">
            <div class="mx-auto max-w-7xl space-y-8 sm:px-6 lg:px-8">
                
                <!-- Profile Information Card -->
                <div class="bg-white/95 backdrop-blur-xl shadow-xl sm:rounded-3xl border border-white/40 overflow-hidden">
                    <div class="p-8 sm:p-10">
                        <UpdateProfileInformationForm
                            :must-verify-email="mustVerifyEmail"
                            :status="status"
                            class="max-w-xl"
                        />
                    </div>
                </div>

                <!-- Comprehensive Employee Details Card -->
                <div v-if="employee" class="bg-white/95 backdrop-blur-xl shadow-xl sm:rounded-3xl border border-white/40 overflow-hidden">
                    <div class="p-8 sm:p-10">
                        <UpdateEmployeeDetailsForm :employee="employee" class="max-w-full" />
                    </div>
                </div>

                <!-- Update Password Card -->
                <div class="bg-white/95 backdrop-blur-xl shadow-xl sm:rounded-3xl border border-white/40 overflow-hidden">
                    <div class="p-8 sm:p-10">
                        <UpdatePasswordForm class="max-w-xl" />
                    </div>
                </div>

                <!-- Danger Zone: Delete Account -->
                <div class="bg-white/95 backdrop-blur-xl shadow-xl sm:rounded-3xl border border-red-100 overflow-hidden">
                    <div class="p-8 sm:p-10">
                        <DeleteUserForm class="max-w-xl" />
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
