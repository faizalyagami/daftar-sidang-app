<?php

namespace Database\Seeders;

use App\Models\Mahasiswa;
use App\Models\PendaftaranSkripsi;
use App\Models\PendaftaranMetodologi;
use App\Models\AcademicPeriod;
use Illuminate\Database\Seeder;

class PendaftaranSeeder extends Seeder
{
    public function run(): void
    {
        $periodeAktif = AcademicPeriod::where('is_active', true)->first();
        if (!$periodeAktif) {
            $this->command->warn('Tidak ada periode aktif. Jalankan AcademicPeriodSeeder terlebih dahulu.');
            return;
        }

        $mahasiswas = Mahasiswa::all();
        if ($mahasiswas->isEmpty()) {
            $this->command->warn('Tidak ada mahasiswa. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // Skripsi
        $skripsiData = [
            [
                'npm' => '10050022251',
                'judul' => 'Pengaruh Media Sosial Terhadap Kesejahteraan Psikologis Mahasiswa',
                'pembimbing' => 'Dr. Endah Nawangsih, M.Psi., Psikolog',
                'narasumber' => 'Andhita Nurul Khasanah, S.Psi., M.Psi., Psikolog',
                'tanggal_seminar' => '2026-02-19',
            ],
            [
                'npm' => '10050019026',
                'judul' => 'Hubungan Work-Life Balance dengan Job Satisfaction pada Karyawan Startup',
                'pembimbing' => 'Lisa Widawati, Dra., M.Si., Psikolog',
                'narasumber' => 'Rizka Hadian, S.Psi., M.Psi., Psikolog',
                'tanggal_seminar' => '2026-03-05',
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
                        'status' => 'approved',
                    ]
                );
            }
        }

        // Metodologi
        $metodologiData = [
            [
                'npm' => '10050019027',
                'judul' => 'Metodologi Penelitian: Analisis Korelasi antara Stres Kerja dan Burnout',
                'pembimbing' => 'Dr. Ihsana Sabriani Boruaglo, M.Si., Psikolog',
                'email' => 'ariq.zahid@student.unisba.ac.id',
                'kuliah_peminatan' => 'Psikologi Industri dan Organisasi',
            ],
        ];

        foreach ($metodologiData as $data) {
            $mahasiswa = Mahasiswa::where('npm', $data['npm'])->first();
            if ($mahasiswa) {
                PendaftaranMetodologi::updateOrCreate(
                    [
                        'mahasiswa_id' => $mahasiswa->id,
                        'academic_period_id' => $periodeAktif->id,
                    ],
                    [
                        'email' => $data['email'],
                        'judul_penelitian' => $data['judul'],
                        'dosen_pembimbing' => $data['pembimbing'],
                        'kuliah_peminatan' => $data['kuliah_peminatan'],
                        'status' => 'approved',
                    ]
                );
            }
        }
    }
}