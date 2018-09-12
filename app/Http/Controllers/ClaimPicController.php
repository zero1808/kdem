<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Config;
use App\Utilities\HttpResponseInterface;
use App\Order;
use Validator;
use App\ClaimPic;
use App\Libraries\Utils;
use Storage;
class ClaimPicController extends Controller {

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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        //

        /* Validations */
        if ($this->validator($request)->fails()) {
            return $this->response->validationErrorResponse($this->validator($request)->messages());
        }

        $status_transaction = true;
        $pics = $request->file('file');
        $claimStored = $request->claim_stored;
        $order = Order::find($claimStored);
        foreach ($pics as $pic) {
            if ($pic->isValid()) {
                $ext = $pic->getClientOriginalExtension();
                $size = $pic->getClientSize();

                $filename = $order->id . "_" . Utils::getStamp() . str_random(12) . "." . $ext;
                $path = Config::get('constants.options.PICS_PATH') . $order->dealer->id . "/";
                $path_post = $pic->storeAs($path, $filename, "public");
                if ($path_post != null) {
                    try {
                        $claimPic = new ClaimPic();
                        $claimPic->src_pic = $path . $filename;
                        $claimPic->size = $size;
                        $claimPic->idOrder = $order->id;
                        $claimPic->save();
                    } catch (\Exception $e) {
                        return $this->response->internalServerErrorResponse("El Claim con VIN: " . $order->vin . " fue registrado correctamente, pero ocurrio un error al guardar las imagenes.\nCódigo:\n" . $e->getMessage());
                    }
                } else {
                    $status_transaction = false;
                }
            }
        }
        if ($status_transaction) {
            return $this->response->successResponse($order->vin);
        } else {
            return $this->response->internalServerErrorResponse("El Claim con VIN: " . $order->vin . " fue registrado correctamente, pero ocurrio un error al guardar las imagenes.\nCódigo 004");
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
        if(!is_numeric($id)){
            return $this->response->internalServerErrorResponse("La foto que intentas borrar no existe\nCódigo: 1001");
        }
        $pic = ClaimPic::find($id);
        
        if($pic == null){
            return $this->response->internalServerErrorResponse("La foto que intentas borrar no existe\nCódigo: 1002");
        }
        
        try {
            Storage::disk('public')->delete($pic->src_pic);
            $pic->delete();
            $data["code"] = 0;
            return $this->response->successResponse($data);
        } catch (\Exception $e) {
            return $this->response->internalServerErrorResponse("Error al intentar borrar foto\nCódigo: 1003");
        }
    }

    public function validator(Request $request) {
        $rules = array(
            'claim_stored' => 'required|integer|exists:orders,id',
            'file' => 'required|array',
            'file.*' => 'image|max:10000|mimes:jpg,jpeg,png'
        );
        $validator = Validator::make($request->all(), $rules);

        return $validator;
    }

    public function validateImages(Request $request) {
        $imageRules = array(
            'image' => 'image|max:10000|mimes:jpg,jpeg,png'
        );
    }
   

}
