@inject('request', 'Illuminate\Http\Request')
@php
    $isKitchenStationPage = $request->is('modules/kitchen/station/custom/*');
    $stationData = isset($station) ? $station : null;
@endphp

<div class="pos-topbar no-print {{ $isKitchenStationPage ? 'kitchen-station-header' : '' }}">
  @if($isKitchenStationPage && $stationData)
    {{-- Kitchen Station Header Layout --}}
    <div class="station-header-left">
        <div class="station-info">
            <div class="station-icon" style="background-color: {{ $stationData->color ?? '#4f46e5' }};">
                <i class="{{ $stationData->icon ?? 'fas fa-utensils' }}"></i>
            </div>
            <div class="station-details">
                <h1>{{ $stationData->name }}</h1>
                <p>{{ $stationData->description ?? __('restaurant.kitchen_station') }}</p>
            </div>
        </div>
    </div>
    
    {{-- Order Statistics Filters with Time Filter --}}
    <div class="station-header-center">
        <div class="order-stats-controls">
            <div class="order-stats">
                <button class="stat-item stat-filter active" data-status="all" onclick="filter_orders_by_status('all')">
                    <i class="fas fa-list"></i>
                    <span id="all-count">{{ isset($statistics) ? array_sum($statistics) : 0 }}</span> @lang('restaurant.all')
                </button>
                <button class="stat-item stat-filter" data-status="pending" onclick="filter_orders_by_status('pending')">
                    <i class="fas fa-clock"></i>
                    <span id="pending-count">{{ isset($statistics) ? ($statistics['pending'] ?? 0) : 0 }}</span> @lang('restaurant.pending')
                </button>
                <button class="stat-item stat-filter" data-status="preparing" onclick="filter_orders_by_status('preparing')">
                    <i class="fas fa-fire"></i>
                    <span id="preparing-count">{{ isset($statistics) ? ($statistics['preparing'] ?? 0) : 0 }}</span> @lang('restaurant.preparing')
                </button>
                <button class="stat-item stat-filter" data-status="ready" onclick="filter_orders_by_status('ready')">
                    <i class="fas fa-check-circle"></i>
                    <span id="ready-count">{{ isset($statistics) ? ($statistics['ready'] ?? 0) : 0 }}</span> @lang('restaurant.ready')
                </button>
                <button class="stat-item stat-filter" data-status="served" onclick="filter_orders_by_status('served')">
                    <i class="fas fa-utensils"></i>
                    <span id="served-count">{{ isset($statistics) ? ($statistics['served'] ?? 0) : 0 }}</span> @lang('restaurant.served')
                </button>
            </div>
            
            <div class="time-filter-inline">
                <select id="time-filter" class="time-filter-select-inline" onchange="change_time_filter(this.value)">
                    <option value="today" {{ isset($time_filter) && $time_filter == 'today' ? 'selected' : '' }}>@lang('restaurant.today')</option>
                    <option value="24h" {{ isset($time_filter) && $time_filter == '24h' ? 'selected' : '' }}>24h</option>
                    <option value="all" {{ isset($time_filter) && $time_filter == 'all' ? 'selected' : '' }}>@lang('restaurant.all')</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Mobile Stats Dropdown Button (visible only on mobile) -->
    <div class="mobile-stats-toggle">
       
        <button class="item display-mobile-only"  id="mobile-stats-btn" onclick="toggle_mobile_stats()">
                <img src="{{ asset('img/icons/menu-flex.svg') }}" alt="">
            </button>
    </div>
    
    <!-- Mobile Stats Dropdown (hidden by default) -->
    <div class="mobile-stats-dropdown" id="mobile-stats-dropdown">
        <div class="mobile-stats-content">
            <div class="mobile-order-stats">
                <button class="stat-item stat-filter active" data-status="all" onclick="filter_orders_by_status('all')">
                    <i class="fas fa-list"></i>
                    <span id="mobile-all-count">{{ isset($statistics) ? array_sum($statistics) : 0 }}</span> @lang('restaurant.all')
                </button>
                <button class="stat-item stat-filter" data-status="pending" onclick="filter_orders_by_status('pending')">
                    <i class="fas fa-clock"></i>
                    <span id="mobile-pending-count">{{ isset($statistics) ? ($statistics['pending'] ?? 0) : 0 }}</span> @lang('restaurant.pending')
                </button>
                <button class="stat-item stat-filter" data-status="preparing" onclick="filter_orders_by_status('preparing')">
                    <i class="fas fa-fire"></i>
                    <span id="mobile-preparing-count">{{ isset($statistics) ? ($statistics['preparing'] ?? 0) : 0 }}</span> @lang('restaurant.preparing')
                </button>
                <button class="stat-item stat-filter" data-status="ready" onclick="filter_orders_by_status('ready')">
                    <i class="fas fa-check-circle"></i>
                    <span id="mobile-ready-count">{{ isset($statistics) ? ($statistics['ready'] ?? 0) : 0 }}</span> @lang('restaurant.ready')
                </button>
                <button class="stat-item stat-filter" data-status="served" onclick="filter_orders_by_status('served')">
                    <i class="fas fa-utensils"></i>
                    <span id="mobile-served-count">{{ isset($statistics) ? ($statistics['served'] ?? 0) : 0 }}</span> @lang('restaurant.served')
                </button>
            </div>
            
            <div class="mobile-time-filter">
                <label for="mobile-time-filter">@lang('restaurant.time_filter'):</label>
                <select id="mobile-time-filter" class="time-filter-select-mobile" onchange="change_time_filter(this.value)">
                    <option value="today" {{ isset($time_filter) && $time_filter == 'today' ? 'selected' : '' }}>@lang('restaurant.today')</option>
                    <option value="24h" {{ isset($time_filter) && $time_filter == '24h' ? 'selected' : '' }}>24h</option>
                    <option value="all" {{ isset($time_filter) && $time_filter == 'all' ? 'selected' : '' }}>@lang('restaurant.all')</option>
                </select>
            </div>
            
            <!-- Mobile Actions (Bell & Logout) -->
            <div class="mobile-actions">
                <button class="mobile-action-btn bell-toggle" id="mobile-bell-toggle" onclick="toggle_bell_sound()" title="Enable/Disable order notification sound">
                    <i class="fas fa-bell" id="mobile-bell-icon"></i>
                    <span class="mobile-bell-status" id="mobile-bell-status">@lang('restaurant.on')</span>
                </button>
                
                <button class="mobile-action-btn logout-btn">
                    <a href="{{action('Auth\LoginController@logout')}}">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>@lang('restaurant.logout')</span>
                    </a>
                </button>
            </div>
        </div>
    </div>
    
        <!-- Kitchen Station Right Side - Bell Toggle & Logout -->
        <div class="options">
            <!-- Bell/Sound Toggle -->
            <button class="item bell-toggle" id="bell-toggle" onclick="toggle_bell_sound()" title="@lang('restaurant.enable_disable_notification_sound')">
                <i class="fas fa-bell" id="bell-icon"></i>
                <span class="bell-status-text hide-mobile-only" id="bell-status">@lang('restaurant.on')</span>
            </button>
            
            <button class="item hide-mobile-only">
                <a href="{{action('Auth\LoginController@logout')}}">
                    <img src="{{ asset('img/icons/log-out-04.svg') }}" alt="">
                </a>
            </button>
            
            
        </div>
    </div>
  @else
    {{-- Regular Restaurant Header --}}
    <div class="top-left">
        <div class="back">
            <a href="{{ action('HomeController@index')}}" title="{{ __('lang_v1.go_back') }}" data-toggle="tooltip" data-placement="bottom">
                <img src="{{ asset('img/icons/back-icon.svg') }}" alt="">
            </a>
        </div>
        <div class="welcome-message">
            <h3>@lang('lang_v1.restaurant')</h3>
        </div>
    </div>

    <div class="top-right">
      <!-- Pos Button -->
      @if (in_array('pos_sale', $enabled_modules))
            @can('sell.create')
                <a href="{{ action('SellPosController@create') }}" title="@lang('sale.pos_sale')" data-toggle="tooltip"
                    data-placement="bottom" class="pos">
                    <svg width="24" height="25" viewBox="0 0 24 25" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M15 21.5V16.1C15 15.5399 15 15.2599 14.891 15.046C14.7951 14.8578 14.6422 14.7049 14.454 14.609C14.2401 14.5 13.9601 14.5 13.4 14.5H10.6C10.0399 14.5 9.75992 14.5 9.54601 14.609C9.35785 14.7049 9.20487 14.8578 9.10899 15.046C9 15.2599 9 15.5399 9 16.1V21.5M3 7.5C3 9.15685 4.34315 10.5 6 10.5C7.65685 10.5 9 9.15685 9 7.5C9 9.15685 10.3431 10.5 12 10.5C13.6569 10.5 15 9.15685 15 7.5C15 9.15685 16.3431 10.5 18 10.5C19.6569 10.5 21 9.15685 21 7.5M6.2 21.5H17.8C18.9201 21.5 19.4802 21.5 19.908 21.282C20.2843 21.0903 20.5903 20.7843 20.782 20.408C21 19.9802 21 19.4201 21 18.3V6.7C21 5.5799 21 5.01984 20.782 4.59202C20.5903 4.21569 20.2843 3.90973 19.908 3.71799C19.4802 3.5 18.9201 3.5 17.8 3.5H6.2C5.0799 3.5 4.51984 3.5 4.09202 3.71799C3.71569 3.90973 3.40973 4.21569 3.21799 4.59202C3 5.01984 3 5.57989 3 6.7V18.3C3 19.4201 3 19.9802 3.21799 20.408C3.40973 20.7843 3.71569 21.0903 4.09202 21.282C4.51984 21.5 5.07989 21.5 6.2 21.5Z"
                            stroke="#fff" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    <span>@lang('lang_v1.pos')</span>
                </a>
            @endcan
        @endif

      <div class="options">
          <div class="dropdown-container">
            <button class="item dropdown-trigger" id="kitchen-settings-btn">
              <img src="{{ asset('img/icons/settings.svg') }}" alt="">
            </button>
            <div class="kitchen-settings-dropdown" id="kitchen-settings-dropdown">
              <div class="dropdown-header">
                <h4>@lang('restaurant.kitchen_settings')</h4>
              </div>
              <div class="dropdown-content">
                {{-- Auto Timer --}}
                <div class="setting-item">
                  <div class="setting-info">
                    <span class="setting-label">@lang('restaurant.auto_timer')</span>
                    <small class="setting-description">@lang('restaurant.auto_timer_description')</small>
                  </div>
                  <label class="setting-toggle">
                    <input type="checkbox" id="auto-timer" checked>
                    <span class="toggle-slider"></span>
                  </label>
                </div>
                
                {{-- Sound Alerts --}}
                <div class="setting-item">
                  <div class="setting-info">
                    <span class="setting-label">@lang('restaurant.sound_alerts')</span>
                    <small class="setting-description">@lang('restaurant.sound_alerts_description')</small>
                  </div>
                  <label class="setting-toggle">
                    <input type="checkbox" id="sound-alerts" checked>
                    <span class="toggle-slider"></span>
                  </label>
                </div>
                
                {{-- Priority Mode --}}
                <div class="setting-item">
                  <div class="setting-info">
                    <span class="setting-label">@lang('restaurant.priority_mode')</span>
                    <small class="setting-description">@lang('restaurant.priority_mode_description')</small>
                  </div>
                  <label class="setting-toggle">
                    <input type="checkbox" id="priority-mode">
                    <span class="toggle-slider"></span>
                  </label>
                </div>
                
                {{-- Auto Tab Switch --}}
                <div class="setting-item">
                  <div class="setting-info">
                    <span class="setting-label">@lang('restaurant.auto_tab_switch')</span>
                    <small class="setting-description">@lang('restaurant.auto_tab_switch_description')</small>
                  </div>
                  <label class="setting-toggle">
                    <input type="checkbox" id="auto-tab-switch" checked>
                    <span class="toggle-slider"></span>
                  </label>
                </div>
                
                <div class="dropdown-divider"></div>
                
                {{-- General Settings Link --}}
                <div class="setting-item">
                  <a href="{{action('BusinessController@getSettings')}}" class="settings-link">
                    <i class="fas fa-cog"></i>
                    <span>@lang('restaurant.general_settings')</span>
                    <i class="fas fa-external-link-alt"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <button class="item hide-mobile-only">
            <a href="{{action('Auth\LoginController@logout')}}">
              <img src="{{ asset('img/icons/log-out-04.svg') }}" alt="">
            </a>
          </button>

          <button class="item display-mobile-only" id="menu-btn" onclick="toggle_mobile_stats()">
              <img src="{{ asset('img/icons/menu-flex.svg') }}" alt="">
          </button>
      </div>

  </div>
  @endif
</div>

{{-- Go to Top Button for Kitchen Station Pages --}}
@if($isKitchenStationPage)
<button class="go-to-top-btn" id="goToTopBtn" onclick="scroll_to_top()" title="@lang('restaurant.go_to_top')">
    <i class="fas fa-chevron-up"></i>
</button>
@endif

