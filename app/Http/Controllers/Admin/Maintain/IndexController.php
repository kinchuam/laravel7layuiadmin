<?php


namespace App\Http\Controllers\Admin\Maintain;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function optimize(Request $request)
    {
        if ($request->ajax()) {
            $json = json_decode(file_get_contents(base_path('composer.json')), true);
            $envs = [
                ['name' => 'PHP version', 'value' => 'PHP/'.PHP_VERSION ],
                ['name' => 'PHP memory', 'color' => '#ff5661', 'value' => '内存总量：'.byteCount(memory_get_peak_usage())],
                ['name' => 'Laravel version', 'value' => app()->version()],
                ['name' => 'CGI', 'value' => php_sapi_name()],
                ['name' => 'Uname', 'value' => php_uname()],
                ['name' => 'Server', 'value' => $_SERVER['SERVER_SOFTWARE']],

                ['name' => 'Cache driver', 'value' => config('cache.default') , 'extra' => ''],
                ['name' => 'Session driver', 'value' => config('session.driver')],
                ['name' => 'Queue driver', 'value' => config('queue.default')],

                ['name' => 'Timezone', 'value' => config('app.timezone')],
                ['name' => 'Locale', 'value' => config('app.locale')],
                ['name' => 'Env', 'value' => config('app.env')],
                ['name' => 'URL', 'value' => config('app.url')],
            ];

            if (strtolower(config('cache.default')) == 'redis') {
                if ($memory = app('redis')->info()) {
                    $envs[] = ['name' => 'Redis memory', 'color' => '#ff5661', 'value' => '消耗峰值：' . byteCount($memory['used_memory_peak']) . '/ 内存总量：' . byteCount($memory['used_memory'])];
                }
            }
            return $this->adminJson([
                'dependencies' => $json['require'],
                'envs' => $envs,
            ]);
        }
        return view('admin.maintain.optimize');
    }
}
