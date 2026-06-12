@extends('layouts.app')
@section('title', 'Laporan Penggunaan')
@section('breadcrumb', 'Laporan Penggunaan')

@section('content')
<div class="page-header-row">
    <div>
        <h1 class="page-title" style="margin-bottom:0;">Laporan Penggunaan</h1>
        <p class="page-subtitle" style="margin-bottom:0;">Statistik dan laporan penggunaan ruangan</p>
    </div>
</div>

{{-- Filter Form --}}
<div class="card" style="margin-bottom:20px; padding:20px;">
    <form method="GET" action="{{ route('admin.laporan') }}" id="filterForm">
        <div style="display:flex; gap:16px; align-items:flex-end; flex-wrap:wrap;">

            {{-- Tahun --}}
            <div>
                <label style="display:block; font-size:12px; font-weight:600; color:#555; margin-bottom:4px;">Tahun</label>
                <select name="tahun" class="form-control" style="width:100px;">
                    @foreach($availableYears as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Bulan (checkbox multi-select) --}}
            <div style="flex:1; min-width:280px;">
                <label style="display:block; font-size:12px; font-weight:600; color:#555; margin-bottom:6px;">
                    Filter Bulan
                    <span style="font-weight:400; color:#aaa;">(kosong = semua bulan)</span>
                </label>
                <div style="display:flex; flex-wrap:wrap; gap:6px;">
                    @php
                        $bulanList = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                    @endphp
                    @foreach($bulanList as $i => $b)
                        @php $m = $i + 1; @endphp
                        <label style="display:flex; align-items:center; gap:4px; cursor:pointer;
                            background:{{ in_array($m, $selectedMonths) ? '#b91c1c' : '#f3f4f6' }};
                            color:{{ in_array($m, $selectedMonths) ? '#fff' : '#333' }};
                            padding:4px 10px; border-radius:20px; font-size:12px; font-weight:600;
                            transition:all 0.2s; user-select:none;" class="bulan-label" data-month="{{ $m }}">
                            <input type="checkbox" name="bulan[]" value="{{ $m }}"
                                {{ in_array($m, $selectedMonths) ? 'checked' : '' }}
                                style="display:none;">
                            {{ $b }}
                        </label>
                    @endforeach
                </div>
            </div>

            {{-- Buttons --}}
            <div style="display:flex; gap:8px;">
                <button type="submit" class="btn btn-primary" style="height:38px;">Terapkan</button>
                <a href="{{ route('admin.laporan') }}" class="btn btn-outline" style="height:38px; line-height:1; display:flex; align-items:center;">Reset</a>
            </div>
        </div>

        @if(!empty($selectedMonths))
            <div style="margin-top:8px; font-size:12px; color:#b91c1c;">
                ✓ Menampilkan data bulan:
                @php
                    $namaList = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
                @endphp
                {{ implode(', ', array_map(fn($m) => $namaList[$m-1], $selectedMonths)) }}
                pada tahun {{ $selectedYear }}
            </div>
        @endif
    </form>
</div>

{{-- Stat Cards --}}
<div class="stat-grid" style="grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));">
    <div class="stat-card">
        <div class="stat-card-inner">
            <div><div class="stat-value" style="color:#7f1d1d;">{{ $totalReservasi }}</div>
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
            <div><div class="stat-value" style="color:#ef4444;">{{ $ditolak }}</div>
            <div class="stat-label">Ditolak</div></div>
            <div class="stat-icon">❌</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-inner">
            <div>
                <div class="stat-value" style="color:#3b82f6;">
                    {{ $totalReservasi > 0 ? round($disetujui / $totalReservasi * 100) : 0 }}%
                </div>
                <div class="stat-label">Tingkat Persetujuan</div>
            </div>
            <div class="stat-icon">📊</div>
        </div>
    </div>
</div>

{{-- Trend 12 Bulan --}}
<div class="card" style="margin-bottom: 20px;">
    <h2 class="section-title">
        Tren Reservasi Per Bulan — {{ $selectedYear }}
        @if(!empty($selectedMonths))
            <span style="font-size:13px; font-weight:400; color:#b91c1c;">({{ count($selectedMonths) }} bulan dipilih)</span>
        @endif
    </h2>
    <div style="display:flex; align-items:flex-end; gap:8px; height:200px; padding: 10px 0; overflow-x:auto;">
        @php $maxTrend = max(array_merge(array_column($trendData, 'total'), [1])); @endphp
        @foreach($trendData as $t)
            <div style="flex:1; min-width:40px; display:flex; flex-direction:column; align-items:center; gap:6px; height:100%;">
                <span style="font-size:11px; font-weight:700; color:#b91c1c; white-space:nowrap;">
                    {{ $t['total'] > 0 ? $t['total'] : '' }}
                </span>
                <div style="flex:1; display:flex; align-items:flex-end; width:100%;">
                    <div style="
                        width: 100%;
                        height: {{ $maxTrend > 0 ? max(round($t['total'] / $maxTrend * 100), ($t['total'] > 0 ? 3 : 0)) : 0 }}%;
                        background: {{ $t['selected'] ? '#7c3aed' : ($t['total'] > 0 ? '#b91c1c' : '#e5e7eb') }};
                        border-radius: 4px 4px 0 0;
                        transition: height 0.5s ease;
                        position: relative;">
                        @if($t['selected'])
                            <div style="position:absolute; top:-18px; left:50%; transform:translateX(-50%);
                                background:#7c3aed; color:#fff; font-size:9px; padding:1px 4px;
                                border-radius:3px; white-space:nowrap;">✓</div>
                        @endif
                    </div>
                </div>
                <span style="font-size:10px; color:{{ $t['selected'] ? '#7c3aed' : '#888' }}; font-weight:{{ $t['selected'] ? '700' : '400' }}; white-space:nowrap;">
                    {{ $t['month'] }}
                </span>
            </div>
        @endforeach
    </div>
    @if(!empty($selectedMonths))
        <div style="display:flex; gap:16px; margin-top:8px; font-size:11px;">
            <span style="display:flex; align-items:center; gap:4px;"><span style="width:12px; height:12px; background:#7c3aed; border-radius:2px; display:inline-block;"></span> Bulan dipilih</span>
            <span style="display:flex; align-items:center; gap:4px;"><span style="width:12px; height:12px; background:#b91c1c; border-radius:2px; display:inline-block;"></span> Bulan lain</span>
        </div>
    @endif
</div>

{{-- Rekap Per Ruangan --}}
<div class="card">
    <h2 class="section-title">Rekap Per Ruangan</h2>
    <div class="table-wrapper">
        <table class="sireru-table">
            <thead>
                <tr>
                    <th>Ruangan</th><th>Total</th><th>Disetujui</th>
                    <th>Ditolak</th><th>Menunggu</th><th>Jam Pakai</th><th style="min-width:120px;">Penggunaan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roomStats as $r)
                    <tr>
                        <td style="font-weight:600;">{{ $r['name'] }}</td>
                        <td style="color:#555; text-align:center;">{{ $r['total'] }}</td>
                        <td style="text-align:center;"><span style="color:#16a34a; font-weight:700;">{{ $r['disetujui'] }}</span></td>
                        <td style="text-align:center;"><span style="color:#ef4444; font-weight:700;">{{ $r['ditolak'] }}</span></td>
                        <td style="text-align:center;"><span style="color:#f59e0b; font-weight:700;">{{ $r['menunggu'] }}</span></td>
                        <td style="color:#555; text-align:center;">{{ $r['hours'] }} jam</td>
                        <td>
                            <div class="progress-bar-wrap">
                                <div class="progress-bar-fill" style="width:{{ $r['pct'] }}%"></div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="table-empty">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle bulan pills
document.querySelectorAll('.bulan-label').forEach(label => {
    label.addEventListener('click', function() {
        const cb = this.querySelector('input[type=checkbox]');
        cb.checked = !cb.checked;
        if (cb.checked) {
            this.style.background = '#b91c1c';
            this.style.color = '#fff';
        } else {
            this.style.background = '#f3f4f6';
            this.style.color = '#333';
        }
    });
});
</script>
@endpush
