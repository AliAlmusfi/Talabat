<?php

namespace App\Http\Controllers\Api;

use App\Models\Market;
use App\Models\Location;
use App\Tools\CRUDTrait;
use App\Tools\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
// use Illuminate\Contracts\Validation\Rule;
use App\Http\Resources\LocationResource;
use Illuminate\Support\Facades\Validator;
// use Illuminate\Validation\Rule;

class LocationController extends Controller
{

    use CRUDTrait;

    public function show(Request $request)
    {
        return $this->show_method($request, LocationResource::class, Location::class);
    }


    public function show_one(Request $request)
    {
        return $this->showOne_method($request, LocationResource::class, Location::class, 'Location');
    }


    public function destroy(Request $request)
    {
        return $this->destroy_method($request, LocationResource::class, Location::class, 'Location');
    }

    public function store(Request $request)
    {


        $validation_rules = [
            'name' => ['required', 'string'],
            'longitude' => ['required', 'numeric'],
            'latitude' => ['required', 'numeric', Rule::unique('locations', 'latitude')->where('longitude', $request->longitude)],
            'market_id' => ['required', 'exists:markets,id'],
        ];
        $market = Market::find($request->market_id);
        $n = $market['name'] . " _ " . $request->name;
        $request->merge(['name' => $n]);
        return $this->store_method($request, LocationResource::class, Location::class, 'Location', $validation_rules);
    }


    public function update(Request $request)
    {
        $validation_rules = [
            'id' => ['exists:locations,id'],
            'market_id' => ['required', 'exists:markets,id'],
            'longitude' => [],
            'latitude' => [],
        ];
        return $this->store_method($request, LocationResource::class, Location::class, 'Location', $validation_rules);
    }



    // End CRUD /////////////////////////////////////////////////////////////////////////////////////////////////////////




    public function get_market_location(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'market_id' => ['required', 'exists:markets,id']
        ]);
        if (! $validator) {
            return $this->validatorFailedResponse($validator->errors());
        }
        $location = LocationResource::collection(Location::where('market_id', $request->market_id)->get());
        if (! $location) {
            return $this->response(null, 400, "Bad Request");
        }
        return $this->response($location);
    }
}
