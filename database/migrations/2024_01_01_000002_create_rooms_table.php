<?php
// MIGRATION: 2024_01_01_000002_create_rooms_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');            // Ruang Rapat, Aula, dst.
            $table->integer('capacity');
            $table->string('location');
            $table->text('facilities')->nullable(); // comma-separated: "Proyektor,AC,Whiteboard"
            $table->enum('status', ['Aktif', 'Maintenance'])->default('Aktif');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
