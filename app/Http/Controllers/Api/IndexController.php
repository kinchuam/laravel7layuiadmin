<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\ApiController;

class IndexController extends ApiController
{
    public function index()
    {
        return $this->appJson();
    }
}
