<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Landlord\Models\PersonalAccessToken;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, Notifiable;

    public function tokens(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(PersonalAccessToken::class, 'tokenable');
    }

    // 💡 السطر السحري: يجبر الموديل يدور جوه داتا بيز الـ Tenant اللي الميدل وير مشغلها حالياً
    protected $connection = 'tenant'; 

    protected $guard_name = 'tenant'; 

    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}