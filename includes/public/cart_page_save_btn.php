<?php

add_action( 'woocommerce_cart_actions', 'add_save_button_to_cart_actions' );
add_action('woocommerce_cart_actions', 'save_cart_data_to_database');

// Add Save Button to Cart Page
function add_save_button_to_cart_actions() {
    ?>
    <form id="save_cart_form" method="post">
        <button type="submit" name="save_cart_button" class="button alt">Save Cart</button>
    </form>
    <?php
}

// Save the data in the table when form is submitted
function save_cart_data_to_database() {
    if (isset($_POST['save_cart_button'])) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'saveorder';
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;

        $items_to_remove = array();

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $product = $cart_item['data'];
            $product_id = $cart_item['product_id'];
            $product_name = $product->get_name();
            $product_price = $product->get_price();
            $quantity = $cart_item['quantity'];
            $optional_purchase_input_label = isset($cart_item['optional_purchase_input_label']) ? $cart_item['optional_purchase_input_label'] : '';

            // Check if optional purchase is checked
            $optional_purchase_checked = isset($cart_item['optional_purchase_checked']) && $cart_item['optional_purchase_checked'] ? 1 : 0;

            // Calculate total price for the product (without optional purchase) based on quantity
            $product_total_price = $product_price * $quantity;

            // Calculate total price for the optional purchase based on quantity and price per unit
            $optional_purchase_price_per_unit = $optional_purchase_checked ? get_post_meta($product_id, 'optional_purchase_label_2', true) : 0;
            $optional_purchase_total_price = $optional_purchase_price_per_unit * $quantity;

            // Calculate the total price including the product and optional purchase
            $total_price = $product_total_price + $optional_purchase_total_price;

            // Insert cart data into the custom table
            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $user_id,
                    'product_id' => $product_id,
                    'product_name' => $product_name,
                    'product_price' => $product_price,
                    'quantity' => $quantity,
                    'optional_purchase_checked' => $optional_purchase_checked,
                    'optional_purchase_price' => $optional_purchase_price_per_unit, 
                    'optional_purchase_input_label' => $optional_purchase_input_label, 
                    'total_price' => $total_price
                )
            );

            // Add cart item key to items to be removed
            $items_to_remove[] = $cart_item_key;
        }

        // Remove items from the cart
        foreach ($items_to_remove as $cart_item_key) {
            WC()->cart->remove_cart_item($cart_item_key);
        }

        
        if ($result !== false) {
            echo 'Cart data successfully saved.';
        } else {
            echo 'Failed to save cart data.';
        }
        exit;
    }
}

