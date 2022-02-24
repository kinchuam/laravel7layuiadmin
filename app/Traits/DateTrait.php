<?php


namespace App\Traits;


use DateTimeInterface;

trait DateTrait
{
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}
