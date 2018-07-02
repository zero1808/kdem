<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\CarModel;
use App\DamageArea;
use App\Damage;
use App\Carrier;
use App\StatusOrder;
use Validator;
use App\DamageOrder;
use App\TemporalPayment;
use App\Utilities\HttpResponseInterface;
use Auth;

class ClaimController extends Controller {

    protected $response;

    public function __construct(HttpResponseInterface $response) {
        $this->response = $response;
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        $models = CarModel::all();
        $damageAreas = DamageArea::all();
        $damageTypes = Damage::all();
        $carriers = Carrier::all();
        $statusOrders = StatusOrder::all();
        return view('/claims/new')->with('models', $models)
                        ->with('damageAreas', $damageAreas)
                        ->with('damages', $damageTypes)
                        ->with('carriers', $carriers)
                        ->with('statusOrders', $statusOrders);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //
        if ($this->validateClaim($request)->fails()) {
            return $this->response->validationErrorResponse($this->validateClaim($request)->messages());
        }

        $claim = new Order();
        $claim->fill([
            'vin' => $request->vin,
            'idModelo' => $request->car_model,
            'idDealer' => Auth::user()->dealer->id,
            'arrive_date' => $request->arrive_date,
            'report_date' => $request->report_date,
            'idStatus' => $request->status,
            'idCarrier' => $request->carrier
        ]);
        $claim->save();
        if ($claim->id != null) {
            if ($this->saveTemporalPayment($request, $claim->id) && $this->saveDamageOrder($request, $claim->id)) {
                return $this->response->successResponse($claim->vin);
            } else {
                $claim->delete();
                return $this->response->internalServerErrorResponse($claim->vin);
            }
        } else {
            $claim->delete();
            return $this->response->internalServerErrorResponse($claim->vin);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
        $claim = Order::find($id);
        if ($claim == null || !is_numeric($id)) {
            $error = "No existe ninguna orden con la información proporcionada.";
            return view('/claims/holder')->with('error', $error);
        }

        return view('/claims/holder')->with('order', $claim);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        //
    }

    private function validateClaim(Request $request) {

        return Validator::make($request->all(), [
                    'amount_smx' => 'nullable|regex:/^\d*(\.\d{2})?$/',
                    'amount_gmx' => 'nullable|regex:/^\d*(\.\d{2})?$/',
                    'carrier' => 'required|integer|exists:carriers,id',
                    'status' => 'required|integer|exists:status_orders,id',
                    'damage_type' => 'required|integer|exists:damages,id',
                    'damage_area' => 'required|integer|exists:damage_areas,id',
                    'arrive_date' => 'required|date|date_format:Y-m-d',
                    'report_date' => 'nullable|date|date_format:Y-m-d',
                    'vin' => 'required|string|alpha_dash',
                    'car_model' => 'required|integer|exists:car_models,id'
        ]);
    }

    private function saveTemporalPayment(Request $request, $id) {
        $temporalPayment = new TemporalPayment();
        $temporalPayment->amount_smx = $request->amount_smx;
        $temporalPayment->amount_gmx = $request->amount_gmx;
        $temporalPayment->idOrder = $id;
        $temporalPayment->save();
        if ($temporalPayment->id != null) {
            return true;
        } else {
            return false;
        }
    }

    private function saveDamageOrder(Request $request, $id) {
        $damageOrder = new DamageOrder();
        $damageOrder->idDamageArea = $request->damage_area;
        $damageOrder->idDamage = $request->damage_type;
        $damageOrder->idOrder = $id;
        $damageOrder->save();
        if ($damageOrder->id != null) {
            return true;
        } else {
            return false;
        }
    }

}
