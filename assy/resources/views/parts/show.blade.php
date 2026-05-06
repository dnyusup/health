<x-layouts.app>
    <x-slot:title>Part Detail - {{ $part->part_id }}</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('parts.index') }}"
               class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Part Detail</h1>
                <p class="text-slate-500 mt-1">{{ $part->part_id }}</p>
            </div>
        </div>

        <!-- Detail Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Part ID</p>
                    <p class="text-lg font-bold text-blue-600">{{ $part->part_id }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Category</p>
                    <p class="text-slate-800 font-medium">{{ $part->category }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Part Name</p>
                    <p class="text-slate-800 font-medium">{{ $part->part_name }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Part Detail</p>
                    <p class="text-slate-800">{{ $part->part_detail ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Created By</p>
                    <p class="text-slate-800">{{ $part->creator->name ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Created On</p>
                    <p class="text-slate-800">{{ $part->created_at->format('m/d/Y H:i:s') }}</p>
                </div>
            </div>

            @if(auth()->user()->isAdmin())
            <div class="flex items-center gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('parts.edit', $part) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 text-amber-600 rounded-xl font-medium hover:bg-amber-100 transition-all">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <form action="{{ route('parts.destroy', $part) }}" method="POST" onsubmit="return confirm('Hapus part ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 rounded-xl font-medium hover:bg-red-100 transition-all">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</x-layouts.app>
