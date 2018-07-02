<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Dealer;
use App\User;
use App\AccountDetail;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use App\Order;
use App\CarModel;
use App\StatusOrder;
use App\ReasonReject;
use App\Carrier;
use App\OrderComment;
use App\DamageOrder;
use App\Damage;
use App\DamageArea;
use App\TemporalPayment;
use App\SatBill;
use Config;

class ImportController extends Controller
{
    //

    public function importUserDealers()
    {
    	Excel::load('dealers.xlsx', function($reader) {

     		foreach ($reader->get() as $row) {
                
                $idUsuario = $this->createUser($row);
                
                if($idUsuario != null){
                    $idDealer = $this->createDealer($idUsuario,$row);
                    if($idDealer != null){
                        $idAccount = $this->createAccountDetails($idDealer,$row);
                        if($idAccount != null){
                            echo "Creado correctamente\n";
                        }else{
                            echo "Error al crear cuenta\n";
                        }
                    }else{
                        echo "Error al crear dealer\n";
                    }
                }else{
                    echo "Error al crear usuario\n";
                }
             
                		
            }
            
		});
    }
    
    public function importOrders()
    {
    	Excel::load('orders_rejected.xlsx', function($reader) {

     		foreach ($reader->get() as $row) {
                if(!empty($row->vin)){
                $idOrder = $this->createOrder($row);
                if($idOrder != null){
                    $idComment = $this->createComment($idOrder,$row);
                    if($idComment != null){
                        $idDamage = $this->createDamageOrder($idOrder,$row);
                        if($idDamage != null){
                            $idPay = $this->temporalPayment($idOrder,$row);
                            if($idPay != null){
                                $idBill = $this->createSatBill($idOrder,$row);
                                if($idBill != null){
                                    echo "Registro insertado correctamente";
                                }else{
                        echo "Fallo al ingresar la factura\n";
                }
                            }else{
                        echo "Fallo al ingresar el pago\n";
                }
                        }
                        else{
                        echo "Fallo al ingresar el daño\n";
                }
                    }
                else{
                    echo "Fallo al ingresar el comentario\n";
                }
                }else{
                    echo "Fallo al ingresar la orden\n";
                }
            }
            }
            
		});
    }
    
    
    protected function createDealer($id_usuario,$row){
        $dealer = Dealer::create([
            'commercial_name' => $row->commercial_name,
            'rfc' => $row->rfc,
            'business_name' => $row->razon_social,
            'idUsuario' => $id_usuario,
            'status' => 1,
            ]);  
        return $dealer->id;
    }
    
    protected function createUser($row){
        
        $user = User::create([
            'code' => $row->code,
            'email' => $row->code."@buscarv.com.mx",
            'password' => Hash::make($row->code),
            'idRol' => 2,
        ]);
        
        return $user->id;
        
    }
    
    protected function createAccountDetails($idDealer,$row){
        
        $account = AccountDetail::create([
            'idDealer' => $idDealer,
            'bank_name' => $row->bank,
            'clabe' => $row->clabe,
            'account' => $row->bank_account,
            'status' => 1,
        ]);
        
        return $account->id;
        
    }
    
    protected function createOrder($row){
        $modelo = CarModel::where('name','=',$row->modelo)->get();
        $modelo = $modelo[0]->id;
        $dealer = User::where('code','=',$row->codigo_dealer)->get();
        $dealer = $dealer[0]->id;
        if(!empty($row->motivo_rechazo)){
        $reason_reject = ReasonReject::where('code','=',$row->motivo_rechazo)->get();
        $reason_reject = $reason_reject[0]->id;
        }else{
        $reason_reject = null; 
        }
        $carrier = Carrier::where('name','=',$row->carrier)->get();
        $carrier = $carrier[0]->id;
        $status = null;
        if($row->status == "ACCEPTED"){
            $status = StatusOrder::where('name','=',Config::get('constants.options.ORDER_ACCEPTED'))->get();
            $status = $status[0]->id;
        }else if($row->status == "IN PROCESS"){
            $status = StatusOrder::where('name','=',Config::get('constants.options.ORDER_INPROCESS'))->get();
            $status = $status[0]->id;       
        }else if($row->status == "REJECTED"){
            $status = StatusOrder::where('name','=',Config::get('constants.options.ORDER_REJECTED'))->get();
            $status = $status[0]->id;       
        }
        if($row->fecha_arribo == "NA" || empty($row->fecha_arribo)){
            $fecha_arribo = null;
        }
        else{
            $fecha_arribo = $row->fecha_arribo;
        }
        if($row->fecha_reporte == "NA" || empty($row->fecha_reporte)){
            $fecha_reporte = null;
        }
        else{
            $fecha_reporte = $row->fecha_arribo;
        }
        
        
        $order = Order::create([
            'vin' => $row->vin,
            'idModelo' => $modelo,
            'idDealer' => $dealer,
            'arrive_date' => $fecha_arribo,
            'report_date' => $fecha_reporte,
            'idStatus' => $status,
            'idReasonReject' => $reason_reject,
            'idCarrier' => $carrier,
            'smx_gmx' => $row->smxgmx,
            'total_amount' => $row->monto_total_de_la_reparacion,
            'status' => 1,
        ]);
        
        return $order->id;
    }
    
    protected function createComment($idOrder,$row){
        
        $order = Order::find($idOrder);
        $comment = OrderComment::create([
            'comment' => $row->comentarios,  
            'status' => 1,
            'idDealer' => $order->idDealer,
            'idOrder' => $order->id,
        ]);
        return $comment->id;
    }
    
    public function createDamageOrder($idOrder,$row){
        $damage = Damage::where('number','=',$row->cod_tipo_de_dano)->get();
        $damage = $damage[0]->id;
        $damageArea = DamageArea::where('number','=',$row->cod_area_danada)->get();
        $damageArea = $damageArea[0]->id;
        $damageOrder = DamageOrder::create([
            'idOrder' => $idOrder,
            'idDamageArea' => $damageArea,
            'idDamage' => $damage,
            
        ]);
        
        return $damageOrder->id;
    }
    
    public function temporalPayment($idOrder,$row){
        
        if($row->fecha_de_pago_gmx == "NA" || empty($row->fecha_de_pago_gmx)){
            $fecha_de_pago_gmx = null;
        }
        else{
            $fecha_de_pago_gmx = $row->fecha_de_pago_gmx;
        }
        
        if($row->fecha_de_pagosompo == "NA" || empty($row->fecha_de_pagosompo)){
            $fecha_de_pagosompo = null;
        }
        else{
            $fecha_de_pagosompo = $row->fecha_de_pagosompo;
        }
        
        if($row->monto_a_pagargmx == "NA" || empty($row->monto_a_pagargmx)){
            $monto_a_pagargmx = null;
        }
        else{
            $monto_a_pagargmx = $row->monto_a_pagargmx;
        }
        
        if($row->monto_a_pagarsmx == "NA" || empty($row->monto_a_pagarsmx)){
            $monto_a_pagarsmx = null;
        }
        else{
            $monto_a_pagarsmx = $row->monto_a_pagarsmx;
        }
        
         $temporalPayment = TemporalPayment::create([
            'idOrder' =>  $idOrder,
            'amount_gmx' => $monto_a_pagargmx,
            'pay_date_gmx' => $fecha_de_pago_gmx,
            'smx_claim_number' => $row->smx_claim_number,
            'amount_smx' => $monto_a_pagarsmx,
            'pay_date_smx' => $fecha_de_pagosompo,
        ]);
        
        return $temporalPayment->id;
    }
    
    public function createSatBill($idOrder,$row){
        $order = Order::find($idOrder);
        if($row->fecha_de_facturacion == "NA" || empty($row->fecha_de_facturacion)){
            $fecha_de_facturacion = null;
        }
        else{
            $fecha_de_facturacion = $row->fecha_de_facturacion;
        }
        
        if($row->total == "NA" || empty($row->total)){
            $total = null;
        }
        else{
            $total = $row->total;
        }
        
        if($row->status_factura == "IN PROCESS"){
            $status_factura = 2;
        }else{
            $status_factura = null;
        }
        $bill = SatBill::create([
            'idOrder' => $idOrder,
            'billing_date' => $fecha_de_facturacion,
            'folio' => $row->factura,
            'idDealer' => $order->idDealer,
            'total_amount' => $total,
            'idStatusBill' => $status_factura,
            'pay_date' => $row->fecha_de_pago,
            'bank' => $row->banco,
            'import_mxn' => $row->importe_mxn,
        ]);
        
        return $bill->id;
    }
}
