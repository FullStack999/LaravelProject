<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    public $timestamps = false;

    public function users(){
        return $this->hasMany('App\User');
    }
    public function setNameAttribute($value){
        $this->attributes['name'] = strip_tags($value);
    }
}
