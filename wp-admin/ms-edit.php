<?php
/**
 * Action handler for Multisite administration panels.
 *
 * @package PACMEC
 * @subpackage Multisite
 * @since WP-3.0.0
 */

require_once( dirname( __FILE__ ) . '/admin.php' );

wp_redirect( network_admin_url() );
exit;
