<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    // public function topic()
    // {
    //     return $this->hasMany('App\Models\topic');
    // }

    public function discussions()
    {
        return $this->hasMany('App\Models\Discussion');
    }
}
