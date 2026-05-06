<x-layouts.app>
    <x-slot:title>Part Registration</x-slot:title>

    <div class="space-y-6">
        <!-- Filter & Search -->
        <form method="GET" action="{{ route('parts.index') }}" class="mb-4">
            <div class="flex flex-col sm:flex-row gap-3 items-center">
                <div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search part id, name, category..." class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 w-64">
                </div>
                <div>
                    <select name="category" class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-all">
                        <i class="fas fa-search"></i> <span>Filter</span>
                    </button>
                </div>
                @if(request('search') || request('category'))
                <div>
                    <a href="{{ route('parts.index') }}" class="inline-flex items-center gap-2 px-3 py-2 bg-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-300 transition-all">
                        <i class="fas fa-times"></i> <span>Reset</span>
                    </a>
                </div>
                @endif
            </div>
        </form>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Part Registration</h1>
                <p class="text-slate-500 mt-1">Manage registered parts</p>
            </div>
            @if(auth()->user()->isAdmin())
            <a href="{{ route('parts.create') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/25">
                <i class="fas fa-plus"></i>
                <span>Add Part</span>
            </a>
            @endif
        </div>

        <!-- Messages -->
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

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Created On</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Part ID</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Category</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Part Name</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Part Detail</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Created By</th>
                            @if(auth()->user()->isAdmin())
                            <th class="px-4 py-4 text-right text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($parts as $part)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-4 text-sm text-slate-500 whitespace-nowrap">
                                {{ $part->created_at->format('m/d/Y H:i:s') }}
                            </td>
                            <td class="px-4 py-4">
                                <a href="{{ route('parts.show', $part) }}" class="text-sm font-semibold text-blue-600 hover:underline">
                                    {{ $part->part_id }}
                                </a>
                            </td>
                            <td class="px-4 py-4 text-sm text-slate-700">{{ $part->category }}</td>
                            <td class="px-4 py-4 text-sm text-slate-700">{{ $part->part_name }}</td>
                            <td class="px-4 py-4 text-sm text-slate-500">{{ $part->part_detail ?? '-' }}</td>
                            <td class="px-4 py-4 text-sm text-slate-700">{{ $part->creator->name ?? '-' }}</td>
                            @if(auth()->user()->isAdmin())
                            <td class="px-4 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('parts.edit', $part) }}"
                                       class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 text-amber-600 rounded-lg text-xs font-medium hover:bg-amber-100 transition-colors">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <form action="{{ route('parts.destroy', $part) }}" method="POST" onsubmit="return confirm('Hapus part ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium hover:bg-red-100 transition-colors">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ auth()->user()->isAdmin() ? 7 : 6 }}" class="px-6 py-12 text-center text-slate-400">
                                <i class="fas fa-box-open text-4xl mb-3 block"></i>
                                No parts registered yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($parts->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $parts->links() }}
            </div>
            @endif
        </div>
    </div>
</x-layouts.app>
