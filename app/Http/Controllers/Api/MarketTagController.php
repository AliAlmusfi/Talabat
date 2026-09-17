<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MarketResource;
use App\Http\Resources\MarketTagResource;
use App\Models\Market;
use App\Models\Market_tag;
use App\Models\Product_tag;
use App\Tools\CRUDTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MarketTagController extends Controller
{

    use CRUDTrait;

    public function show(Request $request)
    {
        return $this->show_method($request, MarketTagResource::class, Market_tag::class);
    }

    public function show_one(Request $request)
    {
        return $this->showOne_method($request, MarketTagResource::class, Market_tag::class, 'Market tag');
    }

    public function store(Request $request){
        $request->merge(['tag' => str_replace(' ' , '_' , trim(strtolower($request->tag)))]);
        $validator = Validator::make($request->all(),[
            'tag' => ['required', 'max:32', 'unique:Market_tag_listing,tag'],
        ]);
        if($validator->fails()){
            return $this->validatorFailedResponse($validator->errors());
        }


        $tag = Market_tag::create(['tag' => $request->tag]);
        return $this->response(new MarketTagResource($tag) , 201 , "Tag Created Successfully");
    }

    public function destroy(Request $request)
    {
        return $this->destroy_method($request, MarketTagResource::class, Market_tag::class, 'Market tag');
    }

    ///END CRUD//////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function search(Request $request){
        $request->merge(['tag' => str_replace(' ' , '_' , trim(strtolower($request->tag)))]);
        $validator = Validator::make($request->all() , [
            'tag' => ['required' , 'max:32']
        ]);
        if($validator->fails()){
            return $this->validatorFailedResponse($validator->errors());
        }

        $tag = Market_tag::where('tag' , $request->tag)->first();
        if(!$tag){
            return $this->response(null , 404 , "Tag Not Found");
        }
        return $this->response(new MarketTagResource($tag));
    }


    public function show_market_tags(Request $request){
        $validator = Validator::make($request->all() , [
            'market_id' => ['required' , 'exists:markets,id']
        ]);
        if($validator->fails()){
            return $this->validatorFailedResponse($validator->errors());
        }

        $tags = Market_tag::whereHas('markets', function ($query) use ($request) {
            $query->where('market_id', $request->market_id);
        })->get();

        return $this->response(MarketTagResource::collection($tags));
    }
    public function show_tag_markets(Request $request){
        $validator = Validator::make($request->all() , [
            'tag_id' => ['required' , 'exists:market_tag,tag_id']
        ]);
        if($validator->fails()){
            return $this->validatorFailedResponse($validator->errors());
        }

        $markets = Market::whereHas('tags', function ($query) use ($request) {
            $query->where('tag_id', $request->tag_id);
        })->get();

        return $this->response(MarketResource::collection($markets));
    }

}
