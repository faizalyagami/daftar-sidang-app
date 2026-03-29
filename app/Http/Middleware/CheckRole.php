<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = Auth::user()->role ? Auth::user()->role->role : null;
        
        if ($userRole !== $role) {
            abort(403, 'Unauthorized access. Anda tidak memiliki akses sebagai ' . $role . '.');
        }

        return $next($request);
    }
}