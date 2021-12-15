<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly

/**
 * Array of Singleton classes.
 *
 * @category   CryptoWoo
 * @package    CryptoWoo
 * @subpackage Singleton
 * @author     Developer: CryptoWoo AS
 */
abstract class CW_Singleton {

	/**
	 *
	 * Instance of this class.
	 *
	 * @var static
	 */
	private static $instance;

	/**
	 *
	 * Constructor.
	 *
	 * @param static $obj Set the object (For unit testing).
	 *
	 * @return static
	 */
	public static function instance( $obj = null ) {
		return self::get_instance( $obj );
	}

	/**
	 *
	 * Return an instance of this class.
	 *
	 * @param static $obj Set the object (For unit testing).
	 *
	 * @return static
	 */
	protected static function get_instance( $obj = null ) {
		if ( $obj ) {
			self::$instance = $obj;
		} elseif ( empty( self::$instance ) ) {
			self::$instance = new static();
		}

		return self::$instance;
	}

	/**
	 *
	 * Avoid possibility to clone this class
	 */
	private function __clone() {
	}
}
