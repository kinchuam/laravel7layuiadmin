<?php

namespace App\Models\System;



use App\Traits\DateTrait;

class Role extends \Spatie\Permission\Models\Role
{
    use DateTrait;
    protected $guard_name = 'admin';
}
