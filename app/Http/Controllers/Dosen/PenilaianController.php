<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\JadwalSkripsi;
use App\Models\PenilaianSkripsi;
use App\Models\RekapitulasiNilaiSkripsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenilaianController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            $userRole = $user->role ? $user->role->role : null;

            if ($userRole !== 'dosen') {
                abort(403, 'Unauthorized access.');
            }

            return $next($request);
        });
    }

    public function index()
    {
        $namaDosen = Auth::user()->name;

        $jadwal = JadwalSkripsi::with(['pendaftaran.mahasiswa.user', 'penilaian'])
            ->where('dosen_penguji_1', $namaDosen)
            ->orWhere('dosen_penguji_2', $namaDosen)
            ->orWhere('dosen_penguji_3', $namaDosen)
            ->get();

        return view('dosen.penilaian.index', compact('jadwal'));
    }

    public function create($id)
    {
        $jadwal = JadwalSkripsi::with('pendaftaran.mahasiswa.user')->findOrFail($id);

        $namaDosen = Auth::user()->dosen->name ?? Auth::user()->name;

        $isPenguji = in_array($namaDosen, [
            $jadwal->dosen_penguji_1,
            $jadwal->dosen_penguji_2,
            $jadwal->dosen_penguji_3
        ]);

        if (!$isPenguji) {
            abort(403);
        }

        $penilaian = PenilaianSkripsi::where('jadwal_skripsi_id', $id)
            ->where('nama_dosen_penguji', $namaDosen)
            ->first();

        if (!$penilaian) {
            $penilaian = PenilaianSkripsi::create([
                'jadwal_skripsi_id' => $id,
                'nama_dosen_penguji' => $namaDosen,
                'bobot' => $this->getBobotPenguji($jadwal, $namaDosen)
            ]);
        }

        // Jika sudah selesai, view menjadi readonly / hanya lihat
        $isReadOnly = $penilaian->is_completed;

        return view('dosen.penilaian.form', compact('jadwal', 'penilaian', 'isReadOnly'));
    }

    public function store(Request $request, $id)
    {
        $penilaian = PenilaianSkripsi::findOrFail($id);

        $request->validate([
            'nilai_fenomena' => 'required|numeric|min:0|max:100',
            'nilai_variabel' => 'required|numeric|min:0|max:100',
            'nilai_teori_metode' => 'required|numeric|min:0|max:100',
            'nilai_alat_ukur' => 'required|numeric|min:0|max:100',
            'nilai_analisis' => 'required|numeric|min:0|max:100',
            'nilai_simpulan_saran' => 'required|numeric|min:0|max:100',
            'nilai_presentasi' => 'required|numeric|min:0|max:100',
            'catatan' => 'nullable|string'
        ]);

        $penilaian->update([
            'nilai_fenomena' => $request->nilai_fenomena,
            'nilai_variabel' => $request->nilai_variabel,
            'nilai_teori_metode' => $request->nilai_teori_metode,
            'nilai_alat_ukur' => $request->nilai_alat_ukur,
            'nilai_analisis' => $request->nilai_analisis,
            'nilai_simpulan_saran' => $request->nilai_simpulan_saran,
            'nilai_presentasi' => $request->nilai_presentasi,
            'catatan' => $request->catatan,
            'is_completed' => true
        ]);

        $penilaian->hitungJumlah();
        $penilaian->save();

        // Update rekapitulasi
        $this->updateRekapitulasi($penilaian->jadwal_skripsi_id);

        return redirect()->route('dosen.penilaian.index')
            ->with('success', 'Penilaian berhasil disimpan');
    }

    public function jadwal()
    {
        $dosenId = Auth::user()->dosen->id;

        $jadwal = JadwalSkripsi::with(['pendaftaran.mahasiswa.user', 'penilaian'])
            ->where('dosen_penguji_1_id', $dosenId)
            ->orWhere('dosen_penguji_2_id', $dosenId)
            ->orWhere('dosen_penguji_3_id', $dosenId)
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('dosen.penilaian.jadwal', compact('jadwal'));
    }

    private function getBobotPenguji($jadwal, $namaDosen)
    {
        // Bobot berdasarkan urutan penguji
        if ($jadwal->dosen_penguji_1 == $namaDosen) return 40;
        if ($jadwal->dosen_penguji_2 == $namaDosen) return 30;
        if ($jadwal->dosen_penguji_3 == $namaDosen) return 30;
        return 0;
    }

    private function updateRekapitulasi($jadwalId)
    {
        $penilaianList = PenilaianSkripsi::where('jadwal_skripsi_id', $jadwalId)
            ->where('is_completed', true)
            ->get();

        if ($penilaianList->count() < 2) {
            return; // Tunggu minimal 2 penguji
        }

        $totalNilai = 0;
        foreach ($penilaianList as $p) {
            $totalNilai += ($p->nilai_akhir * $p->bobot / 100);
        }

        $rekapitulasi = RekapitulasiNilaiSkripsi::updateOrCreate(
            ['jadwal_skripsi_id' => $jadwalId],
            ['nilai_akhir' => $totalNilai]
        );

        $rekapitulasi->huruf_mutu = $rekapitulasi->hitungHurufMutu();
        $rekapitulasi->save();
    }
}
