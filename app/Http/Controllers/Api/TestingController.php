<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Tools\CRUDTrait;
use App\Tools\ImageHandling;
use App\Tools\TranslationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TestingController extends Controller
{
    use CRUDTrait;

    public function translation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'text' => ['required', 'string'],
            ]);
            if ($validator->fails()) {
                return $this->validatorFailedResponse($validator->errors());
            }
            $translator = new TranslationService();
            $detected_lang = $translator->detect_lang($request->text);
            $translated_text = $translator->translate($request->text, $detected_lang);

            return $this->response($translated_text, 200, "Translation Complete");
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function pingTheAi(Request $request)
    {
        // $api_url = 'https://chatgpt-42.p.rapidapi.com/';
        // try{
        //     $response = Http::withHeaders([
        //         'x-rapidapi-key' => 'c2cc7a6a37msh81bcb70c6ceca45p15514cjsnf0c442903e45',
        //         'x-rapidapi-host' => 'chatgpt-42.p.rapidapi.com'
        //     ])->get($api_url);
        //     if($response->successful()){
        //         return $this->response($response->json() , 200 , "Ping Successed");
        //     }
        //     return $this->response($response->body() , 400 , "response didn't success");
        // }catch(Exception $e){
        //     return $this->exceptionResponse($e);
        // }
        $url = ImageHandling::Base64_to_url($request->image, 'Product', 1);
        return $this->response($url);
    }
}
