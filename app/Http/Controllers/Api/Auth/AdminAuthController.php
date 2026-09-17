<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminResource;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Tools\CRUDTrait;
use App\Tools\ImageHandling;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AdminAuthController extends Controller
{
    use CRUDTrait;

    public function register(Request $request)
    {
        $validation_rules = [
            'first_name' => ['required', 'min:3', 'max:255'],
            'last_name' => ['required', 'min:3', 'max:255'],
            'phone_number' => ['required', 'digits:10', 'numeric', 'unique:admins,phone_number'],
            'password' => ['required', 'min:8', 'max:24', 'confirmed'], // password_confirmation
        ];
        return $this->store_method($request, AdminResource::class, Admin::class, 'Admin', $validation_rules);
    }

    public function login(Request $request) {
        $credentials = $request->only('phone_number', 'password');

        $validator = Validator::make($request->all(), [
            'phone_number' => ['required' , 'digits:10' , 'numeric' , 'exists:admins,phone_number'],
            'password' => ['required' , 'min:8'],
            'fcm_token' => ['required'],
        ]);

        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }

        if (!$token = auth('admin_api')->attempt($credentials)) {
            return $this->response(null,401,'Invalid credentials');
        }
        $admin = auth('admin_api')->user();
        $admin->update($validator->validated());
        // return $admin;
        return $this->response([
            'access_token' => $token,
            'expire_date' => JWTAuth::factory()->getTTl(),
        ],201,'Admin successfully signed in');
    }

    public function logout(Request $request)
    {
        try {
            $token = JWTAuth::getToken('admin_api');
            if (!$token) {
                return $this->response(null, 401, 'Unauthorized');
            }
            JWTAuth::invalidate($token);
            auth('admin_api')->logout();
            return $this->response(null, 200, 'Logged out Successfully');
        } catch (Exception $e) {
            return $this->exceptionResponse($e);
        }
    }

    public function me(Request $request)
    {
        $user = auth('admin_api')->user();
        if (!$user) {
            return $this->response(null, 401, 'Unauthorized');
        }
        return $this->response(new UserResource($user), 202);
    }

    public function refresh(Request $request)
    {
        try {
            $newToken = JWTAuth::refresh($request->header('token'));
            return $this->response($newToken, 202, "Token Refreshed Successfully");
        } catch (JWTException $e) {
            return $this->exceptionResponse($e);
        }
    }
}
