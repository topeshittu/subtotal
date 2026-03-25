@extends('layouts.app')

@section('title', __('sale.pos_sale'))
@section('css')
    <style type="text/css">
        .table {
            width: 100%;
            max-width: 100%;
            margin-bottom: 0px !important;
        }

        label {
            margin-bottom: 0 !important;
        }

        @media screen {
            #printSection {
                display: none;
            }
        }

        @media print {

            #printSection,
            #printSection * {
                visibility: visible;
            }

            #printSection {
                position: absolute;
                left: 0;
                top: 0;
            }
        }

        @media (max-width: 992px) {
            #hide_suggestion_btn {
                display: none;
            }
        }
    </style>

@endsection
@section('content')
    <section class="content no-print">
        <input type="hidden" id="amount_rounding_method" value="{{ $pos_settings['amount_rounding_method'] ?? '' }}">
        @if (!empty($pos_settings['allow_overselling']))
            <input type="hidden" id="is_overselling_allowed">
        @endif
        @if (!empty($pos_settings['restrict_discount_to_max_value']))
            <input type="hidden" id="restrict_discount_to_max_value" value="1">
        @endif
        @if (session('business.enable_rp') == 1)
            <input type="hidden" id="reward_point_enabled">
        @endif
        @php
            $is_discount_enabled = $pos_settings['disable_discount'] != 1 ? true : false;
            $is_rp_enabled = session('business.enable_rp') == 1 ? true : false;
        @endphp
        <div class="pos-table  no-print">
            <div class="row mb-12">
                <div class="col-md-12">
                    <div class="row">
                        <div class="@if (empty($pos_settings['hide_product_suggestion'])) col-md-8 @else col-md-12 col-md-offset-1 @endif no-padding"
                            id="cart">
                            <div class="box box-solid mb-12 @if (!isMobile()) mb-40 @endif">
                                <div class="box-body pb-0">
                                    {!! Form::open(['url' => action('SellPosController@store'), 'method' => 'post', 'id' => 'add_pos_sell_form']) !!}
                                    {!! Form::hidden('location_id', $default_location->id ?? null, [
                                        'id' => 'location_id',
                                        'data-receipt_printer_type' => !empty($default_location->receipt_printer_type)
                                            ? $default_location->receipt_printer_type
                                            : 'browser',
                                        'data-default_payment_accounts' => $default_location->default_payment_accounts ?? '',
                                    ]) !!}
                                    <!-- sub_type -->
                                    {!! Form::hidden('sub_type', isset($sub_type) ? $sub_type : null) !!}
                                    <input type="hidden" id="item_addition_method"
                                        value="{{ $business_details->item_addition_method }}">

                                    @include('sale_pos.partials.pos_form')

                                    @include('sale_pos.partials.payment_modal')
                                    @if (empty($pos_settings['disable_suspend']))
                                        @include('sale_pos.partials.suspend_note_modal')
                                    @endif
                                    @if (!empty($restaurant_settings['enable_kot']))
                                        @include('sale_pos.partials.place_order_modal')
                                    @endif
                                    @if (empty($pos_settings['disable_recurring_invoice']))
                                        @include('sale_pos.partials.recurring_invoice_modal')
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if (empty($pos_settings['hide_product_suggestion']) && !isMobile())
                            <button type="button" class="btn btn-default btn-sm" id="hide_suggestion_btn">
                                <i class="fa fa-angle-double-right"></i>
                            </button>
                        @endif
                        <!-- Refresh Cache Button - Only show when instant POS is enabled -->
                        @if (isset($business_details->enable_instant_pos) &&
                                $business_details->enable_instant_pos &&
                                $app_settings->enable_instant_pos &&
                                !isMobile())
                            <button type="button" class="btn btn-default btn-sm" id="refresh_pos_cache_btn"
                                onclick="refreshPOSCache()" title="@lang('settings.refresh_pos_cache')"
                                style="display: none; position: absolute; top: -22px; right: 25px; transform: translateX(-50%); z-index: 10; background: var(--primary-color); color: #fff;">
                                <i class="fas fa-sync"></i>
                            </button>
                        @endif
                        @if (empty($pos_settings['hide_product_suggestion']) && !isMobile())
                            <div class="col-md-4 no-padding" id="pos_sidebar">
                                @include('sale_pos.partials.pos_sidebar')
                            </div>
                        @endif
                        @include('sale_pos.partials.pos_form_actions')
                    </div>
                </div>
            </div>
        </div>

        {!! Form::close() !!}
    </section>
    <!-- This will be printed -->
    <section class="invoice print_section" id="receipt_section">
    </section>
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        @include('contact.create', ['quick_add' => true])
    </div>
    @if (empty($pos_settings['hide_product_suggestion']) && isMobile())
        @include('sale_pos.partials.mobile_product_suggestions')
    @endif
    <!-- /.content -->
    <div class="modal fade register_details_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade close_register_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <!-- quick product modal -->
    <div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
    <div class="modal fade" id="expense_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    @include('sale_pos.partials.configure_search_modal')

    @include('sale_pos.partials.recent_transactions_modal')

    @include('sale_pos.partials.mobile_products_modal')

    @include('sale_pos.partials.weighing_scale_modal')

@endsection
@section('css')
    <!-- include module css -->
    @if (!empty($pos_module_data))
        @foreach ($pos_module_data as $key => $value)
            @if (!empty($value['module_css_path']))
                @includeIf($value['module_css_path'])
            @endif
        @endforeach
    @endif
@stop
@section('javascript')
    <script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/pos_extra.js?v=' . $asset_v) }}"></script>
    {{-- POS App Settings --}}
    @include('sale_pos.partials.pos_realtime_js')
    <script src="{{ asset('js/calculator.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>

    @include('sale_pos.partials.keyboard_shortcuts')
    @include('sale_pos.partials.running_orders_modal')
    <!-- Call restaurant module if defined -->
    @if (in_array('tables', $enabled_modules) ||
            in_array('modifiers', $enabled_modules) ||
            in_array('service_staff', $enabled_modules))
        <script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
    <!-- include module js -->
    @if (!empty($pos_module_data))
        @foreach ($pos_module_data as $key => $value)
            @if (!empty($value['module_js_path']))
                @includeIf($value['module_js_path'], ['view_data' => $value['view_data']])
            @endif
        @endforeach
    @endif
@endsection
