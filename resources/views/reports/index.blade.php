@extends('layouts.app')

@section('title', 'Laporan Kinerja')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Laporan & Rekapitulasi</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau statistik penyelesaian tugas dan performa tim kreatif.</p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-semibold hover:bg-slate-50 shadow-xs flex items-center gap-2">
                <i class="fa-solid fa-print text-slate-500"></i>
                <span>Cetak</span>
            </button>
            <a href="{{ route('reports.export') }}" class="px-4 py-2 rounded-xl gradient-btn text-white text-xs font-semibold shadow-md flex items-center gap-2">
                <i class="fa-solid fa-file-excel"></i>
                <span>Export CSV</span>
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)]">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            <div class="sm:col-span-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-400">
            </div>

            <div class="sm:col-span-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-400">
            </div>

            <div class="sm:col-span-3">
                <label class="block text-xs font-semibold text-slate-600 mb-1">Divisi</label>
                <select name="division" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-purple-400">
                    <option value="Semua">Semua Divisi</option>
                    @foreach(['Video', 'Desain', 'Foto', 'Copywriting', 'Manajemen'] as $div)
                        <option value="{{ $div }}" {{ $division === $div ? 'selected' : '' }}>{{ $div }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-1">
                <button type="submit" class="w-full py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-semibold transition-colors flex items-center justify-center">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Summary KPI Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)]">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Tugas</p>
            <h3 class="text-2xl font-extrabold text-slate-900 mt-1">{{ $totalTasks }}</h3>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)]">
            <p class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider">Disetujui</p>
            <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ $completedTasks }}</h3>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)]">
            <p class="text-[11px] font-bold text-blue-500 uppercase tracking-wider">Dikerjakan</p>
            <h3 class="text-2xl font-extrabold text-blue-600 mt-1">{{ $inProgressTasks }}</h3>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)]">
            <p class="text-[11px] font-bold text-amber-500 uppercase tracking-wider">Menunggu Review</p>
            <h3 class="text-2xl font-extrabold text-amber-600 mt-1">{{ $reviewTasks }}</h3>
        </div>

        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)]">
            <p class="text-[11px] font-bold text-rose-500 uppercase tracking-wider">Revisi</p>
            <h3 class="text-2xl font-extrabold text-rose-600 mt-1">{{ $revisionTasks }}</h3>
        </div>
    </div>

    <!-- Tabel Performa Karyawan -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] overflow-hidden">
        <div class="p-5 border-b border-slate-100">
            <h2 class="font-bold text-base text-slate-900">Performa Anggota Tim Kreatif</h2>
            <p class="text-xs text-slate-400 mt-0.5">Statistik pengerjaan tugas masing-masing individu.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
                        <th class="py-3.5 px-6">ANGGOTA</th>
                        <th class="py-3.5 px-6">DIVISI</th>
                        <th class="py-3.5 px-6 text-center">TUGAS DITERIMA</th>
                        <th class="py-3.5 px-6 text-center">SUBMIT</th>
                        <th class="py-3.5 px-6 text-center">DISETUJUI</th>
                        <th class="py-3.5 px-6 text-center">REVISI</th>
                        <th class="py-3.5 px-6 text-right">TINGKAT PENYELESAIAN</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($teamMembers as $m)
                    <tr class="hover:bg-slate-50/60 transition-colors">
                        <td class="py-4 px-6 flex items-center gap-3">
                            @if($m['user']->avatar)
                                <img src="{{ $m['user']->avatar_url }}" alt="{{ $m['user']->name }}" class="w-8 h-8 rounded-full object-cover">
                            @else
                                <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-600 to-pink-500 text-white font-bold text-xs flex items-center justify-center">
                                    {{ $m['user']->initials }}
                                </div>
                            @endif
                            <div>
                                <p class="font-semibold text-slate-900 text-xs sm:text-sm">{{ $m['user']->name }}</p>
                                <p class="text-[11px] text-slate-400">{{ $m['user']->email }}</p>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-xs font-medium text-slate-600">
                            {{ $m['user']->division }}
                        </td>
                        <td class="py-4 px-6 text-center text-xs font-semibold text-slate-800">
                            {{ $m['assigned_count'] }}
                        </td>
                        <td class="py-4 px-6 text-center text-xs font-semibold text-slate-800">
                            {{ $m['submitted_count'] }}
                        </td>
                        <td class="py-4 px-6 text-center text-xs font-semibold text-emerald-600">
                            {{ $m['approved_count'] }}
                        </td>
                        <td class="py-4 px-6 text-center text-xs font-semibold text-rose-600">
                            {{ $m['revision_count'] }}
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="inline-flex items-center gap-2">
                                <div class="w-24 bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-purple-600 to-pink-500 h-2 rounded-full" style="width: {{ $m['completion_rate'] }}%"></div>
                                </div>
                                <span class="text-xs font-bold text-slate-700">{{ $m['completion_rate'] }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400 text-xs">Belum ada data anggota tim.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

