@extends('layouts.app')

@section('title', 'Tugas')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Tugas</h1>
            <p class="text-sm text-slate-500 mt-1">Semua tugas tim kreatif.</p>
        </div>

        @if(Auth::user()->isSuperadmin())
        <button onclick="document.getElementById('modalTambahTugas').classList.remove('hidden')" class="px-5 py-2.5 rounded-xl gradient-btn text-white text-sm font-semibold shadow-md flex items-center justify-center gap-2">
            <i class="fa-solid fa-plus text-xs"></i>
            <span>+ Tambah Tugas</span>
        </button>
        @endif
    </div>

    <!-- Filters & Search Bar (Matching Screenshot) -->
    <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)]">
        <form method="GET" action="{{ route('tasks.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Search Input -->
            <div class="sm:col-span-6 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul tugas..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
            </div>

            <!-- Status Dropdown -->
            <div class="sm:col-span-3">
                <select name="status" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 text-slate-700">
                    <option value="Semua Status">Semua Status</option>
                    @foreach(['Belum Dimulai', 'Dikerjakan', 'Menunggu Review', 'Disetujui', 'Revisi'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Priority Dropdown -->
            <div class="sm:col-span-3">
                <select name="priority" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 text-slate-700">
                    <option value="Semua Prioritas">Semua Prioritas</option>
                    @foreach(['Rendah', 'Sedang', 'Tinggi'] as $pr)
                        <option value="{{ $pr }}" {{ request('priority') === $pr ? 'selected' : '' }}>{{ $pr }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Tasks Table (Matching Screenshot) -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50">
                        <th class="py-4 px-6">JUDUL</th>
                        <th class="py-4 px-6">DITUGASKAN</th>
                        <th class="py-4 px-6">PRIORITAS</th>
                        <th class="py-4 px-6">STATUS</th>
                        <th class="py-4 px-6">DEADLINE</th>
                        <th class="py-4 px-6 text-right">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($tasks as $task)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <!-- Judul -->
                        <td class="py-4 px-6 font-semibold text-slate-900 max-w-xs truncate">
                            <a href="{{ route('tasks.show', $task->id) }}" class="hover:text-purple-600 transition-colors uppercase text-xs sm:text-sm">
                                {{ $task->title }}
                            </a>
                            <p class="text-[11px] font-normal text-slate-400 truncate mt-0.5">{{ $task->division }} · Dibuat oleh {{ $task->creator->name }}</p>
                        </td>

                        <!-- Ditugaskan -->
                        <td class="py-4 px-6">
                            <div class="flex -space-x-1.5 items-center">
                                @foreach($task->assignees as $assignee)
                                    @if($assignee->avatar)
                                        <img src="{{ $assignee->avatar_url }}" alt="{{ $assignee->name }}" class="w-7 h-7 rounded-full object-cover border-2 border-white shadow-xs" title="{{ $assignee->name }} ({{ $assignee->division }})">
                                    @else
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-purple-600 to-pink-500 text-white font-bold text-[10px] flex items-center justify-center border-2 border-white shadow-xs" title="{{ $assignee->name }} ({{ $assignee->division }})">
                                            {{ $assignee->initials }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </td>

                        <!-- Prioritas -->
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $task->priority_badge_color }}">
                                {{ $task->priority }}
                            </span>
                        </td>

                        <!-- Status -->
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $task->status_badge_color }}">
                                {{ $task->status }}
                            </span>
                        </td>

                        <!-- Deadline -->
                        <td class="py-4 px-6 text-xs font-medium {{ $task->isOverdue() ? 'text-rose-600 font-bold' : 'text-slate-600' }}">
                            {{ $task->deadline->translatedFormat('d M Y') }}
                            @if($task->isOverdue())
                                <span class="block text-[10px] text-rose-500">Lewat deadline</span>
                            @endif
                        </td>

                        <!-- Aksi -->
                        <td class="py-4 px-6 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('tasks.show', $task->id) }}" class="p-2 text-slate-400 hover:text-purple-600 hover:bg-purple-50 rounded-lg transition-colors" title="Lihat Detail & Pengumpulan">
                                    <i class="fa-regular fa-eye"></i>
                                </a>

                                @if(Auth::user()->isSuperadmin())
                                <button onclick="openEditModal({{ json_encode($task) }}, {{ json_encode($task->assignees->pluck('id')) }})" class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Tugas">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>

                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Tugas">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400 text-xs">
                            <i class="fa-regular fa-folder-open text-3xl mb-2 text-slate-300 block"></i>
                            Tidak ada tugas yang sesuai dengan pencarian atau filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($tasks->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $tasks->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal Tambah Tugas -->
<div id="modalTambahTugas" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto custom-scrollbar shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
                <h3 class="text-xl font-bold text-slate-900">Tambah Tugas Baru</h3>
                <p class="text-xs text-slate-400 mt-0.5">Berikan tugas & jobdesk kepada anggota tim kreatif.</p>
            </div>
            <button onclick="document.getElementById('modalTambahTugas').classList.add('hidden')" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Tugas *</label>
                <input type="text" name="title" required placeholder="Contoh: UPLOAD KONTEN HARIAN"
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi / Brief Tugas</label>
                <textarea name="description" rows="3" placeholder="Jelaskan instruksi, referensi, atau format yang diinginkan..."
                    class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Divisi Terkait *</label>
                    <select name="division" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 text-slate-800">
                        <option value="Semua">Semua Divisi</option>
                        <option value="Video">Video</option>
                        <option value="Desain">Desain</option>
                        <option value="Foto">Foto</option>
                        <option value="Copywriting">Copywriting</option>
                        <option value="Manajemen">Manajemen</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Prioritas *</label>
                    <select name="priority" required class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 text-slate-800">
                        <option value="Rendah">Rendah</option>
                        <option value="Sedang" selected>Sedang</option>
                        <option value="Tinggi">Tinggi</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deadline *</label>
                <input type="datetime-local" name="deadline" required
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Ditugaskan Kepada (Pilih Anggota) *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-48 overflow-y-auto p-2 border border-slate-200 rounded-xl bg-slate-50/50">
                    @foreach($allUsers as $u)
                    <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-white border border-transparent hover:border-slate-200 cursor-pointer transition-all">
                        <input type="checkbox" name="assignees[]" value="{{ $u->id }}" class="rounded text-purple-600 focus:ring-purple-400">
                        @if($u->avatar)
                            <img src="{{ $u->avatar_url }}" alt="{{ $u->name }}" class="w-6 h-6 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-6 h-6 rounded-full bg-gradient-to-tr from-purple-600 to-pink-500 text-white font-bold text-[10px] flex items-center justify-center flex-shrink-0">
                                {{ $u->initials }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-slate-800 truncate">{{ $u->name }}</p>
                            <p class="text-[10px] text-slate-400">{{ $u->division }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Lampiran File Brief (Opsional)</label>
                <input type="file" name="attachment" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalTambahTugas').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl gradient-btn text-white text-xs font-semibold shadow-md">
                    Simpan Tugas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Tugas -->
<div id="modalEditTugas" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto custom-scrollbar shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
            <div>
                <h3 class="text-xl font-bold text-slate-900">Edit Tugas</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbarui informasi tugas dan penugasan.</p>
            </div>
            <button onclick="document.getElementById('modalEditTugas').classList.add('hidden')" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="formEditTugas" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Judul Tugas *</label>
                <input type="text" id="edit_title" name="title" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deskripsi</label>
                <textarea id="edit_description" name="description" rows="3" class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Divisi *</label>
                    <select id="edit_division" name="division" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-purple-400 text-slate-800">
                        <option value="Semua">Semua Divisi</option>
                        <option value="Video">Video</option>
                        <option value="Desain">Desain</option>
                        <option value="Foto">Foto</option>
                        <option value="Copywriting">Copywriting</option>
                        <option value="Manajemen">Manajemen</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Prioritas *</label>
                    <select id="edit_priority" name="priority" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-purple-400 text-slate-800">
                        <option value="Rendah">Rendah</option>
                        <option value="Sedang">Sedang</option>
                        <option value="Tinggi">Tinggi</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Status *</label>
                    <select id="edit_status" name="status" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:ring-purple-400 text-slate-800">
                        <option value="Belum Dimulai">Belum Dimulai</option>
                        <option value="Dikerjakan">Dikerjakan</option>
                        <option value="Menunggu Review">Menunggu Review</option>
                        <option value="Disetujui">Disetujui</option>
                        <option value="Revisi">Revisi</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Deadline *</label>
                <input type="datetime-local" id="edit_deadline" name="deadline" required class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-purple-400 text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Ditugaskan Kepada *</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-40 overflow-y-auto p-2 border border-slate-200 rounded-xl bg-slate-50/50">
                    @foreach($allUsers as $u)
                    <label class="flex items-center gap-2.5 p-2 rounded-lg hover:bg-white border border-transparent hover:border-slate-200 cursor-pointer">
                        <input type="checkbox" name="assignees[]" value="{{ $u->id }}" id="edit_assignee_{{ $u->id }}" class="rounded text-purple-600 focus:ring-purple-400">
                        <span class="text-xs font-medium text-slate-800 truncate">{{ $u->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalEditTugas').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl gradient-btn text-white text-xs font-semibold shadow-md">
                    Perbarui Tugas
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openEditModal(task, assigneeIds) {
        const form = document.getElementById('formEditTugas');
        form.action = '/tugas/' + task.id;

        document.getElementById('edit_title').value = task.title;
        document.getElementById('edit_description').value = task.description || '';
        document.getElementById('edit_division').value = task.division;
        document.getElementById('edit_priority').value = task.priority;
        document.getElementById('edit_status').value = task.status;

        // Format datetime for datetime-local
        if (task.deadline) {
            const date = new Date(task.deadline);
            const isoString = date.toISOString().slice(0, 16);
            document.getElementById('edit_deadline').value = isoString;
        }

        // Reset check assignees
        document.querySelectorAll('input[id^="edit_assignee_"]').forEach(el => el.checked = false);
        assigneeIds.forEach(id => {
            const chk = document.getElementById('edit_assignee_' + id);
            if (chk) chk.checked = true;
        });

        document.getElementById('modalEditTugas').classList.remove('hidden');
    }
</script>
@endpush

