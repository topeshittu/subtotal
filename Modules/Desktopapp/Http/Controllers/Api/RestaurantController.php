<?php

namespace Modules\Desktopapp\Http\Controllers\Api;

use App\Models\User;
use App\Restaurant\ResTable;
use App\Utils\Util;
use DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Desktopapp\Transformers\RestaurantResource;
use Spatie\Permission\Models\Role;

/**
 * @group Business Location management
 * @authenticated
 *
 * APIs for managing business locations
 */
class RestaurantController extends ApiController
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;

    public function __construct(Util $commonUtil)
    {
        $this->commonUtil = $commonUtil;
    }

    public function getServiceStaffs()
    {
        $user = Auth::user();

        $business_id = $user->business_id;

        $waiters = [];
        //Get all service staff roles
        $service_staff_roles = Role::where('business_id', $business_id)
                            ->where('is_service_staff', 1)
                            ->pluck('name')
                            ->toArray();
        
        //Get all users of service staff roles
        if (!empty($service_staff_roles)) {
            $waiters = User::where('business_id', $business_id)
                        ->role($service_staff_roles);

            $waiters = $waiters->select('id', DB::raw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(last_name, "")) as full_name'))->get();
            
        }

        if (empty($waiters)) {
            return $waiters;
        } else {
            return RestaurantResource::collection($waiters);
        }

    }

    public function getTables()
    {
        $user = Auth::user();

        $business_id = $user->business_id;

        $tables = ResTable::where('business_id', $business_id)
                            ->get('name', 'id');

        return RestaurantResource::collection($tables);
    }

}