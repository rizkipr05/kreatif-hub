@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    <!-- Greeting Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight flex items-center gap-2">
                Halo, {{ explode(' ', $user->name)[0] }} <span class="animate-pulse">👋</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">Pantau seluruh progres tim kreatif dari sini.</p>
        </div>

        @if($user->isSuperadmin())
        <div class="flex items-center gap-3">
            <a href="{{ route('tasks.index') }}" class="px-4 py-2.5 rounded-xl gradient-btn text-white text-xs font-semibold shadow-md flex items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>+ Tambah Tugas Baru</span>
            </a>
        </div>
        @endif
    </div>

    <!-- 4 Stats Cards (Matching Screenshot) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- 1. Total Tugas -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 tracking-wider uppercase mb-1">TOTAL TUGAS</p>
                <h3 class="text-3xl font-extrabold text-slate-900">{{ $totalTasks }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg">
                <i class="fa-solid fa-bars-progress"></i>
            </div>
        </div>

        <!-- 2. Dikerjakan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 tracking-wider uppercase mb-1">DIKERJAKAN</p>
                <h3 class="text-3xl font-extrabold text-slate-900">{{ $inProgressTasks }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-fuchsia-50 text-fuchsia-500 flex items-center justify-center text-lg">
                <i class="fa-regular fa-clock"></i>
            </div>
        </div>

        <!-- 3. Menunggu Review -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 tracking-wider uppercase mb-1">MENUNGGU REVIEW</p>
                <h3 class="text-3xl font-extrabold text-slate-900">{{ $pendingReviewTasks }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-lg">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
        </div>

        <!-- 4. Terlambat -->
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-400 tracking-wider uppercase mb-1">TERLAMBAT</p>
                <h3 class="text-3xl font-extrabold text-slate-900">{{ $overdueTasks }}</h3>
            </div>
            <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-lg">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
        </div>
    </div>

    <!-- Two Sections (Deadline Terdekat & Menunggu Review) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Card 1: Deadline Terdekat -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)]">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-bold text-base text-slate-900">Deadline Terdekat</h2>
                <a href="{{ route('tasks.index') }}" class="text-xs font-semibold text-slate-500 hover:text-purple-600 flex items-center gap-1.5 transition-colors">
                    <span>Lihat semua</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="space-y-3.5">
                @forelse($nearestDeadlines as $task)
                <div class="p-4 rounded-xl border border-slate-100 hover:border-purple-200 transition-all bg-slate-50/40 hover:bg-white flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <a href="{{ route('tasks.show', $task->id) }}" class="font-bold text-xs sm:text-sm text-slate-900 truncate hover:text-purple-600 transition-colors uppercase">
                                {{ $task->title }}
                            </a>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold {{ $task->priority_badge_color }}">
                                {{ $task->priority }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar-days text-[11px]"></i>
                            <span>{{ $task->deadline->translatedFormat('d M Y H:i') }} · {{ $task->deadline->diffForHumans() }}</span>
                        </p>
                    </div>

                    <!-- Assignees Avatars -->
                    <div class="flex -space-x-1.5 flex-shrink-0">
                        @foreach($task->assignees->take(3) as $assignee)
                            @if($assignee->avatar)
                                <img src="{{ $assignee->avatar_url }}" alt="{{ $assignee->name }}" class="w-7 h-7 rounded-full object-cover border-2 border-white shadow-xs" title="{{ $assignee->name }}">
                            @else
                                <div class="w-7 h-7 rounded-full bg-gradient-to-tr from-purple-500 to-pink-500 text-white font-bold text-[10px] flex items-center justify-center border-2 border-white shadow-xs" title="{{ $assignee->name }}">
                                    {{ $assignee->initials }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-slate-400 text-xs">
                    <i class="fa-solid fa-check-circle text-2xl mb-2 text-emerald-400 block"></i>
                    Belum ada tugas deadline terdekat.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Card 2: Menunggu Review -->
        <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)]">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-bold text-base text-slate-900">Menunggu Review</h2>
                <a href="{{ route('submissions.index') }}" class="text-xs font-semibold text-slate-500 hover:text-purple-600 flex items-center gap-1.5 transition-colors">
                    <span>Lihat pengumpulan</span>
                    <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="space-y-3.5">
                @forelse($awaitingReviewTasks as $task)
                @php
                    $latestSub = $task->submissions->first();
                @endphp
                <div class="p-4 rounded-xl border border-slate-100 hover:border-amber-200 transition-all bg-slate-50/40 hover:bg-white flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1.5">
                            <a href="{{ route('tasks.show', $task->id) }}" class="font-bold text-xs sm:text-sm text-slate-900 truncate hover:text-purple-600 transition-colors uppercase">
                                {{ $task->title }}
                            </a>
                        </div>
                        <p class="text-xs text-slate-400 flex items-center gap-1.5">
                            <i class="fa-regular fa-clock text-[11px]"></i>
                            <span>{{ $latestSub ? 'Dikumpulkan ' . $latestSub->created_at->diffForHumans() : 'Menunggu pengerjaan' }}</span>
                        </p>
                    </div>

                    <div class="flex-shrink-0">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">
                            Menunggu Review
                        </span>
                    </div>
                </div>
                @empty
                <div class="py-8 text-center text-slate-400 text-xs">
                    <i class="fa-solid fa-circle-check text-2xl mb-2 text-emerald-400 block"></i>
                    Tidak ada tugas yang sedang menunggu review.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

