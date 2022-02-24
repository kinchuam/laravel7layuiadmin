<?php

namespace App\Models;

use App\Models\Logs\ActivityLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Sites extends Model
{
    protected $table = 'system_sites';
    protected $fillable = ['key', 'value'];

    /**
     * @param string $key
     * @return array
     */
    public static function GetPluginSet($key = null)
    {
        if (!empty($key)) {
            return Cache::tags(['System', 'Config'])->remember($key, Carbon::now()->addMinutes(config('custom.config_cache_time')), function () use($key) {
                $data = self::query()->where('key', $key)->first(['key', 'value']);
                return !empty($data['value']) ? json_decode($data['value'],true) : [];
            });
        }
        return [];
    }

    /**
     * @param string $key
     * @param array $values
     * @return bool
     */
    public static function UpdatePluginSet($key = null, $values = [])
    {
        if (!empty($key)) {
            $model = self::query()->updateOrCreate(["key" => $key], ["value" => json_encode($values)]);
            ActivityLog::CreateSyslog('更新配置【'.$key.'】', $values, $model);
            Cache::tags(['System', 'Config'])->put($key, $values, Carbon::now()->addMinutes(config('custom.config_cache_time')));
            return true;
        }
        return false;
    }
}
