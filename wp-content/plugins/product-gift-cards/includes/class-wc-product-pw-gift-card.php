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

if ( ! class_exists( 'WC_Product_PW_Gift_Card' ) ) :

class WC_Product_PW_Gift_Card extends WC_Product_Variable {

    /*
     *
     * Getters
     *
     */
    public function get_type() {
        return PWGC_PRODUCT_TYPE_SLUG;
    }

    public function is_type( $type ) {
        return (
            // Some themes/plugins will check to see if this is a Variable type before including files required for
            // the gift card product to work correctly. By checking for 'variable' we make this compatible with these
            // types of themes and plugins.
            $this->get_type() === $type || 'variable' === $type
            || ( is_array( $type ) && ( in_array( $this->get_type(), $type ) || in_array( 'variable', $type ) ) )
        );
    }


    /*
     *
     * Other public methods
     *
     */
    public function get_price_html( $price = '' ) {
        return parent::get_price_html( $price );
    }

    public function add_amount( $amount ) {
        global $pw_gift_cards;

        if ( $pw_gift_cards->numeric_price( $amount ) <= 0 ) {
            return __( 'Amount must be greater than zero.', 'pw-woocommerce-gift-cards' );
        }

        $variations = array_map( 'wc_get_product', $this->get_children() );

        // Check for existing amount.
        foreach ( $variations as $variation ) {
            $variation_attributes = $variation->get_attributes();

            if ( isset( $variation_attributes[ PWGC_DENOMINATION_ATTRIBUTE_SLUG ] ) ) {
                $variation_option = $variation_attributes[ PWGC_DENOMINATION_ATTRIBUTE_SLUG ];

                if ( $pw_gift_cards->equal_prices( $variation_option, $amount ) ) {
                    return __( 'Amount already exists: ', 'pw-woocommerce-gift-cards' ) . $amount;
                }
            }
        }

        $variation_id = $this->create_variation( $amount );

        if ( $variation_id ) {

            $this->save();

            return $variation_id;
        } else {
            return __( 'Could not create variation.', 'pw-woocommerce-gift-cards' );
        }
    }

    public function delete_amount( $variation_id ) {
        if ( $variation = wc_get_product( $variation_id ) ) {
            $variation->delete( true );

            // Add the new variation to the current object's children list.
            $children = $this->get_children();
            if ( ( $key = array_search( $variation_id, $children ) ) !== false ) {
                unset( $children[ $key ] );
            }
            $this->set_children( $children );

            $this->sync_gift_card_amount_attributes();

            $this->save();

            return true;
        } else {
            return __( 'Could not locate variation using variation_id ', 'pw-woocommerce-gift-cards' ) . $variation_id;
        }
    }

    public function has_amount_on_sale() {
        $result = false;

        $variations = array_map( 'wc_get_product', $this->get_children() );
        foreach( $variations as $variation ) {
            if ( !is_a( $variation, 'WC_Product' ) ) {
                continue;
            }

            if ( $variation->is_on_sale() ) {
                $result = true;
                break;
            }
        }

        return $result;
    }



    /*
     *
     * Protected methods
     *
     */
    protected function create_variation( $amount ) {
        global $pw_gift_cards;

        $variation = new WC_Product_Variation();
        $variation->set_parent_id( $this->get_id() );
        $variation->set_virtual( '1' );

        $variation->set_regular_price( $pw_gift_cards->numeric_price( $amount ) );
        $variation->set_attributes( array( PWGC_DENOMINATION_ATTRIBUTE_SLUG => $pw_gift_cards->pretty_price( $amount ) ) );

        do_action( 'product_variation_linked', $variation->save() );

        // Add the new variation to the current object's children list.
        $children = $this->get_children();
        array_push( $children, $variation->get_id() );
        $this->set_children( $children );

        $this->sync_gift_card_amount_attributes();

        return $variation->get_id();
    }

    public function sync_gift_card_amount_attributes() {
        global $post;
        global $pw_gift_cards;
        global $pw_gift_cards_admin;
        global $wpdb;

        $pw_gift_cards->set_current_currency_to_default();

        $variations = array_map( 'wc_get_product', $this->get_children() );

        // Re-order all Variations based on the amount.
        if ( PWGC_SORT_VARIATIONS === true ) {
            uasort( $variations, array( $pw_gift_cards, 'price_sort' ) );
        }

        $index = 0;
        foreach( $variations as $variation ) {
            if ( !is_a( $variation, 'WC_Product' ) ) {
                continue;
            }

            $wpdb->update( $wpdb->posts, array( 'menu_order' => $index ), array( 'ID' => absint( $variation->get_id() ) ) );
            $index++;

            // Ensure that the attributes are correct on the variations.
            $variation->set_attributes( array( PWGC_DENOMINATION_ATTRIBUTE_SLUG => $pw_gift_cards->pretty_price( $variation->get_regular_price() ) ) );
            $variation->save();
        }

        $options = array();
        foreach ( $variations as $variation ) {
            $price = apply_filters( 'pwgc_to_default_currency', $variation->get_regular_price() );
            if ( !in_array( $price, $options ) && $price > 0 ) {
                $options[] = $price;
            }
        }

        $attributes = $this->get_attributes();

        $attribute = new WC_Product_Attribute();
        $attribute->set_name( 'Gift Card Amount' );
        $attribute->is_taxonomy( 0 );
        $attribute->set_position( 0 );
        $attribute->set_visible( apply_filters( 'pw_gift_cards_amount_attribute_visible_on_product_page', false, $this ) );
        $attribute->set_variation( '1' );

        $options = array_map( array( $pw_gift_cards, 'pretty_price' ), $options );

        $attribute->set_options( $options );

        $attributes[ PWGC_DENOMINATION_ATTRIBUTE_SLUG ] = $attribute;

        $this->set_attributes( $attributes );

        if ( !empty( $post ) && $post->post_type == 'product' ) {
            $this->save();
        }
    }
}

// Uses the Variable template for the gift card product type.
add_action( 'woocommerce_' . PWGC_PRODUCT_TYPE_SLUG . '_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );

if ( 'yes' === get_option( 'pwgc_before_add_to_cart_quantity_theme_fix', 'no' ) ) {
    function pwgc_before_add_to_cart_quantity_theme_fix() {
        global $product;

        if ( is_a( $product, 'WC_Product_PW_Gift_Card' ) && !isset( $GLOBALS['pwgc_theme_fix_applied'] ) ) {
            $GLOBALS['pwgc_theme_fix_applied'] = true;
            do_action( 'woocommerce_before_add_to_cart_quantity' );
        }
    }

    add_action( 'woocommerce_before_single_variation', 'pwgc_before_add_to_cart_quantity_theme_fix', 9 );
    add_action( 'woocommerce_single_variation', 'pwgc_before_add_to_cart_quantity_theme_fix', 9 );
    add_action( 'woocommerce_after_single_variation', 'pwgc_before_add_to_cart_quantity_theme_fix', 9 );
}

function pwgc_woocommerce_before_add_to_cart_quantity() {
    global $product;

    if ( is_a( $product, 'WC_Product_PW_Gift_Card' ) ) {
        wp_enqueue_script( 'pw-gift-cards' );

        wc_get_template( 'single-product/add-to-cart/pw-gift-card-before-add-to-cart-quantity.php', array(), '', PWGC_PLUGIN_ROOT . 'templates/woocommerce/' );

        // A customer's theme was calling woocommerce_before_add_to_cart_quantity multiple times so this is a fix for that scenario.
        if ( !defined( 'PWGC_BEFORE_ADD_TO_CART_QUANTITY_FIX' ) || PWGC_BEFORE_ADD_TO_CART_QUANTITY_FIX === false ) {
            remove_action( 'woocommerce_before_add_to_cart_quantity', 'pwgc_woocommerce_before_add_to_cart_quantity', 30 );
        }
    }
}
add_action( 'woocommerce_before_add_to_cart_quantity', 'pwgc_woocommerce_before_add_to_cart_quantity', 30 );


function pwgc_product_type_selector( $types ) {
    $types[ PWGC_PRODUCT_TYPE_SLUG ] = PWGC_PRODUCT_TYPE_NAME;

    return $types;
}
add_filter( 'product_type_selector', 'pwgc_product_type_selector' );


function pwgc_woocommerce_data_stores( $stores ) {
    if ( !isset( $stores[ 'product-' . PWGC_PRODUCT_TYPE_SLUG ] ) ) {
        $stores[ 'product-' . PWGC_PRODUCT_TYPE_SLUG ] = 'WC_Product_Variable_Data_Store_CPT';
    }

    return $stores;
}
add_filter( 'woocommerce_data_stores', 'pwgc_woocommerce_data_stores' );

function pwgc_process_pw_gift_card_product_meta_data( $post_id ) {
    $product = new WC_Product_PW_Gift_Card( $post_id );
    $product->sync_gift_card_amount_attributes();
}
add_action( 'woocommerce_process_product_meta_' . PWGC_PRODUCT_TYPE_SLUG, 'pwgc_process_pw_gift_card_product_meta_data' );

function pwgc_woocommerce_product_add_to_cart_text( $text, $product ) {
    if ( is_a( $product, 'WC_Product_PW_Gift_Card' ) ) {
        return apply_filters( 'pwgc_select_amount_text', __( 'Select amount', 'pw-woocommerce-gift-cards' ), $product );
    } else {
        return $text;
    }
}
add_filter( 'woocommerce_product_add_to_cart_text', 'pwgc_woocommerce_product_add_to_cart_text', 10, 2 );

function pwgc_woocommerce_variation_option_name( $name, $option = null, $attribute_name = null, $product = null ) {
    global $pw_gift_cards;

    if ( empty( $product ) && isset( $GLOBALS['product'] ) ) {
        $product = $GLOBALS['product'];
    }

    if ( is_a( $product, 'WC_Product_PW_Gift_Card' ) && 'yes' === get_option( 'pwgc_format_prices', 'yes' ) && ! class_exists( 'Woo_Variation_Swatches' ) ) {
        $name = $pw_gift_cards->sanitize_amount( $name );
        $price = $pw_gift_cards->numeric_price( $name );

        $_REQUEST['woocs_block_price_hook'] = true; // Needed for WooCommerce Currency Switcher by realmag777
        $_REQUEST['alg_wc_currency_switcher_correction_ignore'] = true; // Currency Switcher for WooCommerce by WP Wham

        $price = apply_filters( 'pwgc_to_current_currency', $price );

        return strip_tags( wc_price( $price ) );
    }

    return $name;
}
if ( isset ( $pw_gift_cards ) && $pw_gift_cards->wc_min_version( '3.6.1' ) ) {
    add_filter( 'woocommerce_variation_option_name', 'pwgc_woocommerce_variation_option_name', 10, 4 );
} else {
    add_filter( 'woocommerce_variation_option_name', 'pwgc_woocommerce_variation_option_name', 10, 1 );
}

endif;
