<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Order;
use App\StatusOrder;
use Config;

class HomeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
         if (Auth::user()->rol->name == Config::get('constants.options.ROL_ADMINISTRATOR')) {
            return view('claims/view_inprocess')->with('code_table', Config::get('constants.options.ORDER_INPROCESS'))
                        ->with('status_table', Config::get('constants.options.CLAIM_LEVEL_UP'));
        } else if (Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')) {
            if (isset(Auth::user()->dealer)) {
                        return view('claims/view_inprocess')->with('code_table', Config::get('constants.options.ORDER_INPROCESS'))
                        ->with('status_table', Config::get('constants.options.CLAIM_LEVEL_UP'));
            }
        }
    }

    public function getAccepted() {
        if (Auth::user()->rol->name == Config::get('constants.options.ROL_ADMINISTRATOR')) {
            return view('claims/view_inprocess')->with('code_table', Config::get('constants.options.ORDER_ACCEPTED'))
                        ->with('status_table', Config::get('constants.options.CLAIM_LEVEL_UP'));
        } else if (Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')) {
            if (isset(Auth::user()->dealer)) {
                        return view('claims/view_accepted')->with('code_table', Config::get('constants.options.ORDER_ACCEPTED'))
                        ->with('status_table', Config::get('constants.options.CLAIM_LEVEL_UP'));
            }
        }
    }

    public function getRejected() {
        if (Auth::user()->rol->name == Config::get('constants.options.ROL_ADMINISTRATOR')) {
            return view('claims/view_inprocess')->with('code_table', Config::get('constants.options.ORDER_REJECTED'))
                        ->with('status_table', Config::get('constants.options.CLAIM_LEVEL_UP'));
        } else if (Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')) {
            if (isset(Auth::user()->dealer)) {
                        return view('claims/view_rejected')->with('code_table', Config::get('constants.options.ORDER_REJECTED'))
                        ->with('status_table', Config::get('constants.options.CLAIM_LEVEL_UP'));
            }
        }
    }

    public function getSaved() {
        return view('claims/view_saved')->with('code_table', Config::get('constants.options.ORDER_INPROCESS'))
                        ->with('status_table', Config::get('constants.options.CLAIM_LEVEL_STORE'));
    }

}
