@php
    $modal_id = 'modifier_modal_' . $row_count . '_' . ($product->variation_id ?? '') . '_' . time();
    $existing_modifiers = [];
    
    if(isset($action) && $action === 'edit' && isset($product->modifiers)) {
        $existing_modifiers = $product->modifiers;
    }
    
    $modifier_count = count($existing_modifiers);
@endphp

<div class="realtime-modifiers-container">
    <button type="button" class="btn btn-sm btn-primary realtime-modifier-btn" 
            data-toggle="modal" data-target="#{{ $modal_id }}"
            data-row-count="{{ $row_count }}"
            data-product-id="{{ $product->product_id ?? '' }}"
            data-variation-id="{{ $product->variation_id ?? '' }}">
        <i class="fa fa-cog"></i> @lang('restaurant.modifiers')
        @if($modifier_count > 0)
            <span class="modifier-count">({{ $modifier_count }})</span>
        @endif
    </button>
</div>

<div class="modal fade realtime-modifier-modal" id="{{ $modal_id }}" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">
                    <i class="fa fa-cog"></i> @lang('restaurant.modifiers') - {{ $product->product_name ?? 'Product' }}
                </h4>
            </div>
            <div class="modal-body realtime-modifier-body">
                @if(!empty($product_ms))
                    @foreach($product_ms as $modifier_set_index => $modifier_set)
                        @if($modifier_set && isset($modifier_set->id) && isset($modifier_set->name))
                            <div class="modifier-set" data-modifier-set-id="{{ $modifier_set->id }}">
                                <h6 class="modifier-set-title">
                                    {{ $modifier_set->name }}
                                    @if(isset($modifier_set->single_selection) && $modifier_set->single_selection)
                                        <small class="text-muted">(@lang('restaurant.single_selection'))</small>
                                    @endif
                                </h6>
                                
                                <div class="modifier-options-grid">
                                    @if(isset($modifier_set->modifiers) && !empty($modifier_set->modifiers))
                                        @foreach($modifier_set->modifiers as $modifier_index => $modifier)
                                            @if($modifier && isset($modifier->id) && isset($modifier->name))
                                                @php
                                                    $is_selected = false;
                                                    if(!empty($existing_modifiers)) {
                                                        $is_selected = collect($existing_modifiers)->contains(function($em) use ($modifier_set, $modifier) {
                                                            return isset($em['modifier_set_id']) && 
                                                                   isset($em['modifier_item_id']) &&
                                                                   $em['modifier_set_id'] == $modifier_set->id && 
                                                                   $em['modifier_item_id'] == $modifier->id;
                                                        });
                                                    }
                                                @endphp
                                    
                                                <label class="modifier-option {{ $is_selected ? 'selected' : '' }}" 
                                                       data-modifier-id="{{ $modifier->id }}"
                                                       data-modifier-set-id="{{ $modifier_set->id }}"
                                                       data-modifier-price="{{ $modifier->modifier_price ?? 0 }}"
                                                       data-single-selection="{{ (isset($modifier_set->single_selection) && $modifier_set->single_selection) ? 'true' : 'false' }}">
                                                    <input type="checkbox" 
                                                           name="products[{{ $row_count }}][modifier_set_id][{{ $modifier_set->id }}][modifier_item_id][]"
                                                           value="{{ $modifier->id }}"
                                                           data-modifier-set-id="{{ $modifier_set->id }}"
                                                           data-modifier-price="{{ $modifier->modifier_price ?? 0 }}"
                                                           {{ $is_selected ? 'checked' : '' }}
                                                           style="display: none;">
                                                    
                                                    <div class="modifier-content">
                                                        <div class="modifier-name">{{ $modifier->name }}</div>
                                                        @if(isset($modifier->modifier_price) && $modifier->modifier_price > 0)
                                                            <div class="modifier-price">
                                                                +{{ number_format($modifier->modifier_price, 2) }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="modifier-check">
                                                        <i class="fa fa-check"></i>
                                                    </div>
                                                </label>
                                            @endif
                                        @endforeach
                                    @else
                                        <p class="text-muted">@lang('restaurant.no_modifiers_available')</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                @else
                    <p class="text-muted text-center">
                        <i class="fa fa-info-circle"></i> @lang('restaurant.no_modifiers_available')
                    </p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    @lang('messages.cancel')
                </button>
                <button type="button" class="btn btn-primary save-modifiers-btn" 
                        data-row-count="{{ $row_count }}" 
                        data-modal-id="{{ $modal_id }}">
                    <i class="fa fa-save"></i> @lang('messages.save')
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modifier-hidden-inputs" data-row-count="{{ $row_count }}">
    @if(isset($action) && $action === 'edit' && !empty($existing_modifiers))
        @foreach($existing_modifiers as $index => $modifier)
            <input type="hidden" name="products[{{ $row_count }}][modifier_set_id][{{ $index }}]" 
                   value="{{ $modifier['modifier_set_id'] ?? '' }}">
            <input type="hidden" name="products[{{ $row_count }}][modifier_item_id][{{ $index }}]" 
                   value="{{ $modifier['modifier_item_id'] ?? '' }}">
            <input type="hidden" name="products[{{ $row_count }}][modifier_price][{{ $index }}]" 
                   value="{{ $modifier['modifier_price'] ?? '' }}">
        @endforeach
    @endif
</div>

<style>
.realtime-modifier-btn {
    font-size: 11px;
    padding: 4px 8px;
    margin-top: 5px;
}

.realtime-modifier-btn .modifier-count {
    background: rgba(255, 255, 255, 0.3);
    padding: 1px 5px;
    border-radius: 10px;
    margin-left: 3px;
    font-size: 10px;
}

.realtime-modifier-body {
    max-height: 500px;
    overflow-y: auto;
}

.modifier-set {
    margin-bottom: 25px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 15px;
    background: #f8f9fa;
}

.modifier-set:last-child {
    margin-bottom: 0;
}

.modifier-set-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 15px;
    color: #495057;
    border-bottom: 2px solid #007bff;
    padding-bottom: 8px;
}

.modifier-options-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px;
}

.modifier-option {
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

.modifier-option:hover {
    border-color: #007bff;
    box-shadow: 0 2px 5px rgba(0, 123, 255, 0.15);
    transform: translateY(-1px);
}

.modifier-option.selected {
    background: #007bff;
    border-color: #007bff;
    color: white;
    box-shadow: 0 3px 10px rgba(0, 123, 255, 0.3);
}

.modifier-content {
    flex: 1;
}

.modifier-name {
    font-weight: 500;
    font-size: 13px;
    line-height: 1.3;
}

.modifier-price {
    font-size: 11px;
    opacity: 0.8;
    margin-top: 2px;
}

.modifier-option.selected .modifier-price {
    opacity: 0.9;
}

.modifier-check {
    opacity: 0;
    transition: opacity 0.2s ease;
    margin-left: 8px;
    font-size: 14px;
}

.modifier-option.selected .modifier-check {
    opacity: 1;
}

@media (max-width: 768px) {
    .modifier-options-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-dialog.modal-lg {
        width: 95%;
        margin: 10px auto;
    }
    
    .realtime-modifier-body {
        max-height: 400px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    $(document).on('click', '.modifier-option', function(e) {
        e.preventDefault();
        
        const checkbox = $(this).find('input[type="checkbox"]');
        const modifierSet = $(this).closest('.modifier-set');
        const isSingleSelection = $(this).data('single-selection') === true;
        
        if (isSingleSelection) {
            modifierSet.find('.modifier-option').removeClass('selected');
            modifierSet.find('input[type="checkbox"]').prop('checked', false);
        }
        const isSelected = $(this).hasClass('selected');
        if (isSelected) {
            $(this).removeClass('selected');
            checkbox.prop('checked', false);
        } else {
            $(this).addClass('selected');
            checkbox.prop('checked', true);
        }
    });
    
    $(document).on('click', '.save-modifiers-btn', function() {
        const rowCount = $(this).data('row-count');
        const modalId = $(this).data('modal-id');
        const modal = $('#' + modalId);
        const selectedModifiers = [];
        modal.find('.modifier-option.selected').each(function() {
            selectedModifiers.push({
                modifier_set_id: $(this).data('modifier-set-id'),
                modifier_item_id: $(this).data('modifier-id'),
                modifier_price: $(this).data('modifier-price')
            });
        });
        
        updateModifierHiddenInputs(rowCount, selectedModifiers);
        updateModifierButton(rowCount, selectedModifiers.length);
        modal.modal('hide');
    });
    
    function updateModifierHiddenInputs(rowCount, modifiers) {
        const container = $(`.modifier-hidden-inputs[data-row-count="${rowCount}"]`);
        
        container.find('input').remove();
        
        modifiers.forEach((modifier, index) => {
            container.append(`
                <input type="hidden" name="products[${rowCount}][modifier_set_id][${index}]" value="${modifier.modifier_set_id}">
                <input type="hidden" name="products[${rowCount}][modifier_item_id][${index}]" value="${modifier.modifier_item_id}">
                <input type="hidden" name="products[${rowCount}][modifier_price][${index}]" value="${modifier.modifier_price}">
            `);
        });
    }
    
    function updateModifierButton(rowCount, modifierCount) {
        const button = $(`.realtime-modifier-btn[data-row-count="${rowCount}"]`);
        const countSpan = button.find('.modifier-count');
        
        if (modifierCount > 0) {
            if (countSpan.length === 0) {
                button.append(`<span class="modifier-count">(${modifierCount})</span>`);
            } else {
                countSpan.text(`(${modifierCount})`);
            }
        } else {
            countSpan.remove();
        }
    }
});
</script>