<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Notification;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ======== USERS ========
        $admin = User::create([
            'nim'      => 'ADM001',
            'name'     => 'Administrator',
            'email'    => 'admin@sireru.id',
            'password' => Hash::make('admin123'),
            'role'     => 'admin',
            'status'   => 'Aktif',
        ]);

        $budi = User::create([
            'nim'      => '2021001234',
            'name'     => 'Budi Santoso',
            'email'    => 'budi@student.sireru.id',
            'password' => Hash::make('user123'),
            'role'     => 'user',
            'status'   => 'Aktif',
        ]);

        $users = [
            ['nim' => '2021005678', 'name' => 'Siti Rahayu',    'email' => 'siti@student.sireru.id'],
            ['nim' => '2020009012', 'name' => 'Ahmad Fauzi',    'email' => 'ahmad@student.sireru.id'],
            ['nim' => '2022003456', 'name' => 'Dewi Pertiwi',   'email' => 'dewi@student.sireru.id'],
            ['nim' => '2023007890', 'name' => 'Rizky Pratama',  'email' => 'rizky@student.sireru.id'],
            ['nim' => '2021004321', 'name' => 'Nisa Amalia',    'email' => 'nisa@student.sireru.id'],
            ['nim' => '2022008765', 'name' => 'Farhan Hidayat', 'email' => 'farhan@student.sireru.id', 'status' => 'Nonaktif'],
        ];

        $createdUsers = [];
        foreach ($users as $u) {
            $createdUsers[] = User::create(array_merge([
                'password' => Hash::make('user123'),
                'role'     => 'user',
                'status'   => 'Aktif',
            ], $u));
        }

        // ======== ROOMS ========
        $rooms = [
            ['name' => 'Ruang Rapat A',  'type' => 'Ruang Rapat',  'capacity' => 20,  'location' => 'Gd.A Lt.2', 'facilities' => 'Proyektor,AC,Whiteboard',     'status' => 'Aktif'],
            ['name' => 'Aula Utama',     'type' => 'Aula',         'capacity' => 200, 'location' => 'Gd.B Lt.1', 'facilities' => 'Sound System,AC,Proyektor',    'status' => 'Aktif'],
            ['name' => 'Ruang Seminar B','type' => 'Ruang Seminar','capacity' => 50,  'location' => 'Gd.C Lt.3', 'facilities' => 'Proyektor,AC,Microphone',      'status' => 'Aktif'],
            ['name' => 'Lab Komputer 1', 'type' => 'Laboratorium', 'capacity' => 30,  'location' => 'Gd.A Lt.1', 'facilities' => 'Komputer,AC,Internet',         'status' => 'Aktif'],
            ['name' => 'Lab Komputer 2', 'type' => 'Laboratorium', 'capacity' => 30,  'location' => 'Gd.A Lt.1', 'facilities' => 'Komputer,AC,Internet',         'status' => 'Aktif'],
            ['name' => 'Ruang Kelas C',  'type' => 'Ruang Kelas',  'capacity' => 40,  'location' => 'Gd.D Lt.2', 'facilities' => 'Whiteboard,AC,Proyektor',      'status' => 'Aktif'],
            ['name' => 'Ruang Rapat B',  'type' => 'Ruang Rapat',  'capacity' => 15,  'location' => 'Gd.B Lt.2', 'facilities' => 'TV,AC,Whiteboard',             'status' => 'Maintenance'],
            ['name' => 'Aula Kecil',     'type' => 'Aula',         'capacity' => 80,  'location' => 'Gd.C Lt.1', 'facilities' => 'Sound System,AC',              'status' => 'Aktif'],
        ];

        $createdRooms = [];
        foreach ($rooms as $r) {
            $createdRooms[] = Room::create($r);
        }

        // ======== RESERVATIONS ========
        $reservationData = [
            ['user' => $budi,            'room' => $createdRooms[0], 'activity' => 'Rapat Himpunan Mahasiswa',  'date' => '2024-06-15', 'start' => '09:00', 'end' => '11:00', 'pax' => 18,  'notes' => 'Rapat rutin bulanan',              'status' => 'Disetujui'],
            ['user' => $createdUsers[0], 'room' => $createdRooms[1], 'activity' => 'Seminar Kewirausahaan',     'date' => '2024-06-20', 'start' => '13:00', 'end' => '17:00', 'pax' => 150, 'notes' => 'Menghadirkan pembicara industri',   'status' => 'Menunggu'],
            ['user' => $createdUsers[1], 'room' => $createdRooms[3], 'activity' => 'Pelatihan Coding',          'date' => '2024-06-18', 'start' => '08:00', 'end' => '12:00', 'pax' => 28,  'notes' => 'Workshop Python dasar',            'status' => 'Disetujui'],
            ['user' => $createdUsers[2], 'room' => $createdRooms[2], 'activity' => 'Presentasi Tugas Akhir',    'date' => '2024-06-22', 'start' => '10:00', 'end' => '12:00', 'pax' => 30,  'notes' => 'Sidang TA semester ini',           'status' => 'Menunggu'],
            ['user' => $budi,            'room' => $createdRooms[5], 'activity' => 'Latihan Debat',             'date' => '2024-06-17', 'start' => '15:00', 'end' => '17:00', 'pax' => 20,  'notes' => 'Latihan rutin UKM debat',          'status' => 'Ditolak'],
            ['user' => $createdUsers[3], 'room' => $createdRooms[4], 'activity' => 'Workshop Desain Grafis',    'date' => '2024-06-25', 'start' => '09:00', 'end' => '13:00', 'pax' => 25,  'notes' => 'Menggunakan software Adobe',        'status' => 'Menunggu'],
            ['user' => $createdUsers[4], 'room' => $createdRooms[7], 'activity' => 'Rapat BEM Universitas',     'date' => '2024-06-19', 'start' => '14:00', 'end' => '16:00', 'pax' => 60,  'notes' => 'Rapat koordinasi semester',        'status' => 'Disetujui'],
            ['user' => $createdUsers[0], 'room' => $createdRooms[2], 'activity' => 'Kompetisi Matematika',      'date' => '2024-06-28', 'start' => '07:00', 'end' => '12:00', 'pax' => 45,  'notes' => 'Olimpiade matematika tingkat univ', 'status' => 'Disetujui'],
        ];

        $createdRes = [];
        foreach ($reservationData as $res) {
            $createdRes[] = Reservation::create([
                'user_id'      => $res['user']->id,
                'room_id'      => $res['room']->id,
                'activity'     => $res['activity'],
                'date'         => $res['date'],
                'start_time'   => $res['start'],
                'end_time'     => $res['end'],
                'participants' => $res['pax'],
                'notes'        => $res['notes'],
                'status'       => $res['status'],
                'created_at'   => now()->subDays(rand(1, 10)),
            ]);
        }

        // ======== NOTIFICATIONS ========
        // Notifikasi untuk Budi Santoso
        Notification::create([
            'user_id'        => $budi->id,
            'reservation_id' => $createdRes[0]->id,
            'message'        => "Reservasi 'Rapat Himpunan Mahasiswa' di Ruang Rapat A telah DISETUJUI.",
            'type'           => 'approved',
            'read_at'        => null,
            'created_at'     => now()->subDays(2),
        ]);

        Notification::create([
            'user_id'        => $budi->id,
            'reservation_id' => $createdRes[4]->id,
            'message'        => "Reservasi 'Latihan Debat' di Ruang Kelas C telah DITOLAK. Ruangan sudah dibooking.",
            'type'           => 'rejected',
            'read_at'        => null,
            'created_at'     => now()->subDays(4),
        ]);

        Notification::create([
            'user_id'        => $budi->id,
            'reservation_id' => null,
            'message'        => "Permintaan reservasi 'Belajar Kelompok' sedang menunggu persetujuan admin.",
            'type'           => 'pending',
            'read_at'        => now()->subDays(1),
            'created_at'     => now()->subDays(6),
        ]);
    }
}
