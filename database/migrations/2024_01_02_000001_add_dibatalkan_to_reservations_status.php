<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('Menunggu','Disetujui','Ditolak','Dibatalkan') NOT NULL DEFAULT 'Menunggu'");
    }

    public function down(): void
    {
        // Ubah data Dibatalkan → Ditolak dulu sebelum hapus enum value
        DB::statement("UPDATE reservations SET status = 'Ditolak' WHERE status = 'Dibatalkan'");
        DB::statement("ALTER TABLE reservations MODIFY COLUMN status ENUM('Menunggu','Disetujui','Ditolak') NOT NULL DEFAULT 'Menunggu'");
    }
};
