<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'username',
        'password',
        'department_id',
        'parent_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /*
    |--------------------------------------------------------------------------
    | العلاقات (Relationships)
    |--------------------------------------------------------------------------
    */

    // القسم الذي ينتمي له المستخدم
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    // المشرف المباشر (المستخدم الأب)
    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    // المستخدمون التابعون له
    public function children()
    {
        return $this->hasMany(User::class, 'parent_id');
    }

    // اللجان التي ينتمي لها المستخدم
    public function committees()
    {
        return $this->belongsToMany(Committee::class, 'committee_user')
                    ->withTimestamps();
    }

    // الجلسات المرتبطة بالمستخدم
    public function sessions()
    {
        return $this->hasMany(Session::class);
    }

    // الأقسام التي يكون هذا المستخدم مديرًا لها
    public function managedDepartments()
    {
        return $this->hasOne(Department::class, 'manager_user_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    // لتصفية المستخدمين حسب الدور
    public function scopeRole($query, $roleName = null)
    {
        if ($roleName) {
            return $query->whereHas('roles', function ($q) use ($roleName) {
                $q->where('name', $roleName);
            });
        }
        return $query;
    }
}