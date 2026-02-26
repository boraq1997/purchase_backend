<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Services\ActivityLogService;
use App\Http\Requests\auth\LoginRequest;
use App\Services\AuthService;

class AuthController extends Controller
{

    protected ActivityLogService $activityLog;
    protected AuthService $authService;

    public function __construct(
        ActivityLogService $activityLog,
        AuthService $service
        ) {
        $this->activityLog = $activityLog;
        $this->authService = $service;
    }

    /**
     * تسجيل الدخول باستخدام اسم المستخدم وكلمة المرور
     */
    public function login(LoginRequest $request)
    {
        $data = $request->validated();
        $user = $this->authService->login($data);
        return $user;
    }

    /**
     * تسجيل الخروج
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        return $this->authService->logout($user);
    }

    /**
     * بيانات المستخدم الحالي
     */
    public function me(Request $request)
    {
        $user = $this->authService->me();

        $this->activityLog->log(
            action: 'view_profile',
            actionLabel: 'عرض بيانات المستخدم',
            actorId: $user->id,
            actorType: 'User',
            subjectType: 'User',
            subjectId: $user->id,
            module: 'auth'
        );
        return response()->json([
            "success" => true,
            "data" => new UserResource($user),
        ]);
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