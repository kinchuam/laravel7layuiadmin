<?php

namespace App\Models\System;


use App\Traits\DateTrait;

class Permission extends \Spatie\Permission\Models\Permission
{
    use DateTrait;
    protected $guard_name = 'admin';

    public function children()
    {
        return $this->hasMany(self::class,'parent_id','id');
    }
}
