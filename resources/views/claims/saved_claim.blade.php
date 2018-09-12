@extends('layouts.navbar')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Saved Claim</h2>
        <ol class="breadcrumb">
            <li>
                <a href="index.html">Inicio</a>
            </li>
            <li>
                <a>Claims</a>
            </li>
            <li class="active">
                <strong>Saved</strong>
            </li>
        </ol>
    </div>
    <div class="col-lg-2">
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="wrapper wrapper-content animated fadeInRight">
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
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Llena todos los campos <small></small></h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    @if(isset($claimId))
                    <div class="ibox-content">
                        <form method="post" class="form-horizontal" id="claim_form" enctype="multipart/form-data">
                            {{method_field('put')}}
                            @csrf
                            <input type="hidden" id="i_damage" name="i_damage"/>
                            <input type="hidden" id="claim_stored" name="claim_stored" value="{{$claimId}}"/>
                            <input type="hidden" id="will_be_saved" name="will_be_saved" value="0"/>
                            <div class="form-group tooltip-demo">
                                <label class="col-sm-2 control-label">VIN</label>
                                <div class="col-sm-4" id="vin_error_control">
                                    <input type="text" class="form-control" id="vin" name="vin" placeholder="Ingresa el n&uacute;mero VIN" onkeyup="aMays(event, this)" onblur="aMays(event, this)"> 
                                    <span class="help-block m-b-none">Puedes usar un lector de c&oacute;digo de barras.</span>
                                </div>
                                <label class="col-sm-2 control-label">Confirmaci&oacute;n VIN</label>
                                <div class="col-sm-4" id="vin_confirmation_error_control">
                                    <input type="text" class="form-control" id="vin_confirmation" name="vin_confirmation" placeholder="Confirma el n&uacute;mero VIN" onkeyup="aMays(event, this)" onblur="aMays(event, this)"> 
                                </div>
                            </div>
                            <div class="hr-line-dashed"></div>
                            <div class="form-group tooltip-demo">
                                <label class="col-sm-2 control-label">Modelo</label>
                                <div class="col-sm-4" id="car_model_error_control">
                                    <select class="form-control" name="car_model" id="car_model">
                                        <option value="0">------ Seleccione ------</option>
                                        @if(isset($models))
                                        @foreach($models as $model)
                                        <option value="{{$model->id}}">{{$model->code}} - {{$model->name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>

                                <label class="col-sm-2 control-label">Dealer</label>
                                <div class="col-sm-4" id="code_dealer_error_control">
                                    <input type="text" id="code_dealer" name="code_dealer" class="form-control" value="{{Auth::user()->code}}" readonly="true"/>
                                </div>
                            </div>
                            <div class="form-group tooltip-demo">
                                <label class="col-sm-2 control-label">Fecha arribo</label>
                                <div class="col-sm-2" id="arrive_date_div">
                                    <div class="input-group date" id="arrive_date_error_control">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" class="form-control" id="arrive_date" name="arrive_date"  data-format="dd/MM/yyyy hh:mm:ss" readonly="true">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group clockpicker" data-autoclose="true" id="arrive_date_time_error_control">
                                        <input type="text" class="form-control" id="arrive_date_time" name="arrive_date_time" value="09:30" readonly="true">
                                        <span class="input-group-addon">
                                            <span class="fa fa-clock-o"></span>
                                        </span>
                                    </div>
                                </div>
                                <label class="col-sm-2 control-label">Fecha reporte</label>
                                <div class="col-sm-4" id="report_date_div">
                                    <div class="input-group date" id="report_date_error_control">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span><input type="text" id="report_date" name="report_date" class="form-control" readonly="true">
                                    </div>
                                </div>

                            </div>

                            <div class="hr-line-dashed"></div>
                            <h5>Daños<small></small></h5>
                            <div class="hr-line-dashed"></div>
                            <div id="damages" class="tooltip-demo">

                            </div>

                            <div align="right">
                                <div class="btn-group">
                                    <button class="btn btn-danger" id="delete_danger" type="button">-</button>
                                    <button class="btn btn-success" id="add_danger" type="button">+</button>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group tooltip-demo">
                                <label class="col-sm-2 control-label">Carrier</label>
                                <div class="col-sm-10" id="carrier_error_control">
                                    <select class="form-control" name="carrier" id="carrier">
                                        <option value="0">------ Seleccione ------</option>
                                        @if(isset($carriers))
                                        @foreach($carriers as $carrier)
                                        <option value="{{$carrier->id}}">{{$carrier->name}} - {{$carrier->business_name}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>  

                            <div class="form-group tooltip-demo">
                                <label class="col-sm-2 control-label">Nombre del responsable</label>
                                <div class="col-sm-10" id="responsable_name_error_control">
                                    <input type="text" class="form-control" id="responsable_name" name="responsable_name" placeholder="Ingresa el nombre del responsable del concesionario" onkeyup="aMays(event, this)" onkeypress="return onlyLetters(event);" onblur="aMays(event, this)"> 
                                </div>
                            </div>

                            <div class="form-group tooltip-demo">
                                <label class="col-sm-2 control-label">Tel&eacute;fono</label>
                                <div class="col-sm-4" id="responsable_phone_error_control">
                                    <input type="text" class="form-control" id="responsable_phone" name="responsable_phone" placeholder="Ingresa el tel&eacute;fono del responsable" onkeypress="return phoneValidation(this);"> 
                                </div>
                                <label class="col-sm-2 control-label">E-Mail</label>
                                <div class="col-sm-4" id="responsable_email_error_control">
                                    <input type="email" class="form-control" id="responsable_email" name="responsable_email" placeholder="Ingresa el email del responsable"> 
                                </div>
                            </div>

                            <div class="form-group tooltip-demo">
                                <label class="col-sm-2 control-label">Carta porte</label>
                                <div class="col-sm-4" id="carry_letter_input_error_control">
                                    <!-- COMPONENT START -->
                                    <div class="input-group input-file"  id="carry_letter_input" name="carry_letter_input">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default btn-choose" type="button">Selecciona</button>
                                        </span>
                                        <input type="text" class="form-control" placeholder='Selecciona un archivo...' />
                                        <span class="input-group-btn">
                                            <button class="btn btn-warning btn-reset" id="btt_reset_carryletter" type="button">Limpiar</button>
                                        </span>
                                    </div>
                                </div>

                                <label class="col-sm-2 control-label">Checklist</label>

                                <div class="col-sm-4" id="checklist_input_error_control">
                                    <!-- COMPONENT START -->
                                    <div class="input-group input-file" id="checklist_input" name="checklist_input">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default btn-choose" type="button">Selecciona</button>
                                        </span>
                                        <input type="text" class="form-control"  placeholder='Selecciona un archivo...' />
                                        <span class="input-group-btn">
                                            <button class="btn btn-warning btn-reset" id="btt_reset_checklist" type="button">Limpiar</button>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group tooltip-demo" id="div_documents">
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-4" >
                                    <label id="carry_letter_loaded" style="display:none;"><a href="" id="href_carry_letter" name="href_carry_letter" target="_blank"><icon class="fa fa-file-pdf-o"></icon> &nbsp; Descargar carta porte actual</a></label>
                                </div>
                                <label class="col-sm-2 control-label"></label>
                                <div class="col-sm-4">
                                    <label id="checklist_loaded" style="display:none;"><a href="" id="href_checklist" name="href_checklist" target="_blank"><icon class="fa fa-file-pdf-o"></icon> &nbsp; Descargar checklist actual</a></label>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Fotos previamente cargadas</label>
                                <div class="col-sm-10">
                                    <ul class="ul-thumnail" id="previous_loaded_images">
                                    </ul>
                                </div>
                            </div>
                            <div class="form-group tooltip-demo">
                                <label class="col-sm-2 control-label">Fotos</label>

                                <div class="col-sm-10">
                                    <div id="file_upload" class="dropzone">
                                        <div class="dz-message" data-dz-message>
                                            <span><center><h4>Tambien puedes agregar fotos aqui</h4></center></span>
                                            <center><p>Arrastralos o da click</p></center>
                                        </div>
                                        <div class="dropzone-previews"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="hr-line-dashed"></div>

                            <div class="form-group">
                                <div class="col-sm-10 col-sm-offset-2" align="right">
                                    <div class="btn-group">
                                        <button class="btn btn-white"  id="cancel_btt" type="button">Cancelar</button>
                                        <button class="btn btn-success" type="button" id="claim_submit" style="display:block;"><i class="fa fa-check"></i>&nbsp;Registrar</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@include('modals.loader')
@include('modals.notification')
@include('modals.validationerror')

@endsection


@section('styles')
<link href="{{asset('css/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet">
<link href="{{asset('css/plugins/datapicker/datepicker3.css')}}" rel="stylesheet">
<link href="{{asset('css/plugins/dropzone/basic.css')}}" rel="stylesheet">
<link href="{{asset('css/plugins/dropzone/dropzone.css')}}" rel="stylesheet">
<link href="{{asset('css/plugins/clockpicker/clockpicker.css')}}" rel="stylesheet">
<link href="{{asset('css/common/thumnail_style.css')}}" rel="stylesheet">
@endsection

@section('scripts')
<script> var path = "{{URL::to('/')}}";</script>

<script src="{{asset('js/plugins/jeditable/jquery.jeditable.js')}}"></script>
<script src="{{asset('js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
<script src="{{asset('js/plugins/sweetalert/sweetalert.min.js')}}"></script>
<!-- Data picker -->
<script src="{{asset('js/plugins/datapicker/bootstrap-datepicker.js')}}"></script>
<script src="{{asset('js/plugins/clockpicker/clockpicker.js')}}"></script>
<!-- DROPZONE -->
<script src="{{asset('js/plugins/dropzone/dropzone.js')}}"></script>
<!-- Utils -->
<script src="{{asset('js/common/validations.js')}}"></script>
<script src="{{asset('js/common/spinners.js')}}"></script>

<!-- Controller -->
<script src="{{asset('js/scripts/saved_claim.controller.js')}}"></script>

@endsection
