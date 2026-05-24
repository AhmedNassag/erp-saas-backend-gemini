<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    // 💡 السطر السحري: يجبر الموديل يدور جوه داتا بيز الـ Tenant اللي الميدل وير مشغلها حالياً
    protected $connection = 'tenant'; 

    protected $fillable = [
        'name', 'email', 'password', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];
}