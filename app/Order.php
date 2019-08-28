<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
      'amt',
      'price',
      'product_id',
      'orders_number_id',
    ];

    public function product(){
        return $this->belongsTo('App\Product');
    }
    public function ordersNumber(){
        return $this->belongsTo('App\OrdersNumber');
    }
    public function getDateAttribute($value){
        return Carbon::parse($value)->format('d/m/Y');
    }


    public function setAmtAttribute($value){
        $this->attributes['amt'] = strip_tags(intval($value));
    }
    public function setPriceAttribute($value){
        $this->attributes['price'] = strip_tags(intval($value));
    }
    public function setProductIdAttribute($value){
        $this->attributes['product_id'] = strip_tags(intval($value));
    }
    public function setOrdersNumberIdAttribute($value){
        $this->attributes['orders_number_id'] = strip_tags(intval($value));
    }

}
