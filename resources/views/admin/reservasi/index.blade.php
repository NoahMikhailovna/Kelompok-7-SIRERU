@extends('layouts.app')
@section('title', 'Semua Reservasi')
@section('breadcrumb', 'Semua Reservasi')

@section('content')
<div class="page-header-row">
    <div>
        <h1 class="page-title" style="margin-bottom:0;">Semua Reservasi</h1>
        <p class="page-subtitle" style="margin-bottom:0;">Riwayat lengkap semua reservasi</p>
    </div>
    <div style="display:flex; gap:8px; flex-wrap:wrap; align-items:center;">
        <form method="GET" action="{{ route('admin.reservasi') }}" style="display:contents;">
            <input type="month" name="bulan" value="{{ request('bulan') }}"
                   class="form-control" style="width:auto;">
            <button type="submit" class="btn btn-outline">Filter</button>
        </form>
        <a href="{{ route('admin.reservasi.export', ['format' => 'csv', 'bulan' => request('bulan')]) }}"
           class="btn btn-success">📥 Ekspor CSV</a>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-wrapper">
        <table class="sireru-table">
            <thead>
                <tr>
                    <th>ID</th><th>Pemohon</th><th>NIM</th><th>Kegiatan</th>
                    <th>Ruangan</th><th>Tanggal</th><th>Waktu</th><th>Peserta</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $r)
                    <tr>
                        <td style="color:#888; font-weight:700;">#{{ $r->id }}</td>
                        <td style="font-weight:600;">{{ $r->user->name ?? '-' }}</td>
                        <td style="color:#888;">{{ $r->user->nim ?? '-' }}</td>
                        <td style="color:#444;">{{ $r->activity }}</td>
                        <td style="color:#444; white-space:nowrap;">{{ $r->room->name ?? '-' }}</td>
                        <td style="color:#888; white-space:nowrap;">{{ \Carbon\Carbon::parse($r->date)->format('d/m/Y') }}</td>
                        <td style="color:#888; white-space:nowrap;">{{ $r->start_time }}–{{ $r->end_time }}</td>
                        <td style="color:#555; text-align:center;">{{ $r->participants }}</td>
                        <td>
                            <span class="badge
                                {{ $r->status === 'Disetujui' ? 'badge-disetujui' :
                                   ($r->status === 'Menunggu'   ? 'badge-menunggu'   :
                                   ($r->status === 'Dibatalkan' ? 'badge-dibatalkan' : 'badge-ditolak')) }}">
                                {{ $r->status }}
                            </span>
                        </td>
                        <td>
                            <button class="btn btn-outline btn-sm"
                                onclick="showDetail(
                                    {{ $r->id }},
                                    '{{ addslashes($r->activity) }}',
                                    '{{ addslashes($r->room->name ?? '-') }}',
                                    {{ $r->participants }},
                                    '{{ \Carbon\Carbon::parse($r->date)->format('d/m/Y') }}',
                                    '{{ $r->start_time }}',
                                    '{{ $r->end_time }}',
                                    '{{ addslashes($r->notes ?? '') }}',
                                    '{{ $r->status }}',
                                    '{{ addslashes($r->user->name ?? '-') }}',
                                    '{{ $r->user->nim ?? '' }}'
                                )">🔍 Detail</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="table-empty">Tidak ada reservasi pada periode ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="table-footer">
        Menampilkan {{ $reservations->count() }} dari {{ $reservations->total() }} reservasi
    </div>
    @if($reservations->hasPages())
        <div style="padding: 12px 16px;">{{ $reservations->links() }}</div>
    @endif
</div>

{{-- Modal Detail --}}
<div class="modal-overlay" id="modalDetail" style="display:none;" onclick="if(event.target===this) closeModal('modalDetail')">
    <div class="modal-box" style="max-width:520px;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 class="modal-title" style="margin:0;">Detail Reservasi <span id="det-id" style="color:#b91c1c;"></span></h3>
            <button onclick="closeModal('modalDetail')" style="background:none;border:none;font-size:20px;cursor:pointer;color:#888;">×</button>
        </div>
        <div style="display:grid; gap:12px;">
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div class="det-group">
                    <div class="det-label">Pemohon</div>
                    <div class="det-value" id="det-pemohon"></div>
                    <div style="font-size:12px; color:#aaa;" id="det-nim"></div>
                </div>
                <div class="det-group">
                    <div class="det-label">Status</div>
                    <div id="det-status-wrap"></div>
                </div>
            </div>
            <div class="det-group">
                <div class="det-label">Nama Kegiatan</div>
                <div class="det-value" id="det-activity"></div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div class="det-group">
                    <div class="det-label">Pilih Ruangan</div>
                    <div class="det-value" id="det-room"></div>
                </div>
                <div class="det-group">
                    <div class="det-label">Jumlah Peserta</div>
                    <div class="det-value" id="det-participants"></div>
                </div>
            </div>
            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:12px;">
                <div class="det-group">
                    <div class="det-label">Tanggal</div>
                    <div class="det-value" id="det-date"></div>
                </div>
                <div class="det-group">
                    <div class="det-label">Jam Mulai</div>
                    <div class="det-value" id="det-start"></div>
                </div>
                <div class="det-group">
                    <div class="det-label">Jam Selesai</div>
                    <div class="det-value" id="det-end"></div>
                </div>
            </div>
            <div class="det-group">
                <div class="det-label">Keterangan / Keperluan</div>
                <div class="det-value" id="det-notes" style="color:#555; font-style:italic;"></div>
            </div>
        </div>
        <div class="modal-actions" style="margin-top:20px;">
            <button class="btn btn-outline" onclick="closeModal('modalDetail')">Tutup</button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function closeModal(id) { document.getElementById(id).style.display = 'none'; }
function showDetail(id, activity, room, participants, date, start, end, notes, status, pemohon, nim) {
    document.getElementById('det-id').textContent       = '#' + id;
    document.getElementById('det-activity').textContent = activity;
    document.getElementById('det-room').textContent     = room;
    document.getElementById('det-participants').textContent = participants + ' orang';
    document.getElementById('det-date').textContent     = date;
    document.getElementById('det-start').textContent    = start;
    document.getElementById('det-end').textContent      = end;
    document.getElementById('det-notes').textContent    = notes || '—';
    document.getElementById('det-pemohon').textContent  = pemohon;
    document.getElementById('det-nim').textContent      = nim ? 'NIM: ' + nim : '';
    const badgeClass = status === 'Disetujui' ? 'badge-disetujui'
                     : status === 'Menunggu'    ? 'badge-menunggu'
                     : status === 'Dibatalkan' ? 'badge-dibatalkan' : 'badge-ditolak';
    document.getElementById('det-status-wrap').innerHTML =
        `<span class="badge ${badgeClass}">${status}</span>`;
    document.getElementById('modalDetail').style.display = 'flex';
}
</script>
<style>
.det-group { background:#f9fafb; border-radius:8px; padding:10px 14px; }
.det-label { font-size:11px; color:#9ca3af; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; }
.det-value { font-weight:600; color:#1f2937; font-size:14px; }
</style>
@endpush
@endsection
