<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
class AuthService
{
    protected ActivityLogService $activityLog;

    public function __construct(ActivityLogService $activityLog) {
        $this->activityLog = $activityLog;
    }

    public function login (array $data) 
    {
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
            
            throw ValidationException::withMessages([
                'credentials' => 'AUTH_INVALID_CREDENTIALS',
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'status' => 'ACCOUNT_NOT_ACTIVE',
            ]);
        }

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
            'message'=> 'LOGIN_SUCCESS',
            'user'    => new UserResource($user->load([
                'roles', 
                'permissions', 
                'department', 
                'committees.manager', 
                'committees.department', 
                'parent'
            ])),
            'token'   => $token,
        ]);
    }

    /**
     * إرجاع بيانات المستخدم الحالي
     */
    public function me()
    {
        $user = auth()->user();

        if (!$user) {
            throw new AuthenticationException('USER_UN_ATHURIZED');
        }

        $user->load([
            'department',
            'parent',
            'children',
            'committees',
            'sessions',
            'managedDepartments',
            'roles',
            'permissions',  
        ]);

        return $user;
    }

    /**
     * تجديد التوكن الحالي
     */
    public function refresh(): array
    {
        $user = auth()->user();

        if (!$user) {
            throw new AuthenticationException('المستخدم غير مسجل دخول.');
        }

        // حذف التوكن القديم
        $user->currentAccessToken()?->delete();

        // إنشاء توكن جديد
        $token = $user->createToken(
            'auth_token_' . now()->format('Ymd_His'),
            ['read', 'write']
        )->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    /**
     * تسجيل الخروج من كل الجلسات
     */
    public function logout(User $user): JsonResponse
    {
        if (!$user) {
            throw new AuthenticationException('USER_UN_ATHURIZED');
        }
        $user->currentAccessToken()->delete();
        
        return response()->json([
            "success" => true,
            "message" => "USER_LOG_OUT_SUCCESS"
        ]);
    }

}