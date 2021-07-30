<?php

use App\Http\Controllers\NotificationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('notifications', NotificationsController::class);
