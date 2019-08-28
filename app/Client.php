<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Client extends Model
{

    protected $fillable = ['name','address','phone','status'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];


    public function ordersNumber(){
        return $this->hasMany('App\OrdersNumber');
    }
    public function clientHistory(){
        return $this->hasMany('App\ClientHistory');
    }
    public function warehouse(){
        return $this->hasMany('App\Warehouse');
    }

    public function setNameAttribute($value){
        $this->attributes['name'] = strip_tags($value);
    }
    public function setAddressAttribute($value){
        $this->attributes['address'] = strip_tags($value);
    }
    public function setPhoneAttribute($value){
        $this->attributes['phone'] = strip_tags($value);
    }
    public function setStatusAttribute($value){
        $this->attributes['status'] = strip_tags(intval($value));
    }
    public function getStatusAttribute($value){
        return intval($value);
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
