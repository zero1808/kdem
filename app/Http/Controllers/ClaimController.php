<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Order;
use App\CarModel;
use App\Carrier;
use App\StatusOrder;
use Validator;
use App\DamageOrder;
use App\TemporalPayment;
use App\Utilities\HttpResponseInterface;
use Auth;
use Carbon\Carbon;
use App\DamageQuotation;
use Config;
use App\Libraries\Utils;
use Storage;

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
        $carriers = Carrier::all();
        $statusOrders = StatusOrder::all();
        return view('/claims/new')->with('models', $models)
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

        $request->carrier = $request->carrier == 0 ? null : $request->carrier;

        /* Validations */
        if ($this->validateClaim($request)->fails()) {
            return $this->response->validationErrorResponse($this->validateClaim($request)->messages());
        }

        for ($ordersIndex = 0; $ordersIndex < $request->i_damage; $ordersIndex ++) {
            if (!$this->validateQuotation($request, ($ordersIndex + 1))) {
                return $this->response->internalServerErrorResponse("Error en la cotización de los daños.\nCódigo 002-" . ($ordersIndex + 1));
            }
        }
        /* End of validations */

        $idStatus = StatusOrder::where('name', '=', Config::get('constants.options.ORDER_INPROCESS'))->get();
        if (!empty($idStatus)) {
            $idStatus = $idStatus[0]->id;
        } else {
            $idStatus = null;
        }
        $carry_letter = null;
        $checklist = null;
        if ($request->file('carry_letter_input') == null) {
            $carry_letter = null;
        } else {
            $path_post_carry = $this->storeCarryLetter($request);
            if ($path_post_carry != null) {
                $carry_letter = $path_post_carry;
            } else {
                return $this->response->internalServerErrorResponse("Ocurrio un error al guardar el Claim, Codigo 500-1.");
            }
        }
        if ($request->file('checklist_input') == null) {
            $checklist = null;
        } else {
            $path_post_checklist = $this->storeChecklist($request);
            if ($path_post_checklist != null) {
                $checklist = $path_post_checklist;
            } else {
                return $this->response->internalServerErrorResponse("Ocurrio un error al guardar el Claim, Codigo 500-2.");
            }
        }

        $claim = new Order();


        $status_action = $request->will_be_saved == "1" ? Config::get('constants.options.CLAIM_LEVEL_STORE') : Config::get('constants.options.CLAIM_LEVEL_UP');
        $arrive_date_time = Carbon::createFromFormat('H:i:s', $request->arrive_date_time . ":00", 'America/Mexico_City');
        $claim->fill([
            'vin' => $request->vin,
            'idModelo' => $request->car_model,
            'idDealer' => Auth::user()->dealer->id,
            'arrive_date' => $request->arrive_date,
            'arrive_date_time' => $arrive_date_time,
            'report_date' => Carbon::now()->toDateTimeString(),
            'src_carry_letter' => $carry_letter,
            'src_checklist' => $checklist,
            'idStatus' => $idStatus,
            'idCarrier' => $request->carrier,
            'responsable_name' => $request->responsable_name,
            'responsable_phone' => $request->responsable_phone,
            'responsable_email' => $request->responsable_email,
            'status' => $status_action
        ]);
        try {
            $claim->save();
        } catch (\Exception $e) {
            return $this->response->internalServerErrorResponse($e->getMessage());
        }
        if ($claim->id != null) {
            //if ($this->saveTemporalPayment($request, $claim->id) && $this->saveDamageOrder($request, $claim->id)) {
            $transactionStatus = true;
            for ($ordersIndex = 0; $ordersIndex < $request->i_damage; $ordersIndex ++) {
                if (!$this->saveDamageOrder($request, $claim->id, ($ordersIndex + 1))) {
                    $transactionStatus = false;
                }
            }
            if ($transactionStatus) {
                $response["claimStored"] = $claim->id;
                $response["claimVin"] = $claim->vin;
                return $this->response->successResponse($response);
            } else {
                $claim->delete();
                return $this->response->internalServerErrorResponse($claim->vin);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        if (!is_numeric($id)) {
            $error = "No se pudo consultar la reclamación con id " . $id . " Código 600.";
            return $this->response->internalServerErrorResponse($error);
        }

        try {
            $claim = Order::find($id);
        } catch (\Exception $e) {
            $error = "No se pudo consultar la reclamación con id " . $id . " Código 601. \n" . $e->getMessage();
            return $this->response->internalServerErrorResponse($error);
        }

        if ($claim == null) {
            $error = "No existe ninguna reclamación con la información proporcionada.";
            return $this->response->internalServerErrorResponse($error);
        }

        if (Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')) {
            if (Auth::user()->id != $claim->dealer->idUsuario) {
                $error = "No tienes acceso a esta reclamación.";
                return $this->response->internalServerErrorResponse($error);
            }
            $claim->carrier;
            $claim->carModel;
            $claim->claimPics;

            foreach ($claim->damageOrders as $damage) {
                $damage->damage;
                $damage->damageArea;
                $damage->damageSeverity;
                $damage->damageQuotation;
                $damage->damageQuotation->amount_paint = number_format((float) $damage->damageQuotation->amount_paint, 2, '.', '');
                $damage->damageQuotation->amount_hand = number_format((float) $damage->damageQuotation->amount_hand, 2, '.', '');
                $damage->damageQuotation->amount_pieces = number_format((float) $damage->damageQuotation->amount_pieces, 2, '.', '');
                $damage->damageQuotation->iva = number_format((float) $damage->damageQuotation->iva, 2, '.', '');
                $damage->damageQuotation->subtotal = number_format((float) $damage->damageQuotation->subtotal, 2, '.', '');
                $damage->damageQuotation->total = number_format((float) $damage->damageQuotation->total, 2, '.', '');
            }
            $response["claim"] = $claim;

            return $this->response->successResponse($response);
        }
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
        $request->carrier = $request->carrier == 0 ? null : $request->carrier;

        /* Validations */
        if ($this->validateClaim($request)->fails()) {
            return $this->response->validationErrorResponse($this->validateClaim($request)->messages());
        }

        if (!is_numeric($id)) {
            return $this->response->internalServerErrorResponse("Error al intentar actualizar la reclamación. \nCódigo 701 - " . ($id));
        }

        $claim = Order::find($id);

        if ($claim == null) {
            return $this->response->internalServerErrorResponse("Error al intentar actualizar la reclamación. \nCódigo 702 - " . ($id));
        }

        if (Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')) {
            if (Auth::user()->id != $claim->dealer->idUsuario) {
                return $this->response->internalServerErrorResponse("No tienes acceso a esta reclamación. \nCódigo 603.");
            }
        }


        for ($ordersIndex = 0; $ordersIndex < $request->i_damage; $ordersIndex ++) {
            if (!$this->validateQuotation($request, ($ordersIndex + 1))) {
                return $this->response->internalServerErrorResponse("Error en la cotización de los daños.\nCódigo 002-" . ($ordersIndex + 1));
            }
        }
        /* End of validations */

        $idStatus = StatusOrder::where('name', '=', Config::get('constants.options.ORDER_INPROCESS'))->get();
        if (!empty($idStatus)) {
            $idStatus = $idStatus[0]->id;
        } else {
            $idStatus = null;
        }
        $carry_letter = null;
        $checklist = null;
        if ($request->file('carry_letter_input') == null) {
            $carry_letter = $claim->src_carry_letter;
        } else {
            $path_post_carry = $this->storeCarryLetter($request);
            if ($path_post_carry != null) {
                $carry_letter = $path_post_carry;
                $this->deleteCarryLetter($id);
            } else {
                return $this->response->internalServerErrorResponse("Ocurrio un error al guardar el Claim, Codigo 500-1.");
            }
        }
        if ($request->file('checklist_input') == null) {
            $checklist = $claim->src_checklist;
            ;
        } else {
            $path_post_checklist = $this->storeChecklist($request);
            if ($path_post_checklist != null) {
                $checklist = $path_post_checklist;
                $this->deleteChecklist($id);
            } else {
                return $this->response->internalServerErrorResponse("Ocurrio un error al guardar el Claim, Codigo 500-2.");
            }
        }

        $status_action = $request->will_be_saved == "1" ? Config::get('constants.options.CLAIM_LEVEL_STORE') : Config::get('constants.options.CLAIM_LEVEL_UP');
        $arrive_date_time = Carbon::createFromFormat('H:i:s', $request->arrive_date_time . ":00", 'America/Mexico_City');
        $claim->vin = $request->vin;
        $claim->idModelo = $request->car_model;
        $claim->idDealer = Auth::user()->dealer->id;
        $claim->arrive_date = $request->arrive_date;
        $claim->arrive_date_time = $arrive_date_time;
        $claim->report_date = Carbon::now()->toDateTimeString();
        $claim->src_carry_letter = $carry_letter;
        $claim->src_checklist = $checklist;
        $claim->idStatus = $idStatus;
        $claim->idCarrier = $request->carrier;
        $claim->responsable_name = $request->responsable_name;
        $claim->responsable_phone = $request->responsable_phone;
        $claim->responsable_email = $request->responsable_email;
        $claim->status = $status_action;
        try {
            $claim->save();
        } catch (\Exception $e) {
            return $this->response->internalServerErrorResponse($e->getMessage());
        }
        if ($claim->id != null) {
            $this->deleteDamageOrders($claim->id);
            $transactionStatus = true;
            for ($ordersIndex = 0; $ordersIndex < $request->i_damage; $ordersIndex ++) {
                if (!$this->saveDamageOrder($request, $claim->id, ($ordersIndex + 1))) {
                    $transactionStatus = false;
                }
            }
            if ($transactionStatus) {
                $response["claimStored"] = $claim->id;
                $response["claimVin"] = $claim->vin;
                return $this->response->successResponse($response);
            } else {
                $claim->delete();
                return $this->response->internalServerErrorResponse($claim->vin);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        if (!is_numeric($id)) {
            return $this->response->internalServerErrorResponse("Error Codigo 001");
        }

        $claim = Order::find($id);

        if ($claim == null) {
            return $this->response->internalServerErrorResponse("Error Codigo 002");
        }

        if (Auth::user()->rol->name == Config::get('constants.options.ROL_OPERATOR')) {
            if (Auth::user()->id != $claim->dealer->idUsuario) {
                return $this->response->internalServerErrorResponse("Error Codigo 003");
            }
            try {
                $response["claimStored"] = $claim->id;
                $response["claimVin"] = $claim->vin;
                $this->deleteCarryLetter($claim->id);
                $this->deleteChecklist($claim->id);
                $this->deleteClaimPics($claim->id);
                $claim->delete();
                return $this->response->successResponse($response);
            } catch (\Exception $e) {
                return $this->response->internalServerErrorResponse("Error Codigo 004");
            }
        }
    }

    private function validateClaim(Request $request) {
        $iDamages = $request->i_damage;
        $arrayToValidate = [
            "will_be_saved" => "required|integer|digits_between:0,1",
            'amount_smx' => 'nullable|regex:/^\d*(\.\d{2})?$/',
            'amount_gmx' => 'nullable|regex:/^\d*(\.\d{2})?$/',
            'carrier' => $request->will_be_saved == 0 ? 'required|integer|exists:carriers,id' : 'integer|nullable',
            'arrive_date' => 'required_if:will_be_saved,0|date|date_format:Y-m-d|nullable',
            'arrive_date_time' => 'nullable|date_format:H:i',
            'report_date' => 'nullable|date|date_format:Y-m-d H:i:s',
            'vin' => 'required|string|alpha_dash|size:17|confirmed',
            'car_model' => 'required|integer|exists:car_models,id',
            "carry_letter" => "nullable|mimes:pdf|max:10000",
            "checklist" => "nullable|mimes:pdf|max:10000",
            "i_damage" => "required|integer|min:1",
            "responsable_name" => "required_if:will_be_saved,0|string|nullable|max:55",
            'responsable_phone' => $request->will_be_saved == 0 ? "required_without_all:responsable_email|integer|digits:10|nullable" : "integer|digits:10|nullable",
            'responsable_email' => $request->will_be_saved == 0 ? 'required_without_all:responsable_phone|email|nullable' : 'email|nullable|max:30',
        ];

        for ($i = 0; $i < $iDamages; $i++) {
            $field_amount_pieces = "amount_pieces_" . ($i + 1);
            $field_amount_paint = "amount_paint_" . ($i + 1);
            $field_amount_hand = "amount_hand_" . ($i + 1);
            $field_amount_reparation = "amount_reparation_" . ($i + 1);
            $field_amount_subtotal = "amount_subtotal_" . ($i + 1);
            $field_amount_total = "amount_total_" . ($i + 1);
            $field_damage = "damage_type_" . ($i + 1);
            $field_area = "damage_area_" . ($i + 1);
            $field_severity = "damage_severity_" . ($i + 1);
            $arrayToValidate += array($field_damage => "required|integer|exists:damages,id");
            $arrayToValidate += array($field_area => "required|integer|exists:damage_areas,id");
            $arrayToValidate += array($field_severity => "required|integer|exists:damage_severities,id");
            $arrayToValidate += array($field_amount_pieces => "required|regex:/^\d*(\.\d{2})?$/");
            $arrayToValidate += array($field_amount_paint => "required|regex:/^\d*(\.\d{2})?$/");
            $arrayToValidate += array($field_amount_hand => "required|regex:/^\d*(\.\d{2})?$/");
            $arrayToValidate += array($field_amount_reparation => "required|regex:/^\d*(\.\d{2})?$/");
            $arrayToValidate += array($field_amount_subtotal => "required|regex:/^\d*(\.\d{2})?$/");
            $arrayToValidate += array($field_amount_total => "required|regex:/^\d*(\.\d{2})?$/");
        }
        return Validator::make($request->all(), $arrayToValidate);
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

    private function saveDamageOrder(Request $request, $id, $i) {
        $amount_pieces = "amount_pieces_" . $i;
        $amount_paint = "amount_paint_" . $i;
        $amount_hand = "amount_hand_" . $i;
        $amount_iva = "amount_reparation_" . $i;
        $amount_subtotal = "amount_subtotal_" . $i;
        $amount_total = "amount_total_" . $i;
        $damage_area = "damage_area_" . $i;
        $damage_type = "damage_type_" . $i;
        $damage_severity = "damage_severity_" . $i;

        $damageOrder = new DamageOrder();
        $damageOrder->idDamageArea = $request->$damage_area;
        $damageOrder->idDamage = $request->$damage_type;
        $damageOrder->idSeverity = $request->$damage_severity;
        $damageOrder->idOrder = $id;
        $damageOrder->save();

        if ($damageOrder->id == null) {
            return false;
        }

        $damageQuotation = new DamageQuotation();
        $damageQuotation->idDamageOrder = $damageOrder->id;
        $damageQuotation->amount_pieces = $request->$amount_pieces;
        $damageQuotation->amount_paint = $request->$amount_paint;
        $damageQuotation->amount_hand = $request->$amount_hand;
        $damageQuotation->iva = $request->$amount_iva;
        $damageQuotation->subtotal = $request->$amount_subtotal;
        $damageQuotation->total = $request->$amount_total;
        $damageQuotation->save();

        if ($damageQuotation->id != null) {
            return true;
        } else {
            return false;
        }
    }

    private function validateQuotation($quotation, $i) {
        $amount_pieces = "amount_pieces_" . $i;
        $amount_paint = "amount_paint_" . $i;
        $amount_hand = "amount_hand_" . $i;
        $amount_iva = "amount_reparation_" . $i;
        $amount_subtotal = "amount_subtotal_" . $i;
        $amount_total = "amount_total_" . $i;
        $subtotal = round(($quotation->$amount_pieces + $quotation->$amount_paint + $quotation->$amount_hand), 2);
        $iva = round(($subtotal * 0.16), 2);
        $total = round(($subtotal + $iva), 2);
        if ($subtotal != round($quotation->$amount_subtotal, 2)) {
            return false;
        }
        if ($iva != round($quotation->$amount_iva, 2)) {
            return false;
        }
        if ($total != round($quotation->$amount_total, 2)) {
            return false;
        }
        return true;
    }

    private function storeCarryLetter(Request $request) {
        $carry_letter = $request->file('carry_letter_input');
        if ($carry_letter->isValid()) {
            $ext = $carry_letter->getClientOriginalExtension();
            $filename = Auth::user()->dealer->id . "_" . Utils::getStamp() . str_random(12) . "." . $ext;
            $path = Config::get('constants.options.CARRYLETTER_PATH') . Auth::user()->dealer->id;
            try {
                $path_post = $carry_letter->storeAs($path, $filename, "public");
                return $path_post;
            } catch (\Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }

    private function storeChecklist(Request $request) {
        $checklist = $request->file('checklist_input');
        if ($checklist->isValid()) {
            $ext = $checklist->getClientOriginalExtension();
            $filename = Auth::user()->dealer->id . "_" . Utils::getStamp() . str_random(12) . "." . $ext;
            $path = Config::get('constants.options.CHECKLIST_PATH') . Auth::user()->dealer->id;
            try {
                $path_post = $checklist->storeAs($path, $filename, "public");
                return $path_post;
            } catch (\Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }

    private function deleteCarryLetter($claimId) {
        $order = Order::find($claimId);
        if ($order->src_carry_letter != null) {
            try {
                Storage::disk('public')->delete($order->src_carry_letter);
                return true;
            } catch (\Exception $e) {
                return true;
            }
        }
    }

    private function deleteChecklist($claimId) {
        $order = Order::find($claimId);
        if ($order->src_checklist != null) {
            try {
                Storage::disk('public')->delete($order->src_checklist);
                return true;
            } catch (\Exception $e) {
                return true;
            }
        }
    }

    /**
     * Display the specified saved resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showSaved($id) {

        if (!is_numeric($id)) {
            $error = "No existe ninguna orden con la información proporcionada.";
            return view('/claims/saved_claim')->with('error', $error);
        }

        $claim = Order::find($id);

        if ($claim == null) {
            $error = "No existe ninguna orden con la información proporcionada.";
            return view('/claims/saved_claim')->with('error', $error);
        }

        if ($claim->dealer->idUsuario != Auth::user()->id) {
            $error = "No tienes acceso a esta reclamación.";
            return view('/claims/saved_claim')->with('error', $error);
        }

        if ($claim->status != Config::get('constants.options.CLAIM_LEVEL_STORE')) {
            $error = "Error codigo ";
            return view('/claims/saved_claim')->with('error', $error);
        }

        $models = CarModel::all();
        $carriers = Carrier::all();
        $statusOrders = StatusOrder::all();

        return view('/claims/saved_claim')->with('claimId', $claim->id)
                        ->with('models', $models)
                        ->with('carriers', $carriers)
                        ->with('statusOrders', $statusOrders);
    }

    private function deleteDamageOrders($claimId) {
        $claim = Order::find($claimId);
        try {
            foreach ($claim->damageOrders as $damageOrder) {
                $damageOrder->delete();
            }
            return true;
        } catch (\Exception $e) {
            return true;
        }
    }

    private function deleteClaimPics($claimId) {
        $claim = Order::find($claimId);
        try {
            foreach ($claim->claimPics as $pic) {
                Storage::disk('public')->delete($pic->src_pic);
                $pic->delete();
            }
            return true;
        } catch (\Exception $e) {
            return true;
        }
    }

}
