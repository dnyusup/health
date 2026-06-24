<nav class="flex flex-1 flex-col mt-4">
    <ul role="list" class="flex flex-1 flex-col gap-y-6">

        <!-- Main Menu -->
        <li>
            <p class="px-2 text-[10px] font-bold text-teal-400/60 uppercase tracking-[0.15em] mb-2">Main Menu</p>
            <ul role="list" class="space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('dashboard') ? 'active text-white' : 'text-slate-300 hover:text-white' }}">
                        <span class="nav-icon flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center
                            {{ request()->routeIs('dashboard') ? 'bg-teal-500 shadow-lg shadow-teal-500/40' : 'bg-white/8 text-slate-400' }}">
                            <i class="fas fa-gauge-high text-sm {{ request()->routeIs('dashboard') ? 'text-white' : '' }}"></i>
                        </span>
                        <span class="flex flex-col min-w-0">
                            <span class="text-sm font-semibold truncate">Dashboard</span>
                            <span class="text-[11px] font-normal {{ request()->routeIs('dashboard') ? 'text-teal-300' : 'text-slate-500' }}">Overview & Stats</span>
                        </span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('health-checks.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('health-checks.*') ? 'active text-white' : 'text-slate-300 hover:text-white' }}">
                        <span class="nav-icon flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center
                            {{ request()->routeIs('health-checks.*') ? 'bg-emerald-500 shadow-lg shadow-emerald-500/40' : 'bg-white/8 text-slate-400' }}">
                            <i class="fas fa-heartbeat text-sm {{ request()->routeIs('health-checks.*') ? 'text-white' : '' }}"></i>
                        </span>
                        <span class="flex flex-col min-w-0">
                            <span class="text-sm font-semibold truncate">Health Monitoring</span>
                            <span class="text-[11px] font-normal {{ request()->routeIs('health-checks.*') ? 'text-emerald-300' : 'text-slate-500' }}">Data kesehatan</span>
                        </span>
                    </a>
                </li>
            </ul>
        </li>

        @if(auth()->user()->isAdmin())
        <!-- Administration -->
        <li>
            <p class="px-2 text-[10px] font-bold text-teal-400/60 uppercase tracking-[0.15em] mb-2">Administration</p>
            <ul role="list" class="space-y-1">
                <li>
                    <a href="{{ route('users.index') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('users.*') ? 'active text-white' : 'text-slate-300 hover:text-white' }}">
                        <span class="nav-icon flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center
                            {{ request()->routeIs('users.*') ? 'bg-violet-500 shadow-lg shadow-violet-500/40' : 'bg-white/8 text-slate-400' }}">
                            <i class="fas fa-users-cog text-sm {{ request()->routeIs('users.*') ? 'text-white' : '' }}"></i>
                        </span>
                        <span class="flex flex-col min-w-0">
                            <span class="text-sm font-semibold truncate">User Management</span>
                            <span class="text-[11px] font-normal {{ request()->routeIs('users.*') ? 'text-violet-300' : 'text-slate-500' }}">Manage accounts</span>
                        </span>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        <!-- Account -->
        <li>
            <p class="px-2 text-[10px] font-bold text-teal-400/60 uppercase tracking-[0.15em] mb-2">Account</p>
            <ul role="list" class="space-y-1">
                <li>
                    <a href="{{ route('profile.show') }}"
                       class="nav-item flex items-center gap-3 px-3 py-2.5 {{ request()->routeIs('profile.*') ? 'active text-white' : 'text-slate-300 hover:text-white' }}">
                        <span class="nav-icon flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center
                            {{ request()->routeIs('profile.*') ? 'bg-sky-500 shadow-lg shadow-sky-500/40' : 'bg-white/8 text-slate-400' }}">
                            <i class="fas fa-user-circle text-sm {{ request()->routeIs('profile.*') ? 'text-white' : '' }}"></i>
                        </span>
                        <span class="flex flex-col min-w-0">
                            <span class="text-sm font-semibold truncate">My Profile</span>
                            <span class="text-[11px] font-normal {{ request()->routeIs('profile.*') ? 'text-sky-300' : 'text-slate-500' }}">View & edit profile</span>
                        </span>
                    </a>
                </li>
            </ul>
        </li>

        <!-- Footer -->
        <li class="mt-auto">
            <div class="rounded-xl bg-gradient-to-r from-teal-500/15 to-emerald-500/10 border border-teal-500/20 p-3.5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-teal-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-teal-500/30 flex-shrink-0">
                        <i class="fas fa-heartbeat text-white text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-white">Health Trace</p>
                        <p class="text-[11px] text-teal-400">v1.0.0 · Health Monitor</p>
                    </div>
                </div>
            </div>
        </li>

    </ul>
</nav>
