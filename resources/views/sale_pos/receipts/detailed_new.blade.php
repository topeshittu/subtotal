@php
    $rd = $receipt_details ?? null;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Invoice - {{ $rd->invoice_no ?? '' }}</title>
  <style>
    body { font-family: Arial, Helvetica, sans-serif; color: #000; font-size: 12px; }
    .container { width: 100%; }
    .text-right { text-align: right; }
    .text-left { text-align: left; }
    .text-center { text-align: center; }
    .muted { color: #555; }
    .small { font-size: 11px; }
    .xs { font-size: 10px; }
    .fw-bold { font-weight: 600; }
    .mb-5 { margin-bottom: 5px; }
    .mb-8 { margin-bottom: 8px; }
    .mb-10 { margin-bottom: 10px; }
    .mb-15 { margin-bottom: 15px; }
    .mt-5 { margin-top: 5px; }
    .mt-8 { margin-top: 8px; }
    .mt-10 { margin-top: 10px; }
    .mt-15 { margin-top: 15px; }
    .w-100 { width: 100%; }
    .w-50 { width: 50%; }
    .inline { display: inline-block; vertical-align: top; }
    .nowrap { white-space: nowrap; }
    .wrap { white-space: normal; word-break: break-word; }
    hr { border: 0; border-top: 1px solid #000; margin: 10px 0; }
    table { width: 100%; border-collapse: collapse; }
    th, td { padding: 6px; vertical-align: top; }
    thead th { border-bottom: 1px solid #000; background: #f6f6f6; }
    tbody td { border-bottom: 1px solid #eee; }
    .totals-table td { padding: 4px 6px; }
    .tax-breakdown td { padding: 2px 6px; }
    .img { max-height: 50px; width: auto; float:left; margin-right:8px; }
    .row { width: 100%; }
    .col { display: inline-block; vertical-align: top; }
    .col-6 { width: 49%; }
    .border { border: 1px solid #000; }
    .grey { background: #f6f6f6; }
    .section-title { font-size: 13px; font-weight: 600; border-bottom: 1px solid #000; padding-bottom: 4px; margin-bottom: 6px; }
  </style>
</head>
<body>
<div class="container">

  {{-- Header / Business --}}
  <table class="w-100 mb-10">
    <tr>
      <td class="w-50">
        @if(empty($rd->letter_head) && !empty($rd->logo))
          <img src="{{ $rd->logo }}" style="max-height: 100px; width:auto;">
        @endif
        @if(!empty($rd->header_text))
          <div class="mt-5">{!! $rd->header_text !!}</div>
        @endif
        <div class="mt-5">
          @if(!empty($rd->display_name))<div class="fw-bold" style="font-size: 16px;">{{ $rd->display_name }}</div>@endif
          @if(!empty($rd->address))<div class="small">{!! $rd->address !!}</div>@endif
          @if(!empty($rd->contact))<div class="small">{!! $rd->contact !!}</div>@endif
          @if(!empty($rd->website))<div class="small">{{ $rd->website }}</div>@endif
          @if(!empty($rd->location_custom_fields))<div class="small">{!! $rd->location_custom_fields !!}</div>@endif
          @if(!empty($rd->tax_info1))<div class="small"><b>{{ $rd->tax_label1 }}</b> {{ $rd->tax_info1 }}</div>@endif
          @if(!empty($rd->tax_info2))<div class="small"><b>{{ $rd->tax_label2 }}</b> {{ $rd->tax_info2 }}</div>@endif
        </div>
      </td>
      <td class="w-50 text-right">
        @if(!empty($rd->letter_head))
          <img src="{{ $rd->letter_head }}" style="max-width: 100%;">
        @endif
        <div class="fw-bold" style="font-size: 18px;">{!! $rd->invoice_heading ?? __('sale.invoice') !!}</div>
        <div class="mt-5 small">
          @if(!empty($rd->invoice_no_prefix))<span class="nowrap">{!! $rd->invoice_no_prefix !!}</span> @endif
          <span class="nowrap">{{ $rd->invoice_no }}</span>
        </div>
        <div class="small">
          <span class="label">{{ $rd->date_label }}</span> <span class="val">{{ $rd->invoice_date }}</span>
          @if(!empty($rd->due_date_label))<br><span class="label">{{ $rd->due_date_label }}</span> <span class="val">{{ $rd->due_date ?? '' }}</span>@endif
        </div>
        @if(!empty($rd->order_type))
          <div class="small">{!! $rd->order_type !!}</div>
        @endif
        @if(!empty($rd->table_label) || !empty($rd->table))
          <div class="small"><b>{!! $rd->table_label !!}</b> {{ $rd->table }}</div>
        @endif
        @if(!empty($rd->service_staff_label) || !empty($rd->service_staff))
          <div class="small"><b>{!! $rd->service_staff_label !!}</b> {{ $rd->service_staff }}</div>
        @endif
      </td>
    </tr>
  </table>

  {{-- Customer / Meta --}}
  <table class="w-100 mb-10">
    <tr>
      <td class="w-50">
        @if(!empty($rd->customer_info))
          <div class="fw-bold small">{{ $rd->customer_label }}</div>
          <div class="small">{!! $rd->customer_info !!}</div>
        @endif
        @if(!empty($rd->client_id_label))
          <div class="small"><b>{{ $rd->client_id_label }}</b> {{ $rd->client_id }}</div>
        @endif
        @if(!empty($rd->customer_tax_label))
          <div class="small"><b>{{ $rd->customer_tax_label }}</b> {{ $rd->customer_tax_number }}</div>
        @endif
        @if(!empty($rd->customer_custom_fields))
          <div class="small">{!! $rd->customer_custom_fields !!}</div>
        @endif
        @if(!empty($rd->customer_rp_label))
          <div class="small"><b>{{ $rd->customer_rp_label }}</b> {{ $rd->customer_total_rp }}</div>
        @endif
      </td>
      <td class="w-50 text-right">
        @if(!empty($rd->sales_person_label))
          <div class="small"><b>{{ $rd->sales_person_label }}</b> {{ $rd->sales_person }}</div>
        @endif
        @if(!empty($rd->commission_agent_label))
          <div class="small"><b>{{ $rd->commission_agent_label }}</b> {{ $rd->commission_agent }}</div>
        @endif
        @if(!empty($rd->sale_orders_invoice_no))
          <div class="small"><b>@lang('restaurant.order_no')</b> {!! $rd->sale_orders_invoice_no !!}</div>
        @endif
        @if(!empty($rd->sale_orders_invoice_date))
          <div class="small"><b>@lang('lang_v1.order_dates')</b> {!! $rd->sale_orders_invoice_date !!}</div>
        @endif
      </td>
    </tr>
  </table>

  {{-- Items --}}
  <table class="w-100 mb-10">
    <thead>
      <tr>
        <th class="nowrap">#</th>
        <th class="nowrap">{{ $rd->table_product_label ?? __('product.product') }}</th>
        <th class="text-right nowrap">{{ $rd->table_qty_label ?? __('sale.qty') }}</th>
        @if(empty($rd->hide_price))
          <th class="text-right nowrap">Unit (Excl)</th>
          <th class="text-right nowrap">Unit (Incl)</th>
          <th class="text-right nowrap">Line Disc</th>
          <th class="text-right nowrap">Tax rate</th>
          <th class="text-right nowrap">Item tax</th>
          <th class="text-right nowrap">Subtotal (Excl)</th>
          <th class="text-right nowrap">Subtotal (Incl)</th>
        @endif
      </tr>
    </thead>
    <tbody>
      @foreach($rd->lines as $line)
        @php
          $unit_exc = $line['unit_price_exc_tax'] ?? $line['unit_price'] ?? $line['unit_price_before_discount'] ?? '';
          $unit_inc = $line['unit_price_inc_tax'] ?? '';
          $discount = $line['total_line_discount'] ?? '0.00';
          $qty = $line['quantity'] ?? '1';
          $tax_rate = $line['tax_rate'] ?? ($line['item_tax'] ?? 0);
          $item_tax_total = $line['total_item_tax'] ?? '';
          $line_total_incl = $line['line_total'] ?? '';
          $line_total_excl = $line['line_total_exc_tax'] ?? '';
        @endphp
        <tr>
          <td class="nowrap">{{ $loop->iteration }}</td>
          <td>
            @if (!empty($line['image']))
              <img src="{{ $line['image'] }}" class="img" alt="" onerror="this.onerror=null;this.remove();">
            @endif
            <div class="wrap">
              {{ $line['name'] }} {{ $line['product_variation'] ?? '' }} {{ $line['variation'] ?? '' }}
              @if(!empty($line['sub_sku'])), {{ $line['sub_sku'] }} @endif
              @if(!empty($line['brand'])), {{ $line['brand'] }} @endif
              @if(!empty($line['cat_code'])), {{ $line['cat_code'] }} @endif
              @if(!empty($line['product_custom_fields'])), {{ $line['product_custom_fields'] }} @endif
              @if(!empty($line['sell_line_note']))<br><span class="xs">{{ $line['sell_line_note'] }}</span>@endif
              @if(!empty($line['lot_number']))<br><span class="xs">{{ $line['lot_number_label'] }}: {{ $line['lot_number'] }}</span>@endif
              @if(!empty($line['product_expiry']))<span class="xs">, {{ $line['product_expiry_label'] }}: {{ $line['product_expiry'] }}</span>@endif
              @if(!empty($line['warranty_name']))<br><span class="xs">{{ $line['warranty_name'] }}</span>@endif
              @if(!empty($line['warranty_exp_date']))<span class="xs"> - {{ @format_date($line['warranty_exp_date']) }}</span>@endif
              @if(!empty($line['warranty_description']))<span class="xs"> - {{ $line['warranty_description'] }}</span>@endif
            </div>

            {{-- Group tax details under product --}}
            @if (!empty($line['group_tax_details']))
              <div class="xs mt-5">
                @foreach($line['group_tax_details'] as $gt)
                  <div class="muted">{{ $gt['name'] }}: {{ @num_format($gt['calculated_tax'] ?? ($gt['amount'] ?? 0)) }}</div>
                @endforeach
              </div>
            @endif

            {{-- Combo children --}}
            @if (!empty($line['combo_variations']))
              <div class="xs mt-5">
                @foreach ($line['combo_variations'] as $combo)
                  <div>- {{ $combo['name'] }} {{ $combo['variation'] ?? '' }} @if(!empty($combo['sub_sku']))({{ $combo['sub_sku'] }})@endif x {{ $combo['quantity'] }}</div>
                  @if(!empty($combo['combo_modifiers']))
                    <div class="xs" style="margin-left:10px;">
                      @foreach($combo['combo_modifiers'] as $cm)
                        <div>• {{ $cm['combo_modifier_name'] }} @if(!empty($cm['combo_modifier_price'])) - {{ $cm['combo_modifier_price'] }} @endif</div>
                      @endforeach
                    </div>
                  @endif
                @endforeach
              </div>
            @endif

            {{-- Line modifiers (plain) --}}
            @if (!empty($line['modifiers']))
              <div class="xs mt-5">
                @foreach($line['modifiers'] as $m)
                  <div>• {{ $m['name'] }} {{ $m['variation'] ?? '' }} @if(!empty($m['sub_sku']))({{ $m['sub_sku'] }})@endif
                    @if(!empty($m['sell_line_note'])) - {{ $m['sell_line_note'] }} @endif
                    @if(!empty($m['unit_price_inc_tax'])) - {{ $m['unit_price_inc_tax'] }} @endif
                  </div>
                @endforeach
              </div>
            @endif
          </td>
          <td class="text-right nowrap">{{ $qty }} {{ $line['units'] ?? '' }}</td>
          @if(empty($rd->hide_price))
            <td class="text-right nowrap">{{ $unit_exc }}</td>
            <td class="text-right nowrap">{{ $unit_inc }}</td>
            <td class="text-right nowrap">{{ $discount }}</td>
            <td class="text-right nowrap">{{ is_numeric($tax_rate) ? @num_format($tax_rate) : $tax_rate }}</td>
            <td class="text-right nowrap">{{ $item_tax_total }}</td>
            <td class="text-right nowrap">{{ $line_total_excl }}</td>
            <td class="text-right nowrap">{{ $line_total_incl }}</td>
          @endif
        </tr>
      @endforeach
    </tbody>
  </table>

  {{-- Totals --}}
  <table class="totals-table w-100">
    @if(!empty($rd->total_quantity_label))
      <tr>
        <td class="text-right w-50">{!! $rd->total_quantity_label !!}</td>
        <td class="text-right w-50">{{ $rd->total_quantity }}</td>
      </tr>
    @endif

    @if(empty($rd->hide_price))
      @if(!empty($rd->subtotal_exc_tax))
        <tr><td class="text-right">Subtotal (Excl)</td><td class="text-right">{{ $rd->subtotal_exc_tax }}</td></tr>
      @endif
      @if(!empty($rd->subtotal_label))
        <tr><td class="text-right">{!! $rd->subtotal_label !!} (Incl)</td><td class="text-right">{{ $rd->subtotal }}</td></tr>
      @endif

      @if(!empty($rd->item_discount_label))
        <tr><td class="text-right">{{ $rd->item_discount_label }}</td><td class="text-right">{{ $rd->item_discount ?? '0.00' }}</td></tr>
      @endif

      @if(!empty($rd->discount))
        <tr><td class="text-right">{{ $rd->discount_label ?? __('lang_v1.discount') }}</td><td class="text-right">{{ $rd->discount }}</td></tr>
      @endif

      @if(!empty($rd->additional_expenses) && is_array($rd->additional_expenses))
        @foreach($rd->additional_expenses as $k=>$v)
          <tr><td class="text-right">{{ $k }}</td><td class="text-right">{{ $v }}</td></tr>
        @endforeach
      @endif

      @if(!empty($rd->reward_point_label))
        <tr><td class="text-right">{!! $rd->reward_point_label !!}</td><td class="text-right">{{ $rd->reward_point_amount }}</td></tr>
      @endif

      @if(!empty($rd->shipping_charges))
        <tr><td class="text-right">{!! $rd->shipping_charges_label !!}</td><td class="text-right">{{ $rd->shipping_charges }}</td></tr>
      @endif

      @if(!empty($rd->packing_charge))
        <tr><td class="text-right">{!! $rd->packing_charge_label !!}</td><td class="text-right">{{ $rd->packing_charge }}</td></tr>
      @endif

      {{-- Group tax breakdown or flat tax --}}
      @if(!empty($rd->taxes) && is_array($rd->taxes))
        <tr><td class="text-right" colspan="2"><b>Tax Breakdown</b></td></tr>
        @foreach($rd->taxes as $tax_name => $tax_amount)
          <tr class="tax-breakdown"><td class="text-right">{{ $tax_name }}</td><td class="text-right">{{ $tax_amount }}</td></tr>
        @endforeach
      @elseif(!empty($rd->tax_label) && !empty($rd->tax))
        <tr><td class="text-right">{!! $rd->tax_label !!}</td><td class="text-right">{{ $rd->tax }}</td></tr>
      @endif

      @if(!empty($rd->round_off))
        <tr><td class="text-right">{!! $rd->round_off_label ?? __('lang_v1.round_off') !!}</td><td class="text-right">{{ $rd->round_off }}</td></tr>
      @endif

      <tr>
        <td class="text-right fw-bold">{!! $rd->total_label ?? __('sale.total') !!}</td>
        <td class="text-right fw-bold">{{ $rd->total }}</td>
      </tr>

      @if(!empty($rd->total_in_words))
        <tr><td class="text-right" colspan="2"><small>({{ $rd->total_in_words }})</small></td></tr>
      @endif

      {{-- Payments --}}
      @if (!empty($rd->payments))
        <tr><td class="text-right" colspan="2"><b>{{ __('lang_v1.payments') }}</b></td></tr>
        @foreach ($rd->payments as $payment)
          <tr>
            <td class="text-right">{{ $payment['method'] }} @if(!empty($payment['date'])) ({{ $payment['date'] }}) @endif</td>
            <td class="text-right">{{ $payment['amount'] }}</td>
          </tr>
        @endforeach
      @endif

      @if(!empty($rd->total_paid))
        <tr><td class="text-right">{!! $rd->total_paid_label ?? __('lang_v1.total_paid') !!}</td><td class="text-right">{{ $rd->total_paid }}</td></tr>
      @endif
      @if(!empty($rd->total_due))
        <tr><td class="text-right">{!! $rd->total_due_label ?? __('lang_v1.total_due') !!}</td><td class="text-right">{{ $rd->total_due }}</td></tr>
      @endif
      @if(!empty($rd->change_due))
        <tr><td class="text-right">{!! $rd->change_due_label ?? __('lang_v1.change_return') !!}</td><td class="text-right">{{ $rd->change_due }}</td></tr>
      @endif
    @endif
  </table>

  {{-- Notes / Footer / Codes --}}
  @if(!empty($rd->additional_notes))
    <div class="mt-10 small"><b>{!! $rd->order_notes_label ?? __('lang_v1.notes') !!}:</b> {!! nl2br($rd->additional_notes) !!}</div>
  @endif

  <div class="mt-10">
    <div class="inline w-50">
      @if(!empty($rd->barcode))
        <img src="data:image/png;base64,{{ $rd->barcode }}" style="max-height:80px;">
      @endif
    </div>
    <div class="inline w-50 text-right">
      @if(isset($rd->is_connected) && $rd->is_connected && $rd->show_qr_code && !empty($rd->qr_code_text))
        {!! $rd->qr_code_text !!}
      @elseif(!empty($rd->qr_code))
        <img src="{!! $rd->qr_code !!}" style="max-height:80px;">
      @endif
    </div>
  </div>

  @if(!empty($rd->footer_text))
    <div class="mt-10 small">{!! $rd->footer_text !!}</div>
  @endif
</div>
</body>
</html>
