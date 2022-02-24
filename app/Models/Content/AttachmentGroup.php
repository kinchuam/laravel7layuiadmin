<?php
namespace App\Models\Content;


use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Model;

class AttachmentGroup extends Model
{
    use DateTrait;
    protected $table = 'attachment_group';
    protected $fillable = ['name', 'sort'];

    public function files()
    {
        return $this->hasMany('App\Models\Content\Attachment', 'group_id', 'id');
    }
}
