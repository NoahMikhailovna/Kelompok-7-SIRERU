@extends('layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<h1 class="page-title">Dashboard</h1>
<p class="page-subtitle">Selamat datang kembali, {{ auth()->user()->name }}</p>

{{-- Stat Cards --}}
<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-card-inner">
            <div>
                <div class="stat-value" style="color: #7f1d1d;">{{ $totalRooms }}</div>
                <div class="stat-label">Total Ruangan Aktif</div>
            </div>
            <div class="stat-icon">🏛️</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-inner">
            <div>
                <div class="stat-value" style="color: #b91c1c;">{{ $thisMonth }}</div>
                <div class="stat-label">Reservasi Bulan Ini</div>
            </div>
            <div class="stat-icon">📅</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-inner">
            <div>
                <div class="stat-value" style="color: #d97706;">{{ $pending }}</div>
                <div class="stat-label">Menunggu Persetujuan</div>
            </div>
            <div class="stat-icon">⏱️</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-inner">
            <div>
                <div class="stat-value" style="color: #16a34a;">{{ $activeUsers }}</div>
                <div class="stat-label">Total Pengguna Aktif</div>
            </div>
            <div class="stat-icon">👥</div>
        </div>
    </div>
</div>

{{-- Recent Reservations --}}
<div class="card" style="margin-bottom: 20px;">
    <h2 class="section-title">Permintaan Terbaru</h2>
    <div class="table-wrapper">
        <table class="sireru-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Pemohon</th>
                    <th>Kegiatan</th>
                    <th>Ruangan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentReservations as $r)
                    <tr>
                        <td style="color:#888; font-weight:700;">#{{ $r->id }}</td>
                        <td style="font-weight:600;">{{ $r->user->name ?? '-' }}</td>
                        <td style="color:#444;">{{ $r->activity }}</td>
                        <td style="color:#444;">{{ $r->room->name ?? '-' }}</td>
                        <td style="color:#888;">{{ \Carbon\Carbon::parse($r->date)->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge
                                {{ $r->status === 'Disetujui'  ? 'badge-disetujui'  :
                                   ($r->status === 'Menunggu'   ? 'badge-menunggu'   :
                                   ($r->status === 'Dibatalkan' ? 'badge-dibatalkan' : 'badge-ditolak')) }}">
                                {{ $r->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="table-empty">Belum ada reservasi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Room Usage --}}
<div class="grid-2">
    <div class="card">
        <h2 class="section-title">Penggunaan Ruangan</h2>
        @foreach($roomUsage as $r)
            <div style="margin-bottom: 12px;">
                <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:4px;">
                    <span style="font-weight:600; color:#333;">{{ $r['name'] }}</span>
                    <span style="color:#b91c1c; font-weight:700;">{{ $r['count'] }}x</span>
                </div>
                <div class="progress-bar-wrap">
                    <div class="progress-bar-fill" style="width: {{ $maxUsage > 0 ? ($r['count'] / $maxUsage * 100) : 0 }}%"></div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card">
        <h2 class="section-title">Statistik Status Reservasi</h2>
        @php
            $statusStats = [
                ['label' => 'Disetujui', 'count' => $statusCount['Disetujui'] ?? 0, 'color' => '#16a34a', 'bg' => '#dcfce7'],
                ['label' => 'Menunggu',  'count' => $statusCount['Menunggu']  ?? 0, 'color' => '#d97706', 'bg' => '#fef3c7'],
                ['label' => 'Ditolak',   'count' => $statusCount['Ditolak']   ?? 0, 'color' => '#ef4444', 'bg' => '#fee2e2'],
            ];
            $totalStat = array_sum(array_column($statusStats, 'count'));
        @endphp
        @foreach($statusStats as $s)
            <div style="display:flex; justify-content:space-between; align-items:center; padding: 12px 0; border-bottom: 1px solid #f5f5f5;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <div style="width:12px; height:12px; border-radius:50%; background:{{ $s['color'] }};"></div>
                    <span style="font-size:13px; font-weight:600;">{{ $s['label'] }}</span>
                </div>
                <div style="display:flex; align-items:center; gap:12px;">
                    <span style="font-size:13px; color:#888;">{{ $totalStat > 0 ? round($s['count'] / $totalStat * 100) : 0 }}%</span>
                    <span style="background:{{ $s['bg'] }}; color:{{ $s['color'] }}; border-radius:99px; padding:3px 10px; font-size:12px; font-weight:700;">
                        {{ $s['count'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
