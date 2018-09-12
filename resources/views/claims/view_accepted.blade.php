@extends('layouts.navbar')

@section('content')


<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>Reclamaciones</h2>
        <ol class="breadcrumb">
            <li>
                <a>Inicio</a>
            </li>
            <li>
                <a>Reclamaciones</a>
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
                        <input type="hidden" id="code_table" name="code_table" value="{{$code_table}}"/>
                        <input type="hidden" id="status_table" name="status_table" value="{{$status_table}}"/>
                        @include('claims.table')
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
<link href="{{asset('css/plugins/dataTables/datatables.min.css')}}" rel="stylesheet">
<link href="{{asset('css/plugins/sweetalert/sweetalert.css')}}" rel="stylesheet">
@endsection

@section('scripts')
<script>
    var path = "{{URL::to('/')}}";
</script>
<script src="{{asset('js/plugins/jeditable/jquery.jeditable.js')}}"></script>
<script src="{{asset('js/plugins/dataTables/datatables.min.js')}}"></script>
<script src="{{asset('js/plugins/metisMenu/jquery.metisMenu.js')}}"></script>
<script src="{{asset('js/plugins/sweetalert/sweetalert.min.js')}}"></script>
<script src="{{asset('js/common/spinners.js')}}"></script>
<!-- Controller -->
<script src="{{asset('js/common/claims_table.controller.js')}}"></script>
<script src="{{asset('js/scripts/claim.controller.js')}}"></script>
@endsection
