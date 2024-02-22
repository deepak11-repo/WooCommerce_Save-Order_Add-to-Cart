<?php
// Define the endpoint variable
$endpoint = 'saved-order';

add_filter( 'woocommerce_account_menu_items', 'add_custom_account_menu_item', 10, 1 );
add_action( 'woocommerce_account_' . $endpoint .  '_endpoint', 'saved_order_endpoint_content' );

//Add a new link in my account menu items
function add_custom_account_menu_item( $items ) {
    $items['saved-order'] = __( 'Saved Order', 'woocommerce' );
    return $items;
}

// Handle endpoint content
function saved_order_endpoint_content() {
    include_once( plugin_dir_path( __FILE__ ) . 'my_account_saved_order_table.php' ); 
}
