<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicPeriod;

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
                'description' => 'Periode Ganjil Tahun Akademik 2024/2025'
            ],
            [
                'semester' => 'Genap',
                'tahun_akademik' => '2024/2025',
                'start_date' => '2025-01-01',
                'end_date' => '2025-06-30',
                'is_active' => false,
                'description' => 'Periode Genap Tahun Akademik 2024/2025'
            ],
            [
                'semester' => 'Ganjil',
                'tahun_akademik' => '2025/2026',
                'start_date' => '2025-08-01',
                'end_date' => '2025-12-31',
                'is_active' => false,
                'description' => 'Periode Ganjil Tahun Akademik 2025/2026'
            ],
            [
                'semester' => 'Genap',
                'tahun_akademik' => '2025/2026',
                'start_date' => '2026-02-08',
                'end_date' => '2026-06-19',
                'is_active' => true,
                'description' => 'Periode Genap Tahun Akademik 2025/2026'
            ],
        ];

        foreach ($periods as $period) {
            AcademicPeriod::create($period);
        }
    }
}
