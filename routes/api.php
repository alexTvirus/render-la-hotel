<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiControllers\V1\Frontend\RoomTypeController;

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
//    $roomservice =  app()->make(\App\Services\RoomTypeServices::class);
//    $request['checkin_at'] = "2024-05-16 00:00:00";
//    $request['checkout_at'] ="2024-05-17 00:00:00";
//    $rooms = $roomservice->getRoomType($request);
//    dd($rooms);
});

Route::group(array('prefix' => '/v1'), function () {
    Route::group(array('prefix' => 'room-type', 'as' => 'room_type.'), function () {
        Route::get('/',[RoomTypeController::class, 'index'])->name('list');

        Route::get('/create', function () {

        })->name('create');

        Route::get('/edit/{code}', function () {

        })->name('edit');

        Route::get('/{code}',[RoomTypeController::class, 'show'])->name('show');

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
    });

    Route::group(array('prefix' => 'bookings', 'as' => 'bookings.'), function () {
    });

    Route::group(array('prefix' => 'payments', 'as' => 'payments.'), function () {
    });

    Route::group(array('prefix' => 'packets', 'as' => 'packets.'), function () {
    });

    Route::group(array('prefix' => 'benefits', 'as' => 'benefits.'), function () {
    });


    Route::group(array('prefix' => 'admin'), function () {

    });
});


