<x-layouts.app>
    <x-slot:title>Users</x-slot:title>
    
    <div class="space-y-6">
                <!-- Filter & Search -->
                <form method="GET" action="{{ route('users.index') }}" class="mb-4">
                    <div class="flex flex-col sm:flex-row gap-3 items-center">
                        <div>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, user id, email..." class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 w-64">
                        </div>
                        <div>
                            <select name="role" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">All Roles</option>
                                <option value="admin" {{ request('role')==='admin' ? 'selected' : '' }}>Admin</option>
                                <option value="user" {{ request('role')==='user' ? 'selected' : '' }}>User</option>
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-all">
                                <i class="fas fa-search"></i> <span>Filter</span>
                            </button>
                        </div>
                        @if(request('search') || request('role'))
                        <div>
                            <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-300 transition-all">
                                <i class="fas fa-times"></i> <span>Reset</span>
                            </a>
                        </div>
                        @endif
                    </div>
                </form>
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">User Management</h1>
                <p class="text-slate-500 mt-1">Manage system users and their roles</p>
            </div>
            <a href="{{ route('users.create') }}" 
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/25">
                <i class="fas fa-plus"></i>
                <span>Add User</span>
            </a>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200">
            <div class="flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-500"></i>
                <p class="text-emerald-700">{{ session('success') }}</p>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-200">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-500"></i>
                <p class="text-red-700">{{ session('error') }}</p>
            </div>
        </div>
        @endif

        <!-- Users Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">User</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">User ID</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Created</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($users as $user)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $user->isAdmin() ? 'from-amber-400 to-orange-500' : 'from-slate-400 to-slate-500' }} flex items-center justify-center text-white font-semibold">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <span class="font-medium text-slate-800">{{ $user->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm text-slate-600 bg-slate-100 px-2 py-1 rounded">{{ $user->user_id }}</span>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $user->email ?? '-' }}</td>
                            <td class="px-6 py-4">
                                @if($user->isAdmin())
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                    <i class="fas fa-crown text-[10px]"></i>
                                    Admin
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                                    <i class="fas fa-user text-[10px]"></i>
                                    User
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-500 text-sm">{{ $user->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('users.show', $user) }}" 
                                       class="p-2 text-slate-400 hover:text-blue-500 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $user) }}" 
                                       class="p-2 text-slate-400 hover:text-amber-500 hover:bg-amber-50 rounded-lg transition-colors"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                                        <i class="fas fa-users text-2xl text-slate-400"></i>
                                    </div>
                                    <p class="text-slate-500 font-medium">No users found</p>
                                    <p class="text-slate-400 text-sm mt-1">Get started by adding your first user</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $users->links() }}
            </div>
            @endif
        </div>
    </div>
</x-layouts.app>
