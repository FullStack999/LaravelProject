<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Supplier extends Model
{


    protected $fillable = ['name'];
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    public $timestamps = false;


    public static function boot() {
        parent::boot();
        static::deleted(function($table) {
            $table->good()->delete();
        });
    }
    public function good(){
        return $this->belongsToMany('App\Good');
    }

    public function getDeletedAtAttribute($value){
        return Carbon::parse($value)->format('d/m/Y');
    }


}
