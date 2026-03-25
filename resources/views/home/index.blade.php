@extends('layouts.app')
@section('title', __('home.home'))
@section('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/home-redesign.css') }}">
@endsection
@section('content')
@php
    $dashboard_settings = !empty(session()->get('business.dashboard_settings')) ? json_decode(session()->get('business.dashboard_settings'), true) : [];
    $sales_due_disabled = ($dashboard_settings['disable_sales_due'] ?? 0) == 1;
    $purchase_due_disabled = ($dashboard_settings['disable_purchase_due'] ?? 0) == 1;
    $stock_alert_disabled = ($dashboard_settings['disable_stock_alert'] ?? 0) == 1;
    $stock_expiry_disabled = ($dashboard_settings['disable_stock_expiry'] ?? 0) == 1;
    $sales_order_disabled = ($dashboard_settings['disable_sales_order'] ?? 0) == 1;
    $purchase_order_disabled = ($dashboard_settings['disable_purchase_order'] ?? 0) == 1;
    $pending_shipments_disabled = ($dashboard_settings['disable_pending_shipments'] ?? 0) == 1;
    $payment_recover_disabled = ($dashboard_settings['disable_payment_recover'] ?? 0) == 1;
    $performance_disabled = ($dashboard_settings['disable_performance'] ?? 0) == 1;
    $fy_sales_chart_disabled = ($dashboard_settings['disable_fy_sales_chart'] ?? 0) == 1;
    $purchase_requisition_disabled = ($dashboard_settings['disable_purchase_requisition'] ?? 0) == 1;
    //Widgets
    $sell_return_widget_disabled = ($dashboard_settings['disable_sell_return_widget'] ?? 0) == 1;
    $purchase_return_widget_disabled = ($dashboard_settings['disable_purchase_return_widget'] ?? 0) == 1;
    $expense_widget_disabled = ($dashboard_settings['disable_expense_widget'] ?? 0) == 1;
    $total_purchase_widget_disabled = ($dashboard_settings['disable_total_purchase_widget'] ?? 0) == 1;
    $purchase_due_widget_disabled = ($dashboard_settings['disable_purchase_due_widget'] ?? 0) == 1;
@endphp

{{-- ═══════════════════ HERO HEADER ═══════════════════ --}}
<div class="db-hero">
    <div class="db-hero-top">
        <div class="db-welcome">
            <div class="db-welcome-badge">
                <i class="fas fa-tachometer-alt"></i> @lang('home.home')
            </div>
            <h1 class="db-biz-name">{{ Session::get('business.name') }}</h1>
            <p class="db-welcome-msg">{{ __('home.welcome_message', ['name' => Session::get('user.first_name')]) }}</p>
        </div>
        @if (auth()->user()->can('dashboard.data'))
            @if ($is_admin)
                <div class="db-filters">
                    @if (count($all_locations) > 1)
                        {!! Form::select('dashboard_location', $all_locations, null, [
                            'class' => 'form-control select2 db-select',
                            'placeholder' => __('lang_v1.select_location'),
                            'id' => 'dashboard_location',
                        ]) !!}
                    @endif
                    <button id="dashboard_date_filter" class="db-date-btn">
                        <i class="fas fa-calendar-alt"></i>
                        <span>@lang('lang_v1.filter')</span>
                    </button>
                </div>
            @endif
        @endif
    </div>

    @if (auth()->user()->can('dashboard.data'))
        @if ($is_admin)
            {{-- ── KPI Cards ── --}}
            <div class="db-kpi-row">

                {{-- Total Sales --}}
                <div class="db-kpi-card db-kpi-sales">
                    <div class="db-kpi-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="db-kpi-body">
                        <span class="db-kpi-label">{{ __('home.total_sell') }}</span>
                        <div class="db-kpi-value">
                            <span class="info-box-number total_sell"><i class="fas fa-sync fa-spin fa-fw"></i>0.00</span>
                        </div>
                        <span class="db-kpi-period dashboard_date_filter_value">{{ __('lang_v1.today') }}</span>
                    </div>
                </div>

                {{-- Sell Return --}}
                @if ($sell_return_widget_disabled == false)
                <div class="db-kpi-card db-kpi-return">
                    <div class="db-kpi-icon">
                        <i class="fas fa-undo-alt"></i>
                    </div>
                    <div class="db-kpi-body">
                        <span class="db-kpi-label">{{ __('lang_v1.total_sell_return') }}</span>
                        <div class="db-kpi-value">
                            <span class="info-box-number total_sell_return"><i class="fas fa-sync fa-spin fa-fw"></i>0.00</span>
                        </div>
                        <span class="db-kpi-period dashboard_date_filter_value">{{ __('lang_v1.today') }}</span>
                    </div>
                </div>
                @endif

                {{-- Invoice Due --}}
                <div class="db-kpi-card db-kpi-due">
                    <div class="db-kpi-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="db-kpi-body">
                        <span class="db-kpi-label">{{ __('home.invoice_due') }}</span>
                        <div class="db-kpi-value">
                            <span class="info-box-number invoice_due"><i class="fas fa-sync fa-spin fa-fw"></i>0.00</span>
                        </div>
                        <span class="db-kpi-period dashboard_date_filter_value">{{ __('lang_v1.today') }}</span>
                    </div>
                </div>

                {{-- Total Payments --}}
                <div class="db-kpi-card db-kpi-payments">
                    <div class="db-kpi-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="db-kpi-body">
                        <span class="db-kpi-label">{{ __('home.total_payments') }}</span>
                        <div class="db-kpi-value">
                            <span class="info-box-number total_payments"><i class="fas fa-sync fa-spin fa-fw"></i>0.00</span>
                        </div>
                        <span class="db-kpi-period dashboard_date_filter_value">{{ __('lang_v1.today') }}</span>
                    </div>
                </div>

                {{-- Net Sales --}}
                <div class="db-kpi-card db-kpi-net">
                    <div class="db-kpi-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <div class="db-kpi-body">
                        <span class="db-kpi-label">{{ __('home.net_sales') }}</span>
                        <div class="db-kpi-value">
                            <span class="info-box-number net_sales"><i class="fas fa-sync fa-spin fa-fw"></i>0.00</span>
                        </div>
                        <span class="db-kpi-period dashboard_date_filter_value">{{ __('lang_v1.today') }}</span>
                    </div>
                </div>

            </div>{{-- .db-kpi-row --}}
        @endif
    @endif
</div>{{-- .db-hero --}}

{{-- ═══════════════════ MAIN BODY ═══════════════════ --}}
<div class="db-body">

    @if (auth()->user()->can('dashboard.data'))
        @if ($is_admin)

            {{-- ── Widget hooks ── --}}
            @if (!empty($widgets['after_sale_purchase_totals']))
                @foreach ($widgets['after_sale_purchase_totals'] as $widget)
                    {!! $widget !!}
                @endforeach
            @endif

            {{-- ── Performance / Charts ── --}}
            @if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view'))
                @if (!empty($all_locations))
                    @if ($performance_disabled == false)

                        <div class="db-section-header">
                            <span class="db-section-icon"><i class="fas fa-chart-line"></i></span>
                            <h2>{{ __('lang_v1.performance') }}</h2>
                        </div>

                        <div class="db-charts-layout">
                            <div class="db-charts-main">
                                <div class="db-chart-card">
                                    @component('components.widget', ['class' => 'box-primary', 'title' => __('home.sells_last_30_days')])
                                        {!! $sells_chart_1->container() !!}
                                    @endcomponent
                                </div>
                                @if ($fy_sales_chart_disabled == false)
                                <div class="db-chart-card">
                                    @component('components.widget', ['class' => 'box-primary', 'title' => __('home.sells_current_fy')])
                                        {!! $sells_chart_2->container() !!}
                                    @endcomponent
                                </div>
                                @endif
                            </div>

                            @if (!$expense_widget_disabled || !$total_purchase_widget_disabled || !$purchase_due_widget_disabled || !$purchase_return_widget_disabled)
                            <div class="db-stats-sidebar">
                                @if ($expense_widget_disabled == false)
                                <div class="db-stat-card">
                                    <div class="db-stat-icon db-stat-expense">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </div>
                                    <div class="db-stat-body">
                                        <span class="db-stat-label">{{ __('lang_v1.expense') }}</span>
                                        <span class="db-stat-value total_expense">0.00</span>
                                        <span class="db-stat-period dashboard_date_filter_value">{{ __('lang_v1.today') }}</span>
                                    </div>
                                </div>
                                @endif

                                @if ($total_purchase_widget_disabled == false)
                                <div class="db-stat-card">
                                    <div class="db-stat-icon db-stat-purchase">
                                        <i class="fas fa-shopping-bag"></i>
                                    </div>
                                    <div class="db-stat-body">
                                        <span class="db-stat-label">{{ __('home.total_purchase') }}</span>
                                        <span class="db-stat-value total_purchase">0.00</span>
                                        <span class="db-stat-period dashboard_date_filter_value">{{ __('lang_v1.today') }}</span>
                                    </div>
                                </div>
                                @endif

                                @if ($purchase_due_widget_disabled == false)
                                <div class="db-stat-card">
                                    <div class="db-stat-icon db-stat-pdue">
                                        <i class="fas fa-exclamation-triangle"></i>
                                    </div>
                                    <div class="db-stat-body">
                                        <span class="db-stat-label">{{ __('home.purchase_due') }}</span>
                                        <span class="db-stat-value purchase_due">0.00</span>
                                        <span class="db-stat-period dashboard_date_filter_value">{{ __('lang_v1.today') }}</span>
                                    </div>
                                </div>
                                @endif

                                @if ($purchase_return_widget_disabled == false)
                                <div class="db-stat-card">
                                    <div class="db-stat-icon db-stat-preturn">
                                        <i class="fas fa-sync-alt"></i>
                                    </div>
                                    <div class="db-stat-body">
                                        <span class="db-stat-label">{{ __('lang_v1.total_purchase_return') }}</span>
                                        <span class="db-stat-value total_purchase_return">0.00</span>
                                        <span class="db-stat-period dashboard_date_filter_value">{{ __('lang_v1.today') }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>

                    @endif
                @endif
            @endif

            {{-- ── Widget hooks ── --}}
            @if (!empty($widgets['after_sales_last_30_days']))
                @foreach ($widgets['after_sales_last_30_days'] as $widget)
                    {!! $widget !!}
                @endforeach
            @endif
            @if (!empty($widgets['after_sales_current_fy']))
                @foreach ($widgets['after_sales_current_fy'] as $widget)
                    {!! $widget !!}
                @endforeach
            @endif

            {{-- ── Data Tables ── --}}
            <div class="db-section-header">
                <span class="db-section-icon"><i class="fas fa-table"></i></span>
                <h2>@lang('home.home')</h2>
            </div>

            <div class="db-tables-grid">

                {{-- Sales Payment Dues --}}
                @if ($sales_due_disabled == false)
                    @if (auth()->user()->can('sell.view') || auth()->user()->can('direct_sell.view'))
                        <div class="db-table-card">
                            <div class="db-table-header">
                                <div class="db-table-header-left">
                                    <span class="db-table-icon"><i class="fas fa-file-invoice-dollar"></i></span>
                                    <h3>{{ __('lang_v1.sales_payment_dues') }} @show_tooltip(__('lang_v1.tooltip_sales_payment_dues'))</h3>
                                </div>
                                <button class="db-collapse-btn"><i class="fas fa-chevron-down"></i></button>
                            </div>
                            <div class="db-table-body">
                                @if (count($all_locations) > 1)
                                    <div class="db-table-filter">
                                        {!! Form::select('sales_payment_dues_location', $all_locations, null, [
                                            'class' => 'form-control select2',
                                            'placeholder' => __('lang_v1.select_location'),
                                            'id' => 'sales_payment_dues_location',
                                        ]) !!}
                                    </div>
                                @endif
                                <div class="db-table-wrap">
                                    <table class="max-table" id="sales_payment_dues_table" style="width:100%;">
                                        <thead><tr>
                                            <th>@lang('contact.customer')</th>
                                            <th>@lang('sale.payment_status')</th>
                                            <th>@lang('sale.invoice_no')</th>
                                            <th>@lang('home.due_amount')</th>
                                            <th>@lang('messages.action')</th>
                                        </tr></thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                {{-- Purchase Payment Dues --}}
                @if ($purchase_due_disabled == false)
                    @can('purchase.view')
                        <div class="db-table-card">
                            <div class="db-table-header">
                                <div class="db-table-header-left">
                                    <span class="db-table-icon"><i class="fas fa-truck"></i></span>
                                    <h3>{{ __('lang_v1.purchase_payment_dues') }} @show_tooltip(__('tooltip.payment_dues'))</h3>
                                </div>
                                <button class="db-collapse-btn"><i class="fas fa-chevron-down"></i></button>
                            </div>
                            <div class="db-table-body">
                                @if (count($all_locations) > 1)
                                    <div class="db-table-filter">
                                        {!! Form::select('purchase_payment_dues_location', $all_locations, null, [
                                            'class' => 'form-control select2',
                                            'placeholder' => __('lang_v1.select_location'),
                                            'id' => 'purchase_payment_dues_location',
                                        ]) !!}
                                    </div>
                                @endif
                                <div class="db-table-wrap">
                                    <table class="max-table" id="purchase_payment_dues_table" style="width:100%;">
                                        <thead><tr>
                                            <th>@lang('purchase.supplier')</th>
                                            <th>@lang('purchase.ref_no')</th>
                                            <th>@lang('home.due_amount')</th>
                                            <th>@lang('messages.action')</th>
                                        </tr></thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endcan
                @endif

                {{-- Stock Alert --}}
                @if ($stock_alert_disabled == false)
                    @can('stock_report.view')
                        <div class="db-table-card">
                            <div class="db-table-header">
                                <div class="db-table-header-left">
                                    <span class="db-table-icon"><i class="fas fa-boxes"></i></span>
                                    <h3>{{ __('home.product_stock_alert') }}</h3>
                                </div>
                                <button class="db-collapse-btn"><i class="fas fa-chevron-down"></i></button>
                            </div>
                            <div class="db-table-body">
                                <div class="db-table-wrap">
                                    <table class="max-table" id="stock_alert_table" style="width:100%;">
                                        <thead><tr>
                                            <th>@lang('sale.product')</th>
                                            <th>@lang('business.location')</th>
                                            <th>@lang('report.current_stock')</th>
                                        </tr></thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endcan
                @endif

                {{-- Stock Expiry Alert --}}
                @if ($stock_expiry_disabled == false)
                    @if (session('business.enable_product_expiry') == 1)
                        <div class="db-table-card">
                            <div class="db-table-header">
                                <div class="db-table-header-left">
                                    <span class="db-table-icon"><i class="fas fa-calendar-times"></i></span>
                                    <h3>{{ __('home.stock_expiry_alert') }}</h3>
                                </div>
                                <button class="db-collapse-btn"><i class="fas fa-chevron-down"></i></button>
                            </div>
                            <div class="db-table-body">
                                <input type="hidden" id="stock_expiry_alert_days" value="{{ \Carbon\Carbon::now()->addDays((int) session('business.stock_expiry_alert_days', 30))->format('Y-m-d') }}">
                                <div class="db-table-wrap">
                                    <table class="max-table" id="stock_expiry_alert_table" style="width:100%;">
                                        <thead><tr>
                                            <th>@lang('business.product')</th>
                                            <th>@lang('business.location')</th>
                                            <th>@lang('report.stock_left')</th>
                                            <th>@lang('product.expires_in')</th>
                                        </tr></thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                {{-- Sales Order --}}
                @if ($sales_order_disabled == false)
                    @if (auth()->user()->can('so.view_all') || auth()->user()->can('so.view_own'))
                        <div class="db-table-card">
                            <div class="db-table-header">
                                <div class="db-table-header-left">
                                    <span class="db-table-icon"><i class="fas fa-receipt"></i></span>
                                    <h3>{{ __('lang_v1.sales_order') }}</h3>
                                </div>
                                <button class="db-collapse-btn"><i class="fas fa-chevron-down"></i></button>
                            </div>
                            <div class="db-table-body">
                                @if (count($all_locations) > 1)
                                    <div class="db-table-filter">
                                        {!! Form::select('so_location', $all_locations, null, [
                                            'class' => 'form-control select2',
                                            'placeholder' => __('lang_v1.select_location'),
                                            'id' => 'so_location',
                                        ]) !!}
                                    </div>
                                @endif
                                <div class="db-table-wrap">
                                    <table class="max-table" id="sales_order_table" style="width:100%;">
                                        <thead><tr>
                                            <th>@lang('messages.action')</th>
                                            <th>@lang('messages.date')</th>
                                            <th>@lang('restaurant.order_no')</th>
                                            <th>@lang('sale.customer_name')</th>
                                            <th>@lang('sale.status')</th>
                                            <th>@lang('lang_v1.shipping_status')</th>
                                            <th>@lang('lang_v1.quantity_remaining')</th>
                                            <th>@lang('lang_v1.added_by')</th>
                                        </tr></thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                {{-- Purchase Requisition --}}
                @if ($purchase_requisition_disabled == false)
                @if (!empty($common_settings['enable_purchase_requisition']) && (auth()->user()->can('purchase_requisition.view_all') || auth()->user()->can('purchase_requisition.view_own')))
                    <div class="db-table-card">
                        <div class="db-table-header">
                            <div class="db-table-header-left">
                                <span class="db-table-icon"><i class="fas fa-clipboard-list"></i></span>
                                <h3>@lang('lang_v1.purchase_requisition')</h3>
                            </div>
                            <button class="db-collapse-btn"><i class="fas fa-chevron-down"></i></button>
                        </div>
                        <div class="db-table-body">
                            @if (count($all_locations) > 1)
                                <div class="db-table-filter">
                                    {!! Form::select('pr_location', $all_locations, null, [
                                        'class' => 'form-control select2',
                                        'placeholder' => __('lang_v1.select_location'),
                                        'id' => 'pr_location',
                                    ]) !!}
                                </div>
                            @endif
                            <div class="db-table-wrap">
                                <table class="max-table" id="purchase_requisition_table" style="width:100%;">
                                    <thead><tr>
                                        <th>@lang('messages.action')</th>
                                        <th>@lang('messages.date')</th>
                                        <th>@lang('purchase.ref_no')</th>
                                        <th>@lang('purchase.location')</th>
                                        <th>@lang('sale.status')</th>
                                        <th>@lang('lang_v1.required_by_date')</th>
                                        <th>@lang('lang_v1.added_by')</th>
                                    </tr></thead>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
                @endif

                {{-- Purchase Order --}}
                @if ($purchase_order_disabled == false)
                    @if (!empty($common_settings['enable_purchase_order']) && (auth()->user()->can('purchase_order.view_all') || auth()->user()->can('purchase_order.view_own')))
                        <div class="db-table-card">
                            <div class="db-table-header">
                                <div class="db-table-header-left">
                                    <span class="db-table-icon"><i class="fas fa-shopping-basket"></i></span>
                                    <h3>@lang('lang_v1.purchase_order')</h3>
                                </div>
                                <button class="db-collapse-btn"><i class="fas fa-chevron-down"></i></button>
                            </div>
                            <div class="db-table-body">
                                @if (count($all_locations) > 1)
                                    <div class="db-table-filter">
                                        {!! Form::select('po_location', $all_locations, null, [
                                            'class' => 'form-control select2',
                                            'placeholder' => __('lang_v1.select_location'),
                                            'id' => 'po_location',
                                        ]) !!}
                                    </div>
                                @endif
                                <div class="db-table-wrap">
                                    <table class="max-table" id="purchase_order_table" style="width:100%;">
                                        <thead><tr>
                                            <th>@lang('messages.action')</th>
                                            <th>@lang('messages.date')</th>
                                            <th>@lang('purchase.ref_no')</th>
                                            <th>@lang('purchase.location')</th>
                                            <th>@lang('purchase.supplier')</th>
                                            <th>@lang('sale.status')</th>
                                            <th>@lang('lang_v1.quantity_remaining')</th>
                                            <th>@lang('lang_v1.added_by')</th>
                                        </tr></thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                {{-- Pending Shipments --}}
                @if ($pending_shipments_disabled == false)
                    @if (auth()->user()->can('access_pending_shipments_only') || auth()->user()->can('access_shipping') || auth()->user()->can('access_own_shipping'))
                        <div class="db-table-card">
                            <div class="db-table-header">
                                <div class="db-table-header-left">
                                    <span class="db-table-icon"><i class="fas fa-shipping-fast"></i></span>
                                    <h3>@lang('lang_v1.pending_shipments')</h3>
                                </div>
                                <button class="db-collapse-btn"><i class="fas fa-chevron-down"></i></button>
                            </div>
                            <div class="db-table-body">
                                @if (count($all_locations) > 1)
                                    <div class="db-table-filter">
                                        {!! Form::select('pending_shipments_location', $all_locations, null, [
                                            'class' => 'form-control select2',
                                            'placeholder' => __('lang_v1.select_location'),
                                            'id' => 'pending_shipments_location',
                                        ]) !!}
                                    </div>
                                @endif
                                <div class="db-table-wrap">
                                    <table class="max-table" id="shipments_table" style="width:100%;">
                                        <thead><tr>
                                            <th>@lang('messages.action')</th>
                                            <th>@lang('messages.date')</th>
                                            <th>@lang('sale.invoice_no')</th>
                                            <th>@lang('sale.customer_name')</th>
                                            <th>@lang('lang_v1.contact_no')</th>
                                            <th>@lang('sale.location')</th>
                                            <th>@lang('lang_v1.shipping_status')</th>
                                            @if (!empty($custom_labels['shipping']['custom_field_1']))
                                                <th>{{ $custom_labels['shipping']['custom_field_1'] }}</th>
                                            @endif
                                            @if (!empty($custom_labels['shipping']['custom_field_2']))
                                                <th>{{ $custom_labels['shipping']['custom_field_2'] }}</th>
                                            @endif
                                            @if (!empty($custom_labels['shipping']['custom_field_3']))
                                                <th>{{ $custom_labels['shipping']['custom_field_3'] }}</th>
                                            @endif
                                            @if (!empty($custom_labels['shipping']['custom_field_4']))
                                                <th>{{ $custom_labels['shipping']['custom_field_4'] }}</th>
                                            @endif
                                            @if (!empty($custom_labels['shipping']['custom_field_5']))
                                                <th>{{ $custom_labels['shipping']['custom_field_5'] }}</th>
                                            @endif
                                            <th>@lang('sale.payment_status')</th>
                                            <th>@lang('restaurant.service_staff')</th>
                                        </tr></thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

                {{-- Payment Recovered Today --}}
                @if ($payment_recover_disabled == false)
                    @if (auth()->user()->can('account.access') && config('constants.show_payments_recovered_today') == true)
                        <div class="db-table-card">
                            <div class="db-table-header">
                                <div class="db-table-header-left">
                                    <span class="db-table-icon"><i class="fas fa-hand-holding-usd"></i></span>
                                    <h3>@lang('lang_v1.payment_recovered_today')</h3>
                                </div>
                                <button class="db-collapse-btn"><i class="fas fa-chevron-down"></i></button>
                            </div>
                            <div class="db-table-body">
                                <div class="db-table-wrap">
                                    <table class="max-table" id="cash_flow_table" style="width:100%;">
                                        <thead><tr>
                                            <th>@lang('messages.date')</th>
                                            <th>@lang('account.account')</th>
                                            <th>@lang('lang_v1.description')</th>
                                            <th>@lang('lang_v1.payment_method')</th>
                                            <th>@lang('lang_v1.payment_details')</th>
                                            <th>@lang('account.credit')</th>
                                            <th>@lang('lang_v1.account_balance') @show_tooltip(__('lang_v1.account_balance_tooltip'))</th>
                                            <th>@lang('lang_v1.total_balance') @show_tooltip(__('lang_v1.total_balance_tooltip'))</th>
                                        </tr></thead>
                                        <tfoot>
                                            <tr class="bg-gray font-17 footer-total text-center">
                                                <td colspan="5"><strong>@lang('sale.total'):</strong></td>
                                                <td class="footer_total_credit"></td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif

            </div>{{-- .db-tables-grid --}}

            @if (!empty($widgets['after_dashboard_reports']))
                @foreach ($widgets['after_dashboard_reports'] as $widget)
                    {!! $widget !!}
                @endforeach
            @endif

        @endif {{-- is_admin --}}
    @endif {{-- can dashboard.data --}}

</div>{{-- .db-body --}}

<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
<div class="modal fade edit_pso_status_modal" tabindex="-1" role="dialog"></div>
<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
@include('home.email_verification_modal') @include('home.review_modal')
@stop
@section('javascript')
<script src="{{ asset('js/home.js?v=' . $asset_v) }}"></script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@includeIf('sales_order.common_js')
@includeIf('purchase_order.common_js')
@if (!empty($all_locations))
    {!! $sells_chart_1->script() !!}
    {!! $sells_chart_2->script() !!}
@endif
<script type="text/javascript">
    if ("{{ session()->get('review_alert') }}") {
        $('#review_modal').modal('toggle');
    }

    function hasOneDayPassed() {
        var date = new Date().toLocaleDateString();
        if (localStorage.yourapp_date == date) return false;
        localStorage.yourapp_date = date;
        return true;
    }

    function runOncePerDay() {
        if (!hasOneDayPassed()) return false;
        $('#message_modal').modal('toggle');
    }

    $(document).ready(function() {

        // ── Collapsible table cards ──
        $(document).on('click', '.db-table-header', function() {
            $(this).closest('.db-table-card').toggleClass('db-collapsed');
        });

        sales_order_table = $('#sales_order_table').DataTable({
            processing: true,
            serverSide: true,
            searching: false,
            scrollY: "75vh",
            scrollX: true,
            scrollCollapse: true,
            fixedHeader: false,
            dom: 'Btirp',
            "ajax": {
                "url": '{{ action('SellController@index') }}?sale_type=sales_order',
                "data": function(d) {
                    d.for_dashboard_sales_order = true;
                    if ($('#so_location').length > 0) {
                        d.location_id = $('#so_location').val();
                    }
                }
            },
            columns: [
                { data: 'action', name: 'action' },
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'invoice_no', name: 'invoice_no' },
                { data: 'conatct_name', name: 'conatct_name' },
                { data: 'status', name: 'status' },
                { data: 'shipping_status', name: 'shipping_status' },
                { data: 'so_qty_remaining', name: 'so_qty_remaining', "searchable": false },
                { data: 'added_by', name: 'u.first_name' },
            ],
            fnDrawCallback: function(oSettings) {
                __show_date_diff_for_human($('#sales_order_table'));
                __currency_convert_recursively($('#sales_order_table'));
            },
        });

        @if (auth()->user()->can('account.access') && config('constants.show_payments_recovered_today') == true)
            cash_flow_table = $('#cash_flow_table').DataTable({
                processing: true,
                serverSide: true,
                "ajax": {
                    "url": "{{ action([\App\Http\Controllers\AccountController::class, 'cashFlow']) }}",
                    "data": function(d) {
                        d.type = 'credit';
                        d.only_payment_recovered = true;
                    }
                },
                "ordering": false,
                "searching": false,
                dom: 'Btirp',
                columns: [
                    { data: 'operation_date', name: 'operation_date' },
                    { data: 'account_name', name: 'account_name' },
                    { data: 'sub_type', name: 'sub_type' },
                    { data: 'method', name: 'TP.method' },
                    { data: 'payment_details', name: 'payment_details', searchable: false },
                    { data: 'credit', name: 'amount' },
                    { data: 'balance', name: 'balance' },
                    { data: 'total_balance', name: 'total_balance' },
                ],
                "fnDrawCallback": function(oSettings) {
                    __currency_convert_recursively($('#cash_flow_table'));
                },
                "footerCallback": function(row, data, start, end, display) {
                    var footer_total_credit = 0;
                    for (var r in data) {
                        footer_total_credit += $(data[r].credit).data('orig-value') ? parseFloat($(data[r].credit).data('orig-value')) : 0;
                    }
                    $('.footer_total_credit').html(__currency_trans_from_en(footer_total_credit));
                }
            });
        @endif

        $('#so_location').change(function() {
            sales_order_table.ajax.reload();
        });

        @if (!empty($common_settings['enable_purchase_order']))
            purchase_order_table = $('#purchase_order_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[1, 'desc']],
                scrollY: "75vh",
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: '{{ action('PurchaseOrderController@index') }}',
                    data: function(d) {
                        d.from_dashboard = true;
                        if ($('#po_location').length > 0) {
                            d.location_id = $('#po_location').val();
                        }
                    },
                },
                dom: 'Btirp',
                columns: [
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                    { data: 'transaction_date', name: 'transaction_date' },
                    { data: 'ref_no', name: 'ref_no' },
                    { data: 'location_name', name: 'BS.name' },
                    { data: 'name', name: 'contacts.name' },
                    { data: 'status', name: 'transactions.status' },
                    { data: 'po_qty_remaining', name: 'po_qty_remaining', "searchable": false },
                    { data: 'added_by', name: 'u.first_name' }
                ]
            });
            $('#po_location').change(function() {
                purchase_order_table.ajax.reload();
            });
        @endif

        sell_table = $('#shipments_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[1, 'desc']],
            scrollY: "75vh",
            scrollX: true,
            scrollCollapse: true,
            "ajax": {
                "url": '{{ action('SellController@index') }}',
                "data": function(d) {
                    d.only_pending_shipments = true;
                    if ($('#pending_shipments_location').length > 0) {
                        d.location_id = $('#pending_shipments_location').val();
                    }
                }
            },
            dom: 'Btirp',
            columns: [
                { data: 'action', name: 'action', searchable: false, orderable: false },
                { data: 'transaction_date', name: 'transaction_date' },
                { data: 'invoice_no', name: 'invoice_no' },
                { data: 'conatct_name', name: 'conatct_name' },
                { data: 'mobile', name: 'contacts.mobile' },
                { data: 'business_location', name: 'bl.name' },
                { data: 'shipping_status', name: 'shipping_status' },
                @if (!empty($custom_labels['shipping']['custom_field_1']))
                    { data: 'shipping_custom_field_1', name: 'shipping_custom_field_1' },
                @endif
                @if (!empty($custom_labels['shipping']['custom_field_2']))
                    { data: 'shipping_custom_field_2', name: 'shipping_custom_field_2' },
                @endif
                @if (!empty($custom_labels['shipping']['custom_field_3']))
                    { data: 'shipping_custom_field_3', name: 'shipping_custom_field_3' },
                @endif
                @if (!empty($custom_labels['shipping']['custom_field_4']))
                    { data: 'shipping_custom_field_4', name: 'shipping_custom_field_4' },
                @endif
                @if (!empty($custom_labels['shipping']['custom_field_5']))
                    { data: 'shipping_custom_field_5', name: 'shipping_custom_field_5' },
                @endif
                { data: 'payment_status', name: 'payment_status' },
                {
                    data: 'waiter',
                    name: 'ss.first_name',
                    @if (empty($is_service_staff_enabled))
                        visible: false
                    @endif
                }
            ],
            "fnDrawCallback": function(oSettings) {
                __currency_convert_recursively($('#sell_table'));
            },
            createdRow: function(row, data, dataIndex) {
                $(row).find('td:eq(4)').attr('class', 'clickable_td');
            }
        });

        $('#pending_shipments_location').change(function() {
            sell_table.ajax.reload();
        });
    });
</script>
@endsection
