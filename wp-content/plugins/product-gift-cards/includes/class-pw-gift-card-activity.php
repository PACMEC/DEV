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

if ( ! class_exists( 'PW_Gift_Card_Activity' ) ) :

class PW_Gift_Card_Activity {

    /*
     *
     * Properties
     *
     */
    public function get_id() { return $this->pimwick_gift_card_activity_id; }
    protected function set_id( $id ) { $this->pimwick_gift_card_activity_id = $id; }
    private $pimwick_gift_card_activity_id;

    public function get_gift_card_id() { return $this->pimwick_gift_card_id; }
    protected function set_gift_card_id( $gift_card_id ) { $this->pimwick_gift_card_id = $gift_card_id; }
    private $pimwick_gift_card_id;

    public function get_activity_date() { return $this->activity_date; }
    protected function set_activity_date( $activity_date ) { $this->activity_date = $activity_date; }
    private $activity_date;

    public function get_action() { return $this->action; }
    protected function set_action( $action ) { $this->action = $action; }
    private $action;

    public function get_amount() { return $this->amount; }
    protected function set_amount( $amount ) { $this->amount = $amount; }
    private $amount;

    public function get_note() { return $this->note; }
    protected function set_note( $note ) { $this->note = $note; }
    private $note;

    public function get_reference_activity_id() { return $this->reference_activity_id; }
    protected function set_reference_activity_id( $reference_activity_id ) { $this->reference_activity_id = $reference_activity_id; }
    private $reference_activity_id;



    /*
     *
     * Static Methods
     *
     */
    public static function get_card_activity( $gift_card, $limit = 0 ) {
        global $wpdb;

        $activity_records = array();

        $results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `{$wpdb->pimwick_gift_card_activity}` WHERE pimwick_gift_card_id = %d ORDER BY `activity_date` LIMIT %d", $gift_card->get_id(), absint( $limit ) ) );
        if ( null !== $results ) {
            foreach ( $results as $row ) {

                $activity = new PW_Gift_Card_Activity();

                $activity->set_id( $result->pimwick_gift_card_activity_id );
                $activity->set_gift_card_id( $result->pimwick_gift_card_id );
                $activity->set_activity_date( $result->activity_date );
                $activity->set_action( $result->action );
                $activity->set_amount( $result->amount );
                $activity->set_note( $result->note );
                $activity->set_reference_activity_id( $result->reference_activity_id );

                $activity_records[] = $activity;
            }

        } else {
            wp_die( sprintf( __( 'Could not find activity record %d.', 'pw-woocommerce-gift-cards' ), $id ) );
        }

        $wpdb->get_results( $wpdb->prepare( "SELECT * FROM `{$wpdb->pimwick_gift_card_activity}` WHERE pimwick_gift_card_activity_id = %d", $id ) );

        return $activity_records;
    }

    public static function record( $gift_card_id, $action, $amount = null, $note = null, $reference_activity_id = null ) {
        global $wpdb;

        if ( !in_array( $action, array( 'create', 'transaction', 'deactivate', 'reactivate', 'note' ) ) ) {
            wp_die( __( 'Invalid action value: ', 'pw-woocommerce-gift-cards' ) . $action );
        }

        $result = $wpdb->insert( $wpdb->pimwick_gift_card_activity, array(
            'pimwick_gift_card_id'  => $gift_card_id,
            'action'                => $action,
            'amount'                => $amount,
            'note'                  => wc_clean( $note ),
            'user_id'               => get_current_user_id(),
            'reference_activity_id' => $reference_activity_id,
        ) );

        if ( $result ) {
            return $result;

        } else {
            wp_die( $wpdb->last_error );
        }
    }

    public static function plugin_activate( $network_wide ) {
        global $wpdb;

        if ( ! current_user_can( 'activate_plugins' ) ) {
            return;
        }

        if ( is_multisite() && $network_wide ) {
            foreach ( $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" ) as $blog_id ) {
                switch_to_blog( $blog_id );

                PW_Gift_Card_Activity::create_tables();

                restore_current_blog();
            }
        } else {
            PW_Gift_Card_Activity::create_tables();
        }
    }

    public static function create_tables() {
        global $wpdb;

        // Call this again in case we're multisite and have switched sites.
        $wpdb->pimwick_gift_card = $wpdb->prefix . 'pimwick_gift_card';
        $wpdb->pimwick_gift_card_activity = $wpdb->prefix . 'pimwick_gift_card_activity';

        $wpdb->query( "
            CREATE TABLE IF NOT EXISTS `{$wpdb->pimwick_gift_card_activity}` (
                `pimwick_gift_card_activity_id` INT NOT NULL AUTO_INCREMENT,
                `pimwick_gift_card_id` INT NOT NULL,
                `user_id` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
                `activity_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `action` VARCHAR(60) NOT NULL,
                `amount` DECIMAL(15,6) NULL DEFAULT NULL,
                `note` TEXT NULL DEFAULT NULL,
                `reference_activity_id` INT NULL DEFAULT NULL,
                PRIMARY KEY (`pimwick_gift_card_activity_id`),
                INDEX `{$wpdb->prefix}ix_pimwick_gift_card_id` (`pimwick_gift_card_id`)
            );
        " );
        if ( $wpdb->last_error != '' ) {
            wp_die( $wpdb->last_error );
        }

        // Drop the foreign key constraint if they exists.
        $foreign_keys = $wpdb->get_results( "
            SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = '{$wpdb->pimwick_gift_card_activity}' AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        " );

        foreach ( $foreign_keys as $row ) {
            $wpdb->query( "
                ALTER TABLE `{$wpdb->pimwick_gift_card_activity}` DROP FOREIGN KEY `{$row->CONSTRAINT_NAME}`;
            " );
        }
    }
}

register_activation_hook( PWGC_PLUGIN_FILE, array( 'PW_Gift_Card_Activity', 'plugin_activate' ) );

endif;

?>