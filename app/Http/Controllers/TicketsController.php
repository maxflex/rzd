<?php

namespace App\Http\Controllers;

use App\Enums\Destination;
use App\Http\Resources\TicketsResource;
use DateTime;
use Illuminate\Http\Request;
use App\Utils\Rzd;

class TicketsController extends Controller
{
    public function __invoke(Request $request)
    {
        // по умолчанию – сегодня
        $date = $request->has('date') ? $request->date : now();
        // по умолчанию – в Белгород
        // $to = $request->has('to') ? $request->to : Destination::BEL;

        return TicketsResource::collection(
            Rzd::get($date, Destination::bel)
        );
    }
}
