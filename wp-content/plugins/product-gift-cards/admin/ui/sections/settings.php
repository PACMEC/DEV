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

global $pw_gift_cards_admin;

?>
<div id="pwgc-section-settings" class="pwgc-section" style="<?php pwgc_dashboard_helper( 'settings', 'display: block;' ); ?>">
    <form id="pwgc-save-settings-form" method="post">
        <?php
            $settings = $pw_gift_cards_admin->settings;
            $settings[0]['title'] = '';

            WC_Admin_Settings::output_fields( $settings );
        ?>
        <div id="pwgc-save-settings-message"></div>
        <p><input type="submit" id="pwgc-save-settings-button" class="button button-primary" value="<?php _e( 'Save Settings', 'pw-woocommerce-gift-cards' ); ?>"></p>
    </form>
</div>
