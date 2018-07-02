<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    //
    
    protected $fillable = ['name'];
    
    protected $table = 'carriers';
    
    public function order(){
        return $this->belongsTo('App\Order','idCarrier','id');
    }
}
