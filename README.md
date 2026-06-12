# SiReRu — Sistem Reservasi Ruangan
## Kelompok 7
Aisma Haidy Putri Berry Ani Nur Rizeki 	20241320001
Adrian Ronald Daga	20241320011
Arya Adi Muhammad Iqbal	20241320018
Nayla Rabia Gustari	20241320034
Fakhry Ahmad Fauzan	20241320038


### Cara Pakai
Copy semua isi folder ini ke dalam project Laravel kamu, **merge** dengan struktur yang sudah ada.

---

### Langkah 1 — Daftarkan Middleware

**Laravel 11** → edit `bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

**Laravel 10** → edit `app/Http/Kernel.php`, bagian `$routeMiddleware`:
```php
'role' => \App\Http\Middleware\RoleMiddleware::class,
```

---

### Langkah 2 — Daftarkan ViewServiceProvider

**Laravel 11** → edit `bootstrap/providers.php`:
```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\ViewServiceProvider::class,  // ← tambahkan
];
```

**Laravel 10** → edit `config/app.php`, bagian `providers`:
```php
App\Providers\ViewServiceProvider::class,
```

---

### Langkah 3 — Jalankan

```bash
php artisan migrate:fresh --seed
php artisan serve
```

---

### Akun Demo

| Role       | Email                      | Password  |
|------------|----------------------------|-----------|
| Admin      | admin@sireru.id            | admin123  |
| Mahasiswa  | budi@student.sireru.id     | user123   |

---

### Struktur File yang Disertakan

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php      ← Login/Logout
│   │   ├── AdminController.php     ← Dashboard, Ruangan, Reservasi, dst
│   │   └── UserController.php      ← Beranda, Cari, Buat Reservasi, dst
│   └── Middleware/
│       └── RoleMiddleware.php      ← Pisah akses admin vs mahasiswa
├── Models/
│   ├── User.php
│   ├── Room.php
│   ├── Reservation.php
│   └── Notification.php
└── Providers/
    └── ViewServiceProvider.php     ← Auto-inject badge notif

database/
├── migrations/                     ← 4 migration files
└── seeders/
    └── DatabaseSeeder.php          ← Data demo lengkap

public/css/
└── sireru.css                      ← Stylesheet utama

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
└── web.php
```
