
document.addEventListener('DOMContentLoaded', function () {
	var go_to_top_btn = document.getElementById('goToTopBtn');

	if (go_to_top_btn) {
		window.addEventListener('scroll', function () {
			if (window.pageYOffset > 300) {
				go_to_top_btn.classList.add('show');
			} else {
				go_to_top_btn.classList.remove('show');
			}
		});
	}
});
function scroll_to_top() {
	window.scrollTo({
		top: 0,
		behavior: 'smooth'
	});
}

function toggle_mobile_stats() {
	var dropdown = document.getElementById('mobile-stats-dropdown');
	var btn = document.getElementById('mobile-stats-btn');

	if (dropdown && btn) {
		dropdown.classList.toggle('show');
		btn.classList.toggle('active');
	}
}

document.addEventListener('click', function (e) {
	var dropdown = document.getElementById('mobile-stats-dropdown');
	var btn = document.getElementById('mobile-stats-btn');

	if (dropdown && btn && dropdown.classList.contains('show')) {
		if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
			dropdown.classList.remove('show');
			btn.classList.remove('active');
		}
	}
});

function update_mobile_stat_counts() {
	var desktop_counts = {
		'all': (document.getElementById('all-count') && document.getElementById('all-count').textContent) || '0',
		'pending': (document.getElementById('pending-count') && document.getElementById('pending-count').textContent) || '0',
		'preparing': (document.getElementById('preparing-count') && document.getElementById('preparing-count').textContent) || '0',
		'ready': (document.getElementById('ready-count') && document.getElementById('ready-count').textContent) || '0',
		'served': (document.getElementById('served-count') && document.getElementById('served-count').textContent) || '0'
	};

	var status_keys = Object.keys(desktop_counts);
	for (var i = 0; i < status_keys.length; i++) {
		var status = status_keys[i];
		var mobile_element = document.getElementById('mobile-' + status + '-count');
		if (mobile_element) {
			mobile_element.textContent = desktop_counts[status];
		}
	}
}
function update_mobile_bell_toggle_ui() {
	var mobile_bell_toggle = document.getElementById('mobile-bell-toggle');
	var mobile_bell_icon = document.getElementById('mobile-bell-icon');
	var mobile_bell_status = document.getElementById('mobile-bell-status');

	if (mobile_bell_toggle && mobile_bell_icon && mobile_bell_status) {
		if (typeof isBellEnabled !== 'undefined' && isBellEnabled) {
			mobile_bell_toggle.classList.remove('bell-disabled');
			mobile_bell_icon.className = 'fas fa-bell';
			mobile_bell_status.textContent = 'ON';
		} else {
			mobile_bell_toggle.classList.add('bell-disabled');
			mobile_bell_icon.className = 'fas fa-bell-slash';
			mobile_bell_status.textContent = 'OFF';
		}
	}
}

document.addEventListener('DOMContentLoaded', function () {
	update_mobile_stat_counts();
	update_mobile_bell_toggle_ui();
	var observer = new MutationObserver(function (mutations) {
		for (var i = 0; i < mutations.length; i++) {
			var mutation = mutations[i];
			if (mutation.type === 'childList' || mutation.type === 'characterData') {
				update_mobile_stat_counts();
			}
		}
	});
	var count_ids = ['all-count', 'pending-count', 'preparing-count', 'ready-count', 'served-count'];
	for (var i = 0; i < count_ids.length; i++) {
		var element = document.getElementById(count_ids[i]);
		if (element) {
			observer.observe(element, { childList: true, characterData: true, subtree: true });
		}
	}
});

var is_bell_enabled = localStorage.getItem('kitchen_bell_enabled') !== 'false';
var bell_audio = null;

function toggle_bell_sound() {
	is_bell_enabled = !is_bell_enabled;
	localStorage.setItem('kitchen_bell_enabled', is_bell_enabled);
	update_bell_toggle_ui();
	if (is_bell_enabled && !bell_audio) {
		initialize_bell_audio();
	}
	var message = is_bell_enabled ? 'Bell notifications enabled' : 'Bell notifications disabled';
	show_notification(message, is_bell_enabled ? 'success' : 'warning');
}

function test_bell() {
	if (!is_bell_enabled) {
		toggle_bell_sound();
		return;
	}
	if (!bell_audio) {
		initialize_bell_audio();
		setTimeout(test_bell, 500);
		return;
	}
	play_bell_notification();
}

function setup_audio_unlock_listeners() {
	var events = ['click', 'touchstart', 'keydown'];
	var audio_unlocked = false;

	function unlock_handler() {
		if (!audio_unlocked && bell_audio) {
			try {
				var play_promise = bell_audio.play();
				if (play_promise !== undefined) {
					play_promise.then(
						function () {
							bell_audio.pause();
							bell_audio.currentTime = 0;
							audio_unlocked = true;
							for (var i = 0; i < events.length; i++) {
								document.removeEventListener(events[i], unlock_handler, true);
							}
						}).catch(function (error) { });
				}
			} catch (error) {
				//
			}
		}
	}
	for (var i = 0; i < events.length; i++) {
		document.addEventListener(events[i], unlock_handler, true);
	}
}

function update_bell_toggle_ui() {
	var bell_toggle = document.getElementById('bell-toggle');
	var bell_icon = document.getElementById('bell-icon');
	var bell_status = document.getElementById('bell-status');
	if (is_bell_enabled) {
		bell_toggle.classList.remove('bell-disabled');
		bell_icon.className = 'fas fa-bell';
		if (bell_status) bell_status.textContent = 'ON';
		bell_toggle.setAttribute('title', 'Disable order notification sound');
	} else {
		bell_toggle.classList.add('bell-disabled');
		bell_icon.className = 'fas fa-bell-slash';
		if (bell_status) bell_status.textContent = 'OFF';
		bell_toggle.setAttribute('title', 'Enable order notification sound');
	}
	if (typeof update_mobile_bell_toggle_ui === 'function') {
		update_mobile_bell_toggle_ui();
	}
}

function play_bell_notification() {
	if (!is_bell_enabled) {
		return;
	}
	if (!bell_audio) {
		initialize_bell_audio();
	}
	if (bell_audio) {
		try {
			bell_audio.currentTime = 0;
			var play_promise = bell_audio.play();
			if (play_promise !== undefined) {
				play_promise.then(
					function () { }).catch(function (error) {
						//
						show_notification('🔔 New order received!', 'info');
					});
			}
		} catch (error) {
			//
			show_notification('🔔 New order received!', 'info');
		}
	} else {
		show_notification('🔔 New order received!', 'info');
	}
}

function show_notification(message, type) {
	if (typeof type === 'undefined') type = 'info';
	var notification = document.createElement('div');
	notification.className = 'notification notification-' + type;
	notification.textContent = message;
	notification.style.cssText = 'position: fixed; top: 20px; right: 20px; background: ' +
		(type === 'success' ? '#10b981' : type === 'warning' ? '#f59e0b' : '#3b82f6') +
		'; color: white; padding: 12px 20px; border-radius: 8px; z-index: 10000; font-weight: 500; box-shadow: 0 4px 12px rgba(0,0,0,0.15); transition: all 0.3s ease;';
	document.body.appendChild(notification);
	setTimeout(function () {
		notification.style.opacity = '0';
		notification.style.transform = 'translateX(100%)';
		setTimeout(function () { notification.remove(); }, 300);
	}, 3000);
}
var event_source = null;
var is_connected = false;
document.addEventListener('DOMContentLoaded', function () {
	update_bell_toggle_ui();
	if (is_bell_enabled) {
		initialize_bell_audio();
	}
	setup_audio_unlock_listeners();
	initialize_timers();
	setInterval(update_timers, 30000);
	initialize_feature_toggles();
	initialize_tooltips();
	initialize_sse_connection();
	initialize_ajax_forms();
});

function initialize_sse_connection() {
	try {
		var sse_url = STATION_CONFIG.sse_url + '?station_id=' + STATION_CONFIG.station_id;
		event_source = new EventSource(sse_url);
		event_source.addEventListener('connected', function (e) {
			var data = JSON.parse(e.data);
			is_connected = true;
			update_connection_status('online', 'Connected - Real-time updates active');
		});
		event_source.addEventListener('kitchen-update', function (e) {
			var data = JSON.parse(e.data);
			handle_real_time_update(data, 'sse');
		});
		event_source.addEventListener('heartbeat', function (e) { });
		event_source.onerror = function (error) {
			is_connected = false;
			if (event_source.readyState === EventSource.CONNECTING) {
				update_connection_status('updating', 'Reconnecting...');
			} else {
				update_connection_status('offline', 'Connection lost - Attempting to reconnect');
				//
				setTimeout(function () {
					if (!is_connected) {
						initialize_sse_connection();
					}
				}, 5000);
			}
		};
		window.addEventListener('beforeunload', function () {
			if (event_source) {
				event_source.close();
			}
		});
	} catch (error) {
		//
		update_connection_status('offline', 'SSE initialization failed');
		start_fallback_polling();
	}
}

function handle_real_time_update(event_data, source) {
	if (event_data.type === 'new_order' || event_data.type === 'order_updated') {
		show_update_notification('New kitchen order received!');
		if (event_data.type === 'new_order') {
			play_bell_notification();
		}
		if (event_data.statistics) {
			update_order_counts(event_data.statistics);
		}
		if (event_data.orders_html) {
			update_orders_html(event_data.orders_html);
		}
		update_last_update_display(event_data.timestamp);
		if (event_data.type === 'new_order') {
			check_for_new_orders_auto_print();
		}
	}
}

function update_orders_html(new_html) {
	var orders_container = document.getElementById('station-orders-container');
	if (orders_container && new_html) {
		orders_container.style.opacity = '0.7';
		setTimeout(function () {
			orders_container.innerHTML = new_html;
			initialize_timers();
			initialize_tooltips();
			initialize_ajax_forms();
			orders_container.style.opacity = '1';
			highlight_new_orders();
		}, 200);
	}
}

function refresh_station_data() {
	fetch(STATION_CONFIG.check_update_url, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content')) || ''
		},
		body: JSON.stringify({
			station_id: STATION_CONFIG.station_id,
			last_check: 0
		})
	}).then(function (response) { return response.json(); }).then(function (data) {
		if (data.has_updates || data.data) {
			update_station_ui(data.data || data);
		}
	}).catch(function (error) {
		//
	});
}

function start_fallback_polling() {
	update_connection_status('updating', 'Using fallback polling - WebSocket unavailable');
	setInterval(refresh_station_data, 30000);
}

function update_station_ui(data) {
	if (data.statistics) {
		update_order_counts(data.statistics);
	}
	if (data.orders_html) {
		var orders_container = document.getElementById('station-orders-container');
		if (orders_container) {
			orders_container.style.opacity = '0.7';
			setTimeout(function () {
				orders_container.innerHTML = data.orders_html;
				initialize_timers();
				initialize_tooltips();
				initialize_ajax_forms();
				orders_container.style.opacity = '1';
				highlight_new_orders();
			}, 200);
		}
	}
}

function update_order_counts(statistics) {
	var counts = {
		'pending-count': statistics.pending || 0,
		'preparing-count': statistics.preparing || 0,
		'ready-count': statistics.ready || 0,
		'completed-count': statistics.completed || 0
	};
	var count_keys = Object.keys(counts);
	for (var i = 0; i < count_keys.length; i++) {
		var count_id = count_keys[i];
		var element = document.getElementById(count_id);
		if (element) {
			element.textContent = counts[count_id];
			element.classList.add('updated');
			setTimeout(function (el) { return function () { el.classList.remove('updated'); }; }(element), 1000);
		}
	}
}

function highlight_new_orders() {
	var new_orders = document.querySelectorAll('.kds-order-card.new-order');
	for (var i = 0; i < new_orders.length; i++) {
		var order = new_orders[i];
		order.classList.add('highlight-update');
		setTimeout(function (o) { return function () { o.classList.remove('highlight-update'); }; }(order), 2000);
	}
}

function show_update_notification(message) {
	var notification = document.createElement('div');
	notification.className = 'update-notification';
	notification.innerHTML = '<div class="notification-content"><i class="fas fa-bell"></i><span>' + message + '</span></div>';
	document.body.appendChild(notification);
	setTimeout(function () { notification.classList.add('show'); }, 100);
	setTimeout(function () {
		notification.classList.remove('show');
		setTimeout(function () { document.body.removeChild(notification); }, 300);
	}, 3000);
}

function update_connection_status(status, text) {
	var status_dot = document.getElementById('connection-status');
	var status_text = document.getElementById('status-text');
	if (status_dot) {
		status_dot.className = 'status-dot ' + status;
	}
	if (status_text) {
		status_text.textContent = text;
	}
}

function update_last_update_display(timestamp) {
	var last_update_text = document.getElementById('last-update-text');
	if (last_update_text && timestamp) {
		var update_time = new Date(timestamp);
		var now = new Date();
		var diff_minutes = Math.floor((now - update_time) / (1000 * 60));
		var display_text;
		if (diff_minutes < 1) {
			display_text = 'Updated just now';
		} else if (diff_minutes < 60) {
			display_text = 'Updated ' + diff_minutes + 'm ago';
		} else {
			display_text = 'Updated ' + Math.floor(diff_minutes / 60) + 'h ago';
		}
		last_update_text.textContent = display_text;
	}
}

function initialize_ajax_forms() {
	var action_forms = document.querySelectorAll('.station-action-form, .item-action-form');
	for (var i = 0; i < action_forms.length; i++) {
		var form = action_forms[i];
		form.addEventListener('submit', function (e) {
			e.preventDefault();
			var button = this.querySelector('button[type="submit"]');
			var original_content = button.innerHTML;
			button.disabled = true;
			button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
			var form_element = this;
			fetch(form_element.action, {
				method: 'POST',
				body: new FormData(form_element),
				headers: {
					'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content')) || '',
					'Accept': 'application/json'
				}
			}).then(function (response) { return response.json(); }).then(function (data) {
				if (data.success) {
					setTimeout(refresh_station_data, 500);
					show_update_notification(data.message || 'Action completed successfully!');
				} else {
					throw new Error(data.message || 'Action failed');
				}
			}).catch(function (error) {
				//
				alert('Action failed: ' + error.message);
			}).finally(function () {
				button.disabled = false;
				button.innerHTML = original_content;
			});
		});
	}
}
document.addEventListener('visibilitychange', function () {
	if (document.hidden) {
		if (event_source && event_source.readyState === EventSource.OPEN) { }
	} else {
		if (!is_connected) {
			initialize_sse_connection();
		}
	}
});

function initialize_timers() {
	var timer_elements = document.querySelectorAll('.timer-display[data-elapsed]');
	for (var i = 0; i < timer_elements.length; i++) {
		var timer = timer_elements[i];
		var order_timer = timer.closest('.order-timer-compact');
		if (order_timer && order_timer.dataset.orderTime) {
			var order_time = new Date(order_timer.dataset.orderTime);
			update_single_timer(timer, order_time);
		}
	}
}

function update_timers() {
	var timer_elements = document.querySelectorAll('.timer-display[data-elapsed]');
	for (var i = 0; i < timer_elements.length; i++) {
		var timer = timer_elements[i];
		var order_timer = timer.closest('.order-timer-compact');
		if (order_timer && order_timer.dataset.orderTime) {
			var order_time = new Date(order_timer.dataset.orderTime);
			update_single_timer(timer, order_time);
		}
	}
}

function update_single_timer(timer_element, order_time) {
	var now = new Date();
	var elapsed_minutes = Math.floor((now - order_time) / (1000 * 60));
	var time_text;
	if (elapsed_minutes < 1) {
		time_text = '<1m';
	} else if (elapsed_minutes < 60) {
		time_text = elapsed_minutes + 'm';
	} else {
		var hours = Math.floor(elapsed_minutes / 60);
		var minutes = elapsed_minutes % 60;
		time_text = hours + 'h ' + minutes + 'm';
	}
	timer_element.textContent = time_text;
	timer_element.dataset.elapsed = elapsed_minutes;
	var order_card = timer_element.closest('.kds-order-card');
	var order_timer = timer_element.closest('.order-timer-compact');
	order_card.classList.remove('urgent', 'warning');
	order_timer.classList.remove('urgent', 'warning');
	if (elapsed_minutes > 20) {
		order_card.classList.add('urgent');
		order_timer.classList.add('urgent');
	} else if (elapsed_minutes > 10) {
		order_card.classList.add('warning');
		order_timer.classList.add('warning');
	}
}

function initialize_feature_toggles() {
	var saved_priority_mode = localStorage.getItem('kds-priority-mode');
	if (saved_priority_mode === 'true') {
		sort_orders_by_priority();
	}
}

function initialize_tooltips() {
	var action_buttons = document.querySelectorAll('.action-icon-btn[title]');
	for (var i = 0; i < action_buttons.length; i++) {
		var button = action_buttons[i];
		button.addEventListener('mouseenter', function (e) {
			var tooltip = document.createElement('div');
			tooltip.className = 'action-tooltip';
			tooltip.textContent = this.getAttribute('title');
			tooltip.style.cssText = 'position: absolute; background: #1f2937; color: white; padding: 4px 8px; border-radius: 4px; font-size: 11px; font-weight: 500; z-index: 1000; pointer-events: none; white-space: nowrap;';
			document.body.appendChild(tooltip);
			var rect = this.getBoundingClientRect();
			tooltip.style.left = (rect.left + rect.width / 2 - tooltip.offsetWidth / 2) + 'px';
			tooltip.style.top = (rect.bottom + 8) + 'px';
			this._tooltip = tooltip;
		});
		button.addEventListener('mouseleave', function () {
			if (this._tooltip) {
				document.body.removeChild(this._tooltip);
				delete this._tooltip;
			}
		});
	}
}

function sort_orders_by_priority() {
	var orders_container = document.getElementById('station-orders-container');
	var order_cards = Array.from(orders_container.querySelectorAll('.kds-order-card'));
	order_cards.sort(function (a, b) {
		var a_elapsed = parseInt(a.dataset.elapsed) || 0;
		var b_elapsed = parseInt(b.dataset.elapsed) || 0;
		return b_elapsed - a_elapsed;
	});
	for (var i = 0; i < order_cards.length; i++) {
		var card = order_cards[i];
		card.style.order = i;
		setTimeout(function (c, index) { return function () { orders_container.appendChild(c); }; }(card, i), i * 50);
	}
}

function sort_orders_by_time() {
	var orders_container = document.getElementById('station-orders-container');
	var order_cards = Array.from(orders_container.querySelectorAll('.kds-order-card'));
	order_cards.sort(function (a, b) {
		var a_order_id = parseInt(a.dataset.orderId) || 0;
		var b_order_id = parseInt(b.dataset.orderId) || 0;
		return a_order_id - b_order_id;
	});
	for (var i = 0; i < order_cards.length; i++) {
		var card = order_cards[i];
		card.style.order = i;
		setTimeout(function (c, index) { return function () { orders_container.appendChild(c); }; }(card, i), i * 50);
	}
}

function start_all_items(order_id) {
	var button = document.querySelector('[data-action="start"][onclick*="' + order_id + '"]');
	if (button) {
		button.disabled = true;
		button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
	}
}

function mark_order_ready(order_id) {
	var button = document.querySelector('[data-action="ready"][onclick*="' + order_id + '"]');
	if (button) {
		button.disabled = true;
		button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
	}
}

function mark_order_complete(order_id) {
	var button = document.querySelector('[data-action="complete"][onclick*="' + order_id + '"]');
	if (button) {
		button.disabled = true;
		button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
	}
}

function start_item_preparation(item_id) { }

function mark_item_done(item_id) { }

function print_order(order_id) {
	show_update_notification('Sending to printer...');
	fetch('/restaurant/kitchen/print-order/' + order_id, {
		method: 'POST',
		headers: {
			'Content-Type': 'application/json',
			'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content')) || ''
		},
		body: JSON.stringify({
			station_id: STATION_CONFIG.station_id
		})
	}).then(function (response) { return response.json(); }).then(function (data) {
		if (data.success) {
			show_update_notification(data.message || 'Order sent to printer successfully!');
			if (data.has_browser_printers) {
				trigger_browser_print(order_id, false);
			} else { }
		} else {
			throw new Error(data.message || 'Print failed');
		}
	}).catch(function (error) {
		//
		show_update_notification('Print failed: ' + error.message);
	});
}

function check_for_new_orders_auto_print() {
	fetch('/restaurant/kitchen/station/' + STATION_CONFIG.station_id + '/check-browser-auto-print', {
		method: 'GET',
		headers: {
			'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content')) || ''
		}
	}).then(function (response) { return response.json(); }).then(function (data) {
		if (data.success && data.should_auto_print) {
			var new_order_cards = document.querySelectorAll('.kds-order-card.new-order');
			for (var i = 0; i < new_order_cards.length; i++) {
				var order_card = new_order_cards[i];
				var order_id = order_card.getAttribute('data-order-id');
				if (order_id && !order_card.hasAttribute('data-auto-printed')) {
					show_update_notification('Auto-printing order #' + order_id + '...');
					trigger_browser_print(order_id, true);
					order_card.setAttribute('data-auto-printed', 'true');
				}
			}
		} else { }
	}).catch(function (error) {
		//
	});
}

function check_and_trigger_auto_print(order_id) {
	fetch('/restaurant/kitchen/station/' + STATION_CONFIG.station_id + '/check-browser-auto-print', {
		method: 'GET',
		headers: {
			'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]') && document.querySelector('meta[name="csrf-token"]').getAttribute('content')) || ''
		}
	}).then(function (response) { return response.json(); }).then(function (data) {
		if (data.success && data.should_auto_print) {
			show_update_notification('Auto-printing new order...');
			trigger_browser_print(order_id, true);
		}
	}).catch(function (error) {
		//
	});
}

function trigger_browser_print(order_id, is_autoprint) {
	if (typeof is_autoprint === 'undefined') is_autoprint = false;
	var print_url = '/restaurant/kitchen/print-order/' + order_id + '?browser=1';
	if (is_autoprint) {
		print_url += '&auto=1';
	}
	var print_window = window.open(print_url, '_blank', 'width=800,height=600');
	if (print_window) {
		if (!is_autoprint) {
			print_window.onload = function () {
				setTimeout(function () {
					print_window.print();
					setTimeout(function () {
						print_window.close();
					}, 1000);
				}, 500);
			};
		}
	} else {
		window.open(print_url, '_self');
	}
}

function open_order_modal(order_id) {
	var order_details_section = document.getElementById('order-' + order_id);
	if (!order_details_section) {
		//
		return;
	}
	var order_content = order_details_section.cloneNode(true);
	order_content.style.display = 'block';
	order_content.id = 'modal-order-content';
	var modal = document.getElementById('orderDetailsModal');
	var modal_body = document.getElementById('modalOrderContent');
	modal_body.innerHTML = '';
	modal_body.appendChild(order_content);
	modal.style.display = 'flex';
	document.addEventListener('keydown', handle_modal_escape);
	document.body.style.overflow = 'hidden';
}

function close_order_modal() {
	var modal = document.getElementById('orderDetailsModal');
	modal.style.display = 'none';
	document.removeEventListener('keydown', handle_modal_escape);
	document.body.style.overflow = '';
}

function handle_modal_escape(event) {
	if (event.key === 'Escape') {
		close_order_modal();
	}
}

function filter_orders_by_status(status) {
	var filter_buttons = document.querySelectorAll('.stat-filter');
	for (var i = 0; i < filter_buttons.length; i++) {
		var btn = filter_buttons[i];
		btn.classList.remove('active');
		if (btn.dataset.status === status) {
			btn.classList.add('active');
		}
	}
	var order_cards = document.querySelectorAll('.kds-order-card');
	for (var j = 0; j < order_cards.length; j++) {
		var card = order_cards[j];
		var order_status = card.dataset.status;
		var should_show = false;
		if (status === 'all') {
			should_show = true;
		} else if (status === 'pending') {
			should_show = order_status === 'pending';
		} else if (status === 'preparing') {
			should_show = order_status === 'preparing';
		} else if (status === 'ready') {
			should_show = order_status === 'ready' || order_status === 'completed' || has_ready_items(card);
		} else if (status === 'served') {
			should_show = has_all_items_served(card);
		}
		card.style.display = should_show ? 'block' : 'none';
	}
	update_empty_state();
}

function has_ready_items(order_card) {
	var ready_items = order_card.querySelectorAll('.kds-item[data-status="ready"], .kds-item[data-status="cooked"]');
	return ready_items.length > 0;
}

function has_all_items_served(order_card) {
	var all_items = order_card.querySelectorAll('.kds-item');
	var served_items = order_card.querySelectorAll('.kds-item[data-status="served"]');
	return all_items.length > 0 && all_items.length === served_items.length;
}

function update_empty_state() {
	var order_cards = document.querySelectorAll('.kds-order-card');
	var visible_cards = [];
	for (var i = 0; i < order_cards.length; i++) {
		var card = order_cards[i];
		if (card.style.display !== 'none' && card.offsetParent !== null) {
			visible_cards.push(card);
		}
	}
	var empty_state = document.querySelector('.kds-empty-state');
	if (empty_state) {
		empty_state.style.display = visible_cards.length === 0 ? 'block' : 'none';
	}
}

function change_time_filter(time_filter) {
	var current_url = new URL(window.location);
	current_url.searchParams.set('time_filter', time_filter);
	window.location.href = current_url.toString();
}