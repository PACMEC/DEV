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

?>
<table class="pwgc-balance-activity-table">
    <tr>
        <th><?php _e( 'Date', 'pw-woocommerce-gift-cards' ); ?></th>
        <th><?php _e( 'Action', 'pw-woocommerce-gift-cards' ); ?></th>
        <th><?php _e( 'User', 'pw-woocommerce-gift-cards' ); ?></th>
        <th><?php _e( 'Note', 'pw-woocommerce-gift-cards' ); ?></th>
        <th><?php _e( 'Amount', 'pw-woocommerce-gift-cards' ); ?></th>
        <th><?php _e( 'Balance', 'pw-woocommerce-gift-cards' ); ?></th>
    </tr>
    <?php
        $running_balance = $gift_card->get_balance();
        foreach ( $gift_card->get_activity() as $activity ) {
            ?>
            <tr>
                <td>
                    <?php echo date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $activity->activity_date ) ); ?>
                </td>
                <td>
                    <?php echo esc_html( ucwords( $activity->action ) ); ?>
                </td>
                <td>
                    <?php
                        echo esc_html( $activity->user );
                        if ( !empty( $activity->user_email ) ) {
                            ?>
                            <br />
                            <a href="mailto: <?php echo esc_attr( $activity->user_email ); ?>"><?php echo esc_attr( $activity->user_email ); ?></a>
                            <?php
                        } else {
                            _e( 'Guest', 'pw-woocommerce-gift-cards' );
                        }
                    ?>
                </td>
                <td>
                    <?php echo esc_html( $activity->note ); ?>
                </td>
                <td class="pwgc-balance-activity <?php echo ( $activity->amount < 0 ) ? 'pwgc-balance-activity-negative' : ''; ?>">
                    <?php
                        if ( $activity->amount != 0 ) {
                            echo wc_price( $activity->amount );
                        }
                    ?>
                </td>
                <td class="pwgc-balance-activity">
                    <?php echo wc_price( $running_balance ); ?>
                </td>
            </tr>
            <?php

            $running_balance -= $activity->amount;
        }
    ?>
</table>
