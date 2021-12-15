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

if ( ! class_exists( 'PW_Gift_Card' ) ) :

class PW_Gift_Card {

    /*
     *
     * Properties
     *
     */
    public function get_id() { return $this->pimwick_gift_card_id; }
    private $pimwick_gift_card_id;

    public function get_number() { return $this->number; }
    private $number;

    public function get_active() { return $this->active; }
    private $active;

    public function get_create_date() { return $this->create_date; }
    private $create_date;

    public function get_expiration_date() { return $this->expiration_date; }
    public function set_expiration_date( $expiration_date ) { return; }
    private $expiration_date;

    public function get_error_message() { return $this->error_message; }
    private $error_message;

    private $balance_cache = false;


    function __construct( $data ) {
        if ( is_object( $data ) && $this->load_gift_card_data( $data ) ) {
            return;
        }

        $number = wc_clean( $data );
        if ( !empty( $number ) ) {

            $loaded = $this->get_gift_card( $number );

            // Fix for gift card numbers that contain a backslash.
            if ( !$loaded ) {
                $loaded = $this->get_gift_card( stripslashes( $number ) );
            }

            if ( !$loaded ) {
                $this->error_message = __( 'Card number does not exist.', 'pw-woocommerce-gift-cards' );
            }
        } else {
            $this->error_message = __( 'Enter a card number.', 'pw-woocommerce-gift-cards' );
        }
    }

    function get_gift_card( $number ) {
        global $wpdb;

        $result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM `{$wpdb->pimwick_gift_card}` WHERE `number` = %s", $number ) );
        if ( $result !== null ) {
            $this->load_gift_card_data( $result );
            return true;
        }

        return false;
    }

    function load_gift_card_data( $row ) {
        if ( property_exists( $row, 'pimwick_gift_card_id' ) ) {
            $this->pimwick_gift_card_id     = $row->pimwick_gift_card_id;
            $this->number                   = $row->number;
            $this->active                   = $row->active;
            $this->create_date              = $row->create_date;
            $this->expiration_date          = $row->expiration_date;

            if ( property_exists( $row, 'balance' ) ) {
                $this->balance_cache = $row->balance;
            }

            return true;
        }

        return false;
    }



    /*
     *
     * Public methods.
     *
     */
    public function get_expiration_date_html() {
        $expiration_date = $this->get_expiration_date();
        if ( !empty( $expiration_date ) ) {
            $html = date( wc_date_format(), strtotime( $expiration_date ) );
            if ( $this->has_expired() ) {
                $html .= '<div style="color: red; font-weight: 600;">' . __( 'Expired', 'pw-woocommerce-gift-cards' ) . '</div>';
            }
        } else {
            $html = __( 'None', 'pw-woocommerce-gift-cards' );
        }

        return $html;
    }

    public function has_expired() {
        if ( !empty( $this->get_expiration_date() ) ) {
            $expiration_date = strtotime( $this->get_expiration_date() );
            $todays_date = strtotime( current_time( 'Y-m-d' ) );
            return ( $expiration_date < $todays_date );
        } else {
            return false;
        }
    }

    public function get_balance( $force_refresh = false ) {
        global $wpdb;

        if ( $force_refresh || $this->balance_cache === false ) {
            $this->balance_cache = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(amount) FROM {$wpdb->pimwick_gift_card_activity} WHERE pimwick_gift_card_id = %d", $this->get_id() ) );

            if ( $this->balance_cache === null ) {
                $this->error_message = $wpdb->last_error;
            }
        }

        return $this->balance_cache;
    }

    public function get_activity() {
        global $wpdb;

        $results = $wpdb->get_results( $wpdb->prepare( "
            SELECT
                card.number AS card_number,
                users.user_nicename AS user,
                users.user_email AS user_email,
                activity.activity_date,
                activity.action,
                activity.amount,
                activity.note
            FROM
                {$wpdb->pimwick_gift_card_activity} AS activity
            JOIN
                {$wpdb->pimwick_gift_card} AS card ON (card.pimwick_gift_card_id = activity.pimwick_gift_card_id)
            LEFT JOIN
                {$wpdb->users} AS users ON (users.ID = activity.user_id)
            WHERE
                activity.pimwick_gift_card_id = %d
            ORDER BY
                activity.pimwick_gift_card_activity_id DESC
        ", $this->get_id() ) );

        return $results;
    }

    private $original_order_item_id = 0;

    public function get_original_order_item_id() {
        global $wpdb;

        if ( empty( $this->original_order_item_id ) ) {
            $sql = $wpdb->prepare( "
                SELECT
                    m.order_item_id
                FROM
                    {$wpdb->prefix}woocommerce_order_itemmeta AS m
                WHERE
                    m.meta_key = %s
                    AND m.meta_value = %s
                LIMIT 1
            ",
            PWGC_GIFT_CARD_NUMBER_META_KEY,
            $this->get_number() );

            $result = $wpdb->get_row( $sql );
            if ( $result ) {
                $this->original_order_item_id = $result->order_item_id;
            } else {
                $this->original_order_item_id = 0;
            }
        }

        return apply_filters( 'pwgc_get_original_order_item_id', $this->original_order_item_id, $this );
    }

    public function credit( $amount, $note = '' ) {
        $amount = floatval( $amount );
        if ( $amount < 0 ) {
            wp_die( __( 'Amount added should be greater than zero.', 'pw-woocommerce-gift-cards' ) );
        }

        if ( empty( $amount ) ) {
            return;
        }

        $this->adjust_balance( $amount, $note );
    }

    public function debit( $amount, $note = '' ) {
        $amount = floatval( $amount );
        if ( $amount >= 0 ) {
            wp_die( __( 'Amount deducted should be less than zero.', 'pw-woocommerce-gift-cards' ) );
        }
        $this->adjust_balance( $amount, $note );
    }

    public function adjust_balance( $amount, $note = '' ) {
        $amount = floatval( $amount );

        if ( !$this->active ) {
            wp_die( __( 'Unable to adjust balance, card is not active.', 'pw-woocommerce-gift-cards' ) );
        }

        if ( ( $this->get_balance() + $amount ) < 0 ) {
            $amount = $this->get_balance() * -1;
        }

        $this->balance_cache = false;

        $this->log_activity( 'transaction', $amount, $note );
    }

    public function deactivate( $note = '' ) {
        if ( $this->update_property( 'active', false ) === true ) {
            $this->log_activity( 'deactivate', null, $note );
        }
    }

    public function reactivate( $note = '' ) {
        if ( $this->update_property( 'active', true ) === true ) {
            $this->log_activity( 'reactivate', null, $note );
        }
    }

    public function check_balance_url() {
        global $pw_gift_cards;

        $check_balance_url = '';

        if ( is_admin() ) {
            $check_balance_url = admin_url( 'admin.php' );
            $check_balance_url = add_query_arg( 'page', 'wc-pw-gift-cards', $check_balance_url );
            $check_balance_url = add_query_arg( 'card_number', $this->get_number(), $check_balance_url );
        }

        return $check_balance_url;
    }



    /*
     *
     * Static Methods
     *
     */
    public static function get_by_id( $id ) {
        global $wpdb;

        if ( !empty( absint( $id ) ) ) {
            $result = $wpdb->get_row( $wpdb->prepare( "SELECT `number` FROM `{$wpdb->pimwick_gift_card}` WHERE pimwick_gift_card_id = %d", absint( $id ) ) );
            if ( null !== $result ) {
                return new PW_Gift_Card( $result->number );
            }
        }

        return false;
    }

    public static function add_card( $number, $note = '' ) {
        global $wpdb;

        $number = wc_clean( $number );

        if ( empty( $number ) ) {
            return __( 'Card Number cannot be empty.', 'pw-woocommerce-gift-cards' );
        }

        $result = $wpdb->insert( $wpdb->pimwick_gift_card, array ( 'number' => $number ) );

        if ( $result !== false ) {
            if ( !empty( absint( $wpdb->insert_id ) ) ) {
                $gift_card = PW_Gift_Card::get_by_id( $wpdb->insert_id );
                if ( !empty( $gift_card ) ) {
                    $gift_card->log_activity( 'create', null, $note );
                    return $gift_card;
                } else {
                    return sprintf( __( 'Gift Card %s was inserted into table %s and received database id %s but it could not be retrieved. Last error: %s', 'pw-woocommerce-gift-cards' ), $number, $wpdb->insert_id, $wpdb->pimwick_gift_card, $wpdb->last_error );
                }
            } else {
                return sprintf( __( 'Gift Card %s could not be inserted into table %s. Last error: %s', 'pw-woocommerce-gift-cards' ), $number, $wpdb->pimwick_gift_card, $wpdb->last_error );
            }
        } else {
            return $wpdb->last_error;
        }

        return __( 'Unknown error in add_card method.', 'pw-woocommerce-gift-cards' );
    }

    public static function create_card( $note = '' ) {
        // Failsafe. If we haven't generated a number after this many tries, throw an error.
        $attempts = 0;
        $max_attempts = 100;

        // Get a random Card Number and insert it. If the insertion fails, it is already in use.
        do {
            $attempts++;

            $number = self::random_card_number();
            $gift_card = PW_Gift_Card::add_card( $number, $note );

        } while ( !( $gift_card instanceof self ) && $attempts < $max_attempts );

        return $gift_card;
    }

    public static function plugin_activate( $network_wide ) {
        global $wpdb;

        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        if ( is_multisite() && $network_wide ) {
            foreach ( $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" ) as $blog_id ) {
                switch_to_blog( $blog_id );

                PW_Gift_Card::create_tables();

                restore_current_blog();
            }
        } else {
            PW_Gift_Card::create_tables();
        }
    }

    public static function create_tables() {
        global $wpdb;

        // Call this again in case we're multisite and have switched sites.
        $wpdb->pimwick_gift_card = $wpdb->prefix . 'pimwick_gift_card';
        $wpdb->pimwick_gift_card_activity = $wpdb->prefix . 'pimwick_gift_card_activity';

        $wpdb->query( "
            CREATE TABLE IF NOT EXISTS `{$wpdb->pimwick_gift_card}` (
                `pimwick_gift_card_id` INT NOT NULL AUTO_INCREMENT,
                `number` TEXT NOT NULL,
                `active` TINYINT(1) NOT NULL DEFAULT 1,
                `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `expiration_date` DATE NULL,
                PRIMARY KEY (`pimwick_gift_card_id`),
                UNIQUE `{$wpdb->prefix}ix_pimwick_gift_card_number` ( `number` (128) )
            );
        " );

        if ( $wpdb->last_error != '' ) {
            wp_die( $wpdb->last_error );
        }
    }



    /*
     *
     * Private methods
     *
     */
    private function update_property( $property, $value ) {
        global $wpdb;

        if ( property_exists( $this, $property ) && $property != 'expiration_date' ) {
            if ( $this->{$property} != $value ) {
                $result = $wpdb->update( $wpdb->pimwick_gift_card, array ( $property => $value ), array( 'pimwick_gift_card_id' => $this->get_id() ) );

                if ( $result !== false ) {
                    $this->{$property} = $value;

                    do_action( 'pwgc_property_updated_' . $property, $this, $value );

                    return true;
                } else {
                    wp_die( $wpdb->last_error );
                }
            }

        } else {
            wp_die( sprintf( __( 'Property %s does not exist on %s', 'pw-woocommerce-gift-cards' ), $property, get_class() ) );
        }
    }

    private function log_activity( $action, $amount = null, $note = null, $reference_activity_id = null ) {
        PW_Gift_Card_Activity::record( $this->get_id(), $action, $amount, $note, $reference_activity_id );

        do_action( 'pwgc_activity_' . $action, $this, $amount, $note, $reference_activity_id );
    }

    public static function random_card_number() {

        $card_number = '';

        for ( $section = 0; $section < PWGC_RANDOM_CARD_NUMBER_SECTIONS; $section++ ) {
            for ( $code = 0; $code < PWGC_RANDOM_CARD_NUMBER_SECTION_LENGTH; $code++ ) {
                $random = str_shuffle( PWGC_RANDOM_CARD_NUMBER_CHARSET );
                $card_number .= $random[0];
            }

            if ( $section + 1 < PWGC_RANDOM_CARD_NUMBER_SECTIONS ) {
                $card_number .= '-';
            }
        }

        return apply_filters( 'pw_gift_cards_random_card_number', $card_number );
    }
}

register_activation_hook( PWGC_PLUGIN_FILE, array( 'PW_Gift_Card', 'plugin_activate' ) );

endif;

?>