<?php


namespace App\Providers;


use App\Models\System\User;
use Carbon\Carbon;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Cache;

class CacheUserProvider extends EloquentUserProvider
{
    /**
     * CacheUserProvider constructor.
     * @param HasherContract $hasher
     */
    public function __construct(HasherContract $hasher)
    {
        parent::__construct($hasher, User::class);
    }
    /**
     * @param mixed $identifier
     * @return false
     */
    public function retrieveById($identifier)
    {
        return $this->GetUser($identifier);
    }

    public function retrieveByToken($identifier, $token)
    {
        if ($model = $this->GetUser($identifier)) {
            $rememberToken = $model->getRememberToken();
            return $rememberToken && hash_equals($rememberToken, $token) ? $model : null;
        }
        return null;
    }

    private function GetUser($id) {
        if (is_numeric($id) && $id >= 0) {
            return Cache::tags(['System', 'CachedUser'])->remember($id, Carbon::now()->addMinutes(config('custom.config_cache_time')), function() use($id) {
                return User::query()->find($id, ['id', 'username', 'display_name', 'password', 'uuid', 'remember_token', 'created_at', 'updated_at']);
            });
        }
        return null;
    }
}
