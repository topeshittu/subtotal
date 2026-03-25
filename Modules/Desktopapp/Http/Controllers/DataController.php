<?php

namespace Modules\Desktopapp\Http\Controllers;

use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Menu;

class DataController extends Controller
{
    public function superadmin_package()
    {
        return [
            [
                'name' => 'desktopapp_module',
                'label' => __('desktopapp::lang.desktopapp_module'),
                'default' => false
            ]
        ];
    }

    /**
     * Adds Connectoe menus
     * @return null
     */
    public function modifyAdminMenu()
    {
        $module_util = new ModuleUtil();
        
        if (auth()->user()->can('superadmin')) {
            $is_desktopapp_enabled = $module_util->isModuleInstalled('Desktopapp');
        } else {
            $business_id = session()->get('user.business_id');
            $is_desktopapp_enabled = (boolean)$module_util->hasThePermissionInSubscription($business_id, 'desktopapp_module', 'superadmin_package');
        }
        if ($is_desktopapp_enabled) {
            Menu::modify('admin-sidebar-menu', function ($menu) {
                $menu->dropdown(
                    __('desktopapp::lang.desktopapp'),
                    function ($sub) {
                        if (auth()->user()->can('superadmin')) {
                            $sub->url(
                                action('\Modules\Desktopapp\Http\Controllers\ClientController@index'),
                               __('desktopapp::lang.clients'),
                                ['icon' => 'fa fas fa-network-wired', 'active' => request()->segment(1) == 'desktopapp' && request()->segment(2) == 'api']
                            );
                        }
                        $sub->url(
                            url('\docs'),
                           __('desktopapp::lang.documentation'),
                            ['icon' => 'fa fas fa-book', 'active' => request()->segment(1) == 'docs']
                        );
                    },
                    ['icon' => 'fas fa-plug', 'style' => 'background-color: #2dce89 !important;']
                )->order(89);
            });
        }
    }
}
