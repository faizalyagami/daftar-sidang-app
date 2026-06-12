<?php

namespace App\Http\Controllers;

use App\Models\AcademicPeriod;
use App\Models\PendaftaranSkripsi;
use App\Models\PendaftaranMetodologi;
use App\Models\ReviewerAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewerController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $userRole = Auth::user()->role ? Auth::user()->role->role : null;

            if ($userRole !== 'reviewer') {
                abort(403, 'Unauthorized access. Anda tidak memiliki akses sebagai reviewer.');
            }

            return $next($request);
        });
    }

    public function dashboard()
    {
        $reviewerId = Auth::id();

        // Ambil pendaftaran skripsi yang diassign ke reviewer ini
        $skripsi = PendaftaranSkripsi::with('mahasiswa.user')
            ->where('reviewer_id', $reviewerId)
            ->whereIn('status', ['review', 'revision'])
            ->latest()
            ->get();

        // Ambil pendaftaran metodologi yang diassign ke reviewer ini
        $metodologi = PendaftaranMetodologi::with('mahasiswa.user')
            ->where('reviewer_id', $reviewerId)
            ->whereIn('status', ['review', 'revision'])
            ->latest()
            ->get();

        // Hitung statistik
        $totalPending = PendaftaranSkripsi::where('reviewer_id', $reviewerId)
            ->where('status', 'review')
            ->count() + PendaftaranMetodologi::where('reviewer_id', $reviewerId)
            ->where('status', 'review')
            ->count();

        $totalRevision = PendaftaranSkripsi::where('reviewer_id', $reviewerId)
            ->where('status', 'revision')
            ->count() + PendaftaranMetodologi::where('reviewer_id', $reviewerId)
            ->where('status', 'revision')
            ->count();

        $totalCompleted = PendaftaranSkripsi::where('reviewer_id', $reviewerId)
            ->where('status', 'approved')
            ->count() + PendaftaranMetodologi::where('reviewer_id', $reviewerId)
            ->where('status', 'approved')
            ->count();

        return view('reviewer.dashboard', compact(
            'skripsi',
            'metodologi',
            'totalPending',
            'totalRevision',
            'totalCompleted'
        ));
    }

    public function history(Request $request)
    {
        $reviewerId = Auth::id();
        $periodId = $request->get('period_id');

        // Query skripsi yang sudah selesai (approved, rejected, revision)
        $skripsiQuery = PendaftaranSkripsi::with('mahasiswa.user')
            ->where('reviewer_id', $reviewerId)
            ->whereIn('status', ['approved', 'rejected', 'revision']);

        // Query metodologi yang sudah selesai
        $metodologiQuery = PendaftaranMetodologi::with('mahasiswa.user')
            ->where('reviewer_id', $reviewerId)
            ->whereIn('status', ['approved', 'rejected', 'revision']);

        if ($periodId) {
            $skripsiQuery->where('academic_period_id', $periodId);
            $metodologiQuery->where('academic_period_id', $periodId);
        }

        $skripsi = $skripsiQuery->latest()->get();
        $metodologi = $metodologiQuery->latest()->get();

        $periods = AcademicPeriod::orderBy('tahun_akademik', 'desc')->get();

        return view('reviewer.history', compact('skripsi', 'metodologi', 'periods'));
    }

    public function reviewSkripsi($id)
    {
        $pendaftaran = PendaftaranSkripsi::with(['mahasiswa.user', 'dokumen', 'reviewDetails'])
            ->where('reviewer_id', Auth::id())
            ->findOrFail($id);

        // Update status assignment menjadi in_progress
        ReviewerAssignment::where('pendaftaran_id', $id)
            ->where('pendaftaran_type', 'skripsi')
            ->update([
                'status' => 'in_progress',
                'started_at' => now()
            ]);

        $reviewMap = [];
        foreach ($pendaftaran->reviewDetails as $review) {
            $reviewMap[$review->jenis_dokumen] = $review;
        }

        return view('reviewer.review-skripsi', compact('pendaftaran', 'reviewMap'));
    }

    public function submitSkripsiReview(Request $request, $id)
    {
        try {
            // Validasi request
            $validated = $request->validate([
                'reviews' => 'required|array',
                'reviews.*.status' => 'required|in:valid,invalid,reupload',
                'reviews.*.komentar' => 'nullable|string',
                'overall_notes' => 'nullable|string'
            ]);

            $pendaftaran = PendaftaranSkripsi::findOrFail($id);

            // Debug: cek data yang masuk
            \Log::info('Review data received:', $request->all());

            // Validasi per dokumen: jika status invalid/reupload, komentar wajib diisi
            foreach ($request->reviews as $jenisDokumen => $review) {
                if (in_array($review['status'], ['invalid', 'reupload']) && empty($review['komentar'])) {
                    return redirect()->back()
                        ->with('error', "Dokumen " . ucfirst(str_replace('_', ' ', $jenisDokumen)) . " membutuhkan komentar karena statusnya {$review['status']}.")
                        ->withInput();
                }
            }

            // Simpan review details
            foreach ($request->reviews as $jenisDokumen => $review) {
                \App\Models\ReviewDetail::updateOrCreate(
                    [
                        'pendaftaran_id' => $pendaftaran->id,
                        'pendaftaran_type' => PendaftaranSkripsi::class,
                        'jenis_dokumen' => $jenisDokumen,
                        'reviewer_id' => Auth::id()
                    ],
                    [
                        'status' => $review['status'],
                        'komentar' => $review['komentar'] ?? null
                    ]
                );
            }

            // Update catatan keseluruhan
            if ($request->overall_notes) {
                $pendaftaran->update(['reviewer_notes' => $request->overall_notes]);
            }

            // Update status pendaftaran berdasarkan review
            $this->updateStatusFromReviews($pendaftaran);

            // Update assignment status
            ReviewerAssignment::updateOrCreate(
                [
                    'pendaftaran_id' => $id,
                    'pendaftaran_type' => 'skripsi'
                ],
                [
                    'reviewer_id' => Auth::id(),
                    'status' => 'completed',
                    'completed_at' => now()
                ]
            );

            $message = $pendaftaran->status == 'approved'
                ? '✅ Semua dokumen valid. Pendaftaran disetujui untuk lanjut sidang.'
                : '📝 Review telah disimpan. Mahasiswa akan melakukan perbaikan.';

            return redirect()->route('reviewer.dashboard')->with('success', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Review submit error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function updateStatusFromReviews($pendaftaran)
    {
        $reviewDetails = $pendaftaran->reviewDetails;

        $hasReupload = $reviewDetails->contains('status', 'reupload');
        $hasInvalid = $reviewDetails->contains('status', 'invalid');
        $allValid = !$hasReupload && !$hasInvalid;

        if ($allValid) {
            $pendaftaran->update(['status' => 'approved']);
        } elseif ($hasReupload) {
            $pendaftaran->update(['status' => 'revision']);
        } else {
            $pendaftaran->update(['status' => 'rejected']);
        }
    }

    public function reviewMetodologi($id)
    {
        $pendaftaran = PendaftaranMetodologi::with(['mahasiswa.user', 'dokumen', 'reviewDetails'])
            ->where('reviewer_id', Auth::id())
            ->findOrFail($id);

        ReviewerAssignment::where('pendaftaran_id', $id)
            ->where('pendaftaran_type', 'metodologi')
            ->update([
                'status' => 'in_progress',
                'started_at' => now()
            ]);

        $reviewMap = [];
        foreach ($pendaftaran->reviewDetails as $review) {
            $reviewMap[$review->jenis_dokumen] = $review;
        }

        return view('reviewer.review-metodologi', compact('pendaftaran', 'reviewMap'));
    }

    public function submitMetodologiReview(Request $request, $id)
    {
        $request->validate([
            'reviews' => 'required|array',
            'reviews.*.status' => 'required|in:valid,invalid,reupload',
            'reviews.*.komentar' => 'nullable|string|required_if:reviews.*.status,invalid,reupload',
            'overall_notes' => 'nullable|string'
        ]);

        $pendaftaran = PendaftaranMetodologi::findOrFail($id);

        foreach ($request->reviews as $dokumenType => $review) {
            \App\Models\ReviewDetail::updateOrCreate(
                [
                    'pendaftaran_id' => $pendaftaran->id,
                    'pendaftaran_type' => PendaftaranMetodologi::class,
                    'jenis_dokumen' => $dokumenType,
                    'reviewer_id' => Auth::id()
                ],
                [
                    'status' => $review['status'],
                    'komentar' => $review['komentar'] ?? null
                ]
            );
        }

        if ($request->overall_notes) {
            $pendaftaran->update(['reviewer_notes' => $request->overall_notes]);
        }

        $pendaftaran->updateStatusFromReviews();

        ReviewerAssignment::where('pendaftaran_id', $id)
            ->where('pendaftaran_type', 'metodologi')
            ->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);

        $message = $pendaftaran->status == 'approved'
            ? 'Semua dokumen valid. Pendaftaran disetujui untuk lanjut ujian metodologi.'
            : 'Review telah disimpan. Mahasiswa akan melakukan perbaikan.';

        return redirect()->route('reviewer.dashboard')->with('success', $message);
    }
}
