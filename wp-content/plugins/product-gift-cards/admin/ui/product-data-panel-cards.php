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

global $pw_gift_cards;
global $product_object;

$variations = array_map( 'wc_get_product', $product_object->get_children() );

?>
<div id='<?php echo PWGC_PRODUCT_TYPE_SLUG; ?>_cards' class='panel woocommerce_options_panel' style="display: none;">
    <div class='options_group show_if_<?php echo PWGC_PRODUCT_TYPE_SLUG; ?>'>
        <?php
            woocommerce_wp_text_input( array(
                'id'                => 'pwgc_new_amount',
                'value'             => '',
                'label'             => __( 'Gift card amounts', 'pw-woocommerce-gift-cards' ) . ' (' . get_woocommerce_currency_symbol() . ')',
                'data_type'         => 'price',
                'class'             => 'pwgc-short-text-field',
                'desc_tip'          => 'true',
                'description'       => sprintf( __( 'The available denominations that can be purchased. For example: %1$s25.00, %1$s50.00, %1$s100.00', 'pw-woocommerce-gift-cards' ), get_woocommerce_currency_symbol() ),
            ) );

        ?>
        <div id="pwgc-amounts-container" class="pwbf-form-text">
            <div id="pwgc-amount-container-template" class="pwgc-amount-container pwgc-hidden"><span class="pwgc-remove-amount-button" role="presentation" aria-hidden="true">×</span> <span class="pwgc-amount"></span></div>
            <?php
                foreach ( $variations as $variation ) {
                    if ( is_a( $variation, 'WC_Product' ) ) {
                        if ( $variation->get_regular_price() > 0 ) {
                            ?>
                            <div class="pwgc-amount-container" data-variation_id="<?php echo $variation->get_id(); ?>"><span class="pwgc-remove-amount-button" role="presentation" aria-hidden="true">×</span> <span class="pwgc-amount"><?php echo $pw_gift_cards->pretty_price( $variation->get_regular_price() ); ?></span></div>
                            <?php
                        }
                    }
                }
            ?>
        </div>
    </div>
    <div class='options_group show_if_<?php echo PWGC_PRODUCT_TYPE_SLUG; ?>'>
        <div style="padding: 32px;">
            <a href="https://www.pimwick.com/gift-cards/" target="_blank" style="font-weight: 600; font-size: 125%;"><?php _e( 'PW WooCommerce Gift Cards Pro', 'pw-woocommerce-gift-cards' ); ?></a><?php _e( ' includes these great additional features:', 'pw-woocommerce-gift-cards' ); ?>
            <ul style="padding: 8px 32px;">
                <li><strong><?php _e( 'Multiple Designs', 'pw-woocommerce-gift-cards' ); ?></strong> - <?php _e( 'Create as many custom email designs as you would like. Happy birthday, congratulations, happy holidays, and more!', 'pw-woocommerce-gift-cards' ); ?></li>
                <li><strong><?php _e( 'Custom Amounts', 'pw-woocommerce-gift-cards' ); ?></strong> - <?php _e( 'Allow customers to specify the amount. You can set a minimum and a maximum amount.', 'pw-woocommerce-gift-cards' ); ?></li>
                <li><strong><?php _e( 'Delivery Date', 'pw-woocommerce-gift-cards' ); ?></strong> - <?php _e( 'Customers can choose when the gift card should be delivered to the recipient.', 'pw-woocommerce-gift-cards' ); ?></li>
                <li><strong><?php _e( 'Physical Gift Cards', 'pw-woocommerce-gift-cards' ); ?></strong> - <?php _e( 'Sell physical gift cards to your customers without requiring a recipient email address.', 'pw-woocommerce-gift-cards' ); ?></li>
                <li><strong><?php _e( 'Create Gift Cards', 'pw-woocommerce-gift-cards' ); ?></strong> - <?php _e( 'Enter the quantity, amount, and expiration date to easily generate gift cards.', 'pw-woocommerce-gift-cards' ); ?></li>
                <li><strong><?php _e( 'Import Card Numbers', 'pw-woocommerce-gift-cards' ); ?></strong> - <?php _e( 'Have physical cards or numbers from another system? Import them in one click!', 'pw-woocommerce-gift-cards' ); ?></li>
                <li><strong><?php _e( 'Default Amount', 'pw-woocommerce-gift-cards' ); ?></strong> - <?php _e( 'choose an amount that will be pre-selected when purchasing a gift card.', 'pw-woocommerce-gift-cards' ); ?></li>
                <li><strong><?php _e( 'Balances', 'pw-woocommerce-gift-cards' ); ?></strong> - <?php _e( 'A shortcode to let customers check their gift card balances.', 'pw-woocommerce-gift-cards' ); ?></li>
                <li><strong><?php _e( 'Expiration Dates', 'pw-woocommerce-gift-cards' ); ?></strong> - <?php _e( 'Automatically set an expiration date based on the purchase date.', 'pw-woocommerce-gift-cards' ); ?></li>
                <li><strong><?php _e( 'Balance Adjustments', 'pw-woocommerce-gift-cards' ); ?></strong> - <?php _e( 'Perform balance adjustments in the admin area.', 'pw-woocommerce-gift-cards' ); ?></li>
            </ul>
        </div>
    </div>
</div>
<?php
