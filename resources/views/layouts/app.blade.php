<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SiReRu – @yield('title', 'Sistem Reservasi Ruangan')</title>
    <link rel="stylesheet" href="{{ asset('css/sireru.css') }}">
    @stack('styles')
</head>
<body>

{{-- Mobile Overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="app-wrapper">

    {{-- ========== SIDEBAR ========== --}}
    <aside class="sidebar" id="sidebar">

        {{-- Header --}}
        <div class="sidebar-header">
            <div class="sidebar-role-label">
                {{ auth()->user()->role === 'admin' ? 'ADMIN PANEL' : 'Portal Mahasiswa' }}
            </div>
            <div class="sidebar-brand">🏫 SiReRu</div>
            <div class="sidebar-brand-sub">Sistem Reservasi Ruangan</div>
        </div>

        {{-- User Info --}}
        <div class="sidebar-user">
            <div class="sidebar-avatar">
                {{ auth()->user()->role === 'admin' ? '👤' : '🎓' }}
            </div>
            <div>
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-sub">
                    {{ auth()->user()->role === 'admin'
                        ? auth()->user()->email
                        : 'NIM: ' . auth()->user()->nim }}
                </div>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            @if(auth()->user()->role === 'admin')
                @php
                    $adminMenus = [
                        ['route' => 'admin.dashboard',  'label' => 'Dashboard',            'icon' => '📊'],
                        ['route' => 'admin.permintaan', 'label' => 'Permintaan Reservasi', 'icon' => '📋'],
                        ['route' => 'admin.ruangan',    'label' => 'Manajemen Ruangan',    'icon' => '🏛️'],
                        ['route' => 'admin.reservasi',  'label' => 'Semua Reservasi',      'icon' => '📅'],
                        ['route' => 'admin.pengguna',   'label' => 'Manajemen Pengguna',   'icon' => '👥'],
                        ['route' => 'admin.laporan',    'label' => 'Laporan Penggunaan',   'icon' => '📈'],
                        ['route' => 'admin.notifikasi', 'label' => 'Notifikasi',           'icon' => '🔔'],
                    ];
                @endphp
                @foreach($adminMenus as $menu)
                    <a href="{{ route($menu['route']) }}"
                       class="sidebar-nav-item {{ request()->routeIs($menu['route']) ? 'active' : '' }}">
                        <span class="sidebar-nav-icon">{{ $menu['icon'] }}</span>
                        <span class="sidebar-nav-label">{{ $menu['label'] }}</span>
                        @if($menu['route'] === 'admin.notifikasi' && ($adminUnreadNotif ?? 0) > 0)
                            <span class="notif-badge">{{ $adminUnreadNotif }}</span>
                        @endif
                    </a>
                @endforeach
            @else
                @php
                    $userMenus = [
                        ['route' => 'user.beranda',         'label' => 'Beranda',           'icon' => '🏠'],
                        ['route' => 'user.cari-ruangan',    'label' => 'Cari Ruangan',      'icon' => '🔍'],
                        ['route' => 'user.buat-reservasi',  'label' => 'Buat Reservasi',    'icon' => '✏️'],
                        ['route' => 'user.riwayat',         'label' => 'Riwayat Reservasi', 'icon' => '📜'],
                        ['route' => 'user.notifikasi',      'label' => 'Notifikasi',        'icon' => '🔔'],
                    ];
                @endphp
                @foreach($userMenus as $menu)
                    <a href="{{ route($menu['route']) }}"
                       class="sidebar-nav-item {{ request()->routeIs($menu['route']) ? 'active' : '' }}">
                        <span class="sidebar-nav-icon">{{ $menu['icon'] }}</span>
                        <span class="sidebar-nav-label">{{ $menu['label'] }}</span>
                        @if($menu['route'] === 'user.notifikasi' && ($unreadNotif ?? 0) > 0)
                            <span class="notif-badge">{{ $unreadNotif }}</span>
                        @endif
                    </a>
                @endforeach
            @endif
        </nav>

        {{-- Logout --}}
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    🚪 Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ========== MAIN CONTENT ========== --}}
    <div class="main-content">

        {{-- Topbar --}}
        <header class="topbar">
            <button class="hamburger-btn" onclick="openSidebar()">☰</button>
            <div>
                <span class="breadcrumb-sub">
                    {{ auth()->user()->role === 'admin' ? 'Admin Panel' : 'Portal Mahasiswa' }} /&nbsp;
                </span>
                <span class="breadcrumb-page">@yield('breadcrumb', 'Dashboard')</span>
            </div>
            <div class="topbar-user">
                <span>{{ auth()->user()->role === 'admin' ? '👤' : '🎓' }}</span>
                <span class="topbar-user-name">{{ auth()->user()->name }}</span>
            </div>
        </header>

        {{-- Flash Messages --}}
        <div style="padding: 0 28px;">
            @if(session('success'))
                <div class="alert alert-success" style="margin-top: 16px;">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error" style="margin-top: 16px;">⚠️ {{ session('error') }}</div>
            @endif
            @if(session('info'))
                <div class="alert alert-info" style="margin-top: 16px;">ℹ️ {{ session('info') }}</div>
            @endif
        </div>

        {{-- Page Content --}}
        <main class="page-content">
            @yield('content')
        </main>
    </div>

</div>

<script>
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebarOverlay').style.display = 'block';
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').style.display = 'none';
}
// Auto-close alerts after 4s
setTimeout(() => {
    document.querySelectorAll('.alert').forEach(el => {
        el.style.transition = 'opacity 0.5s';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 500);
    });
}, 4000);
</script>

@stack('scripts')
</body>
</html>
