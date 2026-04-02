<?php

namespace Database\Seeders;

use App\Models\PendaftaranSkripsi;
use App\Models\PendaftaranMetodologi;
use App\Models\JadwalSkripsi;
use App\Models\JadwalMetodologi;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        // Skripsi
        $skripsiApproved = PendaftaranSkripsi::where('status', 'approved')->get();
        foreach ($skripsiApproved as $index => $skripsi) {
            // Buat jadwal untuk semua
            $tanggal = Carbon::now()->addDays(rand(7, 30));
            $waktuMulai = Carbon::createFromTime(rand(8, 10), 0, 0);
            $waktuSelesai = (clone $waktuMulai)->addHours(2);

            JadwalSkripsi::updateOrCreate(
                ['pendaftaran_id' => $skripsi->id],
                [
                    'tanggal' => $tanggal,
                    'waktu_mulai' => $waktuMulai,
                    'waktu_selesai' => $waktuSelesai,
                    'ruang' => 'Ruang ' . chr(rand(65, 70)) . '.' . rand(1, 3) . '.' . rand(1, 2),
                    'dosen_penguji_1' => 'Prof. ' . $this->randomName(),
                    'dosen_penguji_2' => 'Dr. ' . $this->randomName(),
                    'dosen_penguji_3' => rand(0, 1) ? 'Dr. ' . $this->randomName() : null,
                    'keterangan' => 'Jadwal sidang skripsi',
                    'status' => 'terjadwal',
                ]
            );
        }

        // Metodologi
        $metodologiApproved = PendaftaranMetodologi::where('status', 'approved')->get();
        foreach ($metodologiApproved as $metodologi) {
            $tanggal = Carbon::now()->addDays(rand(10, 35));
            $waktuMulai = Carbon::createFromTime(rand(13, 15), 0, 0);
            $waktuSelesai = (clone $waktuMulai)->addHours(1.5);

            JadwalMetodologi::updateOrCreate(
                ['pendaftaran_id' => $metodologi->id],
                [
                    'tanggal' => $tanggal,
                    'waktu_mulai' => $waktuMulai,
                    'waktu_selesai' => $waktuSelesai,
                    'ruang' => 'Ruang ' . chr(rand(65, 70)) . '.' . rand(1, 3) . '.' . rand(1, 2),
                    'dosen_penguji_1' => 'Prof. ' . $this->randomName(),
                    'dosen_penguji_2' => 'Dr. ' . $this->randomName(),
                    'keterangan' => 'Jadwal ujian metodologi',
                    'status' => 'terjadwal',
                ]
            );
        }
    }

    private function randomName(): string
    {
        $names = ['Ahmad', 'Siti', 'Budi', 'Dewi', 'Rizki', 'Nadia', 'Hendra', 'Yulia', 'Arief', 'Dina', 'Farhan', 'Lestari'];
        return $names[array_rand($names)] . ' ' . $names[array_rand($names)];
    }
}