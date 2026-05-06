<x-layouts.app>
    <x-slot:title>My Profile</x-slot:title>
    <x-slot:header>My Profile</x-slot:header>

    <div class="max-w-4xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Info Card -->
            <div class="lg:col-span-1">
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="bg-gradient-to-br from-slate-700 to-slate-900 px-6 py-8 text-center">
                        <div class="w-24 h-24 mx-auto rounded-full bg-gradient-to-br {{ $user->isAdmin() ? 'from-amber-500 to-orange-500' : 'from-blue-500 to-blue-600' }} flex items-center justify-center shadow-lg mb-4">
                            <span class="text-white text-3xl font-bold">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                        </div>
                        <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                        <p class="text-slate-400 text-sm mt-1">{{ $user->email }}</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium mt-3 {{ $user->isAdmin() ? 'bg-amber-500/20 text-amber-300' : 'bg-blue-500/20 text-blue-300' }}">
                            <i class="fas {{ $user->isAdmin() ? 'fa-shield-alt' : 'fa-user' }} mr-1"></i>
                            {{ $user->isAdmin() ? 'Administrator' : 'User' }}
                        </span>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="divide-y divide-gray-100">
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm text-gray-500">User ID</dt>
                                <dd class="text-sm font-mono text-gray-900">{{ $user->user_id }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm text-gray-500">Role</dt>
                                <dd class="text-sm text-gray-900 capitalize">{{ $user->role }}</dd>
                            </div>
                            <div class="py-3 flex justify-between">
                                <dt class="text-sm text-gray-500">Joined</dt>
                                <dd class="text-sm text-gray-900">{{ $user->created_at->format('d M Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <!-- Change Password Card -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-key text-gray-400 mr-2"></i>Change Password
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Ensure your account is using a secure password.</p>
                    </div>
                    <form method="POST" action="{{ route('profile.password') }}" class="px-6 py-4">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <!-- Current Password -->
                            <div>
                                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                                    Current Password
                                </label>
                                <div class="relative">
                                    <input type="password" name="current_password" id="current_password" 
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-4 py-2.5 border pr-10 @error('current_password') border-red-300 @enderror"
                                           placeholder="Enter current password">
                                    <button type="button" onclick="togglePassword('current_password', this)" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('current_password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- New Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                    New Password
                                </label>
                                <div class="relative">
                                    <input type="password" name="password" id="password" 
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-4 py-2.5 border pr-10 @error('password') border-red-300 @enderror"
                                           placeholder="Enter new password">
                                    <button type="button" onclick="togglePassword('password', this)" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Minimum 6 characters</p>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                                    Confirm New Password
                                </label>
                                <div class="relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 sm:text-sm px-4 py-2.5 border pr-10"
                                           placeholder="Confirm new password">
                                    <button type="button" onclick="togglePassword('password_confirmation', this)" 
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" 
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                <i class="fas fa-save mr-2"></i>Update Password
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Account Activity Card -->
                <div class="bg-white shadow rounded-lg mt-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-history text-gray-400 mr-2"></i>Account Information
                        </h3>
                    </div>
                    <div class="px-6 py-4">
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <dt class="text-sm text-gray-500 mb-1">Account Created</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    <i class="fas fa-calendar-plus text-green-500 mr-2"></i>
                                    {{ $user->created_at->format('d M Y, H:i') }}
                                </dd>
                            </div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <dt class="text-sm text-gray-500 mb-1">Last Updated</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    <i class="fas fa-clock text-blue-500 mr-2"></i>
                                    {{ $user->updated_at->format('d M Y, H:i') }}
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</x-layouts.app>
