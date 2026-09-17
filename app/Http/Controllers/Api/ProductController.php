<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\Product_tag;
use App\Tools\CRUDTrait;
use App\Tools\TranslationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use CRUDTrait;
    public function show(Request $request)
    {
        return $this->show_method($request, ProductResource::class, Product::class);
    }
    public function show_one(Request $request)
    {
        return $this->showOne_method($request, ProductResource::class, Product::class, 'Product');
    }
    public function store(Request $request)
    {
        $translator = new TranslationService();
        $request->merge($translator->translate_inputs([
            'name' => $request->name,
            'info' => $request->info,
        ]));
        $validation_rule = [
            'name' => ['required', 'min:3', 'max:60'],
            'name_ar' => [],
            'price' => ['required', 'numeric'],
            'quantity' => ['required', 'numeric'],
            'info' => ['required', 'string'],
            'info_ar' => [],
            'location_id' => ['required', 'exists:locations,id'],
        ];
        return $this->store_method($request, ProductResource::class, Product::class, 'Product', $validation_rule);
    }

    public function update(Request $request)
    {
        $translator = new TranslationService();

        $translation_array = [];
        (isset($request->name)) ? $translation_array['name'] = $request->name : null;
        (isset($request->info)) ? $translation_array['info'] = $request->info : null;
        $request->merge($translator->translate_inputs($translation_array));

        $validation_rule = [
            'id' => ['required', 'exists:products,id'],
            'name' => ['min:3', 'max:60'],
            'name_ar' => [],
            'price' => ['numeric'],
            'quantity' => ['numeric'],
            'info' => ['string'],
            'info_ar' => [],
            'location_id' => ['exists:locations,id']
        ];
        return $this->update_method($request, ProductResource::class, Product::class, 'Product', $validation_rule);
    }
    public function destroy(Request $request)
    {
        return $this->destroy_method($request, ProductResource::class, Product::class, 'Product');
    }

    ///END CRUD/////////////////////////////////////////////////////////////////////////////////////////////////////////////



    public function change_image(Request $request)
    {
        return $this->change_image_method($request, ProductResource::class, Product::class, "Product");
    }

    public function get_market_location_products(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_id' => ['required', 'exists:locations,id']
        ]);
        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }

        $products = ProductResource::collection(Product::where('location_id', $request->location_id)->get());
        if (! $products) {
            return $this->response(null, 400, "Products Not Found (Location Id Is Wrong)");
        }
        return $this->response($products);
    }


    public function assign_tag_to_product(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'exists:products,id'],
            'product_tag_id' => ['required', 'exists:product_tag_listing,id'],
            'location_id' => ['required', 'exists:locations,id']
        ]);
        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }
        $product = Product::where('location_id', $request->location_id)->get()->first();
        if (! $product) {
            return $this->response(null, 400, "Products Not Found (Product Id Is Wrong)");
        }
        $product_tag = Product_tag::find($request->product_tag_id);
        if (! $product_tag) {
            return $this->response(null, 400, "Product Tag Not Found (Product Tag Id Is Wrong)");
        }

        $product->tag()->attach($product_tag);


        return $this->response(null, 202, 'Tag assigned to Product Successfully');
    }
}
