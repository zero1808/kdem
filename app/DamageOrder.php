<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DamageOrder extends Model
{
    //
    protected $fillable = ['idOrder','idDamageArea','idDamage','idSeverity'];
    
    protected $table = 'damage_orders';
    
    public function orders(){
        return $this->belogsTo('App\Order','id','idOrder');
    }
    
    public function damageArea(){
        return $this->hasOne('App\DamageArea','id','idDamageArea');
    }
    
    public function damage(){
        return $this->hasOne('App\Damage','id','idDamage');
    }
    
    public function damageSeverity(){
        return $this->hasOne('App\DamageSeverity','id','idSeverity');
    }
    
    public function damageQuotation(){
        return $this->hasOne('App\DamageQuotation','idDamageOrder','id');
    }
    
}
