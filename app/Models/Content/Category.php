<?php


namespace App\Models\Content;


use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use DateTrait;
    protected $table = 'article_category';
    protected $fillable = ['sort', 'name', 'status'];

    public function articles()
    {
        return $this->hasMany('App\Models\Content\Article','category_id','id');
    }
}
