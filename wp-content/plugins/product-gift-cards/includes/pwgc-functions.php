<?php

defined( 'ABSPATH' ) or exit;

if ( ! function_exists( 'pwgc_get_designs' ) ) {
    function pwgc_get_designs() {
        global $pw_gift_cards;

        $designs = maybe_unserialize( get_option( 'pw_gift_card_designs_free', array() ) );
        if ( empty( $designs ) ) {
            return $pw_gift_cards->default_designs;
        }

        return $designs;
    }
}

if ( ! function_exists( 'pwgc_redeem_url' ) ) {
    function pwgc_redeem_url( $item_data ) {
        $shop_url = get_permalink( wc_get_page_id( 'shop' ) );
        if ( empty( $shop_url ) ) {
            $shop_url = site_url();
        }
        $redeem_url = add_query_arg( 'pw_gift_card_number', urlencode( $item_data->gift_card_number ), $shop_url );

        return apply_filters( 'pwgc_redeem_url', $redeem_url, $item_data );
    }
}

if ( ! function_exists( 'pwgc_color_picker_field' ) ) {
    function pwgc_color_picker_field( $design, $key, $label ) {
        global $pw_gift_cards;

        if ( !empty( $design[ $key ] ) ) {
            $color = $design[ $key ];
        } else {
            $color = get_option( 'woocommerce_email_text_color', '#3c3c3c' );
        }
        $id = 'pwgc-designer-' . str_replace( '_', '-', $key );

        $preview_element = $pw_gift_cards->design_colors[ $key ][0];
        $preview_element_css = $pw_gift_cards->design_colors[ $key ][1];

        ?>
        <p class="form-field">
            <label class="pwgc-designer-label"><?php echo $label; ?></label>
            <input type="text" name="<?php echo $key; ?>" id="<?php echo $id; ?>" value="<?php echo $color; ?>" style="color: <?php echo $color; ?>; background-color: <?php echo $color; ?>; max-width: 75px;">
        </p>
        <script>
            jQuery(function() {
                pwgcAssignColorPicker('#<?php echo $id; ?>', '<?php echo $preview_element; ?>', '<?php echo $preview_element_css; ?>');
            });
        </script>
        <?php
    }
}

if ( ! function_exists( 'pwgc_dashboard_helper' ) ) {
    // Optionally set the selected CSS for the appropriate section.
    function pwgc_dashboard_helper( $item, $output = 'pwgc-dashboard-item-selected' ) {
        $selected = false;
        if ( isset( $_REQUEST['section'] ) ) {
            $selected = ( $_REQUEST['section'] == $item );
        } else if ( $item == 'balances' ) {
            $selected = true;
        }

        echo ( $selected ) ? $output : '';
    }
}

if ( ! function_exists( 'pwgc_paypal_ipn_pdt_bug_exists' ) ) {
    function pwgc_paypal_ipn_pdt_bug_exists() {
        $bug_exists = false;
        $ipn_enabled = false;
        $pdt_enabled = false;
        $woocommerce_paypal_settings = get_option( 'woocommerce_paypal_settings' );

        if ( empty( $woocommerce_paypal_settings['ipn_notification'] ) || 'no' !== $woocommerce_paypal_settings['ipn_notification'] ) {
            $ipn_enabled = true;
        }

        if ( ! empty( $woocommerce_paypal_settings['identity_token'] ) ) {
            $pdt_enabled = true;
        }

        if ( $ipn_enabled && $pdt_enabled ) {
            $bug_exists = true;
        }

        return apply_filters( 'pwgc_paypal_ipn_pdt_bug_exists', $bug_exists );
    }
}
