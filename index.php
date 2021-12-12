<?php
/**
 * Front to the PACMEC application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells PACMEC to load the theme.
 *
 * @package PACMEC
 */

/**
 * Tells PACMEC to load the PACMEC theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

/** Loads the PACMEC Environment and Template */
require( dirname( __FILE__ ) . '/wp-blog-header.php' );
