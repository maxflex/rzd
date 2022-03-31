<?php

namespace App\Utils;

use App\Enums\Destination;
use DateTime;
use Rzd\Api;

class Rzd
{
    public static function get($date, $from, $to)
    {
        $api = new Api();

        if (in_array($from, Destination::getKeys())) {
            $from = Destination::getValue($from);
        }
        if (in_array($to, Destination::getKeys())) {
            $to = Destination::getValue($to);
        }

        $params = [
            'dir'          => 0, // 0 - только в один конец, 1 - туда-обратно
            'tfl'          => 1, // 3 - поезда и электрички, 2 - электрички, 1 - поезда
            'checkSeats'   => 1, // 1 - только с билетами, 0 - все поезда
            // 'withoutSeats' => 'y', // Если checkSeats = 0, то этот параметр тоже необходим
            // Коды станций можно получить отдельным запросом
            // https://pass.rzd.ru/suggester?compactMode=y&stationNamePart=БЕЛГОРОД
            'code0'        => $from, // код станции отправления
            'code1'        => $to, // код станции прибытия
            'dt0'          => (new DateTime($date))->format('d.m.Y'),
            'md'           => 0, // 0 - без пересадок, 1 - с пересадками
        ];

        return $api->trainRoutes($params);
    }
}
