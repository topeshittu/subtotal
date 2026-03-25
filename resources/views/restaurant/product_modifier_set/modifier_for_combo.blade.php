@php
    if (!isset($variation_id)) { $variation_id = isset($product) ? (is_array($product) ? ($product['variation_id'] ?? '') : ($product->variation_id ?? '')) : ''; }
    if (!isset($product_name)) { $product_name = isset($product) ? (is_array($product) ? ($product['product_name'] ?? 'Product') : ($product->product_name ?? 'Product')) : 'Product'; }
    if (!isset($combo_modifier_ids)) { $combo_modifier_ids = isset($product) ? (is_array($product) ? ($product['combo_modifier_ids'] ?? '') : ($product->combo_modifier_ids ?? '')) : '';}
    if (!isset($modifier_sets)) { $modifier_sets = isset($product) ? (is_array($product) ? ($product['modifier_sets'] ?? []) : ($product->modifier_sets ?? [])) : []; }
    $id = isset($modal_id) ? $modal_id : 'modifier_' . $instance_index . '_' . $variation_id;
@endphp

<div>
    <span class="selected_modifiers" id="selected_modifiers_{{ $id }}" style="display: none;"></span>
    <i class="fa fa-external-link-alt cursor-pointer text-primary select-modifiers-btn" title="@lang('restaurant.modifiers_for_product')" data-toggle="modal" data-target="#{{ $id }}" style="display: none;"></i>
</div>

<div class="modal fade modifier_modal" id="{{ $id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('restaurant.modifiers_for_product'): <span class="text-success">{{ $product_name }}</span></h4>
            </div>

            <div class="modal-body">
                @if (!empty($modifier_sets))
                    <div class="panel-group" id="accordion{{ $id }}" role="tablist" aria-multiselectable="true">
                        @foreach ($modifier_sets as $modifier_set)
                            @php
                                $set_id = is_array($modifier_set) ? $modifier_set['id'] : $modifier_set->id;
                                $set_name = is_array($modifier_set) ? $modifier_set['name'] : $modifier_set->name;
                                $set_variations = is_array($modifier_set)? $modifier_set['variations'] : $modifier_set->variations;
                                $collapse_id = 'collapse' . $set_id . $id;
                            @endphp
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingOne">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse"
                                            data-parent="#accordion{{ $id }}" href="#{{ $collapse_id }}"
                                            aria-expanded="true" aria-controls="collapseOne">
                                            {{ $set_name }}
                                        </a>
                                    </h4>
                                </div>
                                <input type="hidden" class="modifiers_exist" value="true">
                                <input type="hidden" class="index" value="{{ $row_count }}">
                                <input type="hidden" class="combo_index" value="{{ $index }}">
                                <input type="hidden" class="instance_index" value="{{ $instance_index }}">

                                <div id="{{ $collapse_id }}"
                                    class="panel-collapse collapse @if ($loop->index == 0) in @endif"
                                    role="tabpanel" aria-labelledby="headingOne">
                                    <div class="panel-body">
                                        <div class="btn-group">
                                            @foreach ($set_variations as $modifier)
                                                @php
                                                    $mod_id = is_array($modifier) ? $modifier['id'] : $modifier->id;
                                                    $mod_name = is_array($modifier) ? $modifier['name'] : $modifier->name;
                                                    $mod_price = is_array($modifier) ? $modifier['sell_price_inc_tax'] ?? 0 : $modifier->sell_price_inc_tax ?? 0;
                                                    $mod_is_selected = is_array($modifier) ? $modifier['is_selected'] ?? false : $modifier->is_selected ?? false;
                                                @endphp
                                                <label
                                                    class="btn btn-outline-primary modifier-btn @if ($mod_is_selected) active btn-success @endif">
                                                    <input type="checkbox" autocomplete="off" value="{{ $mod_id }}" data-combo-id="{{ $variation_id }}" data-price="{{ $mod_price }}" data-name="{{ $mod_name }}" data-set-name="{{ $set_name }}" @if ($mod_is_selected) checked @endif>
                                                    {{ $mod_name }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>@lang('lang_v1.modifiers_not_available_product')</p>
                @endif
            </div>
            <div class="modal-footer">
                <button data-url="{{ action('Restaurant\ProductModifierSetController@add_selected_modifiers') }}"
                    type="button" class="btn btn-primary add_modifier" data-dismiss="modal">
                    @lang('messages.add')
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>
        </div>
    </div>
</div>
@if (empty($edit_modifiers))
    <script type="text/javascript">
        if (typeof $ !== 'undefined') {
            $(document).ready(function() {
                $('div#{{ $id }}').modal('show');
            });
        }
    </script>
@endif

<script>
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize_combo_modifiers);
    } else {
        initialize_combo_modifiers();
    }

    function initialize_combo_modifiers() {
        function initialize_modal_checkboxes() {
            var modal = document.querySelector('.modal#{{ $id }}');
            if (!modal) {
                return;
            }
            var instance_index_input = modal.querySelector('.instance_index');
            var instance_index = instance_index_input ? instance_index_input.value : null;

            if (instance_index) {
                var combo_modifier_field = document.querySelector('input.combo_modifier_ids_' + instance_index);

                if (combo_modifier_field && combo_modifier_field.value) {
                    var existing_modifier_ids = combo_modifier_field.value.split(',');

                    var checkboxes = modal.querySelectorAll('input[type="checkbox"]');
                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = false;
                    });

                    var labels = modal.querySelectorAll('label.modifier-btn');
                    labels.forEach(function(label) {
                        label.classList.remove('active', 'btn-success');
                        label.classList.add('btn-outline-primary');
                    });

                    existing_modifier_ids.forEach(function(modifier_id) {
                        modifier_id = modifier_id.trim();
                        var checkbox = modal.querySelector('input[type="checkbox"][value="' + modifier_id +
                            '"]');
                        if (checkbox) {
                            checkbox.checked = true;
                            var label = checkbox.closest('label');
                            if (label) {
                                label.classList.remove('btn-outline-primary');
                                label.classList.add('active', 'btn-success');
                            }
                        }
                    });
                }
            }
        }

        var modal = document.querySelector('.modal#{{ $id }}');
        if (modal) {
            modal.addEventListener('shown.bs.modal', function() {
                initialize_modal_checkboxes();
            });

            modal.addEventListener('click', function(e) {
                if (e.target.closest('.modifier-btn')) {
                    e.preventDefault();
                    e.stopPropagation();

                    var label = e.target.closest('.modifier-btn');
                    var checkbox = label.querySelector('input[type="checkbox"]');

                    if (checkbox) {
                        checkbox.checked = !checkbox.checked;
                        if (checkbox.checked) {
                            label.classList.remove('btn-outline-primary');
                            label.classList.add('active', 'btn-success');
                        } else {
                            label.classList.remove('active', 'btn-success');
                            label.classList.add('btn-outline-primary');
                        }
                    }
                }
            });

            var instance_index_input = modal ? modal.querySelector('.instance_index') : null;
            var own_instance_index = instance_index_input ? instance_index_input.value : null;
            if (own_instance_index) {
                var init_function_name =
                    'update_selected_modifiers_display_{{ preg_replace('/[^a-zA-Z0-9]/', '', $id) }}';
                if (typeof window[init_function_name] === 'function') {
                    window[init_function_name](own_instance_index, []);
                }
            }

            var add_modifier_buttons = document.querySelectorAll('.add_modifier');
            add_modifier_buttons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var modal = this.closest('.modal');
                    if (!modal || modal.id !== '{{ $id }}') return;

                    var row_count_input = modal.querySelector('.index');
                    var combo_index_input = modal.querySelector('.combo_index');
                    var instance_index_input = modal.querySelector('.instance_index');

                    var row_count = row_count_input ? row_count_input.value : null;
                    var combo_index = combo_index_input ? combo_index_input.value : null;
                    var instance_index = instance_index_input ? instance_index_input.value : null;

                    var selected_modifiers = [];
                    var checked_boxes = modal.querySelectorAll('input[type="checkbox"]:checked');

                    // Collect selected modifier IDs
                    checked_boxes.forEach(function(checkbox) {
                        selected_modifiers.push(checkbox.value);
                    });

                    var single_modifier_field = document.querySelector(
                        'input.single_combo_modifier_ids_' + instance_index);

                    if (single_modifier_field) {
                        single_modifier_field.value = selected_modifiers.join(',');
                    }

                    combine_single_modifiers(row_count, combo_index);

                    var update_function_name =
                        'update_selected_modifiers_display_{{ preg_replace('/[^a-zA-Z0-9]/', '', $id) }}';

                    if (typeof window[update_function_name] === 'function') {
                        window[update_function_name](instance_index, selected_modifiers);
                    }

                    var product_row = document.querySelector('tr[data-row_index="' + row_count + '"]');
                    var product_quantity_input = product_row ? product_row.querySelector('.pos_quantity') : null;
                    var current_quantity = product_quantity_input ? product_quantity_input.value : '1';

                    var container = document.querySelector(
                        '.combo_modifier_prices_container[data-instance="' + instance_index + '"]');
                    if (container) {
                        container.innerHTML = '';

                        if (selected_modifiers.length === 0) {
                            container.style.display = 'none';
                        } else {
                            container.style.display = 'block';

                            var modifiers_by_set = {};

                            selected_modifiers.forEach(function(modifier_id) {
                                var modifier_price = 0;
                                var modifier_name = 'Unknown';
                                var set_name = 'Modifiers';

                                if (typeof window.combo_modifier_prices !== 'undefined' &&
                                    window.combo_modifier_prices[modifier_id]) {
                                    modifier_price = parseFloat(window.combo_modifier_prices[
                                        modifier_id]) || 0;
                                }

                                var checkbox = modal.querySelector(
                                    'input[type="checkbox"][value="' + modifier_id + '"]');
                                if (checkbox) {
                                    modifier_price = parseFloat(checkbox.getAttribute('data-price')) || 0;
                                    modifier_name = checkbox.getAttribute('data-name') ||
                                        (checkbox.closest('label') ? checkbox.closest('label').textContent.trim() : 'Unknown');
                                    set_name = checkbox.getAttribute('data-set-name') || 'Modifiers';
                                }

                                if (!modifiers_by_set[set_name]) {
                                    modifiers_by_set[set_name] = [];
                                }

                                modifiers_by_set[set_name].push({
                                    id: modifier_id,
                                    name: modifier_name,
                                    price: modifier_price
                                });
                            });

                            for (var set_name in modifiers_by_set) {
                                var set_html = '<div class="modifier-set-group">';
                                set_html += '<div style=" color: #333;">' + set_name + ':</div>';

                                modifiers_by_set[set_name].forEach(function(modifier) {
                                    if (modifier.price > 0) {
                                        set_html += '<div class="product_modifier">' +
                                            '<div class="combo-modifier-item" style="margin: 2px 0; margin-left: 10px;">' +
                                            '<i class="fas fa-check-circle" style="color: #4caf50; margin-right: 5px;"></i>' +
                                            '<span style="color: #4caf50; font-weight: 500;">' + modifier.name + '</span>' +
                                            '<br>' +
                                            '<span style="color: #666; font-size: 0.9em;">(' + modifier.price.toFixed(2) + ' x <span class="modifier_qty_text">' + current_quantity + '</span>)</span>' +
                                            '</div>' +
                                            '<input type="hidden" class="modifiers_price" ' +
                                            'name="products[' + row_count + '][combo_modifier_price][]" ' +
                                            'value="' + modifier.price + '">' +
                                            '<input type="hidden" class="modifiers_quantity" ' +
                                            'name="products[' + row_count + '][combo_modifier_quantity][]" ' +
                                            'value="' + current_quantity + '">' +
                                            '</div>';
                                    }
                                });

                                set_html += '</div>';
                                container.insertAdjacentHTML('beforeend', set_html);
                            }
                        }
                    }

                    if (typeof pos_total_row === 'function') {
                        pos_total_row();
                    }
                });
            });
        }
    }

    function combine_single_modifiers(row_count, combo_index) {
        var modal = document.querySelector('.modal#{{ $id }}');
        if (!modal) {
            return;
        }

        var instance_index_input = modal.querySelector('.instance_index');
        var instance_index = instance_index_input ? instance_index_input.value : null;

        var single_modifier_field = document.querySelector('input.single_combo_modifier_ids_' + instance_index);
        var combined_value = '';

        if (single_modifier_field && single_modifier_field.value) {
            combined_value = single_modifier_field.value;
        }

        var exact_target_field = document.querySelector('input.combo_modifier_ids_' + instance_index);
        if (exact_target_field) {
            exact_target_field.value = combined_value;
        }

        var single_field = document.querySelector('input.single_combo_modifier_ids_' + instance_index);
        if (single_field) {
            single_field.value = combined_value;
        }
    }

    var function_name = 'update_selected_modifiers_display_{{ preg_replace('/[^a-zA-Z0-9]/', '', $id) }}';
    window[function_name] = function(instance_index_param, selected_modifiers) {
        var target_span = document.getElementById('selected_modifiers_{{ $id }}');

        var modal = document.querySelector('.modal#{{ $id }}');
        if (!modal) {
            return;
        }

        var instance_index_input = modal.querySelector('.instance_index');
        var own_instance_index = instance_index_input ? instance_index_input.value : null;

        var combo_modifier_field = document.querySelector('input.single_combo_modifier_ids_' + own_instance_index);
        var actual_modifier_ids = [];

        if (combo_modifier_field && combo_modifier_field.value) {
            actual_modifier_ids = combo_modifier_field.value.split(',');
        }
        var prices_container = document.querySelector('.combo_modifier_prices_container[data-instance="' +
            own_instance_index + '"]');

        if (actual_modifier_ids.length === 0) {
            if (target_span) {
                target_span.innerHTML = '';
                target_span.style.display = 'none';
            }
            if (prices_container) {
                prices_container.style.display = 'none';
            }
            return;
        }
        if (target_span) {
            target_span.innerHTML = '';
            target_span.style.display = 'none';
        }

        if (prices_container) {
            prices_container.style.display = 'block';
        }
    };
</script>
