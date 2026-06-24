<x-layouts.app>
<x-slot:title>Dashboard</x-slot:title>
<x-slot:header>Dashboard</x-slot:header>

<div class="space-y-6">

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {{-- Total Users --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <i class="fas fa-users text-blue-600 text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Total Users</p>
                <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ number_format($totalUsers) }}</p>
            </div>
        </div>
        {{-- Total Admins --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center shrink-0">
                <i class="fas fa-user-shield text-violet-600 text-xl"></i>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">Admins</p>
                <p class="text-2xl font-bold text-gray-800 mt-0.5">{{ number_format($totalAdmins) }}</p>
            </div>
        </div>
    </div>

    {{-- Welcome Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-1">Welcome, {{ auth()->user()->name }}!</h2>
        <p class="text-gray-500 text-sm">This is your application dashboard. Add your features and modules here.</p>
    </div>

</div>
</x-layouts.app>
