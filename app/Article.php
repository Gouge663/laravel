<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Rennokki\QueryCache\Traits\QueryCacheable;
use Laravel\Sanctum\HasApiTokens;

class Article extends Model
{
    use QueryCacheable,HasApiTokens;
    protected $cacheFor = 3600;

    public function tags()
    {
        return $this->hasOne('App\Tag');
    }

    public function user()
    {
        return $this->belongsTo('App\User')->withDefault();
    }
}
