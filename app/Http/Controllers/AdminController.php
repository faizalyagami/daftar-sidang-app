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
        $totalMahasiswa = User::whereHas('role', function($q) {
            $q->where('role', 'mahasiswa');
        })->count();
        
        $totalSkripsi = PendaftaranSkripsi::count();
        $totalMetodologi = PendaftaranMetodologi::count();
        $pendingSkripsi = PendaftaranSkripsi::where('status', 'pending')->count();
        
        $skripsiTerbaru = PendaftaranSkripsi::with('mahasiswa.user')
            ->latest()
            ->take(10)
            ->get();
        
        $metodologiTerbaru = PendaftaranMetodologi::with('mahasiswa.user')
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

    public function allPendaftaran()
    {
        $skripsi = PendaftaranSkripsi::with('mahasiswa.user')->latest()->get();
        $metodologi = PendaftaranMetodologi::with('mahasiswa.user')->latest()->get();
        
        return view('admin.pendaftaran.index', compact('skripsi', 'metodologi'));
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
        return view('admin.mahasiswa.import');
    }
    
    public function importMahasiswaProcess(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);
        
        try {
            $import = new MahasiswaImport();
            Excel::import($import, $request->file('file'));
            
            $importedCount = $import->getImportedCount();
            $updatedCount = $import->getUpdatedCount();
            $errors = $import->getErrors();
            
            if ($importedCount == 0 && $updatedCount == 0 && count($errors) > 0) {
                return redirect()->back()
                    ->with('error', 'Gagal import data. Detail error:<br>' . implode('<br>', array_slice($errors, 0, 20)))
                    ->withInput();
            }
            
            $message = "Import selesai!";
            if ($importedCount > 0) {
                $message .= " <strong>{$importedCount}</strong> data baru ditambahkan.";
            }
            if ($updatedCount > 0) {
                $message .= " <strong>{$updatedCount}</strong> data diupdate.";
            }
            
            if (count($errors) > 0) {
                $message .= '<br><br><strong>Peringatan:</strong><br>' . implode('<br>', array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $message .= '<br>... dan ' . (count($errors) - 10) . ' error lainnya.';
                }
                return redirect()->route('admin.mahasiswa.index')->with('warning', $message);
            }
            
            return redirect()->route('admin.mahasiswa.index')->with('success', $message);
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal import data: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    public function mahasiswaList()
    {
        $mahasiswas = Mahasiswa::with('user')->latest()->paginate(20);
        return view('admin.mahasiswa.index', compact('mahasiswas'));
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
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
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

    /**
     * PERBAIKAN: Urutan parameter harus (type, id) sesuai route
     */
    public function showAssignForm($type, $id)
    {
        if ($type == 'skripsi') {
            $pendaftaran = PendaftaranSkripsi::with('mahasiswa.user')->findOrFail($id);
        } else {
            $pendaftaran = PendaftaranMetodologi::with('mahasiswa.user')->findOrFail($id);
        }
        
        $reviewers = User::whereHas('role', function($q) {
            $q->where('role', 'reviewer');
        })->get();
        
        $stats = $this->assignmentService->getReviewerStats();
        
        return view('admin.pendaftaran.assign-reviewer', compact('pendaftaran', 'reviewers', 'stats', 'type'));
    }
    
    /**
     * PERBAIKAN: Urutan parameter harus (type, id) sesuai route
     */
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
}