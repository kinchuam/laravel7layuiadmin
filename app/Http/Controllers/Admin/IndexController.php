<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Sites;
use Ip2Region;

class IndexController extends Controller
{

    public function layout()
    {
        return view('admin.layout');
    }

    public function index()
    {
        return view('admin.index.index');
    }

    public function GetUser()
    {
        $user = $this->guard()->user();
        $user['ip'] = GetClientIp();
        $info = (new Ip2Region())->btreeSearch($user['ip']);
        $user['ip_data'] = !empty($info['region']) ? $info['region'] : '';
        return $this->adminJson($user);
    }

    public function UserPermissions()
    {
        return $this->adminJson($this->guard()->user()->getAllPermissions()->pluck('name'));
    }

    public function Navigation()
    {
        $list = config('custom.permission_data');
        $user = $this->guard()->user();
        $newArr = [];
        if (!empty($list)) {
            foreach ($list as $k => $v) {
                if ($user->can($v['name'])) {
                    $newArr[$k] = $this->NavData($v);
                }
                if (!empty($v['child'])) {
                    foreach ($v['child'] as $kk => $vv) {
                        if ($user->can($vv['name'])) {
                            $newArr[$k]['child'][$kk] = $this->NavData($vv);;
                        }
                    }
                }
            }
        }
        return $this->adminJson($newArr);
    }

    private function NavData($data)
    {
        return [
            'name' => $data['name'],
            'display_name' => $data['display_name'],
            'url' => isset($data['route']) ? $this->CheckRoute($data['route']) : '',
            'icon' => $data['icon'] ?? '',
        ];
    }

    private function CheckRoute($route)
    {
        if (!empty($route)) {
            if ((substr($route, 0, 7) == 'http://') || (substr($route, 0, 8) == 'https://') || (substr($route, 0, 2) == '//')) {
                return $route;
            }
            return str_replace('.','/', $route);
        }
        return '';
    }

    public function WebSite()
    {
        $website = Sites::GetPluginSet('website');
        return $this->adminJson($website);
    }

    private function guard()
    {
        return auth()->guard('admin');
    }
}
