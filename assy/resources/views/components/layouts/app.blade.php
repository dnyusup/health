<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'SPART' }} - Sparepart Inventory</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(["resources/css/app.css", "resources/js/app.js"])
</head>
<body class="h-full font-sans gradient-bg" x-data="{ sidebarOpen: false }">
    <div class="min-h-full">
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" x-cloak
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm z-40 lg:hidden"
             @click="sidebarOpen = false"></div>

        <!-- Mobile sidebar -->
        <div x-show="sidebarOpen" x-cloak
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 z-50 w-72 glass-sidebar lg:hidden shadow-2xl overflow-x-hidden overflow-y-auto">
            <!-- Decorative circles -->
            <div class="sidebar-decoration w-32 h-32 -top-10 -right-10"></div>
            <div class="sidebar-decoration w-24 h-24 bottom-20 -left-10"></div>
            
            <div class="flex h-20 shrink-0 items-center justify-between px-6 border-b border-slate-700/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                        <i class="fas fa-cogs text-white text-lg"></i>
                    </div>
                    <span class="text-2xl font-bold logo-text">SPART</span>
                </div>
                <button @click="sidebarOpen = false" class="w-8 h-8 rounded-lg bg-slate-700/50 flex items-center justify-center text-slate-400 hover:bg-slate-600 hover:text-white transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            @include('components.layouts.sidebar-nav')
        </div>

        <!-- Desktop sidebar -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto overflow-x-hidden glass-sidebar px-6 pb-4 custom-scrollbar relative shadow-2xl">
                <!-- Decorative circles -->
                <div class="sidebar-decoration w-40 h-40 -top-16 -right-16"></div>
                <div class="sidebar-decoration w-32 h-32 bottom-32 -left-16"></div>
                <div class="sidebar-decoration w-20 h-20 top-1/2 right-0"></div>
                
                <div class="flex h-20 shrink-0 items-center border-b border-slate-700/50">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                            <i class="fas fa-cogs text-white text-xl"></i>
                        </div>
                        <div>
                            <span class="text-2xl font-bold logo-text">SPART</span>
                            <p class="text-[10px] text-slate-500 tracking-wider uppercase">Inventory System</p>
                        </div>
                    </div>
                </div>
                @include('components.layouts.sidebar-nav')
            </div>
        </div>

        <!-- Main content -->
        <div class="lg:pl-72">
            <!-- Top navbar -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 glass-navbar px-4 shadow-lg sm:gap-x-6 sm:px-6 lg:px-8">
                <button type="button" class="lg:hidden -m-2.5 p-2.5 text-gray-600 hover:text-primary-600 transition-colors" @click="sidebarOpen = true">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- Separator -->
                <div class="h-6 w-px bg-gray-300/50 lg:hidden"></div>

                <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                    <div class="flex flex-1 items-center">
                        <h1 class="text-lg font-semibold bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">{{ $header ?? 'Dashboard' }}</h1>
                    </div>
                    <div class="flex items-center gap-x-4 lg:gap-x-6">
                        <div class="hidden sm:flex items-center gap-2 px-4 py-2 rounded-full bg-slate-100 border border-slate-200">
                            <i class="fas fa-calendar-alt text-slate-500"></i>
                            <span class="text-sm font-medium text-slate-600">{{ now()->format('d M Y') }}</span>
                        </div>
                        
                        <!-- User dropdown -->
                        <div x-data="{ userOpen: false }" class="relative">
                            <button @click="userOpen = !userOpen" class="flex items-center gap-3 px-3 py-2 rounded-xl hover:bg-slate-100 transition-colors">
                                <div class="w-9 h-9 rounded-full bg-gradient-to-br {{ auth()->user()->isAdmin() ? 'from-amber-500 to-orange-500' : 'from-slate-600 to-slate-700' }} flex items-center justify-center shadow-lg">
                                    <span class="text-white text-sm font-semibold">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-sm font-medium text-slate-800">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-500">{{ auth()->user()->isAdmin() ? 'Administrator' : 'User' }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-slate-400 text-xs hidden md:block"></i>
                            </button>
                            
                            <div x-show="userOpen" 
                                 x-cloak
                                 @click.outside="userOpen = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-56 rounded-xl bg-white shadow-lg border border-slate-200 py-2 z-50">
                                <div class="px-4 py-3 border-b border-slate-100">
                                    <p class="text-sm font-medium text-slate-800">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-500 font-mono">{{ auth()->user()->user_id }}</p>
                                </div>
                                <a href="{{ route('profile.show') }}" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-slate-700 hover:bg-slate-50 transition-colors">
                                    <i class="fas fa-user-circle"></i>
                                    <span>My Profile</span>
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt"></i>
                                        <span>Sign Out</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page content -->
            <main class="py-6">
                <div class="px-4 sm:px-6 lg:px-8">
                    @if(session('success'))
                        <div class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 border border-green-200" x-data="{ show: true }" x-show="show">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    {{ session('success') }}
                                </div>
                                <button @click="show = false" class="text-green-600 hover:text-green-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800 border border-red-200" x-data="{ show: true }" x-show="show">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-exclamation-circle mr-2"></i>
                                    {{ session('error') }}
                                </div>
                                <button @click="show = false" class="text-red-600 hover:text-red-800">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-800 border border-red-200">
                            <div class="flex items-center mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <span class="font-semibold">Errors found:</span>
                            </div>
                            <ul class="list-disc list-inside text-sm">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>
</body>
</html>
