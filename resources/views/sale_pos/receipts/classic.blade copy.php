<div class="invoice-container" style="font-family: Arial, sans-serif; font-size: 13px; color: #000;">
    <!-- Header -->
    <table width="100%">
        <tr>
            <td>
                <h2 style="margin:0;">{{ $receipt_details->display_name ?? '' }}</h2>
                <p style="margin:0;">{{ $receipt_details->address ?? '' }}<br>
                {{ $receipt_details->location_custom_fields ?? '' }}</p>
                <p style="margin:0;">Tel : {!! $receipt_details->contact ?? '' !!}</p>
            </td>
            <td align="right">
                <h2 style="margin:0;">Invoice</h2>
                <p style="margin:0;">
                Web : {{ $receipt_details->website ?? '' }}</p>
            </td>
        </tr>
    </table>
    <hr>

    <!-- Invoice Info -->
    <table width="100%" style="margin-bottom: 10px;">
        <tr>
            <td>
                Invoice Date : {{ $receipt_details->invoice_date ?? now() }}<br>
                Invoice No : {{ $receipt_details->invoice_no ?? 'INV001' }}<br>
                Customer : {{ $receipt_details->customer_name ?? 'CASH' }}
            </td>
            <td align="right">
                Ref No : {{ $receipt_details->ref_no ?? '' }}<br>
                Location : {{ $receipt_details->location_name ?? '' }}<br>
                Sales Person : {{ $receipt_details->sales_person }}
            </td>
        </tr>
    </table>

    <!-- Items Table -->
 <table class="items-table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Code</th>
                <th>Product Name</th>
                <th>Warranty</th>
                <th>Qty</th>
                <th>Free</th>
                <th>Selling Price</th>
                <th>Net Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receipt_details->lines as $key => $line)
            <tr>
                <td>{{ $key+1 }}</td>
                <td>{{ $line['sub_sku'] ?? '' }}</td>
                <td>{{ $line['name'] ?? '' }}</td>
                <td>@if(!empty($line['warranty_name'])) <br><small>{{$line['warranty_name']}} </small>@endif @if(!empty($line['warranty_exp_date'])) <small>- {{@format_date($line['warranty_exp_date'])}} </small>@endif
						@if(!empty($line['warranty_description'])) <small> {{$line['warranty_description'] ?? ''}}</small>@endif
					</td>
                <td>{{ $line['quantity'] ?? 1 }}</td>
                <td>{{ $line['free_qty'] ?? 0 }}</td>
                <td align="right">{{ $line['unit_price_before_discount'] ?? '0.00' }}</td>
                <td align="right">{{ $line['line_total'] ?? '0.00' }}</td>
            </tr>
            @endforeach
			@if (!empty($line['combo_variations']))
					@foreach ($line['combo_variations'] as $combo)
					<tr>
						<td>&nbsp;</td>
						<td>{{ $combo['sku'] }}</td>
						<td>
							{{ $combo['name'] }} {{ $combo['variation'] }} {{ $combo['sub_sku'] }} x {{ $combo['quantity'] }}  {{--{{ $combo['units'] }} - {{ $combo['unit_price_inc_tax'] }} --}}
						</td>
						<td>{{ $combo['warranty_name'] }}
					</td>
						<td>-</td>
						<td>{{  $line['quantity'] ?? 0 }}</td>
						<td class="text-right">0</td>
						<td class="text-right">0</td>
					</tr>
					@if (!empty($combo['combo_modifiers']))
					@foreach ($combo['combo_modifiers'] as $combo_modifier)
					<tr>
						<td>&nbsp;</td>
						<td>- {{ $combo_modifier['combo_modifier_name'] }} </td>
						@if (!empty($combo_modifier['combo_modifier_price']) && $combo_modifier['combo_modifier_price'] != 0)
						<td></td>
						<td class="text-right">{{ $combo_modifier['combo_modifier_price'] }}</td>
						<td class="text-right">{{ $combo_modifier['combo_modifier_price'] }}</td>
						@else
						<td></td>
						<td class="text-right"></td>
						<td class="text-right"></td>
						@endif
					</tr>
					@endforeach
					@endif
					@endforeach
					@endif
					@if (empty($line['combo_variations']))
					@if(!empty($line['modifiers']))
					@foreach($line['modifiers'] as $modifier)
					<tr>
						<td>
							&nbsp;
						</td>
						<td>
							{{$modifier['name']}} {{$modifier['variation']}}
							@if(!empty($modifier['sub_sku'])), {{$modifier['sub_sku']}} @endif @if(!empty($modifier['cat_code'])), {{$modifier['cat_code']}}@endif
							@if(!empty($modifier['sell_line_note']))({{$modifier['sell_line_note']}}) @endif
						</td>
						<td class="text-right">{{$modifier['quantity']}} {{$modifier['units']}} </td>
						@if(empty($receipt_details->hide_price))
						<td class="text-right">{{$modifier['unit_price_inc_tax']}}</td>
						@if(!empty($receipt_details->item_discount_label))
						<td class="text-right">0.00</td>
						@endif
						<td class="text-right">{{$modifier['line_total']}}</td>
						@endif
					</tr>
					@endforeach
					@endif
					@endif
					

            @php $lines = count($receipt_details->lines); @endphp
            @for ($i = $lines; $i < 16; $i++)
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            @endfor
        </tbody>
    </table>

    <!-- Totals -->
    <table width="100%" style="margin-top:10px;">
        <tr>
            <td align="right"><strong>Total Gross Amount:</strong></td>
            <td align="right">{{ $receipt_details->subtotal ?? '0.00' }}</td>
        </tr>
        <tr>
            <td align="right"><strong>Discount:</strong></td>
            <td align="right">{{ $receipt_details->discount ?? '0.00' }}</td>
        </tr>
        <tr>
            <td align="right"><strong>Other Charges:</strong></td>
            <td align="right">{{ $receipt_details->shipping_charges ?? '0.00' }}</td>
        </tr>
        <tr>
            <td align="right"><strong>Net Amount:</strong></td>
            <td align="right">{{ $receipt_details->total ?? '0.00' }}</td>
        </tr>
    </table>

    <br>

    <!-- Footer -->
   @if(!empty($receipt_details->footer_text))
	<div class="@if($receipt_details->show_barcode || $receipt_details->show_qr_code) col-xs-8 @else col-xs-12 @endif">
		{!! $receipt_details->footer_text !!}
	</div>
	@endif
</div>
<style>
    .items-table {
        width: 100%;
        border-collapse: collapse;
    }

    /* Full border only for thead */
    .items-table thead th {
        border: 1px solid #000;
        padding: 6px;
        font-size: 12px;
        background: #f2f2f2;
        text-align: left;
    }

    /* Only vertical borders for tbody */
    .items-table tbody td {
        border-left: 1px solid #000;
        border-right: 1px solid #000;
        padding: 6px;
        font-size: 12px;
    }

    /* Remove default table border */
    .items-table,
    .items-table tbody tr {
        border: none;
    }
	.items-table tbody tr:last-child td {
    border-bottom: 1px solid #000;
}
</style>