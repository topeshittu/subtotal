<style>
	:root {
		--sidebar-width: 800px;
		--border-color: #dee2e6;
		--text-dark: #2c3e50;
		--text-muted: #6c757d;
		--bg-light: #f8f9fa;
		--success-color: #28a745;
		--warning-color: #ffc107;
		--danger-color: #dc3545;
	}

	.running-orders-sidebar {
		position: fixed;
		top: 0;
		right: calc(-1 * var(--sidebar-width));
		width: var(--sidebar-width);
		height: 100vh;
		background: white;
		box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
		z-index: 9999;
		transition: right 0.3s ease;
		display: flex;
		flex-direction: column;
	}
	 html.dark-mode .running-orders-sidebar{
		background-color: #25212f !important;
	 }

	.running-orders-sidebar.open {
		right: 0;
	}

	.sidebar-header {
		background: white;
		padding: 20px;
		display: flex;
		justify-content: space-between;
		align-items: center;
		border-bottom: 1px solid var(--border-color);
		flex-shrink: 0;
	}
	 html.dark-mode .sidebar-header{
		background: #3f405b !important;
	 }

	.sidebar-header h4 {
		margin: 0;
		font-size: 16px;
		color: var(--text-dark);
	}

	.view-switcher {
		display: flex;
		gap: 4px;
		margin: 0 10px;
	}

	.view-btn {
		border: 1px solid var(--border-color);
		color: var(--text-muted);
		padding: 8px 12px;
		border-radius: 6px;
		cursor: pointer;
		font-size: 12px;
		transition: all 0.2s;
	}

	

	.view-btn.active {
		background: var(--primary-color);
		color: white;
		border-color: var(--primary-color);
	}

	.sidebar-close {
		border: 1px solid var(--border-color);
		color: var(--text-muted);
		padding: 8px;
		border-radius: 6px;
		cursor: pointer;
		transition: all 0.2s;
	}

	.sidebar-tabs {
		background: var(--bg-light);
		border-bottom: 1px solid var(--border-color);
		padding: 0;
		margin: 0;
		display: flex;
		list-style: none;
		flex-shrink: 0;
	}
	 html.dark-mode .sidebar-tabs{
		background: #3f405b !important;
	 }

	.sidebar-tabs li {
		flex: 1;
	}

	.sidebar-tabs li a {
		display: block;
		padding: 12px 8px;
		text-align: center;
		text-decoration: none;
		color: #666;
		font-size: 12px;
		font-weight: 500;
		border-right: 1px solid var(--border-color);
		transition: all 0.2s;
	}

	.sidebar-tabs li:last-child a {
		border-right: none;
	}

	.sidebar-tabs li.active a {
		background: var(--primary-color);
		color: white;
	}

	.sidebar-tabs li a:hover:not(.active) {
		background: #e9ecef;
	}

	.sidebar-content {
		flex: 1;
		overflow-y: auto;
		overflow-x: hidden;
	}

	.tab-content {
		height: 100%;
	}

	.tab-pane {
		min-height: 100%;
	}

	.sidebar-search {
		padding: 10px;
		border-bottom: 1px solid var(--border-color);
	}

	.sidebar-search input {
		width: 100%;
		padding: 8px 12px;
		border: 1px solid var(--border-color);
		border-radius: 6px;
		font-size: 14px;
	}

	.orders-container {
		padding: 10px;
		display: flex;
		flex-direction: column;
		gap: 10px;
	}

	.order-card {
		border: 1px solid #e1e5e9;
		border-radius: 8px;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
		transition: all 0.2s;
	}

	.order-card:hover {
		box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
	}

	.card-header {
		border-bottom: 1px solid #e9ecef;
		padding: 12px 16px;
		display: flex;
		justify-content: space-between;
		align-items: center;
	}

	.card-body {
		padding: 16px;
	}

	.card-footer {
		border-top: 1px solid #e9ecef;
		padding: 12px 16px;
		text-align: center;
	}

	.order-info h6 {
		margin: 0 0 8px 0;
		font-weight: 600;
		font-size: 16px;
		color: var(--text-dark);
	}

	.order-info p {
		margin: 0 0 6px 0;
		color: var(--text-muted);
		font-size: 14px;
		display: flex;
		align-items: center;
	}

	.order-info p i {
		margin-right: 8px;
		width: 16px;
		text-align: center;
	}

	.order-total {
		font-size: 18px;
		font-weight: 700;
		color: var(--text-dark);
		margin: 8px 0;
	}

	.status-badge {
		display: inline-block;
		padding: 6px 12px;
		border-radius: 20px;
		font-size: 11px;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.5px;
	}

	.status-received {
		background: #e3f2fd;
		color: #1565c0;
		border: 1px solid #bbdefb;
	}

	.status-cooked {
		background: #fff3e0;
		color: #ef6c00;
		border: 1px solid #ffcc02;
	}

	.status-served {
		background: #e8f5e8;
		color: #2e7d32;
		border: 1px solid #c8e6c9;
	}

	.status-partial_cooked {
		background: #fff8e1;
		color: #f57c00;
		border: 1px solid #ffecb3;
	}

	.status-partial_served {
		background: #f3e5f5;
		color: #7b1fa2;
		border: 1px solid #e1bee7;
	}

	.elapsed-time {
		padding: 5px 10px;
		border-radius: 5px;
		color: white;
		display: inline-block;
		font-size: 11px;
		font-weight: 600;
	}

	.elapsed-time.green {
		background-color: var(--success-color);
	}

	.elapsed-time.yellow {
		background-color: var(--warning-color);
		color: black;
	}

	.elapsed-time.orange {
		background-color: #ff9800;
	}

	.elapsed-time.red {
		background-color: var(--danger-color);
	}

	.notes-section {
		border-radius: 8px;
		padding: 12px;
		margin-top: 12px;
		display: none;
	}

	.notes-section.show {
		display: block;
	}

	.notes-toggle {
		border: none;
		color: var(--text-muted);
		cursor: pointer;
		padding: 4px;
		border-radius: 4px;
		transition: all 0.2s;
	}

	.notes-toggle:hover {
		background: #e9ecef;
	}

	.dropdown-menu {
		min-width: 160px;
		position: absolute;
		z-index: 10000;
		box-shadow: 0 6px 12px rgba(0, 0, 0, 0.175);
	}

	.dropdown-menu li a {
		padding: 8px 16px;
		display: flex;
		align-items: center;
		text-decoration: none;
		color: #333;
		transition: all 0.2s;
	}

	.dropdown-menu li a:hover {
		background: var(--bg-light);
		color: var(--primary-color);
	}

	.dropdown-menu li a i {
		margin-right: 8px;
		width: 16px;
	}

	.no-results {
		text-align: center;
		padding: 40px 20px;
		color: #999;
		font-style: italic;
	}

	.orders-container.list-view {
		gap: 6px;
	}

	.orders-container.list-view .order-card {
		padding: 12px 16px;
	}

	.orders-container.list-view .card-header,
	.orders-container.list-view .card-body,
	.orders-container.list-view .card-footer {
		border: none;
		padding: 0;
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.orders-container.list-view .order-info {
		display: flex;
		align-items: center;
		gap: 16px;
		margin: 0;
	}

	.orders-container.list-view .order-info h6,
	.orders-container.list-view .order-info p {
		margin: 0;
		font-size: 12px;
	}

	.orders-container.list-view .order-info h6 {
		font-size: 14px;
		min-width: 80px;
	}

	.orders-container.list-view .order-total {
		font-size: 14px;
		margin: 0;
	}

	.orders-container.list-view .notes-toggle,
	.orders-container.list-view .notes-section {
		display: none;
	}

	.orders-container.grid-view {
		display: grid;
		grid-template-columns: repeat(2, 1fr);
		gap: 8px;
	}

	.orders-container.grid-view .order-card {
		font-size: 12px;
	}

	.orders-container.grid-view .card-header,
	.orders-container.grid-view .card-body,
	.orders-container.grid-view .card-footer {
		padding: 8px 10px;
	}

	@media (max-width: 1200px) {
		:root {
			--sidebar-width: 600px;
		}
	}

	@media (max-width: 1024px) {
		:root {
			--sidebar-width: 450px;
		}
	}

	@media (max-width: 768px) {
		:root {
			--sidebar-width: 100%;
		}

		.orders-container.grid-view {
			grid-template-columns: 1fr;
		}
	}
</style>

<div class="running-orders-sidebar no-print" id="running-orders-sidebar">
	<div class="sidebar-header">
		<h4>@lang('lang_v1.running_orders')</h4>
		<div class="view-switcher">
			<button type="button" class="view-btn active" data-view="card" title="Card View">
				<i class="fas fa-th-large"></i>
			</button>
			<button type="button" class="view-btn" data-view="list" title="List View">
				<i class="fas fa-list"></i>
			</button>
			<button type="button" class="view-btn" data-view="grid" title="Grid View">
				<i class="fas fa-th"></i>
			</button>
		</div>
		<button type="button" class="sidebar-close" onclick="toggleRunningOrdersSidebar()">
			<i class="fas fa-times"></i>
		</button>
	</div>

	<ul class="sidebar-tabs nav nav-tabs">
		<li class="active"><a href="#new" data-toggle="tab">@lang('lang_v1.new') (<span id="new-count">0</span>)</a></li>
		<li><a href="#delayed" data-toggle="tab">@lang('lang_v1.delayed') (<span id="delayed-count">0</span>)</a></li>
		<li><a href="#old" data-toggle="tab">@lang('lang_v1.pending') (<span id="old-count">0</span>)</a></li>
		<li><a href="#search" data-toggle="tab">@lang('lang_v1.search')</a></li>
	</ul>

	<div class="sidebar-content">
		<div class="tab-content">
			<div id="new" class="tab-pane fade in active">
				<div class="orders-container" id="new-orders"></div>
			</div>
			<div id="delayed" class="tab-pane fade">
				<div class="orders-container" id="delayed-orders"></div>
			</div>
			<div id="old" class="tab-pane fade">
				<div class="orders-container" id="old-orders"></div>
			</div>
			<div id="search" class="tab-pane fade">
				<div class="sidebar-search">
					{!! Form::text('search_orders', null, ['id' => 'search-input', 'placeholder' => __('lang_v1.search'), 'class' => 'form-control']) !!}
				</div>
				<div class="orders-container" id="search-orders"></div>
			</div>
		</div>
	</div>

	<div style="display: none;" id="cards-container">
		@php
		$subtype = !empty($transaction_sub_type) ? '?sub_type='.$transaction_sub_type : '';
		@endphp
		@forelse($sales ?? [] as $sale)
		@php
		$count_sell_line = count($sale->sell_lines);
		$count_cooked = count($sale->sell_lines->where('res_line_order_status', 'cooked'));
		$count_served = count($sale->sell_lines->where('res_line_order_status', 'served'));
		$order_status = 'received';
		if($count_cooked == $count_sell_line) {
		$order_status = 'cooked';
		} else if($count_served == $count_sell_line) {
		$order_status = 'served';
		} else if ($count_served > 0 && $count_served < $count_sell_line) {
			$order_status = 'partial_served';
			} else if ($count_cooked > 0 && $count_cooked < $count_sell_line) {
				$order_status = 'partial_cooked';
				}
				@endphp
				@if($sale->is_running_order)
				<div class="order-card">
					<div class="card-header">
						<div class="d-flex align-items-center">
							<span class="status-badge status-{{$order_status}}">
								{{ __('restaurant.order_statuses.' . $order_status) }}
							</span>
						</div>
						<div class="btn-group">
							<button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
								data-toggle="dropdown" aria-expanded="false">
								<i class="fas fa-ellipsis-v"></i>
							</button>
							<ul class="dropdown-menu dropdown-menu-right" role="menu">
								@if(auth()->user()->can('sell.update') || auth()->user()->can('direct_sell.update'))
								<li><a href="{{action([\App\Http\Controllers\SellPosController::class, 'edit'], ['po' => $sale->id]).$subtype}}">
										<i class="fas fa-edit"></i> @lang('lang_v1.edit_order')
									</a></li>
								@endif
								@if(auth()->user()->can('sell.delete') || auth()->user()->can('direct_sell.delete'))
								<li><a href="{{action([\App\Http\Controllers\SellPosController::class, 'destroy'], ['po' => $sale->id])}}">
										<i class="fas fa-trash"></i> @lang('messages.delete')
									</a></li>
								@endif
								@if(!auth()->user()->can('sell.update') && auth()->user()->can('edit_pos_payment'))
								<li><a href="{{route('edit-pos-payment', ['po' => $sale->id])}}">
										<i class="fas fa-credit-card"></i> @lang('lang_v1.add_edit_payment')
									</a></li>
								@endif
								<li><a href="{{action([\App\Http\Controllers\SellPosController::class, 'printInvoice'], [$sale->id])}}?prebill=1" class="print-invoice-link">
										<i class="fas fa-print"></i> @lang('lang_v1.print_prebill')
									</a></li>
								<li><a href="{{action([\App\Http\Controllers\SellPosController::class, 'printInvoice'], [$sale->id])}}" class="print-invoice-link">
										<i class="fas fa-receipt"></i> @lang('lang_v1.reprint_kot')
									</a></li>
								<li><a href="#" onclick="printToKitchenStations({{ $sale->id }})" class="kitchen-print-link">
										<i class="fas fa-print"></i> Print to Kitchen Stations
									</a></li>
							</ul>
						</div>
					</div>

					<div class="card-body">
						<div class="order-info">
							<h6>{{$sale->invoice_no}}</h6>
							<p><i class="fas fa-calendar"></i>{{@format_date($sale->transaction_date)}}</p>
							<p><i class="fas fa-user"></i>{{$sale->name}}</p>
							<p><i class="fas fa-cubes"></i>{{count($sale->sell_lines)}} @lang('lang_v1.items')</p>
							@if($is_tables_enabled && !empty($sale->table->name))
							<p><i class="fas fa-table"></i>@lang('restaurant.table'): {{$sale->table->name}}</p>
							@endif
							@if($is_service_staff_enabled && !empty($sale->service_staff))
							<p><i class="fas fa-user-tie"></i>{{$sale->service_staff->user_full_name}}</p>
							@endif
						</div>

						@if(!empty($sale->additional_notes))
						<button type="button" class="notes-toggle" onclick="toggleNotes(this)">
							<i class="fas fa-sticky-note"></i> View Notes
						</button>
						<div class="notes-section">
							<strong>@lang('lang_v1.order_notes_label'):</strong>
							<p>{{$sale->additional_notes}}</p>
						</div>
						@endif

						<div class="order-total">
							<span class="display_currency" data-currency_symbol=true>{{$sale->final_total}}</span>
						</div>
					</div>

					<div class="card-footer">
                        <input type="hidden" id="server-time" value="{{ \Carbon\Carbon::now(session('business.time_zone', config('app.timezone')))->toIso8601String() }}">
						<span class="elapsed-time" data-start-time="{{ \Carbon\Carbon::parse($sale->transaction_date)->timezone(session('business.time_zone', config('app.timezone')))->toIso8601String() }}"></span>
					</div>
				</div>
				@endif
				@empty
				<p class="text-center">@lang('purchase.no_records_found')</p>
				@endforelse
	</div>
</div>

<script src="{{ asset('js/running_orders.js') }}"></script>
