@extends('layouts.app')
@section('title', 'Buat Reservasi')
@section('breadcrumb', 'Buat Reservasi')

@section('content')
<h1 class="page-title">Buat Reservasi</h1>
<p class="page-subtitle">Isi form di bawah untuk mengajukan permintaan reservasi ruangan</p>

<div class="card" style="max-width: 640px;">
    <form action="{{ route('user.buat-reservasi.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label form-label-required">Nama Kegiatan</label>
            <input type="text" name="activity" class="form-control"
                   placeholder="Contoh: Rapat Himpunan Mahasiswa"
                   value="{{ old('activity') }}" required>
            @error('activity')
                <span style="color:#ef4444; font-size:12px;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label form-label-required">Pilih Ruangan</label>
            <select name="room_id" class="form-control" required>
                <option value="">— Pilih Ruangan —</option>
                @foreach($rooms as $room)
                    <option value="{{ $room->id }}"
                        {{ (old('room_id', request('room_id')) == $room->id) ? 'selected' : '' }}>
                        {{ $room->name }} ({{ $room->capacity }} orang · {{ $room->location }})
                    </option>
                @endforeach
            </select>
            @error('room_id')
                <span style="color:#ef4444; font-size:12px;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label form-label-required">Jumlah Peserta</label>
            <input type="number" name="participants" class="form-control"
                   placeholder="Masukkan jumlah peserta"
                   value="{{ old('participants') }}" min="1" required>
            @error('participants')
                <span style="color:#ef4444; font-size:12px;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label form-label-required">Tanggal</label>
            <input type="date" name="date" class="form-control"
                   value="{{ old('date') }}" min="{{ date('Y-m-d') }}" required>
            @error('date')
                <span style="color:#ef4444; font-size:12px;">{{ $message }}</span>
            @enderror
        </div>

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
            <div class="form-group">
                <label class="form-label form-label-required">Jam Mulai</label>
                <input type="time" name="start_time" class="form-control"
                       value="{{ old('start_time') }}" required>
                @error('start_time')
                    <span style="color:#ef4444; font-size:12px;">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label class="form-label form-label-required">Jam Selesai</label>
                <input type="time" name="end_time" class="form-control"
                       value="{{ old('end_time') }}" required>
                @error('end_time')
                    <span style="color:#ef4444; font-size:12px;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Keterangan / Keperluan</label>
            <textarea name="notes" class="form-control" rows="3"
                      placeholder="Tambahkan keterangan tambahan jika perlu...">{{ old('notes') }}</textarea>
        </div>

        <div class="form-alert-info" style="margin-bottom:20px;">
            ℹ️ Permintaan reservasi akan diproses oleh admin dalam 1×24 jam kerja.
        </div>

        <div style="display:flex; gap:12px; justify-content:flex-end;">
            <a href="{{ route('user.beranda') }}" class="btn btn-outline">Batal</a>
            <button type="submit" class="btn btn-primary">📨 Kirim Permintaan</button>
        </div>
    </form>
</div>
@endsection
