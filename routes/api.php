<?php

use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->get('/tickets', function (Request $request) {
    // return $request->user()->tickets;
    $tickets = Ticket::all();

    return TicketResource::collection($tickets);
    // return response()->json($tickets);

});
Route::get('/login', '\App\Http\Controllers\Api\AuthController@login')->name('login');
// Route::apiResource('/*', App\Http\Controllers\Api\AuthController::class);
