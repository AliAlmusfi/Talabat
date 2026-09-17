<?php


use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\Auth\AdminAuthController;
use App\Http\Controllers\Api\Auth\UserAuthController;
use App\Http\Controllers\Api\LocationController;
use App\Http\Controllers\Api\MarketController;
use App\Http\Controllers\Api\MarketTagController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductTagController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SMSController;
use App\Http\Controllers\Api\TestingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\ChangeLangMiddleware;
use App\Http\Middleware\TokenMiddleware;
use App\Models\Product_tag;
use Google\Cloud\Storage\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::middleware('api',/*ChangeLangMiddleware::class*/)->group(function () {                   # All Api Routes Group
    # auth Routes
    Route::middleware('api')->prefix('auth')->group(function(){
        Route::prefix('admin')->group(function () {
            Route::post('/register', [AdminAuthController::class, 'register']);
            Route::post('/login', [AdminAuthController::class, 'login']);
            Route::middleware(TokenMiddleware::class . ':admin_api')->group(function () {
                Route::post('/logout', [AdminAuthController::class, 'logout']);
                Route::get('/me', [AdminAuthController::class, 'me']);
                Route::get('/refresh', [AdminAuthController::class, 'refresh']);

            });
        });
        Route::prefix('user')->group(function(){
            Route::post('/register' , [UserAuthController::class , 'register']);
            Route::post('/login' , [UserAuthController::class , 'login']);
            Route::middleware(TokenMiddleware::class.':user_api')->group(function(){
                Route::post('/logout' , [UserAuthController::class , 'logout']);
                Route::get('/me' , [UserAuthController::class , 'me']);
                Route::get('/refresh' , [UserAuthController::class , 'refresh']);
            });
        });
    });

    # test Routes
    Route::middleware('api')->prefix('test')->group(function () {
        Route::post('/translation', [TestingController::class, 'translation']);
        Route::post('/pingTheAi', [TestingController::class, 'pingTheAi']);
    });


    # user Routes
    Route::middleware('api')->prefix('user')->group(function () {
        Route::get('/show', [UserController::class, 'show']);
        Route::get('/show_one', [UserController::class, 'show_one']);
        Route::patch('/update', [UserController::class, 'update']);
        Route::patch('/change_password', [UserController::class, 'change_password']);
        Route::delete('/destroy', [UserController::class, 'destroy']);
        Route::post('/change_image' , [UserController::class , 'change_image']);

    });

    # market Routes
    Route::middleware('api')->prefix('market')->group(function () {
        Route::get('/show', [MarketController::class, 'show']);
        Route::get('/show_one', [MarketController::class, 'show_one']);
        Route::get('/get_admin_markets', [MarketController::class, 'get_admin_markets']);
        Route::middleware(TokenMiddleware::class . ':admin_api')->group(function () {
            Route::post('/store', [MarketController::class, 'store']);
            Route::patch('/update', [MarketController::class, 'update']);
            Route::delete('/destroy', [MarketController::class, 'destroy']);
            Route::post('/assign_tag_to_market', [MarketController::class, 'assign_tag_to_market']);
            Route::post('/change_image' , [MarketController::class , 'change_image']);
        });
    });
    # market_tag Routes
    Route::middleware('api')->prefix('market_tag')->group(function () {
        Route::get('/show', [MarketTagController::class, 'show']);
        Route::get('/show_one', [MarketTagController::class, 'show_one']);
        Route::get('/search', [MarketTagController::class, 'search']);
        Route::get('/show_market_tags', [MarketTagController::class, 'show_market_tags']);
        Route::get('/show_tag_markets', [MarketTagController::class, 'show_tag_markets']);
        Route::middleware(TokenMiddleware::class . ':admin_api')->group(function () {
            Route::post('/store', [MarketTagController::class, 'store']);
        });
        Route::delete('/destroy', [MarketTagController::class, 'destroy']);
    });

    # admin Routes
    Route::middleware('api')->prefix('admin')->group(function () {
        Route::get('/show', [AdminController::class, 'show']);
        Route::get('/show_one', [AdminController::class, 'show_one']);
        Route::delete('/destroy', [AdminController::class, 'destroy']);
        Route::patch('/update', [AdminController::class, 'update']);
        Route::post('/change_image' , [AdminController::class , 'change_image']);
    });

    # location Routes
    Route::middleware('api')->prefix('location')->group(function () {
        Route::get('/show', [LocationController::class, 'show']);
        Route::get('/show_one', [LocationController::class, 'show_one']);
        Route::get('/get_market_location', [LocationController::class, 'get_market_location']);
        Route::middleware(TokenMiddleware::class . ':admin_api')->group(function () {
            Route::post('/store', [LocationController::class, 'store']);
            Route::patch('/update', [LocationController::class, 'update']);
            Route::delete('/destroy', [LocationController::class, 'destroy']);
        });
    });

    # product Routes
    Route::middleware('api')->prefix('product')->group(function () {
        Route::get('/show', [ProductController::class, 'show']);
        Route::get('/show_one', [ProductController::class, 'show_one']);
        Route::get('/get_market_location_products', [ProductController::class, 'get_market_location_products']);
        Route::middleware(TokenMiddleware::class . ':admin_api')->group(function () {
            Route::post('/store', [ProductController::class, 'store']);
            Route::patch('/update', [ProductController::class, 'update']);
            Route::delete('/destroy', [ProductController::class, 'destroy']);
            Route::post('/assign_tag_to_product', [ProductController::class, 'assign_tag_to_product']);
            Route::post('/change_image' , [ProductController::class , 'change_image']);
        });
    });

    # product_tag Routes
    Route::middleware('api')->prefix('product_tag')->group(function () {
        Route::get('/show', [ProductTagController::class, 'show']);
        Route::get('/show_one', [ProductTagController::class, 'show_one']);
        Route::get('/search', [ProductTagController::class, 'search']);
        Route::get('/show_product_tags', [ProductTagController::class, 'show_product_tags']);
        Route::get('/show_tag_products', [ProductTagController::class, 'show_tag_products']);
        Route::middleware(TokenMiddleware::class . ':admin_api')->group(function () {
            Route::post('/store', [ProductTagController::class, 'store']);
        });
        Route::delete('/destroy', [ProductTagController::class, 'destroy']);
    });

    # order Routes
    Route::middleware('api')->prefix('order')->group(function () {
        Route::get('/show', [OrderController::class, 'show']);
        Route::get('/show_one', [OrderController::class, 'show_one']);
        Route::get('/get_order_by_date', [OrderController::class, 'get_order_by_date']);
        Route::post('/store_one', [OrderController::class, 'store_one']);
        Route::post('/store_full', [OrderController::class, 'store_full']);
        Route::patch('/cancel_order', [OrderController::class, 'cancel_order']);
        Route::middleware(TokenMiddleware::class . ':admin_api')->group(function () {
            Route::patch('/update_status', [OrderController::class, 'update_status']);
        });
    });

    Route::middleware('api')->prefix('notification')->group(function () {
        Route::get('/notify', [NotificationController::class, 'notify']);
    });


    # Report Routes
    Route::middleware('api')->prefix('report')->group(function () {
        Route::get('/show' , [ReportController::class , 'show']);
        Route::get('/show_one' , [ReportController::class , 'show_one']);
        Route::post('/store' , [ReportController::class , 'store']);
    });
    # SMS (OTP) Routes
    Route::middleware('api')->prefix('sms')->group(function(){
        Route::post('/send_verification_code' , [SMSController::class , 'send_verification_code']);
        Route::post('/verify_code' , [SMSController::class , 'verify_code']);
        Route::patch('/resend_verification_code' , [SMSController::class , 'resend_verification_code']);
    });

});
