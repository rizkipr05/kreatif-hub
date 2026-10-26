<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('aset/logo.png') }}">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN with configuration -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fdf4ff',
                            100: '#fae8ff',
                            200: '#f5d0fe',
                            300: '#f0abfc',
                            400: '#e879f9',
                            500: '#d946ef',
                            600: '#c026d3',
                            700: '#a21caf',
                            800: '#86198f',
                            900: '#701a75',
                        },
                        primary: {
                            purple: '#8B5CF6',
                            magenta: '#D946EF',
                            pink: '#EC4899',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #fbf9fe;
            color: #1e293b;
        }
        .gradient-brand {
            background: linear-gradient(135deg, #a855f7 0%, #d946ef 50%, #ec4899 100%);
        }
        .gradient-btn {
            background: linear-gradient(90deg, #9333ea 0%, #c026d3 50%, #e11d48 100%);
            transition: all 0.2s ease;
        }
        .gradient-btn:hover {
            opacity: 0.95;
            box-shadow: 0 4px 15px rgba(192, 38, 211, 0.35);
        }
        .sidebar-active {
            background-color: #fae8ff;
            color: #a21caf;
            font-weight: 600;
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e2e8f0;
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #cbd5e1;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased">

    <div class="flex min-h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside id="sidebar" class="w-64 bg-white border-r border-slate-100 flex-shrink-0 flex flex-col justify-between transition-all duration-300 z-30">
            <div>
                <!-- Brand Header -->
                <div class="px-5 py-4 flex items-center justify-between border-b border-slate-50">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 min-w-0">
                        <img src="{{ asset('aset/logo.png') }}" alt="Logo Kreatif Hub" class="h-12 w-auto max-w-[65px] object-contain flex-shrink-0 drop-shadow-xs">
                        <div class="min-w-0">
                            <h1 class="font-extrabold text-slate-800 text-base leading-tight tracking-tight">Kreatif Hub</h1>
                            <p class="text-[11px] text-slate-400 font-medium">Tim Manajemen Tugas</p>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="px-4 py-5">
                    <p class="px-3 text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-2">Menu</p>
                    <nav class="space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('dashboard') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fa-solid fa-table-cells-large text-base w-5 text-center"></i>
                            <span>Dashboard</span>
                        </a>

                        <!-- Tugas -->
                        <a href="{{ route('tasks.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('tasks.*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fa-solid fa-list-check text-base w-5 text-center"></i>
                            <span>Tugas</span>
                        </a>

                        <!-- Pengumpulan Tim -->
                        <a href="{{ route('submissions.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('submissions.*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fa-solid fa-inbox text-base w-5 text-center"></i>
                            <span>Pengumpulan Tim</span>
                        </a>

                        <!-- Notifikasi -->
                        @php
                            $unreadNavCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
                        @endphp
                        <a href="{{ route('notifications.index') }}" class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('notifications.*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <div class="flex items-center gap-3">
                                <i class="fa-regular fa-bell text-base w-5 text-center"></i>
                                <span>Notifikasi</span>
                            </div>
                            @if($unreadNavCount > 0)
                                <span class="bg-pink-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $unreadNavCount > 9 ? '9+' : $unreadNavCount }}</span>
                            @endif
                        </a>

                        <!-- Laporan (Khusus Superadmin) -->
                        @if(Auth::user()->isSuperadmin())
                        <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('reports.*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fa-solid fa-chart-pie text-base w-5 text-center"></i>
                            <span>Laporan</span>
                        </a>
                        @endif

                        <!-- Manajemen User (Khusus Superadmin) -->
                        @if(Auth::user()->isSuperadmin())
                        <a href="{{ route('users.index') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('users.*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fa-solid fa-user-group text-base w-5 text-center"></i>
                            <span>Manajemen User</span>
                        </a>
                        @endif

                        <!-- Profil -->
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-medium transition-colors {{ request()->routeIs('profile.*') ? 'sidebar-active' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <i class="fa-regular fa-circle-user text-base w-5 text-center"></i>
                            <span>Profil</span>
                        </a>
                    </nav>
                </div>
            </div>

            <!-- User Footer in Sidebar -->
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0 shadow-sm border border-purple-100">
                        @else
                            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-purple-600 to-pink-500 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 shadow-sm">
                                {{ Auth::user()->initials }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-xs font-semibold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-purple-600 font-medium capitalize">{{ Auth::user()->role }} ({{ Auth::user()->division }})</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" title="Keluar" class="p-2 text-slate-400 hover:text-rose-600 transition-colors">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto custom-scrollbar">
            <!-- Top Navbar -->
            <header class="h-16 bg-white/80 backdrop-blur-md border-b border-slate-100 px-6 flex items-center justify-between sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <button id="sidebarToggle" class="p-2 text-slate-500 hover:bg-slate-100 rounded-lg transition-colors" title="Toggle Sidebar">
                        <i class="fa-solid fa-bars text-base"></i>
                    </button>
                    <span class="text-sm font-medium text-slate-500 hidden sm:inline">Platform Manajemen Tugas Tim Kreatif</span>
                </div>

                <div class="flex items-center gap-4">
                    <!-- Notification Bell Icon -->
                    <a href="{{ route('notifications.index') }}" class="relative p-2 text-slate-500 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors">
                        <i class="fa-regular fa-bell text-lg"></i>
                        @if($unreadNavCount > 0)
                            <span class="absolute top-1 right-1 w-4 h-4 bg-pink-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center border-2 border-white">
                                {{ $unreadNavCount > 9 ? '9+' : $unreadNavCount }}
                            </span>
                        @endif
                    </a>

                    <!-- User Pill Badge -->
                    <div class="flex items-center gap-2.5 pl-3 border-l border-slate-100">
                        @if(Auth::user()->avatar)
                            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-full object-cover shadow-xs border border-purple-100">
                        @else
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-600 to-pink-500 text-white font-bold text-xs flex items-center justify-center">
                                {{ Auth::user()->initials }}
                            </div>
                        @endif
                        <span class="text-xs font-semibold text-slate-700 hidden md:inline">{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </header>

            <!-- Alerts Banner -->
            @if(session('success'))
            <div class="mx-6 mt-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-500 text-base"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            @endif

            @if(session('error'))
            <div class="mx-6 mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            @endif

            @if($errors->any())
            <div class="mx-6 mt-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
                <div class="font-semibold mb-1 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500"></i> Terjadi kesalahan:
                </div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Main Content Container -->
            <main class="flex-1 p-6 md:p-8">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Sidebar Toggle Script -->
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        if (toggleBtn && sidebar) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('-ml-64');
            });
        }
    </script>

    @stack('scripts')
</body>
</html>

