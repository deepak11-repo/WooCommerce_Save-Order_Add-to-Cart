<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}
global $wpdb;
echo '<h2>' . __( 'Saved Orders', 'woocommerce' ) . '</h2>';
$table_name = $wpdb->prefix . 'saveorder';
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
$saved_orders = $wpdb->get_results( "SELECT * FROM $table_name WHERE user_id = $user_id" );

// Function to Delete an order
if ( isset( $_GET['delete_order_id'] ) ) {
    $order_id_to_delete = intval( $_GET['delete_order_id'] );
    $deleted = $wpdb->delete($table_name, array('id' => $order_id_to_delete, 'user_id' => $user_id));
    if ($deleted) {        
        echo 'Successfully deleted';
        exit;
    } else {
        echo 'Failed to delete the order';
        exit; 
    }
}

//Function to Add to cart an order
if ( isset( $_GET['add_to_cart_id'] ) ) {
    $product_id = intval( $_GET['add_to_cart_id'] );
    foreach ( $saved_orders as $saved_order ) {
        if ( $saved_order->product_id == $product_id ) {
            $cart_item_data = array(
                'quantity' => $saved_order->quantity,
            );
            if ( $saved_order->optional_purchase_checked == 1 ) {
                $cart_item_data['optional_purchase_checked'] = 'yes';

                $optional_purchase_price = get_post_meta($product_id, 'optional_purchase_label_2', true);
                $optional_purchase_input_label = get_post_meta($product_id, 'optional_purchase_input_label', true);

                $cart_item_data['optional_purchase_price'] = $optional_purchase_price;
                $cart_item_data['optional_purchase_input_label'] = $optional_purchase_input_label;
            }

           $added_to_cart = WC()->cart->add_to_cart( $product_id, $saved_order->quantity, 0, array(), $cart_item_data );

           if ( $added_to_cart ) {
               echo 'Product successfully added to cart.';
           } else {
               echo 'Failed to add product to cart.';
           }
           exit;
        }
    }
}

// Creating a saved order table and fetching data from the database
if ( $saved_orders ) {
    echo '<table>';
    echo '<tr><th>' . __( 'Product Name', 'woocommerce' ) . '</th><th>' . __( 'Product Price', 'woocommerce' ) . '</th><th>' . __( 'Optional Purchase Price', 'woocommerce' ) . '</th><th>' . __( 'Quantity', 'woocommerce' ) . '</th><th>' . __( 'Total Price', 'woocommerce' ) . '</th><th>' . __( 'Optional Purchase Input Label', 'woocommerce' ) . '</th><th>' . __( 'Action', 'woocommerce' ) . '</th></tr>';
    foreach ( $saved_orders as $order ) {
        echo '<tr>';
        echo '<td>' . esc_html( $order->product_name ) . '</td>';
        echo '<td>' . wc_price( $order->product_price ) . '</td>';
        echo '<td>' . wc_price( $order->optional_purchase_price ) . '</td>';
        echo '<td>' . esc_html( $order->quantity ) . '</td>';
        echo '<td>' . wc_price( $order->total_price ) . '</td>';    
        echo '<td>' . esc_html( $order->optional_purchase_input_label ) . '</td>';    
        echo '<td>' .
            '<a href="' . esc_url( add_query_arg( array( 'delete_order_id' => $order->id ) ) ) . '">' . __( '❎ ', 'woocommerce' ) . '</a>' .
            '<a href="' . esc_url( add_query_arg( array( 'add_to_cart_id' => $order->product_id ) ) ) . '">' . __( '🛒', 'woocommerce' ) . '</a>' .
        '</td>';  
        echo '</tr>';
    }   
    echo '</table>';
} else {
    echo '<p>' . __( 'No saved orders found.', 'woocommerce' ) . '</p>';
}
