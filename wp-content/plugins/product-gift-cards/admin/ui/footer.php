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
<div class="pwgc-footer">
    <div class="pwgc-footer-site-logo">
        <a href="https://pimwick.com" target="_blank"><img src="<?php echo $pw_gift_cards->relative_url( '/admin/assets/images/pimwick.png' ); ?>" alt="Pimwick, LLC" width="50" border="0" /></a><br>
        <a href="https://pimwick.com" target="_blank" class="pwgc-footer-site-title">Pimwick, LLC</a>
    </div>
    <div class="pwgc-rating">
        <?php printf( __( 'Need help? Email %s', 'pw-woocommerce-gift-cards' ), '<a href="mailto:us@pimwick.com" class="pwgc-link">us@pimwick.com</a>' ); ?>
        <br>
        <?php printf( __( 'Please consider <%s>leaving a review on WordPress.org</a>. This really helps us out!', 'pw-woocommerce-gift-cards' ), 'a href="https://wordpress.org/support/plugin/pw-woocommerce-gift-cards/reviews/#new-post" target="_blank" class="pwgc-link"' ); ?>
    </div>
</div>
