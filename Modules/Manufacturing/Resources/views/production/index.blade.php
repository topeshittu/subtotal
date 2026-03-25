@extends('layouts.app')
@section('title', __('manufacturing::lang.production'))

@section('content')

<div class="main-container">
                
    <!-- Sub Menu -->
    <div class="horizontal-scroll">
        @include('manufacturing::layouts.nav')
    </div>

    <!-- Card Wrapper for dashboard content -->
    <div class="card-wrapper">
        <!-- Filter through table -->
        <div class="overview-filter">
            <div class="title">
                <h1>@lang('manufacturing::lang.production')</h1>
                <p>{{__('manufacturing::lang.manufacturing')}}</p>
            </div>

            <div class="filter">
                <div class="new-user">
                    <a class="btn btn-block btn-primary" href="{{action('\Modules\Manufacturing\Http\Controllers\ProductionController@create')}}">
                        <i class="fa fa-plus"></i> @lang( 'messages.add' )
                    </a>
                </div>

                <a class="filter-modal-btn" data-toggle="collapse" data-parent="#accordion" href="#collapseFilter">
                    <img src="{{ asset('img/icons/filter.svg') }}" alt="">
                    <span>Filter</span>
                </a>
            </div>
        </div>
        <!-- End of Filter through table -->
 @component('components.filters', ['title' => __('report.filters')])
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('productstion_list_filter_location_id',  __('purchase.business_location') . ':') !!}

                {!! Form::select('productstion_list_filter_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all') ]); !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('production_list_filter_date_range', __('report.date_range') . ':') !!}
                {!! Form::text('production_list_filter_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <p>{{ __('manufacturing::lang.finalize') }}</p>
                <div class="toggle-wrapper d-flex gap-2 mt-4">
                    <label class="switch" for="production_list_is_final">
                        {!! Form::checkbox('production_list_is_final', 1, false, ['id' => 'production_list_is_final']) !!}
                        <div class="sliderCheckbox round"></div>
                    </label>
                </div>
            </div>
            
        </div>
    @endcomponent
 <div class="content">

                <div class="table-responsive">
                    <table class="table max-table" id="productions_table">
                         <thead>
                            <tr>
                                <th>@lang('messages.date')</th>
                                <th>@lang('purchase.ref_no')</th>
                                <th>@lang('purchase.location')</th>
                                <th>@lang('sale.product')</th>
                                <th>@lang('lang_v1.quantity')</th>
                                <th>@lang('manufacturing::lang.total_cost')</th>
                                <th>@lang('messages.action')</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="modal fade" id="recipe_modal" tabindex="-1" role="dialog" 
                aria-labelledby="gridSystemModalLabel">
            </div>
    </div>
</div>
@stop
@section('javascript')
    @include('manufacturing::layouts.partials.common_script')
@endsection
