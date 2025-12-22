<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * تسجيل الدخول باستخدام اسم المستخدم وكلمة المرور
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:4',
        ]);

        $user = User::where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid username or password'
            ], 401);
        }

        // حذف التوكنات القديمة (اختياري)
        $user->tokens()->delete();

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user'    => new UserResource($user->load(['roles', 'permissions', 'department', 'committees.manager', 'committees.department', 'parent'])),
            'token'   => $token,
        ]);
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * بيانات المستخدم الحالي
     */
    public function me(Request $request)
    {
        return response()->json($request->user()->load(['roles', 'permissions']));
    }

    /**
     * تحديث التوكن (تجديد)
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $newToken = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'message' => 'Token refreshed',
            'token'   => $newToken,
        ]);
    }
}