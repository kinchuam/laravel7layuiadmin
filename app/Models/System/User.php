<?php

namespace App\Models\System;

use App\Traits\DateTrait;
use Illuminate\Foundation\Auth\User as Authenticates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticates
{
    use Notifiable, HasRoles, DateTrait;
    protected $table = 'users';
    protected $guard_name = 'admin';
    protected $fillable = ['username', 'display_name', 'password', 'uuid'];
    protected $hidden = ['password', 'remember_token'];

    public static function clearData($userId = null)
    {
        return $userId ? Cache::tags(['System', 'CachedUser'])->forget($userId) :  Cache::tags('CachedUser')->flush();
    }
}
