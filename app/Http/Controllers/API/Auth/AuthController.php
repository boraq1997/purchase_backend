<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\ActivityLogService;

class AuthController extends Controller
{

    protected ActivityLogService $activityLog;

    public function __construct(ActivityLogService $activityLog) {
        $this->activityLog = $activityLog;
    }

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
            $this->activityLog->log(
                action: 'login',
                actionLabel: 'محاولة تسجيل دخول فاشلة',
                actorType: 'Gust',
                status: 'failed',
                severity: 'warning',
                module: 'auth',
                metadata: [
                    'username' => $data['username'],
                ]
            );

            return response()->json([
                'message' => 'Invalid username or password'
            ], 401);
        }

        // حذف التوكنات القديمة (اختياري)
        $user->tokens()->delete();

        $token = $user->createToken('api_token')->plainTextToken;

        $this->activityLog->log(
            action: 'login',
            actionLabel: 'تسجيل دخول ناجح',
            actorId: $user->id,
            actorName: $user->name ?? $user->username,
            actorType: 'User',
            subjectType: 'User',
            subjectId: $user->id,
            subjectIdentifier: $user->username,
            module: 'auth',
            metadata: [
                'login_method' => 'username_password'
            ]
        );

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
        $user = $request->user();

        $this->activityLog->log(
            action: 'logout',
            actionLabel: 'تسجيل خروج',
            actorId: $user->id,
            actorName: $user->name ?? $user->username,
            actorType: 'User',
            subjectType: 'User',
            subjectId: $user->id,
            subjectIdentifier: $user->username,
            module: 'auth'
        );

        $user->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * بيانات المستخدم الحالي
     */
    public function me(Request $request)
    {
        $user = $request->user();

        $this->activityLog->log(
            action: 'view_profile',
            actionLabel: 'عرض بيانات المستخدم',
            actorId: $user->id,
            actorType: 'User',
            subjectType: 'User',
            subjectId: $user->id,
            module: 'auth'
        );
        return response()->json(
            $request->user()->load(['roles', 'permissions'])
        );
    }

    /**
     * تحديث التوكن (تجديد)
     */
    public function refresh(Request $request)
    {
        $user = $request->user();

        $user->tokens()->delete();
        $newToken = $user->createToken('api_token')->plainTextToken;

        $this->activityLog->log(
            action: 'token_refresh',
            actionLabel: 'تجديد رمز الدخول',
            actorId: $user->id,
            actorType: 'User',
            subjectType: 'User',
            subjectId: $user->id,
            module: 'auth'
        );

        return response()->json([
            'message' => 'Token refreshed',
            'token'   => $newToken,
        ]);
    }
}