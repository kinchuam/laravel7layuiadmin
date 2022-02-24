<?php

namespace App\Traits;

use App\Library\Response as LibraryResponse;
use App\Models\System\Permission as SystemPermission;

trait CommonTrait
{
    /**
     * @param $keys
     */
    protected function ClearRedisCache($keys)
    {
        $keys = !is_array($keys) ? [trim($keys)] : $keys;
        if (strtolower(config('cache.default')) == 'redis' && $redis = app('redis')->connection('cache')) {
            foreach ($keys as $key) {
                if (!starts_with($key, config('cache.prefix').':')) {
                    $key = config('cache.prefix').':'.$key.'*';
                }
                if ($ks = $redis->keys($key)) {
                    $p = config('database.redis.options.prefix');
                    foreach ($ks as $k => $v) {
                        $ks[$k] = str_replace($p, '', $v);
                    }
                    $redis->del($ks);
                }
            }
        }
    }

    protected function GetPerId($id, $arr)
    {
        if (!empty($id)) {
            $arr = collect($arr)->pluck('parent_id','id');
            if (!isset($arr[$id])) {
                return [];
            }
            $pid = [];
            while ($arr[$id]) {
                $id = $arr[$id];
                $pid[] = intval($id);
            }
            return $pid;
        }
        return [];
    }

    protected function adminJson($data = [], $code = 0, $message = 'ok')
    {
        return response()->json(LibraryResponse::JsonData($data, $code, $message));
    }

    protected function appJson($data = [], $code = 0, $message = 'ok', $headers = [])
    {
        return response()->json(LibraryResponse::JsonData($data, $code, $message), 200, $headers);
    }

    protected function GetPermissions()
    {
        return listToTree(SystemPermission::query()->get(['id', 'name', 'display_name', 'parent_id', 'parent_id'])->toArray());
    }

}
