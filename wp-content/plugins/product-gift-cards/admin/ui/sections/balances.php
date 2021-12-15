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
<div id="pwgc-section-balances" class="pwgc-section" style="<?php pwgc_dashboard_helper( 'balances', 'display: block;' ); ?>">
    <div id="pwgc-balance-summary-container">
        <?php
            require_once( 'balance-summary.php' );
        ?>
    </div>

    <form id="pwgc-balance-search-form">
        <input type="text" id="pwgc-balance-search" name="card_number" autocomplete="off" placeholder="<?php _e( 'Gift card number or recipient email (leave blank for all)', 'pw-woocommerce-gift-cards' ); ?>" value="<?php echo isset( $_GET['card_number'] ) ? esc_html( stripslashes( $_GET['card_number'] ) ) : ''; ?>">
        <input type="submit" class="button button-primary" value="<?php _e( 'Search', 'pw-woocommerce-gift-cards' ); ?>">
    </form>

    <div id="pwgc-balance-main-container">
        <div id="pwgc-balance-search-results"></div>
    </div>
</div>
