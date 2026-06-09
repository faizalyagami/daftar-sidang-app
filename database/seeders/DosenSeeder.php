<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Dosen;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        $dosens = [
            ['nik' => 'D.07.0.465', 'name' => 'Ali Mubarak, S.Psi., M.Psi. Psikolog', 'email' => 'ali.mubarak@unisba.ac.id'],
            ['nik' => 'D.15.0.656', 'name' => 'Andhita Nurul Khasanah, S.Psi., M.Psi. Psikolog', 'email' => 'andhita.nurul@unisba.ac.id'],
            ['nik' => 'D.12.0.557', 'name' => 'Anna Rozana, S.Psi., M.Psi. Psikolog', 'email' => 'anna.rozana@unisba.ac.id'],
            ['nik' => 'D.15.0.640', 'name' => 'Ayu Tuty Utami, S.Psi., M.Psi. Psikolog', 'email' => 'ayu.tuty@unisba.ac.id'],
            ['nik' => 'D.13.0.595', 'name' => 'Dinda Dwarawati, S.Psi., M.Psi. Psikolog', 'email' => 'dinda.dwarawati@unisba.ac.id'],
            ['nik' => 'D.06.0.427', 'name' => 'Dr. Dewi Rosiana, M.Psi. Psikolog', 'email' => 'dewi.rosiana@unisba.ac.id'],
            ['nik' => 'D.94.0.198', 'name' => 'Dr. Dewi Sartika, M.Si. Psikolog', 'email' => 'dewi.sartika@unisba.ac.id'],
            ['nik' => 'D.97.0.265', 'name' => 'Dr. Endah Nawangsih, Dra., M.Psi. Psikolog', 'email' => 'endah.nawangsih@unisba.ac.id'],
            ['nik' => 'D.97.0.266', 'name' => 'Dr. Eneng Nurlaili Wangi, M.Psi. Psikolog', 'email' => 'eneng.nurlaili@unisba.ac.id'],
            ['nik' => 'D.92.0.157', 'name' => 'Dr. Hedi Wahyudi, M.Psi. Psikolog', 'email' => 'hedi.wahyudi@unisba.ac.id'],
            ['nik' => 'D.94.0.199', 'name' => 'Dr. Ihsana Sabriani Borualogo, M.Si. Psikolog', 'email' => 'ihsana.borualogo@unisba.ac.id'],
            ['nik' => 'D.04.0.388', 'name' => 'Dr. Lilim Halimah, BHSc., MHSPY.', 'email' => 'lilim.halimah@unisba.ac.id'],
            ['nik' => 'D.93.0.176', 'name' => 'Dr. Muhammad Ilmi Hatta, M.Psi. Psikolog', 'email' => 'ilmi.hatta@unisba.ac.id'],
            ['nik' => 'D.07.0.464', 'name' => 'Dr. Oki Mardiawan, M.Psi., Psikolog', 'email' => 'oki.mardiawan@unisba.ac.id'],
            ['nik' => 'D.90.0.112', 'name' => 'Dr. Siti Qodariah, M.Psi. Psikolog', 'email' => 'siti.qodariah@unisba.ac.id'],
            ['nik' => 'D.99.0.302', 'name' => 'Dr. Suci Nugraha, M.Psi. Psikolog', 'email' => 'suci.nugraha@unisba.ac.id'],
            ['nik' => 'D.07.0.463', 'name' => 'Dr. Yunita Sari, M.Psi., Psikolog', 'email' => 'yunita.sari@unisba.ac.id'],
            ['nik' => 'D.88.0.071', 'name' => 'Eni Nuraeni Nugrahawati, Dra., M.Pd.', 'email' => 'eni.nugrahawati@unisba.ac.id'],
            ['nik' => 'D.08.0.472', 'name' => 'Fanni Putri Diantina, S.Psi., M.Psi. Psikolog', 'email' => 'fanni.diantina@unisba.ac.id'],
            ['nik' => 'D.08.0.470', 'name' => 'Farida Coralia, S.Psi., M.Psi. Psikolog', 'email' => 'farida.coralia@unisba.ac.id'],
            ['nik' => 'D.08.0.471', 'name' => 'Indri Utami Sumaryanti, S.Psi., M.Psi. Psikolog', 'email' => 'indri.sumaryanti@unisba.ac.id'],
            ['nik' => 'D.89.0.090', 'name' => 'Lisa Widawati, Dra., M.Si. Psikolog', 'email' => 'lisa.widawati@unisba.ac.id'],
            ['nik' => 'D.99.0.303', 'name' => 'Milda Yanuvianti, S.Psi., M.A.', 'email' => 'milda.yanuvianti@unisba.ac.id'],
            ['nik' => 'D.23.3.979', 'name' => 'Prof. Dr. Saifuddin Azwar, M.A', 'email' => 'saifuddin.azwar@unisba.ac.id'],
            ['nik' => 'D.15.0.673', 'name' => 'Rizka Hadian Permana, S.Psi., M.Psi. Psikolog', 'email' => 'rizka.permana@unisba.ac.id'],
            ['nik' => 'D.13.0.594', 'name' => 'Stephani Raihana Hamdan, S.Psi., M.Psi. Psikolog', 'email' => 'stephani.hamdan@unisba.ac.id'],
            ['nik' => 'D.00.0.329', 'name' => 'Suhana, S.Psi., M.Psi. Psikolog', 'email' => 'suhana@unisba.ac.id'],
            ['nik' => 'D.92.0.156', 'name' => 'Sulisworo Kusdiyati, Dra., M.Si. Psikolog', 'email' => 'sulisworo@unisba.ac.id'],
            ['nik' => 'D.97.0.264', 'name' => 'Susandari, S.Psi., M.Psi. Psikolog', 'email' => 'susandari@unisba.ac.id'],
            ['nik' => 'D.00.0.330', 'name' => 'Temi Damayanti Djamhoer, S.Psi., M.A.', 'email' => 'temi.damayanti@unisba.ac.id'],
            ['nik' => 'D.23.0.011', 'name' => 'Tia Inayatillah, S.Psi., M.Psi. Psikolog', 'email' => 'tia.inayatillah@unisba.ac.id'],
            ['nik' => 'D.19.0.788', 'name' => 'Vici Sofianna Putera, S.Psi., M.Psi.T.', 'email' => 'vici.putera@unisba.ac.id'],
        ];

        foreach ($dosens as $data) {
            // Buat user untuk dosen
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'username' => $data['nik'],
                    'password' => Hash::make('password123'),
                    'is_default_password' => true
                ]
            );

            // Assign role dosen
            Role::updateOrCreate(
                ['user_id' => $user->id],
                ['role' => 'dosen']
            );

            // Buat data dosen
            Dosen::updateOrCreate(
                ['nik' => $data['nik']],
                [
                    'user_id' => $user->id,
                    'name' => $data['name'],
                    'is_active' => true
                ]
            );
        }
    }
}
