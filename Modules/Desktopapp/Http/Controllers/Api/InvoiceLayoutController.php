<?php

namespace Modules\Desktopapp\Http\Controllers\Api;

use App\Models\InvoiceLayout;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Desktopapp\Transformers\InvoiceLayoutResource;

/**
 * @group Business Location management
 * @authenticated
 *
 * APIs for managing business locations
 */
class InvoiceLayoutController extends ApiController
{
    
    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;

        $layouts = InvoiceLayout::where('business_id', $business_id)
                    ->get();

        return InvoiceLayoutResource::collection($layouts);
    }

}
