<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized. Silakan login terlebih dahulu.'
                ], 401);
            }
            
            return redirect()->route('admin.login')
                           ->with('error', 'Silakan login terlebih dahulu');
        }

        $admin = Auth::guard('admin')->user();
        
        if (!$admin->is_active) {
            Auth::guard('admin')->logout();
            
            return redirect()->route('admin.login')
                           ->with('error', 'Akun Anda tidak aktif. Silakan hubungi administrator.');
        }

        return $next($request);
    }
}
