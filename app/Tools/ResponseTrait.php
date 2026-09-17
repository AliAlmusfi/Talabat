<?php

namespace App\Tools;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use function Pest\Laravel\json;

trait ResponseTrait
{
    public function response($data , $status=200 , $msg=""){
        $array = [
            'data' => $data,
            'status' => $status,
            'msg' => $msg,
        ];
        return response()->json($array , $status);
    }
    public function exceptionResponse(Exception $e,$status=400){    // May Have to change status from 400 to something else
        return $this->response('Exception Appeared' , $status , 'Exception message: ' . $e->getMessage());
    }
    public function validatorFailedResponse($validator_errors){
            return $this->response("Validation Error" , 400 , $validator_errors);
    }
}
