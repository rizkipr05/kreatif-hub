@extends('layouts.app')

@section('title', 'Pengumpulan Tim')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Pengumpulan Tim</h1>
        <p class="text-sm text-slate-500 mt-1">Lihat seluruh hasil kerja yang dikumpulkan anggota tim.</p>
    </div>

    <!-- Filter Bar (Matching Screenshot) -->
    <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)]">
        <form method="GET" action="{{ route('submissions.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
            <!-- Search -->
            <div class="sm:col-span-8 relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, judul, proyek..."
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 focus:bg-white text-slate-800">
            </div>

            <!-- Divisi Dropdown -->
            <div class="sm:col-span-4">
                <select name="division" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:outline-none focus:ring-2 focus:ring-purple-400 text-slate-700">
                    <option value="Semua Divisi">Semua Divisi</option>
                    @foreach(['Video', 'Desain', 'Foto', 'Copywriting', 'Manajemen'] as $div)
                        <option value="{{ $div }}" {{ request('division') === $div ? 'selected' : '' }}>{{ $div }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Submissions Cards Grid/List (Matching Screenshot) -->
    <div class="space-y-4">
        @forelse($submissions as $sub)
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] hover:shadow-md transition-all space-y-4">
            <!-- Top Row: User Avatar, Name, Division, Status -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    @if($sub->user->avatar)
                        <img src="{{ $sub->user->avatar_url }}" alt="{{ $sub->user->name }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0 shadow-sm border border-purple-100">
                    @else
                        <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-purple-600 to-pink-500 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 shadow-sm">
                            {{ $sub->user->initials }}
                        </div>
                    @endif
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-bold text-slate-900 text-sm sm:text-base">{{ $sub->user->name }}</span>
                            <span class="text-xs text-purple-600 font-semibold">{{ $sub->user->division }}</span>
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $sub->status_badge_color }}">
                                {{ $sub->status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Review Button for Superadmin -->
                @if(Auth::user()->isSuperadmin())
                <div class="flex items-center gap-2">
                    <button onclick="openReviewModal({{ $sub->id }}, '{{ $sub->user->name }}', '{{ $sub->task->title }}')" class="px-3.5 py-1.5 rounded-xl gradient-btn text-white text-xs font-semibold shadow-xs flex items-center gap-1.5">
                        <i class="fa-solid fa-stamp text-[10px]"></i>
                        <span>Review</span>
                    </button>
                    <a href="{{ route('tasks.show', $sub->task_id) }}" class="p-1.5 text-slate-400 hover:text-purple-600 rounded-lg" title="Buka Detail Tugas">
                        <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                    </a>
                </div>
                @endif
            </div>

            <!-- Task Title (Uppercase in screenshot) -->
            <div>
                <a href="{{ route('tasks.show', $sub->task_id) }}" class="font-extrabold text-slate-900 text-sm sm:text-base uppercase hover:text-purple-600 transition-colors">
                    {{ $sub->task->title }}
                </a>
            </div>

            <!-- Meta: Submit time, Deadline, Version -->
            <div class="text-xs text-slate-400 flex flex-wrap items-center gap-x-2 gap-y-1">
                <span>Submit: {{ $sub->created_at->translatedFormat('d M Y H:i') }} ({{ $sub->created_at->diffForHumans() }})</span>
                <span>·</span>
                <span>Deadline: {{ $sub->task->deadline->translatedFormat('d M Y H:i') }}</span>
                <span>·</span>
                <span class="font-semibold text-slate-600">Versi {{ $sub->version }}</span>
            </div>

            <!-- Notes Section Box -->
            <div class="p-4 rounded-xl bg-slate-50/70 border border-slate-100 text-xs sm:text-sm text-slate-700">
                {{ $sub->notes }}
            </div>

            <!-- Attachment Button & Links -->
            <div class="flex flex-wrap items-center gap-3 pt-1">
                @if($sub->attachment)
                <a href="{{ str_starts_with($sub->attachment, 'submissions/') ? Storage::url($sub->attachment) : '#' }}" target="_blank"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-purple-50 text-purple-700 hover:bg-purple-100 border border-purple-100 text-xs font-semibold transition-colors">
                    <i class="fa-regular fa-image text-purple-500"></i>
                    <span>{{ basename($sub->attachment) }}</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-purple-400"></i>
                </a>
                @endif

                @if($sub->link)
                <a href="{{ $sub->link }}" target="_blank"
                   class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-pink-50 text-pink-700 hover:bg-pink-100 border border-pink-100 text-xs font-semibold transition-colors">
                    <i class="fa-solid fa-link text-pink-500"></i>
                    <span>Tautan Preview</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[10px] text-pink-400"></i>
                </a>
                @endif
            </div>

            <!-- Review Notes (if already reviewed) -->
            @if($sub->review_notes)
            <div class="p-3.5 rounded-xl bg-amber-50/70 border border-amber-200 text-xs text-amber-900 mt-2">
                <span class="font-bold text-amber-800">Catatan Review ({{ $sub->reviewer ? $sub->reviewer->name : 'Superadmin' }}):</span>
                {{ $sub->review_notes }}
            </div>
            @endif
        </div>
        @empty
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-100 text-slate-400 text-xs shadow-xs">
            <i class="fa-solid fa-inbox text-4xl mb-3 text-slate-300 block"></i>
            Belum ada hasil kerja yang dikumpulkan pada filter ini.
        </div>
        @endforelse
    </div>

    @if($submissions->hasPages())
    <div class="p-4 bg-white rounded-2xl border border-slate-100">
        {{ $submissions->links() }}
    </div>
    @endif
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
                <textarea name="review_notes" rows="4" placeholder="Tuliskan apresiasi atau instruksi perbaikan..."
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

