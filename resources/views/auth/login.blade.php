<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Kreatif Hub</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('aset/logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #fbf7fd 0%, #f7eff9 50%, #fbf3fa 100%);
        }
        .gradient-btn {
            background: linear-gradient(90deg, #9333ea 0%, #c026d3 50%, #e11d48 100%);
            transition: all 0.25s ease;
        }
        .gradient-btn:hover {
            opacity: 0.95;
            box-shadow: 0 4px 18px rgba(192, 38, 211, 0.4);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 md:p-10">

    <div class="w-full max-w-5xl grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-center">
        <!-- Left Hero Section -->
        <div class="lg:col-span-6 space-y-6">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <img src="{{ asset('aset/logo.png') }}" alt="Logo Kreatif Hub" class="h-14 sm:h-16 w-auto max-w-[90px] object-contain flex-shrink-0 drop-shadow-xs">
                <div>
                    <h2 class="font-extrabold text-slate-900 text-xl leading-tight tracking-tight">Kreatif Hub</h2>
                    <p class="text-xs text-slate-400 font-medium">Tim Manajemen Tugas</p>
                </div>
            </div>

            <!-- Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-purple-100/80 border border-purple-200 text-purple-700 text-xs font-semibold">
                <i class="fa-solid fa-wand-magic-sparkles text-[11px]"></i>
                <span>Untuk tim kreatif modern</span>
            </div>

            <!-- Headline -->
            <h1 class="text-4xl sm:text-5xl lg:text-[52px] font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                Kelola jobdesk tim <br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-600 via-fuchsia-600 to-pink-500">
                    tanpa drama.
                </span>
            </h1>

            <!-- Description -->
            <p class="text-slate-500 text-base sm:text-lg leading-relaxed max-w-lg">
                Satu tempat untuk menugaskan, mengirim hasil, mereview, dan merevisi pekerjaan tim desain, foto, video, dan copywriting.
            </p>

            <!-- Decorative Dots Indicator -->
            <div class="flex items-center gap-2 pt-2">
                <span class="w-3 h-3 rounded-full bg-purple-600"></span>
                <span class="w-8 h-3 rounded-full bg-fuchsia-500"></span>
                <span class="w-3 h-3 rounded-full bg-pink-500"></span>
            </div>
        </div>

        <!-- Right Card: Login & Register Form -->
        <div class="lg:col-span-6">
            <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-[0_10px_35px_rgba(217,70,239,0.08)] border border-purple-50/80">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-slate-900 tracking-tight">Selamat datang</h2>
                    <p class="text-sm text-slate-500 mt-1">Masuk untuk melanjutkan ke Kreatif Hub</p>
                </div>

                <!-- Tab Buttons -->
                <div class="flex bg-slate-100/80 p-1 rounded-xl mb-6 text-sm font-semibold">
                    <button id="tabLoginBtn" type="button" onclick="switchTab('login')" class="flex-1 py-2 rounded-lg bg-white text-slate-900 shadow-sm transition-all text-center">
                        Masuk
                    </button>
                    <button id="tabRegisterBtn" type="button" onclick="switchTab('register')" class="flex-1 py-2 rounded-lg text-slate-500 hover:text-slate-800 transition-all text-center">
                        Daftar (Owner)
                    </button>
                </div>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mb-5 p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs">
                        @foreach($errors->all() as $error)
                            <p class="flex items-center gap-1.5"><i class="fa-solid fa-circle-exclamation"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @if(session('success'))
                    <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs flex items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif

                <!-- Tab 1: Form Masuk (Dengan Username & Password) -->
                <form id="formLogin" action="{{ route('login.post') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" required autofocus
                            class="w-full px-4 py-2.5 bg-purple-50/40 border border-purple-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white transition-all text-slate-800"
                            placeholder="Masukkan username Anda">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white transition-all text-slate-800"
                            placeholder="••••••••">
                    </div>

                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="rounded text-purple-600 focus:ring-purple-400">
                            <span>Ingat saya</span>
                        </label>
                        <span class="text-purple-600 hover:underline cursor-pointer">Lupa password?</span>
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl text-white font-semibold text-sm gradient-btn shadow-md mt-2">
                        Masuk
                    </button>
                </form>

                <!-- Tab 2: Form Daftar Owner -->
                <form id="formRegister" action="{{ route('register.owner') }}" method="POST" class="space-y-3.5 hidden">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                        <input type="text" name="name" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800" placeholder="Nama Anda / Owner">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Username *</label>
                        <input type="text" name="username" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800" placeholder="contoh: owner_semesta">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp / HP</label>
                        <input type="text" name="phone" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800" placeholder="08xxxxxxxxxx">
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Password *</label>
                            <input type="password" name="password" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800" placeholder="Min. 6 digit">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Ulangi Password *</label>
                            <input type="password" name="password_confirmation" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800" placeholder="Konfirmasi">
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-400">Pendaftaran ini khusus untuk akun Superadmin / Owner Tim Kreatif.</p>

                    <button type="submit" class="w-full py-3 rounded-xl text-white font-semibold text-sm gradient-btn shadow-md mt-2">
                        Daftar Sebagai Owner
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function switchTab(tab) {
            const tabLoginBtn = document.getElementById('tabLoginBtn');
            const tabRegisterBtn = document.getElementById('tabRegisterBtn');
            const formLogin = document.getElementById('formLogin');
            const formRegister = document.getElementById('formRegister');

            if (tab === 'login') {
                tabLoginBtn.className = "flex-1 py-2 rounded-lg bg-white text-slate-900 shadow-sm transition-all text-center";
                tabRegisterBtn.className = "flex-1 py-2 rounded-lg text-slate-500 hover:text-slate-800 transition-all text-center";
                formLogin.classList.remove('hidden');
                formRegister.classList.add('hidden');
            } else {
                tabRegisterBtn.className = "flex-1 py-2 rounded-lg bg-white text-slate-900 shadow-sm transition-all text-center";
                tabLoginBtn.className = "flex-1 py-2 rounded-lg text-slate-500 hover:text-slate-800 transition-all text-center";
                formRegister.classList.remove('hidden');
                formLogin.classList.add('hidden');
            }
        }
    </script>
</body>
</html>

