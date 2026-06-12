<div align="center">

# 🏛️ SiReRu
### Sistem Reservasi Ruangan

Aplikasi web berbasis Laravel untuk manajemen reservasi ruangan kampus — dari pengajuan oleh mahasiswa hingga persetujuan oleh admin, semua dalam satu platform.

<br>

**Kelompok 7 · Rekayasa Perangkat Lunak**

| Nama | NIM |
|------|-----|
| Aisma Haidy Putri Berry Ani Nur Rizeki | 20241320001 |
| Adrian Ronald Daga | 20241320011 |
| Arya Adi Muhammad Iqbal | 20241320018 |
| Nayla Rabia Gustari | 20241320034 |
| Fakhry Ahmad Fauzan | 20241320038 |

</div>

---

## Fitur Utama

**Role Admin**
- Dashboard statistik reservasi & ruangan
- Kelola data ruangan (tambah, edit, hapus)
- Setujui atau tolak permintaan reservasi
- Manajemen akun pengguna (aktif/nonaktif)
- Laporan & ekspor data reservasi
- Notifikasi permintaan masuk

**Role Mahasiswa**
- Cari & lihat ketersediaan ruangan
- Buat reservasi dengan detail waktu dan keperluan
- Pantau riwayat & status reservasi
- Terima notifikasi hasil pengajuan

---

## Tech Stack

- **Backend** — Laravel 11 + PHP
- **Database** — MySQL
- **Frontend** — Blade Templates + CSS kustom (`sireru.css`)
- **Auth** — Laravel built-in + Role Middleware

---

## Instalasi

### Prasyarat
- PHP ≥ 8.2
- Composer
- MySQL
- Node.js (opsional, untuk Vite)

### Langkah-langkah

**1. Clone repository**
```bash
git clone https://github.com/NoahMikhailovna/Kelompok-7-SIRERU.git
cd Kelompok-7-SIRERU
```

**2. Install dependensi**
```bash
composer install
```

**3. Konfigurasi environment**
```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env`, sesuaikan bagian database:
```env
DB_DATABASE=sireru
DB_USERNAME=root
DB_PASSWORD=
```

**4. Daftarkan Middleware**

> **Laravel 11** — edit `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

> **Laravel 10** — edit `app/Http/Kernel.php` di bagian `$routeMiddleware`:
```php
'role' => \App\Http\Middleware\RoleMiddleware::class,
```

**5. Daftarkan ViewServiceProvider**

> **Laravel 11** — edit `bootstrap/providers.php`:
```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ViewServiceProvider::class, // ← tambahkan
];
```

> **Laravel 10** — edit `config/app.php` di bagian `providers`:
```php
App\Providers\ViewServiceProvider::class,
```

**6. Migrasi & seed database**
```bash
php artisan migrate:fresh --seed
```

**7. Jalankan server**
```bash
php artisan serve
```

Buka di browser: [http://localhost:8000](http://localhost:8000)

---

## Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@sireru.id | admin123 |
| Mahasiswa | budi@student.sireru.id | user123 |

---

## Struktur Proyek

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php        # Login & Logout
│   │   ├── AdminController.php       # Dashboard, Ruangan, Reservasi, dll
│   │   └── UserController.php        # Beranda, Cari, Buat Reservasi, dll
│   └── Middleware/
│       └── RoleMiddleware.php         # Pemisahan akses admin & mahasiswa
├── Models/
│   ├── User.php
│   ├── Room.php
│   ├── Reservation.php
│   └── Notification.php
└── Providers/
    └── ViewServiceProvider.php        # Auto-inject badge notifikasi

database/
├── migrations/                        # 4 file migrasi
└── seeders/
    └── DatabaseSeeder.php             # Data demo lengkap

public/css/
└── sireru.css                         # Stylesheet utama

resources/views/
├── layouts/
│   ├── auth.blade.php
│   └── app.blade.php
├── auth/
│   └── login.blade.php
├── admin/
│   ├── dashboard.blade.php
│   ├── permintaan/index.blade.php
│   ├── ruangan/index.blade.php
│   ├── ruangan/_form.blade.php
│   ├── reservasi/index.blade.php
│   ├── pengguna/index.blade.php
│   └── laporan/index.blade.php
└── user/
    ├── beranda.blade.php
    ├── cari-ruangan.blade.php
    ├── buat-reservasi.blade.php
    ├── riwayat.blade.php
    └── notifikasi.blade.php

routes/
└── web.php                            # Semua route web
```

---

## Cara Merge ke Project Laravel yang Sudah Ada

Jika ingin mengintegrasikan kode ini ke project Laravel yang sudah berjalan:

1. **Copy semua isi folder** ini ke dalam project Laravel kamu
2. **Merge** dengan struktur yang sudah ada (jangan timpa file yang tidak berkaitan)
3. Ikuti **Langkah 4–6** di atas (middleware, provider, migrate & seed)

---

<div align="center">
  <sub>Dibuat dengan ❤️ oleh Kelompok 7</sub>
</div>
