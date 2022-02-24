<?php


namespace App\Http\Controllers\Admin\Logs;


use App\Http\Controllers\Controller;
use App\Models\Logs\LoginLog;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class LoginLogController extends Controller
{
    public function index()
    {
        return view('admin.logs.login_log.index');
    }

    public function data(Request $request)
    {
        $query = LoginLog::query()->select(['id', 'username', 'message', 'platform', 'browser', 'ip', 'ip_address', 'created_at']);
        if ($username = $request->get('username')){
            $query->where('username', trim($username));
        }
        if ($ip = $request->get('ip')){
            $query->where('ip','like',trim($ip).'%');
        }
        $res = $query->orderBy('id','desc')->paginate($request->get('limit', 10))->toArray();
        $list = $res['data'];
        if (!empty($list)) {
            $agent = new Agent();
            foreach ($list as $ke => $row) {
                if (!empty($row['platform'])) {
                    $list[$ke]['platform_version'] = $agent->version($row['platform']);
                }
                if (!empty($row['browser'])) {
                    $list[$ke]['browser_version'] = $agent->version($row['browser']);
                }
                $row['platform'] = empty($row['platform'])?'unknown':$row['platform'];
                $row['browser'] = empty($row['browser'])?'unknown':$row['browser'];
            }
        }
        return $this->adminJson([
            'count' => $res['total'],
            'data' => $list
        ]);
    }

}