<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    //
    protected $fillable = ['commercial_name','rfc','business_name','idUsuario','status'];
    
    protected $table = 'dealers';
    
    public function user(){
        return $this->belongsTo('App\User','id','idUsuario');
    }
    
    public function accountDetails(){
        return $this->hasMany('App\AccountDetail','idDealer','id');
    }
    
    public function orders(){
        return $this->hasmany('App\Order','idDealer','id');
    }
    
    public function order(){
        return $this->belongsTo('App\Order','idDealer','id');
    }
    
    
    public function orderComment(){
        return $this->belongsTo('App\OrderComment','idDealer','id');
    }
    
    public function satBill(){
        return $this->belongsTo('App\SatBill','idDealer','id');
    }
    
    public function userInfo(){
        return $this->hasOne('App\User','id','idUsuario');
    }
}
