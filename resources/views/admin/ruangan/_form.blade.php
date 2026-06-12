@php
    $facilityOptions = ['Proyektor','AC','Whiteboard','Sound System','Microphone','Komputer','Internet','TV'];
    $roomTypes = ['Ruang Rapat','Aula','Ruang Seminar','Laboratorium','Ruang Kelas'];
    $selectedFacilities = $room ? array_map('trim', explode(',', $room->facilities ?? '')) : [];
@endphp

<div style="display:grid; grid-template-columns:1fr 1fr; gap:14px;">
    <div class="grid-col-full">
        <label class="form-label form-label-required">Nama Ruangan</label>
        <input type="text" name="name" class="form-control"
               placeholder="Contoh: Ruang Rapat A"
               value="{{ old('name', $room->name ?? '') }}" required>
    </div>
    <div>
        <label class="form-label">Jenis Ruangan</label>
        <select name="type" class="form-control">
            @foreach($roomTypes as $t)
                <option value="{{ $t }}" {{ old('type', $room->type ?? '') === $t ? 'selected' : '' }}>
                    {{ $t }}
                </option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="form-label form-label-required">Kapasitas (orang)</label>
        <input type="number" name="capacity" class="form-control"
               value="{{ old('capacity', $room->capacity ?? 10) }}" min="1" required>
    </div>
    <div class="grid-col-full">
        <label class="form-label form-label-required">Lokasi</label>
        <input type="text" name="location" class="form-control"
               placeholder="Contoh: Gd.A Lt.2"
               value="{{ old('location', $room->location ?? '') }}" required>
    </div>
    <div class="grid-col-full">
        <label class="form-label">Fasilitas</label>
        <div class="facility-toggle">
            @foreach($facilityOptions as $f)
                @php $isChecked = in_array($f, $selectedFacilities); @endphp
                <label class="facility-toggle-item {{ $isChecked ? 'checked' : '' }}">
                    <input type="checkbox" name="facilities[]" value="{{ $f }}"
                           {{ $isChecked ? 'checked' : '' }}
                           style="display:none;">
                    <span class="fac-check">{{ $isChecked ? '✓ ' : '' }}</span>{{ $f }}
                </label>
            @endforeach
        </div>
    </div>
    <div>
        <label class="form-label">Status</label>
        <select name="status" class="form-control">
            <option value="Aktif"       {{ old('status', $room->status ?? 'Aktif') === 'Aktif'       ? 'selected' : '' }}>Aktif</option>
            <option value="Maintenance" {{ old('status', $room->status ?? '') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
        </select>
    </div>
</div>
