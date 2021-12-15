<?php

/*
Copyright (C) 2016-2017 Pimwick, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or exit;

if ( ! class_exists( 'PW_Gift_Cards_Purchasing' ) ) :

final class PW_Gift_Cards_Purchasing {

    function __construct() {

        add_filter( 'woocommerce_get_price_html', array( $this, 'woocommerce_get_price_html' ), 10, 2 );
        add_filter( 'woocommerce_cart_item_quantity', array( $this, 'woocommerce_cart_item_quantity' ), 10, 3 );
        add_filter( 'woocommerce_dropdown_variation_attribute_options_args', array( $this, 'woocommerce_dropdown_variation_attribute_options_args' ) );
        add_filter( 'woocommerce_add_to_cart_handler', array( $this, 'woocommerce_add_to_cart_handler' ), 10 , 2 );
        add_filter( 'woocommerce_add_cart_item', array( $this, 'woocommerce_add_cart_item' ) );
        add_filter( 'woocommerce_add_cart_item_data', array( $this, 'woocommerce_add_cart_item_data' ), 10, 3 );
        add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'woocommerce_get_cart_item_from_session' ), 10, 2 );
        add_filter( 'woocommerce_get_item_data', array( $this, 'woocommerce_get_item_data' ), 10, 2 );
        add_filter( 'woocommerce_cart_item_permalink', array( $this, 'woocommerce_cart_item_permalink' ), 10, 3 );
        add_filter( 'woocommerce_checkout_create_order_line_item', array( $this, 'woocommerce_checkout_create_order_line_item' ), 10, 4 );

        if ( 'yes' == get_option( 'pwgc_send_when_processing', 'no' ) ) {
            add_filter( 'woocommerce_order_status_processing', array( $this, 'woocommerce_order_status_processing' ), 11, 2 );
            add_action( 'woocommerce_payment_complete', array( $this, 'woocommerce_payment_complete' ) );
            add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'woocommerce_checkout_update_order_meta' ), 11, 2 );
        }

        add_filter( 'woocommerce_order_status_completed', array( $this, 'woocommerce_order_status_completed' ), 11, 2 );
        add_filter( 'woocommerce_order_status_cancelled', array( $this, 'woocommerce_order_status_cancelled' ), 11, 2 );
        add_filter( 'woocommerce_order_status_refunded', array( $this, 'woocommerce_order_status_refunded' ), 11, 2 );
        add_filter( 'wp_trash_post', array( $this, 'order_deleted' ) );
        add_filter( 'untrash_post', array( $this, 'order_restored' ) );
        add_filter( 'woocommerce_order_item_needs_processing', array( $this, 'woocommerce_order_item_needs_processing' ), 10, 3 );
        add_filter( 'woocommerce_order_item_permalink', array( $this, 'woocommerce_order_item_permalink' ), 10, 3 );
        add_filter( 'woocommerce_order_item_display_meta_key', array( $this, 'woocommerce_order_item_display_meta_key' ), 10, 3 );
        add_filter( 'woocommerce_order_item_get_formatted_meta_data', array( $this, 'woocommerce_order_item_get_formatted_meta_data' ), 10, 2 );
        add_action( 'woocommerce_order_again_cart_item_data', array( $this, 'woocommerce_order_again_cart_item_data' ), 10, 3 );
        add_filter( 'woocommerce_product_get_price', array( $this, 'woocommerce_product_get_price' ), 10, 2 );
        add_filter( 'wc_memberships_exclude_product_from_member_discounts', array( $this, 'wc_memberships_exclude_product_from_member_discounts' ), 10, 2 );

        // Disable the AJAX add-to-cart for the PW Gift Cards product on certain themes since it does not correctly add the fields to the cart item data.
        add_filter( 'et_option_ajax_addtocart', array( $this, 'et_option_ajax_addtocart' ) );
        add_filter( 'theme_mod_disable_wc_sticky_cart', array( $this, 'theme_mod_disable_wc_sticky_cart' ) );
        add_filter( 'theme_mod_ocean_woo_product_ajax_add_to_cart', array( $this, 'theme_mod_ocean_woo_product_ajax_add_to_cart' ) );
        add_filter( 'option_rigid', array( $this, 'option_rigid' ), 10, 2 );
    }

    function woocommerce_get_price_html( $price, $product ) {
        if ( is_a( $product, 'WC_Product_Variation' ) && empty( $product->get_price() ) ) {
            $product_id = $product->get_parent_id();
            $parent_product = wc_get_product( $product_id );
            if ( is_a( $parent_product, 'WC_Product_PW_Gift_Card' ) ) {
                $price = '';
            }
        }

        return $price;
    }

    function woocommerce_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
        if ( isset( $cart_item[ PWGC_TO_META_KEY ] ) && strpos( $cart_item[ PWGC_TO_META_KEY ], ',' ) !== false ) {
            return $cart_item['quantity'];
        }

        return $product_quantity;
    }

    function woocommerce_dropdown_variation_attribute_options_args( $args ) {
        if ( $args['product'] && is_a( $args['product'], 'WC_Product_PW_Gift_Card' ) ) {
            $args['show_option_none'] = __( 'Choose an amount', 'pw-woocommerce-gift-cards' );
        }

        return $args;
    }

    function woocommerce_add_to_cart_handler( $product_type, $product ) {
        if ( $product_type == PWGC_PRODUCT_TYPE_SLUG ) {
            return 'variable';
        } else {
            return $product_type;
        }
    }

    function woocommerce_add_cart_item( $cart_item ) {
        global $pw_gift_cards;

        if ( isset( $cart_item[ PWGC_TO_META_KEY ] ) && !empty( $cart_item[ PWGC_TO_META_KEY ] ) ) {
            $recipients = preg_split('/[\s,]+/', $cart_item[ PWGC_TO_META_KEY ], PWGC_RECIPIENT_LIMIT, PREG_SPLIT_NO_EMPTY);
            if ( count( $recipients ) > 1 ) {
                $cart_item['quantity'] = count( $recipients );
            }
        }

        return $cart_item;
    }

    function woocommerce_add_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
        global $pw_gift_cards;

        foreach ( $pw_gift_cards->gift_card_meta as $key => $display ) {
            if ( isset( $_REQUEST[ $key ] ) ) {
                if ( $key == PWGC_MESSAGE_META_KEY ) {
                    $cart_item_data[ $key ] = sanitize_textarea_field( stripslashes( $_REQUEST[ $key ] ) );
                } else {
                    $cart_item_data[ $key ] = sanitize_text_field( stripslashes( $_REQUEST[ $key ] ) );
                }
            }
        }

        return $cart_item_data;
    }

    function woocommerce_get_cart_item_from_session( $cart_item, $values ) {
        global $pw_gift_cards;

        foreach ( $pw_gift_cards->gift_card_meta as $key => $display ) {
            if ( isset( $values[ $key ] ) ) {
                $cart_item[ $key ] = $values[ $key ];
            }
        }

        return $cart_item;
    }

    function woocommerce_get_item_data( $item_data, $cart_item ) {
        global $pw_gift_cards;

        foreach ( $pw_gift_cards->gift_card_meta as $key => $display ) {
            if ( isset( $cart_item[ $key ] ) ) {
                $value = $cart_item[ $key ];
                if ( !empty( $value ) ) {
                    $item_data[] = array(
                        'key' => $display,
                        'value' => $value
                    );
                }
            }
        }

        return $item_data;
    }

    function woocommerce_cart_item_permalink( $product_permalink, $cart_item, $cart_item_key ) {
        global $pw_gift_cards;

        if ( !empty( $product_permalink ) ) {
            foreach ( $pw_gift_cards->gift_card_meta as $key => $display ) {
                if ( strpos( $product_permalink, $key ) === false && isset( $cart_item[ $key ] ) ) {
                    $product_permalink = add_query_arg( $key, urlencode( nl2br( $cart_item[ $key ] ) ), $product_permalink );
                }
            }
        }

        return $product_permalink;
    }

    function woocommerce_checkout_create_order_line_item( $order_item, $cart_item_key, $cart_item, $order ) {
        global $pw_gift_cards;

        $product = wc_get_product( $order_item->get_product_id() );
        if ( is_a( $product, 'WC_Product_PW_Gift_Card' ) ) {
            foreach ( $pw_gift_cards->gift_card_meta as $key => $display ) {
                if ( $key == PWGC_AMOUNT_META_KEY ) {

                    // Amount on the gift card is always the regular price, regardless of sales.
                    $price = $cart_item['data']->get_regular_price();

                    // WooCommerce Price Based on Country by Oscar Gare
                    if ( class_exists( 'WCPBC_Pricing_Zones' ) ) {
                        // This plugin uses it's own meta_data value so we have to get it the old fashioned way.
                        $price = get_post_meta( $cart_item['data']->get_id(), '_regular_price', true );
                    }

                    // WooCommerce Ultimate Multi Currency Suite
                    if ( class_exists( 'WooCommerce_Ultimate_Multi_Currency_Suite_Main' ) && isset( $GLOBALS['woocommerce_ultimate_multi_currency_suite'] ) ) {
                        $cs = $GLOBALS['woocommerce_ultimate_multi_currency_suite'];
                        if ( !empty( $cs->frontend ) ) {
                            remove_filter('woocommerce_product_variation_get_regular_price', array($cs->frontend, 'custom_item_price'), 9999, 2);
                            $price = $cart_item['data']->get_regular_price();
                            add_filter('woocommerce_product_variation_get_regular_price', array($cs->frontend, 'custom_item_price'), 9999, 2);
                        }
                    }

                    if (
                        // Multi Currency for WooCommerce by VillaTheme
                        function_exists( 'wmc_get_price' )

                        // Multi-Currency for WooCommerce by TIV.NET INC
                        || class_exists( 'WOOMC\App' )

                        // WooCommerce Currency Switcher by realmag777
                        || isset( $GLOBALS['WOOCS'] )

                            // Currency Switcher for WooCommerce by WP Wham
                            || function_exists( 'alg_get_current_currency_code' )
                    ) {
                        $price = apply_filters( 'pwgc_to_default_currency', $price );
                    }

                    $order_item->add_meta_data( $key, $price );
                } else if ( isset( $cart_item[ $key ] ) ) {
                    $order_item->add_meta_data( $key, $cart_item[ $key ] );
                }
            }
        }
    }

    function woocommerce_order_status_processing( $order_id, $order ) {
        if ( $order->is_paid() ) {
            $this->add_gift_cards_to_order( $order_id, $order, "order_id: $order_id completed" );
        }
    }

    function woocommerce_payment_complete( $order_id ) {
        $order = wc_get_order( $order_id );
        if ( $order->is_paid() ) {
            $this->add_gift_cards_to_order( $order_id, $order, "order_id: $order_id completed" );
        }
    }

    function woocommerce_checkout_update_order_meta( $order_id, $data ) {
        $order = wc_get_order( $order_id );
        if ( $order->is_paid() ) {
            $this->add_gift_cards_to_order( $order_id, $order, "order_id: $order_id created" );
        }
    }

    function woocommerce_order_status_completed( $order_id, $order ) {
        $this->add_gift_cards_to_order( $order_id, $order, "order_id: $order_id completed" );
    }

    function woocommerce_order_status_cancelled( $order_id, $order ) {
        $this->deactivate_gift_cards_from_order( $order_id, $order, "order_id: $order_id cancelled" );
    }

    function woocommerce_order_status_refunded( $order_id, $order ) {
        $this->deactivate_gift_cards_from_order( $order_id, $order, "order_id: $order_id refunded" );
    }

    function order_deleted( $id ) {
        global $post_type;

        if ( $post_type !== 'shop_order' ) {
            return;
        }

        $order = wc_get_order( $id );
        if ( $order ) {
            $this->deactivate_gift_cards_from_order( $id, $order, "order_id: $id deleted" );
        }
    }

    function order_restored( $id ) {
        global $post_type;

        if ( $post_type !== 'shop_order' ) {
            return;
        }

        $order = wc_get_order( $id );
        if ( $order ) {
            $this->add_gift_cards_to_order( $id, $order, "order_id: $id restored" );
        }
    }

    function add_gift_cards_to_order( $order_id, $order, $note ) {

        $create_note = sprintf( __( 'Order %s purchased by %s %s', 'pw-woocommerce-gift-cards' ), $order->get_id(), $order->get_billing_first_name(), $order->get_billing_last_name() );

        foreach ( $order->get_items( 'line_item' ) as $order_item_id => $order_item ) {

            // Make sure we have a quantity (should always be true, right? Oh well, prevents a divide-by-zero error just in case).
            if ( $order_item->get_quantity() <= 0 ) {
                continue;
            }

            // Get the product.
            $product_id = absint( $order_item['product_id'] );
            if ( !( $product = wc_get_product( $product_id ) ) ) {
                continue;
            }

            // We're only interested in these guys.
            if ( !is_a( $product, 'WC_Product_PW_Gift_Card' ) ) {
                continue;
            }

            // Grab the Variation, otherwise there will be trouble.
            $variation_id = absint( $order_item['variation_id'] );
            if ( !( $variation = wc_get_product( $variation_id ) ) ) {
                wp_die( __( 'Unable to retrieve variation ', 'pw-woocommerce-gift-cards' ) . $variation_id );
            }

            $credit_amount = wc_get_order_item_meta( $order_item_id, PWGC_AMOUNT_META_KEY );
            if ( !is_numeric( $credit_amount ) || empty( $credit_amount ) ) {

                // Previously we didn't store the PWGC_AMOUNT_META_KEY so we need to calculate based on purchase price.
                $credit_amount = round( $order_item->get_subtotal() / $order_item->get_quantity(), wc_get_price_decimals() );
                if ( !is_numeric( $credit_amount ) || empty( $credit_amount ) ) {
                    continue;
                }
            }

            if (
                // WooCommerce Currency Switcher by realmag777
                !isset( $GLOBALS['WOOCS'] )

                // Currency Switcher for WooCommerce by WP Wham
                && !function_exists( 'alg_get_current_currency_code' )

                // Price Based on Country for WooCommerce by Oscar Gare
                && !class_exists( 'WCPBC_Pricing_Zones' )
            ) {
                $credit_amount = apply_filters( 'pwgc_to_default_currency', $credit_amount );
            }
            $item_note = $note . ", order_item_id: $order_item_id";

            // Create a gift card for each quantity ordered.
            $gift_card_numbers = (array) wc_get_order_item_meta( $order_item_id, PWGC_GIFT_CARD_NUMBER_META_KEY, false );

            // Make sure any existing gift cards are activated.
            foreach ( $gift_card_numbers as $gift_card_number ) {
                $gift_card = new PW_Gift_Card( $gift_card_number );
                $gift_card->reactivate( $item_note );
            }

            // Create any new/missing gift cards.
            for ( $x = count( $gift_card_numbers ); $x < $order_item['quantity']; $x++ ) {

                $gift_card = PW_Gift_Card::create_card( $create_note );
                $gift_card->credit( $credit_amount, $item_note );

                wc_add_order_item_meta( $order_item_id, PWGC_GIFT_CARD_NUMBER_META_KEY, $gift_card->get_number() );
            }
        }

        do_action( 'pw_gift_cards_send_emails', $order_id );
    }

    function deactivate_gift_cards_from_order( $order_id, $order, $note ) {
        foreach ( $order->get_items( 'line_item' ) as $order_item_id => $order_item ) {
            $item_note = $note . ", order_item_id: $order_item_id";

            $gift_card_numbers = (array) wc_get_order_item_meta( $order_item_id, PWGC_GIFT_CARD_NUMBER_META_KEY, false );
            foreach ( $gift_card_numbers as $gift_card_number ) {
                $gift_card = new PW_Gift_Card( $gift_card_number );
                $gift_card->deactivate( $item_note );
            }
        }
    }

    function woocommerce_order_item_needs_processing( $needs_processing, $product, $order_item_id ) {
        if ( is_a( $product, 'WC_Product_Variation' ) ) {
            if ( 'yes' === get_option( 'pwgc_autocomplete_gift_card_orders', 'yes' ) ) {
                $product_id = $product->get_parent_id();
                $parent_product = wc_get_product( $product_id );
                if ( is_a( $parent_product, 'WC_Product_PW_Gift_Card' ) ) {
                    $needs_processing = false;
                }
            }
        }

        return $needs_processing;
    }

    function woocommerce_order_item_permalink( $product_permalink, $order_item, $order ) {
        global $pw_gift_cards;

        if ( !empty( $product_permalink ) ) {
            $product = wc_get_product( $order_item->get_product_id() );
            if ( is_a( $product, 'WC_Product_PW_Gift_Card' ) ) {

                foreach ( $pw_gift_cards->gift_card_meta as $key => $display ) {
                    if ( strpos( $product_permalink, $key ) === false && isset( $order_item[ $key ] ) ) {
                        $product_permalink = add_query_arg( $key, urlencode( $order_item[ $key ] ), $product_permalink );
                    }
                }
            }
        }

        return $product_permalink;
    }

    function woocommerce_order_item_display_meta_key( $display_key, $meta_data, $order_item ) {
        switch ( $display_key ) {
            case PWGC_GIFT_CARD_NUMBER_META_KEY:
                $display_key = PWGC_GIFT_CARD_NUMBER_META_DISPLAY_NAME;

                $gift_card = new PW_Gift_Card( $meta_data->value );
                if ( !$gift_card->get_active() ) {
                    $display_key .= __( ' (inactive)', 'pw-woocommerce-gift-cards' );
                }
            break;
        }

        return $display_key;
    }

    function woocommerce_order_item_get_formatted_meta_data( $formatted_meta, $order_item ) {
        if ( is_admin() && is_a( $order_item, 'WC_Order_Item_Product' ) && !empty( $order_item->get_product_id() ) ) {
            $product = wc_get_product( $order_item->get_product_id() );
            if ( is_a( $product, 'WC_Product_PW_Gift_Card' ) ) {
                $has_gift_card_number = false;
                foreach ( $formatted_meta as $id => $meta ) {
                    if ( $meta->key == PWGC_GIFT_CARD_NUMBER_META_KEY ) {
                        $has_gift_card_number = true;
                        break;
                    }
                }

                if ( !$has_gift_card_number ) {
                    $meta = new stdClass();
                    $meta->key = PWGC_GIFT_CARD_NUMBER_META_KEY . '_placeholder';
                    $meta->value = false;
                    $meta->display_key = PWGC_GIFT_CARD_NUMBER_META_DISPLAY_NAME;
                    $meta->display_value = '<i>' . __( 'Generated and emailed after the order is marked Complete.', 'pw-woocommerce-gift-cards' ) . '</i>';
                    $formatted_meta[] = $meta;
                }
            }
        }

        if ( PWGC_HIDE_AMOUNT_META ) {
            foreach ( $formatted_meta as $id => $meta ) {
                if ( property_exists( $meta, 'key' ) && $meta->key === PWGC_AMOUNT_META_KEY ) {
                    if ( is_admin() ) {
                        $meta->display_value = '<p>' . round( $meta->value, wc_get_price_decimals() ) . ' ' . get_option( 'woocommerce_currency' ) . '</p>';
                    } else {
                        unset( $formatted_meta[ $id ] );
                    }
                }
            }
        }

        return $formatted_meta;
    }

    function woocommerce_order_again_cart_item_data( $cart_item_data, $order_item, $order ) {
        global $pw_gift_cards;

        foreach ( $pw_gift_cards->gift_card_meta as $key => $display ) {
            if ( isset( $order_item[ $key ] ) ) {
                if ( $key == PWGC_MESSAGE_META_KEY ) {
                    $cart_item_data[ $key ] = sanitize_textarea_field( stripslashes( $order_item[ $key ] ) );
                } else {
                    $cart_item_data[ $key ] = sanitize_text_field( stripslashes( $order_item[ $key ] ) );
                }
            }
        }

        return $cart_item_data;
    }

    function woocommerce_product_get_price( $value, $product ) {
        if ( is_a( $product, 'WC_Product_PW_Gift_Card' ) && '' === $value ) {
            return '0';
        } else {
            return $value;
        }
    }

    function wc_memberships_exclude_product_from_member_discounts( $excluded, $product ) {
        if ( ! (bool) $excluded ) {
            if ( is_numeric( $product ) && $product > 0 ) {
                $product = wc_get_product( $product );
            }

            $product_id = !empty( $product->get_parent_id() ) ? $product->get_parent_id() : $product->get_id();
            $product =  wc_get_product( $product_id );

            if ( is_a( $product, 'WC_Product_PW_Gift_Card' ) && function_exists( 'wc_memberships' ) ) {
                $memberships = wc_memberships();
                if ( method_exists( $memberships, 'get_member_discounts_instance' ) ) {
                    $discounts = $memberships->get_member_discounts_instance();

                    if ( method_exists( $discounts, 'get_products_excluded_from_member_discounts' ) && method_exists( $discounts, 'excluding_on_sale_products_from_member_discounts' ) && method_exists( $discounts, 'product_is_on_sale_before_discount' ) ) {
                        $exclude_product = in_array( $product_id, $discounts->get_products_excluded_from_member_discounts(), false );
                        $exclude_on_sale = ! $exclude_product ? $discounts->excluding_on_sale_products_from_member_discounts() && $discounts->product_is_on_sale_before_discount( $product ) : false;

                        $excluded = $exclude_product || $exclude_on_sale;
                    }
                }
            }
        }

        return $excluded;
    }

    function et_option_ajax_addtocart( $value ) {
        global $product;

        if ( !empty( $product ) ) {
            if ( is_a( $product, 'WC_Product_PW_Gift_Card' ) ) {
                return false;
            }
        }

        return $value;
    }

    function theme_mod_disable_wc_sticky_cart( $value ) {
        global $product;

        if ( !empty( $product ) ) {
            if ( is_a( $product, 'WC_Product_PW_Gift_Card' ) ) {
                return 1;
            }
        }

        return $value;
    }

    function theme_mod_ocean_woo_product_ajax_add_to_cart( $value ) {
        global $post;

        if ( !empty( $post ) ) {
            $product = wc_get_product( $post->ID );
            if ( is_a( $product, 'WC_Product_PW_Gift_Card' ) ) {
                return false;
            }
        }

        return $value;
    }

    function option_rigid( $value, $option ) {
        global $post;

        if ( !empty( $post ) ) {
            $product = wc_get_product( $post->ID );
            if ( is_a( $product, 'WC_Product_PW_Gift_Card' ) ) {
                if ( !empty( $value ) && is_array( $value ) ) {
                    if ( isset( $value['ajax_to_cart_single'] ) && true === boolval( $value['ajax_to_cart_single'] ) ) {
                        $value['ajax_to_cart_single'] = 0;
                    }
                }
            }
        }

        return $value;
    }
}

global $pw_gift_cards_purchasing;
$pw_gift_cards_purchasing = new PW_Gift_Cards_Purchasing();

endif;
