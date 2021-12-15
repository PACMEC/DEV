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

if ( ! class_exists( 'PW_Gift_Cards_Email_Manager' ) ) :

final class PW_Gift_Cards_Email_Manager {

    function __construct() {
        add_action( 'pw_gift_cards_send_emails', array( $this, 'trigger_email_action' ) );
        add_action( 'pw_gift_cards_send_email_manually', array( $this, 'trigger_manual_email_action' ), 10, 6 );
        add_filter( 'woocommerce_email_classes', array( $this, 'woocommerce_email_classes' ) );
        add_action( 'woocommerce_email_actions', array( $this, 'woocommerce_email_actions' ) );
    }

    function woocommerce_email_classes( $emails ) {
        if ( ! isset( $emails[ 'WC_Email_PW_Gift_Card' ] ) ) {
            $emails[ 'WC_Email_PW_Gift_Card' ] = include_once( 'emails/class-wc-email-pw-gift-card.php' );
        }

        return $emails;
    }

    function trigger_email_action( $order_id ) {
        if ( isset( $order_id ) && !empty( $order_id ) ) {
            WC_Emails::instance();
            do_action( 'pw_gift_cards_pending_email_notification', $order_id );
        }
    }

    function trigger_manual_email_action( $gift_card_number, $recipient, $from, $recipient_name, $message, $amount ) {
        if ( !empty( $gift_card_number ) && !empty( $recipient ) ) {
            WC_Emails::instance();
            do_action( 'pw_gift_cards_pending_manual_email_notification', $gift_card_number, $recipient, $from, $recipient_name, $message, $amount );
        }
    }

    function woocommerce_email_actions( $email_actions ) {
        $email_actions[] = 'pw_gift_cards_pending_email';
        $email_actions[] = 'pw_gift_cards_recipient_email';

        return $email_actions;
    }
}

new PW_Gift_Cards_Email_Manager();

endif;
