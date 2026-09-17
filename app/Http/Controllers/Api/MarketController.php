<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MarketResource;
use App\Models\Market;
use App\Models\Market_tag;
use App\Tools\CRUDTrait;
use App\Tools\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MarketController extends Controller
{

    use ResponseTrait;
    use CRUDTrait;

    public function show(Request $request){
        return $this->show_method($request , MarketResource::class , Market::class);
    }

    public function show_one(Request $request){
        return $this->showOne_method($request , MarketResource::class , Market::class , 'Market');
    }

    public function store(Request $request){
        $validation_rules = [
            'name' => ['required' , 'min:3' , 'max:60'],
            'admin_id' => ['required' , 'exists:admins,id'],
        ];
        return $this->store_method($request , MarketResource::class , Market::class , 'Market' , $validation_rules);
    }

    public function update(Request $request){
        $validation_rules = [
            'id' => ['required' , 'exists:markets,id'],
            'name' => ['min:3' , 'max:60'],
        ];
        return $this->update_method($request , MarketResource::class , Market::class , 'Market' , $validation_rules);
    }

    public function destroy(Request $request){
        return $this->destroy_method($request , MarketResource::class , Market::class , 'Market');
    }

    // End CRUD /////////////////////////////////////////////////////////////////////////////////////////////////////////



    public function change_image(Request $request){
        return $this->change_image_method($request , MarketResource::class , Market::class , "Market");
    }

    public function get_admin_markets(Request $request){
        $validator = Validator::make($request->all() , [
            'admin_id' => ['required' , 'exists:admins,id'],
        ]);
        if($validator->fails()){
            return $this->validatorFailedResponse($validator->errors());
        }

        $markets = MarketResource::collection(Market::where('admin_id' , $request->admin_id)->get());
        if(!$markets){
            return $this->response(null , 400 , 'Markets Not Found! (Admin id Is Wrong)');
        }

        return $this->response($markets);
    }


    public function assign_tag_to_market(Request $request){
        $validator = Validator::make($request->all() , [
            'market_id' => ['required' , 'exists:markets,id'],
            'market_tag_id' => ['required' , 'exists:market_tag_listing,id'],
        ]);
        if($validator->fails()){
            return $this->validatorFailedResponse($validator->errors());
        }

        $market = Market::find($request->market_id);
        if(!$market){
            return $this->response(null , 400 , 'Market Not Found! (Market id Is Wrong)');
        }

        $market_tag = Market_tag::find($request->market_tag_id);
        if(!$market_tag){
            return $this->response(null , 400 , 'Market Tag Not Found! (Market Tag id Is Wrong)');
        }

        $market->tags()->attach($market_tag);

        return $this->response(null , 202 , 'Tag assigned to Market Successfully');
    }

}
