<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Good extends Model
{
    protected $fillable = ['name', 'unit', 'price', 'price_id', 'subdivision_id'];

    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public function warehouse(){
        return $this->belongsTo('App\Warehouse');
    }
    public function paidGood(){
        return $this->hasMany('App\PaidGood');
    }
    public function subdivision(){
        return $this->belongsTo('App\Subdivision');
    }
    public function supplier(){
        return $this->belongsToMany('App\Supplier');
    }
    public function place(){
        return $this->belongsTo('App\Place');
    }


    public function setNameAttribute($value){
        $this->attributes['name'] = strip_tags($value);
    }
    public function setUnitAttribute($value){
        $this->attributes['unit'] = strip_tags($value);
    }
    public function setPriceAttribute($value){
        $this->attributes['price'] = strip_tags(intval($value));
    }
    public function setPlaceIdAttribute($value){
        $this->attributes['place_id'] = strip_tags(intval($value));
    }
    public function setSupplierIdAttribute($value){
        $this->attributes['supplier_id'] = strip_tags(intval($value));
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
