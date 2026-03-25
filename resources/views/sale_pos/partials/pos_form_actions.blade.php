@php
    $is_mobile = isMobile();
@endphp
<div class="row no-print">
    <div class="pos-form-actions pos-form-actions-efficient">

        <div class="pos-top-row">
            <div class="pos-items-total">
                @include('sale_pos.partials.pos_form_totals')
            </div>

            <div class="pos-inline-charges">
                @if ($is_discount_enabled)
                    <span class="charge-item">
                        @lang('sale.discount')(-):
                        @if ($edit_discount)
                            <img src="{{ asset('img/icons/pencil.svg') }}"
                                @if ($is_mobile) style="width: 10px;" @endif class="cursor-pointer"
                                alt="" id="pos-edit-discount" title="@lang('sale.edit_discount')" aria-hidden="true"
                                data-toggle="modal" data-target="#posEditDiscountModal">
                        @endif
                        <span id="total_discount">0</span>
                    </span>
                @endif

                <span class="charge-item">
                    @lang('sale.shipping')(+):
                    <img src="{{ asset('img/icons/pencil.svg') }}"
                        @if ($is_mobile) style="width: 10px;" @endif class="cursor-pointer"
                        alt="" aria-hidden="true" title="@lang('sale.shipping')" data-toggle="modal"
                        data-target="#posShippingModal">
                    <span id="shipping_charges_amount">0</span>
                </span>

                <span class="charge-item @if ($pos_settings['disable_order_tax'] != 0) hide @endif">
                    @lang('sale.order_tax')(+):
                    <img src="{{ asset('img/icons/pencil.svg') }}"
                        @if ($is_mobile) style="width: 10px;" @endif class="cursor-pointer"
                        alt="" atitle="@lang('sale.edit_order_tax')" aria-hidden="true" data-toggle="modal"
                        data-target="#posEditOrderTaxModal" id="pos-edit-tax">
                    <span id="order_tax">
                        @if (!isset($transaction))
                            0
                        @else
                            {{ @num_format($transaction->tax_amount) }}
                        @endif
                    </span>
                </span>

                @if (in_array('types_of_service', $enabled_modules))
                    <span class="charge-item">
                        @lang('lang_v1.packing_charge')(+):
                        <img src="{{ asset('img/icons/pencil.svg') }}"
                            @if ($is_mobile) style="width: 10px;" @endif
                            class="cursor-pointer service_modal_btn" alt="">
                        <span id="packing_charge_text">0</span>
                    </span>
                @endif

                @if (!empty($pos_settings['amount_rounding_method']) && $pos_settings['amount_rounding_method'] > 0)
                    <span class="charge-item">
                        <span id="round_off">@lang('lang_v1.round_off'):</span> <span id="round_off_text">0</span>
                    </span>
                @endif
                
            </div>

	@if(in_array('subscription', $enabled_modules))
		<div class="col-md-4 col-sm-6">
			<div class="form-group">
				<div class="toggle-wrapper" style="display: flex; gap: 10px; margin-top: 1.5rem;">
					{!! Form::hidden('is_recurring', 0)   !!}
					<label class="switch" for="is_recurring">
						{!! Form::checkbox('is_recurring', 1, old('is_recurring', ($transaction->is_recurring ?? 0)) == 1, ['id' => 'is_recurring']) !!}
						<div class="sliderCheckbox round"></div>
					</label>
                    <p>@lang('lang_v1.subscribe')? @show_tooltip(__('lang_v1.recurring_invoice_help')) <button type="button" data-toggle="modal" data-target="#recurringInvoiceModal" class="btn btn-link"><i class="fa fa-external-link-square-alt"></i></button></p>
				</div>
			</div>
		</div>
	@endif

        </div>

        <!-- Bottom Row: Buttons Only -->
        <div class="pos-bottom-row">
            @if (!$is_mobile)
                <div class="pos-buttons-section">
                    @if (empty($edit))
                        <button type="button" class="pos-btn-tiny pos-btn-secondary"
                            id="pos-cancel">@lang('sale.cancel')</button>
                    @else
                        <button type="button" class="pos-btn-tiny pos-btn-secondary hide"
                            id="pos-delete">@lang('messages.delete')</button>
                    @endif

                    <button type="button" id="pos-quotation"
                        class="pos-btn-tiny pos-btn-secondary">@lang('lang_v1.quotation')</button>

                    <button type="button"
                        class="@if ($pos_settings['disable_draft'] != 0) hide @endif pos-btn-tiny pos-btn-secondary"
                        id="pos-draft">@lang('sale.draft')</button>

                    @if (empty($pos_settings['disable_suspend']))
                        <button type="button" class="pos-btn-tiny pos-btn-secondary pos-express-finalize"
                            data-pay_method="suspend" title="@lang('lang_v1.tooltip_suspend')">@lang('lang_v1.suspend')</button>
                    @endif

                    @if (empty($pos_settings['disable_credit_sale_button']))
                        <input type="hidden" name="is_credit_sale" value="0" id="is_credit_sale">
                        <button type="button" class="pos-btn-tiny pos-btn-primary pos-express-finalize"
                            data-pay_method="credit_sale" title="@lang('lang_v1.tooltip_credit_sale')">@lang('lang_v1.credit_sale')</button>
                    @endif

                    @if ($restaurant_settings['enable_restaurant_module'] == '1' && $restaurant_settings['enable_kot'] == '1')
                        <button type="button" class="pos-btn-tiny pos-btn-secondary pos-express-finalize"
                            data-pay_method="kot" title="@lang('lang_v1.print_kot')">@lang('lang_v1.print_kot')</button>
                    @endif

                    <button type="button"
                        class="pos-btn-tiny pos-btn-primary @if ($pos_settings['disable_pay_checkout'] != 0) hide @endif"
                        id="pos-finalize" title="@lang('lang_v1.tooltip_checkout_multi_pay')">@lang('lang_v1.checkout_multi_pay')</button>

                    <button type="button"
                        class="pos-btn-tiny pos-btn-success @if ($pos_settings['disable_express_checkout'] != 0 || !array_key_exists('cash', $payment_types)) hide @endif pos-express-finalize"
                        data-pay_method="cash" title="@lang('tooltip.express_checkout')">@lang('lang_v1.express_checkout_cash')</button>
                </div>
                <div class="total-payable pos-total-payable">
                    <input type="hidden" name="final_total" id="final_total_input" value=0>
                    <strong>@lang('sale.total_payable'): <span id="total_payable">0</span></strong>

                    @moduleEnabled('CurrencyExchangeRate')
                        <div class="total-payable-target" style="display: none;">
                            <input type="hidden" name="final_total_target" id="final_total_target_input" value="0">
                            <h4>&nbsp @lang('sale.total_payable'): <span id="currency_code"></span> <span id="total_payable">0</span>
                            </h4>
                        </div>
                    @endmoduleEnabled

                </div>
            @else
                @include('sale_pos.partials.pos_buttons_mobile')
            @endif
        </div>

        <!-- Hidden inputs for all charges -->
        
            <input type="hidden" id="total_discount_input" value="">
            <input type="hidden" name="discount_type" id="discount_type"
                value="@if (!isset($transaction)) {{ 'percentage' }} @else {{ $transaction->discount_type }} @endif"
                data-default="percentage">
            <input type="hidden" name="discount_amount" id="discount_amount"
                value="@if (!isset($transaction)) {{ @num_format($business_details->default_sales_discount) }} @else {{ @num_format($transaction->discount_amount) }} @endif"
                data-default="{{ $business_details->default_sales_discount }}">
            <input type="hidden" name="rp_redeemed" id="rp_redeemed"
                value="@if (!isset($transaction)) {{ '0' }} @else {{ $transaction->rp_redeemed }} @endif">
            <input type="hidden" name="rp_redeemed_amount" id="rp_redeemed_amount"
                value="@if (!isset($transaction)) {{ '0' }} @else {{ $transaction->rp_redeemed_amount }} @endif">
            <input type="hidden" name="shipping_details" id="shipping_details"
                value="@if (!isset($transaction)) {{ '' }} @else {{ $transaction->shipping_details }} @endif"
                data-default="">
            <input type="hidden" name="shipping_address" id="shipping_address"
                value="@if (!isset($transaction)) {{ '' }} @else {{ $transaction->shipping_address }} @endif">
            <input type="hidden" name="shipping_status" id="shipping_status"
                value="@if (!isset($transaction)) {{ '' }} @else {{ $transaction->shipping_status }} @endif">
            <input type="hidden" name="delivered_to" id="delivered_to"
                value="@if (!isset($transaction)) {{ '' }} @else {{ $transaction->delivered_to }} @endif">
            <input type="hidden" name="delivery_person" id="delivery_person"
                value="@if (!isset($transaction)) {{ '' }} @else {{ $transaction->delivery_person }} @endif">
            <input type="hidden" name="shipping_charges" id="shipping_charges"
                value="@if (!isset($transaction)) {{ @num_format(0.0) }} @else {{ @num_format($transaction->shipping_charges) }} @endif"
                data-default="0.00">
            <input type="hidden" id="order_tax_input" value="@if (!isset($transaction)) 0 @else {{ @num_format($transaction->tax_amount) }} @endif">
            <input type="hidden" name="tax_rate_id" id="tax_rate_id"
                value="@if (!isset($transaction)) {{ $business_details->default_sales_tax }} @else {{ $transaction->tax_id }} @endif"
                data-default="{{ $business_details->default_sales_tax }}">
            <input type="hidden" name="tax_calculation_amount" id="tax_calculation_amount"
                value="@if (!isset($transaction)) {{ @num_format($business_details->tax_calculation_amount) }} @else {{ @num_format(optional($transaction->tax)->amount) }} @endif"
                data-default="{{ $business_details->tax_calculation_amount }}">
            @if (in_array('types_of_service', $enabled_modules))
                <!-- Hidden mirrors for packing charge (modal is appended to body, so keep form-owned fields) -->
                <input type="hidden" name="packing_charge" id="packing_charge_hidden" value="@if (isset($transaction)) {{ @num_format($transaction->packing_charge ?? 0) }} @else 0 @endif">
                <input type="hidden" name="packing_charge_type" id="packing_charge_type_hidden" value="@if (isset($transaction)) {{ $transaction->packing_charge_type ?? 'fixed' }} @else fixed @endif">
            @endif
            @if (!empty($pos_settings['amount_rounding_method']) && $pos_settings['amount_rounding_method'] > 0)
                <input type="hidden" name="round_off_amount" id="round_off_amount" value="@if (!isset($transaction)) 0 @else {{ @num_format($transaction->round_off_amount ?? 0) }} @endif">
            @endif
        </div>
    </div>

</div>
@if (isset($transaction))
    @include('sale_pos.partials.edit_discount_modal', [
        'sales_discount' => $transaction->discount_amount,
        'discount_type' => $transaction->discount_type,
        'rp_redeemed' => $transaction->rp_redeemed,
        'rp_redeemed_amount' => $transaction->rp_redeemed_amount,
        'max_available' => !empty($redeem_details['points']) ? $redeem_details['points'] : 0,
    ])
@else
    @include('sale_pos.partials.edit_discount_modal', [
        'sales_discount' => $business_details->default_sales_discount,
        'discount_type' => 'percentage',
        'rp_redeemed' => 0,
        'rp_redeemed_amount' => 0,
        'max_available' => 0,
    ])
@endif

@if (isset($transaction))
    @include('sale_pos.partials.edit_order_tax_modal', ['selected_tax' => $transaction->tax_id])
@else
    @include('sale_pos.partials.edit_order_tax_modal', [
        'selected_tax' => $business_details->default_sales_tax,
    ])
@endif

@include('sale_pos.partials.edit_shipping_modal')
