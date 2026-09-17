<?php

namespace App\Http\Controllers\Api;

use App\Models\Admin;
use App\Tools\ImageHandling;
use App\Tools\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Tools\CRUDTrait;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{

    use CRUDTrait;

    public function show()
    {

        $admins = AdminResource::collection(Admin::all());
        return $this->response($admins);
    }


    public function show_one(Request $request)
    {
        if (! isset($request->id)) {
            return $this->response(null, 401, "No ID in Request");
        }
        $admin = Admin::find($request->id);
        if (! $admin) {
            return $this->response(null, "404", "Not Found");
        }
        return $this->response(new AdminResource($admin));
    }


    public function destroy(Request $request)
    {
        if (! isset($request->id)) {
            return $this->response(null, 401, "No ID in Request");
        }
        $admin = Admin::find($request->id);
        if (! $admin) {
            return $this->response(null, 400, "Bad Request");
        }
        $admin = Admin::destroy($request->id);
        return $this->response(new AdminResource($admin));
    }


    public function change_image(Request $request){
        return $this->change_image_method($request , AdminResource::class , Admin::class , "Admin");
    }
    public function update(Request $request)
    {
        $Validation_rules = [
            'id' => ['required', 'exists:admins,id'],
            'first_name' => ['min:3'],
            'last_name' => ['min:3'],
            'phone_number' => ['numirec', Rule::unique('admins', 'phone_number')->ignore($request->id)],
        ];

        $validator = Validator($request->all(), $Validation_rules);
        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }
        $admin = Admin::find($request->id);
        if (! $admin) {
            return $this->response(null, 400, "Bad Request");
        }
        $admin->update($validator->validated());
        return $this->response($admin);
    }


    public function change_password(Request $request)
    {
        $Validation_rules = [
            'id' => ['required', 'exists:admins,id'],
            'old_password' => ['required', 'min:8', 'max:24'],
            'new_password' => ['required', 'min:8', 'max:24', 'confirmed'],
        ];
        $validator = Validator($request->all(), $Validation_rules);
        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }
        $admin = Admin::find($request->id);
        if (! $admin) {
            return $this->response(null, 400, "Bad Request");
        }
        $admin->update($validator->validated());
        return $this->response($admin);
    }

    // End CRUD /////////////////////////////////////////////////////////////////////////////////////////////////////////

}
