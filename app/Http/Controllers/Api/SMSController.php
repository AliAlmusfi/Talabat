<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OTP;
use App\Tools\CRUDTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Twilio\Rest\Client;

class SMSController extends Controller
{
    use CRUDTrait;

    public function send_verification_code(Request $request){
        // validation
        $validator = Validator::make($request->all() , [
            'phone_number' => ['required' , 'numeric' , 'digits:10' , 'unique:users,phone_number']
        ]);
        if($validator->fails()){
            return $this->validatorFailedResponse($validator->errors());
        }
        // preparing twilio client
        $twilio = new Client(env('TWILIO_SID') , env('TWILIO_AUTH_TOKEN'));
        if(!$twilio){
            return $this->response(null , 500 , "twilio client error");
        }
        // generate the code
        $otp_code = $this->generate_OTP(6);
        // save the code in database
        $otp = OTP::create([
            'otp' => $otp_code,
            'expire_date' => now()->addMinutes(3)
        ]);
        if(!$otp){
            return $this->response(null , 500 , "Can't Save OTP in Database");
        }
        // send message with the code
        $twilio->messages->create($this->correcting_phone_number($request->phone_number) , [
            'from' => env('TWILIO_NUMBER'),
            'body' => 'Your Verification Code is: ' . $otp_code
        ]);
        // response
        return $this->response($otp , 200 , "Verification Code Sent Successfully");
    }


    public function verify_code(Request $request){
        Artisan::call('delete:otp-old-rows');
        $validator = Validator::make($request->all() , [
            'otp_id' => ['required' , 'exists:otps,id'],
            'otp' => ['required']
        ]);
        if($validator->fails()){
            return $this->validatorFailedResponse($validator->errors());
        }

        $otp = OTP::find($request->otp_id);
        if(!$otp){
            return $this->response(null , 404 , "OTP Not Found");
        }

        if($otp->otp === $request->otp){
            return $this->response(['success' => true] , 200 , "OTP use rentered is Right");
        }else{
            return $this->response(['success' => false] , 400 , "OTP use rentered is Wrong");
        }
    }


    public function resend_verification_code(Request $request){
        $validator = Validator::make($request->all() , [
            'otp_id' => ['required' , 'exists:otps,id'],
            'phone_number' => ['required' , 'numeric' , 'digits:10' , 'unique:users,phone_number']
        ]);
        if($validator->fails()){
            return $this->validatorFailedResponse($validator->errors());
        }
        // finding the otp
        $otp = OTP::find($request->otp_id);
        if(!$otp){
            return $this->response(null , 404 , "OTP Not Found");
        }
        // preparing twilio client
        $twilio = new Client(env('TWILIO_SID') , env('TWILIO_AUTH_TOKEN'));
        if(!$twilio){
            return $this->response(null , 500 , "twilio client error");
        }
        // generate the code
        $otp_code = $this->generate_OTP(6);
        // save the code in database
        $otp->update([
            'otp' => $otp_code,
            'expire_date' => now()->addMinutes(3)
        ]);
        if(!$otp){
            return $this->response(null , 500 , "Can't Save OTP in Database");
        }
        // send message with the code
        $twilio->messages->create($this->correcting_phone_number($request->phone_number) , [
            'from' => env('TWILIO_NUMBER'),
            'body' => 'Your Verification Code is: ' . $otp_code
        ]);
        // response
        return $this->response($otp , 200 , "Verification Code Sent Successfully");
    }

    private function generate_OTP(int $digits_count): string{
        $min = 1; $max = 9;
        for($i = 0 ; $i < $digits_count-1 ; $i++){
            $min *= 10;
            $max *= 10; $max += 9;
        }
        return rand($min , $max);
    }
    private function correcting_phone_number(string $phone_number){
        if(strlen($phone_number) == 10){
            $phone_number = substr($phone_number , 1);
            $phone_number = '+963' . $phone_number;
        }else if(strlen($phone_number) == 9){
            $phone_number = '+963' . $phone_number;
        }else{
            throw new Exception('phone_number provided is not 9 or 10 digits !');
        }
        return $phone_number;
    }
}
