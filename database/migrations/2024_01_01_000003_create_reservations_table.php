<?php
// MIGRATION: 2024_01_01_000003_create_reservations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('room_id')->constrained()->onDelete('cascade');
            $table->string('activity');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('participants');
            $table->text('notes')->nullable();
            $table->enum('status', ['Menunggu', 'Disetujui', 'Ditolak', 'Dibatalkan'])->default('Menunggu');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
