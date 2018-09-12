<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClaimPic extends Model
{
    //
    protected $fillable = ['src_pic','size','idOrder'];
    
    protected $table = 'claim_pics';
    
    
    public function order(){
        return $this->belongsTo('App\Order','id','idOrder');
    }
}
