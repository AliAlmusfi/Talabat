<?php

namespace App\Http\Middleware;

use App\Tools\ResponseTrait;
use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ChangeLangMiddleware
{
    use ResponseTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try{
            if(!isset($request->lang)) return $this->response(null , 400 , "Missing Lang Attribute");
            if($request->lang == 'ar'){
                app()->setlocale('ar');
            }else if($request->lang == 'en'){
                app()->setlocale('en');
            }else{
                return $this->response(null , 400 , "Wrong Value For Lang Attribute, Lang should be 'ar' or 'en'");
            }
            return $next($request);
        }catch(Exception $e){
            return $this->exceptionResponse($e);
        }
    }
}
