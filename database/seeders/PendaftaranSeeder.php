<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\PendaftaranSkripsi;
use App\Models\AcademicPeriod;
use Illuminate\Database\Seeder;

class PendaftaranSeeder extends Seeder
{
    public function run(): void
    {
        $periodeAktif = AcademicPeriod::where('is_active', true)->first();
        if (!$periodeAktif) {
            $periodeAktif = AcademicPeriod::first();
            $this->command->warn('Tidak ada periode aktif, menggunakan periode pertama: ' . ($periodeAktif->id ?? 'null'));
        }

        if (!$periodeAktif) {
            $this->command->error('Tidak ada periode akademik. Jalankan AcademicPeriodSeeder terlebih dahulu.');
            return;
        }

        $mahasiswas = Mahasiswa::all();
        if ($mahasiswas->isEmpty()) {
            $this->command->warn('Tidak ada mahasiswa. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // Skripsi dengan status approved
        $skripsiData = [
            [
                'npm' => '10050022251',
                'judul' => 'Pengaruh Media Sosial Terhadap Kesejahteraan Psikologis Mahasiswa',
                'pembimbing' => 'Dr. Endah Nawangsih, M.Psi., Psikolog',
                'narasumber' => 'Andhita Nurul Khasanah, S.Psi., M.Psi., Psikolog',
                'tanggal_seminar' => '2026-02-19',
                'status' => 'approved',
            ],
            [
                'npm' => '10050019026',
                'judul' => 'Hubungan Work-Life Balance dengan Job Satisfaction pada Karyawan Startup',
                'pembimbing' => 'Lisa Widawati, Dra., M.Si., Psikolog',
                'narasumber' => 'Rizka Hadian, S.Psi., M.Psi., Psikolog',
                'tanggal_seminar' => '2026-03-05',
                'status' => 'approved',
            ],
        ];

        foreach ($skripsiData as $data) {
            $mahasiswa = Mahasiswa::where('npm', $data['npm'])->first();
            if ($mahasiswa) {
                PendaftaranSkripsi::updateOrCreate(
                    [
                        'mahasiswa_id' => $mahasiswa->id,
                        'academic_period_id' => $periodeAktif->id,
                    ],
                    [
                        'judul_skripsi' => $data['judul'],
                        'dosen_pembimbing' => $data['pembimbing'],
                        'narasumber' => $data['narasumber'],
                        'tanggal_seminar' => $data['tanggal_seminar'],
                        'status' => $data['status'],
                    ]
                );
                $this->command->info("Pendaftaran skripsi untuk {$data['npm']} - status: {$data['status']}");
            }
        }
    }
}
