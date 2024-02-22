<?php

if (!session_id()) {
    session_start();
}

add_action('woocommerce_before_add_to_cart_button', 'custom_display_optional_purchase_section');
add_action('woocommerce_add_to_cart', 'update_optional_purchase_session', 10, 3);
add_action('woocommerce_cart_calculate_fees', 'add_custom_fee', 10, 1);

function custom_display_optional_purchase_section() {
    global $product;
    $product_id = $product->get_id();
    $enable_optional_purchases = get_post_meta($product_id, '_enable_optional_purchases', true);
    $input_type = get_post_meta($product_id, 'optional_purchase_input_type', true);
    $input_label = get_post_meta($product_id, 'optional_purchase_input_label', true);
    $additional_price = get_post_meta($product_id, 'optional_purchase_label_2', true);

    if ($enable_optional_purchases === 'yes' && !empty($input_type) && !empty($input_label)) {
        echo '<div class="optional-purchase-section">';
        echo '<div class="optional-purchase-content">';
        echo '<div class="optional-purchase-checkbox">';
        echo '<input type="checkbox" class="custom-checkbox" id="optional_purchase_checkbox" name="optional_purchase_checkbox"';
        if (isset($_POST['optional_purchase_checkbox']) && $_POST['optional_purchase_checkbox'] === 'on') {
            echo ' checked="checked"';
        }
        echo '>';
        echo '<label for="optional_purchase_checkbox" class="optional-purchase-label">' . esc_html($input_label) . '</label>';
        echo '</div>';
        echo '<span class="additional-price">' . (!empty($additional_price) ? '(₹' . esc_html($additional_price) . ')' : '') . '</span>';
        echo '</div>';

        echo '<div id="optional-purchase-input-container">';
        if ($input_type === 'text' || $input_type === 'number') {
            echo '<input type="' . esc_attr($input_type) . '" name="optional_purchase_input" id="optional_purchase_input" value="">';
        }
        echo '</div>';
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                var checkbox = document.getElementById("optional_purchase_checkbox");
                var inputContainer = document.getElementById("optional-purchase-input-container");
    
                checkbox.addEventListener("change", function() {
                    if (this.checked) {
                        inputContainer.style.display = "block";
                    } else {
                        inputContainer.style.display = "none";
                    }
                });
            });
        </script>';    
        echo '</div>';
    }   
    
}   

function update_optional_purchase_session($cart_item_key, $product_id, $quantity) {
    if (isset($_POST['optional_purchase_checkbox']) && $_POST['optional_purchase_checkbox'] === 'on') {
        // Set optional purchase checked to true
        WC()->cart->cart_contents[$cart_item_key]['optional_purchase_checked'] = 'yes';
        
        // Get optional purchase details from post meta
        $optional_purchase_price = get_post_meta($product_id, 'optional_purchase_label_2', true);
        $optional_purchase_input_label = get_post_meta($product_id, 'optional_purchase_input_label', true);

        // Include optional purchase details in cart item data
        WC()->cart->cart_contents[$cart_item_key]['optional_purchase_price'] = $optional_purchase_price;
        WC()->cart->cart_contents[$cart_item_key]['optional_purchase_input_label'] = $optional_purchase_input_label;

    } elseif (isset(WC()->cart->cart_contents[$cart_item_key]['optional_purchase_price']) && isset(WC()->cart->cart_contents[$cart_item_key]['optional_purchase_input_label'])) {
        error_log('meta data already exists');
    } else {
        // Set optional purchase checked to false
        WC()->cart->cart_contents[$cart_item_key]['optional_purchase_checked'] = false;
        // Remove optional purchase details from cart item data
        unset(WC()->cart->cart_contents[$cart_item_key]['optional_purchase_price']);
        unset(WC()->cart->cart_contents[$cart_item_key]['optional_purchase_input_label']);
    }
}

function add_custom_fee($cart) {
    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        if (isset($cart_item['optional_purchase_checked']) && $cart_item['optional_purchase_checked'] == 'yes') {
            // Get product ID and quantity
            $product_id = $cart_item['product_id'];
            $quantity = $cart_item['quantity'];

            // Get optional purchase price and input label from post meta
            $optional_purchase_label_2 = get_post_meta($product_id, 'optional_purchase_label_2', true);
            $input_label = get_post_meta($product_id, 'optional_purchase_input_label', true);

            // Unique fee identifier based on product ID
            $fee_identifier = $input_label . '_' . $product_id;

            // Add custom fee based on optional purchase details and quantity
            if ($optional_purchase_label_2) {
                $cart->add_fee(__($fee_identifier, 'woocommerce'), $optional_purchase_label_2 * $quantity);
            }
        } else {
            error_log('Error Log');
        }
    }
}
