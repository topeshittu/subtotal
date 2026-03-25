<script>
window.POS_APP_SETTINGS = {};
function get_instant_pos_enabled() {
    var app_enabled = {{ $app_settings->enable_instant_pos ? 'true' : 'false' }};
    var business_enabled = {{ isset($business_details->enable_instant_pos) && $business_details->enable_instant_pos ? 'true' : 'false' }};
    var local_setting = localStorage.getItem('enable_instant_pos');
    if (!app_enabled || !business_enabled) {
        return false;
    }
    if (local_setting === null) {
        return false;
    }
    return local_setting === 'true';
}
var instant_pos_enabled = get_instant_pos_enabled();
if (instant_pos_enabled) {
    Object.assign(window.POS_APP_SETTINGS, {
        enable_instant_pos: true,
        enable_instant_search: {{ $app_settings->enable_instant_search ? 'true' : 'false' }},
        pos_cache_refresh_interval: '{{ $app_settings->pos_cache_refresh_interval }}',
        pos_max_cached_products: {{ $app_settings->pos_max_cached_products }},
        messages: @js([
            'loading_products' => __('settings.loading_products'),
            'please_wait_while_products_load' => __('settings.please_wait_while_products_load'),
            'loading_products_placeholder' => __('settings.loading_products_placeholder'),
            'updating_stock' => __('settings.updating_stock'),
            'failed_to_load_cache' => __('settings.failed_to_load_cache')
        ])
    });
}

localStorage.setItem('enable_instant_pos', instant_pos_enabled.toString());
document.addEventListener('DOMContentLoaded', function() {
    var button = document.getElementById('instant_pos_btn');
    var icon = document.getElementById('instant_pos_icon');
    if (button && icon) {
        update_button_state(get_instant_pos_enabled());
    }
});

function update_button_state(is_enabled) {
    var button = document.getElementById('instant_pos_btn');
    var icon = document.getElementById('instant_pos_icon');
    var path = document.getElementById('instant_pos_path');
    var refresh_btn = document.getElementById('refresh_pos_cache_btn');
    
    if (button && icon && path) {
        if (is_enabled) {
            button.classList.add('active');
            button.style.backgroundColor = 'var(--primary-color)';
            button.style.borderRadius = '4px';
            path.setAttribute('fill', '#FFB636');
            path.setAttribute('stroke', '#fff');
            button.setAttribute('title', '@lang("settings.disable_instant_pos")');
            button.setAttribute('data-original-title', '@lang("settings.disable_instant_pos")');
            if (refresh_btn) {
                refresh_btn.style.display = 'inline-block';
            }
        } else {
            button.classList.remove('active');
            button.style.backgroundColor = 'transparent';
            button.style.borderRadius = '';
            path.setAttribute('fill', 'none');
            path.setAttribute('stroke', '#666');
            button.setAttribute('title', '@lang("settings.enable_instant_pos")');
            button.setAttribute('data-original-title', '@lang("settings.enable_instant_pos")');
            if (refresh_btn) {
                refresh_btn.style.display = 'none';
            }
        }
        
        if (typeof $ !== 'undefined' && $ && typeof $.fn !== 'undefined' && typeof $.fn.tooltip === 'function') {
            try {
                var $button = $(button);
                if ($button.length && typeof $button.tooltip === 'function') {
                    try {
                        $button.tooltip('dispose');
                    } catch (disposeError) {
                    }
                    $button.tooltip({
                        title: button.getAttribute('title') || '',
                        placement: 'bottom'
                    });
                }
            } catch (e) {
                //
            }
        }
    }
}

function toggle_instant_pos() {
    var current_state = get_instant_pos_enabled();
    var is_enabled = !current_state;
    localStorage.setItem('enable_instant_pos', is_enabled.toString());
    update_button_state(is_enabled);
    
    window.POS_APP_SETTINGS = window.POS_APP_SETTINGS || {};
    
    if (is_enabled) {
        Object.assign(window.POS_APP_SETTINGS, {
            enable_instant_pos: true,
            enable_instant_search: {{ $app_settings->enable_instant_search ? 'true' : 'false' }},
            pos_cache_refresh_interval: '{{ $app_settings->pos_cache_refresh_interval }}',
            pos_max_cached_products: {{ $app_settings->pos_max_cached_products }},
            messages: @js([
                'loading_products' => __('settings.loading_products'),
                'please_wait_while_products_load' => __('settings.please_wait_while_products_load'),
                'loading_products_placeholder' => __('settings.loading_products_placeholder'),
                'updating_stock' => __('settings.updating_stock'),
                'failed_to_load_cache' => __('settings.failed_to_load_cache')
            ])
        });
        if (!window.pos_realtime_loaded) {
            var script = document.createElement('script');
            script.src = "{{ asset('js/pos_realtime.js?v=' . $asset_v) }}";
            script.onload = function() {
                window.pos_realtime_loaded = true;
                if (typeof initialize_pos_realtime === 'function') {
                    initialize_pos_realtime();
                }
            };
            document.head.appendChild(script);
        } else {
            if (typeof initialize_pos_realtime === 'function') {
                initialize_pos_realtime();
            }
        }
    } else {
        window.POS_APP_SETTINGS.enable_instant_pos = false;
        if (typeof cleanup_pos_realtime === 'function') {
            cleanup_pos_realtime();
        }
    }
    
    if (is_enabled) {
        toastr.success('@lang("settings.instant_pos_enabled")');
        toggle_tax_column_visibility();
    } else {
        alert('@lang("settings.instant_pos_disabled")');
        setTimeout(function() {
            window.location.reload();
        }, 500);
    }
}
</script>

@if(($business_details->enable_instant_pos ?? false) && $app_settings->enable_instant_pos)
<script>
if (get_instant_pos_enabled()) {
    var script = document.createElement('script');
    script.src = "{{ asset('js/pos_realtime.js?v=' . $asset_v) }}";
    document.head.appendChild(script);
}
</script>
@endif
<script>

window.POS_TRANSLATIONS = @js([
    'pos_edit_product_price_help' => __('lang_v1.pos_edit_product_price_help'),
    'in_stock' => __('lang_v1.in_stock'),
    'item_out_of_stock' => __('lang_v1.item_out_of_stock'),
    'decimal_value_not_allowed' => __('lang_v1.decimal_value_not_allowed'),
    'this_field_is_required' => __('validation.custom-messages.this_field_is_required'),
    'quantity_not_available' => __('validation.custom-messages.quantity_not_available'),
    'quantity_error_msg_in_lot' => __('lang_v1.quantity_error_msg_in_lot'),
    'lot_n_expiry' => __('lang_v1.lot_n_expiry'),
    'exp_date' => __('product.exp_date'),
    'expired' => __('report.expired'),
    'sell_line_description_help' => __('lang_v1.sell_line_description_help'),
    'select_service_staff' => __('restaurant.select_service_staff'),
    'prev_unit_price' => __('lang_v1.prev_unit_price'),
    'prev_discount' => __('lang_v1.prev_discount'),
    'fixed' => __('lang_v1.fixed'),
    'percentage' => __('lang_v1.percentage'),
    'applied_discount_text' => __('lang_v1.applied_discount_text'),
    'please_select' => __('messages.please_select'),
    'quantity_in_second_unit' => __('lang_v1.quantity_in_second_unit'),
    'minimum_selling_price_error_msg' => __('lang_v1.minimum_selling_price_error_msg'),
    'unit_price' => __('sale.unit_price'),
    'discount_type' => __('sale.discount_type'),
    'discount_amount' => __('sale.discount_amount'),
    'tax' => __('sale.tax'),
    'warranty' => __('lang_v1.warranty'),
    'description' => __('lang_v1.description'),
    'close' => __('messages.close'),
    'no_products_found' => __('lang_v1.no_products_found'),
    'out_of_stock' => __('lang_v1.out_of_stock'),
]);
</script>

<style>
.dropdown-menu {
    border-radius: 10px !important;
}
.sub-unit-option.selected {
    background-color: var(--primary-color) !important;
    color: white !important;
    border-radius: 10px !important;
    margin: 2px 4px !important;
}
.sub-unit-option.selected:hover,
.sub-unit-option.selected:focus {
    background-color: var(--primary-color) !important;
    color: white !important;
    opacity: 0.9 !important;
}
.input-group .btn-group {
    position: static !important;
}
.input-group .btn-group .dropdown-menu {
    position: absolute !important;
    z-index: 1050 !important;
}
@media (max-width: 768px) {
    .input-group .btn-group .dropdown-menu {
        position: fixed !important;
        max-width: 200px !important;
    }
}
</style>

<script>
function toggle_tax_column_visibility() {
    var instant_pos_enabled = localStorage.getItem('enable_instant_pos') === 'true';
    var tax_columns = document.querySelectorAll('.instant-pos-tax-column');
    
    for (var i = 0; i < tax_columns.length; i++) {
        if (instant_pos_enabled) {
            tax_columns[i].style.display = '';
        } else {
            tax_columns[i].style.display = 'none';
        }
    }
}

function toggleRunningOrdersSidebar() {
    var sidebar = document.getElementById('running-orders-sidebar');
    if (sidebar) {
        if (sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
        } else {
            loadRunningOrders();
            sidebar.classList.add('open');
        }
    }
}

function loadRunningOrders() {
    var button = document.getElementById('view_running_orders');
    var url = button ? button.getAttribute('data-href') : null;
    
    if (!url) {
        console.log('No URL found for loading running orders');
        return;
    }
    
    var containers = ['new-orders', 'delayed-orders', 'old-orders', 'cards-container'];
    containers.forEach(function(id) {
        var container = document.getElementById(id);
        if (container) {
            container.innerHTML = '<div style="text-align: center; padding: 20px;"><i class="fas fa-spinner fa-spin"></i> Loading orders...</div>';
        }
    });
    
    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function(response) {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(function(html) {
        var temp = document.createElement('div');
        temp.innerHTML = html;
        
        var responseCards = temp.querySelectorAll('.order-card');
        var cardsContainer = document.getElementById('cards-container');
        
        if (cardsContainer) {
            cardsContainer.innerHTML = '';
            responseCards.forEach(function(card) {
                cardsContainer.appendChild(card.cloneNode(true));
            });
            
            if (typeof update_elapsed_time === 'function') {
                update_elapsed_time();
            }
            if (typeof categorize_orders === 'function') {
                categorize_orders();
            }
            
            var savedView = localStorage.getItem('running_orders_view') || 'card';
            if (typeof switchView === 'function') {
                switchView(savedView);
            }
        }
    })
    .catch(function(error) {
        console.error('Error loading running orders:', error);
        containers.forEach(function(id) {
            var container = document.getElementById(id);
            if (container) {
                container.innerHTML = '<div style="text-align: center; padding: 20px; color: #dc3545;"><i class="fas fa-exclamation-triangle"></i> Error loading orders</div>';
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function() {
    toggle_tax_column_visibility();
    window.addEventListener('storage', function(e) {
        if (e.key === 'enable_instant_pos') {
            toggle_tax_column_visibility();
        }
    });
    
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('sub-unit-option')) {
            e.preventDefault();
            
            var option = e.target;
            var dropdown = option.closest('.dropdown-menu');
            var dropdown_btn = option.closest('.btn-group').querySelector('.dropdown-toggle');
            var hidden_select = option.closest('tr').querySelector('.sub_unit');
            var all_options = dropdown.querySelectorAll('.sub-unit-option');
            for (var i = 0; i < all_options.length; i++) {
                all_options[i].classList.remove('selected');
            }
            option.classList.add('selected');
            
            if (hidden_select) {
                hidden_select.value = option.getAttribute('data-value');
                var change_event = new Event('change', { bubbles: true });
                hidden_select.dispatchEvent(change_event);
            }
            dropdown_btn.click();
        }
    });
});
</script>