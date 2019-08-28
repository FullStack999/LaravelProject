<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'good_id',
        'place_id',
        'supplier_id',
        'amt',
        'balance',
        'paid',
    ];



    public function good(){
        return $this->belongsTo('App\Good');
    }
    public function place(){
        return $this->belongsTo('App\Place');
    }
    public function client(){
        return $this->belongsTo('App\Client');
    }


    public function setGoodIdAttribute($value){
        $this->attributes['good_id'] = strip_tags($value);
    }
    public function setPlaceIdAttribute($value){
        $this->attributes['place_id'] = strip_tags($value);
    }
    public function setAmtAttribute($value){
        $this->attributes['amt'] = strip_tags($value);
    }
    public function setBalanceAttribute($value){
        $this->attributes['balance'] = strip_tags($value);
    }


    public function getDateOfCreatedAttribute($value){
        $monthsList = Config('months');
        $currentDate = Carbon::parse($value)->format('d-m H:i');
        $mD = date("m");
        $currentDate = str_replace('-'.$mD, ", ".$monthsList[$mD]." ", $currentDate);
        return $currentDate;
    }


    public function getDateOfUpdatedAttribute($value){
        $monthsList = Config('months');
        $currentDate = Carbon::parse($value)->format('d-m H:i');
        $mD = date("m");
        $currentDate = str_replace('-'.$mD, ", ".$monthsList[$mD]." ", $currentDate);
        return $currentDate;
    }
}
