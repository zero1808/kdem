<?php
namespace App\Utilities;

interface HttpResponseInterface {

    public function successResponse($data);

    public function unauthorizedResponse($data);

    public function validationErrorResponse($errors);

    public function internalServerErrorResponse($exception);
}

?>
