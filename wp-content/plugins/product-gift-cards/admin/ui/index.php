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
global $wpdb;

require( 'header.php' );

?>
<div class="pwgc-main-content">
    <?php
        require( 'initial-setup.php' );
        require( 'section-buttons.php' );
        require( 'sections/balances.php' );
        require( 'sections/designer.php' );
        require( 'sections/create.php' );
        require( 'sections/import.php' );
        require( 'sections/settings.php' );
    ?>
</div>
<?php

require( 'footer.php' );
