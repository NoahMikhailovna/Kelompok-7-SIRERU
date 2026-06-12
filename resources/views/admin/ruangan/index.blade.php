@extends('layouts.app')
@section('title', 'Manajemen Ruangan')
@section('breadcrumb', 'Manajemen Ruangan')

@section('content')
<div class="page-header-row">
    <div>
        <h1 class="page-title" style="margin-bottom:0;">Manajemen Ruangan</h1>
        <p class="page-subtitle" style="margin-bottom:0;">Kelola data ruangan yang tersedia</p>
    </div>
    <button class="btn btn-primary" onclick="openModal('modalTambah')">+ Tambah Ruangan</button>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-wrapper">
        <table class="sireru-table">
            <thead>
                <tr>
                    <th>ID</th><th>Nama Ruangan</th><th>Jenis</th><th>Kapasitas</th>
                    <th>Lokasi</th><th>Fasilitas</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rooms as $room)
                    <tr>
                        <td style="color:#888; font-weight:700;">#{{ $room->id }}</td>
                        <td style="font-weight:700;">{{ $room->name }}</td>
                        <td style="color:#555;">{{ $room->type }}</td>
                        <td style="color:#555;">{{ $room->capacity }} org</td>
                        <td style="color:#555;">{{ $room->location }}</td>
                        <td>
                            @foreach(explode(',', $room->facilities ?? '') as $f)
                                @if(trim($f))
                                    <span class="facility-tag">{{ trim($f) }}</span>
                                @endif
                            @endforeach
                        </td>
                        <td>
                            <span class="badge {{ $room->status === 'Aktif' ? 'badge-aktif' : 'badge-maintenance' }}">
                                {{ $room->status }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <button class="btn btn-info btn-sm"
                                    data-id="{{ $room->id }}"
                                    data-name="{{ $room->name }}"
                                    data-type="{{ $room->type }}"
                                    data-capacity="{{ $room->capacity }}"
                                    data-location="{{ $room->location }}"
                                    data-facilities="{{ $room->facilities }}"
                                    data-status="{{ $room->status }}"
                                    onclick="openEditModal(this)">✏️ Edit</button>
                                <form action="{{ route('admin.ruangan.destroy', $room->id) }}" method="POST"
                                      onsubmit="return confirm('Hapus ruangan {{ addslashes($room->name) }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">🗑️ Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="table-empty">Belum ada ruangan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ======= Modal Tambah ======= --}}
<div class="modal-overlay" id="modalTambah" style="display:none;">
    <div class="modal-box modal-box-lg">
        <h3 class="modal-title">Tambah Ruangan Baru</h3>
        <form action="{{ route('admin.ruangan.store') }}" method="POST">
            @csrf
            @include('admin.ruangan._form', ['room' => null])
            <div class="modal-actions" style="margin-top:24px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalTambah')">Batal</button>
                <button type="submit" class="btn btn-primary">Tambah Ruangan</button>
            </div>
        </form>
    </div>
</div>

{{-- ======= Modal Edit ======= --}}
<div class="modal-overlay" id="modalEdit" style="display:none;">
    <div class="modal-box modal-box-lg">
        <h3 class="modal-title">Edit Ruangan</h3>
        <form id="formEdit" action="" method="POST">
            @csrf @method('PUT')
            @include('admin.ruangan._form', ['room' => null])
            <div class="modal-actions" style="margin-top:24px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEdit')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
// ── Modal helpers ──────────────────────────────────────
function openModal(id) {
    document.getElementById(id).style.display = 'flex';
}
function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}

// ── Facility toggle (event delegation — works for ALL modals) ──────────
document.addEventListener('click', function(e) {
    const item = e.target.closest('.facility-toggle-item');
    if (!item) return;
    e.preventDefault();
    const cb = item.querySelector('input[type=checkbox]');
    const span = item.querySelector('.fac-check');
    cb.checked = !cb.checked;
    item.classList.toggle('checked', cb.checked);
    if (span) span.textContent = cb.checked ? '✓ ' : '';
});

// ── Open edit modal & populate ────────────────────────
function openEditModal(btn) {
    const id         = btn.dataset.id;
    const name       = btn.dataset.name;
    const type       = btn.dataset.type;
    const capacity   = btn.dataset.capacity;
    const location   = btn.dataset.location;
    const facilities = btn.dataset.facilities;
    const status     = btn.dataset.status;

    const form = document.getElementById('formEdit');
    form.action = `/admin/ruangan/${id}`;

    form.querySelector('[name="name"]').value     = name;
    form.querySelector('[name="type"]').value     = type;
    form.querySelector('[name="capacity"]').value = capacity;
    form.querySelector('[name="location"]').value = location;
    form.querySelector('[name="status"]').value   = status;

    // Reset & re-check fasilitas
    const facilityArr = facilities ? facilities.split(',').map(f => f.trim()).filter(Boolean) : [];
    form.querySelectorAll('[name="facilities[]"]').forEach(cb => {
        const checked = facilityArr.includes(cb.value);
        cb.checked = checked;
        const item = cb.closest('.facility-toggle-item');
        const span = item ? item.querySelector('.fac-check') : null;
        if (item) item.classList.toggle('checked', checked);
        if (span) span.textContent = checked ? '✓ ' : '';
    });

    openModal('modalEdit');
}
</script>
@endpush
@endsection
