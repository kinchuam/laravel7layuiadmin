<?php

namespace App\Http\Controllers;

use App\Traits\CommonTrait;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class ApiController extends BaseController
{
    use DispatchesJobs, ValidatesRequests, CommonTrait;

}
