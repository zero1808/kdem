<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'KIA') }}</title>

        <link href="{{asset('css/bootstrap.min.css')}}" rel="stylesheet">
        <link href="{{asset('font-awesome/css/font-awesome.css')}}" rel="stylesheet">

        <!-- FooTable -->
        <link href="{{asset('css/plugins/footable/footable.core.css')}}" rel="stylesheet">

        <link href="{{asset('css/animate.css')}}" rel="stylesheet">
        <link href="{{asset('css/style.css')}}" rel="stylesheet">

        <!-- Scripts -->

        <!-- Fonts -->
        <link rel="dns-prefetch" href="https://fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Raleway:300,400,600" rel="stylesheet" type="text/css">

        @yield('styles')


    </head>

    <body>

        <div id="wrapper">

            <nav class="navbar-default navbar-static-side" role="navigation">
                <div class="sidebar-collapse">
                    <ul class="nav metismenu" id="side-menu">
                        <li class="nav-header">
                            <div class="dropdown profile-element"> <span>
                                    <img alt="image" class="img-circle" src="{{asset('img/profile_small.png')}}" />
                                </span>
                                <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                                    <span class="clear"> <span class="block m-t-xs">
                                            <strong class="font-bold">
                                                @if(isset(Auth::user()->dealer))
                                                {{ Auth::user()->dealer->commercial_name }}
                                                @else 
                                                {{ Auth::user()->code }}
                                                @endif
                                            </strong>
                                        </span> <span class="text-muted text-xs block">
                                            @if(\Auth::user()->rol->name == \Config::get('constants.options.ROL_OPERATOR'))
                                            Dealer
                                            @elseif(\Auth::user()->rol->name == \Config::get('constants.options.ROL_ADMINISTRATOR'))
                                            Administrator
                                            @endif
                                            <b class="caret"></b></span></span></a>
                                <ul class="dropdown-menu animated fadeInRight m-t-xs">
                                    <li><a href="profile.html">Perfil</a></li>
                                    <li><a href="{{ route('logout') }}"
                                           onclick="event.preventDefault();
                                               document.getElementById('logout-form').submit();">Cerrar sesión</a></li>
                                </ul>
                            </div>
                            <div class="logo-element">
                                KIA
                            </div>
                        </li>

                        <li id="claims_menu">
                            <a href="#"><i class="fa fa-table"></i> <span class="nav-label">Reclamaciones</span><span class="fa arrow"></span></a>
                            <ul class="nav nav-second-level collapse">
                                <li id="claims_inprocess_option"><a href="{{ URL::to('/home') }}">En proceso</a></li>
                                <li><a href="{{URL::to('/orders/accepted')}}">Aceptadas</a></li>
                                <li><a href="{{URL::to('/orders/rejected')}}">Rechazadas</a></li>
                                @if(Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR'))
                                <li><a href="{{URL::to('/orders/saved')}}">Guardadas</a></li>
                                @endif
                            </ul>
                        </li>
                        @if(Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR'))
                        <li>
                            <a href="{{URL::to('/claims/create')}}" id="new_claim"><i class="fa fa-edit"></i> <span class="nav-label">Registro</span></a>
                        </li>
                        @endif

                        @if(Auth::user()->rol->name == Config::get('constants.options.ROL_ADMINISTRATOR'))
                        <li id="charts_menu">
                            <a href="#"><i class="fa fa-pie-chart"></i> <span class="nav-label">Estad&iacute;sticas</span>  <span class="pull-right label label-primary">New</span></a>
                            <ul class="nav nav-second-level collapse">

                            </ul>
                        </li>
                        @endif


                    </ul>

                </div>
            </nav>

            <div id="page-wrapper" class="gray-bg">
                <div class="row border-bottom">
                    <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                        <div class="navbar-header">
                            <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i class="fa fa-bars"></i> </a>
                            <form role="search" class="navbar-form-custom" action="search_results.html">
                                <div class="form-group">
                                    <input type="text" placeholder="Search for something..." class="form-control" name="top-search" id="top-search">
                                </div>
                            </form>
                        </div>
                        <ul class="nav navbar-top-links navbar-right">
                            <li>
                                <span class="m-r-sm text-muted welcome-message">Bienvenido!
                                    @if(isset(Auth::user()->dealer))
                                    <strong>{{ Auth::user()->dealer->commercial_name }}</strong>
                                    @else 
                                    <strong>{{ Auth::user()->code }}</strong>
                                    @endif</span>
                            </li>



                            @guest
                            <li><a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a></li>
                            <li><a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a></li>
                            @else
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                            <li>
                                <a href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                       document.getElementById('logout-form').submit();">
                                    <i class="fa fa-sign-out"></i> {{ __('Cerrar sesión') }}
                                </a>
                            </li>

                            @endguest
                        </ul>

                    </nav>
                </div>
                <main class="py-4">
                    @yield('content')
                </main>    
                <div class="footer">
                    <div class="pull-right">
                        <strong> Claim Solution Version 1.01 </strong> 
                    </div>
                    <div>
                        <strong>KIA</strong> 2018
                    </div>
                </div>

            </div>
        </div>


        <script> var numberImpresion = "{{Auth::user()->rol->name}}";</script>
        <!-- Mainly scripts -->

        <script src="{{asset('js/jquery-2.1.1.js')}}"></script>
        <script src="{{asset('js/bootstrap.min.js')}}"></script>
        <script src="{{asset('js/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>

        <!-- Custom and plugin javascript -->
        <script src="{{asset('js/inspinia.js')}}"></script>
        <script src="{{asset('js/plugins/pace/pace.min.js')}}"></script>

        <!-- FooTable -->
        <script src="{{asset('js/plugins/footable/footable.all.min.js')}}"></script>
        @yield('scripts')


        <!-- Page-Level Scripts -->

    </body>

</html>
