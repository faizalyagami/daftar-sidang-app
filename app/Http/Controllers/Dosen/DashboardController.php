<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $userRole = Auth::user()->role ? Auth::user()->role->role : null;

            if ($userRole !== 'dosen') {
                abort(403, 'Unauthorized access. Anda tidak memiliki akses sebagai dosen.');
            }

            return $next($request);
        });
    }

    public function index()
    {
        return view('dosen.dashboard');
    }
}
