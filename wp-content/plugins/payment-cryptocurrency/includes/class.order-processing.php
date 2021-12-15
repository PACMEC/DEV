<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}// Exit if accessed directly
/**
 * Process orders
 *
 * @category   CryptoWoo
 * @package    CryptoWoo
 * @subpackage OrderProcessing
 * @todo       refactor: Remove backward compatibility
 */
class CW_OrderProcessing {

	/**
	 *
	 * Order processing instance
	 *
	 * @var CW_Order_Processing The order processing object.
	 */
	private static $processing;

	/**
	 *
	 * Block explorer processing instance
	 *
	 * @var CW_Block_Explorer_Processing The block explorer processing object.
	 */
	private static $block_explorer_processing;

	/**
	 *
	 * Block explorer tools instance
	 *
	 * @var CW_Order_Processing_Tools The block explorer tools object.
	 */
	private static $tools;

	/**
	 *
	 * Block explorer tools instance
	 *
	 * @var CW_Block_Explorer_Tools The block explorer tools object.
	 */
	private static $block_explorer_tools;

	/**
	 *
	 * Is triggered when invoking inaccessible methods in a static context.
	 *
	 * @param string $name      the function name.
	 * @param array  $arguments the arguments.
	 *
	 * @return mixed
	 * @link   https://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
	 *
	 * TODO: This is for backward compatibility, remove this when no more usage!
	 */
	public static function __callStatic( $name, $arguments ) {
		return self::call_function( $name, $arguments );
	}

	/**
	 *
	 * Is triggered when invoking inaccessible methods.
	 *
	 * @param string $name      the function name.
	 * @param array  $arguments the arguments.
	 *
	 * @return mixed
	 * @link   https://php.net/manual/en/language.oop5.overloading.php#language.oop5.overloading.methods
	 *
	 * TODO: This is for backward compatibility, remove this when no more usage!
	 */
	public function __call( $name, $arguments ) {
		return self::call_function( $name, $arguments );
	}

	/**
	 *
	 * Is triggered when invoking inaccessible methods in a static context.
	 *
	 * @param string $name      the function name.
	 * @param array  $arguments the arguments.
	 *
	 * @return mixed
	 *
	 * TODO: This is for backward compatibility, remove this when no more usage!
	 */
	public static function call_function( $name, array $arguments ) {
		if ( method_exists( CW_Order_Processing::class, $name ) ) {
			$foo = CW_Order_Processing::class;
		} elseif ( method_exists( CW_Order_Processing_Tools::class, $name ) ) {
			$foo = CW_Order_Processing_Tools::class;
		} elseif ( method_exists( CW_Block_Explorer_Tools::class, $name ) ) {
			$foo = CW_Block_Explorer_Tools::class;
		} elseif ( method_exists( CW_Block_Explorer_Processing::class, $name ) ) {
			$foo = CW_Block_Explorer_Processing::class;
		} elseif ( method_exists( self::tools(), $name ) ) {
			$foo = self::tools();
		} elseif ( method_exists( self::block_explorer_tools(), $name ) ) {
			$foo = self::block_explorer_tools();
		} else {
			return false;
		}
		return call_user_func_array( array( $foo, $name ), $arguments );
	}

	/**
	 *
	 * Save payment details.
	 *
	 * @param string $payment_address    Cryptocurrency blockchain payment address.
	 * @param int    $amount             Order total in fiat value.
	 * @param string $customer_reference Customer reference.
	 * @param string $payment_currency   Payment currency code (eg. BTC).
	 * @param float  $crypto_amount      Order total in cryptocurrency satoshi value.
	 * @param int    $order_id           Woocommerce order id.
	 */
	public static function cryptowoo_insert_payment_details( $payment_address, $amount, $customer_reference, $payment_currency, $crypto_amount, $order_id ) {
		/* Insert entry into table payments */
		CW_Database_CryptoWoo::insert_payment_details( $payment_address, $amount, $customer_reference, $payment_currency, $crypto_amount, $order_id );

		// Update WooCommerce order details.
		CW_Database_Woocommerce::instance( $order_id )
			->set_payment_address( $payment_address )
			->set_crypto_amount( $crypto_amount )
			->set_amount( $amount )
			->set_payment_currency( $payment_currency )
			->set_tx_confirmed( false )
			->update();
	}

	/**
	 *
	 * Update CryptoWoo orders payment details
	 *
	 * @param string $payment_address    Cryptocurrency blockchain payment address.
	 * @param int    $amount             Order total in fiat value.
	 * @param string $customer_reference Customer reference.
	 * @param string $payment_currency   Payment currency code (eg. BTC).
	 * @param float  $crypto_amount      Order total in cryptocurrency satoshi value.
	 * @param int    $order_id           Woocommerce order id.
	 *
	 * @return bool
	 */
	public static function cryptowoo_update_payment_details( $payment_address, $amount, $customer_reference, $payment_currency, $crypto_amount, $order_id ) {
		$update_payment_request = CW_Database_CryptoWoo::reset_payment_details( $payment_address, $amount, $customer_reference, $payment_currency, $crypto_amount, $order_id );

		if ( false !== $update_payment_request ) {
			// Update WooCommerce order details.
			CW_Database_Woocommerce::instance( $order_id )
				->set_payment_address( $payment_address )
				->set_crypto_amount( $crypto_amount )
				->set_amount( $amount )
				->set_payment_currency( $payment_currency )
				->set_tx_confirmed( false )
				->update();
		}

		return $update_payment_request;
	}

	/**
	 *
	 * Update WooCommerce order meta
	 *
	 * @param int   $order_id   Woocommerce order id.
	 * @param array $order_meta Woocommerce order meta.
	 *
	 * @return int|WC_Order
	 *
	 * @deprecated Deprecated, use {@link CW_Database_Woocommerce} instead.
	 * TODO: Remove when no more usage by add-ons.
	 */
	static function cwwc_update_order_meta( $order_id, $order_meta ) {
		if ( ! count( $order_meta ) ) {
			return 0;
		}
		$cw_db_wc = CW_Database_Woocommerce::instance( $order_id );
		if ( is_object( $cw_db_wc ) ) {
			if ( isset( $order_meta['received_confirmed'] ) ) {
				$cw_db_wc->set_received_confirmed( $order_meta['received_confirmed'] );
			}
			if ( isset( $order_meta['received_unconfirmed'] ) ) {
				$cw_db_wc->set_received_unconfirmed( $order_meta['received_unconfirmed'] );
			}
			if ( isset( $order_meta['txids'] ) ) {
				$cw_db_wc->set_tx_ids( unserialize( $order_meta['txids'] ) );
			}

			return $cw_db_wc->update();
		}
	}

	/**
	 * Update payment address info in database
	 *
	 * @param string       $address            Blockchain address.
	 * @param int          $amount_received    Crypto amount confirmed (1e8).
	 * @param int          $amount_unconfirmed Crypto amount unconfirmed (1e8).
	 * @param string       $txids_serialized   Blockchain txids serialized.
	 * @param int          $order_id           Woocommerce order id.
	 * @param int|false    $crypto_amount_due  Crypto amount due (1e8).
	 * @param string|false $currency           Payment currency code.
	 *
	 * @return     int|false
	 * @package    OrderProcess
	 * @deprecated Deprecated, use {@link CW_Database_CryptoWoo} instead.
	 * TODO: Remove when no more usage by add-ons.
	 */
	public static function update_address_info( $address, $amount_received, $amount_unconfirmed, $txids_serialized, $order_id, $crypto_amount_due = false, $currency = false ) {
		$cwdb = CW_Database_CryptoWoo::instance( $order_id );

		$cwdb->set_address( $address )
			->set_received_confirmed( $amount_received )
			->set_received_unconfirmed( $amount_unconfirmed )
			->set_tx_ids( unserialize( $txids_serialized ) )
			->set_last_update();

		false === $currency ?: $cwdb->set_payment_currency( $currency );
		false === $crypto_amount_due ?: $cwdb->set_crypto_amount( $crypto_amount_due );

		return $cwdb->update();
	}

	/**
	 *
	 * Get the exchange rate processing object
	 *
	 * @param int|CW_Block_Explorer_Processing $order_id Woocommerce Order ID (or set the object (For unit testing)).
	 *
	 * @return CW_Block_Explorer_Processing
	 */
	public static function processing( $order_id = null ) {
		if ( is_object( $order_id ) ) {
			self::$processing = $order_id;
		} elseif ( ! self::$processing ) {
			self::$processing = CW_Order_Processing::instance( $order_id );
		}

		return self::$processing;
	}

	/**
	 *
	 * Get the exchange rate processing object
	 *
	 * @param null|CW_Block_Explorer_Processing $obj Set the object (For unit testing).
	 *
	 * @return CW_Block_Explorer_Processing
	 */
	public static function block_explorer( $obj = null ) {
		if ( $obj ) {
			self::$block_explorer_processing = $obj;
		} elseif ( ! self::$block_explorer_processing ) {
			self::$block_explorer_processing = new CW_Block_Explorer_Processing();
		}

		return self::$block_explorer_processing;
	}

	/**
	 *
	 * Get the exchange rate tools object
	 *
	 * @param null|CW_Order_Processing_Tools $obj Set the object (For unit testing).
	 *
	 * @return CW_Order_Processing_Tools
	 */
	public static function tools( $obj = null ) {
		if ( $obj ) {
			self::$tools = $obj;
		} elseif ( ! self::$tools ) {
			self::$tools = new CW_Order_Processing_Tools();
		}

		return self::$tools;
	}

	/**
	 *
	 * Get the exchange rate tools object
	 *
	 * @param null|CW_Block_Explorer_Tools $obj Set the object (For unit testing).
	 *
	 * @return CW_Block_Explorer_Tools
	 */
	public static function block_explorer_tools( $obj = null ) {
		if ( $obj ) {
			self::$block_explorer_tools = $obj;
		} elseif ( ! self::$block_explorer_tools ) {
			self::$block_explorer_tools = new CW_Block_Explorer_Tools();
		}

		return self::$block_explorer_tools;
	}
}
