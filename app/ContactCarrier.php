<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ContactCarrier extends Model
{
    //
    protected $fillable = ['name','name'];
    
    protected $table = 'contact_carriers';
    
    public function user(){
        return $this->belongsTo('App\User','id','idUser');
    }
    
    public function carrier(){
        return $this->belongsTo('App\Carrier','id','idCarrier');
    }
    
}
