<?php
/**
 * Dependencies for Scripts and Styles functions
 *
 * @category   CryptoWoo
 * @package    CryptoWoo
 * @subpackage Dependencies
 * Author: CryptoWoo AS
 * Author URI: https://cryptowoo.com
 */

/**
 *
 * Copied and overridden from wp-includes\functions.wp-scripts.php
 *
 * Enqueue a script.
 *
 * Registers the script if $src provided (does NOT overwrite), and enqueues it.
 *
 * @see WP_Dependencies::add()
 * @see WP_Dependencies::add_data()
 * @see WP_Dependencies::enqueue()
 *
 * @since 2.1.0
 *
 * @param string           $handle    Name of the script. Should be unique.
 * @param string           $src       Full URL of the script, or path of the script relative to the WordPress root directory.
 *                                    Default empty.
 * @param array            $deps      Optional. An array of registered script handles this script depends on. Default empty array.
 * @param string|bool|null $ver       Optional. String specifying script version number, if it has one, which is added to the URL
 *                                    as a query string for cache busting purposes. If version is set to false, a version
 *                                    number is automatically added equal to current installed WordPress version.
 *                                    If set to null, no version is added.
 * @param bool             $in_footer Optional. Whether to enqueue the script before </body> instead of in the <head>.
 *                                    Default 'false'.
 */
function cw_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $in_footer = false ) {
	wp_enqueue_script( $handle, $src, $deps, $ver ?: CWOO_VERSION, $in_footer );
}

/**
 *
 * Copied and overridden from wp-includes\functions.wp-scripts.php
 *
 * Enqueue a CSS stylesheet.
 *
 * Registers the style if source provided (does NOT overwrite) and enqueues.
 *
 * @see  WP_Dependencies::add()
 * @see  WP_Dependencies::enqueue()
 * @link https://www.w3.org/TR/CSS2/media.html#media-types List of CSS media types.
 *
 * @since 2.6.0
 *
 * @param string           $handle Name of the stylesheet. Should be unique.
 * @param string           $src    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
 *                                 Default empty.
 * @param array            $deps   Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
 * @param string|bool|null $ver    Optional. String specifying stylesheet version number, if it has one, which is added to the URL
 *                                 as a query string for cache busting purposes. If version is set to false, a version
 *                                 number is automatically added equal to current installed WordPress version.
 *                                 If set to null, no version is added.
 * @param string           $media  Optional. The media for which this stylesheet has been defined.
 *                                 Default 'all'. Accepts media types like 'all', 'print' and 'screen', or media queries like
 *                                 '(orientation: portrait)' and '(max-width: 640px)'.
 */
function cw_enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ) {
	wp_enqueue_style( $handle, $src, $deps, $ver ?: CWOO_VERSION, $media );
}

/**
 *
 * Copied and overridden from wp-includes\functions.wp-scripts.php
 *
 * Register a new script.
 *
 * Registers a script to be enqueued later using the wp_enqueue_script() function.
 *
 * @see WP_Dependencies::add()
 * @see WP_Dependencies::add_data()
 *
 * @since 2.1.0
 * @since 4.3.0 A return value was added.
 *
 * @param  string           $handle    Name of the script. Should be unique.
 * @param  string|bool      $src       Full URL of the script, or path of the script relative to the WordPress root directory.
 *                                     If source is set to false, script is an alias of other scripts it depends on.
 * @param  array            $deps      Optional. An array of registered script handles this script depends on. Default empty array.
 * @param  string|bool|null $ver       Optional. String specifying script version number, if it has one, which is added to the URL
 *                                     as a query string for cache busting purposes. If version is set to false, a version
 *                                     number is automatically added equal to current installed WordPress version.
 *                                     If set to null, no version is added.
 * @param  bool             $in_footer Optional. Whether to enqueue the script before </body> instead of in the <head>.
 *                                     Default 'false'.
 * @return bool Whether the script has been registered. True on success, false on failure.
 */
function cw_register_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false ) {
	return wp_register_script( $handle, $src, $deps, $ver ?: CWOO_VERSION, $in_footer );
}

/**
 *
 * Copied and overridden from wp-includes\functions.wp-scripts.php
 *
 * Register a CSS stylesheet.
 *
 * @see  WP_Dependencies::add()
 * @link https://www.w3.org/TR/CSS2/media.html#media-types List of CSS media types.
 *
 * @since 2.6.0
 * @since 4.3.0 A return value was added.
 *
 * @param  string           $handle Name of the stylesheet. Should be unique.
 * @param  string|bool      $src    Full URL of the stylesheet, or path of the stylesheet relative to the WordPress root directory.
 *                                  If source is set to false, stylesheet is an alias of other stylesheets it depends on.
 * @param  array            $deps   Optional. An array of registered stylesheet handles this stylesheet depends on. Default empty array.
 * @param  string|bool|null $ver    Optional. String specifying stylesheet version number, if it has one, which is added to the URL
 *                                  as a query string for cache busting purposes. If version is set to false, a version
 *                                  number is automatically added equal to current installed WordPress version.
 *                                  If set to null, no version is added.
 * @param  string           $media  Optional. The media for which this stylesheet has been defined.
 *                                  Default 'all'. Accepts media types like 'all', 'print' and 'screen', or media queries like
 *                                  '(orientation: portrait)' and '(max-width: 640px)'.
 * @return bool Whether the style has been registered. True on success, false on failure.
 */
function cw_register_style( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {
	return wp_register_style( $handle, $src, $deps, $ver ?: CWOO_VERSION, $media );
}
