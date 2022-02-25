<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Logs;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{
    public function LineChart()
    {
        return $this->adminJson([
            'platform' => $this->GetPlatform(6),
            'browser' => $this->GetBrowser(),
        ]);
    }

    public function GetCount()
    {
        return $this->adminJson([
            'shortcut' => [
                [
                    ['title' => '管理员', 'url' => 'admin/system/user', 'icon' => 'layui-icon-user'],
                    ['title' => '角色管理', 'url' => 'admin/system/role', 'icon' => 'layui-icon-group'],
                    ['title' => '日志管理', 'url' => 'admin/logs/operation', 'icon' => 'layui-icon-log'],
                    ['title' => '上传设置', 'url' => 'admin/config/attachment', 'icon' => 'layui-icon-set-fill'],
                ],
            ],
            'data_counts' =>[
                [
                    ['title' => '日志数', 'url' => '', 'count'=>  Logs\AccessLog::query()->count()],
                ],
            ],
            'widget_config' => [
                ['Laravel', 'Web', '操作系统'],
                [app()->version(), $_SERVER['SERVER_SOFTWARE'], PHP_OS ],
                ['上传限制', 'PHP', 'Mysql'],
                [ini_get('upload_max_filesize'), phpversion(), $this->MysqlVersion()],
                ['GD', 'PDO', 'CURL'],
                [extension_loaded('gd') ? 'YES' : 'NO', class_exists('pdo') ? 'YES' : 'NO', extension_loaded('curl') ? 'YES' : 'NO'],
            ]
        ]);
    }

    private function GetPlatform($days = 5)
    {
        $start = date('Y-m-d', strtotime("-".$days." day")).' 00:00:00';
        $end = date('Y-m-d').' 23:00:00';
        $pvs = Logs\AccessLog::query()->whereBetween('created_at',  [$start, $end])->select(DB::raw('SQL_CACHE DATE_FORMAT(`created_at`, "%Y-%m-%d") as date_t, COUNT(*) as total'))
            ->groupBy(['date_t'])->get()->toArray();
        $subQuery = Logs\AccessLog::query()->whereBetween('created_at',  [$start, $end])->select(DB::raw('DATE_FORMAT(`created_at`, "%Y-%m-%d") as date_t, COUNT(*) as total'))
            ->groupBy(['date_t','ip']);
        $uvs = DB::table(DB::raw("({$subQuery->toSql()}) as sub"))->mergeBindings($subQuery->getQuery())
            ->select(DB::raw('SQL_CACHE date_t , COUNT(*) as total'))->groupBy('date_t')->get()->toArray();
        $i = $days;$transaction = [];
        while (0 <= $i) {
            $key = date('Y-m-d', time() - $i * 3600 * 24);
            $transaction['pv'][$key] = 0;
            $transaction['uv'][$key] = 0;
            --$i;
        }
        if (!empty($pvs)) {
            foreach ($pvs as $v) {
                $transaction['pv'][$v['date_t']] = $v['total'];
            }
        }
        if (!empty($uvs)) {
            foreach ($uvs as $v) {
                $transaction['uv'][$v->date_t] = $v->total;
            }
        }
        return [
            'title' => '访问量统计',
            'keys' => array_keys($transaction['pv']),
            'pv' => array_values($transaction['pv']),
            'uv' => array_values($transaction['uv'])
        ];
    }

    private function GetBrowser()
    {
        $browsers = ['Chrome', 'Edge', 'Firefox', 'Safari', 'WeChat', 'UCBrowser'];
        $list  =  Logs\AccessLog::query()->whereIn('browser', $browsers)->select('browser', DB::raw('COUNT(`id`) as count'))
            ->groupBy(['browser'])->pluck('count', 'browser')->toArray();
        $data = [];
        foreach ($browsers as $row) {
            $data[] = [
                'name' => $row,
                'value' => $list[$row] ?? 0,
            ];
        }
        return [
            'title' => '浏览器分布图',
            'keys' => $browsers,
            'data' => $data
        ];
    }

    private function MysqlVersion()
    {
        $sql = DB::select('SELECT VERSION() as VERSION;');
        return $sql[0]->VERSION;
    }
}
