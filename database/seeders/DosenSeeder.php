<?php

namespace Database\Seeders;

use App\Models\Dosen;
use Illuminate\Database\Seeder;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        $dosens = [
            ['nik' => 'D.07.0.465', 'name' => 'Ali Mubarak, S.Psi., M.Psi. Psikolog'],
            ['nik' => 'D.15.0.656', 'name' => 'Andhita Nurul Khasanah, S.Psi., M.Psi. Psikolog'],
            ['nik' => 'D.12.0.557', 'name' => 'Anna Rozana, S.Psi., M.Psi. Psikolog'],
            ['nik' => 'D.15.0.640', 'name' => 'Ayu Tuty Utami, S.Psi., M.Psi. Psikolog'],
            ['nik' => 'D.13.0.595', 'name' => 'Dinda Dwarawati, S.Psi., M.Psi. Psikolog'],
            ['nik' => 'D.06.0.427', 'name' => 'Dr. Dewi Rosiana, M.Psi. Psikolog'],
            ['nik' => 'D.94.0.198', 'name' => 'Dr. Dewi Sartika, M.Si. Psikolog'],
            ['nik' => 'D.97.0.265', 'name' => 'Dr. Endah Nawangsih, Dra., M.Psi. Psikolog'],
            ['nik' => 'D.97.0.266', 'name' => 'Dr. Eneng Nurlaili Wangi, M.Psi. Psikolog'],
            ['nik' => 'D.92.0.157', 'name' => 'Dr. Hedi Wahyudi, M.Psi. Psikolog'],
            ['nik' => 'D.94.0.199', 'name' => 'Dr. Ihsana Sabriani Borualogo, M.Si. Psikolog'],
            ['nik' => 'D.04.0.388', 'name' => 'Dr. Lilim Halimah, BHSc., MHSPY.'],
            ['nik' => 'D.93.0.176', 'name' => 'Dr. Muhammad Ilmi Hatta, M.Psi. Psikolog'],
            ['nik' => 'D.07.0.464', 'name' => 'Dr. Oki Mardiawan, M.Psi., Psikolog'],
            ['nik' => 'D.90.0.112', 'name' => 'Dr. Siti Qodariah, M.Psi. Psikolog'],
            ['nik' => 'D.99.0.302', 'name' => 'Dr. Suci Nugraha, M.Psi. Psikolog'],
            ['nik' => 'D.07.0.463', 'name' => 'Dr. Yunita Sari, M.Psi., Psikolog'],
            ['nik' => 'D.88.0.071', 'name' => 'Eni Nuraeni Nugrahawati, Dra., M.Pd.'],
            ['nik' => 'D.08.0.472', 'name' => 'Fanni Putri Diantina, S.Psi., M.Psi. Psikolog'],
            ['nik' => 'D.08.0.470', 'name' => 'Farida Coralia, S.Psi., M.Psi. Psikolog'],
            ['nik' => 'D.08.0.471', 'name' => 'Indri Utami Sumaryanti, S.Psi., M.Psi. Psikolog'],
            ['nik' => 'D.89.0.090', 'name' => 'Lisa Widawati, Dra., M.Si. Psikolog'],
            ['nik' => 'D.99.0.303', 'name' => 'Milda Yanuvianti, S.Psi., M.A.'],
            ['nik' => 'D.23.3.979', 'name' => 'Prof. Dr. Saifuddin Azwar, M.A'],
            ['nik' => 'D.15.0.673', 'name' => 'Rizka Hadian Permana, S.Psi., M.Psi. Psikolog'],
            ['nik' => 'D.13.0.594', 'name' => 'Stephani Raihana Hamdan, S.Psi., M.Psi. Psikolog'],
            ['nik' => 'D.00.0.329', 'name' => 'Suhana, S.Psi., M.Psi. Psikolog'],
            ['nik' => 'D.92.0.156', 'name' => 'Sulisworo Kusdiyati, Dra., M.Si. Psikolog'],
            ['nik' => 'D.97.0.264', 'name' => 'Susandari, S.Psi., M.Psi. Psikolog'],
            ['nik' => 'D.00.0.330', 'name' => 'Temi Damayanti Djamhoer, S.Psi., M.A.'],
            ['nik' => 'D.23.0.011', 'name' => 'Tia Inayatillah, S.Psi., M.Psi. Psikolog'],
            ['nik' => 'D.19.0.788', 'name' => 'Vici Sofianna Putera, S.Psi., M.Psi.T.'],
        ];

        foreach ($dosens as $dosen) {
            Dosen::updateOrCreate(
                ['nik' => $dosen['nik']],
                [
                    'name' => $dosen['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}