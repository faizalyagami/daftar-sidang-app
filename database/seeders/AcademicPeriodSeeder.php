<?php

namespace Database\Seeders;

use App\Models\AcademicPeriod;
use Illuminate\Database\Seeder;

class AcademicPeriodSeeder extends Seeder
{
    public function run(): void
    {
        $periods = [
            [
                'semester' => 'Ganjil',
                'tahun_akademik' => '2024/2025',
                'start_date' => '2024-08-01',
                'end_date' => '2024-12-31',
                'is_active' => false,
                'skripsi_open' => true,
                'metodologi_open' => true,
                'description' => 'Periode Ganjil 2024/2025',
            ],
            [
                'semester' => 'Genap',
                'tahun_akademik' => '2024/2025',
                'start_date' => '2025-01-01',
                'end_date' => '2025-06-30',
                'is_active' => false,
                'skripsi_open' => true,
                'metodologi_open' => true,
                'description' => 'Periode Genap 2024/2025',
            ],
            [
                'semester' => 'Ganjil',
                'tahun_akademik' => '2025/2026',
                'start_date' => '2025-08-01',
                'end_date' => '2025-12-31',
                'is_active' => false,
                'skripsi_open' => true,
                'metodologi_open' => true,
                'description' => 'Periode Ganjil 2025/2026',
            ],
            [
                'semester' => 'Genap',
                'tahun_akademik' => '2025/2026',
                'start_date' => '2026-02-01',
                'end_date' => '2026-06-30',
                'is_active' => true,
                'skripsi_open' => true,
                'metodologi_open' => true,
                'description' => 'Periode Genap 2025/2026',
            ],
            [
                'semester' => 'Genap',
                'tahun_akademik' => '2026/2027',
                'start_date' => '2027-02-01',
                'end_date' => '2027-06-30',
                'is_active' => false,
                'skripsi_open' => true,
                'metodologi_open' => true,
                'description' => 'Periode Genap 2026/2027',
            ],
        ];

        foreach ($periods as $period) {
            AcademicPeriod::updateOrCreate(
                ['semester' => $period['semester'], 'tahun_akademik' => $period['tahun_akademik']],
                $period
            );
        }
    }
}