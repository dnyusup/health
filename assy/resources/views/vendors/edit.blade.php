<x-layouts.app>
    <x-slot:title>Edit Vendor</x-slot:title>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-4">
            <a href="{{ route('vendors.index') }}"
               class="p-2 text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Edit Vendor</h1>
                <p class="text-slate-500 mt-1">{{ $vendor->vendor_id }} &mdash; {{ $vendor->vendor_name }}</p>
            </div>
        </div>

        <!-- Form -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <form action="{{ route('vendors.update', $vendor) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Vendor ID -->
                    <div>
                        <label for="vendor_id" class="block text-sm font-medium text-slate-700 mb-2">
                            Vendor ID <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="vendor_id"
                               name="vendor_id"
                               value="{{ old('vendor_id', $vendor->vendor_id) }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all uppercase @error('vendor_id') border-red-500 @enderror"
                               required>
                        @error('vendor_id')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Vendor Name -->
                    <div>
                        <label for="vendor_name" class="block text-sm font-medium text-slate-700 mb-2">
                            Nama Vendor <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               id="vendor_name"
                               name="vendor_name"
                               value="{{ old('vendor_name', $vendor->vendor_name) }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('vendor_name') border-red-500 @enderror"
                               required>
                        @error('vendor_name')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PIC Vendor -->
                    <div>
                        <label for="pic_vendor" class="block text-sm font-medium text-slate-700 mb-2">
                            PIC Vendor
                        </label>
                        <input type="text"
                               id="pic_vendor"
                               name="pic_vendor"
                               value="{{ old('pic_vendor', $vendor->pic_vendor) }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('pic_vendor') border-red-500 @enderror"
                               placeholder="Nama kontak person">
                        @error('pic_vendor')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Telp -->
                    <div>
                        <label for="telp" class="block text-sm font-medium text-slate-700 mb-2">
                            Telepon
                        </label>
                        <input type="text"
                               id="telp"
                               name="telp"
                               value="{{ old('telp', $vendor->telp) }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('telp') border-red-500 @enderror"
                               placeholder="e.g. 08123456789">
                        @error('telp')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div class="md:col-span-2">
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                            Email
                        </label>
                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email', $vendor->email) }}"
                               class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 outline-none transition-all @error('email') border-red-500 @enderror"
                               placeholder="email@vendor.com">
                        @error('email')
                        <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('vendors.index') }}"
                       class="px-5 py-2.5 text-slate-600 bg-slate-100 rounded-xl font-medium hover:bg-slate-200 transition-all">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl font-medium hover:from-blue-600 hover:to-blue-700 transition-all shadow-lg shadow-blue-500/25">
                        <i class="fas fa-save mr-2"></i> Update Vendor
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
