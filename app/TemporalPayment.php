<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TemporalPayment extends Model
{
    //
    protected $fillable = ['idOrder','amount_gmx','pay_date_gmx','smx_claim_number','amount_smx','pay_date_smx'];
    
    protected $table = 'temporal_payments';
    

    public function order(){
        return $this->belongsTo('App\Order','id','idOrder');
    }
    
}
