<?php
namespace App\Models\Content;

use App\Models\Logs\ActivityLog;
use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use SoftDeletes, DateTrait;
    protected $table = 'attachment';
    protected $fillable = ['group_id', 'filename', 'path', 'suffix', 'type', 'storage', 'size', 'uuid'];

    public static $image_size = 2048;
    public static $image_type = ["jpg", "jpeg", "png", "gif", "webp", "avif"];
    public static $file_size = 5120;
    public static $file_type = ['mp3', 'mp4', 'mov'];

    public function group()
    {
        return $this->belongsTo('App\Models\Content\AttachmentGroup');
    }

    /**
     * @param array $fileInfo
     * @param false $is_plural
     * @return bool|Model
     */
    public static function CreateUploadFile($fileInfo = [], $is_plural = false)
    {
        if (!empty($fileInfo)) {
            if ($is_plural) {
                $fileInfo = is_array($fileInfo) ? $fileInfo : [$fileInfo];
                foreach ($fileInfo as $ke => $row) {
                    $fileInfo[$ke]['created_at'] = date('Y-m-d H:i:s');
                    $fileInfo[$ke]['updated_at'] = date('Y-m-d H:i:s');
                }
                $model = self::query()->insert($fileInfo);
                return ActivityLog::CreateSyslog('批量添加附件', $fileInfo, $model);
            }
            $model = self::query()->create($fileInfo);
            ActivityLog::CreateSyslog('添加附件', $fileInfo, $model);
            return $model;
        }
        return false;
    }
}
