<?php
/*
Plugin Name: WooCommerce Additional Features
Description: This plugin adds extra functionalities to your WooCommerce store, enhancing the user experience and extending the capabilities of your online shop.
Version: 1.0
Author: Deepak
Text Domain: woocommerce-additional-features
*/

//SETUP
/***** create_table &  register_endpoints ******/
require_once plugin_dir_path(__FILE__) . 'activation/setup.php';

//ADMIN SECTION
require_once plugin_dir_path(__FILE__) . 'includes/admin/product-extra-fields.php';

//PUBLIC SECTION

/***** product_page ******/
require_once plugin_dir_path(__FILE__) . 'includes/public/product_page.php';

/***** cart_page_save_btn ******/
require_once plugin_dir_path(__FILE__) . 'includes/public/cart_page_save_btn.php';

/***** my_account_save_link ******/
require_once plugin_dir_path(__FILE__) . 'includes/public/my_account_page.php';


// Enqueue custom CSS file
function enqueue_custom_css() {
    $plugin_url = plugin_dir_url( __FILE__ );
    wp_enqueue_style( 'custom-style', $plugin_url . 'assets/css/style.css');
}
add_action( 'wp_enqueue_scripts', 'enqueue_custom_css' );
