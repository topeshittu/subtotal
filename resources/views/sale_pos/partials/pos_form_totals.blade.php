@php
$is_mobile = isMobile();
@endphp
<div class="pos_form_totals_compact">
	<div class="totals-inline">
		<div class="total-item">
			<b>@lang('sale.item'):</b> <span class="total_quantity">0</span>
		</div>
		<div class="total-item">
			<b>@lang('sale.total'):</b> <span class="price_total">0</span>
		</div>
		@if($is_mobile)
		@if (!empty($pos_settings['amount_rounding_method']) && $pos_settings['amount_rounding_method'] > 0)
		<div class="total-item">
			<b><span id="round_off">@lang('lang_v1.round_off'):</span></b> <span id="round_off_text">0</span>
			<input type="hidden" name="round_off_amount" id="round_off_amount" value=0>
		</div>
		@endif
		@endif
	</div>
</div>