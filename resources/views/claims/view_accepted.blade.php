@extends('layouts.navbar')

@section('content')


    <div class="row wrapper border-bottom white-bg page-heading">
                    <div class="col-lg-10">
                    <h2>Claims List</h2>
                    <ol class="breadcrumb">
                        <li>
                            <a href="index.html">Inicio</a>
                        </li>
                        <li>
                            <a>Claims</a>
                        </li>
                        <li class="active">
                            <strong>Aceptadas</strong>
                        </li>
                    </ol>
                </div>
                    <div class="col-lg-2">
                    </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight ecommerce">
        <div class="ibox-content m-b-sm border-bottom">
                <div class="row">
                <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Aceptadas </h5>
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

                        </div>
                    </div>
                    <div class="ibox-content">

                        <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover dataTables-example" >
                    <thead>
                    <tr>
                        <th>Vin</th>
                        <th>Modelo</th>
                        <th>Fecha arribo</th>
                        <th>Status</th>
                        <th>Carrier</th>
                        @if(Auth::user()->rol->name == \Config::get('constants.options.ROL_ADMINISTRATOR'))
                        <th>Dealer</th>
                        @endif
                        <th>Men&uacute;</th>
                    </tr>
                    </thead>
                    <tbody>
                        
                    @if(isset($orders_accepted))
                        @foreach($orders_accepted as $order)
                            <tr class="gradeA">
                                <td>{{$order->vin}}</td>
                                <td>{{$order->carModel->name}}</td>
                                <td>{{$order->arrive_date}}</td>
                                <td class="center">
                                @if($order->statusOrder->name == \Config::get('constants.options.ORDER_ACCEPTED'))
                                            <span class="label label-success">Aceptada</span>
                                            @endif
                                        
                                            @if($order->statusOrder->name == \Config::get('constants.options.ORDER_REJECTED'))
                                            <span class="label label-danger">Rechazada</span>
                                            @endif
                                        
                                            @if($order->statusOrder->name == \Config::get('constants.options.ORDER_INPROCESS'))
                                            <span class="label label-info">En proceso</span>
                                @endif
                                </td>
                                <td class="center">{{$order->carrier->name}}</td>
                                @if(Auth::user()->rol->name == \Config::get('constants.options.ROL_ADMINISTRATOR'))
                                <td class="center">{{$order->dealer->commercial_name}}</td>
                                @endif
                                <td class="center">
                                        <div class="btn-group">
                                            <a href="{{URL::to('/claims')}}/{{$order->id}}"><button class="btn-white btn btn-xs">Ver</button></a>
                                            <button class="btn-white btn btn-xs">Editar</button>
                                        </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                    <tfoot>
                    <tr>
                        <th>Vin</th>
                        <th>Modelo</th>
                        <th>Fecha arribo</th>
                        <th>Status</th>
                        <th>Carrier</th>
                        @if(Auth::user()->rol->name == \Config::get('constants.options.ROL_ADMINISTRATOR'))
                        <th>Dealer</th>
                        @endif
                        <th>Men&uacute;</th>
                    </tr>
                    </tfoot>
                    </table>
                        </div>

                    </div>
                </div>
            </div>
            </div>

            </div>
    </div>

@endsection


@section('styles')
<link href="{{asset('css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
@endsection

@section('scripts')
<script> var path = "{{URL::to('/')}}";</script>
<script src="{{asset('js/plugins/jeditable/jquery.jeditable.js')}}"></script>
<script src="{{asset('js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
    <!-- Controller -->
<script src="{{asset('js/scripts/viewclaims_accepted.controller.js')}}"></script>
@endsection
