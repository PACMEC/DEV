<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;  // Exit if accessed directly
}

class CW_AddressList {


	/**
	 * Check if the address list is enabled (contains at least one unused address)
	 *
	 * @param $currency
	 *
	 * @return bool
	 */
	static function address_list_enabled( $currency ) {
		return (bool) ( self::get_address_list_count( $currency ) > 0 );
	}

	/**
	 * Return the next unused address in the list
	 *
	 * @param $currency
	 * @param $order_id
	 *
	 * @return int|string
	 */
	static function get_address_from_list( $currency, $order_id ) {
		$addresses   = self::get_address_list( $currency );
		$new_address = '';
		foreach ( $addresses as $address => $assigned_order_id ) {
			if ( (int) $assigned_order_id === 0 ) {
				$addresses[ $address ] = $order_id;
				$new_address           = $address;
				// Update address list
				self::save_address_list( $currency, $addresses );

				// Check number remaining
				$address_count = count( $addresses );
				if ( $address_count <= 5 ) {
					// Email admin
					self::email_admin( $currency, $address_count );
				}
				break;
			}
		}

		return $new_address;
	}

	/**
	 * Send address list count to admin email
	 *
	 * @param $currency
	 * @param $address_count
	 */
	static function email_admin( $currency, $address_count ) {
		if ( cw_get_option( 'send_address_list_alert' ) ) {
			$to       = get_site_option( 'admin_email' );
			$subject  = sprintf( __( '[%1$s] %2$s address list notification' ), get_bloginfo( 'name' ), $currency );
			$message  = sprintf( __( 'Only %1$s %2$s addresses left in CryptoWoo address list. Please log in at %3$s and add new addresses to the list. Sent from %4$s', 'cryptowoo' ), (int) $address_count, $currency, get_bloginfo( 'name' ), get_bloginfo( 'url' ) );
			$message .= sprintf( __( '%1$sTired of manually adding addresses? Get our %2$sHD Wallet Add-on%3$s to automate the derivation of new addresses!', 'cryptowoo' ), "\n", '<a href="https://www.cryptowoo.com/shop/cryptowoo-hd-wallet-addon/" target="_blank">', '</a>' );
			$headers  = sprintf( 'From: "%1$s" <%2$s>', _x( 'Site Admin', 'email "From" field' ), get_site_option( 'admin_email' ) );
			wp_mail( $to, $subject, $message, $headers );
		}
	}

	/**
	 * Add an address to the list, prevent duplicates, enforce limit
	 *
	 * @param string $address  The address to be added to the list
	 * @param string $currency The currency symbol
	 *
	 * @return bool
	 */
	static function add_address_to_list( $address, $currency ) {
		$addresses     = self::get_address_list( $currency );
		$added         = false;
		$max_addresses = apply_filters( 'cw_address_list_max', 20, $currency );

		if ( count( $addresses ) < $max_addresses ) {

			if ( ! array_key_exists( $address, $addresses ) ) {
				// Address did not exist before in the list
				$addresses[ $address ] = 0;
				$added                 = self::save_address_list( $currency, $addresses );
			}
		}

		return $added;
	}

	/**
	 * Return the address list for a currency
	 *
	 * @param $currency
	 *
	 * @param bool     $unused_only
	 *
	 * @return array
	 */
	static function get_address_list( $currency, $unused_only = false ) {

		$all_addresses = (array) get_option( self::get_address_list_key( $currency ), array() );
		if ( $unused_only ) {
			$addresses = array();
			foreach ( $all_addresses as $address => $assigned_order_id ) {
				if ( (int) $assigned_order_id === 0 ) {
					$addresses[ $address ] = 0;
				}
			}
		} else {
			$addresses = $all_addresses;
		}

		return (array) $addresses;
	}

	static function get_address_list_details( $currency ) {
		$addresses = self::get_address_list( $currency, true );
		if ( count( $addresses ) ) {

			$details = sprintf( '<table id="unused_addresses_%s">', $currency );
			$count   = 0;
			foreach ( $addresses as $address => $order_id ) {
				if ( $order_id < 1 ) {
					$count ++;
					$details .= sprintf( '<tr><td>%s</td><td>%s</td></tr>', $count, $address );
				}
			}
			$details .= '</table>';
		} else {
			$details = '<p>None</p>';
		}

		return $details;
	}

	/**
	 * Count the unused addresses in the list
	 *
	 * @param $currency
	 *
	 * @return int
	 */
	static function get_address_list_count( $currency ) {
		return count( self::get_address_list( $currency, true ) );
	}

	/**
	 * Save the address list
	 *
	 * @param $currency
	 * @param $addresses
	 *
	 * @return bool
	 */
	static function save_address_list( $currency, $addresses ) {
		return update_option( self::get_address_list_key( $currency ), $addresses );
	}

	/**
	 * Prepare button HTML to delete the list
	 *
	 * @param $currency
	 *
	 * @return string
	 */
	static function get_delete_list_button( $currency ) {
		$button = sprintf( '<div class="button" id="delete_address_list_%1$s">Delete %1$s Address List</div>', $currency );

		return $button;
	}

	/**
	 * Empty list
	 *
	 * @param $currency
	 *
	 * @return bool
	 */
	static function delete_list( $currency ) {
		return self::save_address_list( $currency, array() );
	}

	/**
	 * Return address list option key for the currency
	 *
	 * @param $currency
	 *
	 * @return string
	 */
	static function get_address_list_key( $currency ) {
		return 'cryptowoo_address_list_' . strtolower( $currency );
	}

}
