<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use App\Models\Mahasiswa;
use App\Models\PendaftaranSkripsi;
use App\Models\PendaftaranMetodologi;
use App\Models\AcademicPeriod;
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
    private $registeredCount = 0; // jumlah pendaftaran yang dibuat
    private $errors = [];
    private $rowNumber = 0;

    protected $jenisPerwalian; // 'skripsi' atau 'metodologi'
    protected $academicPeriodId;

    public function __construct($jenisPerwalian, $academicPeriodId)
    {
        $this->jenisPerwalian = $jenisPerwalian;
        $this->academicPeriodId = $academicPeriodId;
    }

    public function collection(Collection $rows)
    {
        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $this->rowNumber++;

                // Lewati baris kosong
                if (empty($row['npm']) && empty($row['nama_mahasiswa'])) {
                    continue;
                }

                // Validasi
                if (empty($row['npm'])) {
                    $this->errors[] = "Baris {$this->rowNumber}: NPM tidak boleh kosong";
                    continue;
                }

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

                $tanggalLahir = null;
                if (isset($row['tgl_lahir']) && !empty($row['tgl_lahir'])) {
                    $tanggalLahir = $this->formatTanggalLahir(trim((string)$row['tgl_lahir']));
                }

                // Cek apakah mahasiswa sudah ada
                $existingMahasiswa = Mahasiswa::where('npm', $npm)->first();

                if ($existingMahasiswa) {
                    // Update data mahasiswa
                    $this->updateMahasiswa($existingMahasiswa, $nama, $tempatLahir, $tanggalLahir, $ipk, $dosenWali);
                    $this->updatedCount++;
                    $mahasiswaId = $existingMahasiswa->id;
                } else {
                    // Buat user dan mahasiswa baru
                    $user = $this->createUser($nama, $npm);
                    $mahasiswa = $this->createMahasiswa($user->id, $npm, $tempatLahir, $tanggalLahir, $ipk, $dosenWali);
                    $mahasiswaId = $mahasiswa->id;
                    $this->importedCount++;
                }

                // Buat pendaftaran berdasarkan jenis perwalian
                $this->createPendaftaran($mahasiswaId);

                $this->registeredCount++;
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors[] = 'Error: ' . $e->getMessage();
            Log::error('Import Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createUser($nama, $npm)
    {
        $user = User::create([
            'name' => $nama,
            'email' => $this->generateEmail($nama, $npm),
            'username' => $npm,
            'password' => Hash::make($npm), // default password = NPM
            'is_default_password' => true,
        ]);

        Role::create([
            'user_id' => $user->id,
            'role' => 'mahasiswa'
        ]);

        return $user;
    }

    private function createMahasiswa($userId, $npm, $tempatLahir, $tanggalLahir, $ipk, $dosenWali)
    {
        return Mahasiswa::create([
            'user_id' => $userId,
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
        $mahasiswa->update([
            'tempat_lahir' => $tempatLahir,
            'tanggal_lahir' => $tanggalLahir,
            'ipk' => $ipk,
            'dosen_wali' => $dosenWali,
        ]);

        if ($mahasiswa->user->name != $nama) {
            $mahasiswa->user->update(['name' => $nama]);
        }

        if ($mahasiswa->user->username != $mahasiswa->npm) {
            $mahasiswa->user->update(['username' => $mahasiswa->npm]);
        }
    }

    private function createPendaftaran($mahasiswaId)
    {
        $academicPeriod = AcademicPeriod::find($this->academicPeriodId);
        if (!$academicPeriod) {
            $this->errors[] = "Baris {$this->rowNumber}: Periode akademik tidak valid.";
            return;
        }

        if ($this->jenisPerwalian == 'skripsi') {
            PendaftaranSkripsi::create([
                'mahasiswa_id' => $mahasiswaId,
                'academic_period_id' => $academicPeriod->id,
                'judul_skripsi' => 'Belum diisi',
                'dosen_pembimbing' => 'Belum ditentukan',
                'status' => 'pending',
            ]);
        } elseif ($this->jenisPerwalian == 'metodologi') {
            PendaftaranMetodologi::create([
                'mahasiswa_id' => $mahasiswaId,
                'academic_period_id' => $academicPeriod->id,
                'email' => User::find($mahasiswaId)->email ?? '',
                'judul_penelitian' => 'Belum diisi',
                'dosen_pembimbing' => 'Belum ditentukan',
                'kuliah_peminatan' => 'Belum dipilih',
                'status' => 'pending',
            ]);
        }
    }

    private function generateEmail($name, $npm)
    {
        $nameSlug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '.', $name));
        $nameSlug = preg_replace('/\.+/', '.', $nameSlug);
        $nameSlug = trim($nameSlug, '.');
        $email = $nameSlug . '@student.unisba.ac.id';
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
        if (empty($tglLahir)) return null;
        try {
            if (strpos($tglLahir, '-') !== false) {
                $parts = explode('-', $tglLahir);
                if (count($parts) == 3) {
                    $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
                    $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                    $year = $parts[2];
                    if (strlen($year) == 2) {
                        $year = 2000 + (int)$year;
                    }
                    $dateString = "{$year}-{$month}-{$day}";
                    if (strtotime($dateString)) {
                        return date('Y-m-d', strtotime($dateString));
                    }
                }
            }
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

    public function getRegisteredCount()
    {
        return $this->registeredCount;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}