<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Tools\CRUDTrait;
use App\Tools\ResponseTrait;
use App\Tools\TranslationService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpParser\Node\Stmt\Catch_;

class UserController extends Controller
{
    use ResponseTrait;
    use CRUDTrait;

    public function show(Request $request)
    {
        return $this->show_method($request, UserResource::class, User::class);
    }

    public function show_one(Request $request)
    {
        return $this->showOne_method($request, UserResource::class, User::class, 'User');
    }

    public function update(Request $request)
    {
        $validation_rules = [
            'id' => ['required', 'exists:users,id'],
            'first_name' => ['min:4'],
            'last_name' => ['min:2'],
            'phone_number' => ['size:10', 'numeric', Rule::unique('users', 'phone_number')->ignore($request->id)],
        ];
        return $this->update_method($request, UserResource::class, User::class, 'User', $validation_rules);
    }

    public function destroy(Request $request)
    {
        return $this->destroy_method($request, UserResource::class, User::class, 'User');
    }

    // End CRUD ////////////////////////////////////////////////////////////////////////////////////////////////////////


    public function change_image(Request $request)
    {
        return $this->change_image_method($request, UserResource::class, User::class, "User");
    }


    public function change_password(Request $request)
    {
        $validation_rules = [
            'id' => ['required', 'exists:users,id'],
            'old_password' => ['required', 'min:8', 'max:24', Rule::exists('users', 'password')->where('id', $request->id)],
            'new_password' => ['required', 'min:8', 'max:24', 'confirmed'], // new_password_confirmation
        ];
        $validator = Validator::make($request->all(), $validation_rules);
        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }

        $user = User::find($request->id);
        if (!$user) {
            return $this->response(null, 400, 'User Not Found! (User id Is Wrong)');
        }

        $user->update($validator->validated());

        return $this->response(new UserResource($user), 202, 'User Password Changed Successfully');
    }
}
