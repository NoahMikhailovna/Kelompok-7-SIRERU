<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SiReRu — Sistem Reservasi Ruangan
| Routes
|--------------------------------------------------------------------------
*/

// ── AUTH ─────────────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/',       [AuthController::class, 'showLogin'])->name('home');
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ── ADMIN ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::get('/dashboard',    [AdminController::class, 'dashboard'])->name('dashboard');

    // Permintaan Reservasi
    Route::get('/permintaan',              [AdminController::class, 'permintaan'])->name('permintaan');
    Route::patch('/permintaan/{id}/approve', [AdminController::class, 'approve'])->name('permintaan.approve');
    Route::patch('/permintaan/{id}/reject',  [AdminController::class, 'reject'])->name('permintaan.reject');

    // Manajemen Ruangan
    Route::get('/ruangan',          [AdminController::class, 'ruangan'])->name('ruangan');
    Route::post('/ruangan',         [AdminController::class, 'ruanganStore'])->name('ruangan.store');
    Route::put('/ruangan/{id}',     [AdminController::class, 'ruanganUpdate'])->name('ruangan.update');
    Route::delete('/ruangan/{id}',  [AdminController::class, 'ruanganDestroy'])->name('ruangan.destroy');

    // Semua Reservasi
    Route::get('/reservasi',        [AdminController::class, 'reservasi'])->name('reservasi');
    Route::get('/reservasi/export', [AdminController::class, 'reservasiExport'])->name('reservasi.export');

    // Manajemen Pengguna
    Route::get('/pengguna',               [AdminController::class, 'pengguna'])->name('pengguna');
    Route::post('/pengguna',              [AdminController::class, 'penggunaStore'])->name('pengguna.store');
    Route::put('/pengguna/{id}',          [AdminController::class, 'penggunaUpdate'])->name('pengguna.update');
    Route::patch('/pengguna/{id}/toggle', [AdminController::class, 'penggunaToggle'])->name('pengguna.toggle');
    Route::delete('/pengguna/{id}',       [AdminController::class, 'penggunaDestroy'])->name('pengguna.destroy');

    // Laporan
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');

    // Notifikasi Admin
    Route::get('/notifikasi',                        [AdminController::class, 'notifikasi'])->name('notifikasi');
    Route::patch('/notifikasi/{id}/read',            [AdminController::class, 'notifikasiRead'])->name('notifikasi.read');
    Route::patch('/notifikasi/read-all',             [AdminController::class, 'notifikasiReadAll'])->name('notifikasi.read-all');
});

// ── USER / MAHASISWA ──────────────────────────────────────────────────────
Route::middleware(['auth', 'role:user'])->name('user.')->group(function () {

    Route::get('/beranda',        [UserController::class, 'beranda'])->name('beranda');
    Route::get('/cari-ruangan',   [UserController::class, 'cariRuangan'])->name('cari-ruangan');

    Route::get('/buat-reservasi', [UserController::class, 'buatReservasi'])->name('buat-reservasi');
    Route::post('/buat-reservasi',[UserController::class, 'buatReservasiStore'])->name('buat-reservasi.store');

    Route::get('/riwayat',        [UserController::class, 'riwayat'])->name('riwayat');
    Route::patch('/riwayat/{id}/cancel', [UserController::class, 'cancelReservasi'])->name('reservasi.cancel');

    Route::get('/notifikasi',     [UserController::class, 'notifikasi'])->name('notifikasi');
    Route::patch('/notifikasi/{id}/read',   [UserController::class, 'markRead'])->name('notifikasi.read');
    Route::patch('/notifikasi/read-all',    [UserController::class, 'markAllRead'])->name('notifikasi.read-all');
});

// ── REDIRECT after login ──────────────────────────────────────────────────
Route::middleware('auth')->get('/home', function () {
    $user = auth()->user();
    if (!$user) return redirect()->route('login');
    if ($user->status === 'Nonaktif') {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login')->withErrors(['email' => 'Akun Anda telah dinonaktifkan.']);
    }
    return redirect(
        $user->role === 'admin'
            ? route('admin.dashboard')
            : route('user.beranda')
    );
});
