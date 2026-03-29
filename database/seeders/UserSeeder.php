<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@unisba.ac.id'],
            [
                'name' => 'Admin Sistem',
                'username' => 'admin',
                'password' => Hash::make('password123')
            ]
        );
        
        Role::updateOrCreate(
            ['user_id' => $admin->id],
            ['role' => 'admin']
        );
        
        // Create Reviewer
        $reviewer = User::firstOrCreate(
            ['email' => 'reviewer@unisba.ac.id'],
            [
                'name' => 'Reviewer 1',
                'username' => 'reviewer',
                'password' => Hash::make('password123')
            ]
        );
        
        Role::updateOrCreate(
            ['user_id' => $reviewer->id],
            ['role' => 'reviewer']
        );
        
        // Create Mahasiswa 1
        $mahasiswa1 = User::firstOrCreate(
            ['email' => 'ahmad.fauzi@student.unisba.ac.id'],
            [
                'name' => 'Ahmad Fauzi',
                'username' => '1234567890',
                'password' => Hash::make('password123')
            ]
        );
        
        Role::updateOrCreate(
            ['user_id' => $mahasiswa1->id],
            ['role' => 'mahasiswa']
        );
        
        Mahasiswa::updateOrCreate(
            ['npm' => '1234567890'],
            [
                'user_id' => $mahasiswa1->id,
                'npm' => '1234567890',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '2000-01-01',
                'ipk' => 3.75,
                'no_hp' => '081234567890',
                'dosen_wali' => 'Dr. Budi Santoso, M.Si'
            ]
        );
        
        // Create Mahasiswa 2 dengan NPM sebagai username
        $mahasiswa2 = User::firstOrCreate(
            ['email' => 'siti.nurhaliza@student.unisba.ac.id'],
            [
                'name' => 'Siti Nurhaliza',
                'username' => '10050019026',
                'password' => Hash::make('password123')
            ]
        );
        
        Role::updateOrCreate(
            ['user_id' => $mahasiswa2->id],
            ['role' => 'mahasiswa']
        );
        
        Mahasiswa::updateOrCreate(
            ['npm' => '10050019026'],
            [
                'user_id' => $mahasiswa2->id,
                'npm' => '10050019026',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2000-02-15',
                'ipk' => 3.85,
                'no_hp' => '081234567891',
                'dosen_wali' => 'Dr. Dewi Lestari, M.Psi'
            ]
        );
    }
}