<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\User;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AdminController extends Controller
{
    // ─────────────────────────────────────────
    //  DASHBOARD
    // ─────────────────────────────────────────
    public function dashboard()
    {
        $totalRooms  = Room::where('status', 'Aktif')->count();
        $thisMonth   = Reservation::whereMonth('date', now()->month)
                                  ->whereYear('date', now()->year)->count();
        $pending     = Reservation::where('status', 'Menunggu')->count();
        $activeUsers = User::where('role', 'user')->where('status', 'Aktif')->count();

        $recentReservations = Reservation::with(['user', 'room'])
            ->latest()->take(8)->get();

        // Penggunaan ruangan (top 6)
        $roomUsage = Reservation::where('status', 'Disetujui')
            ->selectRaw('room_id, COUNT(*) as count')
            ->groupBy('room_id')
            ->orderByDesc('count')
            ->take(6)
            ->with('room')
            ->get()
            ->map(fn($r) => ['name' => $r->room->name ?? '-', 'count' => $r->count]);
        $maxUsage = $roomUsage->max('count') ?: 1;

        // Status count
        $statusCount = Reservation::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Admin notifications (pending reservations)
        $adminNotifications = Notification::whereHas('user', fn($q) => $q->where('role', 'admin'))
            ->orWhereNull('user_id')
            ->with(['reservation.user', 'reservation.room'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalRooms', 'thisMonth', 'pending', 'activeUsers',
            'recentReservations', 'roomUsage', 'maxUsage', 'statusCount'
        ));
    }

    // ─────────────────────────────────────────
    //  PERMINTAAN RESERVASI
    // ─────────────────────────────────────────
    public function permintaan(Request $request)
    {
        $query = Reservation::with(['user', 'room'])->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('activity', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('room', fn($r) => $r->where('name', 'like', "%{$search}%"));
            });
        }

        $reservations = $query->paginate(15)->withQueryString();

        return view('admin.permintaan.index', compact('reservations'));
    }

    public function approve(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update(['status' => 'Disetujui']);

        // Notifikasi untuk user (mahasiswa)
        Notification::create([
            'user_id'        => $reservation->user_id,
            'reservation_id' => $reservation->id,
            'message'        => "Reservasi '{$reservation->activity}' di {$reservation->room->name} telah DISETUJUI.",
            'type'           => 'approved',
        ]);

        return back()->with('success', 'Reservasi berhasil disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $reservation = Reservation::findOrFail($id);
        $reservation->update([
            'status'           => 'Ditolak',
            'rejection_reason' => $request->rejection_reason,
        ]);

        // Notifikasi untuk user (mahasiswa) — sertakan alasan
        Notification::create([
            'user_id'        => $reservation->user_id,
            'reservation_id' => $reservation->id,
            'message'        => "Reservasi '{$reservation->activity}' di {$reservation->room->name} telah DITOLAK. Alasan: {$request->rejection_reason}",
            'type'           => 'rejected',
        ]);

        return back()->with('success', 'Reservasi berhasil ditolak.');
    }

    // ─────────────────────────────────────────
    //  MANAJEMEN RUANGAN
    // ─────────────────────────────────────────
    public function ruangan()
    {
        $rooms = Room::orderBy('id')->get();
        return view('admin.ruangan.index', compact('rooms'));
    }

    public function ruanganStore(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'type'       => 'required|string',
            'capacity'   => 'required|integer|min:1',
            'location'   => 'required|string|max:255',
            'facilities' => 'nullable|array',
            'status'     => 'required|in:Aktif,Maintenance',
        ]);

        $data['facilities'] = implode(',', $data['facilities'] ?? []);

        Room::create($data);

        return back()->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function ruanganUpdate(Request $request, $id)
    {
        $room = Room::findOrFail($id);

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'type'       => 'required|string',
            'capacity'   => 'required|integer|min:1',
            'location'   => 'required|string|max:255',
            'facilities' => 'nullable|array',
            'status'     => 'required|in:Aktif,Maintenance',
        ]);

        $data['facilities'] = implode(',', $data['facilities'] ?? []);

        $room->update($data);

        return back()->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function ruanganDestroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return back()->with('success', 'Ruangan berhasil dihapus.');
    }

    // ─────────────────────────────────────────
    //  SEMUA RESERVASI
    // ─────────────────────────────────────────
    public function reservasi(Request $request)
    {
        $query = Reservation::with(['user', 'room'])->latest();

        if ($request->bulan) {
            $query->where('date', 'like', $request->bulan . '%');
        }

        $reservations = $query->paginate(20)->withQueryString();

        return view('admin.reservasi.index', compact('reservations'));
    }

    public function reservasiExport(Request $request)
    {
        $query = Reservation::with(['user', 'room'])->orderBy('date')->orderBy('start_time');

        if ($request->bulan) {
            $query->where('date', 'like', $request->bulan . '%');
        }

        $reservations = $query->get();

        // Build proper Excel-compatible CSV with BOM for UTF-8
        $bom = "\xEF\xBB\xBF";
        $csv = $bom . "ID,Nama Pemohon,NIM,Kegiatan,Ruangan,Tanggal,Jam Mulai,Jam Selesai,Jumlah Peserta,Status\n";

        foreach ($reservations as $r) {
            $row = [
                $r->id,
                $r->user->name ?? '-',
                $r->user->nim ?? '-',
                $r->activity,
                $r->room->name ?? '-',
                Carbon::parse($r->date)->format('d/m/Y'),
                substr($r->start_time, 0, 5),
                substr($r->end_time, 0, 5),
                $r->participants,
                $r->status,
            ];

            // Escape fields with commas or quotes
            $escapedRow = array_map(function($field) {
                if (str_contains((string)$field, ',') || str_contains((string)$field, '"') || str_contains((string)$field, "\n")) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }
                return $field;
            }, $row);

            $csv .= implode(',', $escapedRow) . "\n";
        }

        $filename = 'laporan_reservasi_' . ($request->bulan ?? now()->format('Y-m')) . '.csv';

        return Response::make($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ─────────────────────────────────────────
    //  MANAJEMEN PENGGUNA
    // ─────────────────────────────────────────
    public function pengguna()
    {
        $users = User::withCount('reservations')->orderBy('id')->get();
        return view('admin.pengguna.index', compact('users'));
    }

    public function penggunaStore(Request $request)
    {
        $rules = [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'required|in:admin,user',
        ];

        if ($request->role === 'user') {
            $rules['nim'] = 'required|string|max:20|unique:users,nim';
        }

        $data = $request->validate($rules);

        User::create([
            'nim'      => $data['nim'] ?? null,
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
            'role'     => $data['role'],
            'status'   => 'Aktif',
        ]);

        return back()->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function penggunaUpdate(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $rules = [
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ];

        if ($user->role === 'user') {
            $rules['nim'] = 'required|string|max:20|unique:users,nim,' . $id;
        }

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6|confirmed';
        }

        $data = $request->validate($rules);

        $updateData = [
            'name'  => $data['name'],
            'email' => $data['email'],
        ];

        if ($user->role === 'user') {
            $updateData['nim'] = $data['nim'];
        }

        if ($request->filled('password')) {
            $updateData['password'] = bcrypt($data['password']);
        }

        $user->update($updateData);

        return back()->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function penggunaToggle($id)
    {
        $user = User::findOrFail($id);
        $user->update([
            'status' => $user->status === 'Aktif' ? 'Nonaktif' : 'Aktif',
        ]);

        return back()->with('success', 'Status pengguna berhasil diubah.');
    }

    public function penggunaDestroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->role === 'admin') {
            return back()->with('error', 'Akun admin tidak bisa dihapus.');
        }
        $user->delete();
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }

    // ─────────────────────────────────────────
    //  NOTIFIKASI ADMIN
    // ─────────────────────────────────────────
    public function notifikasi()
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->with(['reservation.user', 'reservation.room'])
            ->latest()
            ->paginate(20);

        $unread = Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return view('admin.notifikasi.index', compact('notifications', 'unread'));
    }

    public function notifikasiRead($id)
    {
        $notif = Notification::where('user_id', auth()->id())->findOrFail($id);
        $notif->update(['read_at' => now()]);
        return back();
    }

    public function notifikasiReadAll()
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        return back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }

    // ─────────────────────────────────────────
    //  LAPORAN
    // ─────────────────────────────────────────
    public function laporan(Request $request)
    {
        // Ambil tahun yang ada di database + tahun sekarang + tahun berikutnya
        $existingYears = Reservation::selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();

        $currentYear = (int) now()->year;
        $nextYear    = $currentYear + 1;

        // Gabungkan: tahun sekarang & tahun depan selalu ada, plus tahun yang punya data
        $availableYears = collect(array_merge([$nextYear, $currentYear], $existingYears))
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();

        $selectedYear = (int) ($request->tahun ?? $currentYear);

        // Filter bulan (bisa multiple)
        $selectedMonths = $request->has('bulan') ? array_map('intval', (array) $request->bulan) : [];

        // Hitung stat cards
        $statQuery = Reservation::query();
        if ($selectedYear) {
            $statQuery->whereYear('date', $selectedYear);
        }
        if (!empty($selectedMonths)) {
            $statQuery->whereIn(\DB::raw('MONTH(date)'), $selectedMonths);
        }

        $totalReservasi = (clone $statQuery)->count();
        $disetujui      = (clone $statQuery)->where('status', 'Disetujui')->count();
        $ditolak        = (clone $statQuery)->where('status', 'Ditolak')->count();

        // Tren 12 bulan dalam tahun yang dipilih
        $bulanId = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $trendData = [];
        for ($m = 1; $m <= 12; $m++) {
            $q = Reservation::whereYear('date', $selectedYear)->whereMonth('date', $m);
            $trendData[] = [
                'month'    => $bulanId[$m - 1],
                'month_no' => $m,
                'total'    => $q->count(),
                'selected' => in_array($m, $selectedMonths),
            ];
        }

        // Rekap per ruangan (filter by selected months)
        $rooms = Room::withCount([
            'reservations as total'     => fn($q) => $this->applyLaporanFilter($q, $selectedYear, $selectedMonths),
            'reservations as disetujui' => fn($q) => $this->applyLaporanFilter($q, $selectedYear, $selectedMonths)->where('status', 'Disetujui'),
            'reservations as ditolak'   => fn($q) => $this->applyLaporanFilter($q, $selectedYear, $selectedMonths)->where('status', 'Ditolak'),
            'reservations as menunggu'  => fn($q) => $this->applyLaporanFilter($q, $selectedYear, $selectedMonths)->where('status', 'Menunggu'),
        ])->get();

        $maxTotal  = $rooms->max('total') ?: 1;
        $roomStats = $rooms->map(function ($r) use ($maxTotal, $selectedYear, $selectedMonths) {
            $hoursQuery = Reservation::where('room_id', $r->id)->where('status', 'Disetujui');
            $this->applyLaporanFilter($hoursQuery, $selectedYear, $selectedMonths);
            $hours = $hoursQuery->get()->sum(function ($res) {
                $start = Carbon::parse($res->start_time);
                $end   = Carbon::parse($res->end_time);
                return round($start->diffInMinutes($end) / 60, 2);
            });

            return [
                'name'      => $r->name,
                'total'     => $r->total,
                'disetujui' => $r->disetujui,
                'ditolak'   => $r->ditolak,
                'menunggu'  => $r->menunggu,
                'hours'     => $hours,
                'pct'       => $maxTotal > 0 ? round($r->total / $maxTotal * 100) : 0,
            ];
        })->sortByDesc('total')->values();

        return view('admin.laporan.index', compact(
            'totalReservasi', 'disetujui', 'ditolak',
            'trendData', 'roomStats', 'selectedYear', 'availableYears', 'selectedMonths'
        ));
    }

    private function applyLaporanFilter($query, $selectedYear, $selectedMonths)
    {
        if ($selectedYear) {
            $query->whereYear('date', $selectedYear);
        }
        if (!empty($selectedMonths)) {
            $query->whereIn(\DB::raw('MONTH(date)'), $selectedMonths);
        }
        return $query;
    }
}