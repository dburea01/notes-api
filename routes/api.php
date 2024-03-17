<?php

use App\Http\Controllers\NoteController;
use App\Http\Middleware\DetectLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/
Route::prefix('v1')->group(function () {

    Route::resource('notes', NoteController::class)->middleware(DetectLocale::class);

});
