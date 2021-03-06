<?php

namespace App\Http\Controllers;

use App\Enums\CarType;
use App\Enums\Destination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class NotificationsController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'number' => ['required'],
            'from' => ['required', 'enum_key:' . Destination::class],
            'to' => ['required', 'enum_key:' . Destination::class],
            'date' => ['required', 'date_format:Y-m-d'],
            'hours' => ['numeric'],
            'type' => ['enum_key:' . CarType::class],
        ]);
        Redis::set(
            'notification:' . uniqid(),
            json_encode($request->except('hours')),
            'EX',
            intval($request->has('hours') ? $request->hours : 12) * 3600
        );
        return 'success';
    }
}
