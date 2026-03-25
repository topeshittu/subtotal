@php
	$is_mobile = isMobile();
@endphp

@if($is_mobile)
<button class="btn-scroll" id="btn-scroll-left" onclick="scrollHorizontally(1)"> 
    <img src="{{ asset('img/icons/scroll-left.svg') }}" alt="">
</button>
<button class="btn-scroll" id="btn-scroll-right" onclick="scrollHorizontally(-1)"> 
    <img src="{{ asset('img/icons/scroll-right.svg') }}" alt="">
</button>
@endif
<div class="storys-container">
<a class="navbar-brand" href="{{action([\Modules\Manufacturing\Http\Controllers\RecipeController::class, 'index'])}}"><i class="fas fa-industry"></i> {{__('manufacturing::lang.manufacturing')}}</a>
                     
@can('manufacturing.access_recipe')
<a href="{{action('\Modules\Manufacturing\Http\Controllers\RecipeController@index')}}" class="sub-menu-item {{ request()->segment(1) == 'manufacturing' && in_array(request()->segment(2), ['recipe', 'add-ingredient']) ? 'active' : '' }}">@lang('manufacturing::lang.recipe')</a>
@endcan

@can('manufacturing.access_production')
<a href="{{action('\Modules\Manufacturing\Http\Controllers\ProductionController@index')}}" class="sub-menu-item {{ request()->segment(2) == 'production' ? 'active' : '' }}">@lang('manufacturing::lang.production')</a>

<a href="{{action('\Modules\Manufacturing\Http\Controllers\SettingsController@index')}}" class="sub-menu-item {{ request()->segment(1) == 'manufacturing' && request()->segment(2) == 'settings' ? 'active' : '' }}">@lang('messages.settings')</a>

<a href="{{action('\Modules\Manufacturing\Http\Controllers\ProductionController@getManufacturingReport')}}" class="sub-menu-item {{ request()->segment(2) == 'report' ? 'active' : '' }}">@lang('manufacturing::lang.manufacturing_report')</a>
@endcan
</div>