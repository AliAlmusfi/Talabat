<?php

namespace App\Services;

use App\Models\User;
use App\Models\Admin;
use App\Tools\ResponseTrait;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

class NotificationService
{
    protected $messaging;

    protected $userRepository;

    use ResponseTrait;
    public function __construct()
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));

        $this->messaging = $firebase->createMessaging();
    }

    public function sendNotification($deviceToken, $title, $body, array $data = [])
    {
        $notification = Notification::create($title, $body);
        // $notification = new Notification($title, $body);

        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification($notification)
            ->withData($data);

        return $this->messaging->send($message);
    }

    public function get_all_users_has_fcm_token()
    {
        $users = User::whereNotNull('fcm_token')->get();
        if (!$users) {
            return $this->response(null, 404, "Users FCM Token Not Found");
        }
        return $users;
    }
    public function get_all_admins_has_fcm_token()
    {
        $admins = Admin::whereNotNull('fcm_token')->get();
        if (!$admins) {
            return $this->response(null, 404, "Users FCM Token Not Found");
        }
        return $admins;
    }
}
