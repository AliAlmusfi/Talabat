<?php

namespace App\Http\Controllers\Api;

use App\Tools\CRUDTrait;
use App\Models\Product_tag;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ProductTagResource;
use App\Models\Product;

class ProductTagController extends Controller
{

    use CRUDTrait;

    public function show(Request $request)
    {
        return $this->show_method($request, ProductTagResource::class, Product_tag::class);
    }

    public function show_one(Request $request)
    {
        return $this->showOne_method($request, ProductTagResource::class, Product_tag::class, 'Product Tag');
    }

    public function store(Request $request)
    {
        $request->merge(['tag' => str_replace(' ', '_', trim(strtolower($request->tag)))]);
        $validator = Validator::make($request->all(), [
            'tag' => ['required', 'max:32', 'unique:Product_tag_listing,tag'],
        ]);
        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }


        $tag = Product_tag::create(['tag' => $request->tag]);
        return $this->response(new ProductTagResource($tag), 201, "Tag Created Successfully");
    }

    public function destroy(Request $request)
    {
        return $this->destroy_method($request, ProductTagResource::class, Product_tag::class, 'Product Tag');
    }

    //End CRUD /////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public function search(Request $request)
    {
        $request->merge(['tag' => str_replace(' ', '_', trim(strtolower($request->tag)))]);
        $validator = Validator::make($request->all(), [
            'tag' => ['required', 'max:32']
        ]);
        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }
        $tag = Product_tag::where('tag', $request->tag)->first();
        if (!$tag) {
            return $this->response(null, 404, "Tag Not Found");
        }
        return $this->response(new ProductTagResource($tag));
    }


    public function show_product_tags(Request $request){
        $validator = Validator::make($request->all() , [
            'product_id' => ['required' , 'exists:products,id']
        ]);
        if($validator->fails()){
            return $this->validatorFailedResponse($validator->errors());
        }

        $tags = product_tag::whereHas('products', function ($query) use ($request) {
            $query->where('product_id', $request->product_id);
        })->get();

        return $this->response(ProductResource::collection($tags));
    }

    public function Show_tag_products(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tag_id' => ['required', 'exists:market_tag,tag_id']
        ]);
        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }
        $products = Product::whereHas('tag', function ($query) use ($request) {
            $query->where('tag_id', $request->tag_id);
        });

        return $this->response(ProductResource::collection($products));

    }
}
