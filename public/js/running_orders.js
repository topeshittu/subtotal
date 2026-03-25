var search_timeout;
var server_time_element = document.getElementById('server-time');
var server_time = server_time_element ? new Date(server_time_element.value) : new Date();
var browser_time = new Date();
var time_offset = browser_time.getTime() - server_time.getTime();

function toggleNotes(button) {
    var notes_section = button.nextElementSibling;
    if (notes_section && notes_section.classList.contains('notes-section')) {
        notes_section.classList.toggle('show');
        var icon = button.querySelector('i');
        if (notes_section.classList.contains('show')) {
            button.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Notes';
        } else {
            button.innerHTML = '<i class="fas fa-sticky-note"></i> View Notes';
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

function openRunningOrdersSidebar() {
    var sidebar = document.getElementById('running-orders-sidebar');
    if (sidebar) {
        loadRunningOrders();
        sidebar.classList.add('open');
    }
}

function closeRunningOrdersSidebar() {
    var sidebar = document.getElementById('running-orders-sidebar');
    if (sidebar) {
        sidebar.classList.remove('open');
    }
}

function loadRunningOrders() {
    var button = document.getElementById('view_running_orders');
    var url = button ? button.getAttribute('data-href') : null;

    if (!url) {
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

            update_elapsed_time();
            categorize_orders();

            var savedView = localStorage.getItem('running_orders_view') || 'card';
            switchView(savedView);
        }
    })
    .catch(function(error) {
        containers.forEach(function(id) {
            var container = document.getElementById(id);
            if (container) {
                container.innerHTML = '<div style="text-align: center; padding: 20px; color: #dc3545;"><i class="fas fa-exclamation-triangle"></i> Error loading orders</div>';
            }
        });
    });
}

function update_elapsed_time() {
    var elapsed_elements = document.querySelectorAll('.elapsed-time');
    for (var i = 0; i < elapsed_elements.length; i++) {
        var element = elapsed_elements[i];
        var start_time = new Date(element.getAttribute('data-start-time'));
        var current_time = new Date(new Date().getTime() - time_offset);
        var elapsed_time = current_time - start_time;

        var days = Math.floor(elapsed_time / (1000 * 60 * 60 * 24));
        var hours = Math.floor((elapsed_time % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((elapsed_time % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((elapsed_time % (1000 * 60)) / 1000);

        var formatted_time = '';
        if (days >= 1) {
            formatted_time = days + 'd ' + hours + 'h ' + minutes + 'm ' + seconds + 's';
        } else if (hours >= 1) {
            formatted_time = hours + 'h ' + minutes + 'm ' + seconds + 's';
        } else {
            formatted_time = minutes + 'm ' + seconds + 's';
        }

        element.textContent = formatted_time;

        var elapsed_minutes = Math.floor(elapsed_time / (1000 * 60));
        element.className = element.className.replace(/\b(green|yellow|orange|red)\b/g, '');
        element.classList.add('elapsed-time');
        if (elapsed_minutes <= 15) {
            element.classList.add('green');
        } else if (elapsed_minutes <= 45) {
            element.classList.add('yellow');
        } else if (elapsed_minutes <= 59) {
            element.classList.add('orange');
        } else {
            element.classList.add('red');
        }
    }
}

function categorize_orders() {
    var new_count = 0, delayed_count = 0, old_count = 0;

    var cards_container = document.getElementById('cards-container');
    var new_orders = document.getElementById('new-orders');
    var delayed_orders = document.getElementById('delayed-orders');
    var old_orders = document.getElementById('old-orders');

    if (new_orders) new_orders.innerHTML = '';
    if (delayed_orders) delayed_orders.innerHTML = '';
    if (old_orders) old_orders.innerHTML = '';

    if (!cards_container) return;

    var all_cards = cards_container.querySelectorAll('.order-card');

    for (var i = 0; i < all_cards.length; i++) {
        var card = all_cards[i];
        var elapsed_element = card.querySelector('.elapsed-time');
        if (!elapsed_element) continue;

        var start_time = new Date(elapsed_element.getAttribute('data-start-time'));
        var current_time = new Date();
        var elapsed_hours = Math.floor((current_time - start_time) / (1000 * 60 * 60));

        var card_clone = card.cloneNode(true);

        if (elapsed_hours < 1) {
            if (new_orders) new_orders.appendChild(card_clone);
            new_count++;
        } else if (elapsed_hours >= 1 && elapsed_hours <= 23) {
            if (delayed_orders) delayed_orders.appendChild(card_clone);
            delayed_count++;
        } else {
            if (old_orders) old_orders.appendChild(card_clone);
            old_count++;
        }
    }

    var new_count_element = document.getElementById('new-count');
    var delayed_count_element = document.getElementById('delayed-count');
    var old_count_element = document.getElementById('old-count');

    if (new_count_element) new_count_element.textContent = new_count > 99 ? '99+' : new_count;
    if (delayed_count_element) delayed_count_element.textContent = delayed_count > 99 ? '99+' : delayed_count;
    if (old_count_element) old_count_element.textContent = old_count > 99 ? '99+' : old_count;
}

function switchView(viewType) {
    localStorage.setItem('running_orders_view', viewType);

    var viewButtons = document.querySelectorAll('.view-btn');
    viewButtons.forEach(function(btn) {
        btn.classList.remove('active');
        if (btn.getAttribute('data-view') === viewType) {
            btn.classList.add('active');
        }
    });

    var containers = document.querySelectorAll('.orders-container');
    containers.forEach(function(container) {
        container.classList.remove('list-view', 'grid-view');
        if (viewType !== 'card') {
            container.classList.add(viewType + '-view');
        }
    });
}

function perform_search(value) {
    var search_orders = document.getElementById('search-orders');

    if (value && value.length >= 2) {
        var found_cards = [];

        var tab_containers = ['new-orders', 'delayed-orders', 'old-orders'];
        for (var t = 0; t < tab_containers.length; t++) {
            var container = document.getElementById(tab_containers[t]);
            if (container) {
                var cards = container.querySelectorAll('.order-card');
                for (var i = 0; i < cards.length; i++) {
                    var card = cards[i];
                    var card_text = card.textContent.toLowerCase();

                    if (card_text.indexOf(value) > -1) {
                        var card_clone = card.cloneNode(true);
                        found_cards.push(card_clone);
                    }
                }
            }
        }

        if (search_orders) {
            search_orders.innerHTML = '';
            search_orders.classList.remove('list-view', 'grid-view');

            if (found_cards.length > 0) {
                var savedView = localStorage.getItem('running_orders_view') || 'card';

                for (var j = 0; j < found_cards.length; j++) {
                    search_orders.appendChild(found_cards[j]);
                }

                if (savedView !== 'card') {
                    search_orders.classList.add(savedView + '-view');
                }
            } else {
                search_orders.innerHTML = '<div class="no-results">No orders found matching "' + value + '"</div>';
            }
        }

    } else {
        if (search_orders) {
            search_orders.innerHTML = '';
            search_orders.classList.remove('list-view', 'grid-view');
        }
    }
}

function printToKitchenStations(transactionId) {
    if (!transactionId) {
        toastr.error('Invalid order ID');
        return;
    }

    toastr.info('Sending order to kitchen stations...');

    fetch('/modules/kitchen/print-order/' + transactionId, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '',
            'Content-Type': 'application/json'
        }
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        if (data.success) {
            toastr.success(data.message || 'Order sent to kitchen stations successfully!');
        } else {
            toastr.error(data.message || 'Failed to send order to kitchen stations');
        }
    })
    .catch(function(error) {
        toastr.error('Error sending order to kitchen stations');
    });
}

function update_if_visible() {
    if (document.visibilityState === 'visible') {
        update_elapsed_time();
    }
}

function categorize_if_visible() {
    if (document.visibilityState === 'visible') {
        var searchInput = document.getElementById('search-input');
        var searchTab = document.querySelector('a[href="#search"]');
        var isSearchActive = searchTab && searchTab.parentElement.classList.contains('active');
        var hasSearchValue = searchInput && searchInput.value.trim().length >= 2;

        if (!isSearchActive || !hasSearchValue) {
            categorize_orders();
        }
    }
}

document.addEventListener('keyup', function(e) {
    if (e.target && e.target.id === 'search-input') {
        var value = e.target.value.toLowerCase().trim();

        if (search_timeout) {
            clearTimeout(search_timeout);
        }

        if (value.length >= 2) {
            search_timeout = setTimeout(function() {
                perform_search(value);
            }, 200);
        } else if (value.length === 0) {
            perform_search('');
        }
    }
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.view-btn')) {
        var button = e.target.closest('.view-btn');
        var viewType = button.getAttribute('data-view');
        switchView(viewType);
    }
});

document.addEventListener('click', function(e) {
    var sidebar = document.getElementById('running-orders-sidebar');
    var target = e.target;

    if (sidebar && sidebar.classList.contains('open') &&
        !sidebar.contains(target) &&
        !target.closest('[data-target="#running_orders_modal"]') &&
        !target.closest('.running-orders-trigger')) {
        closeRunningOrdersSidebar();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRunningOrdersSidebar();
    }
});

document.addEventListener('visibilitychange', function() {
    if (document.visibilityState === 'visible') {
        update_elapsed_time();

        var searchInput = document.getElementById('search-input');
        var searchTab = document.querySelector('a[href="#search"]');
        var isSearchActive = searchTab && searchTab.parentElement.classList.contains('active');
        var hasSearchValue = searchInput && searchInput.value.trim().length >= 2;

        if (!isSearchActive || !hasSearchValue) {
            categorize_orders();
        }
    }
});

document.addEventListener('DOMContentLoaded', function() {
    var savedView = localStorage.getItem('running_orders_view') || 'card';
    switchView(savedView);

    setTimeout(function() {
        update_elapsed_time();
        categorize_orders();
    }, 500);

    var tab_links = document.querySelectorAll('.nav-tabs a');
    for (var i = 0; i < tab_links.length; i++) {
        tab_links[i].addEventListener('click', function(e) {
            setTimeout(function() {
                update_elapsed_time();
            }, 100);
        });
    }
});

setInterval(update_if_visible, 1000);
setInterval(categorize_if_visible, 60000);
