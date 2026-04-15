<?php

namespace App\Exports;

use App\Models\JadwalSkripsi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JadwalSkripsiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $jadwal;

    public function __construct($jadwal = null)
    {
        $this->jadwal = $jadwal ?? JadwalSkripsi::with('pendaftaran.mahasiswa.user')->get();
    }

    public function collection()
    {
        return $this->jadwal;
    }

    public function headings(): array
    {
        return [
            'Hari',
            'Tanggal',
            'Waktu Mulai',
            'Waktu Selesai',
            'Ruang',
            'NPM',
            'Nama Mahasiswa',
            'Judul Skripsi',
            'Dosen Pembimbing',
            'Dosen Penguji 1',
            'Dosen Penguji 2',
            'Dosen Penguji 3',
            'Status',
        ];
    }

    public function map($jadwal): array
    {
        $pendaftaran = $jadwal->pendaftaran;
        $mahasiswa = $pendaftaran->mahasiswa;
        return [
            \Carbon\Carbon::parse($jadwal->tanggal)->translatedFormat('l'),
            $jadwal->tanggal->format('d/m/Y'),
            $jadwal->waktu_mulai->format('H:i'),
            $jadwal->waktu_selesai->format('H:i'),
            $jadwal->ruang ?? '-',
            $mahasiswa->npm,
            $mahasiswa->user->name,
            $pendaftaran->judul_skripsi,
            $pendaftaran->dosen_pembimbing,
            $jadwal->dosen_penguji_1 ?? '-',
            $jadwal->dosen_penguji_2 ?? '-',
            $jadwal->dosen_penguji_3 ?? '-',
            $jadwal->status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}