<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Expense extends Model
{
    protected $fillable = ['name','unit'];
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    public $timestamps = false;

    public function paidUtility(){
        return $this->hasMany('App\PaidUtility');
    }

    public function setNameAttribute($value){
        $this->attributes['name'] = strip_tags($value);
    }
    public function setUnitAttribute($value){
        $this->attributes['unit'] = strip_tags($value);
    }

    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format('d/m/Y');
    }
    public function getUpdatedAtAttribute($value){
        return Carbon::parse($value)->format('d/m/Y');
    }
    public function getDeletedAtAttribute($value){
        return Carbon::parse($value)->format('d/m/Y');
    }
}
