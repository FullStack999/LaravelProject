<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use App\Notifications\MailResetPasswordToken;

class User extends Authenticatable
{
    use Notifiable;


    protected $fillable = ['name','last_name', 'email', 'login', 'password','type_id'];

    use SoftDeletes;
    protected $dates = ['deleted_at'];

    protected $hidden = ['remember_token'];

    public function setNameAttribute($value){
        $this->attributes['name'] = strip_tags($value);
    }
    public function setLastNameAttribute($value){
        $this->attributes['last_name'] = strip_tags($value);
    }
    public function setLoginAttribute($value){
        $this->attributes['login'] = strip_tags($value);
    }
    public function getLastNameAttribute($value){
        return strip_tags($value);
    }
    public function getLoginAttribute($value){
        return strip_tags($value);
    }
    public function getNameAttribute($value){
        return strip_tags($value);
    }
    public function getTypeIdAttribute($value){
        return strip_tags($value);
    }
    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format('d/m/Y');
    }
    public function getUpdatedAtAttribute($value){
        return Carbon::parse($value)->format('d/m/Y');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type(){
        return $this->belongsTo('App\Type');
    }


    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordToken($token));
    }


    public function endProduct(){
        return $this->hasMany('App\EndProduct');
    }
}
