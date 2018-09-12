<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Damage extends Model
{
    //
    protected $fillable = ['number','name','name_english'];
    
    protected $table = 'damages';
    
    public function damageOrder(){
        return $this->belongsTo('App\DamageOrder','idDamage','id');
    }
}
