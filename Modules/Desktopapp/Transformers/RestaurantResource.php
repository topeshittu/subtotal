<?php

namespace Modules\Desktopapp\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;;

class RestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $array = parent::toArray($request);

        return $array;
    }
}
