<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'must be logged in',
            ], 401);
        }

        // استخدام مكتبة Spatie للتحقق من الصلاحية
        if (!$user->can($permission)) {
            return response()->json([
                'status' => false,
                'message' => 'U have no permission',
            ], 403);
        }

        return $next($request);
    }
}
