<?php

use App\Http\Controllers\NotificationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketsController;

Route::get('tickets', TicketsController::class);
Route::apiResource('notifications', NotificationsController::class);
