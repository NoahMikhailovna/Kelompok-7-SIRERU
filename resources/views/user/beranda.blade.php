@extends('layouts.app')
@section('title', 'Beranda')
@section('breadcrumb', 'Beranda')

@section('content')
<h1 class="page-title">Beranda</h1>
<p class="page-subtitle">Selamat datang, {{ auth()->user()->name }}! 👋</p>

{{-- Stat Cards --}}
<div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));">
    <div class="stat-card">
        <div class="stat-card-inner">
            <div><div class="stat-value" style="color:#7f1d1d;">{{ $total }}</div>
            <div class="stat-label">Total Reservasi</div></div>
            <div class="stat-icon">📋</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-inner">
            <div><div class="stat-value" style="color:#16a34a;">{{ $disetujui }}</div>
            <div class="stat-label">Disetujui</div></div>
            <div class="stat-icon">✅</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-inner">
            <div><div class="stat-value" style="color:#d97706;">{{ $menunggu }}</div>
            <div class="stat-label">Menunggu</div></div>
            <div class="stat-icon">⏱️</div>
        </div>
    </div>
</div>

<div class="grid-2">
    {{-- Recent Reservations --}}
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h2 class="section-title" style="margin:0;">Reservasi Terbaru</h2>
            <a href="{{ route('user.riwayat') }}" style="color:#b91c1c; font-size:12px; font-weight:600;">
                Lihat Semua →
            </a>
        </div>
        <div style="display:flex; flex-direction:column; gap:10px;">
            @forelse($recentReservations as $r)
                <div style="padding:12px 14px; background:#fff5f5; border-radius:8px; border:1px solid #fce8e8;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:4px;">
                        <span style="font-weight:700; font-size:13px;">{{ $r->activity }}</span>
                        <span class="badge
                            {{ $r->status === 'Disetujui' ? 'badge-disetujui' :
                               ($r->status === 'Menunggu'   ? 'badge-menunggu'   :
                                   ($r->status === 'Dibatalkan' ? 'badge-dibatalkan' : 'badge-ditolak')) }}">
                            {{ $r->status }}
                        </span>
                    </div>
                    <div style="font-size:12px; color:#888;">
                        {{ $r->room->name ?? '-' }} · {{ \Carbon\Carbon::parse($r->date)->format('d/m/Y') }} · {{ $r->start_time }}–{{ $r->end_time }}
                    </div>
                </div>
            @empty
                <p style="color:#aaa; font-size:13px;">Belum ada reservasi</p>
            @endforelse
        </div>
    </div>

    {{-- Room Availability --}}
    <div class="card">
        <h2 class="section-title" style="margin-bottom:4px;">Ketersediaan Ruangan Hari Ini</h2>
        <p style="font-size:11px; color:#aaa; margin:0 0 16px;">{{ now()->translatedFormat('l, d F Y') }}</p>
        <div style="display:flex; flex-direction:column; gap:10px;">
            @foreach($roomAvailability as $r)
                <div style="display:flex; align-items:center; justify-content:space-between;
                            padding:8px 12px; background:#fafafa; border-radius:8px; border:1px solid #f0f0f0;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <div class="avail-dot avail-{{ strtolower($r['status']) }}"></div>
                        <span style="font-size:13px; font-weight:500;">{{ $r['name'] }}</span>
                    </div>
                    <span style="font-size:11px; font-weight:700;
                        color:{{ $r['status'] === 'tersedia' ? '#16a34a' : ($r['status'] === 'penuh' ? '#ef4444' : '#d97706') }}">
                        {{ ucfirst($r['status']) }}
                    </span>
                </div>
            @endforeach
        </div>
        <div style="margin-top:14px; display:flex; gap:16px; font-size:11px;">
            @foreach([['color'=>'#16a34a','label'=>'Tersedia'],['color'=>'#ef4444','label'=>'Penuh'],['color'=>'#d97706','label'=>'Sebagian']] as $l)
                <div style="display:flex; align-items:center; gap:5px;">
                    <div style="width:8px; height:8px; border-radius:50%; background:{{ $l['color'] }};"></div>
                    <span style="color:#888;">{{ $l['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
