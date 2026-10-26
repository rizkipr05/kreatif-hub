@extends('layouts.app')

@section('title', 'Detail Tugas: ' . $task->title)

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <!-- Back Button & Breadcrumbs -->
    <div class="flex items-center justify-between">
        <a href="{{ route('tasks.index') }}" class="text-xs font-semibold text-slate-500 hover:text-purple-600 flex items-center gap-2 transition-colors">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Daftar Tugas</span>
        </a>

        <!-- Quick Status Change for Employee / Admin -->
        <div class="flex items-center gap-2">
            @if($task->status === 'Belum Dimulai')
            <form action="{{ route('tasks.status', $task->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="Dikerjakan">
                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 text-xs font-semibold transition-all flex items-center gap-1.5">
                    <i class="fa-solid fa-play text-[10px]"></i>
                    <span>Mulai Kerjakan</span>
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Main Task Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-2">
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $task->priority_badge_color }}">
                        Prioritas {{ $task->priority }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $task->status_badge_color }}">
                        {{ $task->status }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-100 text-slate-600">
                        Divisi: {{ $task->division }}
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight uppercase">
                    {{ $task->title }}
                </h1>
                <p class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                    <span>Dibuat oleh <strong class="text-slate-600">{{ $task->creator->name }}</strong></span>
                    <span>•</span>
                    <span>{{ $task->created_at->translatedFormat('d F Y, H:i') }}</span>
                </p>
            </div>

            <!-- Deadline Box -->
            <div class="p-4 rounded-2xl bg-purple-50/50 border border-purple-100 text-right min-w-[180px]">
                <p class="text-[11px] font-semibold text-purple-600 uppercase tracking-wider">DEADLINE</p>
                <p class="text-base font-bold text-slate-800 mt-0.5">{{ $task->deadline->translatedFormat('d M Y, H:i') }}</p>
                <p class="text-[11px] {{ $task->isOverdue() ? 'text-rose-500 font-bold' : 'text-slate-400' }}">
                    {{ $task->deadline->diffForHumans() }}
                </p>
            </div>
        </div>

        <!-- Description & Brief -->
        <div class="space-y-3">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Instruksi & Deskripsi Tugas</h3>
            <div class="p-5 rounded-2xl bg-slate-50/60 border border-slate-100 text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                {{ $task->description ?: 'Tidak ada deskripsi tambahan.' }}
            </div>

            @if($task->attachment)
            <div class="pt-2">
                <a href="{{ Storage::url($task->attachment) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-purple-50 text-purple-700 hover:bg-purple-100 text-xs font-semibold transition-colors">
                    <i class="fa-regular fa-file-lines"></i>
                    <span>Unduh File Brief Lampiran</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] ml-1"></i>
                </a>
            </div>
            @endif
        </div>

        <!-- Assignees Section -->
        <div class="space-y-3 pt-4 border-t border-slate-100">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Anggota Tim Yang Ditugaskan</h3>
            <div class="flex flex-wrap gap-3">
                @foreach($task->assignees as $assignee)
                <div class="flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-slate-50 border border-slate-200">
                    @if($assignee->avatar)
                        <img src="{{ $assignee->avatar_url }}" alt="{{ $assignee->name }}" class="w-7 h-7 rounded-full object-cover">
                    @else
                        <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-purple-600 to-pink-500 text-white font-bold text-xs flex items-center justify-center">
                            {{ $assignee->initials }}
                        </div>
                    @endif
                    <div>
                        <p class="text-xs font-semibold text-slate-800 leading-tight">{{ $assignee->name }}</p>
                        <p class="text-[10px] text-purple-600 font-medium">{{ $assignee->division }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Form Pengumpulan Tugas (Untuk Anggota Tim / Assignee) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Kumpulkan Hasil Kerja</h2>
                <p class="text-xs text-slate-500 mt-0.5">Upload bukti pengerjaan, file desain/video, atau tautan Instagram/Drive.</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-purple-50 text-purple-700">
                Versi Berikutnya: {{ $task->submissions->count() + 1 }}
            </span>
        </div>

        <form action="{{ route('submissions.store', $task->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Pengerjaan *</label>
                <textarea name="notes" rows="3" required placeholder="Contoh: done @souvenirtahsemesta, link canva / draft reels siap dicek."
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Upload File / Gambar (Opsional)</label>
                    <input type="file" name="attachment" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Link Tautan Eksternal (Opsional)</label>
                    <input type="url" name="link" placeholder="https://instagram.com/p/... atau link GDrive"
                        class="w-full px-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
                </div>
            </div>

            <button type="submit" class="px-6 py-2.5 rounded-xl gradient-btn text-white text-xs sm:text-sm font-semibold shadow-md flex items-center gap-2">
                <i class="fa-solid fa-paper-plane text-xs"></i>
                <span>Kirim Hasil Kerja</span>
            </button>
        </form>
    </div>

    <!-- Riwayat Pengumpulan (Submissions Timeline) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] space-y-6">
        <h2 class="text-lg font-bold text-slate-900 pb-3 border-b border-slate-100">Riwayat Pengumpulan & Review</h2>

        <div class="space-y-4">
            @forelse($task->submissions as $sub)
            <div class="p-5 rounded-2xl border border-slate-100 bg-slate-50/40 hover:bg-white transition-all space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-3">
                        @if($sub->user->avatar)
                            <img src="{{ $sub->user->avatar_url }}" alt="{{ $sub->user->name }}" class="w-9 h-9 rounded-full object-cover flex-shrink-0 shadow-sm border border-purple-100">
                        @else
                            <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-purple-600 to-pink-500 text-white font-bold text-xs flex items-center justify-center flex-shrink-0">
                                {{ $sub->user->initials }}
                            </div>
                        @endif
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-slate-800">{{ $sub->user->name }}</span>
                                <span class="text-[11px] text-purple-600 font-medium">({{ $sub->user->division }})</span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">Versi {{ $sub->version }}</span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                Dikumpulkan: {{ $sub->created_at->translatedFormat('d M Y, H:i') }} ({{ $sub->created_at->diffForHumans() }})
                            </p>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $sub->status_badge_color }}">
                            {{ $sub->status }}
                        </span>
                    </div>
                </div>

                <!-- Notes -->
                <div class="p-3.5 rounded-xl bg-white border border-slate-100 text-xs sm:text-sm text-slate-700">
                    <p class="font-semibold text-slate-500 text-[11px] uppercase mb-1">Catatan Karyawan:</p>
                    {{ $sub->notes }}
                </div>

                <!-- File or Link -->
                <div class="flex flex-wrap gap-2 pt-1">
                    @if($sub->attachment)
                    <a href="{{ str_starts_with($sub->attachment, 'submissions/') ? Storage::url($sub->attachment) : '#' }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-purple-50 text-purple-700 text-xs font-medium hover:bg-purple-100 transition-colors">
                        <i class="fa-regular fa-image"></i>
                        <span>{{ basename($sub->attachment) }}</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                    @endif

                    @if($sub->link)
                    <a href="{{ $sub->link }}" target="_blank" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-pink-50 text-pink-700 text-xs font-medium hover:bg-pink-100 transition-colors">
                        <i class="fa-solid fa-link"></i>
                        <span>Buka Tautan Eksternal</span>
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                    @endif
                </div>

                <!-- Review Notes if Available -->
                @if($sub->review_notes)
                <div class="p-3.5 rounded-xl bg-amber-50/60 border border-amber-200 text-xs sm:text-sm text-amber-900">
                    <p class="font-semibold text-amber-700 text-[11px] uppercase mb-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-comment-dots"></i> Feedback Review Superadmin ({{ $sub->reviewer ? $sub->reviewer->name : 'Superadmin' }}):
                    </p>
                    {{ $sub->review_notes }}
                </div>
                @endif

                <!-- Superadmin Review Form Trigger -->
                @if(Auth::user()->isSuperadmin() && $sub->status === 'Menunggu Review')
                <div class="pt-2 flex items-center justify-end">
                    <button onclick="openReviewModal({{ $sub->id }}, '{{ $sub->user->name }}', '{{ $task->title }}')" class="px-4 py-2 rounded-xl gradient-btn text-white text-xs font-semibold shadow-sm flex items-center gap-1.5">
                        <i class="fa-solid fa-stamp"></i>
                        <span>Beri Review Hasil Ini</span>
                    </button>
                </div>
                @endif
            </div>
            @empty
            <div class="py-8 text-center text-slate-400 text-xs">
                <i class="fa-regular fa-circle-question text-3xl mb-2 text-slate-300 block"></i>
                Belum ada pengumpulan hasil kerja untuk tugas ini.
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Review Superadmin -->
<div id="modalReview" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
            <div>
                <h3 class="text-xl font-bold text-slate-900">Review Hasil Kerja</h3>
                <p id="reviewSubtitle" class="text-xs text-slate-400 mt-0.5">Tentukan persetujuan atau instruksi revisi.</p>
            </div>
            <button onclick="document.getElementById('modalReview').classList.add('hidden')" class="p-2 text-slate-400 hover:text-slate-600 rounded-lg">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <form id="formReview" method="POST" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Keputusan Review *</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-2 p-3 rounded-xl border border-emerald-200 bg-emerald-50/50 cursor-pointer hover:bg-emerald-50">
                        <input type="radio" name="status" value="Disetujui" checked class="text-emerald-600 focus:ring-emerald-400">
                        <span class="text-xs font-bold text-emerald-800">Setujui (Approve)</span>
                    </label>

                    <label class="flex items-center gap-2 p-3 rounded-xl border border-rose-200 bg-rose-50/50 cursor-pointer hover:bg-rose-50">
                        <input type="radio" name="status" value="Revisi" class="text-rose-600 focus:ring-rose-400">
                        <span class="text-xs font-bold text-rose-800">Minta Revisi</span>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan Feedback / Catatan Revisi</label>
                <textarea name="review_notes" rows="4" placeholder="Tuliskan catatan apresiasi atau bagian detail yang perlu diperbaiki..."
                    class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800"></textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('modalReview').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl gradient-btn text-white text-xs font-semibold shadow-md">
                    Simpan Review
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openReviewModal(subId, userName, taskTitle) {
        const form = document.getElementById('formReview');
        form.action = '/pengumpulan/' + subId + '/review';
        document.getElementById('reviewSubtitle').innerText = 'Pengumpulan oleh ' + userName + ' untuk ' + taskTitle;
        document.getElementById('modalReview').classList.remove('hidden');
    }
</script>
@endpush

