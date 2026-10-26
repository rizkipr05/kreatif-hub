@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Profil Saya</h1>
        <p class="text-sm text-slate-500 mt-1">Perbarui foto profil, informasi akun, dan keamanan kata sandi Anda.</p>
    </div>

    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] space-y-8">
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Foto Profil Section -->
            <div class="pb-6 border-b border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Foto Profil</h3>

                <div class="flex flex-col sm:flex-row sm:items-center gap-6">
                    <!-- Avatar Preview Container -->
                    <div class="relative group w-24 h-24 flex-shrink-0">
                        <div id="avatarPreviewContainer" class="w-24 h-24 rounded-3xl overflow-hidden shadow-md shadow-purple-100 border-2 border-purple-200 bg-gradient-to-tr from-purple-600 via-fuchsia-600 to-pink-500 flex items-center justify-center text-white font-bold text-2xl">
                            @if($user->avatar)
                                <img id="avatarImage" src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                            @else
                                <span id="avatarInitials">{{ $user->initials }}</span>
                                <img id="avatarImage" src="" alt="Preview" class="w-full h-full object-cover hidden">
                            @endif
                        </div>

                        <!-- Camera overlay badge -->
                        <label for="avatarInput" class="absolute -bottom-1.5 -right-1.5 w-8 h-8 rounded-full bg-purple-600 hover:bg-purple-700 text-white flex items-center justify-center cursor-pointer shadow-md transition-transform hover:scale-110" title="Ganti Foto Profil">
                            <i class="fa-solid fa-camera text-xs"></i>
                        </label>
                    </div>

                    <!-- Upload Controls & Info -->
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-3">
                            <label for="avatarInput" class="px-4 py-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-xs font-semibold cursor-pointer transition-colors inline-flex items-center gap-2">
                                <i class="fa-solid fa-arrow-up-from-bracket text-xs"></i>
                                <span>Pilih Foto Baru</span>
                            </label>

                            @if($user->avatar)
                            <label class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-semibold cursor-pointer transition-colors inline-flex items-center gap-1.5">
                                <input type="checkbox" name="remove_avatar" value="1" onchange="toggleRemoveAvatar(this)" class="rounded text-rose-600 focus:ring-rose-400">
                                <span>Hapus Foto</span>
                            </label>
                            @endif
                        </div>

                        <input type="file" id="avatarInput" name="avatar" accept="image/png, image/jpeg, image/jpg, image/webp" class="hidden" onchange="previewAvatar(this)">
                        <p class="text-[11px] text-slate-400">Format yang didukung: JPG, PNG, atau WEBP. Maksimal 5 MB.</p>
                    </div>
                </div>
            </div>

            <!-- Informasi Akun -->
            <div class="space-y-4">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Informasi Akun</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Username *</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp / HP</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Divisi</label>
                        <input type="text" name="division" value="{{ old('division', $user->division) }}"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
                    </div>
                </div>
            </div>

            <!-- Ganti Password -->
            <div class="space-y-4 pt-6 border-t border-slate-100">
                <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Keamanan & Ubah Password</h3>
                <p class="text-xs text-slate-500">Kosongkan jika Anda tidak ingin mengubah kata sandi.</p>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Password Saat Ini</label>
                    <input type="password" name="current_password" placeholder="••••••••"
                        class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Password Baru</label>
                        <input type="password" name="new_password" placeholder="Minimal 6 karakter"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" placeholder="Ulangi password baru"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl gradient-btn text-white text-xs sm:text-sm font-semibold shadow-md">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('avatarImage');
                const initials = document.getElementById('avatarInitials');

                img.src = e.target.result;
                img.classList.remove('hidden');
                if (initials) {
                    initials.classList.add('hidden');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function toggleRemoveAvatar(checkbox) {
        const img = document.getElementById('avatarImage');
        const initials = document.getElementById('avatarInitials');
        if (checkbox.checked) {
            if (img) img.classList.add('opacity-30');
        } else {
            if (img) img.classList.remove('opacity-30');
        }
    }
</script>
@endpush
