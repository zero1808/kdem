        

@extends('layouts.navbar')

@section('content')


<div class="wrapper wrapper-content animated fadeIn">
    
        <div class="row wrapper border-bottom white-bg page-heading">
                    <div class="col-lg-10">
                    <h2>Claim info</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index.html">Inicio</a>
                        </li>
                        <li>
                            <a>Claims</a>
                        </li>
                        <li class="active">
                            <strong>Información</strong>
                        </li>
                    </ol>
                </div>
                    <div class="col-lg-2">
                    </div>
        </div>
    
    <div class="row">
        @if(isset($error))
        <div class="alert alert-danger">
            {{$error}}
        </div>
        @endif
        
        @if(isset($info))
        <div class="alert alert-danger">
            {{$info}}
        </div>
        @endif
    </div>
    
    @if(isset($order))
        @if($order != null)
    <div class="row">
                <div class="col-lg-6">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Tabla de informaci&oacute;n</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-wrench"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="#">Config option 1</a>
                                    </li>
                                    <li><a href="#">Config option 2</a>
                                    </li>
                                </ul>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>VIN</th>
                                    <th>Modelo</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>{{$order->vin}}</td>
                                    <td>@if(isset($order->carModel)) {{$order->carModel->name}} @else  @endif</td>
                                    <td>@if(isset($order->statusOrder)) 
                                        
                                            @if($order->statusOrder->name == \Config::get('constants.options.ORDER_ACCEPTED'))
                                            <span class="label label-success">Aceptada</span>
                                            @endif
                                        
                                            @if($order->statusOrder->name == \Config::get('constants.options.ORDER_REJECTED'))
                                            <span class="label label-danger">Rechazada</span>
                                            @endif
                                        
                                            @if($order->statusOrder->name == \Config::get('constants.options.ORDER_INPROCESS'))
                                            <span class="label label-info">En proceso</span>
                                            @endif
                                        
                                        @else 
                                        @endif</td>
                                </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>Informaci&oacute;n dealer</h5>
                            <div class="ibox-tools">
                                <a class="collapse-link">
                                    <i class="fa fa-chevron-up"></i>
                                </a>
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                    <i class="fa fa-wrench"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-user">
                                    <li><a href="#">Config option 1</a>
                                    </li>
                                    <li><a href="#">Config option 2</a>
                                    </li>
                                </ul>
                                <a class="close-link">
                                    <i class="fa fa-times"></i>
                                </a>
                            </div>
                        </div>
                        <div class="ibox-content">

                            <table class="table table-hover">
                                <thead>
                                <tr>
                                    <th>Dealer</th>
                                    <th>Codigo dealer</th>
                                    <th>Raz&oacute;n social</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <td>@if(isset($order->dealer)) {{$order->dealer->commercial_name}} @else  @endif</td>
                                    <td>@if(isset($order->dealer)) {{$order->dealer->userInfo->code}} @else  @endif</td>
                                    <td>@if(isset($order->dealer)) {{$order->dealer->business_name}} @else  @endif</td>
                                </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
    </div>
    <div class="row m-t-lg">
                <div class="col-lg-12">
                    <div class="tabs-container">

                        <div class="tabs-left">
                            <ul class="nav nav-tabs">
                                <li class="active"><a data-toggle="tab" href="#tab-6"> Información general</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-7">Información dealer</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-8">Comentarios</a></li>
                                <li class=""><a data-toggle="tab" href="#tab-9">Pagos y facturas</a></li>
                            </ul>
                            <div class="tab-content ">
                                <div id="tab-6" class="tab-pane active">
                                    <div class="panel-body">
                                        <form method="get" class="form-horizontal">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">VIN</label>
                                                    <div class="col-sm-10"><input type="text" class="form-control" value="{{$order->vin}}"></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Fecha arribo</label>
                                                    <div class="col-sm-10"><input type="text" class="form-control" value="{{$order->arrive_date}}"></div>
                                                </div>
                                                @foreach($order->damageOrders as $orderDamageArea)
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Parte dañada</label>
                                                        <div class="col-sm-10">
                                                            <select  class="form-control">
                                                                <option value="{{$orderDamageArea->damageArea->number}}">{{$orderDamageArea->damageArea->number}} - {{$orderDamageArea->damageArea->name}}</option>
                                                            </select>
                                                        </div>
                                                </div>   
                                                @endforeach
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Motivo rechazo</label>
                                                    <div class="col-sm-10"><input type="text" class="form-control" value="@if(isset($order->reasonReject)) {{$order->reasonReject->code}} @else NA  @endif"></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">SMX / GMX</label>
                                                    <div class="col-sm-10"><input type="text" class="form-control" value="{{$order->smx_gmx}}"></div>
                                                </div>
                                                
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Carrier</label>
                                                    <div class="col-sm-10">
                                                        <select class="form-control">
                                                            @foreach(App\Carrier::all() as $carrier)
                                                                @if(isset($order->carrier))
                                                                    @if($order->carrier->id == $carrier->id)
                                                                    <option value="{{$carrier->id}}" selected>{{$carrier->name}}</option>
                                                                    @else
                                                                    <option value="{{$carrier->id}}">{{$carrier->name}}</option>
                                                                    @endif
                                                                @else
                                                                    <option value="{{$carrier->id}}">{{$carrier->name}}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
          
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Modelo</label>
                                                    <div class="col-sm-10"><input type="text" class="form-control" value="@if(isset($order->carModel)) {{$order->carModel->name}} @else  @endif"></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Fecha reporte</label>
                                                    <div class="col-sm-10"><input type="text" class="form-control" value="{{$order->report_date}}"></div>
                                                </div>
                                                @foreach($order->damageOrders as $orderDamage)
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Tipo daño</label>
                                                        <div class="col-sm-10">
                                                            <select  class="form-control">
                                                                <option value="{{$orderDamage->damage->number}}">{{$orderDamage->damage->number}} - {{$orderDamage->damage->name}}</option>
                                                            </select>
                                                        </div>
                                                </div>   
                                                @endforeach
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Status</label>
                                                    <div class="col-sm-10">
                                                        <select class="form-control">
                                                            @foreach(App\StatusOrder::all() as $status)
                                                                @if(isset($order->statusOrder))
                                                                    @if($order->statusOrder->id == $status->id)
                                                                    <option value="{{$status->id}}" selected>{{$status->name}}</option>
                                                                    @else
                                                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                                                    @endif
                                                                @else
                                                                    <option value="{{$status->id}}">{{$status->name}}</option>
                                                                @endif
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Monto total</label>
                                                    <div class="col-sm-10"><input type="text" class="form-control" value="{{$order->total_amount}}"></div>
                                                </div>

                                            </div>
                                            
                                        </form>
                                    </div>
                                </div>
                                <div id="tab-7" class="tab-pane">
                                    <div class="panel-body">
                                        <form method="get" class="form-horizontal">
                                            <div class="col-sm-6">
                                                
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Raz&oacute;n social</label>
                                                    <div class="col-sm-10"><input type="text" class="form-control" value="@if(isset($order->dealer)) {{$order->dealer->business_name}} @else  @endif"></div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">C&oacute;digo</label>
                                                    <div class="col-sm-10"><input type="text" class="form-control" value="@if(isset($order->dealer)) {{$order->dealer->userInfo->code}} @else  @endif"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label class="col-sm-2 control-label">Dealer</label>
                                                    <div class="col-sm-10"><input type="text" class="form-control" value="@if(isset($order->dealer)) {{$order->dealer->commercial_name}} @else  @endif"></div>
                                                </div>
  
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div id="tab-8" class="tab-pane">
                                    <div class="panel-body">                              
                                        <div class="col-lg-12">
                                            <div class="ibox float-e-margins">
                                                <div class="ibox-title">
                                                    <h5><i class="fa fa-send"></i> Comentarios</h5>
                                                    <div class="ibox-tools">
                                                        <a class="collapse-link">
                                                            <i class="fa fa-chevron-up"></i>
                                                        </a>

                                                    </div>
                                                </div>

                                                <div class="ibox-content">
                                                        <div id="chat_revision" class="chat-activity-list">
                                                            
                                                           <!-- FILL WITH AJAX-->
                                                            @foreach($order->comments as $comment)
                                                            <hr>
                                                            <div class="chat-element right"><a href="#" class="pull-right">
                                                                <img alt="image" width="50px" heigth="50px" class="img-circle" src="{{asset('img/profile_small.jpg')}}"></a>
                                                                <div class="media-body text-right">
                                                                    <small class="pull-right text-navy">
                                                                    </small><strong> {{$comment->dealer->commercial_name}} </strong>
                                                                    <p class="m-b-xs">{{$comment->comment}}</p>
                                                                    <small class="text-muted">{{$comment->created_at}}</small>
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    <div class="hr-line-dashed"></div>

                                                    <div class="chat-form">
                                                        <form enctype="multipart/form-data" method="post" id="form_mensajepresidencia_revision" name="form_mensajepresidencia_revision" class="form-vertical">
                                                            <div class="form-group">
                                                                <textarea class="form-control" id="mensaje" name="mensaje" placeholder="Escribe un mensaje..."></textarea>
                                                            </div>
                                                            <div class="text-right">
                                                                <button type="button" id="btt_enviar_mensaje" name="btt_enviar_mensaje" class="btn btn-sm btn-danger m-t-n-xs"><strong>Enviar</strong></button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="tab-9" class="tab-pane">
                                    <div class="panel-body">                              
                                        <div class="col-lg-12">
                                            <div class="ibox float-e-margins">
                                                    <div class="ibox-title">
                                                        <h5>Pagos</h5>
                                                        <div class="ibox-tools">
                                                            <a class="collapse-link">
                                                                <i class="fa fa-chevron-up"></i>
                                                            </a>
                                                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                                                <i class="fa fa-wrench"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-user">
                                                                <li><a href="#">Config option 1</a>
                                                                </li>
                                                                <li><a href="#">Config option 2</a>
                                                                </li>
                                                            </ul>
                                                            <a class="close-link">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="ibox-content">

                                                        <table class="table table-bordered">
                                                            <thead>
                                                            <tr>
                                                                <th>Monto a pagar GMX</th>
                                                                <th>Fecha de pago GMX</th>
                                                                <th>SMX CLAIM NUMBER</th>
                                                                <th>Monto a pagar GMX</th>
                                                                <th>Fecha de pago SOMPO</th>
                                                                <th>Men&uacute;</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @if(isset($order->temporalPayment))
                                                            <tr>
                                                                <td>{{$order->temporalPayment->amount_gmx}}</td>
                                                                <td>{{$order->temporalPayment->pay_date_gmx}}</td>
                                                                <td>{{$order->temporalPayment->smx_claim_number}}</td>
                                                                <td>{{$order->temporalPayment->amount_smx}}</td>
                                                                <td>{{$order->temporalPayment->pay_date_smx}}</td>
                                                                <td></td>
                                                            </tr>
                                                            @endif
                                                            </tbody>
                                                        </table>

                                                    </div>
                                            </div>
                                        </div>
                                        
                                        <div class="ibox float-e-margins">
                                                    <div class="ibox-title">
                                                        <h5>Facturas</h5>
                                                        <div class="ibox-tools">
                                                            <a class="collapse-link">
                                                                <i class="fa fa-chevron-up"></i>
                                                            </a>
                                                            <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                                                                <i class="fa fa-wrench"></i>
                                                            </a>
                                                            <ul class="dropdown-menu dropdown-user">
                                                                <li><a href="#">Config option 1</a>
                                                                </li>
                                                                <li><a href="#">Config option 2</a>
                                                                </li>
                                                            </ul>
                                                            <a class="close-link">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="ibox-content">

                                                        <table class="table table-bordered">
                                                            <thead>
                                                            <tr>
                                                                <th>Fecha de facturaci&oacute;n</th>
                                                                <th>Factura</th>
                                                                <th>Total</th>
                                                                <th>Status</th>
                                                                <th>Fecha de pago</th>
                                                                <th>Banco</th>
                                                                <th>Importe</th>
                                                                <th>Men&uacute;</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            
                                                            @foreach($order->satBills as $bill)
                                                            <tr>
                                                                <td>{{$bill->billing_date}}</td>
                                                                <td>{{$bill->folio}}</td>
                                                                <td>{{$bill->total_amount}}</td>
                                                                <td>@if(isset($bill->statusBill)) {{$bill->statusBill->name}} @else  @endif</td>
                                                                <td>{{$bill->pay_date}}</td>
                                                                <td>{{$bill->bank}}</td>
                                                                <td>{{$bill->import_mxn}}</td>
                                                                <td></td>
                                                            </tr>
                                                            @endforeach
                                                            </tbody>
                                                        </table>

                                                    </div>
                                            </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
        </div>
        @endif
    @endif
</div>

@endsection


@section('scripts')
<script> var path = "{{URL::to('/')}}";</script>

<script src="{{asset('js/plugins/jeditable/jquery.jeditable.js')}}"></script>
<script src="{{asset('js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
    <!-- Controller -->
@endsection