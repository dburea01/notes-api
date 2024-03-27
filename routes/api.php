<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\OrganizationController;
use App\Http\Middleware\DetectLocale;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::post('login', [AuthController::class, 'login']);

    Route::middleware([DetectLocale::class, 'auth:sanctum'])->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::resource('organizations', OrganizationController::class)->whereUuid('organization');
        Route::resource('organizations/{organization}/notes', NoteController::class)->whereUuid(['organization', 'note']);
    });

});
