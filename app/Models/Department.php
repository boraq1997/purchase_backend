<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use voku\helper\ASCII;

class Department extends Model
{
    protected $fillable = [
        'name',
        'manager_user_id',
        'description',
    ];

    protected static function booted() {
        static::saving(function($department) {
            $newCode = self::generateUniqueCode($department);
            if ($department->code !== $newCode) {
                $department->code = $newCode;
            }
        });
    }

    private static function generateUniqueCode($department) {
        $asciiName = ASCII::to_ascii($department->name);
        $words = array_filter(explode(' ', $asciiName));

        if (count($words) > 3) {
            $baseCode = '';
            foreach($words as $word) {
                $baseCode .= strtoupper(substr($word, 0, 1));
            }
        } else {
            $baseCode = strtoupper(Str::slug($asciiName));
        }

        if (empty($baseCode)) {
            $baseCode = 'DEP';
        }

        $code = $baseCode;
        $counter = 1;

        while(
            self::where('code', $code)
                ->where('id', '!=', $department->id)
                ->exists()
        ) {
            $code = $baseCode . $counter;
            $counter++;
        }
        return $code;
    }

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