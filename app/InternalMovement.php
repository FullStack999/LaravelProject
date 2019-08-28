<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class InternalMovement extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id','product_id','amt'];

    public function product(){
        return $this->belongsTo('App\Product');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function getDateAttribute($value){
        return Carbon::parse($value)->format('d/m/Y');
    }


    public function setUserIdAttribute($value){
        $this->attributes['user_id'] = strip_tags(intval($value));
    }
    public function setProductIdAttribute($value){
        $this->attributes['product_id'] = strip_tags(intval($value));
    }
    public function setAmtAttribute($value){
        $this->attributes['amt'] = strip_tags(intval($value));
    }


    public function getCreatedAtAttribute($value){
        $monthsList = Config('months');
        $currentDate = Carbon::parse($value)->format('d-m H:i');
        $mD = date("m");
        $currentDate = str_replace('-'.$mD, ", ".$monthsList[$mD]." ", $currentDate);
        return $currentDate;
    }
    public function getUpdatedAtAttribute($value){
        $monthsList = Config('months');
        $currentDate = Carbon::parse($value)->format('d-m H:i');
        $mD = date("m");
        $currentDate = str_replace('-'.$mD, ", ".$monthsList[$mD]." ", $currentDate);
        return $currentDate;
    }
}
