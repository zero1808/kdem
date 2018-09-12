<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    //
    protected $fillable = ['name','year','code','status'];
    
    protected $table = 'car_models';
    
    public function order(){
        return $this->belogsTo('App\Order','idModelo','id');
    }
}
