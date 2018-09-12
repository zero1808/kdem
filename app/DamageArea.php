<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DamageArea extends Model
{
    //
    protected $fillable = ['number','name','name_english'];
    
    protected $table = 'damage_areas';
    
    public function damageOrder(){
        return $this->belongsTo('App\DamageOrder','idDamageArea','id');
    }
}
