<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    //
    protected $fillable = ['vin', 'idModelo', 'idDealer', 'arrive_date', 'arrive_date_time', 'report_date', 'idStatus', 'idReasonReject', 'idCarrier', 'responsable_name', 'responsable_phone', 'responsable_email', 'smx_gmx', 'total_amount', 'status', 'src_carry_letter', 'src_checklist'];
    protected $table = 'orders';

    public function carModel() {
        return $this->hasOne('App\CarModel', 'id', 'idModelo');
    }

    public function dealers() {
        return $this->belongsTo('App\Dealer', 'id', 'idDealer');
    }

    public function statusOrder() {
        return $this->hasOne('App\StatusOrder', 'id', 'idStatus');
    }

    public function reasonReject() {
        return $this->hasOne('App\ReasonReject', 'id', 'idReasonReject');
    }

    public function carrier() {
        return $this->hasOne('App\Carrier', 'id', 'idCarrier');
    }

    public function comments() {
        return $this->hasMany('App\OrderComment', 'idOrder', 'id');
    }

    public function satBills() {
        return $this->hasMany('App\SatBill', 'idOrder', 'id');
    }

    public function temporalPayment() {
        return $this->hasOne('App\TemporalPayment', 'idOrder', 'id');
    }

    public function dealer() {
        return $this->hasOne('App\Dealer', 'id', 'idDealer');
    }

    public function damageOrders() {
        return $this->hasMany('App\DamageOrder', 'idOrder', 'id');
    }

    public function claimPics() {
        return $this->hasMany('App\ClaimPic', 'idOrder', 'id');
    }

}
