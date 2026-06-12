@extends('layouts.app')
@section('title', 'Cari Ruangan')
@section('breadcrumb', 'Cari Ruangan')

@section('content')
<h1 class="page-title">Cari Ruangan</h1>
<p class="page-subtitle">Temukan ruangan yang sesuai dengan kebutuhan Anda</p>

{{-- Filter --}}
<div class="card" style="margin-bottom: 24px;">
    <form method="GET" action="{{ route('user.cari-ruangan') }}">
        <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:14px; margin-bottom:16px;">
            <div>
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" class="form-control"
                       value="{{ request('tanggal') }}" min="{{ date('Y-m-d') }}">
            </div>
            <div>
                <label class="form-label">Jam Mulai</label>
                <input type="time" name="jam_mulai" class="form-control" value="{{ request('jam_mulai') }}">
            </div>
            <div>
                <label class="form-label">Jam Selesai</label>
                <input type="time" name="jam_selesai" class="form-control" value="{{ request('jam_selesai') }}">
            </div>
            <div>
                <label class="form-label">Kapasitas Min</label>
                <input type="number" name="kapasitas_min" class="form-control"
                       placeholder="Contoh: 20" value="{{ request('kapasitas_min') }}" min="1">
            </div>
            <div>
                <label class="form-label">Jenis Ruangan</label>
                <select name="jenis" class="form-control">
                    <option value="">Semua Jenis</option>
                    @foreach(['Ruang Rapat','Aula','Ruang Seminar','Laboratorium','Ruang Kelas'] as $j)
                        <option value="{{ $j }}" {{ request('jenis') === $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">Fasilitas</label>
                <select name="fasilitas" class="form-control">
                    <option value="">Semua Fasilitas</option>
                    @foreach(['Proyektor','AC','Komputer','Sound System','Microphone','Whiteboard','Internet','TV'] as $f)
                        <option value="{{ $f }}" {{ request('fasilitas') === $f ? 'selected' : '' }}>{{ $f }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">🔍 Cari Ruangan</button>
    </form>
</div>

@if(!request()->hasAny(['tanggal','kapasitas_min','jenis','fasilitas']))
    <div style="text-align:center; padding:60px 0; color:#ccc;">
        <div style="font-size:48px; margin-bottom:12px;">🏛️</div>
        <p style="font-size:14px;">Gunakan filter di atas untuk mencari ruangan yang tersedia</p>
    </div>
@elseif($rooms->isEmpty())
    <div style="text-align:center; padding:60px 0; color:#ccc;">
        <div style="font-size:48px; margin-bottom:12px;">😔</div>
        <p style="font-size:14px;">Tidak ada ruangan yang sesuai dengan filter Anda</p>
    </div>
@else
    <div class="room-grid">
        @foreach($rooms as $room)
            @php
                $avail = $room->availability_status ?? 'Tersedia';
                $availClass = strtolower($avail);
            @endphp
            <div class="room-card">
                <div class="room-card-header">
                    <h3 class="room-card-name">{{ $room->name }}</h3>
                    <span class="badge badge-{{ $availClass }}">{{ $avail }}</span>
                </div>
                <p class="room-card-meta">🏛️ {{ $room->type }} · 👥 {{ $room->capacity }} orang</p>
                <p class="room-card-meta">📍 {{ $room->location }}</p>
                <div class="room-card-facilities">
                    @foreach(explode(',', $room->facilities) as $f)
                        @if(trim($f))
                            <span class="facility-tag">{{ trim($f) }}</span>
                        @endif
                    @endforeach
                </div>
                <a href="{{ route('user.buat-reservasi', ['room_id' => $room->id]) }}"
                   class="btn btn-primary {{ $avail === 'Penuh' ? 'btn:disabled' : '' }}"
                   style="{{ $avail === 'Penuh' ? 'background:#e5e7eb; color:#9ca3af; pointer-events:none;' : '' }}">
                    {{ $avail === 'Penuh' ? '🚫 Penuh' : 'Reservasi Sekarang →' }}
                </a>
            </div>
        @endforeach
    </div>
@endif
@endsection
