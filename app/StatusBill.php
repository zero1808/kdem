<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusBill extends Model
{
    //
    protected $fillable = ['name'];
    
    protected $table = 'status_bills';
    
    public function satBill(){
        return $this->belongsTo('App\SatBill','idStatusBill','id');
    }
}
