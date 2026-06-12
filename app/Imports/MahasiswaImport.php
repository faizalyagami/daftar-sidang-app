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
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MahasiswaImport implements ToCollection, WithHeadingRow, WithChunkReading
{
    private $importedCount = 0;
    private $updatedCount = 0;
    private $registeredCount = 0;
    private $errors = [];
    private $rowNumber = 0;

    protected $jenisPerwalian;
    protected $academicPeriodId;

    // Cache existing data
    private $existingNpms = [];
    private $existingPendaftaran = [];

    public function chunkSize(): int
    {
        return 200;
    }

    public function __construct($jenisPerwalian, $academicPeriodId)
    {
        $this->jenisPerwalian = $jenisPerwalian;
        $this->academicPeriodId = $academicPeriodId;

        // Pre-load existing data untuk pengecekan cepat
        $this->existingNpms = Mahasiswa::pluck('npm')->toArray();

        // Pre-load pendaftaran yang sudah ada di periode ini (untuk cek duplikat)
        if ($jenisPerwalian == 'skripsi') {
            $existing = PendaftaranSkripsi::where('academic_period_id', $academicPeriodId)
                ->pluck('mahasiswa_id')
                ->toArray();
            foreach ($existing as $id) {
                $mahasiswa = Mahasiswa::find($id);
                if ($mahasiswa) {
                    $this->existingPendaftaran[$mahasiswa->npm] = true;
                }
            }
        } else {
            $existing = PendaftaranMetodologi::where('academic_period_id', $academicPeriodId)
                ->pluck('mahasiswa_id')
                ->toArray();
            foreach ($existing as $id) {
                $mahasiswa = Mahasiswa::find($id);
                if ($mahasiswa) {
                    $this->existingPendaftaran[$mahasiswa->npm] = true;
                }
            }
        }
    }

    public function collection(Collection $rows)
    {
        $batchUsers = [];
        $batchMahasiswas = [];

        foreach ($rows as $row) {
            $this->rowNumber++;

            if (empty($row['npm']) && empty($row['nama_mahasiswa'])) {
                continue;
            }

            if (empty($row['npm'])) {
                $this->errors[] = "Baris {$this->rowNumber}: NPM tidak boleh kosong";
                continue;
            }

            if (empty($row['nama_mahasiswa'])) {
                $this->errors[] = "Baris {$this->rowNumber}: Nama Mahasiswa tidak boleh kosong";
                continue;
            }

            $npm = trim((string)$row['npm']);
            $nama = trim((string)$row['nama_mahasiswa']);
            $dosenWali = isset($row['dosen_wali']) ? trim((string)$row['dosen_wali']) : null;
            $tempatLahir = isset($row['tmpt_lahir']) ? trim((string)$row['tmpt_lahir']) : null;
            $ipk = isset($row['ipk_3_digit']) ? (float)$row['ipk_3_digit'] : (isset($row['ipk']) ? (float)$row['ipk'] : null);

            $tanggalLahir = null;
            if (isset($row['tgl_lahir']) && !empty($row['tgl_lahir'])) {
                $tanggalLahir = $this->formatTanggalLahir(trim((string)$row['tgl_lahir']));
            }

            $isExisting = in_array($npm, $this->existingNpms);

            if ($isExisting) {
                // Update existing mahasiswa
                $this->updateExistingMahasiswa($npm, $nama, $tempatLahir, $tanggalLahir, $ipk, $dosenWali);
                $this->updatedCount++;

                // Cek apakah sudah punya pendaftaran di periode ini
                if (!isset($this->existingPendaftaran[$npm])) {
                    $this->createPendaftaranForNpm($npm);
                    $this->registeredCount++;
                }
            } else {
                // Siapkan data untuk batch insert mahasiswa baru
                $email = $this->generateEmail($nama, $npm);
                $username = $npm;
                $password = Hash::make($npm);
                $now = now();

                $batchUsers[] = [
                    'name' => $nama,
                    'email' => $email,
                    'username' => $username,
                    'password' => $password,
                    'is_default_password' => true,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $batchMahasiswas[] = [
                    'npm' => $npm,
                    'tempat_lahir' => $tempatLahir,
                    'tanggal_lahir' => $tanggalLahir,
                    'ipk' => $ipk,
                    'dosen_wali' => $dosenWali,
                    'no_hp' => '',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                $this->importedCount++;
                $this->registeredCount++; // Akan dibuat pendaftaran setelah batch insert
            }
        }

        // Batch insert untuk mahasiswa baru
        if (!empty($batchUsers)) {
            $this->batchInsertMahasiswa($batchUsers, $batchMahasiswas);
        }
    }

    private function batchInsertMahasiswa($users, $mahasiswas)
    {
        if (empty($users)) return;

        DB::beginTransaction();

        try {
            // Insert users
            User::insert($users);

            // Ambil user yang baru diinsert
            $insertedUsers = User::whereIn('username', array_column($users, 'username'))->get();

            $rolesToInsert = [];
            $mahasiswasWithUserId = [];
            $pendaftaranToInsert = [];

            foreach ($insertedUsers as $user) {
                // Roles
                $rolesToInsert[] = [
                    'user_id' => $user->id,
                    'role' => 'mahasiswa',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Mahasiswa
                $mahasiswaData = collect($mahasiswas)->firstWhere('npm', $user->username);
                if ($mahasiswaData) {
                    $mahasiswasWithUserId[] = [
                        'user_id' => $user->id,
                        'npm' => $mahasiswaData['npm'],
                        'tempat_lahir' => $mahasiswaData['tempat_lahir'],
                        'tanggal_lahir' => $mahasiswaData['tanggal_lahir'],
                        'ipk' => $mahasiswaData['ipk'],
                        'dosen_wali' => $mahasiswaData['dosen_wali'],
                        'no_hp' => $mahasiswaData['no_hp'],
                        'created_at' => $mahasiswaData['created_at'],
                        'updated_at' => $mahasiswaData['updated_at'],
                    ];

                    // Siapkan pendaftaran untuk mahasiswa baru
                    $pendaftaranToInsert[] = [
                        'mahasiswa_id' => 0, // placeholder, akan diupdate setelah insert
                        'npm' => $mahasiswaData['npm'],
                        'academic_period_id' => $this->academicPeriodId,
                        'judul_skripsi' => 'Belum diisi',
                        'dosen_pembimbing' => 'Belum ditentukan',
                        'status' => 'belum_daftar',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Batch insert roles
            if (!empty($rolesToInsert)) {
                Role::insert($rolesToInsert);
            }

            // Batch insert mahasiswas
            if (!empty($mahasiswasWithUserId)) {
                Mahasiswa::insert($mahasiswasWithUserId);
            }

            // Insert pendaftaran untuk mahasiswa baru
            if (!empty($pendaftaranToInsert)) {
                // Ambil ID mahasiswa yang baru diinsert
                $newMahasiswas = Mahasiswa::whereIn('npm', array_column($pendaftaranToInsert, 'npm'))->get();

                $finalPendaftaran = [];
                foreach ($pendaftaranToInsert as $p) {
                    $mahasiswa = $newMahasiswas->firstWhere('npm', $p['npm']);
                    if ($mahasiswa) {
                        $finalPendaftaran[] = [
                            'mahasiswa_id' => $mahasiswa->id,
                            'academic_period_id' => $p['academic_period_id'],
                            'judul_skripsi' => $p['judul_skripsi'],
                            'dosen_pembimbing' => $p['dosen_pembimbing'],
                            'status' => $p['status'],
                            'created_at' => $p['created_at'],
                            'updated_at' => $p['updated_at'],
                        ];
                    }
                }

                if (!empty($finalPendaftaran)) {
                    if ($this->jenisPerwalian == 'skripsi') {
                        PendaftaranSkripsi::insert($finalPendaftaran);
                    } else {
                        // Untuk metodologi
                        $metodologiData = [];
                        foreach ($finalPendaftaran as $p) {
                            $mahasiswa = Mahasiswa::find($p['mahasiswa_id']);
                            $metodologiData[] = [
                                'mahasiswa_id' => $p['mahasiswa_id'],
                                'academic_period_id' => $p['academic_period_id'],
                                'email' => $mahasiswa ? $mahasiswa->user->email : '',
                                'judul_penelitian' => 'Belum diisi',
                                'dosen_pembimbing' => 'Belum ditentukan',
                                'kuliah_peminatan' => 'Belum dipilih',
                                'status' => 'belum_daftar',
                                'created_at' => $p['created_at'],
                                'updated_at' => $p['updated_at'],
                            ];
                        }
                        PendaftaranMetodologi::insert($metodologiData);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errors[] = 'Batch insert error: ' . $e->getMessage();
            Log::error('Batch insert error: ' . $e->getMessage());
        }
    }

    private function updateExistingMahasiswa($npm, $nama, $tempatLahir, $tanggalLahir, $ipk, $dosenWali)
    {
        $mahasiswa = Mahasiswa::where('npm', $npm)->first();
        if (!$mahasiswa) return;

        // Update mahasiswa
        $mahasiswa->update([
            'tempat_lahir' => $tempatLahir,
            'tanggal_lahir' => $tanggalLahir,
            'ipk' => $ipk,
            'dosen_wali' => $dosenWali,
        ]);

        // Update user name jika perlu
        if ($mahasiswa->user && $mahasiswa->user->name != $nama) {
            $mahasiswa->user->update(['name' => $nama]);
        }
    }

    private function createPendaftaranForNpm($npm)
    {
        $mahasiswa = Mahasiswa::where('npm', $npm)->first();
        if (!$mahasiswa) return;

        if ($this->jenisPerwalian == 'skripsi') {
            // CEK LAGI SUDAH ADA? (double check)
            $exists = PendaftaranSkripsi::where('mahasiswa_id', $mahasiswa->id)
                ->where('academic_period_id', $this->academicPeriodId)
                ->exists();

            if (!$exists) {
                PendaftaranSkripsi::create([
                    'mahasiswa_id' => $mahasiswa->id,
                    'academic_period_id' => $this->academicPeriodId,
                    'judul_skripsi' => 'Belum diisi',
                    'dosen_pembimbing' => 'Belum ditentukan',
                    'status' => 'belum_daftar',
                ]);
            }
        } else {
            $exists = PendaftaranMetodologi::where('mahasiswa_id', $mahasiswa->id)
                ->where('academic_period_id', $this->academicPeriodId)
                ->exists();

            if (!$exists) {
                PendaftaranMetodologi::create([
                    'mahasiswa_id' => $mahasiswa->id,
                    'academic_period_id' => $this->academicPeriodId,
                    'email' => $mahasiswa->user->email ?? '',
                    'judul_penelitian' => 'Belum diisi',
                    'dosen_pembimbing' => 'Belum ditentukan',
                    'kuliah_peminatan' => 'Belum dipilih',
                    'status' => 'belum_daftar',
                ]);
            }
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
            Log::error('Error parsing date: ' . $tglLahir);
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

    public function getJenisPerwalian()
    {
        return $this->jenisPerwalian;
    }

    public function getAcademicPeriodId()
    {
        return $this->academicPeriodId;
    }
}
