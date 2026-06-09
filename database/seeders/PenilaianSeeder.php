<?php

namespace Database\Seeders;

use App\Models\JadwalSkripsi;
use App\Models\PenilaianSkripsi;
use App\Models\RekapitulasiNilaiSkripsi;
use App\Models\Dosen;
use Illuminate\Database\Seeder;

class PenilaianSeeder extends Seeder
{
    public function run(): void
    {
        $jadwalList = JadwalSkripsi::all();

        foreach ($jadwalList as $jadwal) {
            // Ambil ID dosen dari jadwal
            $daftarPengujiId = array_filter([
                $jadwal->dosen_penguji_1_id,
                $jadwal->dosen_penguji_2_id,
                $jadwal->dosen_penguji_3_id
            ]);

            $bobotPenguji = [40, 30, 30];
            $index = 0;

            foreach ($daftarPengujiId as $pengujiId) {
                $dosen = Dosen::find($pengujiId);
                if (!$dosen) continue;

                // Generate nilai random antara 65-85
                $nilaiFenomena = rand(65, 85);
                $nilaiVariabel = rand(65, 85);
                $nilaiTeori = rand(65, 85);
                $nilaiAlat = rand(65, 85);
                $nilaiAnalisis = rand(65, 85);
                $nilaiSimpulan = rand(65, 85);
                $nilaiPresentasi = rand(65, 85);

                // Hitung total dengan bobot
                $total = ($nilaiFenomena * 2) + ($nilaiVariabel * 2) + ($nilaiTeori * 2) +
                    ($nilaiAlat * 1) + ($nilaiAnalisis * 1) + ($nilaiSimpulan * 1) + ($nilaiPresentasi * 1);
                $nilaiAkhirPenguji = $total / 10;

                PenilaianSkripsi::updateOrCreate(
                    [
                        'jadwal_skripsi_id' => $jadwal->id,
                        'dosen_id' => $pengujiId
                    ],
                    [
                        'nama_dosen_penguji' => $dosen->name,
                        'bobot' => $bobotPenguji[$index],
                        'nilai_fenomena' => $nilaiFenomena,
                        'nilai_variabel' => $nilaiVariabel,
                        'nilai_teori_metode' => $nilaiTeori,
                        'nilai_alat_ukur' => $nilaiAlat,
                        'nilai_analisis' => $nilaiAnalisis,
                        'nilai_simpulan_saran' => $nilaiSimpulan,
                        'nilai_presentasi' => $nilaiPresentasi,
                        'jumlah' => $total,
                        'nilai_akhir' => round($nilaiAkhirPenguji, 2),
                        'is_completed' => true,
                        'catatan' => 'Bagus, pertahankan!'
                    ]
                );
                $index++;
            }

            // Hitung rekapitulasi nilai akhir
            $penilaianList = PenilaianSkripsi::where('jadwal_skripsi_id', $jadwal->id)->get();
            $totalNilai = 0;

            foreach ($penilaianList as $p) {
                $totalNilai += ($p->nilai_akhir * $p->bobot / 100);
            }

            RekapitulasiNilaiSkripsi::updateOrCreate(
                ['jadwal_skripsi_id' => $jadwal->id],
                [
                    'nilai_akhir' => round($totalNilai, 2),
                    'huruf_mutu' => $this->hitungHurufMutu($totalNilai),
                    'catatan_kumulatif' => 'Selamat! Anda telah menyelesaikan sidang skripsi.',
                    'is_published' => true
                ]
            );
        }
    }

    private function hitungHurufMutu($nilai)
    {
        if ($nilai <= 44.00) return 'E';
        if ($nilai <= 55.49) return 'D';
        if ($nilai <= 59.49) return 'C';
        if ($nilai <= 63.49) return 'C+';
        if ($nilai <= 67.49) return 'B-';
        if ($nilai <= 71.49) return 'B';
        if ($nilai <= 75.49) return 'B+';
        if ($nilai <= 79.50) return 'A-';
        return 'A';
    }
}
