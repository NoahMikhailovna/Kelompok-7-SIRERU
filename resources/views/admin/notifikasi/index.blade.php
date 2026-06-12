@extends('layouts.app')
@section('title', 'Notifikasi Admin')
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
        <form action="{{ route('admin.notifikasi.read-all') }}" method="POST">
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
            $isRead = $n->read_at !== null;
            $icon = $n->type === 'approved' ? '✅' : ($n->type === 'rejected' ? '❌' : '⏱️');
            $bg   = $isRead ? '#fff' : ($n->type === 'approved' ? '#f0fdf4' : ($n->type === 'rejected' ? '#fef2f2' : '#fffbeb'));
            $border = $isRead ? '#e5e7eb' : ($n->type === 'approved' ? '#bbf7d0' : ($n->type === 'rejected' ? '#fecaca' : '#fde68a'));
        @endphp
        <div style="
            background:{{ $bg }};
            border:1px solid {{ $border }};
            border-radius:10px;
            padding:14px 18px;
            display:flex;
            align-items:center;
            gap:14px;
            {{ !$isRead ? 'box-shadow:0 2px 6px rgba(0,0,0,0.06);' : '' }}
        ">
            <div style="font-size:24px; flex-shrink:0;">{{ $icon }}</div>
            <div style="flex:1;">
                <p style="margin:0 0 4px; font-size:14px; font-weight:{{ $isRead ? 400 : 600 }}; color:#1f2937;">
                    {{ $n->message }}
                </p>
                <span style="font-size:11px; color:#aaa;">
                    {{ \Carbon\Carbon::parse($n->created_at)->format('d/m/Y H:i') }}
                </span>
            </div>
            @if(!$isRead)
                <form action="{{ route('admin.notifikasi.read', $n->id) }}" method="POST" style="flex-shrink:0;">
                    @csrf @method('PATCH')
                    <button type="submit" style="background:none; border:none; cursor:pointer; padding:4px;" title="Tandai dibaca">
                        <div style="width:10px; height:10px; background:#b91c1c; border-radius:50%;"></div>
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

@if($notifications->hasPages())
    <div style="margin-top:20px;">{{ $notifications->links() }}</div>
@endif
@endsection
