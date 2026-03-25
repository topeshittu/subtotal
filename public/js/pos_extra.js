// Safe-define globals some legacy snippets might expect
(function(){
    if (typeof window.packing_charge_text === 'undefined') {
        window.packing_charge_text = document.getElementById('packing_charge_text') || null;
    }
})();

document.addEventListener('DOMContentLoaded', function () {
    var exchange_rate_select = document.getElementById('exchange_rate_id');
    var exchange_rate_input = document.getElementById('currency_exchange_rate');

    if (exchange_rate_select && exchange_rate_input) {
        exchange_rate_select.addEventListener('change', function () {
            var selected_option = this.options[this.selectedIndex];
            var exchange_rate = selected_option.getAttribute('data-rate');
            if (exchange_rate) {
                exchange_rate_input.value = exchange_rate;
            } else {
                exchange_rate_input.value = '';
                updateCurrencyValues();
            }
        });
        exchange_rate_select.dispatchEvent(new Event('change'));
    }
});

function adjust_tbody_scroll_height() {
    var footer_height = document.querySelector('.pos-form-actions') ? document.querySelector('.pos-form-actions').offsetHeight : 0;
    var tbody_scroll = document.querySelector('.tbody-scroll');
    var header_height = document.querySelector('.pos-header') ? document.querySelector('.pos-header').offsetHeight : 0;
    var pos_heading = document.querySelector('.pos-heading') ? document.querySelector('.pos-heading').offsetHeight : 0;
    var is_mobile = window.innerWidth <= 768;
    var additional_height = is_mobile ? 90 : 70;

    if (tbody_scroll) {
        tbody_scroll.style.height = 'calc(100vh - ' + (footer_height + header_height + pos_heading + additional_height) + 'px)';

        if (document.body.classList.contains('repair-sub-type')) {
            var pos_wrapper = document.querySelector('.pos-wrapper');
            if (pos_wrapper) {
                pos_wrapper.style.minHeight = document.body.scrollHeight + 'px';
            }
        }
    }
}

function setup_mutation_observer() {
    var target_node = document.body;
    if (target_node) {
        var observer = new MutationObserver(function (mutations_list, observer) {
            mutations_list.forEach(function (mutation) {
                if (mutation.type === 'childList') {
                    if (mutation.addedNodes.length > 0) {
                        adjust_tbody_scroll_height();
                    }
                }
            });
        });

        var config = { childList: true, subtree: true };
        observer.observe(target_node, config);
    }
}

function adjust_product_list_height() {
    var footer_height = document.querySelector('.pos-form-actions') ? document.querySelector('.pos-form-actions').offsetHeight : 0;
    var product_list_body = document.querySelector('#product_list_body');
    var header_height = document.querySelector('.pos-header') ? document.querySelector('.pos-header').offsetHeight : 0;
    var pos_heading = document.querySelector('.pos-heading') ? document.querySelector('.pos-heading').offsetHeight : 0;
    var is_mobile = window.innerWidth <= 768;
    var additional_height = is_mobile ? 90 : 90;

    if (product_list_body) {
        product_list_body.style.maxHeight = 'calc(100vh - ' + (footer_height + pos_heading + additional_height) + 'px)';
        product_list_body.style.overflowY = 'auto';
    }
}

function setup_product_list_mutation_observer() {
    var target_node = document.body;
    if (target_node) {
        var observer = new MutationObserver(function (mutations_list, observer) {
            mutations_list.forEach(function (mutation) {
                if (mutation.type === 'childList') {
                    if (mutation.addedNodes.length > 0) {
                        adjust_product_list_height();
                    }
                }
            });
        });

        var config = { childList: true, subtree: true };
        observer.observe(target_node, config);
    }
}

window.addEventListener('load', function () {
    adjust_tbody_scroll_height();
    adjust_product_list_height();
    setup_mutation_observer();
    setup_product_list_mutation_observer();
    window.addEventListener('resize', adjust_tbody_scroll_height);
    window.addEventListener('resize', adjust_product_list_height);
});

if (window.location.href.includes('/pos/create?sub_type=repair')) {
    document.body.classList.add('repair-sub-type');
}

document.addEventListener('DOMContentLoaded', function () {
    var main_categories = document.querySelectorAll('.main-category-div');
    for (var i = 0; i < main_categories.length; i++) {
        var category = main_categories[i];

        category.addEventListener('mouseenter', function () {
            var sub_category_dropdown = this.querySelector('.sub-category-dropdown');

            if (sub_category_dropdown) {
                var category_rect = this.getBoundingClientRect();
                var window_width = window.innerWidth;

                if (window_width - category_rect.right > sub_category_dropdown.offsetWidth) {
                    sub_category_dropdown.style.left = category_rect.width + 'px';
                    sub_category_dropdown.style.right = 'auto';
                } else {
                    sub_category_dropdown.style.right = category_rect.width + 'px';
                    sub_category_dropdown.style.left = 'auto';
                }
                sub_category_dropdown.style.display = 'block';
            }
        });

        category.addEventListener('mouseleave', function () {
            var sub_category_dropdown = this.querySelector('.sub-category-dropdown');
            if (sub_category_dropdown) {
                sub_category_dropdown.style.display = 'none';
            }
        });
    }

    var category_drawer_toggle = document.getElementById('category-drawer-toggle');
    var brand_drawer_toggle = document.getElementById('brand-drawer-toggle');
    var category_drawer = document.getElementById('category-drawer');
    var brand_drawer = document.getElementById('brand-drawer');

    // Handle click outside for drawers and popovers
    document.addEventListener('click', function (event) {
        if (typeof category_drawer_toggle !== 'undefined' && category_drawer_toggle &&
            typeof category_drawer !== 'undefined' && category_drawer) {

            if (category_drawer_toggle.checked
                && !category_drawer.contains(event.target)
                && event.target.id !== 'category-drawer-toggle') {

                category_drawer_toggle.checked = false;
            }
        }
        if (brand_drawer_toggle && brand_drawer && brand_drawer_toggle.checked && !brand_drawer.contains(event.target) && event.target.id !== 'brand-drawer-toggle') {
            brand_drawer_toggle.checked = false;
        }

        // Handle all popovers click outside
        var popover_buttons = document.querySelectorAll('.popover-default');
        for (var i = 0; i < popover_buttons.length; i++) {
            var popover_btn = popover_buttons[i];
            var popover_visible = popover_btn.getAttribute('aria-describedby');
            if (popover_visible) {
                var popover_element = document.getElementById(popover_visible);
                if (popover_element &&
                    !popover_btn.contains(event.target) &&
                    !popover_element.contains(event.target)) {
                    $(popover_btn).popover('hide');
                }
            }
        }
    });
});
