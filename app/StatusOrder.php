<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusOrder extends Model
{
    //
    protected $fillable = ['name'];
    
    protected $table = 'status_orders';
    
    public function order(){
        return $this->belongsTo('App\Order','idStatus','id');
    }
}
