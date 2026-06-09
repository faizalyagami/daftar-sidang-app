<?php

namespace Database\Seeders;

use App\Models\Dosen;
use App\Models\User;
use App\Models\Role;
use App\Models\Mahasiswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ==================== ADMIN ====================
        $admin = User::updateOrCreate(
            ['email' => 'admin@unisba.ac.id'],
            [
                'name' => 'Admin Sistem',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'is_default_password' => false,
                'is_active' => true,
            ]
        );
        Role::updateOrCreate(
            ['user_id' => $admin->id],
            ['role' => 'admin']
        );

        // ==================== REVIEWER ====================
        $reviewer = User::updateOrCreate(
            ['email' => 'reviewer@unisba.ac.id'],
            [
                'name' => 'Reviewer 1',
                'username' => 'reviewer',
                'password' => Hash::make('password123'),
                'is_default_password' => false,
                'is_active' => true,
            ]
        );
        Role::updateOrCreate(
            ['user_id' => $reviewer->id],
            ['role' => 'reviewer']
        );

        // ==================== DOSEN ====================
        $dosenList = [
            [
                'nik' => 'D.13.0.595',
                'name' => 'Dinda Dwarawati, S.Psi., M.Psi., Psikolog',
                'email' => 'dinda.dwarawati@unisba.ac.id',
                'username' => 'D.13.0.595',
            ],
            [
                'nik' => 'D.15.0.656',
                'name' => 'Andhita Nurul Khasanah, S.Psi., M.Psi. Psikolog',
                'email' => 'andhita.nurul@unisba.ac.id',
                'username' => 'D.15.0.656',
            ],
            [
                'nik' => 'D.12.0.557',
                'name' => 'Anna Rozana, S.Psi., M.Psi. Psikolog',
                'email' => 'anna.rozana@unisba.ac.id',
                'username' => 'D.12.0.557',
            ],
        ];

        foreach ($dosenList as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'password' => Hash::make('password123'),
                    'is_default_password' => true,
                    'is_active' => true,
                ]
            );

            Role::updateOrCreate(
                ['user_id' => $user->id],
                ['role' => 'dosen']
            );

            Dosen::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'nik' => $data['nik'],
                    'name' => $data['name'],
                    'is_active' => true,
                ]
            );
        }

        // ==================== MAHASISWA ====================
        $mahasiswaData = [
            [
                'npm' => '10050022251',
                'name' => 'PUTI RACHEL LAUDZA SARNOVA',
                'email' => 'puti.rachel@student.unisba.ac.id',
                'username' => '10050022251',
                'password' => '10050022251',
                'tempat_lahir' => 'Sukabumi',
                'tanggal_lahir' => '2003-11-19',
                'ipk' => 3.25,
                'dosen_wali' => 'VICI SOFIANNA PUTERA, S.PSI., M.PSI.T.',
                'no_hp' => '081234567890',
            ],
            [
                'npm' => '10050019026',
                'name' => 'MOCHAMAD AZMI FAUZAN MUSYAFA',
                'email' => 'azmi.fauzan@student.unisba.ac.id',
                'username' => '10050019026',
                'password' => '10050019026',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2000-12-23',
                'ipk' => 2.57,
                'dosen_wali' => 'RIZKA HADIAN PERMANA., S.PSI, M.PSI.',
                'no_hp' => '081234567891',
            ],
            [
                'npm' => '10050019027',
                'name' => 'M. ARIQ ZAHID BAIHAQI',
                'email' => 'ariq.zahid@student.unisba.ac.id',
                'username' => '10050019027',
                'password' => '10050019027',
                'tempat_lahir' => 'Sukabumi',
                'tanggal_lahir' => '2000-09-17',
                'ipk' => 2.55,
                'dosen_wali' => 'RIZKA HADIAN PERMANA., S.PSI, M.PSI.',
                'no_hp' => '081234567892',
            ],
        ];

        foreach ($mahasiswaData as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'username' => $data['username'],
                    'password' => Hash::make($data['password']),
                    'is_default_password' => true,
                    'is_active' => true,
                ]
            );

            Role::updateOrCreate(
                ['user_id' => $user->id],
                ['role' => 'mahasiswa']
            );

            Mahasiswa::updateOrCreate(
                ['npm' => $data['npm']],
                [
                    'user_id' => $user->id,
                    'npm' => $data['npm'],
                    'tempat_lahir' => $data['tempat_lahir'],
                    'tanggal_lahir' => $data['tanggal_lahir'],
                    'ipk' => $data['ipk'],
                    'dosen_wali' => $data['dosen_wali'],
                    'no_hp' => $data['no_hp'],
                ]
            );
        }
    }
}
