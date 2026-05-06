<x-layouts.app>
    <x-slot:title>Machine Detail</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('machines.index') }}"
                   class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">{{ $machine->mach_number }}</h1>
                    <p class="text-slate-500 mt-1">Machine Detail</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('machines.edit', $machine) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 text-blue-700 border border-blue-200 rounded-xl font-medium hover:bg-blue-100 transition-all">
                    <i class="fas fa-edit"></i> Edit
                </a>
                @if(auth()->user()->isAdmin())
                <form action="{{ route('machines.destroy', $machine) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus machine {{ $machine->mach_number }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-red-50 text-red-700 border border-red-200 rounded-xl font-medium hover:bg-red-100 transition-all">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </form>
                @endif
            </div>
        </div>

        <!-- Detail Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 divide-y divide-slate-100">
            <div class="px-6 py-4 flex items-center justify-between">
                <span class="text-sm font-medium text-slate-500">Mach Number</span>
                <span class="font-semibold text-slate-800">{{ $machine->mach_number }}</span>
            </div>
            <div class="px-6 py-4 flex items-center justify-between">
                <span class="text-sm font-medium text-slate-500">Mach Type</span>
                <span class="text-slate-700">{{ $machine->mach_type }}</span>
            </div>
            <div class="px-6 py-4 flex items-center justify-between">
                <span class="text-sm font-medium text-slate-500">Mach Area</span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                    {{ $machine->mach_area }}
                </span>
            </div>
            <div class="px-6 py-4 flex items-center justify-between">
                <span class="text-sm font-medium text-slate-500">Created By</span>
                <span class="text-slate-700">{{ $machine->creator->name ?? '-' }}</span>
            </div>
            <div class="px-6 py-4 flex items-center justify-between">
                <span class="text-sm font-medium text-slate-500">Created On</span>
                <span class="text-slate-700">{{ $machine->created_at?->format('d/m/Y H:i') }}</span>
            </div>
            <div class="px-6 py-4 flex items-center justify-between">
                <span class="text-sm font-medium text-slate-500">Last Updated</span>
                <span class="text-slate-700">{{ $machine->updated_at?->format('d/m/Y H:i') }}</span>
            </div>
        </div>
    </div>

</x-layouts.app>
