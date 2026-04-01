<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\AcademicPeriod;
use App\Models\PendaftaranSkripsi;
use App\Models\PendaftaranMetodologi;
use App\Models\DokumenSkripsi;
use App\Models\DokumenMetodologi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }
            
            $userRole = Auth::user()->role ? Auth::user()->role->role : null;
            
            if ($userRole !== 'mahasiswa') {
                abort(403, 'Unauthorized access. Anda tidak memiliki akses sebagai mahasiswa.');
            }
            
            return $next($request);
        });
    }
    
    public function dashboard()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        $pendaftaranSkripsi = $mahasiswa->pendaftaranSkripsi()->latest()->get();
        $pendaftaranMetodologi = $mahasiswa->pendaftaranMetodologi()->latest()->get();

        // Durasi
        $skripsiDuration = $mahasiswa->getSkripsiDuration();
        $metodologiDuration = $mahasiswa->getMetodologiDuration();

        // Riwayat periode
        $skripsiPeriods = $mahasiswa->getSkripsiPeriods();
        $metodologiPeriods = $mahasiswa->getMetodologiPeriods();

        return view('mahasiswa.dashboard', compact(
            'pendaftaranSkripsi',
            'pendaftaranMetodologi',
            'skripsiDuration',
            'metodologiDuration',
            'skripsiPeriods',
            'metodologiPeriods'
        ));
    }
    
    public function daftarSkripsi()
    {
        $activePeriod = AcademicPeriod::getActive();
        return view('mahasiswa.daftar-skripsi', compact('activePeriod'));
    }

    public function storeSkripsi(Request $request)
    {
        try {
            $request->validate([
                'judul_skripsi' => 'required|string',
                'dosen_pembimbing' => 'required|string',
                'narasumber' => 'nullable|string',
                'tanggal_seminar' => 'nullable|date',
                'bukti_pembayaran_registrasi' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'bukti_pembayaran_sidang' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'bukti_pembayaran_skripsi' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
                'frs' => 'required|file|mimes:pdf|max:2048',
                'transkrip_nilai' => 'required|file|mimes:pdf|max:2048',
                'surat_bebas_perpus' => 'required|file|mimes:pdf|max:2048',
                'surat_bebas_alat_tes' => 'required|file|mimes:pdf|max:2048',
                'sertifikat_pesantren' => 'required|file|mimes:pdf|max:2048',
                'sertifikat_sks_non_akademik' => 'required|file|mimes:pdf|max:2048',
                'surat_lolos_turnitin' => 'required|file|mimes:pdf|max:2048',
                'sertifikat_toefl' => 'required|file|mimes:pdf|max:2048',
                'pas_foto' => 'required|file|mimes:jpg,jpeg,png|max:2048',
                'buku_bimbingan' => 'required|file|mimes:pdf|max:2048', 
                'surat_perbaikan' => 'required|file|mimes:pdf|max:2048',
                'surat_ijin_sidang' => 'required|file|mimes:pdf|max:2048',
                'berkas_skripsi' => 'required|file|mimes:pdf|max:10240',
            ]);
            
            $activePeriod = AcademicPeriod::getActive();
            if (!$activePeriod) {
                return redirect()->back()->with('error', 'Pendaftaran Sidang Skripsi sedang ditutup.');
            }

            $mahasiswa = Auth::user()->mahasiswa;

            if (!$mahasiswa) {
                return redirect()->back()->with('error', 'Data mahasiswa tidak ditemukan. Silakan hubungi admin.');
            }

            $pendaftaran = PendaftaranSkripsi::create([
                'mahasiswa_id' => $mahasiswa->id,
                'judul_skripsi' => $request->judul_skripsi,
                'dosen_pembimbing' => $request->dosen_pembimbing,
                'narasumber' => $request->narasumber,
                'tanggal_seminar' => $request->tanggal_seminar,
                'status' => 'pending',
                'academic_period_id' => $activePeriod->id,
            ]);

            // Upload dokumen
            $dokumenTypes = [
                'bukti_pembayaran_registrasi',
                'bukti_pembayaran_sidang',
                'bukti_pembayaran_skripsi',
                'frs',
                'transkrip_nilai',
                'surat_bebas_perpus',
                'surat_bebas_alat_tes',
                'sertifikat_pesantren',
                'sertifikat_sks_non_akademik',
                'surat_lolos_turnitin',
                'sertifikat_toefl',
                'pas_foto',
                'buku_bimbingan',
                'surat_perbaikan',
                'surat_ijin_sidang',
                'berkas_skripsi'
            ];

            foreach ($dokumenTypes as $dokumenType) {
                if ($request->hasFile($dokumenType)) {
                    $file = $request->file($dokumenType);
                    $path = $file->store("dokumen/skripsi/{$pendaftaran->id}/{$dokumenType}", 'public');
                    
                    DokumenSkripsi::create([
                        'pendaftaran_id' => $pendaftaran->id,
                        'jenis_dokumen' => $dokumenType,
                        'file_path' => $path
                    ]);
                }
            }

            return redirect()->route('mahasiswa.dashboard')->with('success', 'Pendaftaran skripsi berhasil dikirim');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function daftarMetodologi()
    {
        $mahasiswa = Auth::user()->mahasiswa;
        return view('mahasiswa.daftar-metodologi', compact('mahasiswa'));
    }

    public function storeMetodologi(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'no_hp' => 'required|string',
            'judul_penelitian' => 'required|string',
            'dosen_pembimbing' => 'required|string',
            'dosen_pembimbing_2' => 'nullable|string',
            'kuliah_peminatan' => 'required|string',
            'proposal_word' => 'required|file|mimes:doc,docx|max:5120',
            'kartu_bimbingan' => 'required|file|mimes:pdf|max:2048',
            'surat_ijin_ujian' => 'required|file|mimes:pdf|max:2048',
            'lembar_pengesahan' => 'required|file|mimes:pdf|max:2048',
        ]);

        $activePeriod = AcademicPeriod::getActive();
        if (!$activePeriod) {
            return redirect()->back()->with('error', 'Pendaftaran Ujian Metodologi Penelitian sedang ditutup.');
        }

        $mahasiswa = Auth::user()->mahasiswa;
        
        // Update no_hp if provided
        if ($request->no_hp) {
            $mahasiswa->update(['no_hp' => $request->no_hp]);
        }

        $pendaftaran = PendaftaranMetodologi::create([
            'mahasiswa_id' => $mahasiswa->id,
            'email' => $request->email,
            'judul_penelitian' => $request->judul_penelitian,
            'dosen_pembimbing' => $request->dosen_pembimbing,
            'dosen_pembimbing_2' => $request->dosen_pembimbing_2,
            'kuliah_peminatan' => $request->kuliah_peminatan,
            'status' => 'pending',
            'academic_period_id' => $activePeriod->id,
        ]);

        // Upload dokumen
        $dokumenTypes = [
            'proposal_word' => 'Proposal Penelitian (Word)',
            'kartu_bimbingan' => 'Kartu Bimbingan',
            'surat_ijin_ujian' => 'Surat Ijin Mengikuti Ujian',
            'lembar_pengesahan' => 'Lembar Pengesahan'
        ];

        foreach ($dokumenTypes as $field => $displayName) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . $field . '.' . $extension;
                $path = $file->storeAs(
                    "dokumen/metodologi/{$pendaftaran->id}/{$field}",
                    $fileName,
                    'public'
                );
                
                DokumenMetodologi::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'jenis_dokumen' => $field,
                    'file_path' => $path
                ]);
            }
        }

        return redirect()->route('mahasiswa.dashboard')->with('success', 'Pendaftaran ujian metodologi berhasil dikirim');
    }

    public function showSkripsi($id)
    {
        $pendaftaran = PendaftaranSkripsi::with(['mahasiswa', 'dokumen'])->findOrFail($id);
        
        if ($pendaftaran->mahasiswa_id != Auth::user()->mahasiswa->id) {
            abort(403);
        }
        
        return view('mahasiswa.show-skripsi', compact('pendaftaran'));
    }

    public function showMetodologi($id)
    {
        $pendaftaran = PendaftaranMetodologi::with(['mahasiswa.user', 'dokumen'])->findOrFail($id);
        
        if ($pendaftaran->mahasiswa_id != Auth::user()->mahasiswa->id) {
            abort(403);
        }
        
        return view('mahasiswa.show-metodologi', compact('pendaftaran'));
    }
}