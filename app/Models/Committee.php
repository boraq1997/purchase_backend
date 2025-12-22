<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    protected $fillable = [
        'name',
        'department_id',
        'description',
        'manager_user_id',
    ];

    public function department() {
        return $this->belongsTo(Department::class);
    }

    public function users() {
        return $this->belongsToMany(User::class, 'committee_user');
    }

    public function manager() {
        return $this->belongsTo(User::class, 'manager_user_id');
    }
}