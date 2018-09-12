<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DamageSeverity extends Model
{
    //
    protected $fillable = ['number','name','name_english'];
    
    protected $table = 'damage_severities';
    
    public function damageOrder(){
        return $this->belongsTo('App\DamageOrder','idSeverity','id');
    }

}
