<?php

namespace Modules\Desktopapp\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;;

class InvoiceLayoutResource extends JsonResource
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

        $array['logo'] = !empty($array['logo']) ? base64_encode(@file_get_contents(upload_asset('uploads/invoice_logos/' . $array['logo']))) : null;

        
        return $array;
    }
}
