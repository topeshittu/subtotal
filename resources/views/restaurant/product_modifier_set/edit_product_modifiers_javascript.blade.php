 <script>
     document.addEventListener('DOMContentLoaded', function() {
         var edit_buttons = document.querySelectorAll('.modifier-edit-btn');

         edit_buttons.forEach(function(button) {
             button.addEventListener('click', function() {
                 var row_count = this.getAttribute('data-row-count');

                 var modal_selector = '[id^="modifier_' + row_count + '_"]';
                 var modal = document.querySelector(modal_selector);

                 if (modal) {
                     if (typeof window.bootstrap !== 'undefined') {
                         var modal_instance = new bootstrap.Modal(modal);
                         modal_instance.show();
                     } else if (typeof window.$ !== 'undefined') {
                         $(modal).modal('show');
                     } else {
                         //
                     }
                 } else {
                     //
                 }
             });
         });
     });

     window.toggle_combo_items = function(row_count) {
         var combo_items = document.getElementById('combo-items-' + row_count);
         var toggle_icon = document.getElementById('combo-icon-' + row_count);

         if (!combo_items) return;

         var combo_modifier_containers = combo_items.querySelectorAll('.combo_modifier_prices_container');

         function reset_combo_items_style() {
             combo_items.removeAttribute('style');
         }

         function set_combo_containers_display(display_value) {
             combo_modifier_containers.forEach(function(container) {
                 container.removeAttribute('style');
                 if (display_value === 'block') {
                     // Only show containers that have modifier content
                     var has_modifiers = container.querySelector('.product_modifier') ||
                                       container.querySelector('.combo-modifier-item');
                     if (has_modifiers && container.innerHTML.trim() !== '') {
                         container.style.display = 'block';
                     } else {
                         container.style.display = 'none';
                     }
                 } else {
                     container.style.display = display_value;
                 }
             });
         }

         var is_currently_hidden = combo_items.style.display === 'none' || combo_items.style.display === '';

         if (is_currently_hidden) {
             reset_combo_items_style();
             combo_items.style.display = 'block';
             combo_items.style.opacity = '0';
             combo_items.style.transform = 'translateY(-10px)';

             set_combo_containers_display('block');
             setTimeout(function() {
                 combo_items.style.transition = 'all 0.3s ease';
                 combo_items.style.opacity = '1';
                 combo_items.style.transform = 'translateY(0)';
             }, 10);

             toggle_icon.className = 'fas fa-minus combo-toggle-icon';
             toggle_icon.style.transform = 'rotate(0deg)';
         } else {
             set_combo_containers_display('none');

             combo_items.style.transition = 'all 0.3s ease';
             combo_items.style.opacity = '0';
             combo_items.style.transform = 'translateY(-10px)';

             setTimeout(function() {
                 reset_combo_items_style();
                 combo_items.style.display = 'none';
             }, 300);

             toggle_icon.className = 'fas fa-plus combo-toggle-icon';
             toggle_icon.style.transform = 'rotate(90deg)';
         }
     };

     window.toggle_plain_modifiers = function(row_count) {
         var plain_items = document.getElementById('plain-items-' + row_count);
         var toggle_icon = document.getElementById('plain-icon-' + row_count);

         if (!plain_items) return;

         var selected_modifiers_span = plain_items.querySelector('.selected_modifiers');
         var all_modifier_spans = document.querySelectorAll('[id^="selected_modifiers_modifier_' + row_count +
             '_"]');
       
         function reset_plain_items_style() {
             plain_items.removeAttribute('style');
         }

         

         function set_selected_modifiers_display(display_value) {
             if (selected_modifiers_span) {
                 selected_modifiers_span.removeAttribute('style');
                 selected_modifiers_span.style.display = display_value;
                 var child_modifiers = selected_modifiers_span.querySelectorAll('.product_modifier, .modifier-item');
                 child_modifiers.forEach(function(child, index) {
                     child.removeAttribute('style');
                     child.style.display = display_value;
                 });
             }

             all_modifier_spans.forEach(function(span, index) {
                 span.removeAttribute('style');
                 span.style.display = display_value;

                 var child_modifiers = span.querySelectorAll('.product_modifier, .modifier-item');
                 child_modifiers.forEach(function(child, child_index) {
                     child.removeAttribute('style');
                     child.style.display = display_value;

                 });
             });
         }

         var is_currently_hidden = plain_items.style.display === 'none' || plain_items.style.display === '';

         if (is_currently_hidden) {
             reset_plain_items_style();
             plain_items.style.display = 'block';
             plain_items.style.opacity = '0';
             plain_items.style.transform = 'translateY(-10px)';

             set_selected_modifiers_display('block');
            
             setTimeout(function() {
                 plain_items.style.transition = 'all 0.3s ease';
                 plain_items.style.opacity = '1';
                 plain_items.style.transform = 'translateY(0)';
             }, 10);

             toggle_icon.className = 'fas fa-minus combo-toggle-icon';
             toggle_icon.style.transform = 'rotate(0deg)';
         } else {
             set_selected_modifiers_display('none');
             
             plain_items.style.transition = 'all 0.3s ease';
             plain_items.style.opacity = '0';
             plain_items.style.transform = 'translateY(-10px)';

             setTimeout(function() {
                 reset_plain_items_style();
                 plain_items.style.display = 'none';
             }, 300);

             toggle_icon.className = 'fas fa-plus combo-toggle-icon';
             toggle_icon.style.transform = 'rotate(90deg)';
         }
     };

     document.addEventListener('DOMContentLoaded', function() {
         var all_combo_items = document.querySelectorAll('[id^="combo-items-"]');
         all_combo_items.forEach(function(combo_item) {
             combo_item.removeAttribute('style');
             combo_item.style.display = 'none';

             var modifier_containers = combo_item.querySelectorAll('.combo_modifier_prices_container');
             modifier_containers.forEach(function(container) {
                 container.removeAttribute('style');
                 // Only hide containers that don't have any modifier content
                 var has_modifiers = container.querySelector('.product_modifier') ||
                                   container.querySelector('.combo-modifier-item');
                 if (!has_modifiers || container.innerHTML.trim() === '') {
                     container.style.display = 'none';
                 } else {
                     // Container has modifiers, keep it visible when combo section is shown
                     container.style.display = 'block';
                 }
             });
         });

         var all_combo_toggle_icons = document.querySelectorAll('[id^="combo-icon-"]');
         all_combo_toggle_icons.forEach(function(icon) {
             icon.className = 'fas fa-plus combo-toggle-icon';
             icon.style.transform = 'rotate(90deg)';
         });

         var all_plain_items = document.querySelectorAll('[id^="plain-items-"]');
         all_plain_items.forEach(function(plain_item) {
             plain_item.removeAttribute('style');
             plain_item.style.display = 'none';

             var selected_modifiers_span = plain_item.querySelector('.selected_modifiers');
             if (selected_modifiers_span) {
                 selected_modifiers_span.removeAttribute('style');
                 selected_modifiers_span.style.display = 'none';
             }
         });

         var all_specific_modifier_spans = document.querySelectorAll('[id^="selected_modifiers_modifier_"]');
         all_specific_modifier_spans.forEach(function(span) {
             span.removeAttribute('style');
             span.style.display = 'none';

         });


         var all_plain_toggle_icons = document.querySelectorAll('[id^="plain-icon-"]');
         all_plain_toggle_icons.forEach(function(icon) {
             icon.className = 'fas fa-plus combo-toggle-icon';
             icon.style.transform = 'rotate(90deg)';
         });

         // Use event delegation for combo products to handle dynamically added rows
         // Only add listener once using a flag
         if (!window.combo_click_listener_added) {
             window.combo_click_listener_added = true;

             document.addEventListener('click', function(e) {
                 var combo_product = e.target.closest('.clickable-combo-product');
                 if (combo_product) {
                     e.preventDefault();
                     e.stopPropagation();

                     var modal_id = combo_product.getAttribute('data-modal-id');
                     console.log('Combo product clicked, modal ID:', modal_id);

                     if (modal_id) {
                         var modal = document.getElementById(modal_id);
                         console.log('Modal element found:', !!modal);

                         if (modal) {
                             if (typeof window.$ !== 'undefined') {
                                 // Use jQuery modal for consistency
                                 $(modal).modal('show');
                                 console.log('Opening modal with jQuery');
                             } else if (typeof window.bootstrap !== 'undefined') {
                                 // Get or create Bootstrap modal instance
                                 var modal_instance = bootstrap.Modal.getInstance(modal);
                                 if (!modal_instance) {
                                     modal_instance = new bootstrap.Modal(modal);
                                 }
                                 modal_instance.show();
                                 console.log('Opening modal with Bootstrap');
                             }
                         } else {
                             console.log('Modal not found:', modal_id);
                         }
                     } else {
                         console.log('No modal-id found on combo product');
                     }
                 }
             });
         }
     });
 </script>

 {{-- Add click listener for combo products OUTSIDE DOMContentLoaded to handle AJAX-loaded rows --}}
 <script>
     // Only add listener once using a flag
     if (!window.combo_click_listener_added) {
         window.combo_click_listener_added = true;

         document.addEventListener('click', function(e) {
             var combo_product = e.target.closest('.clickable-combo-product');
             if (combo_product) {
                 e.preventDefault();
                 e.stopPropagation();

                 var modal_id = combo_product.getAttribute('data-modal-id');
                 console.log('Combo product clicked, modal ID:', modal_id);

                 if (modal_id) {
                     var modal = document.getElementById(modal_id);
                     console.log('Modal element found:', !!modal);

                     if (modal) {
                         if (typeof window.$ !== 'undefined') {
                             // Use jQuery modal for consistency
                             $(modal).modal('show');
                             console.log('Opening modal with jQuery');
                         } else if (typeof window.bootstrap !== 'undefined') {
                             // Get or create Bootstrap modal instance
                             var modal_instance = bootstrap.Modal.getInstance(modal);
                             if (!modal_instance) {
                                 modal_instance = new bootstrap.Modal(modal);
                             }
                             modal_instance.show();
                             console.log('Opening modal with Bootstrap');
                         }
                     } else {
                         console.log('Modal not found:', modal_id);
                     }
                 } else {
                     console.log('No modal-id found on combo product');
                 }
             }
         });
     }
 </script>

 {{-- Add click listener for plain modifier Edit buttons OUTSIDE DOMContentLoaded to handle AJAX-loaded rows --}}
 <script>
     // Only add listener once using a flag
     if (!window.plain_modifier_click_listener_added) {
         window.plain_modifier_click_listener_added = true;

         document.addEventListener('click', function(e) {
             var edit_button = e.target.closest('.modifier-edit-btn');
             if (edit_button) {
                 e.preventDefault();
                 e.stopPropagation();

                 var row_count = edit_button.getAttribute('data-row-count');
                 console.log('Plain modifier edit button clicked, row_count:', row_count);

                 var modal_selector = '[id^="modifier_' + row_count + '_"]';
                 var modal = document.querySelector(modal_selector);
                 console.log('Looking for modal with selector:', modal_selector);
                 console.log('Modal element found:', !!modal);

                 if (modal) {
                     if (typeof window.$ !== 'undefined') {
                         $(modal).modal('show');
                         console.log('Opening plain modifier modal with jQuery');
                     } else if (typeof window.bootstrap !== 'undefined') {
                         var modal_instance = bootstrap.Modal.getInstance(modal);
                         if (!modal_instance) {
                             modal_instance = new bootstrap.Modal(modal);
                         }
                         modal_instance.show();
                         console.log('Opening plain modifier modal with Bootstrap');
                     }
                 } else {
                     console.log('Modal not found for row:', row_count);
                 }
             }
         });
     }
 </script>
