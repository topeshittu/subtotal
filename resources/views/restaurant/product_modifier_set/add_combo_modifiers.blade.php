@if (empty($edit_modifiers))
    <small>
        @foreach ($modifiers as $modifier)
            <div class="product_modifier">
                <div class="modifier_name"> {{ $modifier->name }} </div>
                (<span class="modifier_price_text">{{ @num_format($modifier->sell_price_inc_tax) }}</span> X <span
                    class="modifier_qty_text">{{ @num_format($quantity) }}</span>)
                <input type="hidden" name="products[{{ $index }}][modifier][]" value="{{ $modifier->id }}">
                <input type="hidden" class="modifiers_price" name="products[{{ $index }}][modifier_price][]"
                    value="{{ @num_format($modifier->sell_price_inc_tax) }}">
                <input type="hidden" class="modifiers_quantity"
                    name="products[{{ $index }}][modifier_quantity][]" value="{{ $quantity }}">
                <input type="hidden" name="products[{{ $index }}][modifier_set_id][]"
                    value="{{ $modifier->product_id }}">
            </div>
        @endforeach
    </small>
@else
    @foreach ($modifiers as $modifier)
        <div class="product_modifier">
            <div class="modifier_name">
                @if(is_object($modifier->variation) && isset($modifier->variation->name))
                    {{ $modifier->variation->name }}
                @elseif(is_object($modifier->variations) && isset($modifier->variations->name))
                    {{ $modifier->variations->name }}
                @elseif(isset($modifier->variation_name))
                    {{ $modifier->variation_name }}
                @elseif(isset($modifier->name))
                    {{ $modifier->name }}
                @else
                    @php
                        $variation = \App\Models\Variation::find($modifier->variation_id);
                    @endphp
                    {{ $variation ? $variation->name : 'Unknown Modifier' }}
                @endif
            </div>
            (<span class="modifier_price_text">{{ @num_format($modifier->unit_price_inc_tax) }}</span> X <span
                class="modifier_qty_text">{{ @num_format($modifier->quantity) }}</span>)
            <input type="hidden" name="products[{{ $index }}][modifier][]"
                value="{{ $modifier->variation_id }}">
            <input type="hidden" class="modifiers_price" name="products[{{ $index }}][modifier_price][]"
                value="{{ @num_format($modifier->unit_price_inc_tax) }}">
            <input type="hidden" class="modifiers_quantity" name="products[{{ $index }}][modifier_quantity][]"
                value="{{ $modifier->quantity }}">
            <input type="hidden" name="products[{{ $index }}][modifier_set_id][]"
                value="{{ $modifier->product_id }}">
            <input type="hidden" name="products[{{ $index }}][modifier_sell_line_id][]"
                value="{{ $modifier->id }}">
        </div>
    @endforeach
@endif
