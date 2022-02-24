<?php

namespace App\Http\Controllers\Admin\Logs;

use App\Models\Logs\AccessLog;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Jenssegers\Agent\Agent;

class AccessLogController extends Controller
{
    public function index()
    {
        $methods = AccessLog::$methods;
        return view('admin.logs.access.index', compact('methods'));
    }

    public function data(Request $request)
    {
        $model = AccessLog::query()->select(['id', 'path', 'method', 'ip', 'ip_address', 'platform', 'browser', 'created_at']);
        if ($path = trim($request->get('path'))) {
            $model->whereRaw("( LOCATE('".escapeLike($path)."', `path`) > 0 )");
        }
        if ($method = trim($request->get('method'))) {
            $model->where('method', $method);
        }
        if ($ip = trim($request->get('ip'))) {
            $model->whereRaw("( LOCATE('".escapeLike($ip)."', `ip`) > 0 )");
        }
        $res = $model->orderBy('id','desc')->paginate($request->get('limit',10))->toArray();
        $methodColors = AccessLog::$methodColors;
        $list = $res['data'];
        if (!empty($list)) {
            $agent = new Agent();
            foreach ($list as $ke => $row) {
                if (!empty($row['browser'])) {
                    $list[$ke]['platform_version'] = $agent->version($row['platform']);
                }
                if (!empty($row['platform'])) {
                    $list[$ke]['browser_version'] = $agent->version($row['browser']);
                }
                $list[$ke]['platform'] = empty($row['platform'])?'unknown':$row['platform'];
                $list[$ke]['browser'] = empty($row['browser'])?'unknown':$row['browser'];
                $list[$ke]['method_color'] = $methodColors[$row['method']] ?? 'blue';
            }
        }
        return $this->adminJson([
            'count' => $res['total'],
            'data'  => $list
        ]);
    }

    public function show($id, Request $request)
    {
        if ($request->ajax()) {
            $item = AccessLog::query()->findOrFail($id);
            if (!empty($item['agent'])) {
                $agent = new Agent();
                if (!empty($row['browser'])) {
                    $item["platform_version"] = $agent->version($item['platform']);
                }
                if (!empty($row['platform'])) {
                    $item["browser_version"] = $agent->version($item['browser']);
                }
                $item['platform'] = empty($item['platform'])?'unknown':$item['platform'];
                $item['browser'] = empty($item['browser'])?'unknown':$item['browser'];
                $item["device_name"] = $agent->device();
                $item["is_robot"] = $agent->isRobot();
                $item["robot_name"] = $agent->robot();
                $item["languages"] = implode('、',$agent->languages());
            }
            return $this->adminJson($item);
        }
        return view('admin.logs.access.show');
    }
}
