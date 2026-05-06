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
                <li>
                    <a href="{{ route('parts.index') }}" 
                       class="nav-item group flex gap-x-3 rounded-xl p-3 text-sm leading-6 font-medium {{ request()->routeIs('parts.*') ? 'active text-white' : 'text-slate-400 hover:text-white' }}">
                        <span class="nav-icon w-10 h-10 rounded-lg {{ request()->routeIs('parts.*') ? 'bg-blue-500/20' : 'bg-slate-700/50 group-hover:bg-slate-600' }} flex items-center justify-center transition-all">
                            <i class="fas fa-cog text-lg {{ request()->routeIs('parts.*') ? 'text-blue-400' : '' }}"></i>
                        </span>
                        <span class="flex flex-col justify-center">
                            <span>Part Registration</span>
                            <span class="text-[10px] text-slate-500 font-normal">Manage parts</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('machines.index') }}" 
                       class="nav-item group flex gap-x-3 rounded-xl p-3 text-sm leading-6 font-medium {{ request()->routeIs('machines.*') ? 'active text-white' : 'text-slate-400 hover:text-white' }}">
                        <span class="nav-icon w-10 h-10 rounded-lg {{ request()->routeIs('machines.*') ? 'bg-blue-500/20' : 'bg-slate-700/50 group-hover:bg-slate-600' }} flex items-center justify-center transition-all">
                            <i class="fas fa-industry text-lg {{ request()->routeIs('machines.*') ? 'text-blue-400' : '' }}"></i>
                        </span>
                        <span class="flex flex-col justify-center">
                            <span>Machine Registration</span>
                            <span class="text-[10px] text-slate-500 font-normal">Manage machines</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('work-orders.index') }}" 
                       class="nav-item group flex gap-x-3 rounded-xl p-3 text-sm leading-6 font-medium {{ request()->routeIs('work-orders.*') ? 'active text-white' : 'text-slate-400 hover:text-white' }}">
                        <span class="nav-icon w-10 h-10 rounded-lg {{ request()->routeIs('work-orders.*') ? 'bg-blue-500/20' : 'bg-slate-700/50 group-hover:bg-slate-600' }} flex items-center justify-center transition-all">
                            <i class="fas fa-clipboard-list text-lg {{ request()->routeIs('work-orders.*') ? 'text-blue-400' : '' }}"></i>
                        </span>
                        <span class="flex flex-col justify-center">
                            <span>Work Order Bongkar</span>
                            <span class="text-[10px] text-slate-500 font-normal">Pembongkaran mesin</span>
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
                        <i class="fas fa-cogs text-white"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-white">ASSY PART</p>
                        <p class="text-[10px] text-slate-500">v1.0.0</p>
                    </div>
                </div>
                <p class="text-[11px] text-slate-500 leading-relaxed">
                    Assy Part Application
                </p>
            </div>
        </li>
    </ul>
</nav>
