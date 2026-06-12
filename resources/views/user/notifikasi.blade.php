@extends('layouts.app')
@section('title', 'Notifikasi')
@section('breadcrumb', 'Notifikasi')

@section('content')
<div class="page-header-row">
    <div>
        <h1 class="page-title" style="margin-bottom:0;">Notifikasi</h1>
        <p class="page-subtitle" style="margin-bottom:0;">
            {{ $unread > 0 ? "{$unread} notifikasi belum dibaca" : 'Semua notifikasi sudah dibaca' }}
        </p>
    </div>
    @if($unread > 0)
        <form action="{{ route('user.notifikasi.read-all') }}" method="POST">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-outline" style="border-color:#b91c1c; color:#b91c1c;">
                ✓ Tandai Semua Dibaca
            </button>
        </form>
    @endif
</div>

<div style="display:flex; flex-direction:column; gap:12px;">
    @forelse($notifications as $n)
        @php
            $typeMap = ['approved' => 'approved', 'rejected' => 'rejected', 'pending' => 'pending'];
            $cardClass = $n->read_at ? 'notif-card-read' : 'notif-card-' . ($typeMap[$n->type] ?? 'pending');
            $icon = $n->type === 'approved' ? '✅' : ($n->type === 'rejected' ? '❌' : '⏱️');
        @endphp
        <div class="notif-card {{ $cardClass }}">
            <div class="notif-icon">{{ $icon }}</div>
            <div style="flex:1;">
                <p class="notif-msg" style="font-weight:{{ $n->read_at ? 400 : 600 }};">
                    {{ $n->message }}
                </p>
                <span class="notif-date">{{ \Carbon\Carbon::parse($n->created_at)->format('d/m/Y H:i') }}</span>
            </div>
            @if(!$n->read_at)
                <form action="{{ route('user.notifikasi.read', $n->id) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" style="background:none; border:none; cursor:pointer; padding:0;"
                            title="Tandai dibaca">
                        <div class="notif-dot"></div>
                    </button>
                </form>
            @endif
        </div>
    @empty
        <div class="card" style="text-align:center; padding:48px;">
            <div style="font-size:48px; margin-bottom:12px;">🔔</div>
            <p style="color:#aaa; font-size:14px;">Tidak ada notifikasi</p>
        </div>
    @endforelse
</div>
@endsection
