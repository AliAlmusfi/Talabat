<?php

namespace App\Http\Controllers\Api;


use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Tools\CRUDTrait;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{


    use CRUDTrait;



    public function show(Request $request)
    {
        return $this->show_method($request, OrderResource::class, Order::class);
    }


    public function show_one(Request $request)
    {
        return $this->showOne_method($request, OrderResource::class, Order::class, 'Order');
    }


    public function store_one(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'product_id' => ['required', 'exists:products,id'],
            'total_price' => ['required', 'numeric'],
            'user_longitude' => ['required', 'numeric'],
            'user_latitude' => ['required', 'numeric'],
            'quantity' => ['required', 'numeric'],
        ]);
        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }
        if (!$this->check_product_quantity($request->quantity, $request->product_id)) {
            return $this->response(null, 400, "Product Quantity Is Less Than Required");
        }
        $order = $this->create_order($request->product_id, $validator->validated());
        $this->notifyAdmins_oneOrder($order);
        return $this->response(new OrderResource($order), 201, "Order Created Successfully");
    }

    public function store_full(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'products.*.id' => ['required', 'exists:products,id'],
            'products.*.quantity' => ['required', 'numeric']
        ]);

        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }

        foreach ($request->products as $product) {
            if (!$this->check_product_quantity($product['quantity'], $product['id'])) {
                return $this->response(null, 400, "The Quantity less than your Order in the Order id " . $product['id']);
            }
        }

        $orders = [];
        foreach ($request->products as $product) {
            $orders[] = $this->create_order($product['id'], [
                'user_id' => $request->user_id,
                'product_id' => $product['id'],
                'total_price' => $request->total_price,
                'user_longitude' => $request->user_longitude,
                'user_latitude' => $request->user_latitude,
                'quantity' => $product['quantity']
            ]);
        }
        $this->notifyAdmins_manyOrders($orders);
        return $this->response($orders, 201, "All Order Created");
    }

    private function check_product_quantity($request_quantity, $product_id)
    {
        $product = Product::find($product_id);
        if ($product->quantity < $request_quantity) {
            return false;
        }
        return true;
    }

    private function create_order($product_id, $data)
    {
        $product = Product::find($product_id);
        if (! $product) {
            return $this->response(null, 404, "Product Not Found");
        }

        $product->update(['quantity' => $product->quantity - $data['quantity']]);

        $order = Order::create($data);
        if (! $order) {
            return $this->response(null, 400, "Can't Create Order");
        }

        $order = Order::find($order->id);
        $order->products()->attach($product, ['quantity' => $data['quantity']]);
        return $order;
    }


    public function update_status(Request $request)
    {
        $status_ar = 'قيد التحضير';
        switch ($request->status) {
            case 'sent':
                $status_ar = 'تم الارسال';
                break;
            case 'recieved':
                $status_ar = 'تم الاستلام';
                break;
            case 'canceled':
                $status_ar = 'ملغي';
                break;
        }
        $request->merge([$status_ar]);

        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:orders,id'],
            'status' => ['required', 'in:preparing,sent,recieved'],
            'status_ar' => ['required', Rule::in(['قيد التحضير', 'تم الارسال', 'تم الاستلام'])],
        ]);
        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }
        $order = Order::find($request->id);
        if (!$order) {
            return $this->response(null, 404, "Order Not Found");
        }
        $order->update($validator->validated());

        // $notification = new NotificationService();
        $this->notifyUsers($order);


        return $this->response($order);
    }


    //END CRUD//////////////////////////////////////////////

    public function get_order_by_date(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'exists:users,id'],
            'date' => ['required', 'date']
        ]);
        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }

        $order = Order::where('user_id', $request->user_id)->where('created_at', $request->date)->get();
        if (! $order) {
            return $this->response(null, 404, "Order Not Found By This Date");
        }
        return $this->response($order);
    }

    public function cancel_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => ['required', 'exists:orders,id']
        ]);
        if ($validator->fails()) {
            return $this->validatorFailedResponse($validator->errors());
        }

        $order = Order::find($request->id);
        if (!$order) {
            return $this->response(null, 404, "Order Not Found");
        }
        if ($order->status != "preparing") {
            return $this->response(null, 400, "Order Not In Preparing Status");
        }

        $order->update([
            'status' => 'canceled',
            'status_ar' => 'ملغي'
        ]);

        $this->notifyUsers($order);

        return $this->response($order, 202, "Order Canceled Successfully ");
    }


    //Check If Wrong Function
    public function notifyUsers(Order $order)
    {
        // $user = auth('api')->user();
        $title = "Status Changed";
        $body = $order . "The Status Update To " . $order->status;
        $notification = new NotificationService();
        $users = $notification->get_all_users_has_fcm_token();
        foreach ($users as $user) {
            $notification->sendNotification($user->fcm_token, $title, $body, ['Order_id' => $order->id]);
        }
    }

    public function notifyAdmins_oneOrder(Order $order)
    {
        $title = "Request done";
        $body = "The Order $order is Requested ";
        $notification = new NotificationService();
        $admins = $notification->get_all_admins_has_fcm_token();
        foreach ($admins as $admin) {
            $notification->sendNotification($admin->fcm_token, $title, $body, ['Order_id' => $order->id]);
        }
    }

    public function notifyAdmins_manyOrders(array $order)
    {
        $title = "Many Request done";
        $body = "The Orders is Requested ";
        $notification = new NotificationService();
        $admins = $notification->get_all_admins_has_fcm_token();
        foreach ($admins as $admin) {
            $notification->sendNotification($admin->fcm_token, $title, $body, ['Order_id']);
        }
    }
}
