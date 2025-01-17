<?php

use App\Http\Controllers\API\TicketsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return response()->json([
        'success'=>true,
        'message'=>'detail login user',
        'data'=>$request->user()
    ]);
});
Route::resource('tickets',TicketsController::class)->only(['store','index','show'])->middleware(['auth:sanctum']);
Route::get('tickets/{ticket}/queue-position', [TicketsController::class, 'getQueuePosition'])
    ->middleware(['auth:sanctum']);

require __DIR__.'/auth.php';