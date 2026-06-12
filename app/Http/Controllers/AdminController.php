<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Mahasiswa;
use App\Models\PendaftaranSkripsi;
use App\Models\PendaftaranMetodologi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MahasiswaImport;
use App\Models\AcademicPeriod;
use App\Services\ReviewerAssignmentService;

class AdminController extends Controller
{
    protected $assignmentService;

    public function __construct(ReviewerAssignmentService $assignmentService)
    {
        $this->assignmentService = $assignmentService;

        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $userRole = Auth::user()->role ? Auth::user()->role->role : null;

            if ($userRole !== 'admin') {
                abort(403, 'Unauthorized access. Anda tidak memiliki akses sebagai admin.');
            }

            return $next($request);
        });
    }

    public function dashboard()
    {
        $totalMahasiswa = User::whereHas('role', function ($q) {
            $q->where('role', 'mahasiswa');
        })->count();

        $totalSkripsi = PendaftaranSkripsi::whereIn('status', [
            'pending',
            'review',
            'approved',
            'rejected'
        ])
            ->distinct('mahasiswa_id')
            ->count('mahasiswa_id');
        $totalMetodologi = PendaftaranMetodologi::whereIn('status', [
            'pending',
            'review',
            'approved',
            'rejected'
        ])
            ->distinct('mahasiswa_id')
            ->count('mahasiswa_id');

        $pendingSkripsi = PendaftaranSkripsi::where('status', 'pending')->count();

        $skripsiTerbaru = PendaftaranSkripsi::with('mahasiswa.user')
            ->where('status', '!=', 'belum_daftar')
            ->latest()
            ->take(10)
            ->get();

        $metodologiTerbaru = PendaftaranMetodologi::with('mahasiswa.user')
            ->where('status', "!=", "belum_daftar")
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalMahasiswa',
            'totalSkripsi',
            'totalMetodologi',
            'pendingSkripsi',
            'skripsiTerbaru',
            'metodologiTerbaru'
        ));
    }

    public function manageUsers(Request $request)
    {
        $perPage = $request->get('per_page', 10);

        $users = User::with('role', 'mahasiswa')
            ->leftJoin('mahasiswas', 'users.id', '=', 'mahasiswas.user_id')
            ->select('users.*', 'mahasiswas.npm')
            ->orderByRaw('CASE 
                WHEN mahasiswas.npm IS NULL THEN 1 
                ELSE 0 
            END, mahasiswas.npm ASC')
            ->paginate($perPage);

        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        return view('admin.users.create');
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,reviewer,mahasiswa',
            'npm' => 'required_if:role,mahasiswa|unique:mahasiswas,npm',
            'tempat_lahir' => 'required_if:role,mahasiswa',
            'tanggal_lahir' => 'required_if:role,mahasiswa|date',
            'no_hp' => 'required_if:role,mahasiswa',
            'dosen_wali' => 'required_if:role,mahasiswa'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->npm ?? $request->email,
            'password' => Hash::make($request->password),
            'is_default_password' => true
        ]);

        Role::create([
            'user_id' => $user->id,
            'role' => $request->role
        ]);

        if ($request->role == 'mahasiswa') {
            Mahasiswa::create([
                'user_id' => $user->id,
                'npm' => $request->npm,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'no_hp' => $request->no_hp,
                'dosen_wali' => $request->dosen_wali,
                'ipk' => $request->ipk ?? null
            ]);
        }

        return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan');
    }

    public function allPendaftaran(Request $request)
    {
        $periodId = $request->get('period_id');

        $perPageSkripsi = $request->get('per_page_skripsi', 10);
        $perPageMetodologi = $request->get('per_page_metodologi', 10);

        // Ambil data dengan unique per mahasiswa_id + academic_period_id
        $skripsi = PendaftaranSkripsi::with('mahasiswa.user', 'academicPeriod')
            ->when($periodId, function ($query, $periodId) {
                return $query->where('academic_period_id', $periodId);
            })
            ->latest()
            ->get()
            ->unique(function ($item) {
                return $item->mahasiswa_id . '_' . $item->academic_period_id;
            })
            ->values(); // Reset index setelah unique

        $metodologi = PendaftaranMetodologi::with('mahasiswa.user', 'academicPeriod')
            ->when($periodId, function ($query, $periodId) {
                return $query->where('academic_period_id', $periodId);
            })
            ->latest()
            ->get()
            ->unique(function ($item) {
                return $item->mahasiswa_id . '_' . $item->academic_period_id;
            })
            ->values();

        // Pagination manual untuk collection
        $currentPageSkripsi = $request->get('page_skripsi', 1);
        $skripsi = new \Illuminate\Pagination\LengthAwarePaginator(
            $skripsi->forPage($currentPageSkripsi, $perPageSkripsi),
            $skripsi->count(),
            $perPageSkripsi,
            $currentPageSkripsi,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'page_skripsi']
        );

        $currentPageMetodologi = $request->get('page_metodologi', 1);
        $metodologi = new \Illuminate\Pagination\LengthAwarePaginator(
            $metodologi->forPage($currentPageMetodologi, $perPageMetodologi),
            $metodologi->count(),
            $perPageMetodologi,
            $currentPageMetodologi,
            ['path' => $request->url(), 'query' => $request->query(), 'pageName' => 'page_metodologi']
        );

        $periods = AcademicPeriod::orderBy('tahun_akademik', 'desc')->get();

        $reviewers = User::whereHas('role', function ($q) {
            $q->where('role', 'reviewer');
        })->get();

        return view('admin.pendaftaran.index', compact('skripsi', 'metodologi', 'periods', 'reviewers'));
    }

    public function showSkripsi($id)
    {
        $pendaftaran = PendaftaranSkripsi::with(['mahasiswa.user', 'dokumen', 'reviewDetails'])->findOrFail($id);
        return view('admin.pendaftaran.show-skripsi', compact('pendaftaran'));
    }

    public function showMetodologi($id)
    {
        $pendaftaran = PendaftaranMetodologi::with(['mahasiswa.user', 'dokumen', 'reviewDetails'])->findOrFail($id);
        return view('admin.pendaftaran.show-metodologi', compact('pendaftaran'));
    }

    public function importMahasiswa()
    {
        $periods = AcademicPeriod::orderBy('tahun_akademik', 'desc')->get();
        return view('admin.mahasiswa.import', compact('periods'));
    }

    public function importMahasiswaProcess(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:20480', // Maks 20MB
            'academic_period_id' => 'required|exists:academic_periods,id',
            'jenis_perwalian' => 'required|in:skripsi,metodologi',
        ]);

        $academicPeriod = AcademicPeriod::findOrFail($request->academic_period_id);
        $jenis = $request->jenis_perwalian;

        try {
            $import = new MahasiswaImport($jenis, $academicPeriod->id);
            Excel::import($import, $request->file('file'));

            $importedCount = $import->getImportedCount();
            $updatedCount = $import->getUpdatedCount();
            $registeredCount = $import->getRegisteredCount();
            $errors = $import->getErrors();

            $message = "Import selesai!";
            if ($importedCount > 0) {
                $message .= " <strong>{$importedCount}</strong> data mahasiswa baru ditambahkan.";
            }
            if ($updatedCount > 0) {
                $message .= " <strong>{$updatedCount}</strong> data mahasiswa diupdate.";
            }
            if ($registeredCount > 0) {
                $message .= " <strong>{$registeredCount}</strong> pendaftaran {$jenis} berhasil dibuat.";
            }

            if (count($errors) > 0) {
                $message .= '<br><br><strong>Peringatan:</strong><br>' . implode('<br>', array_slice($errors, 0, 20));
                return redirect()->route('admin.mahasiswa.index')->with('warning', $message);
            }

            return redirect()->route('admin.mahasiswa.index')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal import data: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function mahasiswaList(Request $request)
    {
        // dd($request->all());x
        $perPage = $request->get('per_page', 20);
        $periodId = $request->get('period_id');

        // if ($periodId) {
        //     $mahasiswaIds = PendaftaranSkripsi::where('academic_period_id', $periodId)
        //         ->pluck('mahasiswa_id')
        //         ->toArray();
        //     dd($mahasiswaIds);
        // }

        \Log::info('Period ID: ' . $periodId);

        $query = Mahasiswa::with('user', 'pendaftaranSkripsi', 'pendaftaranMetodologi');

        if ($periodId) {
            $query->where(function ($q) use ($periodId) {
                $q->whereHas('pendaftaranSkripsi', function ($sub) use ($periodId) {
                    $sub->where('academic_period_id', $periodId);
                })->orWhereHas('pendaftaranMetodologi', function ($sub) use ($periodId) {
                    $sub->where('academic_period_id', $periodId);
                });
            });
            \Log::info('Jumlah setelah filter: ' . $query->count());
        }

        $mahasiswas = $query->latest()->paginate($perPage);
        $periods = AcademicPeriod::orderBy('tahun_akademik', 'desc')->get();

        // \DB::enableQueryLog();
        // $mahasiswas = $query->latest()->paginate($perPage);
        // dd(\DB::getQueryLog());

        return view('admin.mahasiswa.index', compact('mahasiswas', 'periods'));
    }


    public function mahasiswaDetail($id)
    {
        $mahasiswa = Mahasiswa::with('user')->findOrFail($id);
        return view('admin.mahasiswa.detail', compact('mahasiswa'));
    }

    public function exportTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_import_mahasiswa.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');

            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'NPM',
                'Nama Mahasiswa',
                'NIK Dosen Wali',
                'Dosen Wali',
                'SKS Lulus',
                'SKS Tempuh',
                'SKS Sisa',
                'IPK',
                'IPK (3 digit)',
                'Tmpt Lahir',
                'Tgl Lahir'
            ]);

            fputcsv($file, [
                '10050019026',
                'MOCHAMAD AZMI FAUZAN MUSYAFA',
                'D150673',
                'RIZKA HADIAN PERMANA., S.PSI, M.PSI.',
                '107',
                '121',
                '39',
                '2.57',
                '2.572',
                'BANDUNG',
                '23-12-00'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }


    public function showAssignForm($type, $id)
    {
        if ($type == 'skripsi') {
            $pendaftaran = PendaftaranSkripsi::with('mahasiswa.user')->findOrFail($id);
        } else {
            $pendaftaran = PendaftaranMetodologi::with('mahasiswa.user')->findOrFail($id);
        }

        $reviewers = User::whereHas('role', function ($q) {
            $q->where('role', 'reviewer');
        })->get();

        $stats = $this->assignmentService->getReviewerStats();

        return view('admin.pendaftaran.assign-reviewer', compact('pendaftaran', 'reviewers', 'stats', 'type'));
    }


    public function assignReviewer(Request $request, $type, $id)
    {
        if ($type == 'skripsi') {
            $pendaftaran = PendaftaranSkripsi::findOrFail($id);
        } else {
            $pendaftaran = PendaftaranMetodologi::findOrFail($id);
        }

        // Jika pilih auto assign
        if ($request->assignment_type == 'auto') {
            $result = $this->assignmentService->autoAssign($pendaftaran, $type);
        } else {
            // Manual assign ke reviewer tertentu
            $request->validate([
                'reviewer_id' => 'required|exists:users,id'
            ]);

            $result = $this->assignmentService->manualAssign($pendaftaran, $type, $request->reviewer_id);
        }

        if ($result['success']) {
            return redirect()->route('admin.pendaftaran.' . $type . '.show', $id)
                ->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'skripsi_ids' => 'array',
            'metodologi_ids' => 'array',
            'reviewer_id' => 'required|exists:users,id',
            'notes' => 'nullable|string'
        ]);

        $reviewerId = $request->reviewer_id;
        $count = 0;

        // Assign skripsi
        if (!empty($request->skripsi_ids)) {
            $count += PendaftaranSkripsi::whereIn('id', $request->skripsi_ids)
                ->where('status', 'pending')
                ->update([
                    'reviewer_id' => $reviewerId,
                    'status' => 'review',
                    'reviewer_notes' => $request->notes
                ]);
        }

        // Assign metodologi
        if (!empty($request->metodologi_ids)) {
            $count += PendaftaranMetodologi::whereIn('id', $request->metodologi_ids)
                ->where('status', 'pending')
                ->update([
                    'reviewer_id' => $reviewerId,
                    'status' => 'review',
                    'reviewer_notes' => $request->notes
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => "{$count} pendaftaran berhasil diassign ke reviewer."
        ]);
    }

    public function reviewerStats()
    {
        $stats = $this->assignmentService->getReviewerStats();
        return view('admin.reviewers.stats', compact('stats'));
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);

        if ($user->hasRole('mahasiswa') && $user->mahasiswa) {
            $user->update([
                'password' => Hash::make($user->mahasiswa->npm),
                'is_default_password' => true
            ]);

            return response()->json(['success' => true, 'message' => 'Password berhasil direset ke NPM']);
        }

        return response()->json(['success' => false, 'message' => 'Gagal reset password'], 400);
    }

    public function getMahasiswaPeriods($id, Request $request)
    {
        $jenis = $request->get('jenis');
        $mahasiswa = Mahasiswa::findOrFail($id);

        if ($jenis == 'skripsi') {
            $periods = $mahasiswa->pendaftaranSkripsi()
                ->with('academicPeriod')
                ->orderBy('created_at')
                ->get()
                ->map(function ($p) {
                    return $p->academicPeriod ? $p->academicPeriod->semester . ' ' . $p->academicPeriod->tahun_akademik : 'Periode tidak diketahui';
                });
        } else {
            $periods = $mahasiswa->pendaftaranMetodologi()
                ->with('academicPeriod')
                ->orderBy('created_at')
                ->get()
                ->map(function ($p) {
                    return $p->academicPeriod ? $p->academicPeriod->semester . ' ' . $p->academicPeriod->tahun_akademik : 'Periode tidak diketahui';
                });
        }

        return response()->json($periods);
    }

    public function edit($id)
    {
        $period = AcademicPeriod::findOrFail($id);
        return view('admin.academic-periods.edit', compact('period'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'semester' => 'required|in:Ganjil,Genap',
            'tahun_akademik' => 'required|string|regex:/^\d{4}\/\d{4}$/',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'skripsi_open' => 'boolean',
            'metodologi_open' => 'boolean',
            'is_active' => 'boolean',
            'description' => 'nullable|string'
        ]);

        $period = AcademicPeriod::findOrFail($id);

        if ($request->is_active) {
            AcademicPeriod::where('id', '!=', $id)->update(['is_active' => false]);
        }

        $period->update([
            'semester' => $request->semester,
            'tahun_akademik' => $request->tahun_akademik,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->is_active ? true : false,
            'skripsi_open' => $request->skripsi_open ? true : false,
            'metodologi_open' => $request->metodologi_open ? true : false,
            'description' => $request->description
        ]);

        return redirect()->route('admin.academic-periods.index')
            ->with('success', 'Periode akademik berhasil diupdate');
    }
}
