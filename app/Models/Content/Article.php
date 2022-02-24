<?php

namespace App\Models\Content;

use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use DateTrait;
    protected $table = 'articles';
    protected $fillable = ['sort', 'category_id', 'title', 'thumb', 'desc', 'url', 'content', 'view_count', 'status'];

    public function category()
    {
        return $this->hasOne('App\Models\Content\Category','id','category_id');
    }
}
