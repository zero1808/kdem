<?php
namespace App\Utilities;
use App\Utilities\HttpResponseInterface;
class HttpResponseImpl implements HttpResponseInterface {

    public $successStatus = 200;
    public $unauthorizedStatus = 401;
    public $badRequestStatus = 400;
    public $backendErrorStatus = 500;
    public $internalServerErrorMessage = "The system can not respond at this time";
            
    public function successResponse($data) {

        $response = [
            'status' => [
                'http_response' => $this->successStatus
            ],
            'response' => $data
        ];
        return response()->json($response,$this->successStatus);
    }

    public function unauthorizedResponse($data) {

        $response = [
            'status' => [
                'http_response' => $this->unauthorizedStatus
            ],
            'response' => $data
        ];
        return response()->json($response,$this->unauthorizedStatus);

    }
    
    public function validationErrorResponse($errors){
        $response = [
            'status' => [
                'http_response' => $this->badRequestStatus
            ],
            'errors' => $errors
        ];
        
        return response()->json($response,$this->successStatus);
    }
    
    public function internalServerErrorResponse($exception){
        $response = [
            'status' => [
                'http_response' => $this->backendErrorStatus
            ],
            'message' => $this->internalServerErrorMessage,
            'exception' => $exception
        ];
        return response()->json($response,$this->backendErrorStatus);
    }



}

?>

