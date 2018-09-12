<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::resource('/claims', 'ClaimController');
Route::resource('/damages', 'DamageOrderController');
Route::resource('/pics', 'ClaimPicController');

Route::get('/import/users', 'ImportController@importUserDealers');
Route::get('/import/orders', 'ImportController@importOrders');
Route::get('/orders/accepted', 'HomeController@getAccepted');
Route::get('/orders/rejected', 'HomeController@getRejected');
Route::get('/orders/accepted', 'HomeController@getAccepted');
Route::get('/orders/saved', 'HomeController@getSaved');
Route::get('/order/saved/{id}', 'ClaimController@showSaved');
Route::post('/allclaims', 'ClaimTableController@allClaims');

Route::get('/claimreport/{id}', function($id) {
    if (\Auth::user()) {
        $claim = \App\Order::find($id);
        if (null != $claim) {
            $header["carrier"] = isset($claim->carrier) ? $claim->carrier->name : " - ";
            $header["arrive_date"] = $claim->arrive_date . " " . $claim->arrive_date_time;
            $pdf = new \App\Libraries\ClaimReportPdf();
            $pdf->AddPage("P", "A4");
            $pdf->Header($header);
            $pdf->dealerInfo($claim);
            $carInfo["vin"] = $claim->vin;
            $carInfo["model"] = $claim->carModel->code . " - " . $claim->carModel->name;
            $pdf->carInfo($carInfo);
            $pdf->damages($claim->damageOrders);
            $pdf->quotations($claim->damageOrders);
            $pdf->photos($claim->claimPics);
            if (\Auth::user()->rol->name == \Config::get('constants.options.ROL_ADMINISTRATOR')) {
                return $pdf->Output("I", "file.pdf", true);
            } else if (\Auth::user()->rol->name == \Config::get('constants.options.ROL_OPERATOR')) {
                if (\Auth::user()->id == $claim->dealer->idUsuario) {
                    return $pdf->Output("I", "file.pdf", true);
                }
            }
        }
    }
});

