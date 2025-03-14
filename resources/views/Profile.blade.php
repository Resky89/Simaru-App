@extends('Layout.app')

@section('title', 'Profile')

@section('content')
<div class="h-full">
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Left Section - User Avatar & Info -->
        <div class="md:col-span-3 bg-white rounded-lg shadow p-6 flex flex-col items-center">
            <!-- Avatar with Upload Option -->
            <div class="mb-6 mt-4 relative group">
                <div class="w-40 h-40 rounded-full bg-gray-200 flex items-center justify-center text-[#213268] text-7xl font-semibold overflow-hidden">
                    <div id="current-avatar" class="w-full h-full flex items-center justify-center">
                        AR
                    </div>
                    <img id="avatar-preview" class="w-full h-full object-cover absolute top-0 left-0 hidden" alt="Profile Picture">
                </div>

                <!-- Camera/Edit Icon Overlay -->
                <label for="avatar-upload" class="absolute inset-0 w-40 h-40 rounded-full flex items-center justify-center bg-black/50 text-white opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="ml-2">Change</span>
                </label>

                <!-- Hidden File Input -->
                <input type="file" id="avatar-upload" accept="image/*" class="hidden">
            </div>

            <!-- User Name -->
            <h2 class="text-2xl font-semibold text-[#213268] text-center">Austin Robertson</h2>
            <p class="text-gray-500 mb-8">Super Admin</p>

            <!-- Last Login -->
            <div class="text-center text-gray-500 text-sm">
                <p>Last login:</p>
                <p class="font-medium">July 18, 2023 09:15 AM</p>
            </div>
        </div>

        <!-- Right Section - Forms -->
        <div class="md:col-span-9 space-y-6">
            <!-- Profile Information -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold text-[#213268] mb-6">Profile Information</h3>

                <form id="profileForm" class="space-y-4">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" id="name" value="Austin Robertson"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#213268] focus:border-[#213268]">
                    </div>

                    <!-- Phone Number -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="tel" id="phone" value="123456789"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#213268] focus:border-[#213268]">
                    </div>

                    <!-- Email ID -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email ID</label>
                        <input type="email" id="email" value="austin@example.com"
                               class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#213268] focus:border-[#213268]">
                    </div>

                    <!-- Update Button -->
                    <div class="mt-6">
                        <button type="submit" id="updateProfileBtn"
                                class="w-full bg-[#213268] text-white py-2 rounded-md hover:bg-[#162348] transition-colors">
                            UPDATE
                        </button>
                    </div>
                </form>
            </div>

            <!-- Reset Password -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold text-[#213268] mb-6">Reset Password</h3>

                <form id="passwordResetForm" class="space-y-4">
                    <!-- New Password -->
                    <div class="relative">
                        <label for="newPassword" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <div class="relative">
                            <input type="password" id="newPassword"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#213268] focus:border-[#213268] pr-10">
                            <button type="button" class="password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500" data-target="newPassword">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="relative">
                        <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <div class="relative">
                            <input type="password" id="confirmPassword"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-[#213268] focus:border-[#213268] pr-10">
                            <button type="button" class="password-toggle absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500" data-target="confirmPassword">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Reset Button -->
                    <div class="mt-6">
                        <button type="submit" id="resetPasswordBtn"
                                class="w-full bg-[#213268] text-white py-2 rounded-md hover:bg-[#162348] transition-colors">
                            RESET PASSWORD
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Profile picture upload
        const avatarUpload = document.getElementById('avatar-upload');
        const avatarPreview = document.getElementById('avatar-preview');
        const currentAvatar = document.getElementById('current-avatar');

        // Handle file selection for avatar
        avatarUpload.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    // Show the image preview
                    avatarPreview.src = e.target.result;
                    avatarPreview.classList.remove('hidden');
                    currentAvatar.classList.add('hidden');

                    // Show toast notification
                    showToast('Profile picture updated', 'success');

                    // You would typically upload the file to the server here
                    // uploadProfilePicture(file);
                }

                reader.readAsDataURL(file);
            }
        });

        // Toggle password visibility
        document.querySelectorAll('.password-toggle').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const passwordInput = document.getElementById(targetId);

                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>`;
                } else {
                    passwordInput.type = 'password';
                    this.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>`;
                }
            });
        });

        // Handle profile form submission
        document.getElementById('profileForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const name = document.getElementById('name').value;
            const phone = document.getElementById('phone').value;
            const email = document.getElementById('email').value;

            // Simple validation
            if (!name || !phone || !email) {
                showToast('Please fill in all fields', 'error');
                return;
            }

            // Simulate API call
            const submitButton = document.getElementById('updateProfileBtn');
            submitButton.disabled = true;
            submitButton.innerHTML = 'UPDATING...';

            setTimeout(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'UPDATE';
                showToast('Profile updated successfully', 'success');
            }, 1000);
        });

        // Handle password reset form submission
        document.getElementById('passwordResetForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            // Simple validation
            if (!newPassword) {
                showToast('Please enter a new password', 'error');
                return;
            }

            if (newPassword !== confirmPassword) {
                showToast('Passwords do not match', 'error');
                return;
            }

            // Simulate API call
            const submitButton = document.getElementById('resetPasswordBtn');
            submitButton.disabled = true;
            submitButton.innerHTML = 'RESETTING...';

            setTimeout(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'RESET PASSWORD';
                document.getElementById('newPassword').value = '';
                document.getElementById('confirmPassword').value = '';
                showToast('Password reset successfully', 'success');
            }, 1000);
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            // Create toast container if it doesn't exist
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'fixed top-20 right-5 z-50 flex flex-col gap-2';
                document.body.appendChild(toastContainer);
            }

            // Create toast
            const toast = document.createElement('div');
            toast.className = `p-3 rounded shadow-lg flex items-center gap-2 transform translate-x-full transition-transform duration-300 ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                'bg-blue-500 text-white'
            }`;

            // Add icon based on type
            let icon = '';
            if (type === 'success') {
                icon = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>`;
            } else if (type === 'error') {
                icon = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>`;
            } else {
                icon = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>`;
            }

            toast.innerHTML = `${icon}<span>${message}</span>`;
            toastContainer.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.classList.replace('translate-x-full', 'translate-x-0');
            }, 10);

            // Animate out after delay and remove
            setTimeout(() => {
                toast.classList.replace('translate-x-0', 'translate-x-full');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }, 3000);
        }
    });
</script>
@endpush
