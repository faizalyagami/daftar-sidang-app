<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\FeedbackSkripsi;
use App\Models\JadwalSkripsi;
use App\Models\PenilaianSkripsi;
use App\Models\RekapitulasiNilaiSkripsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        $dosen = Auth::user()->dosen;

        if (!$dosen) {
            abort(403, 'Data dosen tidak ditemukan. Silakan hubungi admin.');
        }

        $dosenId = $dosen->id;

        $jadwal = JadwalSkripsi::with(['pendaftaran.mahasiswa.user', 'penilaian', 'feedback'])
            ->where(function ($query) use ($dosenId) {
                $query->where('dosen_penguji_1_id', $dosenId)
                    ->orWhere('dosen_penguji_2_id', $dosenId)
                    ->orWhere('dosen_penguji_3_id', $dosenId);
            })
            ->get();

        return view('dosen.penilaian.index', compact('jadwal'));
    }

    public function create($id)
    {
        $jadwal = JadwalSkripsi::with('pendaftaran.mahasiswa.user')->findOrFail($id);

        $dosen = Auth::user()->dosen;

        if (!$dosen) {
            abort(403, 'Data dosen tidak ditemukan. Silakan hubungi admin.');
        }

        $dosenId = $dosen->id;
        $namaDosen = $dosen->name;

        // Cek apakah dosen ini adalah penguji (berdasarkan ID)
        $isPenguji = in_array($dosenId, [
            $jadwal->dosen_penguji_1_id,
            $jadwal->dosen_penguji_2_id,
            $jadwal->dosen_penguji_3_id
        ]);

        if (!$isPenguji) {
            abort(403, 'Anda tidak terdaftar sebagai penguji untuk sidang ini.');
        }

        $penilaian = PenilaianSkripsi::where('jadwal_skripsi_id', $id)
            ->where('dosen_id', $dosenId)
            ->first();

        if (!$penilaian) {
            $penilaian = PenilaianSkripsi::create([
                'jadwal_skripsi_id' => $id,
                'dosen_id' => $dosenId,
                'nama_dosen_penguji' => $namaDosen,
                'bobot' => $this->getBobotPenguji($jadwal, $dosenId)
            ]);
        }

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

    public function show($id)
    {
        $dosen = Auth::user()->dosen;

        if (!$dosen) {
            abort(403, 'Data dosen tidak ditemukan. Silakan hubungi admin.');
        }

        \Log::info('Show penilaian - Parameter ID: ' . $id);
        \Log::info('Dosen login: ' . $dosen->name . ' (ID: ' . $dosen->id . ')');

        // Cari penilaian berdasarkan jadwal_skripsi_id
        $penilaian = PenilaianSkripsi::where('jadwal_skripsi_id', $id)
            ->with(['jadwal.pendaftaran.mahasiswa.user', 'jadwal.penguji1', 'jadwal.penguji2', 'jadwal.penguji3'])
            ->first();

        \Log::info('Penilaian ditemukan: ' . ($penilaian ? 'Ya' : 'Tidak'));

        if (!$penilaian) {
            // Jika tidak ditemukan, coba berdasarkan ID penilaian langsung
            $penilaian = PenilaianSkripsi::find($id);
            \Log::info('Pencarian berdasarkan ID penilaian: ' . ($penilaian ? 'Ditemukan' : 'Tidak ditemukan'));
        }

        if (!$penilaian) {
            return redirect()->route('dosen.penilaian.index')
                ->with('error', 'Data penilaian tidak ditemukan. ID yang dicari: ' . $id);
        }

        // Update dosen_id jika masih kosong
        if (!$penilaian->dosen_id) {
            $penilaian->update(['dosen_id' => $dosen->id]);
        }

        $jadwal = $penilaian->jadwal;
        $isReadOnly = true;

        return view('dosen.penilaian.show', compact('penilaian', 'jadwal', 'isReadOnly'));
    }

    public function jadwal()
    {
        $dosen = Auth::user()->dosen;

        if (!$dosen) {
            abort(403, 'Data dosen tidak ditemukan. Silakan hubungi admin.');
        }

        $dosenId = $dosen->id;

        $jadwal = JadwalSkripsi::with(['pendaftaran.mahasiswa.user', 'penilaian', 'feedback'])
            ->where(function ($query) use ($dosenId) {
                $query->where('dosen_penguji_1_id', $dosenId)
                    ->orWhere('dosen_penguji_2_id', $dosenId)
                    ->orWhere('dosen_penguji_3_id', $dosenId);
            })
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('dosen.penilaian.jadwal', compact('jadwal'));
    }

    private function getBobotPenguji($jadwal, $dosenId)
    {
        // Bobot berdasarkan urutan penguji (menggunakan ID)
        if ($jadwal->dosen_penguji_1_id == $dosenId) return 40;
        if ($jadwal->dosen_penguji_2_id == $dosenId) return 30;
        if ($jadwal->dosen_penguji_3_id == $dosenId) return 30;
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

        if ($rekapitulasi) {
            $rekapitulasi->huruf_mutu = $this->hitungHurufMutu($totalNilai);
            $rekapitulasi->save();
        }
    }

    private function hitungHurufMutu($nilai)
    {
        if ($nilai <= 44.00) return 'E';
        if ($nilai <= 55.49) return 'D';
        if ($nilai <= 59.49) return 'C';
        if ($nilai <= 63.49) return 'C+';
        if ($nilai <= 67.49) return 'B-';
        if ($nilai <= 71.49) return 'B';
        if ($nilai <= 75.49) return 'B+';
        if ($nilai <= 79.50) return 'A-';
        return 'A';
    }

    public function feedback($id)
    {
        $jadwal = JadwalSkripsi::with('pendaftaran.mahasiswa.user')->findOrFail($id);

        $dosen = Auth::user()->dosen;

        if (!$dosen) {
            abort(403, 'Data dosen tidak ditemukan. Silakan hubungi admin.');
        }

        $dosenId = $dosen->id;
        $namaDosen = $dosen->name;

        // Cek apakah dosen ini adalah penguji (berdasarkan ID)
        $isPenguji = in_array($dosenId, [
            $jadwal->dosen_penguji_1_id,
            $jadwal->dosen_penguji_2_id,
            $jadwal->dosen_penguji_3_id
        ]);

        if (!$isPenguji) {
            abort(403, 'Anda tidak terdaftar sebagai penguji untuk sidang ini.');
        }

        // CARI FEEDBACK BERDASARKAN DOSEN_ID
        $feedback = FeedbackSkripsi::where('jadwal_skripsi_id', $id)
            ->where('dosen_id', $dosenId)
            ->first();

        if (!$feedback) {
            $feedback = FeedbackSkripsi::create([
                'jadwal_skripsi_id' => $id,
                'dosen_id' => $dosenId,
                'nama_dosen' => $namaDosen,
                'is_completed' => false
            ]);
        }

        // Update dosen_id jika masih kosong
        if (!$feedback->dosen_id) {
            $feedback->update(['dosen_id' => $dosenId]);
        }

        $isReadOnly = $feedback->is_completed;

        return view('dosen.penilaian.feedback', compact('jadwal', 'feedback', 'isReadOnly'));
    }

    public function storeFeedback(Request $request, $id)
    {
        $feedback = FeedbackSkripsi::findOrFail($id);

        // Pastikan feedback milik dosen yang login
        $dosen = Auth::user()->dosen;

        if (!$dosen) {
            abort(403, 'Data dosen tidak ditemukan.');
        }

        if ($feedback->dosen_id != $dosen->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit feedback ini.');
        }

        $request->validate([
            'catatan_perbaikan' => 'nullable|string',
            'catatan_bagian_depan' => 'nullable|string',
            'catatan_bab1' => 'nullable|string',
            'catatan_bab2' => 'nullable|string',
            'catatan_bab3' => 'nullable|string',
            'catatan_bab4' => 'nullable|string',
            'catatan_bab5' => 'nullable|string',
            'catatan_daftar_pustaka' => 'nullable|string',
            'catatan_presentasi' => 'nullable|string',
        ]);

        $feedback->update([
            'catatan_perbaikan' => $request->catatan_perbaikan,
            'catatan_bagian_depan' => $request->catatan_bagian_depan,
            'catatan_bab1' => $request->catatan_bab1,
            'catatan_bab2' => $request->catatan_bab2,
            'catatan_bab3' => $request->catatan_bab3,
            'catatan_bab4' => $request->catatan_bab4,
            'catatan_bab5' => $request->catatan_bab5,
            'catatan_daftar_pustaka' => $request->catatan_daftar_pustaka,
            'catatan_presentasi' => $request->catatan_presentasi,
            'perbaikan_mayor' => $request->has('perbaikan_mayor'),
            'perbaikan_minor' => $request->has('perbaikan_minor'),
            'rekomendasi' => $request->has('perbaikan_mayor') ? 'perbaikan_mayor' : ($request->has('perbaikan_minor') ? 'perbaikan_minor' : 'layak'),
            'is_completed' => true
        ]);

        return redirect()->route('dosen.penilaian.index')
            ->with('success', 'Lembar feedback berhasil disimpan. Silakan lanjutkan ke penilaian.');
    }
}
