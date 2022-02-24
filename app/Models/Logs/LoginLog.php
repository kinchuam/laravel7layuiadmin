<?php


namespace App\Models\Logs;

use App\Traits\DateTrait;
use Ip2Region;
use Jenssegers\Agent\Agent;
use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    use DateTrait;
    protected  $table = 'user_login_log';
    protected $fillable = ['username', 'message', 'platform', 'browser', 'ip', 'ip_address', 'header' ];
    protected $attributes = [ 'ip_address' => '', 'header' => '' ];

    /**
     * @param $message
     * @param null $username
     */
    public static function CreateLoginLog($message, $username = null)
    {
        try {
            $agent = new Agent();
            $ip = GetClientIp();
            $info = (new Ip2Region())->btreeSearch($ip);
            self::query()->create([
                'username'      => $username ?: '',
                'message'       => trim($message),
                'platform'      => $agent->platform(),
                'browser'       => $agent->browser(),
                'ip'            => $ip,
                'ip_address'    => !empty($info['region']) ? $info['region'] : '',
                'header'        => json_encode(request()->header()),
            ]);
        }catch (\Exception $exception) {
            //
        }
    }
}
