@extends('layouts.app')
@section('title', 'Permintaan Reservasi')
@section('breadcrumb', 'Permintaan Reservasi')

@section('content')
<h1 class="page-title">Permintaan Reservasi</h1>
<p class="page-subtitle">Kelola semua permintaan reservasi ruangan</p>

{{-- Filter & Search --}}
<form method="GET" action="{{ route('admin.permintaan') }}" class="filter-bar">
    <div class="tab-group">
        @foreach(['Semua', 'Menunggu', 'Disetujui', 'Ditolak'] as $tab)
            <a href="{{ route('admin.permintaan', ['status' => $tab === 'Semua' ? '' : $tab, 'search' => request('search')]) }}"
               class="tab-btn {{ (request('status', '') === ($tab === 'Semua' ? '' : $tab)) ? 'active' : '' }}">
                {{ $tab }}
            </a>
        @endforeach
    </div>
    <input
        type="text"
        name="search"
        placeholder="🔍 Cari pemohon, kegiatan, ruangan..."
        value="{{ request('search') }}"
        class="form-control"
        style="flex: 1; min-width: 200px; max-width: 360px;"
    >
</form>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-wrapper">
        <table class="sireru-table">
            <thead>
                <tr>
                    <th>ID</th><th>Pemohon</th><th>Kegiatan</th><th>Ruangan</th>
                    <th>Tanggal</th><th>Waktu</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $r)
                    <tr>
                        <td style="color:#888; font-weight:700;">#{{ $r->id }}</td>
                        <td style="font-weight:600; white-space:nowrap;">
                            {{ $r->user->name ?? '-' }}
                            <div style="font-size:11px; color:#aaa;">{{ $r->user->nim ?? '' }}</div>
                        </td>
                        <td style="color:#444;">{{ $r->activity }}</td>
                        <td style="color:#444; white-space:nowrap;">{{ $r->room->name ?? '-' }}</td>
                        <td style="color:#888; white-space:nowrap;">{{ \Carbon\Carbon::parse($r->date)->format('d/m/Y') }}</td>
                        <td style="color:#888; white-space:nowrap;">{{ $r->start_time }}–{{ $r->end_time }}</td>
                        <td>
                            <span class="badge
                                {{ $r->status === 'Disetujui' ? 'badge-disetujui' :
                                   ($r->status === 'Menunggu'   ? 'badge-menunggu'   :
                                   ($r->status === 'Dibatalkan' ? 'badge-dibatalkan' : 'badge-ditolak')) }}">
                                {{ $r->status }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                {{-- Tombol Detail --}}
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
                                        '{{ $r->user->nim ?? '' }}',
                                        '{{ addslashes($r->rejection_reason ?? '') }}'
                                    )">🔍 Detail</button>
                                @if($r->status === 'Menunggu')
                                    <form action="{{ route('admin.permintaan.approve', $r->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-success btn-sm">✓ Setujui</button>
                                    </form>
                                    <button type="button" class="btn btn-danger btn-sm"
                                        onclick="openTolakModal({{ $r->id }}, '{{ addslashes($r->activity) }}')">
                                        ✕ Tolak
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="table-empty">Tidak ada data permintaan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reservations->hasPages())
        <div style="padding: 12px 16px;">{{ $reservations->links() }}</div>
    @endif
</div>

{{-- Modal Detail Reservasi --}}
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
            <div class="det-group" id="det-rejection-wrap" style="display:none; background:#fef2f2; border:1px solid #fecaca;">
                <div class="det-label" style="color:#ef4444;">Alasan Penolakan</div>
                <div class="det-value" id="det-rejection" style="color:#b91c1c;"></div>
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

function showDetail(id, activity, room, participants, date, start, end, notes, status, pemohon, nim, rejectionReason) {
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

    // Tampilkan alasan penolakan jika ada
    const rejWrap = document.getElementById('det-rejection-wrap');
    if (status === 'Ditolak' && rejectionReason) {
        document.getElementById('det-rejection').textContent = rejectionReason;
        rejWrap.style.display = 'block';
    } else {
        rejWrap.style.display = 'none';
    }

    const badgeClass = status === 'Disetujui' ? 'badge-disetujui'
                     : status === 'Menunggu'    ? 'badge-menunggu'
                     : status === 'Dibatalkan' ? 'badge-dibatalkan' : 'badge-ditolak';
    document.getElementById('det-status-wrap').innerHTML =
        `<span class="badge ${badgeClass}">${status}</span>`;

    document.getElementById('modalDetail').style.display = 'flex';
}

// ── Modal Tolak ──
function openTolakModal(id, activity) {
    document.getElementById('tolakForm').action = `/admin/permintaan/${id}/reject`;
    document.getElementById('tolakActivity').textContent = activity;
    document.getElementById('tolakReason').value = '';
    document.getElementById('modalTolak').style.display = 'flex';
}
</script>

{{-- Modal Tolak dengan Alasan --}}
<div class="modal-overlay" id="modalTolak" style="display:none;" onclick="if(event.target===this) this.style.display='none'">
    <div class="modal-box" style="max-width:480px;">
        <h3 class="modal-title" style="color:#ef4444;">✕ Tolak Reservasi</h3>
        <p style="font-size:13px; color:#555; margin-bottom:16px;">
            Reservasi: <strong id="tolakActivity"></strong>
        </p>
        <form id="tolakForm" action="" method="POST">
            @csrf @method('PATCH')
            <div class="form-group">
                <label class="form-label form-label-required">Alasan Penolakan</label>
                <textarea id="tolakReason" name="rejection_reason" class="form-control"
                    rows="4" placeholder="Tulis alasan penolakan reservasi ini..." required
                    style="resize:vertical;"></textarea>
                <span style="font-size:11px; color:#aaa;">Alasan ini akan dikirim sebagai notifikasi kepada pemohon.</span>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('modalTolak').style.display='none'">Batal</button>
                <button type="submit" class="btn btn-danger">✕ Tolak Reservasi</button>
            </div>
        </form>
    </div>
</div>

<style>
.det-group { background:#f9fafb; border-radius:8px; padding:10px 14px; }
.det-label { font-size:11px; color:#9ca3af; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px; }
.det-value { font-weight:600; color:#1f2937; font-size:14px; }
</style>
@endpush
@endsection
