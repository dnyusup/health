<x-layouts.app>
    <x-slot:title>Dashboard</x-slot:title>
    <x-slot:header>Dashboard</x-slot:header>

    <!-- Welcome Card -->
    <div class="mb-8 bg-white shadow rounded-lg p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">
            <i class="fas fa-hand-wave text-yellow-500 mr-2"></i>
            Welcome, {{ auth()->user()->name }}!
        </h3>
        <p class="text-gray-500 text-sm">Selamat datang di aplikasi Assy Part.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 mb-8">
        <!-- Total Users -->
        <div class="relative overflow-hidden rounded-lg bg-white px-4 py-5 shadow sm:px-6 sm:py-6">
            <dt>
                <div class="absolute rounded-md bg-primary-500 p-3">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
                <p class="ml-16 truncate text-sm font-medium text-gray-500">Total Users</p>
            </dt>
            <dd class="ml-16 flex items-baseline">
                <p class="text-2xl font-semibold text-gray-900">{{ number_format($totalUsers) }}</p>
            </dd>
        </div>
    </div>

    @if(auth()->user()->isAdmin())
    <!-- Quick Actions for Admin -->
    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>
            Quick Actions
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('users.index') }}" class="flex flex-col items-center p-4 bg-primary-50 rounded-lg hover:bg-primary-100 transition">
                <i class="fas fa-users-cog text-primary-600 text-2xl mb-2"></i>
                <span class="text-sm font-medium text-primary-700">Manage Users</span>
            </a>
            <a href="{{ route('users.create') }}" class="flex flex-col items-center p-4 bg-green-50 rounded-lg hover:bg-green-100 transition">
                <i class="fas fa-user-plus text-green-600 text-2xl mb-2"></i>
                <span class="text-sm font-medium text-green-700">Add User</span>
            </a>
        </div>
    </div>
    @endif

</x-layouts.app>

