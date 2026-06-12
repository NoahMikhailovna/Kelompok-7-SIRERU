<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'type', 'capacity', 'location', 'facilities', 'status',
    ];

    /**
     * Relasi: satu ruangan punya banyak reservasi
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Scope: hanya ruangan aktif
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    /**
     * Helper: cek ketersediaan di tanggal & jam tertentu
     */
    public function isAvailableOn(string $date, string $startTime, string $endTime): bool
    {
        return !$this->reservations()
            ->where('date', $date)
            ->where('status', 'Disetujui')
            ->where(function ($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function ($q2) use ($startTime, $endTime) {
                      $q2->where('start_time', '<=', $startTime)
                         ->where('end_time', '>=', $endTime);
                  });
            })->exists();
    }
}
