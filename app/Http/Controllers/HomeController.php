<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\StatusOrder;
use Config;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $idStatus = StatusOrder::where('name','=', Config::get('constants.options.ORDER_INPROCESS'))->get();
        $idStatus = $idStatus[0]->id;
        $claims = [];
        
        if(Auth::user()->rol->name == Config::get('constants.options.ROL_ADMINISTRATOR')){
          $claims = Order::where('idStatus','=',$idStatus)->get();  
        }else if(Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')){
           if(isset(Auth::user()->dealer)){
               $claims = Auth::user()->dealer->orders->where('idStatus','=',$idStatus);
           }
        }
        if(Auth::user()->rol->name == Config::get('constants.options.ROL_ADMINISTRATOR')){
            return view('claims/view_inprocess')->with('orders_process',$claims);
        }else if(Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')){
            return view('claims/view_inprocess')->with('orders_process',$claims);
        }
    }
    
    public function getAccepted(){
        $idStatus = StatusOrder::where('name','=', Config::get('constants.options.ORDER_ACCEPTED'))->get();
        $idStatus = $idStatus[0]->id;
        $claims = null;
        if(Auth::user()->rol->name == Config::get('constants.options.ROL_ADMINISTRATOR')){
          $claims = Order::where('idStatus','=',$idStatus)->get();  
        }else if(Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')){
           if(isset(Auth::user()->dealer)){
               $claims = Auth::user()->dealer->orders->where('idStatus','=',$idStatus);
           }
        }
    
        return view('claims/view_accepted')->with('orders_accepted',$claims);
        
    }
    
    public function getRejected(){
        $idStatus = StatusOrder::where('name','=', Config::get('constants.options.ORDER_REJECTED'))->get();
        $idStatus = $idStatus[0]->id;
        $claims = null;
        if(Auth::user()->rol->name == Config::get('constants.options.ROL_ADMINISTRATOR')){
          $claims = Order::where('idStatus','=',$idStatus)->get();  
        }else if(Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')){
           if(isset(Auth::user()->dealer)){
               $claims = Auth::user()->dealer->orders->where('idStatus','=',$idStatus);
           }
        }
    
        return view('claims/view_rejected')->with('orders_rejected',$claims);
        
    }
}
