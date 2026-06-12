<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'room_id', 'activity', 'date',
        'start_time', 'end_time', 'participants', 'notes', 'rejection_reason', 'status',
    ];

    // Status: Menunggu | Disetujui | Ditolak | Dibatalkan

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function notification()
    {
        return $this->hasOne(Notification::class);
    }

    /**
     * Scope: filter by status
     */
    public function scopeByStatus($query, $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope: filter by month (Y-m format)
     */
    public function scopeByMonth($query, $month)
    {
        if ($month) {
            return $query->where('date', 'like', $month . '%');
        }
        return $query;
    }
}
