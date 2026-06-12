@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('breadcrumb', 'Manajemen Pengguna')

@section('content')
<div class="page-header-row">
    <div>
        <h1 class="page-title" style="margin-bottom:0;">Manajemen Pengguna</h1>
        <p class="page-subtitle" style="margin-bottom:0;">Kelola akun pengguna sistem SiReRu</p>
    </div>
    <div style="display:flex; gap:8px;">
        <button class="btn btn-primary" onclick="openModal('modalTambahMahasiswa')">+ Tambah Mahasiswa</button>
        <button class="btn btn-outline" style="border-color:#7c3aed;color:#7c3aed;" onclick="openModal('modalTambahAdmin')">+ Tambah Admin</button>
    </div>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="table-wrapper">
        <table class="sireru-table">
            <thead>
                <tr>
                    <th>ID</th><th>NIM</th><th>Nama</th><th>Email</th><th>Role</th>
                    <th>Total Reservasi</th><th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $u)
                    <tr>
                        <td style="color:#aaa; font-size:12px;">#{{ $u->id }}</td>
                        <td style="color:#888; font-weight:700;">{{ $u->nim ?? '-' }}</td>
                        <td style="font-weight:700;">{{ $u->name }}</td>
                        <td style="color:#555;">{{ $u->email }}</td>
                        <td>
                            <span class="badge {{ $u->role === 'admin' ? 'badge-admin' : 'badge-user' }}">
                                {{ $u->role === 'admin' ? 'Admin' : 'Mahasiswa' }}
                            </span>
                        </td>
                        <td style="color:#555; text-align:center;">{{ $u->reservations_count }}</td>
                        <td>
                            <span class="badge {{ $u->status === 'Aktif' ? 'badge-aktif' : 'badge-nonaktif' }}">
                                {{ $u->status }}
                            </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                <button class="btn btn-info btn-sm"
                                    onclick="openEditModal(
                                        {{ $u->id }},
                                        '{{ addslashes($u->name) }}',
                                        '{{ $u->email }}',
                                        '{{ $u->nim ?? '' }}',
                                        '{{ $u->role }}'
                                    )">✏️ Edit</button>

                                @if($u->role !== 'admin')
                                    <form action="{{ route('admin.pengguna.toggle', $u->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-sm {{ $u->status === 'Aktif' ? 'btn-warning' : 'btn-success' }}">
                                            {{ $u->status === 'Aktif' ? '🚫 Nonaktif' : '✓ Aktifkan' }}
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.pengguna.destroy', $u->id) }}" method="POST" style="display:inline;"
                                          onsubmit="return confirm('Hapus pengguna {{ addslashes($u->name) }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                                    </form>
                                @else
                                    <span style="color:#ccc; font-size:12px;">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="table-empty">Belum ada pengguna</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ===== MODAL TAMBAH MAHASISWA ===== --}}
<div class="modal-overlay" id="modalTambahMahasiswa" style="display:none;">
    <div class="modal-box modal-box-lg">
        <h3 class="modal-title">Tambah Mahasiswa Baru</h3>
        <form action="{{ route('admin.pengguna.store') }}" method="POST">
            @csrf
            <input type="hidden" name="role" value="user">

            <div class="grid-2">
                <div class="grid-col-full">
                    <div class="form-group">
                        <label class="form-label form-label-required">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Budi Santoso" required>
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label form-label-required">NPM / NIM</label>
                        <input type="text" name="nim" class="form-control" placeholder="Contoh: 2021001234" required>
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label form-label-required">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@student.sireru.id" required>
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label form-label-required">Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required minlength="6">
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label form-label-required">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                    </div>
                </div>
            </div>

            <div class="modal-actions" style="margin-top:8px;">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalTambahMahasiswa')">Batal</button>
                <button type="submit" class="btn btn-primary">Tambah Mahasiswa</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL TAMBAH ADMIN ===== --}}
<div class="modal-overlay" id="modalTambahAdmin" style="display:none;">
    <div class="modal-box">
        <h3 class="modal-title" style="color:#7c3aed;">Tambah Admin Baru</h3>
        <form action="{{ route('admin.pengguna.store') }}" method="POST">
            @csrf
            <input type="hidden" name="role" value="admin">

            <div class="form-group">
                <label class="form-label form-label-required">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" placeholder="Contoh: Administrator" required>
            </div>
            <div class="form-group">
                <label class="form-label form-label-required">Gmail / Email</label>
                <input type="email" name="email" class="form-control" placeholder="admin@gmail.com" required>
            </div>
            <div class="form-group">
                <label class="form-label form-label-required">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required minlength="6">
            </div>
            <div class="form-group">
                <label class="form-label form-label-required">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalTambahAdmin')">Batal</button>
                <button type="submit" class="btn btn-primary" style="background:#7c3aed;">Tambah Admin</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL EDIT PENGGUNA ===== --}}
<div class="modal-overlay" id="modalEditUser" style="display:none;">
    <div class="modal-box modal-box-lg">
        <h3 class="modal-title">Edit Pengguna</h3>
        <form id="formEditUser" action="" method="POST">
            @csrf @method('PUT')

            <div id="nimField" class="form-group" style="display:none;">
                <label class="form-label form-label-required">NPM / NIM</label>
                <input type="text" name="nim" id="editUserNim" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label form-label-required">Nama Lengkap</label>
                <input type="text" name="name" id="editUserName" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label form-label-required">Email</label>
                <input type="email" name="email" id="editUserEmail" class="form-control" required>
            </div>
            <div class="grid-2">
                <div>
                    <div class="form-group">
                        <label class="form-label">Password Baru <span style="color:#aaa;font-weight:400;">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" minlength="6">
                    </div>
                </div>
                <div>
                    <div class="form-group">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                    </div>
                </div>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalEditUser')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openModal(id) { document.getElementById(id).style.display = 'flex'; }
function closeModal(id) { document.getElementById(id).style.display = 'none'; }

function openEditModal(id, name, email, nim, role) {
    const form = document.getElementById('formEditUser');
    form.action = `/admin/pengguna/${id}`;
    document.getElementById('editUserName').value  = name;
    document.getElementById('editUserEmail').value = email;
    document.getElementById('editUserNim').value   = nim;

    // Tampilkan field NIM hanya untuk mahasiswa
    document.getElementById('nimField').style.display = role === 'user' ? 'block' : 'none';

    openModal('modalEditUser');
}

// Tutup modal klik di luar
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
});
</script>
@endpush
@endsection
