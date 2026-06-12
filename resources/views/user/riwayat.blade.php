@extends('layouts.app')
@section('title', 'Riwayat Reservasi')
@section('breadcrumb', 'Riwayat Reservasi')

@section('content')
<div class="page-header-row">
    <div>
        <h1 class="page-title" style="margin-bottom:0;">Riwayat Reservasi</h1>
        <p class="page-subtitle" style="margin-bottom:0;">Daftar semua reservasi yang pernah Anda ajukan</p>
    </div>
    <a href="{{ route('user.buat-reservasi') }}" class="btn btn-primary">+ Reservasi Baru</a>
</div>

<div class="card" style="padding:0; overflow:hidden;">
    <div class="table-wrapper">
        <table class="sireru-table">
            <thead>
                <tr>
                    <th>ID</th><th>Nama Kegiatan</th><th>Ruangan</th>
                    <th>Tanggal</th><th>Waktu</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $r)
                    <tr>
                        <td style="color:#888; font-weight:700;">#{{ $r->id }}</td>
                        <td style="font-weight:600;">{{ $r->activity }}</td>
                        <td style="color:#444; white-space:nowrap;">{{ $r->room->name ?? '-' }}</td>
                        <td style="color:#888; white-space:nowrap;">{{ \Carbon\Carbon::parse($r->date)->format('d/m/Y') }}</td>
                        <td style="color:#888; white-space:nowrap;">{{ $r->start_time }}–{{ $r->end_time }}</td>
                        <td>
                            <span class="badge
                                {{ $r->status === 'Disetujui'  ? 'badge-disetujui'  :
                                   ($r->status === 'Menunggu'   ? 'badge-menunggu'   :
                                   ($r->status === 'Dibatalkan' ? 'badge-dibatalkan' : 'badge-ditolak')) }}"
                            >>
                                {{ $r->status }}
                            </span>
                        </td>
                        <td>
                            @if($r->status === 'Menunggu')
                                <form action="{{ route('user.reservasi.cancel', $r->id) }}" method="POST"
                                      onsubmit="return confirm('Batalkan reservasi ini?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm">Batalkan</button>
                                </form>
                            @else
                                <span style="color:#ccc; font-size:12px;">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="table-empty">Anda belum memiliki riwayat reservasi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($reservations->hasPages())
        <div style="padding: 12px 16px;">{{ $reservations->links() }}</div>
    @endif
</div>
@endsection
