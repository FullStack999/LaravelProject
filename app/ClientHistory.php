<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ClientHistory extends Model
{

    protected $fillable = [
        'client_id','debt','bucket', 'lid'
    ];

    public function setClientIdAttribute($value){
        $this->attributes['client_id'] = strip_tags(intval($value));
    }
    public function setDebtAttribute($value){
        $this->attributes['debt'] = strip_tags(intval($value));
    }
    public function setBucketAttribute($value){
        $this->attributes['bucket'] = strip_tags(intval($value));
    }
    public function setLidAttribute($value){
        $this->attributes['lid'] = strip_tags(intval($value));
    }

    public function getClientIdAttribute($value){
        return intval($value);
    }
    public function getDebtAttribute($value){
        return intval($value);
    }
    public function getBucketAttribute($value){
        return intval($value);
    }
    public function getLidAttribute($value){
        return intval($value);
    }
    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format('d/m/Y');
    }
    public function getUpdatedAtAttribute($value){
        return Carbon::parse($value)->format('d/m/Y');
    }

    public function client(){
        return $this->belongsTo('App\Client');
    }
    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->format('d/m/Y');
    }
}
