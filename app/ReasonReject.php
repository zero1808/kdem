<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReasonReject extends Model
{
    //
    protected $fillable = ['code','description'];
    
    protected $table = 'reason_rejects';
    
    public function order(){
        return $this->belongsTo('App\Order','idReasonReject','id');
    }
    
    
}
