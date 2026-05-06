<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - SPART</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { font-family: 'Inter', sans-serif; }
        
        .gradient-bg {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 50%, #1e293b 100%);
            min-height: 100vh;
        }
        
        .glass-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(71, 85, 105, 0.3);
        }
        
        .input-field {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(71, 85, 105, 0.3);
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            background: rgba(15, 23, 42, 0.8);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .btn-login {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
        }
        
        .floating-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }
        
        .shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
        }
        
        .shape-1 {
            width: 400px;
            height: 400px;
            background: #3b82f6;
            top: -100px;
            right: -100px;
        }
        
        .shape-2 {
            width: 300px;
            height: 300px;
            background: #8b5cf6;
            bottom: -50px;
            left: -50px;
        }
        
        .shape-3 {
            width: 200px;
            height: 200px;
            background: #06b6d4;
            top: 50%;
            left: 10%;
        }
    </style>
</head>
<body class="gradient-bg">
    <div class="floating-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg mb-4">
                    <i class="fas fa-warehouse text-white text-2xl"></i>
                </div>
                <h1 class="text-3xl font-bold text-white">SPART</h1>
                <p class="text-slate-400 mt-2">Sparepart Inventory Management</p>
            </div>
            
            <!-- Login Card -->
            <div class="glass-card rounded-2xl p-8">
                <h2 class="text-xl font-semibold text-white mb-6">Sign In</h2>
                
                @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                        <p class="text-red-400 text-sm">{{ $errors->first() }}</p>
                    </div>
                </div>
                @endif
                
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    
                    <!-- User ID -->
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-slate-300 mb-2">
                            User ID
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-user text-slate-500"></i>
                            </div>
                            <input type="text" 
                                   id="user_id" 
                                   name="user_id" 
                                   value="{{ old('user_id') }}"
                                   class="input-field w-full pl-11 pr-4 py-3 rounded-xl text-white placeholder-slate-500 focus:outline-none"
                                   placeholder="Enter your User ID"
                                   required
                                   autofocus>
                        </div>
                    </div>
                    
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-300 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-slate-500"></i>
                            </div>
                            <input type="password" 
                                   id="password" 
                                   name="password" 
                                   class="input-field w-full pl-11 pr-4 py-3 rounded-xl text-white placeholder-slate-500 focus:outline-none"
                                   placeholder="Enter your password"
                                   required>
                        </div>
                    </div>
                    
                    <!-- Remember Me -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   name="remember" 
                                   class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-blue-500 focus:ring-blue-500 focus:ring-offset-0">
                            <span class="ml-2 text-sm text-slate-400">Remember me</span>
                        </label>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn-login w-full py-3 rounded-xl text-white font-semibold">
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign In
                    </button>
                </form>
            </div>
            
            <!-- Footer -->
            <p class="text-center text-slate-500 text-sm mt-6">
                &copy; {{ date('Y') }} SPART. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
