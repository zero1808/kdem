<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderComment extends Model
{
    //
    protected $fillable = ['comment','status','idDealer','idOrder'];
    
    protected $table = 'order_comments';
    
    public function order(){
        return $this->belongsTo('App\Order','id','idOrder');
    }
    
    public function dealer(){
        return $this->hasOne('App\Dealer','id','idDealer');
    }
    
}
