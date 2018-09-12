<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\StatusOrder;
use Auth;
use App\Dealer;
use Config;
use URL;

class ClaimTableController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    public function allClaims(Request $request) {
        if (Auth::user()->rol->name == Config::get('constants.options.ROL_ADMINISTRATOR')) {
            $columns = array(
                0 => 'vin',
                1 => 'model',
                2 => 'carrier',
                3 => 'arrive_date',
                4 => 'dealer',
                5 => 'id',
            );
        }

        if (Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')) {
            $columns = array(
                0 => 'vin',
                1 => 'model',
                2 => 'carrier',
                3 => 'arrive_date',
                4 => 'id',
            );
        }


        if (Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')) {
            $dealer = Dealer::where('idUsuario', '=', Auth::user()->id)->first();
            $dealerId = $dealer->id;
        }
        $idStatus = StatusOrder::where('name', '=', $request->code_table)->first();

        if (Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')) {
            $query = DB::table('orders')->select('orders.id', 'orders.vin', 'car_models.name as model', 'carriers.name as carrier', 'orders.arrive_date', 'orders.arrive_date_time')
                            ->where('orders.status', '=', $request->status_table)
                            ->where('orders.idStatus', '=', $idStatus->id)
                            ->where('idDealer', '=', $dealerId)
                            ->join('car_models', 'orders.idModelo', '=', 'car_models.id')
                            ->leftJoin('carriers', 'orders.idCarrier', '=', 'carriers.id')->get();
        }

        if (Auth::user()->rol->name == Config::get('constants.options.ROL_ADMINISTRATOR')) {
            $query = DB::table('orders')->select('orders.id', 'orders.vin', 'car_models.name as model', 'carriers.name as carrier', 'orders.arrive_date', 'orders.arrive_date_time', 'dealers.commercial_name as dealer')
                            ->where('orders.status', '=', $request->status_table)
                            ->where('orders.idStatus', '=', $idStatus->id)
                            ->join('car_models', 'orders.idModelo', '=', 'car_models.id')
                            ->join('dealers', 'orders.idDealer', '=', 'dealers.id')
                            ->leftJoin('carriers', 'orders.idCarrier', '=', 'carriers.id')->get();
        }


        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {

            if (Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')) {
                $orders = DB::table('orders')->select('orders.id', 'orders.vin', 'car_models.name as model', 'carriers.name as carrier', 'orders.arrive_date', 'orders.arrive_date_time')
                        ->where('orders.status', '=', $request->status_table)
                        ->where('orders.idStatus', '=', $idStatus->id)
                        ->where('orders.idDealer', '=', $dealerId)
                        ->join('car_models', 'orders.idModelo', '=', 'car_models.id')
                        ->leftJoin('carriers', 'orders.idCarrier', '=', 'carriers.id')
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
            }

            if (Auth::user()->rol->name == Config::get('constants.options.ROL_ADMINISTRATOR')) {
                $orders = DB::table('orders')->select('orders.id', 'orders.vin', 'car_models.name as model', 'carriers.name as carrier', 'orders.arrive_date', 'orders.arrive_date_time', 'dealers.commercial_name as dealer')
                        ->where('orders.status', '=', $request->status_table)
                        ->where('orders.idStatus', '=', $idStatus->id)
                        ->join('car_models', 'orders.idModelo', '=', 'car_models.id')
                        ->leftJoin('carriers', 'orders.idCarrier', '=', 'carriers.id')
                        ->join('dealers', 'orders.idDealer', '=', 'dealers.id')
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
            }
        } else {
            $search = $request->input('search.value');


            if (Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')) {
                $orders = DB::table('orders')->select('orders.id', 'orders.vin', 'car_models.name as model', 'carriers.name as carrier', 'orders.arrive_date', 'orders.arrive_date_time')
                        ->where(function($query) use($request, $idStatus, $dealerId) {
                            $query->where('orders.status', '=', $request->status_table);
                            $query->where('orders.idStatus', '=', $idStatus->id);
                            $query->where('orders.idDealer', '=', $dealerId);
                        })->where(function($query) use($search) {
                            $query->where('orders.vin', 'LIKE', "%{$search}%");
                            $query->orWhere('carriers.name', 'LIKE', "%{$search}%");
                            $query->orWhere('car_models.name', 'LIKE', "%{$search}%");
                        })
                        ->join('car_models', 'orders.idModelo', '=', 'car_models.id')
                        ->leftJoin('carriers', 'orders.idCarrier', '=', 'carriers.id')
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
            }

            if (Auth::user()->rol->name == Config::get('constants.options.ROL_ADMINISTRATOR')) {
                $orders = DB::table('orders')->select('orders.id', 'orders.vin', 'car_models.name as model', 'carriers.name as carrier', 'orders.arrive_date', 'orders.arrive_date_time', 'dealers.commercial_name as dealer')
                        ->where(function($query) use($request, $idStatus) {
                            $query->where('orders.status', '=', $request->status_table);
                            $query->where('orders.idStatus', '=', $idStatus->id);
                        })->where(function($query) use($search) {
                            $query->where('orders.vin', 'LIKE', "%{$search}%");
                            $query->orWhere('carriers.name', 'LIKE', "%{$search}%");
                            $query->orWhere('car_models.name', 'LIKE', "%{$search}%");
                            $query->orWhere('dealers.commercial_name', 'LIKE', "%{$search}%");
                        })
                        ->join('car_models', 'orders.idModelo', '=', 'car_models.id')
                        ->leftJoin('carriers', 'orders.idCarrier', '=', 'carriers.id')
                        ->join('dealers', 'orders.idDealer', '=', 'dealers.id')
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order, $dir)
                        ->get();
            }

            $totalFiltered = $orders->count();
        }

        $data = array();
        if (!empty($orders)) {
            foreach ($orders as $order) {
                $show = route('claims.show', $order->id);
                $edit = route('claims.edit', $order->id);
                $delete = route('claims.destroy', $order->id);
                $showSaved = URL::to('/order/saved/' . $order->id);
                $printUrl = URL::to('/claimreport/' . $order->id);
                $nestedData['vin'] = $order->vin;
                $nestedData['model'] = $order->model;
                $nestedData['carrier'] = $order->carrier;
                $nestedData['arrive_date'] = $order->arrive_date . " " . $order->arrive_date_time;
                if (Auth::user()->rol->name == Config::get('constants.options.ROL_ADMINISTRATOR')) {
                $nestedData['dealer'] = $order->dealer;
                }

                $baseUrlOptions = "";
                if ($request->status_table == Config::get('constants.options.CLAIM_LEVEL_STORE') && Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')) {
                    $baseUrlOptions = "<button class='btn-white btn btn-xs ' onClick='deleteClaim({$order->id})'><i class='fa fa-trash'>&nbsp;Borrar</i></button>";
                }

                if ($request->status_table == Config::get('constants.options.CLAIM_LEVEL_STORE')) {
                    $baseUrlOptions = $baseUrlOptions . "<a href='" . $showSaved . "' class='btn-white btn btn-xs'><i class='fa fa-edit'>&nbsp;Editar</i></a>";
                }
                if ($request->status_table == Config::get('constants.options.CLAIM_LEVEL_UP')) {
                    $baseUrlOptions = $baseUrlOptions . "<a href='" . $printUrl . "' class='btn-white btn btn-xs' target='_blank'><i class='fa fa-print'>&nbsp;Ficha</i></a>";
                }
                $holerOptions = "<div class='btn-group'>" . $baseUrlOptions . "</div>";
                $nestedData['options'] = $holerOptions;
                $data[] = $nestedData;
            }
        }

        $json_data = array(
            "draw" => intval($request->input('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data" => $data
        );

        echo json_encode($json_data);
    }

}
