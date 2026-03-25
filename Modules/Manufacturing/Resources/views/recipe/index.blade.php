@extends('layouts.app')
@section('title', __('manufacturing::lang.recipe'))

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
                <h1>@lang('manufacturing::lang.recipe')</h1>
                <p>{{__('manufacturing::lang.manufacturing')}}</p>
            </div>

            <div class="filter">
                <div class="new-user">
                    @can("manufacturing.add_recipe")
                        <button class="btn-modal" data-container="#recipe_modal" data-href="{{action('\Modules\Manufacturing\Http\Controllers\RecipeController@create')}}">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )
                        </button>
                     @endcan
                </div>
            </div>
        </div>
        <!-- End of Filter through table -->

            <div class="content">
                <div class="table-responsive">
                    <table class="table max-table" id="recipe_table">
                        <thead>
                            <tr>
                                <th><input type="checkbox" id="select-all-row" data-table-id="recipe_table"></th>
                                <th>@lang( 'manufacturing::lang.recipe' )</th>
                                <th>@lang( 'product.category' )</th>
                                <th>@lang( 'product.sub_category' )</th>
                                <th>@lang( 'lang_v1.quantity' )</th>
                                <th>@lang( 'lang_v1.price' ) @show_tooltip(__('manufacturing::lang.price_updated_live'))</th>
                                <th>@lang( 'sale.unit_price' )</th>
                                <th>@lang( 'messages.action' )</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td colspan="8">
                                    <button type="button" class="btn btn-xs btn-danger" style="background: #f5365c;" id="mass_update_product_price" >@lang('manufacturing::lang.update_product_price')</button> @show_tooltip(__('manufacturing::lang.update_product_price_help'))
                                </td>
                            </tr>
                        </tfoot>
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
