<div class='storys-container'>

@can('superadmin')
<a href="{{ action([\Modules\Desktopapp\Http\Controllers\ClientController::class, 'index']) }}" class="sub-menu-item {{ request()->segment(1) == 'desktopapp' ? 'active' : '' }}">@lang('desktopapp::lang.clients')</a>
@endcan

</div>