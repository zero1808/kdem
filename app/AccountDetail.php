<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AccountDetail extends Model
{
    //
    protected $fillable = ['idDealer','bank_name','clabe','account','status'];
    
    protected $table = 'account_details';
    
    public function dealers(){
        return $this->belongsTo('App\Dealer','id','idDealer');
    }
}
