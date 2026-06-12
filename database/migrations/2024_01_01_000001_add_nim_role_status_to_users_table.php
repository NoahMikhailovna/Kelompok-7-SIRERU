<?php
// ============================================================
// MIGRATION: 2024_01_01_000001_add_nim_role_status_to_users_table.php
// ============================================================
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nim')->nullable()->after('id');
            $table->enum('role', ['admin', 'user'])->default('user')->after('email');
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif')->after('role');
        });
    }
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nim', 'role', 'status']);
        });
    }
};
