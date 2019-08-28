<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;


class OrdersNumber extends Model
{

    protected $fillable = ['exit_ware_status', 'confirmed', 'client_id', 'back_fill', 'not_enough'];

    protected $table = 'orders_number';

    public function order(){
        return $this->hasMany('App\Order');
    }
    public function endProduct(){
        return $this->hasMany('App\EndProduct');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function client(){
        return $this->belongsTo('App\Client');
    }


    public function setConfirmedAttribute($value){
        $this->attributes['confirmed'] = strip_tags(intval($value));
    }
    public function setExitWareStatusAttribute($value){
        $this->attributes['exit_ware_status'] = strip_tags(intval($value));
    }

    public function getDateAttribute($value){
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
