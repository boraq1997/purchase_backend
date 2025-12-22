<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $fillable = [
        'name',
        'username',
        'code',
        'manager_user_id',
        'description',
    ];

    public function users() {
        return $this->hasMany(User::class);
    }

    public function manager() {
        return $this->belongsTo(User::class, 'manager_user_id');
    }

    public function committees() {
        return $this->hasMany(Committee::class);
    }
}