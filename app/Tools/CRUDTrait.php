<?php

namespace App\Tools;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait CRUDTrait
{

    use ResponseTrait;

    public function show_method(Request $request, $resource, $model)
    {
        $collection = $resource::collection($model::all());
        return $this->response($collection);
    }

    public function showOne_method(Request $request, $resource, $model, string $model_name)
    {
        if (!isset($request->id)) {
            return $this->response(null, 400, 'Needed ' . $model_name . ' id Doesn\'t Exist In The Request');
        }
        $obj = $model::find($request->id);
        if (!$obj) {
            return $this->response(null, 400, $model_name . ' Not Found! (' . $model_name . ' id Is Wrong)');
        }

        return $this->response(new $resource($obj));
    }

    public function store_method(Request $request, $resource, $model, string $model_name, array $validation_rules)
    {
        try {
            $validator = Validator::make($request->all(), $validation_rules);
            if ($validator->fails()) {
                return $this->validatorFailedResponse($validator->errors());
            }
            $obj = $model::create($validator->validated());
            if (!$obj) {
                return $this->response(null, 400, $model_name . ' Not Found! (' . $model_name . ' id Is Wrong)');
            }

            return $this->response(new $resource($obj), 201, $model_name . ' Created Successfully');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function update_method(Request $request, $resource, $model, string $model_name, array $validation_rules)
    {
        $validator = Validator::make($request->all(), $validation_rules);
        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }

        $obj = $model::find($request->id);
        if (!$obj) {
            return $this->response(null, 400, $model_name . ' Not Found! (' . $model_name . ' id Is Wrong)');
        }
        $obj->update($validator->validated());
        return $this->response(new $resource($obj), 202, $model_name . ' Updated Successfully');
    }

    // '.$model_name.'

    public function destroy_method(Request $request, $resource, $model, string $model_name)
    {
        if (!isset($request->id)) {
            return $this->response(null, 400, 'Needed ' . $model_name . ' id Doesn\'t Exist In The Request');
        }

        $obj = $model::find($request->id);
        if (!$obj) {
            return $this->response(null, 400, $model_name . ' Not Found! (' . $model_name . ' id Is Wrong)');
        }

        $model::destroy($request->id);

        return $this->response(new $resource($obj), 202, $model_name . ' Deleted Successfully');
    }


    public function change_image_method(Request $request, $resource, $model, string $model_name)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:admins,id'],
            'image' => ['required', 'string'],
        ]);
        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }
        $image_url = ImageHandling::Base64_to_url($request->image,  $model_name, $request->id);
        if (!$image_url) {
            return $this->response(null, 500, 'Couldn\'t convert base64 to url');
        }

        $obj = $model::find($request->id);
        if (!$obj) {
            return $this->response(null, 404, 'Not Found the ' . $model_name . ' Specified!');
        }
        $obj->update(['image_url' => $image_url]);
        return $this->response(new $resource($obj), 201, '' . $model_name . ' Updated');
    }
}
