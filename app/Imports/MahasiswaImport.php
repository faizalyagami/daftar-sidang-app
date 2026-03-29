<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use App\Models\Mahasiswa;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MahasiswaImport implements ToCollection, WithHeadingRow
{
    private $importedCount = 0;
    private $updatedCount = 0;
    private $errors = [];
    private $rowNumber = 0;

    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        
        try {
            foreach ($rows as $row) {
                $this->rowNumber++;
                
                // Skip jika row kosong
                if (empty($row['npm']) && empty($row['nama_mahasiswa'])) {
                    continue;
                }
                
                // Validasi NPM
                if (empty($row['npm'])) {
                    $this->errors[] = "Baris {$this->rowNumber}: NPM tidak boleh kosong";
                    continue;
                }
                
                // Validasi Nama Mahasiswa
                if (empty($row['nama_mahasiswa'])) {
                    $this->errors[] = "Baris {$this->rowNumber}: Nama Mahasiswa tidak boleh kosong";
                    continue;
                }
                
                // Bersihkan data
                $npm = trim((string)$row['npm']);
                $nama = trim((string)$row['nama_mahasiswa']);
                $dosenWali = isset($row['dosen_wali']) ? trim((string)$row['dosen_wali']) : null;
                $tempatLahir = isset($row['tmpt_lahir']) ? trim((string)$row['tmpt_lahir']) : null;
                $ipk = isset($row['ipk_3_digit']) ? (float)$row['ipk_3_digit'] : (isset($row['ipk']) ? (float)$row['ipk'] : null);
                
                // Format tanggal lahir
                $tanggalLahir = null;
                if (isset($row['tgl_lahir']) && !empty($row['tgl_lahir'])) {
                    $tanggalLahir = $this->formatTanggalLahir(trim((string)$row['tgl_lahir']));
                }
                
                // Cek apakah NPM sudah ada
                $existingMahasiswa = Mahasiswa::where('npm', $npm)->first();
                
                if ($existingMahasiswa) {
                    // Update data existing
                    $this->updateMahasiswa($existingMahasiswa, $nama, $tempatLahir, $tanggalLahir, $ipk, $dosenWali);
                    $this->updatedCount++;
                } else {
                    // Create new user and mahasiswa
                    $this->createMahasiswa($npm, $nama, $tempatLahir, $tanggalLahir, $ipk, $dosenWali);
                    $this->importedCount++;
                }
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors[] = 'Error: ' . $e->getMessage();
            Log::error('Import Error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    private function createMahasiswa($npm, $nama, $tempatLahir, $tanggalLahir, $ipk, $dosenWali)
    {
        // Buat user baru
        $user = User::create([
            'name' => $nama,
            'email' => $this->generateEmail($nama, $npm),
            'username' => $npm,
            'password' => Hash::make('password123'),
        ]);
        
        // Assign role mahasiswa
        Role::create([
            'user_id' => $user->id,
            'role' => 'mahasiswa'
        ]);
        
        // Buat data mahasiswa
        Mahasiswa::create([
            'user_id' => $user->id,
            'npm' => $npm,
            'tempat_lahir' => $tempatLahir,
            'tanggal_lahir' => $tanggalLahir,
            'ipk' => $ipk,
            'dosen_wali' => $dosenWali,
            'no_hp' => '',
        ]);
    }
    
    private function updateMahasiswa($mahasiswa, $nama, $tempatLahir, $tanggalLahir, $ipk, $dosenWali)
    {
        // Update data mahasiswa
        $mahasiswa->update([
            'tempat_lahir' => $tempatLahir,
            'tanggal_lahir' => $tanggalLahir,
            'ipk' => $ipk,
            'dosen_wali' => $dosenWali,
        ]);
        
        // Update user name jika berubah
        if ($mahasiswa->user->name != $nama) {
            $mahasiswa->user->update(['name' => $nama]);
        }
        
        // Update username jika perlu
        if ($mahasiswa->user->username != $mahasiswa->npm) {
            $mahasiswa->user->update(['username' => $mahasiswa->npm]);
        }
    }
    
    private function generateEmail($name, $npm)
    {
        // Generate email dari nama dan npm
        $nameSlug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '.', $name));
        $nameSlug = preg_replace('/\.+/', '.', $nameSlug);
        $nameSlug = trim($nameSlug, '.');
        
        $email = $nameSlug . '@student.unisba.ac.id';
        
        // Cek apakah email sudah ada
        $counter = 1;
        $originalEmail = $email;
        while (User::where('email', $email)->exists()) {
            $email = str_replace('@student.unisba.ac.id', $counter . '@student.unisba.ac.id', $originalEmail);
            $counter++;
        }
        
        return $email;
    }
    
    private function formatTanggalLahir($tglLahir)
    {
        if (empty($tglLahir)) {
            return null;
        }
        
        try {
            // Coba format dd-mm-yy atau dd-mm-yyyy
            if (strpos($tglLahir, '-') !== false) {
                $parts = explode('-', $tglLahir);
                if (count($parts) == 3) {
                    $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                    $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                    $year = $parts[2];
                    
                    // Jika tahun 2 digit (00-99), konversi ke 4 digit
                    if (strlen($year) == 2) {
                        $year = 2000 + (int)$year;
                    }
                    
                    $dateString = "{$year}-{$month}-{$day}";
                    if (strtotime($dateString)) {
                        return date('Y-m-d', strtotime($dateString));
                    }
                }
            }
            
            // Coba format lain
            if (strtotime($tglLahir)) {
                return date('Y-m-d', strtotime($tglLahir));
            }
            
            return null;
        } catch (\Exception $e) {
            Log::error('Error parsing date: ' . $tglLahir . ' - ' . $e->getMessage());
            return null;
        }
    }
    
    public function getImportedCount()
    {
        return $this->importedCount;
    }
    
    public function getUpdatedCount()
    {
        return $this->updatedCount;
    }
    
    public function getErrors()
    {
        return $this->errors;
    }
}