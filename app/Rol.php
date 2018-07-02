<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    //
    protected $fillable = ['name'];
    
    protected $table = 'rols';
    
    public function user(){
        return $this->belongsTo('App\User','idRol','id');
    }
    
}
