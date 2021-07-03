<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return $request->all();
        return [
            'from' => [
                'station' => $this->route0,
                'time' => $this->date0 . ' ' . $this->time0,
            ],
            'to' => [
                'station' => $this->route1,
                'time' => $this->date1 . ' ' . $this->time1,
            ],
            'duration' => $this->timeInWay,
            'brand' => $this->brand,
            'number' => $this->number,
        ];
    }
}
