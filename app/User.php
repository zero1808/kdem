<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code','email','password','idRol',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    
    public function dealer(){
        return $this->hasOne('App\Dealer','idUsuario','id');
    }
    
    public function rol(){
        return $this->hasOne('App\Rol','id','idRol');
    }
    
    public function dealerInfo(){
        return $this->belongsTo('App\Dealer','idUsuario','id');
    }
}
