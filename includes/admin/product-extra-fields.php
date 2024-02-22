<?php
add_action( 'woocommerce_product_options_general_product_data', 'custom_woocommerce_product_enable_optional_purchases_checkbox' );
add_action( 'woocommerce_process_product_meta', 'custom_woocommerce_save_product_enable_optional_purchases_checkbox' );

// Adding fields in the product section
function custom_woocommerce_product_enable_optional_purchases_checkbox() {
    global $woocommerce, $post;

    $enable_optional_purchases = get_post_meta( $post->ID, '_enable_optional_purchases', true );

    ?>
    <div class="options_group">
        <?php
        woocommerce_wp_checkbox(
            array(
                'id'            => '_enable_optional_purchases',
                'label'         => __( 'Enable Optional Purchases', 'woocommerce' ),
                'description'   => __( 'Check this box to enable optional purchases.', 'woocommerce' ),
                'desc_tip'      => true,
                'value'         => $enable_optional_purchases,
            )
        );
        ?>
        <div class="optional-purchases-fields" style="display: <?php echo $enable_optional_purchases === 'yes' ? 'block' : 'none'; ?>;">
            <p class="form-field">
                <label for="optional_purchase_input_label"><?php _e( 'Input Label', 'woocommerce' ); ?></label>
                <input type="text" name="optional_purchase_input_label" id="optional_purchase_input_label" value="<?php echo esc_attr( get_post_meta( $post->ID, 'optional_purchase_input_label', true ) ); ?>">
            </p>
            <p class="form-field">
                <label for="optional_purchase_input_type"><?php _e( 'Input Type', 'woocommerce' ); ?></label>
                <select name="optional_purchase_input_type" id="optional_purchase_input_type">
                    <option value="text" <?php selected( get_post_meta( $post->ID, 'optional_purchase_input_type', true ), 'text' ); ?>><?php _e( 'Text', 'woocommerce' ); ?></option>
                    <option value="number" <?php selected( get_post_meta( $post->ID, 'optional_purchase_input_type', true ), 'number' ); ?>><?php _e( 'Number', 'woocommerce' ); ?></option>
                    <option value="default" <?php selected( get_post_meta( $post->ID, 'optional_purchase_input_type', true ), 'default' ); ?>><?php _e( 'Default (Checkbox)', 'woocommerce' ); ?></option>

                </select>
            </p>
            <p class="form-field">
                <label for="optional_purchase_label_2"><?php _e( 'Additional Price (₹)', 'woocommerce' ); ?></label>
                <input type="number" name="optional_purchase_label_2" id="optional_purchase_label_2" value="<?php echo esc_attr( get_post_meta( $post->ID, 'optional_purchase_label_2', true ) ); ?>">
            </p>
        </div>
    </div>
    <script>
    jQuery(function($) {
        $('#_enable_optional_purchases').change(function() {
            if ($(this).is(':checked')) {
                $('.optional-purchases-fields').show();
            } else {
                $('.optional-purchases-fields').hide();
            }
        }).change();
    });
    </script>
    <?php
}

// Saving the data
function custom_woocommerce_save_product_enable_optional_purchases_checkbox( $post_id ) {
    
    $enable_optional_purchases = isset( $_POST['_enable_optional_purchases'] ) ? 'yes' : 'no';
    
    update_post_meta( $post_id, '_enable_optional_purchases', $enable_optional_purchases );

    if ( isset( $_POST['optional_purchase_input_label'] ) ) {
        update_post_meta( $post_id, 'optional_purchase_input_label', sanitize_text_field( $_POST['optional_purchase_input_label'] ) );
    }

    if ( isset( $_POST['optional_purchase_input_type'] ) ) {
        update_post_meta( $post_id, 'optional_purchase_input_type', sanitize_text_field( $_POST['optional_purchase_input_type'] ) );
    }

    if ( isset( $_POST['optional_purchase_label_2'] ) ) {
        update_post_meta( $post_id, 'optional_purchase_label_2', sanitize_text_field( $_POST['optional_purchase_label_2'] ) );
    }
}
