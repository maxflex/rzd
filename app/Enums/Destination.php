<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

// https://ticket.rzd.ru/api/v1/suggests?Query=Москва
// или на https://ticket.rzd.ru/ при вводе пункта назначения в suggest-запросе
final class Destination extends Enum
{
    const mos = '2000000';
    const spb = '2004000';
    const bel = '2014370';
    const kur = '2000150';
    const minsk = '2100001';
}
