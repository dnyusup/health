<x-layouts.app>
    <x-slot:title>Vendor Registration</x-slot:title>
    <x-slot:header>Vendor Registration</x-slot:header>

    <div class="space-y-6">
        <!-- Filter & Search -->
        <form method="GET" action="{{ route('vendors.index') }}" class="mb-4">
            <div class="flex flex-col sm:flex-row gap-3 items-center">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search vendor ID, nama, PIC, email, telp..."
                       class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 w-80">
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-all">
                    <i class="fas fa-search"></i> Filter
                </button>
                @if(request('search'))
                <a href="{{ route('vendors.index') }}"
                   class="inline-flex items-center gap-2 px-3 py-2 bg-slate-200 text-slate-700 rounded-lg font-medium hover:bg-slate-300 transition-all">
                    <i class="fas fa-times"></i> Reset
                </a>
                @endif
            </div>
        </form>

        <!-- Actions -->
        <div class="flex items-center gap-3 flex-wrap justify-end">
            <a href="{{ route('vendors.create') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/25">
                <i class="fas fa-plus"></i> Add Vendor
            </a>
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

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Created On</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Vendor ID</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Nama Vendor</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">PIC Vendor</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Email</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Telp</th>
                            <th class="px-4 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Created By</th>
                            <th class="px-4 py-4 text-center text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($vendors as $vendor)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3 text-sm text-slate-500 whitespace-nowrap">
                                {{ $vendor->created_at?->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('vendors.show', $vendor) }}"
                                   class="font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $vendor->vendor_id }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-700 font-medium">{{ $vendor->vendor_name }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $vendor->pic_vendor ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                @if($vendor->email)
                                    <a href="mailto:{{ $vendor->email }}" class="text-blue-500 hover:underline">{{ $vendor->email }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $vendor->telp ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-500">{{ $vendor->creator->name ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('vendors.edit', $vendor) }}"
                                       class="p-1.5 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                       title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                    <form action="{{ route('vendors.destroy', $vendor) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus vendor {{ addslashes($vendor->vendor_name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Delete">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                                <i class="fas fa-truck text-4xl mb-3 block opacity-30"></i>
                                <p class="text-sm">Belum ada data vendor.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($vendors->hasPages())
            <div class="px-4 py-4 border-t border-slate-200">
                {{ $vendors->links() }}
            </div>
            @endif
        </div>
    </div>

</x-layouts.app>
