<?php

namespace Database\Seeders;

use App\Models\PendaftaranSkripsi;
use App\Models\JadwalSkripsi;
use App\Models\Dosen;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class JadwalSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil dosen berdasarkan NIK (pastikan NIK ini ada di tabel dosens)
        $penguji1 = Dosen::where('nik', 'D.13.0.595')->first(); // Dinda Dwarawati
        $penguji2 = Dosen::where('nik', 'D.15.0.656')->first(); // Andhita Nurul Khasanah
        $penguji3 = Dosen::where('nik', 'D.12.0.557')->first(); // Anna Rozana

        // Jika tidak ditemukan, cari berdasarkan nama
        if (!$penguji1) {
            $penguji1 = Dosen::where('name', 'LIKE', '%Dinda Dwarawati%')->first();
        }
        if (!$penguji2) {
            $penguji2 = Dosen::where('name', 'LIKE', '%Andhita Nurul Khasanah%')->first();
        }
        if (!$penguji3) {
            $penguji3 = Dosen::where('name', 'LIKE', '%Anna Rozana%')->first();
        }

        $skripsiApproved = PendaftaranSkripsi::where('status', 'approved')->get();

        foreach ($skripsiApproved as $skripsi) {
            $tanggal = Carbon::now()->subDays(rand(1, 15));
            $waktuMulai = Carbon::createFromTime(rand(8, 10), 0, 0);
            $waktuSelesai = (clone $waktuMulai)->addHours(2);

            JadwalSkripsi::updateOrCreate(
                ['pendaftaran_id' => $skripsi->id],
                [
                    'tanggal' => $tanggal,
                    'waktu_mulai' => $waktuMulai,
                    'waktu_selesai' => $waktuSelesai,
                    'ruang' => 'Ruang A.2.1',
                    'dosen_penguji_1' => $penguji1 ? $penguji1->name : 'Dinda Dwarawati, S.Psi., M.Psi., Psikolog',
                    'dosen_penguji_1_id' => $penguji1 ? $penguji1->id : null,
                    'dosen_penguji_2' => $penguji2 ? $penguji2->name : 'Andhita Nurul Khasanah, S.Psi., M.Psi. Psikolog',
                    'dosen_penguji_2_id' => $penguji2 ? $penguji2->id : null,
                    'dosen_penguji_3' => $penguji3 ? $penguji3->name : 'Anna Rozana, S.Psi., M.Psi. Psikolog',
                    'dosen_penguji_3_id' => $penguji3 ? $penguji3->id : null,
                    'keterangan' => 'Jadwal sidang skripsi',
                    'status' => 'terjadwal',
                ]
            );
        }
    }
}
