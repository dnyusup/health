<nav class="flex flex-1 flex-col mt-6">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <p class="px-3 text-[11px] font-semibold text-slate-500 uppercase tracking-widest mb-3">Main Menu</p>
            <ul role="list" class="-mx-2 space-y-2">
                <li>
                    <a href="{{ route('dashboard') }}" 
                       class="nav-item group flex gap-x-3 rounded-xl p-3 text-sm leading-6 font-medium {{ request()->routeIs('dashboard') ? 'active text-white' : 'text-slate-400 hover:text-white' }}">
                        <span class="nav-icon w-10 h-10 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-500/20' : 'bg-slate-700/50 group-hover:bg-slate-600' }} flex items-center justify-center transition-all">
                            <i class="fas fa-home text-lg {{ request()->routeIs('dashboard') ? 'text-blue-400' : '' }}"></i>
                        </span>
                        <span class="flex flex-col justify-center">
                            <span>Dashboard</span>
                            <span class="text-[10px] text-slate-500 font-normal">Overview & Stats</span>
                        </span>
                    </a>
                </li>
                @if(auth()->user()->isAdmin())
                <li>
                    <a href="{{ route('spareparts.index') }}" 
                       class="nav-item group flex gap-x-3 rounded-xl p-3 text-sm leading-6 font-medium {{ request()->routeIs('spareparts.*') ? 'active text-white' : 'text-slate-400 hover:text-white' }}">
                        <span class="nav-icon w-10 h-10 rounded-lg {{ request()->routeIs('spareparts.*') ? 'bg-blue-500/20' : 'bg-slate-700/50 group-hover:bg-slate-600' }} flex items-center justify-center transition-all">
                            <i class="fas fa-box text-lg {{ request()->routeIs('spareparts.*') ? 'text-blue-400' : '' }}"></i>
                        </span>
                        <span class="flex flex-col justify-center">
                            <span>Material Master</span>
                            <span class="text-[10px] text-slate-500 font-normal">Manage inventory</span>
                        </span>
                    </a>
                </li>
                @else
                <li>
                    <a href="{{ route('spareparts.index') }}" 
                       class="nav-item group flex gap-x-3 rounded-xl p-3 text-sm leading-6 font-medium {{ request()->routeIs('spareparts.*') ? 'active text-white' : 'text-slate-400 hover:text-white' }}">
                        <span class="nav-icon w-10 h-10 rounded-lg {{ request()->routeIs('spareparts.*') ? 'bg-blue-500/20' : 'bg-slate-700/50 group-hover:bg-slate-600' }} flex items-center justify-center transition-all">
                            <i class="fas fa-box text-lg {{ request()->routeIs('spareparts.*') ? 'text-blue-400' : '' }}"></i>
                        </span>
                        <span class="flex flex-col justify-center">
                            <span>Material Master</span>
                            <span class="text-[10px] text-slate-500 font-normal">View inventory</span>
                        </span>
                    </a>
                </li>
                @endif
                @if(auth()->user()->role === 'admin')
                <li>
                    <a href="{{ route('categories.index') }}" 
                       class="nav-item group flex gap-x-3 rounded-xl p-3 text-sm leading-6 font-medium {{ request()->routeIs('categories.*') ? 'active text-white' : 'text-slate-400 hover:text-white' }}">
                        <span class="nav-icon w-10 h-10 rounded-lg {{ request()->routeIs('categories.*') ? 'bg-blue-500/20' : 'bg-slate-700/50 group-hover:bg-slate-600' }} flex items-center justify-center transition-all">
                            <i class="fas fa-tags text-lg {{ request()->routeIs('categories.*') ? 'text-blue-400' : '' }}"></i>
                        </span>
                        <span class="flex flex-col justify-center">
                            <span>Categories</span>
                            <span class="text-[10px] text-slate-500 font-normal">Group & organize</span>
                        </span>
                    </a>
                </li>
                @endif
                
            </ul>
                <li>
                    <a href="{{ route('transactions.index') }}" 
                       class="nav-item group flex gap-x-3 rounded-xl p-3 text-sm leading-6 font-medium {{ request()->routeIs('transactions.*') ? 'active text-white' : 'text-slate-400 hover:text-white' }}">
                        <span class="nav-icon w-10 h-10 rounded-lg {{ request()->routeIs('transactions.*') ? 'bg-blue-500/20' : 'bg-slate-700/50 group-hover:bg-slate-600' }} flex items-center justify-center transition-all">
                            <i class="fas fa-exchange-alt text-lg {{ request()->routeIs('transactions.*') ? 'text-blue-400' : '' }}"></i>
                        </span>
                        <span class="flex flex-col justify-center">
                            <span>Stock Transactions</span>
                            <span class="text-[10px] text-slate-500 font-normal">In/Out movements</span>
                        </span>
                    </a>
                </li>
            </ul>
        </li>
        @if(auth()->user()->isAdmin())
        <li>
            <p class="px-3 text-[11px] font-semibold text-slate-500 uppercase tracking-widest mb-3">Administration</p>
            <ul role="list" class="-mx-2 space-y-2">
                <li>
                    <a href="{{ route('users.index') }}" 
                       class="nav-item group flex gap-x-3 rounded-xl p-3 text-sm leading-6 font-medium {{ request()->routeIs('users.*') ? 'active text-white' : 'text-slate-400 hover:text-white' }}">
                        <span class="nav-icon w-10 h-10 rounded-lg {{ request()->routeIs('users.*') ? 'bg-blue-500/20' : 'bg-slate-700/50 group-hover:bg-slate-600' }} flex items-center justify-center transition-all">
                            <i class="fas fa-users-cog text-lg {{ request()->routeIs('users.*') ? 'text-blue-400' : '' }}"></i>
                        </span>
                        <span class="flex flex-col justify-center">
                            <span>User Management</span>
                            <span class="text-[10px] text-slate-500 font-normal">Manage accounts</span>
                        </span>
                    </a>
                </li>
            </ul>
        </li>
        @endif
        <li>
            <p class="px-3 text-[11px] font-semibold text-slate-500 uppercase tracking-widest mb-3">Account</p>
            <ul role="list" class="-mx-2 space-y-2">
                <li>
                    <a href="{{ route('profile.show') }}" 
                       class="nav-item group flex gap-x-3 rounded-xl p-3 text-sm leading-6 font-medium {{ request()->routeIs('profile.*') ? 'active text-white' : 'text-slate-400 hover:text-white' }}">
                        <span class="nav-icon w-10 h-10 rounded-lg {{ request()->routeIs('profile.*') ? 'bg-blue-500/20' : 'bg-slate-700/50 group-hover:bg-slate-600' }} flex items-center justify-center transition-all">
                            <i class="fas fa-user-circle text-lg {{ request()->routeIs('profile.*') ? 'text-blue-400' : '' }}"></i>
                        </span>
                        <span class="flex flex-col justify-center">
                            <span>My Profile</span>
                            <span class="text-[10px] text-slate-500 font-normal">View & edit profile</span>
                        </span>
                    </a>
                </li>
            </ul>
        </li>
        <li class="mt-auto">
            <div class="rounded-2xl bg-slate-800/50 p-4 border border-slate-700/50">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-warehouse text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">SPART</p>
                        <p class="text-[10px] text-slate-500">v1.0.0</p>
                    </div>
                </div>
                <p class="text-[11px] text-slate-500 leading-relaxed">
                    Sparepart Inventory Management
                </p>
            </div>
        </li>
    </ul>
</nav>
