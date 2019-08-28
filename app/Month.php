<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Month extends Model
{
    //
    protected $fillable = ['name'];
    public $timestamps = false;
    protected $table = 'months';

}
