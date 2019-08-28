<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Product extends Model
{

    protected $fillable = ['name','height','local_price','export_price'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    public function end_product(){
        return $this->hasOne('App\EndProduct');
    }

    public function internal_movement(){
        return $this->hasOne('App\InternalMovement');
    }

    public function orders(){
        return $this->hasMany('App\Order');
    }

    public function setNameAttribute($value){
        $this->attributes['name'] = strip_tags($value);
        }
    public function setHeightAttribute($value){
        $this->attributes['height'] = strip_tags($value);
        }
    public function setLocalPriceAttribute($value){
        $this->attributes['local_price'] = strip_tags(intval($value));
        }
    public function setExportPriceAttribute($value){
        $this->attributes['export_price'] = strip_tags(intval($value));
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
