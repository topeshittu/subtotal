@php
    $common_settings = session()->get('business.common_settings');
    $multiplier = 1;
    $image = DB::table('products')->where('id', $product->product_id)->value('image');
    $default_image = asset('/img/default.png');
    $image_url = $default_image;
    if ($image) {
        $image_path = public_path('uploads/img/' . $image);
        if (file_exists($image_path)) {
            $image_url = upload_asset('uploads/img/' . rawurlencode($image));
        }
    }
@endphp


@foreach ($sub_units as $key => $value)
    @if (!empty($product->sub_unit_id) && $product->sub_unit_id == $key)
        @php
            $multiplier = $value['multiplier'];
        @endphp
    @endif
@endforeach

<tr class="product_row" data-row_index="{{ $row_count }}"
    @if (!empty($so_line)) data-so_id="{{ $so_line->transaction_id }}" @endif>
    <input type="hidden" name="row_id" id="row_{{ $row_count }}" value="{{ $row_count }}">
    <input type="hidden" name="quantity_cd [{{ $row_count }}]" id="quantity_cd"
        value="{{ @format_quantity($product->quantity_ordered) }}">

    <td class="product-img"><img src="{{ $image_url }}" alt="product-img" loading="lazy"
            style="height: auto;display: inline;margin-left: 3px; border: black;border-radius: 5px; margin-top: 5px; width: 50px;object-fit: cover;">
    </td>
    <td style="width: 25%">
        @if (!empty($so_line))
            <input type="hidden" name="products[{{ $row_count }}][so_line_id]" value="{{ $so_line->id }}">
        @endif
       @php
			$product_name = e($product->product_name) . '<br/>' . '('. $product->sub_sku .')';
			if(!empty($product->brand)){ $product_name .= ' ' . $product->brand ;}
		@endphp

        <input type="hidden" name="item_name [{{ $row_count }}]" id="item_name" value="{!! $product_name !!}">

        @if (($edit_price || $edit_discount) && empty($is_direct_sell))
            <div title="@lang('lang_v1.pos_edit_product_price_help')">
                <span class="text-link text-info cursor-pointer" data-toggle="modal"
                    data-target="#row_edit_product_price_modal_{{ $row_count }}">
                    {!! $product_name !!}
                    &nbsp;<i class="fa fa-info-circle"></i>
                </span>
            </div>
        @else
            {!! $product_name !!}
        @endif
        <input type="hidden" class="enable_sr_no" value="{{ $product->enable_sr_no }}">
        <input type="hidden" class="product_type" name="products[{{ $row_count }}][product_type]"
            value="{{ $product->product_type }}">

        @php
            $hide_tax = 'hide';
            if (session()->get('business.enable_inline_tax') == 1) {
                $hide_tax = '';
            }

            $tax_id = $product->tax_id;
            $item_tax = !empty($product->item_tax) ? $product->item_tax : 0;
            $unit_price_inc_tax = $product->sell_price_inc_tax;

            if ($hide_tax == 'hide') {
                $tax_id = null;
                $unit_price_inc_tax = $product->default_sell_price;
            }

            if (!empty($so_line) && $action !== 'edit') {
                $tax_id = $so_line->tax_id;
                $item_tax = $so_line->item_tax;
                $unit_price_inc_tax = $so_line->unit_price_inc_tax;
            }

            $discount_type = !empty($product->line_discount_type) ? $product->line_discount_type : 'fixed';
            $discount_amount = !empty($product->line_discount_amount) ? $product->line_discount_amount : 0;

            if (!empty($discount)) {
                $discount_type = $discount->discount_type;
                $discount_amount = $discount->discount_amount;
            }

            if (!empty($so_line) && $action !== 'edit') {
                $discount_type = $so_line->line_discount_type;
                $discount_amount = $so_line->line_discount_amount;
            }

            $sell_line_note = '';
            if (!empty($product->sell_line_note)) {
                $sell_line_note = $product->sell_line_note;
            }
            if (!empty($so_line)) {
                $sell_line_note = $so_line->sell_line_note;
            }
        @endphp
        <input type="hidden" name="discount_cd [{{ $row_count }}]" id="discount_cd"
            value="{{ $discount_amount }}">
        @if (!empty($discount))
            {!! Form::hidden("products[$row_count][discount_id]", $discount->id) !!}
        @endif

        @php
            $warranty_id =
                !empty($action) && $action == 'edit' && !empty($product->warranties->first())
                    ? $product->warranties->first()->id
                    : $product->warranty_id;

            if ($discount_type == 'fixed') {
                $discount_amount = $discount_amount * $multiplier;
            }
        @endphp


        @if (empty($is_direct_sell))
            <div class="modal fade row_edit_product_price_model" id="row_edit_product_price_modal_{{ $row_count }}"
                tabindex="-1" role="dialog">
                @include('sale_pos.partials.row_edit_product_price_modal')
            </div>
        @endif
        @if ($product->enable_stock == 1)

            @if ($product->qty_available > 0)
                <small class="text-muted p-1">{{ number_format($product->qty_available, 2) }} {{ $product->unit }}
                    @lang('lang_v1.in_stock')</small>
            @else
                <small class="text-muted p-1">@lang('lang_v1.item_out_of_stock')</small>
            @endif
        @elseif ($product->manage_stock == 0)
            <small class="text-muted p-1">~</small>
        @endif
        <!-- Description modal end -->
        <div class="modifiers_html">
            @if (in_array('modifiers', $enabled_modules))
                @if (!empty($product->product_ms))
                    @include('restaurant.product_modifier_set.modifier_for_product', [
                        'edit_modifiers' => true,
                        'row_count' => $row_count,
                        'product_ms' => $product->product_ms,
                        'is_edit' => $is_edit ?? false,
                    ])
                @endif
            @endif
        </div>
        <!--  modifier display section -->
        <div class="modifier-display-section">
            <!-- Plain modifiers section -->
            @if (in_array('modifiers', $enabled_modules) && !empty($product->product_ms))
                <div class="plain-modifiers-section">
                    <div class="combo-header" style="margin-top: 8px; cursor: pointer;"
                        onclick="toggle_plain_modifiers({{ $row_count }})">
                        <i class="fas fa-minus combo-toggle-icon" id="plain-icon-{{ $row_count }}"></i>
                        <span class="combo-count">{{ count($product->product_ms) }} Modifier {{ count($product->product_ms) > 1 ? 'Sets' : 'Set' }}</span>
                    </div>
                    <div class="plain_modifiers_content" id="plain-items-{{ $row_count }}">
                        <div class="selected_modifiers">
                        </div>

                        @php
                            $modal_id = 'modifier_' . $row_count . '_' . time();
                        @endphp
                        <div class="modifier-edit-button" style="margin-top: 5px;">
                            <button type="button" class="btn btn-sm btn-default modifier-edit-btn" data-row-count="{{ $row_count }}" title="Edit Modifiers">
                                <i class="fas fa-edit"></i> Edit Modifiers (<span class="selected-modifier-count-{{ $row_count }}">0</span>)
                            </button>
                        </div>
                    </div>
                </div>
            @else
                <div class="selected_modifiers">
                </div>
            @endif

            <!-- Combo products section -->
            @if (isset($combo_products) && count($combo_products) > 0)
                <div class="combo-items-section">
                    <div class="combo-header" style="margin-top: 8px; cursor: pointer;"
                        onclick="toggle_combo_items({{ $row_count }})">
                        <i class="fas fa-minus combo-toggle-icon" id="combo-icon-{{ $row_count }}"></i>
                        <span class="combo-count">{{ count($combo_products) }} Combo Items</span>
                    </div>
                    <div class="combo_modifiers" id="combo-items-{{ $row_count }}">
                        @foreach ($combo_products as $index => $combo_product)
                            @php
                                $combo_qty = is_array($combo_product) ? $combo_product['quantity'] : $combo_product->quantity;
                                $combo_product_name = is_array($combo_product) ? $combo_product['product_name'] ?? 'Unknown Product' : $combo_product->product_name;
                                $combo_modifier_ids = is_array($combo_product) ? $combo_product['combo_modifier_ids'] ?? '' : $combo_product->combo_modifier_ids ?? '';
                                $combo_variation_id = is_array($combo_product) ? $combo_product['variation_id'] : $combo_product->variation_id;
                                $combo_product_id = is_array($combo_product) ? $combo_product['product_id'] : $combo_product->product_id;
                            @endphp
                            @for ($i = 0; $i < $combo_qty; $i++)
                                @php
                                    $instance_index = $row_count . '_' . $index . '_' . $i;
                                    $modal_id = 'modifier_' . $instance_index . '_' . $combo_variation_id;
                                    $qty_total = 0;
                                    $combo_modifier_sets = is_array($combo_product) ? (isset($combo_product['modifier_sets']) ? $combo_product['modifier_sets'] : []) : (isset($combo_product->modifier_sets) ? $combo_product->modifier_sets : []);
                                    $has_modifiers = !empty($combo_modifier_sets) && ( is_array($combo_modifier_sets) ? count($combo_modifier_sets) > 0 : $combo_modifier_sets->count() > 0 );
                                @endphp
                                @if ($has_modifiers)
                                    <div class="combo-product-item clickable-combo-product" style="cursor: pointer;"
                                        data-modal-id="{{ $modal_id }}" title="Click to edit modifiers">
                                        <i class="fas fa-cog" style="color: #2196f3; margin-right: 5px;"></i>
                                        <strong style="color: #2196f3;">{{ $combo_product_name }}</strong>
                                    </div>

                                    @if (!empty($is_edit))
                                        @php
                                            if (!isset($GLOBALS['combo_modals_to_render'])) {
                                                $GLOBALS['combo_modals_to_render'] = [];
                                            }
                                            $GLOBALS['combo_modals_to_render'][] = [
                                                'modal_id' => $modal_id,
                                                'modifier_sets' => $combo_modifier_sets,
                                                'row_count' => $row_count,
                                                'index' => $index,
                                                'instance_index' => $instance_index,
                                                'variation_id' => $combo_variation_id,
                                                'product_name' => $combo_product_name,
                                                'combo_modifier_ids' => $combo_modifier_ids,
                                            ];
                                        @endphp
                                    @else
                                        <div id="modal-container-{{ $modal_id }}" style="display: none;">
                                            @include('restaurant.product_modifier_set.modifier_for_combo', [
                                                'modal_id' => $modal_id,
                                                'modifier_sets' => $combo_modifier_sets,
                                                'row_count' => $row_count,
                                                'index' => $index,
                                                'instance_index' => $instance_index,
                                                'variation_id' => $combo_variation_id,
                                                'product_name' => $combo_product_name,
                                                'combo_modifier_ids' => $combo_modifier_ids,
                                            ])
                                        </div>
                                        <script>
                                            (function() {
                                                var modal_container = document.getElementById('modal-container-{{ $modal_id }}');
                                                if (modal_container) {
                                                    var modal_element = modal_container.querySelector('.modal');
                                                    if (modal_element) {
                                                        document.body.appendChild(modal_element);
                                                    }
                                                    modal_container.remove();
                                                }
                                            })();
                                        </script>
                                    @endif
                                @else
                                    <div class="combo-product-item" style="margin: 2px 0;">
                                        <span>{{ $combo_product_name }}</span>
                                    </div>
                                @endif

                                <input type="hidden" name="products[{{ $row_count }}][combo][{{ $index }}][product_id]" value="{{ $combo_product_id }}">
                                <input type="hidden" name="products[{{ $row_count }}][combo][{{ $index }}][variation_id]" value="{{ $combo_variation_id }}">
                                <input type="hidden" class="combo_product_qty"name="products[{{ $row_count }}][combo][{{ $index }}][quantity]" data-unit_quantity="{{ $combo_qty }}" value="{{ $qty_total }}">

                                {{-- modifier display for combo products --}}
                                <div class="combo_modifier_prices_container" data-instance="{{ $instance_index }}">
                                    @if (!empty($combo_modifier_ids))
                                        @php
                                            $modifier_ids_array = explode(',', $combo_modifier_ids);
                                            $existing_modifiers = \App\Models\Variation::whereIn('id', $modifier_ids_array)->get();
                                            $modifiers_by_set = [];

                                            foreach ($existing_modifiers as $modifier) {
                                                $set_name = 'Modifiers';

                                                if (!empty($combo_modifier_sets)) {
                                                    foreach ($combo_modifier_sets as $set) {
                                                        $set_variations = is_array($set) ? $set['variations'] : $set->variations;
                                                        foreach ($set_variations as $var) {
                                                            $var_id = is_array($var) ? $var['id'] : $var->id;
                                                            if ($var_id == $modifier->id) {
                                                                $set_name = is_array($set) ? $set['name'] : $set->name;
                                                                break 2;
                                                            }
                                                        }
                                                    }
                                                }

                                                if (!isset($modifiers_by_set[$set_name])) {
                                                    $modifiers_by_set[$set_name] = [];
                                                }
                                                $modifiers_by_set[$set_name][] = $modifier;
                                            }
                                        @endphp
                                        @foreach ($modifiers_by_set as $set_name => $modifiers)
                                            <div class="modifier-set-group" >
                                                <div style=" color: #333;">{{ $set_name }}:</div>
                                                @foreach ($modifiers as $modifier)
                                                    <div class="product_modifier">
                                                        <div class="combo-modifier-item" style="margin: 2px 0; margin-left: 10px;">
                                                            <i class="fas fa-check-circle" style="color: #4caf50; margin-right: 5px;"></i>
                                                            <span style="color: #4caf50; font-weight: 500;">{{ $modifier->name }}</span>
                                                            <br>
                                                            <span style="color: #666; font-size: 0.9em;">({{ number_format($modifier->sell_price_inc_tax, 2) }} x <span class="modifier_qty_text">{{ @format_quantity($product->quantity_ordered) }}</span>)</span>
                                                        </div>
                                                        <input type="hidden" class="modifiers_price" name="products[{{ $row_count }}][combo_modifier_price][]" value="{{ $modifier->sell_price_inc_tax }}">
                                                        <input type="hidden" class="modifiers_quantity" name="products[{{ $row_count }}][combo_modifier_quantity][]" value="{{ $product->quantity_ordered }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                                <input type="hidden" name="products[{{ $row_count }}][combo][{{ $index }}][modifier_ids]" class="combo_modifier_ids_{{ $row_count }}_{{ $index }}_{{ $i }}" value="{{ $combo_modifier_ids }}">
                                <input type="hidden" name="products[{{ $row_count }}][combo][{{ $index }}][single_modifier_ids]" class="single_combo_modifier_ids_{{ $row_count }}_{{ $index }}_{{ $i }}" value="{{ isset($action) && $action == 'edit' ? $combo_modifier_ids : '' }}">
                                <input type="hidden" class="instance_index" value="{{ $instance_index }}">
                                @php
                                    $modifier_sets = is_array($combo_product) ? (isset($combo_product['modifier_sets']) ? $combo_product['modifier_sets'] : []) : (isset($combo_product->modifier_sets) ? $combo_product->modifier_sets : []);
                                @endphp
                                @if (!empty($modifier_sets) && (is_array($modifier_sets) ? count($modifier_sets) > 0 : !$modifier_sets->isEmpty()))
                                    @php
                                        if (!isset($combo_modals)) {
                                            $combo_modals = [];
                                        }

                                        $modal_product = (object) [
                                            'product_name' => $combo_product_name,
                                            'variation_id' => $combo_variation_id,
                                            'product_id' => $combo_product_id,
                                            'combo_modifier_ids' => $combo_modifier_ids,
                                            'modifier_sets' => is_array($modifier_sets) ? array_map(function ($set) { return (object) $set; }, $modifier_sets) : $modifier_sets,
                                            'modifiers' => is_array($combo_product) && isset($combo_product['modifiers']) ? $combo_product['modifiers'] : [],
                                        ];
                                        $combo_modals[] = [
                                            'edit_modifiers' => true,
                                            'product' => $modal_product,
                                            'modal_id' => $modal_id,
                                            'row_count' => $row_count,
                                            'instance_index' => $instance_index,
                                            'index' => $index,
                                        ];
                                    @endphp
                                @endif
                            @endfor
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        @php
            $max_quantity = $product->qty_available;
            $formatted_max_quantity = $product->formatted_qty_available;

            if (!empty($action) && $action == 'edit') {
                if (!empty($so_line)) {
                    $qty_available = $so_line->quantity - $so_line->so_quantity_invoiced + $product->quantity_ordered;
                    $max_quantity = $qty_available;
                    $formatted_max_quantity = number_format(
                        $qty_available,
                        config('constants.currency_precision', 2),
                        session('currency')['decimal_separator'],
                        session('currency')['thousand_separator'],
                    );
                }
            } else {
                if (!empty($so_line) && $so_line->qty_available <= $max_quantity) {
                    $max_quantity = $so_line->qty_available;
                    $formatted_max_quantity = $so_line->formatted_qty_available;
                }
            }

            $max_qty_rule = $max_quantity;
            $max_qty_msg = __('validation.custom-messages.quantity_not_available', [
                'qty' => $formatted_max_quantity,
                'unit' => $product->unit,
            ]);
        @endphp

        @if (session()->get('business.enable_lot_number') == 1 || session()->get('business.enable_product_expiry') == 1)
            @php
                $lot_enabled = session()->get('business.enable_lot_number');
                $exp_enabled = session()->get('business.enable_product_expiry');
                $lot_no_line_id = '';
                if (!empty($product->lot_no_line_id)) {
                    $lot_no_line_id = $product->lot_no_line_id;
                }
            @endphp
            @if (!empty($product->lot_numbers) && empty($is_sales_order))
                <select class="form-control lot_number input-sm" name="products[{{ $row_count }}][lot_no_line_id]"
                    @if (!empty($product->transaction_sell_lines_id)) disabled @endif>
                    <option value="">@lang('lang_v1.lot_n_expiry')</option>
                    @foreach ($product->lot_numbers as $lot_number)
                        @php
                            $selected = '';
                            if ($lot_number->purchase_line_id == $lot_no_line_id) {
                                $selected = 'selected';

                                $max_qty_rule = $lot_number->qty_available;
                                $max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', [
                                    'qty' => $lot_number->qty_formated,
                                    'unit' => $product->unit,
                                ]);
                            }

                            $expiry_text = '';
                            if ($exp_enabled == 1 && !empty($lot_number->exp_date)) {
                                if (
                                    Carbon\Carbon::now()->gt(
                                        Carbon\Carbon::createFromFormat('Y-m-d', $lot_number->exp_date),
                                    )
                                ) {
                                    $expiry_text = '(' . __('report.expired') . ')';
                                }
                            }

                            //preselected lot number if product searched by lot number
                            if (!empty($purchase_line_id) && $purchase_line_id == $lot_number->purchase_line_id) {
                                $selected = 'selected';

                                $max_qty_rule = $lot_number->qty_available;
                                $max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', [
                                    'qty' => $lot_number->qty_formated,
                                    'unit' => $product->unit,
                                ]);
                            }
                        @endphp
                        <option value="{{ $lot_number->purchase_line_id }}"
                            data-qty_available="{{ $lot_number->qty_available }}" data-msg-max="@lang('lang_v1.quantity_error_msg_in_lot', ['qty' => $lot_number->qty_formated, 'unit' => $product->unit])"
                            {{ $selected }}>
                            @if (!empty($lot_number->lot_number) && $lot_enabled == 1)
                                {{ $lot_number->lot_number }}
                                @endif @if ($lot_enabled == 1 && $exp_enabled == 1)
                                    -
                                    @endif @if ($exp_enabled == 1 && !empty($lot_number->exp_date))
                                        @lang('product.exp_date'): {{ @format_date($lot_number->exp_date) }}
                                    @endif {{ $expiry_text }}
                        </option>
                    @endforeach
                </select>
            @endif
        @endif
        @if (!empty($is_direct_sell))
            <br>
            <textarea class="form-control" name="products[{{ $row_count }}][sell_line_note]" rows="2">{{ $sell_line_note }}</textarea>
            <p class="help-block"><small>@lang('lang_v1.sell_line_description_help')</small></p>
        @endif
    </td>

    <td class="unit-quantity-flex" style="max-width:19%">
        {{-- If edit then transaction sell lines will be present --}}
        @if (!empty($product->transaction_sell_lines_id))
            <input type="hidden" name="products[{{ $row_count }}][transaction_sell_lines_id]"
                class="form-control" value="{{ $product->transaction_sell_lines_id }}">
        @endif

        <input type="hidden" name="products[{{ $row_count }}][product_id]" class="form-control product_id"
            value="{{ $product->product_id }}">

        <input type="hidden" value="{{ $product->variation_id }}"
            name="products[{{ $row_count }}][variation_id]" class="row_variation_id">

        <input type="hidden" name="products[{{ $row_count }}][product_type]"
            value="{{ $product->product_type }}">

        <input type="hidden" value="{{ $product->enable_stock }}"
            name="products[{{ $row_count }}][enable_stock]">

        @if (empty($product->quantity_ordered))
            @php
                $product->quantity_ordered = 1;
            @endphp
        @endif

        @php
            $allow_decimal = true;
            if ($product->unit_allow_decimal != 1) {
                $allow_decimal = false;
            }
        @endphp
        @foreach ($sub_units as $key => $value)
            @if (!empty($product->sub_unit_id) && $product->sub_unit_id == $key)
                @php
                    $max_qty_rule = $max_qty_rule / $multiplier;
                    $unit_name = $value['name'];
                    $max_qty_msg = __('validation.custom-messages.quantity_not_available', [
                        'qty' => $max_qty_rule,
                        'unit' => $unit_name,
                    ]);

                    if (!empty($product->lot_no_line_id)) {
                        $max_qty_msg = __('lang_v1.quantity_error_msg_in_lot', [
                            'qty' => $max_qty_rule,
                            'unit' => $unit_name,
                        ]);
                    }

                    if ($value['allow_decimal']) {
                        $allow_decimal = true;
                    }
                @endphp
            @endif
        @endforeach
        <div class="input-group input-number">
            <span class="input-group-btn"><button type="button" class="btn btn-default btn-flat quantity-down"
                    style="height: 30px; border:none"><i class="fa fa-minus text-danger"></i></button></span>
            <input type="text" data-min="1"
                class="form-control pos_quantity input_number mousetrap input_quantity width-100"
                style="height: 30px; border:none;text-align: center;"
                value="{{ @format_quantity($product->quantity_ordered) }}"
                name="products[{{ $row_count }}][quantity]"
                data-allow-overselling="@if (empty($pos_settings['allow_overselling'])) {{ 'false' }}@else{{ 'true' }} @endif"
                @if ($allow_decimal) data-decimal=1
				@else
				data-decimal=0
				data-rule-abs_digit="true"
				data-msg-abs_digit="@lang('lang_v1.decimal_value_not_allowed')" @endif
                data-rule-required="true" data-msg-required="@lang('validation.custom-messages.this_field_is_required')"
                @if ($product->enable_stock && empty($pos_settings['allow_overselling']) && empty($is_sales_order)) data-rule-max-value="{{ $max_qty_rule }}" data-qty_available="{{ $product->qty_available }}" data-msg-max-value="{{ $max_qty_msg }}"
			data-msg_max_default="@lang('validation.custom-messages.quantity_not_available', ['qty'=> $product->formatted_qty_available, 'unit' => $product->unit ])" @endif>
            <span class="input-group-btn"><button type="button" class="btn btn-default btn-flat quantity-up"
                    style="height: 30px; border:none"><i class="fa fa-plus text-success"></i></button></span>
            @if (count($sub_units) > 0)
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-flat dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false"
                        style="height: 30px; border:none; padding: 0 6px; min-width: 20px;">
                        <i class="fa fa-caret-down"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        @foreach ($sub_units as $key => $value)
                            <li><a href="#"
                                    class="sub-unit-option @if (!empty($product->sub_unit_id) && $product->sub_unit_id == $key) selected @endif"
                                    data-value="{{ $key }}" data-multiplier="{{ $value['multiplier'] }}"
                                    data-unit_name="{{ $value['name'] }}"
                                    data-allow_decimal="{{ $value['allow_decimal'] }}">{{ $value['name'] }}</a></li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <input type="hidden" name="products[{{ $row_count }}][product_unit_id]"
            value="{{ $product->unit_id }}">
        @if (count($sub_units) > 0)
            <select name="products[{{ $row_count }}][sub_unit_id]" class="form-control input-sm sub_unit"
                style="display: none;">
                @foreach ($sub_units as $key => $value)
                    <option value="{{ $key }}" data-multiplier="{{ $value['multiplier'] }}"
                        data-unit_name="{{ $value['name'] }}" data-allow_decimal="{{ $value['allow_decimal'] }}"
                        @if (!empty($product->sub_unit_id) && $product->sub_unit_id == $key) selected @endif>
                        {{ $value['name'] }}
                    </option>
                @endforeach
            </select>
        @else
            <span class="unit-text"
                style="font-size: 12px; color: #666; margin-left: 5px;">{{ $product->unit }}</span>
        @endif

        @if (!empty($product->second_unit))
            <br>
            <span style="white-space: nowrap;">
                @lang('lang_v1.quantity_in_second_unit', ['unit' => $product->second_unit])*:</span><br>
            <input type="text" name="products[{{ $row_count }}][secondary_unit_quantity]"
                value="{{ @format_quantity($product->secondary_unit_quantity) }}"
                class="form-control input-sm input_number" required>
        @endif

        <input type="hidden" class="base_unit_multiplier"
            name="products[{{ $row_count }}][base_unit_multiplier]" value="{{ $multiplier }}">

        <input type="hidden" class="hidden_base_unit_sell_price"
            value="{{ $product->default_sell_price / $multiplier }}">

        {{-- Hidden fields for combo products --}}
        @if ($product->product_type == 'combo' && !empty($product->combo_products))

            @foreach ($product->combo_products as $k => $combo_product)
                @if (isset($action) && $action == 'edit')
                    @php
                        $combo_product['qty_required'] = $combo_product['quantity'] / $product->quantity_ordered;

                        $qty_total = $combo_product['quantity'];
                    @endphp
                @else
                    @php
                        $qty_total = $combo_product['qty_required'];
                    @endphp
                @endif

                <input type="hidden" name="products[{{ $row_count }}][combo][{{ $k }}][product_id]"
                    value="{{ $combo_product['product_id'] }}">

                <input type="hidden"
                    name="products[{{ $row_count }}][combo][{{ $k }}][variation_id]"
                    value="{{ $combo_product['variation_id'] }}">

                <input type="hidden" class="combo_product_qty"
                    name="products[{{ $row_count }}][combo][{{ $k }}][quantity]"
                    data-unit_quantity="{{ $combo_product['qty_required'] }}" value="{{ $qty_total }}">

                @if (isset($action) && $action == 'edit')
                    <input type="hidden"
                        name="products[{{ $row_count }}][combo][{{ $k }}][transaction_sell_lines_id]"
                        value="{{ $combo_product['id'] }}">
                @endif
            @endforeach
        @endif
    </td>
    @if (!empty($is_direct_sell))
        @if (!empty($pos_settings['inline_service_staff']))
            <td>
                <div class="form-group">
                    <div class="input-group">
                        {!! Form::select(
                            'products[' . $row_count . '][res_service_staff_id]',
                            $waiters,
                            !empty($product->res_service_staff_id) ? $product->res_service_staff_id : null,
                            [
                                'class' => 'form-control input-sm select2 order_line_service_staff',
                                'placeholder' => __('restaurant.select_service_staff'),
                                'required' =>
                                    !empty($pos_settings['is_service_staff_required']) && $pos_settings['is_service_staff_required'] == 1
                                        ? true
                                        : false,
                            ],
                        ) !!}
                    </div>
                </div>
            </td>
        @endif
        @php
            $pos_unit_price = !empty($product->unit_price_before_discount)
                ? $product->unit_price_before_discount
                : $product->default_sell_price;

            if (!empty($so_line)) {
                $pos_unit_price = $so_line->unit_price_before_discount;
            }
        @endphp

        <td class="@if (!auth()->user()->can('edit_product_price_from_sale_screen')) hide @endif">
            <input type="text" name="products[{{ $row_count }}][unit_price]"
                class="form-control input-sm pos_unit_price input_number mousetrap"
                value="{{ @num_format($pos_unit_price) }}"
                @if (!empty($pos_settings['enable_msp'])) data-rule-min-value="{{ $pos_unit_price }}" data-msg-min-value="{{ __('lang_v1.minimum_selling_price_error_msg', ['price' => @num_format($pos_unit_price)]) }}" @endif>
            @if (!empty($last_sell_line))
                <br>
                <small class="text-muted">@lang('lang_v1.prev_unit_price'): @format_currency($last_sell_line->unit_price_before_discount)</small>
            @endif
            @if (!empty($last_sell_line))
                <br>
                <small class="text-muted">
                    @lang('lang_v1.prev_discount'):
                    @if ($last_sell_line->line_discount_type == 'percentage')
                        {{ @num_format($last_sell_line->line_discount_amount) }}%
                    @else
                        @format_currency($last_sell_line->line_discount_amount)
                    @endif
                </small>
            @endif
        </td>

        <td @if (!$edit_discount) class="hide" @endif>
            {!! Form::text("products[$row_count][line_discount_amount]", @num_format($discount_amount), [
                'class' => 'form-control input-sm input_number row_discount_amount',
            ]) !!}<br>
            {!! Form::select(
                "products[$row_count][line_discount_type]",
                ['fixed' => __('lang_v1.fixed'), 'percentage' => __('lang_v1.percentage')],
                $discount_type,
                ['class' => 'form-control input-sm row_discount_type'],
            ) !!}
            @if (!empty($discount))
                <p class="help-block">{!! __('lang_v1.applied_discount_text', [
                    'discount_name' => $discount->name,
                    'starts_at' => $discount->formated_starts_at,
                    'ends_at' => $discount->formated_ends_at,
                ]) !!}</p>
            @endif
        </td>

        <td class="text-center {{ $hide_tax }}">
            {!! Form::hidden("products[$row_count][item_tax]", @num_format($item_tax), ['class' => 'item_tax']) !!}
            {!! Form::select(
                "products[$row_count][tax_id]",
                $tax_dropdown['tax_rates'],
                $tax_id,
                ['placeholder' => 'Select', 'class' => 'form-control input-sm tax_id width-100'],
                $tax_dropdown['attributes'],
            ) !!}
        </td>
    @else
        @if (!empty($pos_settings['inline_service_staff']))
            <td>
                <div class="form-group">
                    <div class="input-group">
                        {!! Form::select(
                            'products[' . $row_count . '][res_service_staff_id]',
                            $waiters,
                            !empty($product->res_service_staff_id) ? $product->res_service_staff_id : null,
                            [
                                'class' => 'form-control input-sm select2 order_line_service_staff',
                                'placeholder' => __('restaurant.select_service_staff'),
                                'required' =>
                                    !empty($pos_settings['is_service_staff_required']) && $pos_settings['is_service_staff_required'] == 1
                                        ? true
                                        : false,
                            ],
                        ) !!}
                    </div>
                </div>
            </td>
        @endif
    @endif

    <td class="{{ $hide_tax }}">

        <input type="hidden" name="item_price [{{ $row_count }}]" id="item_price"
            value="{{ $unit_price_inc_tax }}">
        <input type="text" name="products[{{ $row_count }}][unit_price_inc_tax]"
            style="height: 30px; line-height: 30px;" class="form-control input-sm pos_unit_price_inc_tax input_number"
            value="{{ @num_format($unit_price_inc_tax) }}" @if (!$edit_price) readonly @endif
            @if (!empty($pos_settings['enable_msp'])) data-rule-min-value="{{ $unit_price_inc_tax }}" data-msg-min-value="{{ __('lang_v1.minimum_selling_price_error_msg', ['price' => @num_format($unit_price_inc_tax)]) }}" @endif>
    </td>

    @if (!empty($common_settings['enable_product_warranty']) && !empty($is_direct_sell))
        <td>
            {!! Form::select("products[$row_count][warranty_id]", $warranties, $warranty_id, [
                'placeholder' => __('messages.please_select'),
                'class' => 'form-control input-sm',
            ]) !!}
        </td>
    @endif

    <td class="text-center">
        @php
            $subtotal_type = !empty($pos_settings['is_pos_subtotal_editable']) ? 'text' : 'hidden';

        @endphp
        <input type="{{ $subtotal_type }}" style="height: 30px; line-height: 30px;"
            class="form-control input-sm pos_line_total @if (!empty($pos_settings['is_pos_subtotal_editable'])) input_number @endif"
            value="{{ @num_format($product->quantity_ordered * $unit_price_inc_tax) }}">
        <span class="display_currency pos_line_total_text @if (!empty($pos_settings['is_pos_subtotal_editable'])) hide @endif"
            data-currency_symbol="true">{{ $product->quantity_ordered * $unit_price_inc_tax }}</span>
    </td>
    <td class="text-center v-center">

        <i class="fa fa-times text-danger pos_remove_row cursor-pointer" aria-hidden="true"></i>
    </td>
</tr>

@if (isset($GLOBALS['combo_modals_to_render']) && count($GLOBALS['combo_modals_to_render']) > 0)
    @foreach ($GLOBALS['combo_modals_to_render'] as $modal_data)
        @include('restaurant.product_modifier_set.modifier_for_combo', $modal_data)
    @endforeach
    @php
        // Clear the array for next row
        $GLOBALS['combo_modals_to_render'] = [];
    @endphp
@endif

@if (isset($combo_products) && count($combo_products) > 0)
    <script>
        if (typeof window.combo_modifier_prices === 'undefined') {
            window.combo_modifier_prices = {};
        }
        @foreach ($combo_products as $index => $combo_product)
            @php
                $modifier_sets = is_array($combo_product) ? (isset($combo_product['modifier_sets']) ? $combo_product['modifier_sets'] : []) : (isset($combo_product->modifier_sets) ? $combo_product->modifier_sets : []);
            @endphp
            @if (!empty($modifier_sets) && (is_array($modifier_sets) ? count($modifier_sets) > 0 : !$modifier_sets->isEmpty()))
                @foreach ($modifier_sets as $modifier_set)
                    @php
                        $set_variations = is_array($modifier_set) ? $modifier_set['variations'] : $modifier_set->variations;
                    @endphp
                    @foreach ($set_variations as $modifier)
                        @php
                            $mod_id = is_array($modifier) ? $modifier['id'] : $modifier->id;
                            $mod_price = is_array($modifier) ? $modifier['sell_price_inc_tax'] ?? 0 : $modifier->sell_price_inc_tax ?? 0;
                        @endphp
                        window.combo_modifier_prices[{{ $mod_id }}] = {{ $mod_price }};
                    @endforeach
                @endforeach
            @endif
        @endforeach
    </script>
@endif

@include('restaurant.product_modifier_set.edit_product_modifiers_javascript')
