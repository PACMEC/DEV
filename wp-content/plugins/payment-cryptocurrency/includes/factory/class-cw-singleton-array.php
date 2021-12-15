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
abstract class CW_Singleton_Array {

	/**
	 *
	 * Instances of this class.
	 *
	 * @var static[]
	 */
	private static $instance;

	/**
	 *
	 * ID of the singleton element.
	 *
	 * @var int|string
	 */
	private $id;

	/**
	 *
	 * Constructor.
	 *
	 * @param int|string $id ID of the singleton element.
	 */
	protected function __construct( $id ) {
		$this->id = $id;
	}

	/**
	 *
	 * Return an instance of this class.
	 *
	 * @param int|string $key The key of the singleton element.
	 * @param static     $obj Set the object (For unit testing).
	 *
	 * @throws InvalidArgumentException Throws invalid argument if $key is not string or int or string cast able..
	 * @return static
	 */
	public static function instance( $key, $obj = null ) {
		return self::get_instance( $key, $obj );
	}

	/**
	 *
	 * Return an instance of this class.
	 *
	 * @param string|int $id  ID of the singleton element.
	 * @param static     $obj Set the object (For unit testing).
	 *
	 * @throws InvalidArgumentException Throws invalid argument if $id is not string or int or string cast able.
	 * @return static
	 */
	public static function get_instance( $id, $obj = null ) {
		// Failsafe if the class is called with a class with get_id method we get the id.
		if ( method_exists( $id, 'get_id' ) ) {
			$id = $id->get_id();
		}

		// Make sure this is string or integer. Or that it can be casted to string if object, source: https://stackoverflow.com/a/5496674.
		if ( ( ! is_string( $id ) && ! is_numeric( $id ) ) && ( ( ! is_array( $id ) ) && ( ( ! is_object( $id ) && settype( $id, 'string' ) !== false ) || ( is_object( $id ) && method_exists( $id, '__toString' ) ) ) ) ) {
			throw new InvalidArgumentException( 'The instance ID must be an integer or string or able to cast to string' );
		}

		$instance_id = static::class . "_$id";

		if ( $obj ) {
			self::$instance[ $instance_id ] = $obj;
		} elseif ( empty( self::$instance[ $instance_id ] ) ) {
			self::$instance[ $instance_id ] = new static( $id );
		}

		return self::$instance[ $instance_id ];
	}

	/**
	 *
	 * Get id of the singleton element.
	 *
	 * @return int|string
	 */
	protected function get_id() {
		return $this->id;
	}

	/**
	 *
	 * Avoid possibility to clone this class
	 */
	private function __clone() {
	}
}
