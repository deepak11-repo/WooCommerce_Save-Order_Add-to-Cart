<?php
add_action('init', 'create_saveorder_table');
add_action('init', 'register_saved_order_endpoint');

// Function to create the table
function create_saveorder_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'saveorder';

    // Check if the table exists, if not, create it
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            product_id int(11) NOT NULL,
            product_name varchar(255) NOT NULL,
            product_price decimal(10,2) NOT NULL,
            quantity int(11) NOT NULL,
            optional_purchase_checked tinyint(1) NOT NULL DEFAULT 0,
            optional_purchase_price decimal(10,2) NOT NULL DEFAULT 0.00,
            optional_purchase_input_label varchar(255) DEFAULT NULL,
            total_price decimal(10,2) NOT NULL,
            created_at timestamp DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY user_id (user_id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        if ($wpdb->last_error) {
            wp_die('Database error encountered: ' . $wpdb->last_error);
        }
    }
}

// Function to register the endpoint
function register_saved_order_endpoint() {
    add_rewrite_endpoint( 'saved-order', EP_ROOT | EP_PAGES );
}
