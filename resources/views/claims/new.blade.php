@extends('layouts.navbar')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>New Claim</h2>
        <ol class="breadcrumb">
            <li>
                <a href="index.html">Inicio</a>
            </li>
            <li>
                <a>Claims</a>
            </li>
            <li class="active">
                <strong>New</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Llena todos los campos <small></small></h5>
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
                        <form method="post" class="form-horizontal" id="claim_form" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group"><label class="col-sm-2 control-label">VIN</label>
                                <div class="col-sm-10"><input type="text" class="form-control" id="vin" name="vin" placeholder="Ingresa el n&uacute;mero VIN" onkeyup="aMays(event, this)" onblur="aMays(event, this)"> <span class="help-block m-b-none">Puedes usar un lector de c&oacute;digo de barras.</span>
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Modelo</label>
                                <div class="col-sm-4">
                                    <select class="form-control" name="car_model" id="car_model">
                                        @if(isset($models))
                                        @foreach($models as $model)
                                        <option value="{{$model->id}}">{{$model->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>

                                <label class="col-sm-2 control-label">Dealer</label>
                                <div class="col-sm-4">
                                    <input type="text" id="code_dealer" name="code_dealer" class="form-control" value="{{Auth::user()->code}}" readonly="true"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Fecha arribo</label>
                                <div class="col-sm-4" id="arrive_date_div">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" id="arrive_date" name="arrive_date" value="2014-08-18" readonly="true">
                                    </div>
                                </div>

                                <label class="col-sm-2 control-label">Fecha reporte</label>
                                <div class="col-sm-4" id="report_date_div">
                                    <div class="input-group date">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" id="report_date" name="report_date" class="form-control" readonly="true">
                                    </div>
                                </div>

                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">&Aacute;rea daño</label>
                                <div class="col-sm-4">
                                    <select class="form-control" name="damage_area" id="damage_area">
                                        <option value="0">------ Seleccione ------</option>
                                        @if(isset($damageAreas))
                                        @foreach($damageAreas as $damageArea)
                                        <option value="{{$damageArea->id}}">{{$damageArea->number}} - {{$damageArea->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>

                                <label class="col-sm-2 control-label">Tipo daño</label>
                                <div class="col-sm-4">
                                    <select class="form-control" name="damage_type" id="damage_type">
                                        <option value="0">------ Seleccione ------</option>
                                        @if(isset($damages))
                                        @foreach($damages as $damage)
                                        <option value="{{$damage->id}}">{{$damage->number}} - {{$damage->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div> 

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Carrier</label>
                                <div class="col-sm-4">
                                    <select class="form-control" name="carrier" id="carrier">
                                        <option value="0">------ Seleccione ------</option>
                                        @if(isset($carriers))
                                        @foreach($carriers as $carrier)
                                        <option value="{{$carrier->id}}">{{$carrier->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>

                                <label class="col-sm-2 control-label">Status</label>
                                <div class="col-sm-4">
                                    <select class="form-control" name="status" id="status" >
                                        <option value="0">------ Seleccione ------</option>
                                        @if(isset($statusOrders))
                                        @foreach($statusOrders as $statusOrder)
                                        <option value="{{$statusOrder->id}}">{{$statusOrder->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>  

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Monto SMX</label>
                                <div class="col-sm-4">
                                    <div class="input-group m-b"><span class="input-group-addon">$</span> <input type="text" class="form-control" id="amount_smx" name="amount_smx" onkeypress="return validateFloatKeyPress(this, event);"></div>
                                </div>

                                <label class="col-sm-2 control-label">Monto GMX</label>
                                <div class="col-sm-4">
                                    <div class="input-group m-b"><span class="input-group-addon">$</span> <input type="text" class="form-control" id="amount_gmx" name="amount_gmx" onkeypress="return validateFloatKeyPress(this, event);"></div>
                                </div>
                            </div> 

                            <div class="hr-line-dashed"></div>

                            <div id="file_upload" class="dropzone">
                                <div class="dz-message" data-dz-message>
                                    <span><center><h4>Agrega tus archivos PDF</h4></center></span>
                                    <center><p>Arrastralos o da click</p></center>
                                </div>
                                <div class="dropzone-previews"></div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-2" align="right">
                                    <button class="btn btn-white"  id="cancel_btt" type="button">Cancel</button>
                                    <button class="btn btn-primary" type="button" id="claim_submit">Guardar</button>
                                </div>
                            </div>



                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('modals.loader')
@include('modals.notification')

@endsection


@section('styles')
<link href="{{asset('css/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet">
<link href="{{asset('css/plugins/datapicker/datepicker3.css')}}" rel="stylesheet">
<link href="{{asset('css/plugins/dropzone/basic.css')}}" rel="stylesheet">
<link href="{{asset('css/plugins/dropzone/dropzone.css')}}" rel="stylesheet">

@endsection

@section('scripts')
<script> var path = "{{URL::to('/')}}";</script>

<script src="{{asset('js/plugins/jeditable/jquery.jeditable.js')}}"></script>
<script src="{{asset('js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
<script src="{{asset('js/plugins/sweetalert/sweetalert.min.js')}}"></script>
<!-- Data picker -->
<script src="{{asset('js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
<!-- DROPZONE -->
<script src="{{asset('js/plugins/dropzone/dropzone.js')}}"></script>
<!-- Utils -->
<script src="{{asset('js/common/validations.js')}}"></script>
<!-- Controller -->
<script src="{{asset('js/scripts/new_claim.controller.js')}}"></script>

@endsection
