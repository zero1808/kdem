<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DamageQuotation extends Model
{
    //
    protected $fillable = ['idDamageOrder','amount_pieces','amount_paint','amount_hand','iva','subtotal','total'];
    
    protected $hidden = ["id"];
    
    public function damageOrder(){
        return $this->belongsTo('App\DamageOrder','id','idDamageOrder');
    }
}
