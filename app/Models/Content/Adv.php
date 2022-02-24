<?php


namespace App\Models\Content;


use App\Traits\DateTrait;
use Illuminate\Database\Eloquent\Model;

class Adv extends Model
{
    use DateTrait;
    protected $table = 'advertising';
    protected $fillable = ['sort', 'name', 'thumb', 'url', 'status'];
}
