<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Damage;
use App\DamageArea;
use App\Utilities\HttpResponseInterface;
use App\DamageSeverity;

class DamageOrderController extends Controller {

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
        try {
            $response["damage_areas"] = DamageArea::all();
            $response["damages"] = Damage::all();
            $response["damage_severities"] = DamageSeverity::all();
            return $this->response->successResponse($response);
        } catch (\Exception $e) {
            return $this->response->internalServerErrorResponse($e);
        }
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
        //
    }

}
