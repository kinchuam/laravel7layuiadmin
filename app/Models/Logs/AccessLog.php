<?php

namespace App\Models\Logs;

use App\Traits\DateTrait;
use Ip2Region;
use Jenssegers\Agent\Agent;
use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    use DateTrait;
    protected $table = 'access_log';
    protected $fillable = ['path', 'method', 'input', 'ip', 'ip_address', 'platform', 'browser', 'header' ];
    protected $attributes = [ 'ip_address' => '', 'header' => '' ];

    public static $methodColors = [
        'GET'     => '#43d543',
        'POST'    => '#75751c',
        'PUT'     => 'blue',
        'DELETE'  => 'red',
        'OPTIONS' => 'hotpink',
        'PATCH'   => 'thistle',
        'LINK'    => 'mintcream',
        'UNLINK'  => 'firebrick',
        'COPY'    => 'lightcyan',
        'HEAD'    => 'gray',
        'PURGE'   => 'copper',
    ];
    public static $methods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'PATCH', 'LINK', 'UNLINK', 'COPY', 'HEAD', 'PURGE'];

    /**
     * @param $request
     */
    public static function CreateAccessLog($request)
    {
        try {
            $agent = new Agent();
            $ip = GetClientIp();
            $info = (new Ip2Region())->btreeSearch($ip);
            self::query()->create([
                "path"          => substr($request->path(), 0, 255),
                "method"        => $request->method(),
                "input"         => json_encode($request->input()),
                "ip"            => $ip,
                "ip_address"    => !empty($info['region'])?$info['region']:'',
                "platform"      => $agent->platform(),
                "browser"       => $agent->browser(),
                "header"        => json_encode($request->header()),
            ]);
        } catch (\Exception $exception) {
            //
        }
    }
}
