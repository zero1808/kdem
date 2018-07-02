<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SatBill extends Model
{
    //
    protected $fillable = ['idOrder','billing_date','folio','idDealer','total_amount','idStatusBill','pay_date','bank','import_mxn'];
    
    protected $table = 'sat_bills';
    
    public function orders(){
        return $this->belongsTo('App\Order','id','idOrder');
    }
    
    public function dealer(){
        return $this->hasOne('App\Dealer','id','idDealer');
    }
    
    public function statusBill(){
        return $this->hasOne('App\StatusBill','id','idStatusBill');
    }
}
