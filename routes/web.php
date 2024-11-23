<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\ScanController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('participant')->name('participant')->group(function () {
    Route::get("/register", [ParticipantController::class, "register"])->name('.register');
    Route::post("/register", [ParticipantController::class, "register_store"]);
});
