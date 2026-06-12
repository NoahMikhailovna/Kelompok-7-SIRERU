@extends('layouts.auth')

@section('content')
<div style="width: 100%; max-width: 420px;">

    {{-- Header --}}
    <div class="auth-header">
        <div class="auth-logo">🏫</div>
        <h1 class="auth-title">SiReRu</h1>
        <p class="auth-subtitle">Sistem Reservasi Ruangan</p>
        <p class="auth-univ">Universitas Kebangsaan Republik Indonesia</p>
    </div>

    {{-- Card --}}
    <div class="auth-card">
        <h2 style="margin: 0 0 6px; font-size: 20px; font-weight: 800; color: #1a1a1a;">Masuk ke Sistem</h2>
        <p style="margin: 0 0 24px; color: #888; font-size: 13px;">Gunakan akun yang telah terdaftar</p>

        {{-- Demo Quick Login --}}
        <div style="margin-bottom: 16px;">
            <p style="font-size: 12px; font-weight: 700; color: #888; margin: 0 0 8px;">AKUN DEMO</p>
            <div class="demo-btns">
                <button type="button" class="demo-btn-admin" onclick="fillDemo('admin')">👤 Admin</button>
                <button type="button" class="demo-btn-user" onclick="fillDemo('mahasiswa')">🎓 Mahasiswa</button>
            </div>
        </div>

        <div class="auth-divider"></div>

        {{-- Error --}}
        @if($errors->any())
            <div class="alert alert-error" style="margin-bottom: 16px;">
                ⚠️ {{ $errors->first() }}
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="email">Email / Username</label>
                <input
                    type="text"
                    id="email"
                    name="email"
                    class="form-control"
                    placeholder="Masukkan email"
                    value="{{ old('email') }}"
                    autocomplete="email"
                    required
                >
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label" for="password">Password</label>
                <div style="position: relative;">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        style="padding-right: 44px;"
                        required
                    >
                    <button
                        type="button"
                        onclick="togglePass()"
                        style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%);
                               background: none; border: none; cursor: pointer; font-size: 14px; color: #aaa;">
                        <span id="eyeIcon">👁️</span>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px; justify-content: center; padding: 12px;">
                Masuk →
            </button>
        </form>
    </div>

    <p class="auth-footer">© 2024 SiReRu · Universitas Kebangsaan Republik Indonesia</p>
</div>

<script>
function fillDemo(role) {
    if (role === 'admin') {
        document.getElementById('email').value = 'admin@sireru.id';
        document.getElementById('password').value = 'admin123';
    } else {
        document.getElementById('email').value = 'budi@student.sireru.id';
        document.getElementById('password').value = 'user123';
    }
}
function togglePass() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') { input.type = 'text';     icon.textContent = '🙈'; }
    else                           { input.type = 'password'; icon.textContent = '👁️'; }
}
</script>
@endsection
