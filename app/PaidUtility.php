<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PaidUtility extends Model
{
    //
    public  $table = 'paid_utilities';

    public function expenseHistory(){
        return $this->belongsTo('App\Expense');
    }

    public function setMonthIdAttribute($value){
        $this->attributes['month_id'] = strip_tags(intval($value));
    }
    public function setExpenseIdAttribute($value){
        $this->attributes['expense_id'] = strip_tags(intval($value));
    }
    public function setAmtAttribute($value){
        $this->attributes['amt'] = strip_tags(intval($value));
    }
    public function setBalanceAttribute($value){
        $this->attributes['balance'] = strip_tags(intval($value));
    }

    public function getDateAttribute($value){
        $monthsList = Config('months');
        $currentDate = Carbon::parse($value)->format('d-m H:i');
        $mD = date("m");
        $currentDate = str_replace('-'.$mD, ", ".$monthsList[$mD]." ", $currentDate);
        return $currentDate;
    }

}
