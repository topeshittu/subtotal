@php
    $id = 'modifier_' . $row_count . '_' . time();
@endphp
<div class="modal fade modifier_modal" id="{{ $id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('restaurant.modifiers_for_product'): <span class="text-success"></span>
                </h4>
            </div>

            <div class="modal-body">
                @if (!empty($product_ms))
                    <div class="panel-group" id="accordion{{ $id }}" role="tablist"
                        aria-multiselectable="true">

                        @foreach ($product_ms as $modifier_set)
                            @php
                                $collapse_id = 'collapse' . $modifier_set->id . $id;
                            @endphp

                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingOne">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse"
                                            data-parent="#accordion{{ $id }}" href="#{{ $collapse_id }}"
                                            aria-expanded="true" aria-controls="collapseOne">
                                            {{ $modifier_set->name }}
                                        </a>
                                    </h4>
                                </div>
                                <input type="hidden" class="modifiers_exist" value="true">
                                <input type="hidden" class="index" value="{{ $row_count }}">

                                <div id="{{ $collapse_id }}"
                                    class="panel-collapse collapse @if ($loop->index == 0) in @endif"
                                    role="tabpanel" aria-labelledby="headingOne">
                                    <div class="panel-body">
                                        <div class="btn-group" data-toggle="">
                                            @foreach ($modifier_set->variations as $modifier)
                                                @php
                                                    $is_selected = !empty($edit_modifiers) &&
                                                                   isset($product) &&
                                                                   isset($product->modifiers_ids) &&
                                                                   is_array($product->modifiers_ids) &&
                                                                   in_array($modifier->id, $product->modifiers_ids);
                                                @endphp
                                                <label
                                                    class="btn btn-outline-primary modifier-btn @if ($is_selected) active btn-success @endif">
                                                    <input type="checkbox" autocomplete="off"
                                                        value="{{ $modifier->id }}"
                                                        data-price="{{ $modifier->sell_price_inc_tax }}"
                                                        data-modifier-set-id="{{ $modifier->product_id }}"
                                                        data-set-name="{{ $modifier_set->name }}"
                                                        @if ($is_selected) checked @endif>
                                                    <i class="fa fa-square-o modifier-icon"></i>
                                                    {{ $modifier->name }}
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <button data-url="{{ action('Restaurant\ProductModifierSetController@add_selected_modifiers') }}"
                    type="button" class="btn btn-primary add_modifier" data-dismiss="modal">
                    @lang('messages.add')</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
            </div>

            {!! Form::close() !!}

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<script type="text/javascript">
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initialize_traditional_modifiers);
    } else {
        initialize_traditional_modifiers();
    }

    function initialize_traditional_modifiers() {
        var modal = document.getElementById('{{ $id }}');

        modal.addEventListener('click', function(e) {
            if (e.target.closest('.add_modifier')) {
                e.preventDefault();
                e.stopPropagation();
                if (typeof window.bootstrap !== 'undefined') {
                    var modal_instance = bootstrap.Modal.getInstance(modal);
                    if (modal_instance) {
                        modal_instance.hide();
                    }
                } else if (typeof window.$ !== 'undefined') {
                    $(modal).modal('hide');
                }
                return false;
            }
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
                        label.classList.add('btn-outline-primary');
                        label.classList.remove('active', 'btn-success');
                    }
                    update_traditional_modifier_display();
                }
            }
        });

        function update_traditional_modifier_display() {
            setTimeout(function() {
                var plain_modifiers_section = document.getElementById('plain-items-{{ $row_count }}');
                var target_span = plain_modifiers_section ? plain_modifiers_section.querySelector('.selected_modifiers') : null;
                if (!target_span) {
                    return;
                }
                update_display_content(target_span);
            }, 100);
        }

        function update_display_content(target_span) {
            var checked_modifiers = modal.querySelectorAll('input[type="checkbox"]:checked');
            var count_span = document.querySelector('.selected-modifier-count-{{ $row_count }}');
            if (count_span) {
                count_span.textContent = checked_modifiers.length;
            }
            if (checked_modifiers.length === 0) {
                target_span.innerHTML = '';
                clear_traditional_modifier_prices();
                return;
            }
            var product_row = modal.closest('.product_row');
            var product_quantity = product_row ? product_row.querySelector('.pos_quantity') : null;
            var current_quantity = product_quantity ? product_quantity.value : '1.00';

            var modifiers_by_set = {};
            var modifier_data = [];

            checked_modifiers.forEach(function(checkbox) {
                var label = checkbox.closest('.modifier-btn');
                var modifier_name = label ? label.textContent.trim() : 'Unknown';
                var modifier_id = checkbox.value;
                var modifier_price = parseFloat(checkbox.getAttribute('data-price') || '0');
                var modifier_set_id = checkbox.getAttribute('data-modifier-set-id') || '';
                var set_name = checkbox.getAttribute('data-set-name') || 'Modifiers';

                if (!modifiers_by_set[set_name]) {
                    modifiers_by_set[set_name] = [];
                }

                modifiers_by_set[set_name].push({
                    name: modifier_name,
                    price: modifier_price,
                    id: modifier_id
                });

                modifier_data.push({
                    id: modifier_id,
                    name: modifier_name,
                    price: modifier_price,
                    modifierSetId: modifier_set_id
                });
            });

            var html = '';
            for (var set_name in modifiers_by_set) {
                html += '<div class="modifier-set-group">';
                html += '<div style=" color: #333;">' + set_name + ':</div>';

                modifiers_by_set[set_name].forEach(function(modifier) {
                    html += '<div class="product_modifier">';
                    html += '<div class="modifier-item" style="margin-left: 10px;">';
                    html += '<i class="fas fa-check-circle" style="color: #4caf50;"></i>';
                    html += '<span style="color: #4caf50; font-weight: 500;">' + modifier.name + '</span>';
                    html += '<span style="color: #666; font-size: 0.9em;">(' + modifier.price.toFixed(2) + ' x <span class="modifier_qty_text">' + current_quantity + '</span>)</span>';
                    html += '</div>';
                    html += '</div>';
                });

                html += '</div>';
            }

            target_span.innerHTML = html;
            update_traditional_modifier_prices(modifier_data);
        }

        function clear_traditional_modifier_prices() {
            var existing_wrappers = document.querySelectorAll('.traditional-modifier-wrapper[data-traditional="true"]');
            existing_wrappers.forEach(function(wrapper) {
                wrapper.remove();
            });
        }

        function update_traditional_modifier_prices(modifier_data) {
            clear_traditional_modifier_prices();
            var product_row = modal.closest('.product_row');
            var row_index = product_row ? product_row.getAttribute('data-row-index') || '{{ $row_count }}' : '{{ $row_count }}';
            var product_quantity = product_row ? product_row.querySelector('.pos_quantity') : null;
            var current_quantity = product_quantity ? product_quantity.value : '1';
            var target_container = document.querySelector('.modifiers_html') || document.querySelector('form');
            if (!target_container) {
                return;
            }
            var wrapper_div = document.createElement('div');
            wrapper_div.className = 'traditional-modifier-wrapper';
            wrapper_div.setAttribute('data-traditional', 'true');
            modifier_data.forEach(function(modifier) {
                var modifier_div = document.createElement('div');
                modifier_div.className = 'product_modifier';
                var price_input = document.createElement('input');
                price_input.type = 'hidden';
                price_input.className = 'modifiers_price';
                price_input.name = 'products[' + row_index + '][modifier_price][]';
                price_input.value = modifier.price;
                var qty_input = document.createElement('input');
                qty_input.type = 'hidden';
                qty_input.className = 'modifiers_quantity';
                qty_input.name = 'products[' + row_index + '][modifier_quantity][]';
                qty_input.value = current_quantity;
                var id_input = document.createElement('input');
                id_input.type = 'hidden';
                id_input.name = 'products[' + row_index + '][modifier][]';
                id_input.value = modifier.id;
                var set_id_input = document.createElement('input');
                set_id_input.type = 'hidden';
                set_id_input.name = 'products[' + row_index + '][modifier_set_id][]';
                set_id_input.value = modifier.modifierSetId;
                modifier_div.appendChild(price_input);
                modifier_div.appendChild(qty_input);
                modifier_div.appendChild(id_input);
                modifier_div.appendChild(set_id_input);
                wrapper_div.appendChild(modifier_div);
            });
            target_container.appendChild(wrapper_div);
            if (typeof pos_total_row !== 'undefined') {
                pos_total_row();
            }
        }

        if (typeof window.$ !== 'undefined') {
            $(modal).on('shown.bs.modal', function() {
                update_traditional_modifier_display();
            });
        } else {
            modal.addEventListener('shown.bs.modal', function() {
                update_traditional_modifier_display();
            });
        }

        @if (!empty($is_edit))
            update_traditional_modifier_display();
        @endif
    }
</script>
