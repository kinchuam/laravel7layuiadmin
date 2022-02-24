<?php

namespace App\Http\Controllers\Admin\System;

use App\Http\Controllers\Controller;
use App\Models\Logs\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CacheController extends Controller
{

    public function index()
    {
        return view('admin.system.clear_cache');
    }

    public function clearCache(Request $request)
    {
        $str = [];
        if ($request->get('cache')) {
            Artisan::call('cache:clear');
            $str[] = '数据缓存';
        }
        if ($request->get('picture')) {
            $str[] = '图片缓存';
        }
        if ($request->get('view')) {
            Artisan::call('view:clear');
            $str[] = '视图缓存';
        }
        if ($request->get('route')) {
            Artisan::call('route:cache');
            $str[] = '路由缓存';
        }
        if ($request->get('config')) {
            Artisan::call('config:cache');
            $str[] = '配置缓存';
        }
        if (empty($str)) {
            return $this->adminJson([],1, '请选择选清除项');
        }
        ActivityLog::CreateSyslog('清除 '.implode('、', $str), []);
        return $this->adminJson([],0,'清除成功');
    }
}