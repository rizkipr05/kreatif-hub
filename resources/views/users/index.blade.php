@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Manajemen User</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola akun Superadmin dan Karyawan Tim Kreatif.</p>
        </div>

        <button onclick="document.getElementById('modalTambahUser').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl gradient-btn text-white text-sm font-semibold shadow-md flex items-center justify-center gap-2">
            <i class="fa-solid fa-user-plus text-xs"></i>
            <span>+ Tambah Anggota</span>
        </button>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)]">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <div class="sm:col-span-6 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, no HP..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 text-slate-800">
            </div>

            <div class="sm:col-span-3">
                <select name="role" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 text-slate-700">
                    <option value="Semua">Semua Role</option>
                    <option value="superadmin" {{ request('role') === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    <option value="karyawan" {{ request('role') === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
                </select>
            </div>

            <div class="sm:col-span-3">
                <select name="division" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 text-slate-700">
                    <option value="Semua">Semua Divisi</option>
                    @foreach(['Manajemen', 'Video', 'Desain', 'Foto', 'Copywriting'] as $div)
                        <option value="{{ $div }}" {{ request('division') === $div ? 'selected' : '' }}>{{ $div }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                        <th class="py-4 px-6">NAMA & USERNAME</th>
                        <th class="py-4 px-6">ROLE</th>
                        <th class="py-4 px-6">DIVISI</th>
                        <th class="py-4 px-6">NO. HP / WA</th>
                        <th class="py-4 px-6 text-center">TUGAS DITERIMA</th>
                        <th class="py-4 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($users as $u)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6 flex items-center gap-3">
                            @if($u->avatar)
                                <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0 shadow-sm border border-purple-100">
                            @else
                                <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-purple-600 to-pink-500 text-white font-bold text-xs flex items-center justify-center flex-shrink-0">
                                    {{ $u->initials }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-bold text-slate-900 text-xs sm:text-sm">{{ $u->name }}</p>
                                <p class="text-xs text-purple-600 font-medium">@<span>{{ $u->username }}</span></p>
                            </div>
                        </td>

                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $u->role === 'superadmin' ? 'bg-purple-100 text-purple-700 border border-purple-200' : 'bg-slate-100 text-slate-700' }}">
                                {{ ucfirst($u->role) }}
                            </span>
                        </td>

                        <td class="py-4 px-6 text-xs font-semibold text-slate-700">
                            {{ $u->division }}
                        </td>

                        <td class="py-4 px-6 text-xs text-slate-500">
                            {{ $u->phone ?: '-' }}
                        </td>

                        <td class="py-4 px-6 text-center text-xs font-semibold text-slate-800">
                            {{ $u->tasks_count }}
                        </td>

                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button onclick="openEditUserModal({{ json_encode($u) }})" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit User">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>

                                @if($u->id !== Auth::id())
                                <form action="{{ route('users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus User">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400 text-xs">Tidak ada data user.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Tambah User -->
<div id="modalTambahUser" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div>
                <h3 class="text-xl font-bold text-slate-900">Tambah Anggota Tim</h3>
                <p class="text-xs text-slate-400 mt-0.5">Buat akun untuk Superadmin atau Karyawan baru.</p>
            </div>
            <button onclick="document.getElementById('modalTambahUser').classList.add('hidden')" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                <input type="text" name="name" required placeholder="Contoh: Rian Rizki"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Username (Untuk Login) *</label>
                <input type="text" name="username" required placeholder="contoh: rian"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Role *</label>
                    <select name="role" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-purple-400 text-slate-800">
                        <option value="karyawan">Karyawan</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Divisi *</label>
                    <select name="division" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-purple-400 text-slate-800">
                        <option value="Video">Video</option>
                        <option value="Desain">Desain</option>
                        <option value="Foto">Foto</option>
                        <option value="Copywriting">Copywriting</option>
                        <option value="Manajemen">Manajemen</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp / HP</label>
                <input type="text" name="phone" placeholder="08xxxxxxxxxx"
                    class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Foto Profil (Opsional)</label>
                <input type="file" name="avatar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Password Awal *</label>
                <input type="password" name="password" required placeholder="Minimal 6 karakter"
                    class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalTambahUser').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl gradient-btn text-white text-xs font-semibold shadow-md">
                    Simpan Anggota
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit User -->
<div id="modalEditUser" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div>
                <h3 class="text-xl font-bold text-slate-900">Edit Data Anggota</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbarui profil, divisi, role, atau reset password.</p>
            </div>
            <button onclick="document.getElementById('modalEditUser').classList.add('hidden')" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="formEditUser" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap *</label>
                <input type="text" id="edit_user_name" name="name" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-purple-400 text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Username (Untuk Login) *</label>
                <input type="text" id="edit_user_username" name="username" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-purple-400 text-slate-800">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Role *</label>
                    <select id="edit_user_role" name="role" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-purple-400 text-slate-800">
                        <option value="karyawan">Karyawan</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Divisi *</label>
                    <select id="edit_user_division" name="division" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:ring-purple-400 text-slate-800">
                        <option value="Video">Video</option>
                        <option value="Desain">Desain</option>
                        <option value="Foto">Foto</option>
                        <option value="Copywriting">Copywriting</option>
                        <option value="Manajemen">Manajemen</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">No. WhatsApp / HP</label>
                <input type="text" id="edit_user_phone" name="phone" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-purple-400 text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Ubah Foto Profil (Opsional)</label>
                <input type="file" name="avatar" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Reset Password Baru (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" placeholder="Minimal 6 digit" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-purple-400 text-slate-800">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalEditUser').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl gradient-btn text-white text-xs font-semibold shadow-md">
                    Perbarui User
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditUserModal(user) {
        const form = document.getElementById('formEditUser');
        form.action = '/manajemen-user/' + user.id;

        document.getElementById('edit_user_name').value = user.name;
        document.getElementById('edit_user_username').value = user.username || '';
        document.getElementById('edit_user_role').value = user.role;
        document.getElementById('edit_user_division').value = user.division;
        document.getElementById('edit_user_phone').value = user.phone || '';

        document.getElementById('modalEditUser').classList.remove('hidden');
    }
</script>
@endpush

