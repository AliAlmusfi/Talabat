<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use App\Services\NotificationService;
use App\Tools\CRUDTrait;

class NotificationController extends Controller
{
    use CRUDTrait;

    public function notify(/*$obj ,$title , $body*/Request $request)
    {
        // $user = auth('api')->user();

        $notification = new NotificationService();
        $users = $notification->get_all_users_has_fcm_token();

        if (!$users) {
            return $this->response(null, 404, "No users found with FCM tokens.");
        }

        $messages = [];
        
        foreach ($users as $user) {
            $messages[] = $notification->sendNotification(
                $user->fcm_token,
                $request->title,
                $request->body,
                [$request->id]
            );
        }
        return $this->response($messages);
    }
}
