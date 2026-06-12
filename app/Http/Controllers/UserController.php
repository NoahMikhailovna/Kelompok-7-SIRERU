<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // ─────────────────────────────────────────
    //  BERANDA
    // ─────────────────────────────────────────
    public function beranda()
    {
        $user = Auth::user();

        $total     = $user->reservations()->count();
        $disetujui = $user->reservations()->where('status', 'Disetujui')->count();
        $menunggu  = $user->reservations()->where('status', 'Menunggu')->count();

        $recentReservations = $user->reservations()
            ->with('room')->latest()->take(5)->get();

        // Ketersediaan ruangan hari ini
        $rooms = Room::where('status', 'Aktif')->get();
        $today = now()->toDateString();

        $roomAvailability = $rooms->map(function ($room) use ($today) {
            $approvedToday = $room->reservations()
                ->where('date', $today)
                ->where('status', 'Disetujui')
                ->count();

            if ($approvedToday === 0) {
                $status = 'tersedia';
            } elseif ($approvedToday >= 3) {
                $status = 'penuh';
            } else {
                $status = 'sebagian';
            }

            return ['name' => $room->name, 'status' => $status];
        });

        return view('user.beranda', compact(
            'total', 'disetujui', 'menunggu', 'recentReservations', 'roomAvailability'
        ));
    }

    // ─────────────────────────────────────────
    //  CARI RUANGAN
    // ─────────────────────────────────────────
    public function cariRuangan(Request $request)
    {
        $rooms = collect();

        if ($request->hasAny(['tanggal', 'kapasitas_min', 'jenis', 'fasilitas'])) {
            $query = Room::where('status', 'Aktif');

            if ($request->kapasitas_min) {
                $query->where('capacity', '>=', $request->kapasitas_min);
            }

            if ($request->jenis) {
                $query->where('type', $request->jenis);
            }

            if ($request->fasilitas) {
                $query->where('facilities', 'like', '%' . $request->fasilitas . '%');
            }

            $rooms = $query->get()->map(function ($room) use ($request) {
                if ($request->tanggal && $request->jam_mulai && $request->jam_selesai) {
                    $conflict = $room->reservations()
                        ->where('date', $request->tanggal)
                        ->whereIn('status', ['Disetujui', 'Menunggu'])
                        ->where(function ($q) use ($request) {
                            $q->where('start_time', '<', $request->jam_selesai)
                              ->where('end_time',   '>', $request->jam_mulai);
                        })->exists();

                    $room->availability_status = $conflict ? 'Penuh' : 'Tersedia';
                } else {
                    $room->availability_status = 'Tersedia';
                }

                return $room;
            });
        }

        return view('user.cari-ruangan', compact('rooms'));
    }

    // ─────────────────────────────────────────
    //  BUAT RESERVASI
    // ─────────────────────────────────────────
    public function buatReservasi(Request $request)
    {
        $rooms = Room::where('status', 'Aktif')->get();
        return view('user.buat-reservasi', compact('rooms'));
    }

    public function buatReservasiStore(Request $request)
    {
        $data = $request->validate([
            'activity'     => 'required|string|max:255',
            'room_id'      => 'required|exists:rooms,id',
            'participants' => 'required|integer|min:1',
            'date'         => 'required|date|after_or_equal:today',
            'start_time'   => 'required',
            'end_time'     => 'required|after:start_time',
            'notes'        => 'nullable|string|max:500',
        ]);

        // Cek konflik jadwal (strict overlap — adjacent booking tidak konflik)
        $conflict = Reservation::where('room_id', $data['room_id'])
            ->where('date', $data['date'])
            ->whereIn('status', ['Disetujui', 'Menunggu'])
            ->where(function ($q) use ($data) {
                // overlap terjadi jika: start_existing < end_new AND end_existing > start_new
                $q->where('start_time', '<', $data['end_time'])
                  ->where('end_time',   '>', $data['start_time']);
            })->exists();

        if ($conflict) {
            return back()
                ->withErrors(['date' => 'Ruangan sudah dipesan pada waktu tersebut.'])
                ->withInput();
        }

        $reservation = Reservation::create([
            ...$data,
            'user_id' => Auth::id(),
            'status'  => 'Menunggu',
        ]);

        // Notifikasi pending untuk user
        Notification::create([
            'user_id'        => Auth::id(),
            'reservation_id' => $reservation->id,
            'message'        => "Permintaan reservasi '{$reservation->activity}' sedang menunggu persetujuan admin.",
            'type'           => 'pending',
        ]);

        // Notifikasi untuk semua admin tentang reservasi baru
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id'        => $admin->id,
                'reservation_id' => $reservation->id,
                'message'        => "Reservasi baru '{$reservation->activity}' dari {$reservation->user->name} di {$reservation->room->name} menunggu persetujuan.",
                'type'           => 'pending',
            ]);
        }

        return redirect()->route('user.riwayat')
            ->with('success', 'Reservasi berhasil diajukan! Silakan tunggu persetujuan admin.');
    }

    // ─────────────────────────────────────────
    //  RIWAYAT RESERVASI
    // ─────────────────────────────────────────
    public function riwayat()
    {
        $reservations = Auth::user()
            ->reservations()
            ->with('room')
            ->latest()
            ->paginate(10);

        return view('user.riwayat', compact('reservations'));
    }

    public function cancelReservasi($id)
    {
        $reservation = Reservation::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'Menunggu')
            ->firstOrFail();

        $reservation->update(['status' => 'Dibatalkan']);

        // Notifikasi untuk semua admin bahwa user membatalkan reservasi
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'user_id'        => $admin->id,
                'reservation_id' => $reservation->id,
                'message'        => "Reservasi '{$reservation->activity}' oleh {$reservation->user->name} di {$reservation->room->name} telah DIBATALKAN oleh pemohon.",
                'type'           => 'pending',
            ]);
        }

        return back()->with('success', 'Reservasi berhasil dibatalkan.');
    }

    // ─────────────────────────────────────────
    //  NOTIFIKASI
    // ─────────────────────────────────────────
    public function notifikasi()
    {
        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->paginate(20);

        $unread = Auth::user()->notifications()->whereNull('read_at')->count();

        return view('user.notifikasi', compact('notifications', 'unread'));
    }

    public function markRead($id)
    {
        Notification::where('id', $id)
            ->where('user_id', Auth::id())
            ->update(['read_at' => now()]);

        return back();
    }

    public function markAllRead()
    {
        Auth::user()
            ->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back()->with('success', 'Semua notifikasi sudah ditandai dibaca.');
    }
}
