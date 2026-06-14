<?php

namespace App\Http\Controllers\Admin;

use App\Exports\JadwalMetodologiExport;
use App\Exports\JadwalSkripsiExport;
use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\PendaftaranSkripsi;
use App\Models\PendaftaranMetodologi;
use App\Models\JadwalSkripsi;
use App\Models\JadwalMetodologi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;

class JadwalController extends Controller
{
    // Daftar pendaftaran yang statusnya approved (lanjut sidang/ujian)
    public function index()
    {
        $skripsi = PendaftaranSkripsi::with('mahasiswa.user', 'jadwal')
            ->where('status', 'approved')
            ->get();
        $metodologi = PendaftaranMetodologi::with('mahasiswa.user', 'jadwal')
            ->where('status', 'approved')
            ->get();
        return view('admin.jadwal.index', compact('skripsi', 'metodologi'));
    }

    // Form tambah jadwal skripsi
    public function createSkripsi($id)
    {
        $pendaftaran = PendaftaranSkripsi::with('mahasiswa.user')->findOrFail($id);
        $dosens = Dosen::active()->orderBy('name')->get();
        return view('admin.jadwal.create-skripsi', compact('pendaftaran', 'dosens'));
    }

    public function storeSkripsi(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'ruang' => 'nullable|string',
            'dosen_penguji_1_id' => 'nullable|exists:dosens,id',
            'dosen_penguji_2_id' => 'nullable|exists:dosens,id',
            'dosen_penguji_3_id' => 'nullable|exists:dosens,id',
            'keterangan' => 'nullable|string',
        ]);

        $pendaftaran = PendaftaranSkripsi::findOrFail($id);

        if ($pendaftaran->jadwal) {
            return redirect()->back()->with('error', 'Jadwal sudah ada untuk pendaftaran ini.');
        }

        // Ambil nama dosen untuk kolom teks (opsional, untuk kompatibilitas)
        $dosen1 = Dosen::find($request->dosen_penguji_1_id);
        $dosen2 = Dosen::find($request->dosen_penguji_2_id);
        $dosen3 = Dosen::find($request->dosen_penguji_3_id);

        // Jika penguji 1 tidak dipilih, gunakan dosen pembimbing dari pendaftaran
        $penguji1Id = $request->dosen_penguji_1_id;
        if (!$penguji1Id && $pendaftaran->dosen_pembimbing_id) {
            $penguji1Id = $pendaftaran->dosen_pembimbing_id;
            $dosen1 = Dosen::find($penguji1Id);
        }

        JadwalSkripsi::create([
            'pendaftaran_id' => $id,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'ruang' => $request->ruang,

            // Simpan ID (primary)
            'dosen_penguji_1_id' => $penguji1Id,
            'dosen_penguji_2_id' => $request->dosen_penguji_2_id,
            'dosen_penguji_3_id' => $request->dosen_penguji_3_id,

            // Simpan nama (opsional, untuk tampilan jika diperlukan)
            'dosen_penguji_1' => $dosen1 ? $dosen1->name : null,
            'dosen_penguji_2' => $dosen2 ? $dosen2->name : null,
            'dosen_penguji_3' => $dosen3 ? $dosen3->name : null,

            'keterangan' => $request->keterangan,
            'status' => 'terjadwal',
        ]);

        return redirect()
            ->route('admin.jadwal.index')
            ->with('success', 'Jadwal sidang skripsi berhasil ditambahkan.');
    }

    // Form tambah jadwal metodologi (sama)
    public function createMetodologi($id)
    {
        $pendaftaran = PendaftaranMetodologi::with('mahasiswa.user')->findOrFail($id);
        return view('admin.jadwal.create-metodologi', compact('pendaftaran'));
    }

    public function storeMetodologi(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'ruang' => 'nullable|string',
            'dosen_penguji_1' => 'nullable|string',
            'dosen_penguji_2' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $pendaftaran = PendaftaranMetodologi::findOrFail($id);
        if ($pendaftaran->jadwal) {
            return redirect()->back()->with('error', 'Jadwal sudah ada.');
        }

        JadwalMetodologi::create([
            'pendaftaran_id' => $id,
            'tanggal' => $request->tanggal,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
            'ruang' => $request->ruang,
            'dosen_penguji_1' => $request->dosen_penguji_1,
            'dosen_penguji_2' => $request->dosen_penguji_2,
            'keterangan' => $request->keterangan,
            'status' => 'terjadwal'
        ]);

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal ujian metodologi berhasil ditambahkan.');
    }

    // Edit jadwal
    public function editSkripsi($id)
    {
        $jadwal = JadwalSkripsi::with('pendaftaran.mahasiswa.user')->findOrFail($id);
        $dosens = Dosen::active()->orderBy('name')->get();
        return view('admin.jadwal.edit-skripsi', compact('jadwal', 'dosens'));
    }

    public function updateSkripsi(Request $request, $id)
    {
        $jadwal = JadwalSkripsi::findOrFail($id);
        $request->validate([
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'ruang' => 'nullable|string',
            'dosen_penguji_1' => 'nullable|string',
            'dosen_penguji_2' => 'nullable|string',
            'dosen_penguji_3' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $jadwal->update($request->all());

        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal sidang skripsi berhasil diupdate.');
    }

    // Hapus jadwal
    public function destroySkripsi($id)
    {
        $jadwal = JadwalSkripsi::findOrFail($id);
        $jadwal->delete();
        return redirect()->route('admin.jadwal.index')->with('success', 'Jadwal sidang skripsi dihapus.');
    }

    public function exportSkripsi()
    {
        $jadwal = JadwalSkripsi::with('pendaftaran.mahasiswa.user')->get();
        return Excel::download(new JadwalSkripsiExport($jadwal), 'jadwal_skripsi.xlsx');
    }

    public function exportMetodologi()
    {
        $jadwal = JadwalMetodologi::with('pendaftaran.mahasiswa.user')->get();
        return Excel::download(new JadwalMetodologiExport($jadwal), 'jadwal_metodologi.xlsx');
    }
}
