<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Tools\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserAuthController extends Controller {

    use ResponseTrait;

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:50|min:3',
            'last_name' => 'required|string|max:50|min:3',
            'phone_number' => 'required|max:10|unique:users',
            'password' => 'required|string|min:6|confirmed',
            // 'default_longitude' => 'required|numeric',
            // 'default_latitude'=> 'required|numeric',
            'image_url' => [], //TODO
        ]);

        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
            //return response()->json($validator->errors(), 422);
        }

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
        ]);

        return $this->response($user,201,'User registered successfully');
        //return response()->json(['message' => 'User registered successfully']);
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
            //return response()->json($validator->errors(), 422);
        }

        if (!$token = auth('api')->attempt($credentials)) {
            return $this->response(null,401,'Invalid credentials');
            //return response()->json(['error' => 'Invalid credentials'], 401);
        }
        $admin = auth('api')->user();
        $admin->update($validator->validated());
        return $this->response($token,201,'User successfully signed in');
        //return response()->json(['token' => $token]);
    }

    public function logout() {
        auth()->logout();
        return $this->response(null,200,'User successfully signed out');
        //return response()->json(['message' => 'User successfully signed out']);
    }

    public function me() {
        return $this->response(auth()->user(),200,'Showing user profile');
        //return response()->json(auth()->user());
    }

    public function refresh(){
            try {
                // Invalidate the current token
                JWTAuth::invalidate(JWTAuth::getToken());

                // Generate a new token
                $newToken = JWTAuth::fromUser(auth()->user());
                return $this->response($newToken,201,'Token regenerated successfully');
//                return response()->json([
//                    'success' => true,
//                    'token' => $newToken,
//                ]);
            } catch (JWTException $e) {
                return $this->exceptionResponse($e,500);
//                return response()->json([
//                    'success' => false,
//                    'message' => 'Token refresh failed: ' . $e->getMessage(),
//                ], 500);
            }
        }
}
