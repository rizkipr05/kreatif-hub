@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header (Matching Screenshot) -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Notifikasi</h1>
            <p class="text-sm text-slate-500 mt-1">{{ $unreadCount }} belum dibaca</p>
        </div>

        @if($unreadCount > 0)
        <form action="{{ route('notifications.markAll') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 rounded-xl bg-white border border-slate-200 hover:border-purple-300 text-slate-700 text-xs font-semibold shadow-xs flex items-center gap-2 transition-all">
                <i class="fa-solid fa-check text-purple-600"></i>
                <span>Tandai semua</span>
            </button>
        </form>
        @endif
    </div>

    <!-- Notification Cards List (Matching Screenshot) -->
    <div class="space-y-3">
        @forelse($notifications as $notif)
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-[0_2px_12px_rgba(0,0,0,0.03)] hover:border-purple-200 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ !$notif->is_read ? 'border-l-4 border-l-pink-500' : '' }}">
            <div class="flex items-start gap-3.5 min-w-0">
                <!-- Pink indicator dot -->
                <div class="mt-1 flex-shrink-0">
                    <span class="w-2.5 h-2.5 rounded-full {{ !$notif->is_read ? 'bg-pink-500' : 'bg-slate-300' }} block"></span>
                </div>

                <div class="min-w-0">
                    <h3 class="font-bold text-sm text-slate-900 capitalize">{{ $notif->title }}</h3>
                    <p class="text-xs text-slate-600 mt-0.5 leading-relaxed">{{ $notif->message }}</p>
                    <p class="text-[11px] text-slate-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                </div>
            </div>

            <!-- Action buttons: [Buka] [Tandai] (Matching Screenshot) -->
            <div class="flex items-center gap-2 flex-shrink-0 self-end sm:self-center">
                @if($notif->link)
                <a href="{{ $notif->link }}" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors">
                    Buka
                </a>
                @endif

                @if(!$notif->is_read)
                <form action="{{ route('notifications.read', $notif->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 text-xs font-semibold transition-colors">
                        Tandai
                    </button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl p-12 text-center border border-slate-100 text-slate-400 text-xs">
            <i class="fa-regular fa-bell-slash text-3xl mb-2 text-slate-300 block"></i>
            Tidak ada notifikasi saat ini.
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="p-4 bg-white rounded-2xl border border-slate-100">
        {{ $notifications->links() }}
    </div>
    @endif
</div>
@endsection

