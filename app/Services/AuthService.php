<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * تسجيل الدخول باستخدام اسم المستخدم وكلمة المرور
     */
    public function login(string $username, string $password): array
    {
        $user = User::where('username', $username)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'credentials' => 'بيانات الدخول غير صحيحة.',
            ]);
        }

        if ($user->status !== 'active') {
            throw ValidationException::withMessages([
                'status' => 'الحساب غير مفعل.',
            ]);
        }

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
     * إرجاع بيانات المستخدم الحالي
     */
    public function me(): User
    {
        $user = auth()->user();

        if (!$user) {
            throw new AuthenticationException('المستخدم غير مسجل دخول.');
        }

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
    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }
}