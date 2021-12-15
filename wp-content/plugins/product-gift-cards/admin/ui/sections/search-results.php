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

if ( count( $gift_cards ) == 0 ) {
    ?>
    <h1><?php _e( 'No results', 'pw-woocommerce-gift-cards' ); ?></h1>
    <p><?php _e( 'There are no gift cards found matching your search terms.', 'pw-woocommerce-gift-cards' ); ?></p>
    <?php
} else {
    ?>
    <table class="pwgc-admin-table">
        <tr>
            <th><?php _e( 'Card Number', 'pw-woocommerce-gift-cards' ); ?></th>
            <th><?php _e( 'Balance', 'pw-woocommerce-gift-cards' ); ?></th>
            <th><?php _e( 'Expiration Date', 'pw-woocommerce-gift-cards' ); ?></th>
            <th>&nbsp;</th>
        </tr>
        <?php
            foreach ( $gift_cards as $gift_card ) {
                ?>
                <tr data-gift-card-number="<?php echo esc_html( $gift_card->get_number() ); ?>">
                    <td class="pwgc-search-result-card-number">
                        <?php echo esc_html( $gift_card->get_number() ); ?>
                        <div class="pwgc-inactive-card pwgc-balance-error <?php if ( $gift_card->get_active() ) { echo 'pwgc-hidden'; } ?>">
                            <?php _e( 'Card has been deleted.', 'pw-woocommerce-gift-cards' ); ?>
                        </div>
                    </td>
                    <td class="pwgc-search-result-balance">
                        <?php echo wc_price( $gift_card->get_balance() ); ?>
                    </td>
                    <td class="pwgc-search-result-expiration-date">
                        <?php
                            echo $gift_card->get_expiration_date_html();
                        ?>
                    </td>
                    <td class="pwgc-search-result-buttons">
                        <a href="#" class="pwgc-view-activity button button-secondary"><i class="fas fa-history"></i> <?php _e( 'View activity', 'pw-woocommerce-gift-cards' ); ?></a>
                        <span class="pwgc-buttons-active <?php if ( !$gift_card->get_active() ) { echo 'pwgc-hidden'; } ?>">
                            <a href="#" class="pwgc-delete button button-secondary"><i class="fas fa-times"></i> <?php _e( 'Delete', 'pw-woocommerce-gift-cards' ); ?></a>
                        </span>
                        <span class="pwgc-buttons-inactive <?php if ( $gift_card->get_active() ) { echo 'pwgc-hidden'; } ?>">
                            <a href="#" class="pwgc-restore button button-secondary"><i class="fas fa-history"></i> <?php _e( 'Restore', 'pw-woocommerce-gift-cards' ); ?></a>
                        </span>
                    </td>
                </tr>
                <?php
            }
        ?>
    </table>
    <script>
        jQuery('.pwgc-view-activity').off('click').on('click', function(e) {
            var row = jQuery(this).closest('tr');
            if (row.find('.pwgc-balance-activity-table').length > 0) {
                row.find('.pwgc-balance-activity-container').remove();
            } else {
                pwgcAdminGiftCardActivity(row);
            }

            e.preventDefault();
            return false;
        });

        jQuery('.pwgc-delete').off('click').on('click', function(e) {
            var row = jQuery(this).closest('tr');
            if (confirm('<?php _e( 'Are you sure you want to delete this gift card?', 'pw-woocommerce-gift-cards' ); ?>')) {
                pwgcDelete(row);
            }

            e.preventDefault();
            return false;
        });

        jQuery('.pwgc-restore').off('click').on('click', function(e) {
            var row = jQuery(this).closest('tr');
            pwgcRestore(row);

            e.preventDefault();
            return false;
        });
    </script>
    <?php
}
