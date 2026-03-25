<div class="card-wrapper" style="margin-bottom:30px;">

    <div class="overview-filter">
        <div class="title">
            <h2> @lang('sale.payments')</h2>
        </div>
        <div class="filter">
            <div class="form-box">
                <select id="contact_payments_per_page" class="form-control" style="width:auto; display:inline-block;">
                    @php $pp = request()->get('per_page', $payments->perPage()); @endphp
                    <option value="10" {{ (string)$pp==='10' ? 'selected' : '' }}>10</option>
                    <option value="25" {{ (string)$pp==='25' ? 'selected' : '' }}>25</option>
                    <option value="50" {{ (string)$pp==='50' ? 'selected' : '' }}>50</option>
                    <option value="100" {{ (string)$pp==='100' ? 'selected' : '' }}>100</option>
                    <option value="all" {{ (string)$pp==='all' ? 'selected' : '' }}>@lang('lang_v1.all')</option>
                </select>
            </div>
        </div>
    </div>
    <table class="table table-bordered max-table" id="contact_payments_table">
        <thead>
            <tr>
                <th>@lang('lang_v1.paid_on')</th>
                <th>@lang('purchase.ref_no')</th>
                <th>@lang('sale.amount')</th>
                <th>@lang('lang_v1.payment_method')</th>
                <th>@lang('account.payment_for')</th>
                <th>@lang('messages.action')</th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            @php
            $count_child_payments = count($payment->child_payments);
            @endphp
            @include('contact.partials.payment_row', compact('payment', 'count_child_payments', 'payment_types'))

            @if($count_child_payments > 0)
            @foreach($payment->child_payments as $child_payment)
            @include('contact.partials.payment_row', ['payment' => $child_payment, 'count_child_payments' => 0, 'payment_types' => $payment_types, 'parent_payment_ref_no' => $payment->payment_ref_no])
            @endforeach
            @endif
            @empty
            <tr>
                <td colspan="6" class="text-center">@lang('purchase.no_records_found')</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="text-right" style="width: 100%;" id="contact_payments_pagination">{{ $payments->appends(request()->query())->links() }}</div>
