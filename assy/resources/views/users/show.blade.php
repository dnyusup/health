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
                    @if($user->isAdmin())
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-amber-100 text-amber-700">
                        <i class="fas fa-crown"></i>
                        Admin
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-slate-100 text-slate-600">
                        <i class="fas fa-user"></i>
                        User
                    </span>
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

        <!-- Recent Transactions -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800">Recent Transactions</h3>
                <p class="text-slate-500 text-sm mt-1">Stock transactions made by this user</p>
            </div>
            
            @if($user->stockTransactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Sparepart</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Quantity</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Reference</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($user->stockTransactions->take(10) as $transaction)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $transaction->created_at->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                @if($transaction->type === 'in')
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                    <i class="fas fa-arrow-down"></i> IN
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                    <i class="fas fa-arrow-up"></i> OUT
                                </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-800">{{ $transaction->sparepart->description ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-800">{{ $transaction->quantity }}</td>
                            <td class="px-6 py-4 text-sm text-slate-600">{{ $transaction->reference_no ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exchange-alt text-2xl text-slate-400"></i>
                </div>
                <p class="text-slate-500">No transactions yet</p>
            </div>
            @endif
        </div>
    </div>
</x-layouts.app>
