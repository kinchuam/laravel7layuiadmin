<?php


namespace App\Models\Logs;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ActivityLog extends Model
{
    use DateTrait;
    protected $table = 'activity_log';
    protected $fillable = ['log_name', 'description', 'subject_id', 'subject_type', 'causer_id', 'causer_type', 'properties'];

    public function user()
    {
        return $this->hasOne('App\Models\System\User','id','causer_id');
    }

    /**
     * @param string $desc
     * @param array $properties
     * @param $model
     * @param null $causer
     * @param string $logName
     * @return bool
     */
    public static function CreateSyslog($desc = '', $properties = [], $model = null, $causer = null, $logName = 'Syslog')
    {
        $causedUser = $causer ?: Auth::guard('admin')->user();
        $arr = [
            'log_name' => $logName,
            'description' => $desc,
            'causer_id' => $causedUser->id,
            'causer_type' => get_class($causedUser),
            'properties' => collect($properties),
        ];
        if (!empty($model)) {
            $arr['subject_id'] = !empty($model[$model->primaryKey]) ? $model[$model->primaryKey] : 0;
            $arr['subject_type'] = get_class($model);
        }
        self::query()->create($arr);
        return true;
    }
}
