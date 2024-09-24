<?php

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiControllers\V1\Frontend\RoomTypeController;
use App\Http\Controllers\ApiControllers\V1\Frontend\BookingController;
use App\Http\Controllers\ApiControllers\V1\Frontend\AmenityController;
use App\Http\Controllers\ApiControllers\V1\Frontend\PacketController;
use App\Http\Controllers\ApiControllers\V1\Frontend\UserController;
use App\Http\Controllers\ApiControllers\V1\Frontend\RatingController;
use App\Http\Controllers\ApiControllers\V1\Auth\AuthController;
use App\Http\Controllers\ApiControllers\V1\Auth\VerificationController;
use App\Http\Controllers\ApiControllers\V1\Backend\DashBoardController ;
use App\Http\Controllers\ApiControllers\V1\Backend\BookingController as BEBookingController;
use App\Http\Controllers\ApiControllers\V1\Backend\RoomController as BERoomController;
use App\Http\Controllers\ApiControllers\V1\Backend\RatingController as BERatingController;

use App\Http\Controllers\ApiControllers\V1\Backend\PacketController as BEPacketController;
use App\Http\Controllers\ApiControllers\V1\Backend\UserController as BEUserController;
use App\Http\Controllers\ApiControllers\V1\Backend\AmenityController as BEAmenityController;
use App\Http\Controllers\ApiControllers\V1\Backend\RoomTypeController as BERoomTypeController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group(array('prefix' => '/test', 'as' => 'test'), function () {
    $x = Carbon::now()->format('Y-m-d');
    $x = Carbon::create()->year(2024)->month(1)->firstOfQuarter();
    $x = Carbon::create()->year(2024)->month(1)->endOfQuarter();
//    $roomservice =  app()->make(\App\Services\RoomTypeServices::class);
//    $request['checkin_at'] = "2024-05-16 00:00:00";
//    $request['checkout_at'] ="2024-05-17 00:00:00";
//    $rooms = $roomservice->getRoomType($request);
//    dd($rooms);
});


Route::group(array('prefix' => '/v1'), function () {
    Route::group(array('prefix' => 'room-type', 'as' => 'room_type.'), function () {
        Route::get('/', [RoomTypeController::class, 'index'])->name('list');

        Route::get('/create', function () {

        })->name('create');

        Route::get('/edit/{code}', function () {

        })->name('edit');

        Route::get('/{code}', [RoomTypeController::class, 'show'])->name('show');

        Route::patch('/{code}', function () {

        })->name('patch');

        Route::post('/', function () {

        })->name('store');

        Route::delete('/{code}', function () {

        })->name('delete');

        Route::group(array('prefix' => '{room_type_id}/room', 'as' => 'room.'), function () {

        });

    });

    Route::group(array('prefix' => 'amenities', 'as' => 'amenities.'), function () {
        Route::get('/', [AmenityController::class, 'index'])->name('list');
    });

    Route::group(array('prefix' => 'rating', 'as' => 'rating.'), function () {
        Route::get('/room/{roomTypeId}/packet/{packetId}', [RatingController::class, 'ratingRoom'])->name('ratingRoom');
        Route::get('/', [RatingController::class, 'index'])->name('list');
        Route::post('/', [RatingController::class, 'store'])
//            ->middleware('auth:api')
            ->name('store');
    });

    Route::group(array('prefix' => 'user', 'as' => 'user.', 'middleware' => 'auth:api'), function () {
        Route::group(array('prefix' => '{customerId}/bookings', 'as' => 'bookings.'), function () {
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::get('/{bookingId}', [BookingController::class, 'show'])->name('show');
            Route::patch('/{bookingId}', [BookingController::class, 'update'])->name('patch');
        });
        Route::patch('/{id}', [UserController::class, 'update'])->name('patch');

    });


    Route::group(array('prefix' => 'payments', 'as' => 'payments.'), function () {
    });

    Route::group(array('prefix' => 'checkout', 'as' => 'checkout.'), function () {
        Route::post('/', [BookingController::class, 'store'])->name('store');
    });

    Route::group(array('prefix' => 'packets', 'as' => 'packets.'), function () {
        Route::get('/', [PacketController::class, 'index'])->name('list');
    });

    Route::group(array('prefix' => 'benefits', 'as' => 'benefits.'), function () {
    });


    Route::group(array('prefix' => 'admin'), function () {
        Route::get('/dashboard', [DashBoardController::class, 'index'])->name('list');
        Route::group(array('prefix' => 'bookings', 'as' => 'bookings.'), function () {
            Route::get('/', [BEBookingController::class, 'index'])->name('index');
            Route::get('/{bookingId}', [BEBookingController::class, 'show'])->name('show');
            Route::patch('/{bookingId}', [BEBookingController::class, 'update'])->name('patch');
        });
		 Route::group(array('prefix' => 'rooms', 'as' => 'rooms.'), function () {
            Route::get('/', [BERoomController::class, 'index'])->name('index');
            Route::get('/{Id}', [BERoomController::class, 'show'])->name('show');
            Route::patch('/{Id}', [BERoomController::class, 'update'])->name('patch');
        });

		Route::group(array('prefix' => 'ratings', 'as' => 'ratings.'), function () {
            Route::get('/', [BERatingController::class, 'index'])->name('index');
            Route::get('/{Id}', [BERatingController::class, 'show'])->name('show');
            Route::patch('/{Id}', [BERatingController::class, 'update'])->name('patch');
        });

		Route::group(array('prefix' => 'packets', 'as' => 'packets.'), function () {
            Route::get('/', [BEPacketController::class, 'index'])->name('index');
            Route::get('/{Id}', [BERatingController::class, 'show'])->name('show');
            Route::patch('/{Id}', [BERatingController::class, 'update'])->name('patch');
        });

		Route::group(array('prefix' => 'amenities', 'as' => 'amenities.'), function () {
            Route::get('/', [BEAmenityController::class, 'index'])->name('index');
            Route::get('/{Id}', [BEAmenityController::class, 'show'])->name('show');
            Route::patch('/{Id}', [BEAmenityController::class, 'update'])->name('patch');
        });

		Route::group(array('prefix' => 'room-types', 'as' => 'room_type.'), function () {
            Route::get('/', [BERoomTypeController::class, 'index'])->name('index');
            Route::get('/{Id}', [BERoomTypeController::class, 'show'])->name('show');
            Route::patch('/{Id}', [BERoomTypeController::class, 'update'])->name('patch');
        });

        Route::group(array('prefix' => 'users', 'as' => 'room_type.'), function () {
            Route::get('/', [BEUserController::class, 'index'])->name('index');
            Route::get('/{Id}', [BEUserController::class, 'show'])->name('show');
            Route::patch('/{Id}', [BEUserController::class, 'update'])->name('patch');
        });


    });

    Route::group(['prefix' => '/auth'], function () {
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::post('/change-password', [AuthController::class, 'changePassword'])->name('changePassword');
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgotPassword');

        Route::post('/me', [AuthController::class, 'me'])->name('me');
        Route::get('/user-profile', [AuthController::class, 'userProfile'])->name('userProfile');
        Route::post('/updatePass', [AuthController::class, 'updatePass'])->name('updatePass');
    });

    Route::get('/email/verify/notice', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/', [VerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/resend', [VerificationController::class, 'resend'])->name('verification.resend');
});


