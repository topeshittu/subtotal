
@php
    $combo_products = $combo_products ?? [];

    // For edit mode, handle both array and object format
    if (is_object($combo_products) && method_exists($combo_products, 'toArray')) {
        $combo_products = $combo_products->toArray();
    }
@endphp

@if (!empty($combo_products))
    <div class="realtime-combo-modifiers-container">
        <div class="combo-products-header">
            <h6 class="combo-title">
                <i class="fa fa-cubes"></i> @lang('restaurant.combo_items')
            </h6>
        </div>

        <div class="combo-products-list">
            @foreach ($combo_products as $index => $combo_product)
                @php
                    // Handle both array and object format
                    if (is_array($combo_product)) {
                        $product_name = $combo_product['product_name'] ?? 'Unknown Product';
                        $product_id = $combo_product['product_id'] ?? '';
                        $variation_id = $combo_product['variation_id'] ?? '';
                        $quantity = $combo_product['quantity'] ?? 1;
                        $qty_required = $combo_product['qty_required'] ?? 1;
                    } else {
                        $product_name = $combo_product->product_name ?? 'Unknown Product';
                        $product_id = $combo_product->product_id ?? '';
                        $variation_id = $combo_product->variation_id ?? '';
                        $quantity = $combo_product->quantity ?? 1;
                        $qty_required = $combo_product->qty_required ?? ($combo_product->quantity ?? 1);
                    }

                    $modal_id =
                        'combo_modifier_modal_' . $row_count . '_' . $index . '_' . $variation_id . '_' . time();

                    // Calculate final quantity based on action
                    if (isset($action) && $action === 'edit') {
                        $final_qty = $quantity;
                    } else {
                        $final_qty = $qty_required;
                    }
                @endphp

                <div class="combo-product-item" data-combo-index="{{ $index }}">
                    <div class="combo-product-info">
                        <div class="combo-product-name">
                            <i class="fa fa-cube"></i> {{ $product_name }}
                        </div>
                        <div class="combo-product-qty">
                            Qty: {{ $final_qty }}
                        </div>
                    </div>

                    <div class="combo-product-actions">
                        <button type="button" class="btn btn-sm btn-info realtime-combo-modifier-btn"
                            data-toggle="modal" data-target="#{{ $modal_id }}" data-row-count="{{ $row_count }}"
                            data-combo-index="{{ $index }}" data-product-id="{{ $product_id }}"
                            data-variation-id="{{ $variation_id }}">
                            <i class="fa fa-cog"></i> @lang('restaurant.modifiers')
                            <span class="combo-modifier-count"></span>
                        </button>
                    </div>

                    <input type="hidden" name="products[{{ $row_count }}][combo][{{ $index }}][product_id]"
                        value="{{ $product_id }}">
                    <input type="hidden"
                        name="products[{{ $row_count }}][combo][{{ $index }}][variation_id]"
                        value="{{ $variation_id }}">
                    <input type="hidden" class="combo_product_qty"
                        name="products[{{ $row_count }}][combo][{{ $index }}][quantity]"
                        data-unit_quantity="{{ $qty_required }}" value="{{ $final_qty }}">
                    <input type="hidden"
                        name="products[{{ $row_count }}][combo][{{ $index }}][modifier_ids]"
                        class="combo_modifier_ids_{{ $row_count }}_{{ $index }}" value="">
                    <input type="hidden"
                        name="products[{{ $row_count }}][combo][{{ $index }}][single_modifier_ids]"
                        class="single_combo_modifier_ids_{{ $row_count }}_{{ $index }}" value="">
                    <div class="modal fade realtime-combo-modifier-modal" id="{{ $modal_id }}" tabindex="-1"
                        role="dialog">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title">
                                        <i class="fa fa-cog"></i> @lang('restaurant.modifiers') - {{ $product_name }}
                                    </h4>
                                </div>
                                <div class="modal-body realtime-combo-modifier-body"
                                    data-variation-id="{{ $variation_id }}" data-product-id="{{ $product_id }}">
                                    <div class="loading-combo-modifiers text-center">
                                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                                        <p class="text-muted">@lang('messages.loading')...</p>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">
                                        @lang('messages.cancel')
                                    </button>
                                    <button type="button" class="btn btn-primary save-combo-modifiers-btn"
                                        data-row-count="{{ $row_count }}" data-combo-index="{{ $index }}"
                                        data-modal-id="{{ $modal_id }}">
                                        <i class="fa fa-save"></i> @lang('messages.save')
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <style>
        .realtime-combo-modifiers-container {
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }

        .combo-products-header {
            margin-bottom: 15px;
        }

        .combo-title {
            font-size: 14px;
            font-weight: 600;
            color: #495057;
            margin: 0;
            border-bottom: 2px solid #17a2b8;
            padding-bottom: 5px;
        }

        .combo-products-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .combo-product-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 10px 15px;
            transition: all 0.2s ease;
        }

        .combo-product-item:hover {
            border-color: #17a2b8;
            box-shadow: 0 2px 5px rgba(23, 162, 184, 0.15);
        }

        .combo-product-info {
            flex: 1;
        }

        .combo-product-name {
            font-size: 13px;
            font-weight: 500;
            color: #495057;
            margin-bottom: 3px;
        }

        .combo-product-name i {
            color: #17a2b8;
            margin-right: 5px;
        }

        .combo-product-qty {
            font-size: 11px;
            color: #6c757d;
        }

        .combo-product-actions {
            margin-left: 15px;
        }

        .realtime-combo-modifier-btn {
            font-size: 11px;
            padding: 4px 8px;
            background: #17a2b8;
            border-color: #17a2b8;
        }

        .realtime-combo-modifier-btn:hover {
            background: #138496;
            border-color: #117a8b;
        }

        .combo-modifier-count {
            background: rgba(255, 255, 255, 0.3);
            padding: 1px 5px;
            border-radius: 10px;
            margin-left: 3px;
            font-size: 10px;
        }

        .realtime-combo-modifier-body {
            max-height: 500px;
            overflow-y: auto;
        }

        .loading-combo-modifiers {
            padding: 40px 20px;
        }

        .combo-modifier-set {
            margin-bottom: 25px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            background: #f8f9fa;
        }

        .combo-modifier-set-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #495057;
            border-bottom: 2px solid #17a2b8;
            padding-bottom: 8px;
        }

        .combo-modifier-options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 10px;
        }

        .combo-modifier-option {
            display: flex;
            align-items: center;
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            margin: 0;
        }

        .combo-modifier-option:hover {
            border-color: #17a2b8;
            box-shadow: 0 2px 5px rgba(23, 162, 184, 0.15);
            transform: translateY(-1px);
        }

        .combo-modifier-option.selected {
            background: #17a2b8;
            border-color: #17a2b8;
            color: white;
            box-shadow: 0 3px 10px rgba(23, 162, 184, 0.3);
        }

        .combo-modifier-content {
            flex: 1;
        }

        .combo-modifier-name {
            font-weight: 500;
            font-size: 13px;
            line-height: 1.3;
        }

        .combo-modifier-price {
            font-size: 11px;
            opacity: 0.8;
            margin-top: 2px;
        }

        .combo-modifier-option.selected .combo-modifier-price {
            opacity: 0.9;
        }

        .combo-modifier-check {
            opacity: 0;
            transition: opacity 0.2s ease;
            margin-left: 8px;
            font-size: 14px;
        }

        .combo-modifier-option.selected .combo-modifier-check {
            opacity: 1;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .combo-product-item {
                flex-direction: column;
                align-items: stretch;
            }

            .combo-product-actions {
                margin-left: 0;
                margin-top: 10px;
                text-align: center;
            }

            .combo-modifier-options-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).on('shown.bs.modal', '.realtime-combo-modifier-modal', function() {
                const modal = $(this);
                const modalBody = modal.find('.realtime-combo-modifier-body');
                const variationId = modalBody.data('variation-id');
                const productId = modalBody.data('product-id');

                if (modalBody.find('.combo-modifier-set').length > 0) {
                    return;
                }

                $.ajax({
                    url: '/pos/get-product-modifiers',
                    method: 'GET',
                    data: {
                        product_id: productId,
                        variation_id: variationId
                    },
                    success: function(response) {
                        if (response.success && response.product && response.product
                            .product_ms) {
                            renderComboModifiers(modalBody, response.product.product_ms);
                        } else {
                            modalBody.html(`
                        <div class="text-center text-muted">
                            <i class="fa fa-info-circle fa-2x"></i>
                            <p>@lang('restaurant.no_modifiers_available')</p>
                        </div>
                    `);
                        }
                    },
                    error: function() {
                        modalBody.html(`
                    <div class="text-center text-danger">
                        <i class="fa fa-exclamation-triangle fa-2x"></i>
                        <p>@lang('messages.something_went_wrong')</p>
                    </div>
                `);
                    }
                });
            });

            function renderComboModifiers(container, modifierSets) {
                let html = '';

                modifierSets.forEach(function(modifierSet) {
                    if (!modifierSet || !modifierSet.id || !modifierSet.name) {
                        return; // Skip invalid modifier sets
                    }

                    html += `
                <div class="combo-modifier-set" data-modifier-set-id="${modifierSet.id}">
                    <h6 class="combo-modifier-set-title">
                        ${modifierSet.name}
                        ${modifierSet.single_selection ? '<small class="text-muted">(@lang('restaurant.single_selection'))</small>' : ''}
                    </h6>
                    <div class="combo-modifier-options-grid">
            `;

                    if (modifierSet.modifiers && Array.isArray(modifierSet.modifiers)) {
                        modifierSet.modifiers.forEach(function(modifier) {
                            if (!modifier || !modifier.id || !modifier.name) {
                                return; // Skip invalid modifiers
                            }
                            html += `
                        <label class="combo-modifier-option" 
                               data-modifier-id="${modifier.id}"
                               data-modifier-set-id="${modifierSet.id}"
                               data-modifier-price="${modifier.modifier_price || 0}"
                               data-single-selection="${modifierSet.single_selection ? 'true' : 'false'}">
                            <input type="checkbox" 
                                   value="${modifier.id}"
                                   data-modifier-set-id="${modifierSet.id}"
                                   data-modifier-price="${modifier.modifier_price || 0}"
                                   style="display: none;">
                            
                            <div class="combo-modifier-content">
                                <div class="combo-modifier-name">${modifier.name}</div>
                                ${modifier.modifier_price > 0 ? `<div class="combo-modifier-price">+${parseFloat(modifier.modifier_price).toFixed(2)}</div>` : ''}
                            </div>
                            
                            <div class="combo-modifier-check">
                                <i class="fa fa-check"></i>
                            </div>
                        </label>
                    `;
                        });
                    } else {
                        html += '<p class="text-muted">@lang('restaurant.no_modifiers_available')</p>';
                    }

                    html += `
                    </div>
                </div>
            `;
                });

                container.html(html);
            }

            $(document).on('click', '.combo-modifier-option', function(e) {
                e.preventDefault();

                const checkbox = $(this).find('input[type="checkbox"]');
                const modifierSet = $(this).closest('.combo-modifier-set');
                const isSingleSelection = $(this).data('single-selection') === true;

                if (isSingleSelection) {
                    // For single selection, uncheck all others in this set
                    modifierSet.find('.combo-modifier-option').removeClass('selected');
                    modifierSet.find('input[type="checkbox"]').prop('checked', false);
                }

                // Toggle current selection
                const isSelected = $(this).hasClass('selected');
                if (isSelected) {
                    $(this).removeClass('selected');
                    checkbox.prop('checked', false);
                } else {
                    $(this).addClass('selected');
                    checkbox.prop('checked', true);
                }
            });

            // Handle save combo modifiers
            $(document).on('click', '.save-combo-modifiers-btn', function() {
                const rowCount = $(this).data('row-count');
                const comboIndex = $(this).data('combo-index');
                const modalId = $(this).data('modal-id');
                const modal = $('#' + modalId);

                // Get selected modifiers
                const selectedModifiers = [];
                modal.find('.combo-modifier-option.selected').each(function() {
                    selectedModifiers.push({
                        modifier_set_id: $(this).data('modifier-set-id'),
                        modifier_item_id: $(this).data('modifier-id'),
                        modifier_price: $(this).data('modifier-price')
                    });
                });

                const modifierIds = selectedModifiers.map(m => m.modifier_item_id).join(',');
                $(`.combo_modifier_ids_${rowCount}_${comboIndex}`).val(modifierIds);
                updateComboModifierButton(rowCount, comboIndex, selectedModifiers.length);
                modal.modal('hide');

                console.log('Combo modifiers saved:', selectedModifiers);
            });

            function updateComboModifierButton(rowCount, comboIndex, modifierCount) {
                const button = $(
                    `.realtime-combo-modifier-btn[data-row-count="${rowCount}"][data-combo-index="${comboIndex}"]`
                    );
                const countSpan = button.find('.combo-modifier-count');

                if (modifierCount > 0) {
                    if (countSpan.length === 0) {
                        button.append(`<span class="combo-modifier-count">(${modifierCount})</span>`);
                    } else {
                        countSpan.text(`(${modifierCount})`);
                    }
                } else {
                    countSpan.text('');
                }
            }
        });
    </script>
@endif
