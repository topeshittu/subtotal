<?php

namespace Modules\Desktopapp\Http\Controllers\Api;

use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Utils\BusinessUtil;
use Illuminate\Support\Facades\Auth;

use Modules\Desktopapp\Transformers\SalesCommissionAgentResource;

use App\Models\User;

/**
 * @group Business Location management
 * @authenticated
 *
 * APIs for managing business locations
 */
class SalesCommissionAgentController extends ApiController
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil) {
        $this->businessUtil = $businessUtil;
    }
    public function index()
    {
        $user = Auth::user();

        $business_id = $user->business_id;

        $business_details = $this->businessUtil->getDetails($business_id);

        $commsn_agnt_setting = $business_details->sales_cmsn_agnt;

        

        $query = User::where('business_id', $business_id)->user();
        if ($commsn_agnt_setting == 'user') {
            $query->where('is_cmmsn_agnt', 0);
        } elseif ($commsn_agnt_setting == 'cmsn_agnt') {
            $query->where('is_cmmsn_agnt', 1);
        } elseif($commsn_agnt_setting == 'logged_in_user') {
            $query->where('id', $user->id);
        }

        $sales_commission_agents = $query->select(['id',
                                    DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name")])->get();

        return SalesCommissionAgentResource::collection($sales_commission_agents);
    }

}
