<x-layouts.app>
    <x-slot:title>User Details</x-slot:title>
    
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('users.index') }}" 
                   class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">User Details</h1>
                    <p class="text-slate-500 mt-1">View user information and activity</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('users.edit', $user) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-amber-100 text-amber-700 rounded-xl font-medium hover:bg-amber-200 transition-colors">
                    <i class="fas fa-edit"></i>
                    <span>Edit</span>
                </a>
            </div>
        </div>

        <!-- User Info Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 bg-gradient-to-r {{ $user->isAdmin() ? 'from-amber-500 to-orange-500' : 'from-slate-500 to-slate-600' }}">
                <div class="flex items-center gap-4">
                    <div class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="text-white">
                        <h2 class="text-2xl font-bold">{{ $user->name }}</h2>
                        <p class="text-white/80 font-mono">{{ $user->user_id }}</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-slate-500 mb-1">Email</p>
                    <p class="text-slate-800 font-medium">{{ $user->email ?? 'Not set' }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500 mb-1">Role</p>
                    @if($user->role_assypart === 'admin')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-sm font-medium bg-violet-100 text-violet-700">
                        <i class="fas fa-star text-xs"></i> Admin
                    </span>
                    @elseif($user->role_assypart === 'tech_shopfloor')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-700">
                        <i class="fas fa-industry text-xs"></i> Tech Shopfloor
                    </span>
                    @elseif($user->role_assypart === 'tech_workshop')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-700">
                        <i class="fas fa-wrench text-xs"></i> Tech Workshop
                    </span>
                    @else
                    <span class="text-slate-400 text-sm">-</span>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-slate-500 mb-1">Created At</p>
                    <p class="text-slate-800 font-medium">{{ $user->created_at->format('d M Y, H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500 mb-1">Last Updated</p>
                    <p class="text-slate-800 font-medium">{{ $user->updated_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
