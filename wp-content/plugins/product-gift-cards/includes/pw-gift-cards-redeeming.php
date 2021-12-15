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

if ( ! class_exists( 'PW_Gift_Cards_Redeeming' ) ) :

final class PW_Gift_Cards_Redeeming {

    function __construct() {
        $redeem_hook_priority = 40;

        // Fixes a conflict with the 'WooCommerce Extended Coupon Features PRO' plugin.
        if ( class_exists( 'WJECF_AutoCoupon' ) && !defined( 'PWGC_BYPASS_FIX_FOR_WJECF' ) ) {
            $redeem_hook_priority = 20;
        }

        // Fixes a conflict with the 'WooCommerce AvaTax' plugin by SkyVerge.
        if ( class_exists( 'WC_AvaTax_Checkout_Handler' ) && !defined( 'PWGC_BYPASS_FIX_FOR_AVATAX' ) ) {
            $redeem_hook_priority = 1000;
            add_action( 'wc_avatax_before_checkout_tax_calculated', array( $this, 'wc_avatax_before_checkout_tax_calculated' ) );
            add_action( 'wc_avatax_after_checkout_tax_calculated', array( $this, 'wc_avatax_after_checkout_tax_calculated' ) );
        }

        // Fixes the totals shown on the Checkout page when using Country Based Restrictions for WooCommerce by zorem
        if ( class_exists( 'ZH_Product_Country_Restrictions' ) && !defined( 'PWGC_BYPASS_FIX_FOR_FZPCR' ) ) {
            add_action( 'woocommerce_review_order_before_cart_contents', array( $this, 'fzpcr_fix' ), 20 );
        }

        // Fixes a conflict with the 'Advanced Dynamic Pricing' plugin by AlgolPlus
        add_filter( 'wdp_calculate_totals_hook_priority', function( $priority ) use ( $redeem_hook_priority ) { return $redeem_hook_priority - 1; });

        add_action( 'woocommerce_before_checkout_form', array( $this, 'woocommerce_before_checkout_form' ), 40 );
        add_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'woocommerce_cart_totals_before_order_total' ) );
        add_action( 'woocommerce_review_order_before_order_total', array( $this, 'woocommerce_review_order_before_order_total' ) );
        add_action( 'woocommerce_review_order_before_submit', array( $this, 'woocommerce_review_order_before_submit' ) );
        add_action( 'woocommerce_after_calculate_totals', array( $this, 'woocommerce_after_calculate_totals' ), $redeem_hook_priority );
        add_action( 'woocommerce_update_order', array( $this, 'woocommerce_update_order' ) );
        add_action( 'woocommerce_order_after_calculate_totals', array( $this, 'woocommerce_order_after_calculate_totals' ), 10, 2 );
        add_action( 'woocommerce_pre_payment_complete', array( $this, 'woocommerce_pre_payment_complete' ) );
        add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'woocommerce_checkout_update_order_meta' ), 10, 2 );
        add_action( 'woocommerce_order_status_processing', array( $this, 'woocommerce_order_status_processing' ) );
        add_action( 'woocommerce_order_status_pre-ordered', array( $this, 'woocommerce_order_status_preordered' ) );
        add_action( 'woocommerce_order_status_completed', array( $this, 'woocommerce_order_status_completed' ) );
        add_action( 'woocommerce_order_status_cancelled', array( $this, 'woocommerce_order_status_cancelled' ) );
        add_action( 'woocommerce_order_status_refunded', array( $this, 'woocommerce_order_status_refunded' ) );
        add_action( 'woocommerce_order_status_failed', array( $this, 'woocommerce_order_status_failed' ) );
        add_filter( 'woocommerce_get_order_item_totals', array( $this, 'woocommerce_get_order_item_totals' ), 10, 3 );
        add_action( 'woocommerce_checkout_create_order', array( $this, 'woocommerce_checkout_create_order' ), 10, 2 );
        add_action( 'woocommerce_proceed_to_checkout', array( $this, 'woocommerce_proceed_to_checkout' ) );
        add_action( 'woocommerce_after_cart_contents', array( $this, 'woocommerce_after_cart_contents' ) );
        add_filter( 'woocommerce_paypal_args', array( $this, 'woocommerce_paypal_args' ), 10, 2 );
        add_filter( 'wc_paytrail_payment_params', array( $this, 'wc_paytrail_payment_params' ), 10, 3 );

        add_action( 'wp_ajax_nopriv_pw-gift-cards-redeem', array( $this, 'ajax_redeem' ) );
        add_action( 'wp_ajax_pw-gift-cards-redeem', array( $this, 'ajax_redeem' ) );

        add_action( 'wp_ajax_nopriv_pw-gift-cards-remove', array( $this, 'ajax_remove' ) );
        add_action( 'wp_ajax_pw-gift-cards-remove', array( $this, 'ajax_remove' ) );

        add_filter( 'woocommerce_get_shop_coupon_data', array( $this, 'woocommerce_get_shop_coupon_data' ), 10, 2 );
        add_action( 'woocommerce_applied_coupon', array( $this, 'woocommerce_applied_coupon' ) );
        add_filter( 'woocommerce_apply_with_individual_use_coupon', array( $this, 'woocommerce_apply_with_individual_use_coupon' ), 10, 4 );
        add_filter( 'alg_wc_oma_amount_cart_total', array( $this, 'alg_wc_oma_amount_cart_total' ), 10, 2 );
        add_filter( 'alg_wc_order_minimum_amount_message', array( $this, 'alg_wc_order_minimum_amount_message' ), 10, 3 );
    }

    function woocommerce_before_checkout_form() {
        wp_enqueue_script( 'pw-gift-cards' );
        wc_get_template( 'checkout/coupon-area-pw-gift-card.php', array(), '', PWGC_PLUGIN_ROOT . 'templates/woocommerce/' );
    }

    function woocommerce_cart_totals_before_order_total() {
        wp_enqueue_script( 'pw-gift-cards' );
        wc_get_template( 'cart/cart-pw-gift-card.php', array(), '', PWGC_PLUGIN_ROOT . 'templates/woocommerce/' );
    }

    function woocommerce_review_order_before_order_total() {
        wp_enqueue_script( 'pw-gift-cards' );
        wc_get_template( 'checkout/pw-gift-cards.php', array(), '', PWGC_PLUGIN_ROOT . 'templates/woocommerce/' );
    }

    function woocommerce_review_order_before_submit() {
        wp_enqueue_script( 'pw-gift-cards' );
        wc_get_template( 'checkout/payment-method-pw-gift-card.php', array(), '', PWGC_PLUGIN_ROOT . 'templates/woocommerce/' );
    }

    function wc_avatax_before_checkout_tax_calculated() {
        remove_action( 'woocommerce_after_calculate_totals', array( $this, 'woocommerce_after_calculate_totals' ), 1000 );
    }

    function wc_avatax_after_checkout_tax_calculated() {
        $this->woocommerce_after_calculate_totals( WC()->cart );
        add_action( 'woocommerce_after_calculate_totals', array( $this, 'woocommerce_after_calculate_totals' ), 1000 );
    }

    function woocommerce_after_calculate_totals( $cart ) {

        // The "recurring cart" for WooCommerce Subscriptions should not be adjusted.
        if ( property_exists( $cart, 'recurring_cart_key' ) ) {
            return;
        }

        $session_data = (array) WC()->session->get( PWGC_SESSION_KEY );
        if ( !isset( $session_data['gift_cards'] ) ) {
            return;
        }

        if ( property_exists( $cart, 'pwgc_calculated_total' ) ) {
            //
            // There is a conflict with PPOM for WooCommerce (https://wordpress.org/plugins/woocommerce-product-addon/)
            // and WooCommerce Multilingual (https://wordpress.org/plugins/woocommerce-multilingual/)
            // When a shipping method changes, the cart should be recalculated but PPOM forces the method to run a
            // second time. If PPOM is installed, we will always recalculate the cart totals to fix the issue.
            //
            if ( !defined( 'PPOM_VERSION' ) && !defined( 'WCML_VERSION' ) ) {
                $cart->total = $cart->pwgc_calculated_total;
                return;
            }
        }

        // Prevent gift cards from buying gift cards if not allowed.
        if ( $this->cart_contains_gift_card() && 'yes' !== get_option( 'pwgc_allow_gift_card_purchasing', 'yes' ) ) {
            unset( $session_data['gift_cards'] );
            WC()->session->set( PWGC_SESSION_KEY, $session_data );
            return;
        }

        // This is where we could optionally exclude Gift Cards, Shipping amounts, etc.
        $eligible_amount = apply_filters( 'pwgc_eligible_cart_amount', $cart->total, $cart );

        // Sum all the gift card amounts (with a sanity check for good measure).
        $gift_card_total = 0;
        foreach ( $session_data['gift_cards'] as $card_number => $amount ) {
            $pw_gift_card = new PW_Gift_Card( $card_number );
            if ( $pw_gift_card->get_id() ) {

                $gift_card_blocked = apply_filters( 'pwgc_gift_card_blocked', false, $card_number );

                $amount = 0;
                if ( !$pw_gift_card->has_expired() && !$gift_card_blocked) {
                    $gift_card_balance = apply_filters( 'pwgc_to_current_currency', $pw_gift_card->get_balance() );
                    if ( $gift_card_balance < ( $eligible_amount - $gift_card_total ) ) {
                        $amount = $gift_card_balance;
                    } else {
                        $amount = ( $eligible_amount - $gift_card_total );
                    }
                }

                $session_data['gift_cards'][ $card_number ] = $amount;
                $gift_card_total += $amount;
            }

            if ( $gift_card_total >= $eligible_amount ) {
                break;
            }
        }

        // Keep the original total before the gift card was applied.
        $cart->pwgc_total_before_gift_cards = $cart->total;
        $cart->pwgc_total_gift_cards_redeemed = $gift_card_total;

        // Make sure we don't set the cart to a negative amount.
        $new_cart_total = ( $cart->total - $gift_card_total );
        $cart->total = max( 0, $new_cart_total );
        $cart->pwgc_calculated_total = $cart->total;

        WC()->session->set( PWGC_SESSION_KEY, $session_data );
    }

    function calculate_order_total( $order ) {

        $cart_total = 0;
        $fees_total = 0;

        foreach ( $order->get_items() as $item ) {
            $cart_total += $item->get_total();
        }

        foreach ( $order->get_fees() as $item ) {
            $fee_total = $item->get_total();

            if ( 0 > $fee_total ) {
                $max_discount = round( $cart_total + $fees_total + $shipping_total, wc_get_price_decimals() ) * -1;

                if ( $fee_total < $max_discount ) {
                    $item->set_total( $max_discount );
                }
            }

            $fees_total += $item->get_total();
        }

        $new_total = round( $cart_total + $fees_total + $order->get_shipping_total() + $order->get_cart_tax() + $order->get_shipping_tax(), wc_get_price_decimals() );

        return $new_total;
    }

    // Ensure we have the right total, even after recalculations and such.
    function woocommerce_update_order( $order_id ) {
        remove_action( 'woocommerce_update_order', array( $this, 'woocommerce_update_order' ) );

        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            return;
        }

        $gift_card_total = 0;

        foreach( $order->get_items( 'pw_gift_card' ) as $line ) {
            $gift_card_total += apply_filters( 'pwgc_to_order_currency', $line->get_amount(), $order );
        }

        if ( $gift_card_total > 0 ) {

            $order_total = $this->calculate_order_total( $order );
            $order->set_total( max( 0, $order_total - $gift_card_total ) );
            $order->save();
        }

        add_action( 'woocommerce_update_order', array( $this, 'woocommerce_update_order' ) );
    }

    function woocommerce_order_after_calculate_totals( $and_taxes, $order ) {
        $gift_card_total = 0;

        foreach( $order->get_items( 'pw_gift_card' ) as $line ) {
            $gift_card_total += apply_filters( 'pwgc_to_order_currency', $line->get_amount(), $order );
        }

        if ( $gift_card_total > 0 ) {
            $order->set_total( max( 0, $order->get_total() - $gift_card_total ) );
        }
    }

    function woocommerce_pre_payment_complete( $order_id ) {
        $order = wc_get_order( $order_id );
        $this->debit_gift_cards( $order_id, $order, "order_id: $order_id processing" );
    }

    function woocommerce_checkout_update_order_meta( $order_id, $data ) {
        $order = wc_get_order( $order_id );
        $this->debit_gift_cards( $order_id, $order, "order_id: $order_id processing" );
    }

    function woocommerce_order_status_processing( $order_id ) {
        $order = wc_get_order( $order_id );
        $this->debit_gift_cards( $order_id, $order, "order_id: $order_id processing" );
    }

    function woocommerce_order_status_preordered( $order_id ) {
        $order = wc_get_order( $order_id );
        $this->debit_gift_cards( $order_id, $order, "order_id: $order_id processing" );
    }

    function woocommerce_order_status_completed( $order_id ) {
        $order = wc_get_order( $order_id );
        $this->debit_gift_cards( $order_id, $order, "order_id: $order_id completed" );
    }

    function woocommerce_order_status_cancelled( $order_id ) {
        $order = wc_get_order( $order_id );
        $this->credit_gift_cards( $order_id, $order, "order_id: $order_id cancelled" );
    }

    function woocommerce_order_status_refunded( $order_id ) {
        $order = wc_get_order( $order_id );
        $this->credit_gift_cards( $order_id, $order, "order_id: $order_id refunded" );
    }

    function woocommerce_order_status_failed( $order_id ) {
        $order = wc_get_order( $order_id );
        $this->credit_gift_cards( $order_id, $order, "order_id: $order_id failed" );
    }

    function debit_gift_cards( $order_id, $order, $note ) {
        if ( ! is_a( $order, 'WC_Order' ) ) {
            return;
        }

        foreach( $order->get_items( 'pw_gift_card' ) as $order_item_id => $line ) {
            $gift_card = new PW_Gift_Card( $line->get_card_number() );
            if ( $gift_card->get_id() ) {
                if ( !$line->meta_exists( '_pw_gift_card_debited' ) ) {
                    if ( $line->get_amount() != 0 ) {
                        $gift_card->debit( ( $line->get_amount() * -1 ), "$note, order_item_id: $order_item_id" );
                    }

                    $line->add_meta_data( '_pw_gift_card_debited', true );
                    $line->save();
                }
            }
        }
    }

    function credit_gift_cards( $order_id, $order, $note ) {
        if ( ! is_a( $order, 'WC_Order' ) ) {
            return;
        }

        foreach( $order->get_items( 'pw_gift_card' ) as $order_item_id => $line ) {
            $gift_card = new PW_Gift_Card( $line->get_card_number() );
            if ( $gift_card->get_id() ) {
                if ( $line->meta_exists( '_pw_gift_card_debited' ) ) {
                    if ( $line->get_amount() != 0 ) {
                        $gift_card->credit( $line->get_amount(), "$note, order_item_id: $order_item_id" );
                    }

                    $line->delete_meta_data( '_pw_gift_card_debited' );
                    $line->save();
                }
            }
        }
    }

    function woocommerce_get_order_item_totals( $total_rows, $order, $tax_display ) {
        if ( !isset( $total_rows['pw_gift_cards'] ) ) {
            $gift_cards_redeemed = array();
            $gift_card_total = 0;
            foreach( $order->get_items( 'pw_gift_card' ) as $line ) {
                $gift_cards_redeemed[] = $line->get_card_number();
                $gift_card_total += $line->get_amount();
            }

            if ( !empty( $gift_card_total ) ) {
                $price = apply_filters( 'pwgc_to_order_currency', $gift_card_total * -1, $order );
                $args = array();

                // Multi-Currency for WooCommerce by TIV.NET INC
                if ( class_exists( 'WOOMC\App' ) ) {
                    $args['currency'] = get_post_meta( $order->get_id(), '_order_currency', true );
                }

                // Currency Switcher for WooCommerce by WP Wham
                if ( is_a( $order, 'WC_Order' ) && function_exists( 'alg_get_current_currency_code' ) ) {
                    $args['currency'] = get_post_meta( $order->get_id(), '_order_currency', true );
                }

                $price = wc_price( $price, $args );
                $gift_card_numbers = implode( ', ', $gift_cards_redeemed );

                $gift_card_row = array(
                    'label'  => sprintf( _n( 'Gift card %s:', 'Gift cards %s:', count( $gift_cards_redeemed ), 'pw-woocommerce-gift-cards' ), $gift_card_numbers ),
                    'value'  => $price,
                );

                $total_index = array_search( 'order_total', array_keys( $total_rows ) );
                if ( $total_index !== false ) {
                    // Insert this just before the Total row.
                    $total_rows = array_slice( $total_rows, 0, $total_index, true ) + array( 'pw_gift_cards' => $gift_card_row ) + array_slice( $total_rows, $total_index, count( $total_rows ) - $total_index, true );
                } else {
                    $total_rows['pw_gift_cards'] = $gift_card_row;
                }
            }
        }

        return $total_rows;
    }

    function woocommerce_checkout_create_order( $order, $data ) {
        $session_data = (array) WC()->session->get( PWGC_SESSION_KEY );
        if ( !isset( $session_data['gift_cards'] ) ) {
            return;
        }

        foreach ( $session_data['gift_cards'] as $card_number => $amount ) {
            $pw_gift_card = new PW_Gift_Card( $card_number );
            if ( $pw_gift_card->get_id() ) {

                $item = new WC_Order_Item_PW_Gift_Card();

                $item->set_props( array(
                    'card_number'   => $pw_gift_card->get_number(),
                    'amount'        => apply_filters( 'pwgc_to_default_currency', $amount ),
                ) );

                $order->add_item( $item );
            }
        }
    }

    function woocommerce_proceed_to_checkout() {
        wp_enqueue_script( 'pw-gift-cards' );
        wc_get_template( 'cart/apply-gift-card.php', array(), '', PWGC_PLUGIN_ROOT . 'templates/woocommerce/' );
    }

    function woocommerce_after_cart_contents() {
        wp_enqueue_script( 'pw-gift-cards' );
        wc_get_template( 'cart/apply-gift-card-after-cart-contents.php', array(), '', PWGC_PLUGIN_ROOT . 'templates/woocommerce/' );
    }

    function woocommerce_paypal_args( $args, $order ) {
        // If a Gift Card isn't being used on this order, no need to apply the fix.
        $session_data = (array) WC()->session->get( PWGC_SESSION_KEY );
        if ( !isset( $session_data['gift_cards'] ) ) {
            return $args;
        }

        if ( isset( $args['shipping_1'] ) ) {
            $need_paypal_shipping_fix = false;

            // Check that shipping is not the **only** cost as PayPal won't allow payment if the items have no cost.
            if ( !isset( $args['item_name_1'] ) ) {
                $need_paypal_shipping_fix = true;
            }

            // Make sure the product isn't negative where Shipping was covered.
            if ( isset( $args['amount_1'] ) && $args['amount_1'] < 0 ) {
                $need_paypal_shipping_fix = true;
            }

            if ( $need_paypal_shipping_fix ) {
                // We'll remove shipping_1 and then add a new item for Shipping.
                unset( $args['shipping_1'] );
                $args['item_name_1'] = sprintf( __( 'Shipping via %s', 'pw-woocommerce-gift-cards' ), $order->get_shipping_method() );
                $args['quantity_1'] = 1;
                $args['amount_1'] = $order->get_total();
                $args['item_number_1'] = '';
            }
        }

        // Fix an issue where Tax would be re-calculated since it was added to amount_1 instead of split out.
        // Only do this if we have 1 line item going to PayPal.
        if ( !isset( $args['tax_cart'] ) && !empty( $order->get_total_tax() ) && $order->get_total_tax() != 0 && !isset( $args['amount_2'] ) && $args['amount_1'] >= $order->get_total_tax() ) {
            $args['tax_cart'] = $order->get_total_tax();
            $args['amount_1'] = $args['amount_1'] - $order->get_total_tax();
        }

        return $args;
    }

    // Support for the WooCommerce Paytrail Gateway plugin by SkyVerge
    function wc_paytrail_payment_params( $params, $order, $api_request ) {
        // If we aren't sending extended info, nothing to do here.
        if ( !isset( $params['orderDetails'] ) ) {
            return $params;
        }

        // If a Gift Card isn't being used on this order, no need to apply the fix.
        $session_data = (array) WC()->session->get( PWGC_SESSION_KEY );
        if ( !isset( $session_data['gift_cards'] ) ) {
            return $params;
        }

        // Make sure we have a valid cart object.
        if ( !isset( WC()->cart ) || !isset( WC()->cart->pwgc_total_gift_cards_redeemed ) ) {
            return $params;
        }

        $gift_card_value = WC()->cart->pwgc_total_gift_cards_redeemed;
        if ( $gift_card_value > 0 ) {
            foreach( $params['orderDetails']['products'] as $x => &$product ) {
                if ( $gift_card_value <= 0 ) {
                    break;
                }

                if ( $gift_card_value >= $product['price'] ) {
                    $gift_card_value -= $product['price'];
                    unset( $params['orderDetails']['products'][ $x ] );
                } else {
                    $price = $product['price'];
                    $product['price'] -= $gift_card_value;
                    $gift_card_value -= $price;
                }
            }
        }

        return $params;
    }

    function ajax_redeem() {
        check_ajax_referer( 'pw-gift-cards-apply-gift-card', 'security' );

        $card_number = wc_clean( $_REQUEST['card_number'] );

        $result = $this->add_gift_card_to_session( $card_number );
        if ( $result === true ) {
            $gift_card = new PW_Gift_Card( $card_number );
            if ( $gift_card->get_balance() <= 0 ) {
                wc_add_notice( __( 'This gift card has a zero balance.', 'pw-woocommerce-gift-cards' ), 'notice' );
            }

            wc_add_notice( __( 'Gift card applied.', 'pw-woocommerce-gift-cards' ) );

            wp_send_json_success();
        } else {
            wp_send_json_error( array( 'message' => $result ) );
        }
    }

    function ajax_remove() {
        check_ajax_referer( 'pw-gift-cards-remove-card', 'security' );

        $card_number = wc_clean( $_REQUEST['card_number'] );

        $this->remove_gift_card_from_session( $card_number );

        wc_add_notice( __( 'Gift card removed.', 'pw-woocommerce-gift-cards' ) );

        wp_send_json_success();
    }

    function add_gift_card_to_session( $card_number ) {
        $gift_card = new PW_Gift_Card( $card_number );
        if ( $gift_card->get_id() ) {
            $card_number = $gift_card->get_number(); // Normalize the value.

            if ( $gift_card->get_active() ) {
                $balance = $gift_card->get_balance();
                if ( !empty( $balance ) ) {
                    if ( false === $this->cart_contains_gift_card() || 'yes' === get_option( 'pwgc_allow_gift_card_purchasing', 'yes' ) ) {
                        $error_message = apply_filters( 'pwgc_gift_card_can_be_redeemed', '', $card_number );
                        if ( empty( $error_message ) ) {
                            $session_data = (array) WC()->session->get( PWGC_SESSION_KEY );

                            if ( !isset( $session_data['gift_cards'] ) ) {
                                $session_data['gift_cards'] = array();
                            }

                            if ( ! WC()->session->has_session() ) {
                                WC()->session->set_customer_session_cookie( true );
                            }

                            $session_data['gift_cards'][ $card_number ] = 0; // This will get calculated in woocommerce_after_calculate_totals()
                            WC()->session->set( PWGC_SESSION_KEY, $session_data );
                            return true;
                        }
                    } else {
                        $error_message = __( 'Gift cards cannot be used to purchase other gift cards.', 'pw-woocommerce-gift-cards' );
                    }

                } else {
                    $error_message = __( 'This gift card has a zero balance.', 'pw-woocommerce-gift-cards' );
                }
            } else {
                $error_message = __( 'Card is inactive.', 'pw-woocommerce-gift-cards' );
            }
        } else {
            $error_message = $gift_card->get_error_message();

            // Tar-pit to make brute-force guessing inefficient.
            sleep(3);
        }

        return $error_message;
    }

    function cart_contains_gift_card() {
        if ( ! did_action( 'wp_loaded' ) ) {
            return false;
        }

        if ( is_admin() ) {
            return false;
        }

        foreach ( WC()->cart->get_cart() as $cart_item ) {
            if ( isset( $cart_item['product_id'] ) ) {
                $product = wc_get_product( $cart_item['product_id'] );
                if ( is_a( $product, 'WC_Product_PW_Gift_Card' ) ) {
                    return true;
                }
            }
        }

        return false;
    }

    function remove_gift_card_from_session( $card_number ) {
        $card_number = stripslashes( $card_number );
        $session_data = (array) WC()->session->get( PWGC_SESSION_KEY );
        if ( isset( $session_data['gift_cards'][ $card_number ] ) ) {
            unset( $session_data['gift_cards'][ $card_number ] );
            WC()->session->set( PWGC_SESSION_KEY, $session_data );
        }
    }

    function woocommerce_get_shop_coupon_data( $data, $coupon_code ) {
        if ( empty( $coupon_code ) || empty( WC()->cart ) ) {
            return $data;
        }

        if ( true === $this->is_gift_card_code( $coupon_code ) ) {
            // Creates a virtual coupon
            $data = array(
                'id' => -1,
                'code' => $coupon_code,
                'description' => 'pw_gift_card',
                'amount' => 0,
                'coupon_amount' => 0
            );
        }

        return $data;
    }

    function woocommerce_applied_coupon( $coupon_code ) {
        if ( true === $this->is_gift_card_code( $coupon_code ) ) {
            WC()->cart->remove_coupon( $coupon_code );
            wc_clear_notices();

            $result = $this->add_gift_card_to_session( $coupon_code );
            if ( $result === true ) {
                $gift_card = new PW_Gift_Card( $coupon_code );
                if ( $gift_card->get_balance() <= 0 ) {
                    wc_add_notice( __( 'This gift card has a zero balance.', 'pw-woocommerce-gift-cards' ), 'notice' );
                }

                wc_add_notice( __( 'Gift card applied.', 'pw-woocommerce-gift-cards' ) );
            } else {
                wc_add_notice( sprintf( __( 'Error: %s', 'pw-woocommerce-gift-cards' ), $result ), 'error' );
            }
        }
    }

    function woocommerce_apply_with_individual_use_coupon( $apply, $the_coupon, $coupon, $applied_coupons ) {
        if ( false === $apply ) {
            // Allow gift cards to be added with individual use coupons (gift cards are not coupons).
            return $this->is_gift_card_code( $the_coupon->get_code() );
        }

        return $apply;
    }

    function is_gift_card_code( $coupon_code ) {
        $gift_card = new PW_Gift_Card( $coupon_code );
        if ( $gift_card->get_id() ) {
            return true;
        }

        return false;
    }

    // Order Minimum/Maximum Amount for WooCommerce by Algoritmika Ltd
    // Version 3
    function alg_wc_oma_amount_cart_total( $result, $type ) {
        if ( isset( WC()->cart ) && isset( WC()->cart->pwgc_total_gift_cards_redeemed ) ) {
            $result += WC()->cart->pwgc_total_gift_cards_redeemed;
        }

        return $result;
    }

    // Order Minimum/Maximum Amount for WooCommerce by Algoritmika Ltd
    // Version 2
    function alg_wc_order_minimum_amount_message( $message, $title, $cart_or_checkout ) {
        if ( function_exists( 'alg_wc_order_minimum_amount' ) ) {
            if ( !empty( $message ) && $title == 'message_min_sum' && isset( WC()->cart ) && isset( WC()->cart->pwgc_total_before_gift_cards ) ) {
                $core = alg_wc_order_minimum_amount();
                if ( isset( $core->core ) && method_exists( $core->core, 'get_order_min_max_amount' ) ) {
                    $min_val = $core->core->get_order_min_max_amount( 'min', 'sum' );
                    $total = WC()->cart->pwgc_total_before_gift_cards;
                    if ( $min_val && $total && $total > $min_val ) {
                        $message = '';
                    }
                }
            }
        }

        return $message;
    }

    // Fixes the totals shown on the Checkout page when using Country Based Restrictions for WooCommerce by zorem
    function fzpcr_fix() {
        global $woocommerce;
        $woocommerce->cart->calculate_totals();
    }
}

global $pw_gift_cards_redeeming;
$pw_gift_cards_redeeming = new PW_Gift_Cards_Redeeming();

endif;

if ( isset( WC()->cart ) && class_exists( 'AngellEYE_Gateway_Paypal' ) && !class_exists( 'YITH_YWGC_Cart_Checkout' ) ) {
    $session_data = (array) WC()->session->get( PWGC_SESSION_KEY );
    if ( isset( $session_data['gift_cards'] ) ) {
        WC()->cart->applied_gift_cards = array();
        WC()->cart->applied_gift_cards_amounts = array();
        foreach ( $session_data['gift_cards'] as $card_number => $amount ) {
            WC()->cart->applied_gift_cards[] = $card_number;
            WC()->cart->applied_gift_cards_amounts[ $card_number ] = $amount;
        }

        class YITH_YWGC_Cart_Checkout {
            // Define the class so that it is detected in /plugins/paypal-for-woocommerce/classes/wc-gateway-calculations-angelleye.php
        }
    }
}
