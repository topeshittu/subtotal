<?php

namespace Modules\Desktopapp\Http\Controllers\Api;

use DB;
use App\Models\User;
use App\Models\DesktopInstall;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Desktopapp\Transformers\CommonResource;


/**
 * @group Brand management
 * @authenticated
 *
 * APIs for managing brands
 */
class DesktopInstallController extends ApiController
{
    public function store(Request $request)
    {
        try {
            $user = User::where('username', $request->input('username'))->where('business_id', $request->input('business_id'))->first(['id', 'business_id']);

            DB::beginTransaction();

            $install = DesktopInstall::updateOrCreate(
                [
                    'business_id' => $user->business_id,
                    'user_id' => $user->id,
                ],
                [
                    'machine_id' => $request->input('machine_id')
                ]
            );

            DB::commit();

            return new CommonResource($install);
            
        } catch(\Exception $e){
            DB::rollback();
            $errorCode = $e->errorInfo[1];
            if($errorCode == 1062){
                return [
                    'success'=>0,
                    'msg'=>'Machine ID already exists.'
                ];
            }
            
        }
        catch (\Exception $e) {
            DB::rollback();

            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            return $this->otherExceptions($e);
        }
    }
}
