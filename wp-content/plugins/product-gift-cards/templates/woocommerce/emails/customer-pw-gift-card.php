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

if ( ! isset( $item_data->preview ) ) {
    do_action( 'woocommerce_email_header', $email_heading, $email );
}

?>

<style type="text/css">
    @font-face {
        font-family: 'Roboto';
        font-style: normal;
        font-weight: 400;
        src: local('Roboto'), local('Roboto-Regular'), url(https://fonts.gstatic.com/s/roboto/v15/CrYjSnGjrRCn0pd9VQsnFOvvDin1pK8aKteLpeZ5c0A.woff) format('woff');
    }

    @font-face {
        font-family: 'Roboto';
        font-style: normal;
        font-weight: 700;
        src: local('Roboto Bold'), local('Roboto-Bold'), url(https://fonts.gstatic.com/s/roboto/v15/d-6IYplOFocCacKzxwXSOLO3LdcAZYWl9Si6vvxL-qU.woff) format('woff');
    }

    .pwgc-email-section {
        font-family: 'Roboto', Helvetica, Arial, sans-serif;
        color: #333333;
        margin: 24px 0;
    }

    #pwgc-email-title {
        font-size: 150%;
        font-weight: bold;
        line-height: 1.4;
        text-align: center;
    }

    #pwgc-email-message {
        margin-top: 0px;
    }

    .pwgc-email-label {
        font-size: 80%;
        color: #666666;
        line-height: 1.4;
    }

    #pwgc-email-gift-card-container td {
        padding-top: 12px;
        vertical-align: top;
    }

    #pwgc-email-gift-card-container {
        width: 100%;
        height: 275px;
        border-style: solid;
        border-width: 1px;
        border-color: #333;
        border-radius: 16px;
        background-position: center;
        background-repeat: no-repeat;
        background-size: auto 100%;
    }

    #pwgc-email-amount {
        font-size: 250%;
        line-height: 1.0;
    }

    #pwgc-email-card-number {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 600;
        font-size: 125%;
        line-height: 1.0;
    }

    #pwgc-email-redeem-button {
        border: none;
        padding: 15px 32px;
        text-align: center;
        border-radius: 6px;
        display: inline-block;
    }

    #pwgc-email-redeem-button a {
        font-size: 16px;
        text-decoration: none;
        display: inline-block;
    }

    <?php
        foreach ( $GLOBALS['pw_gift_cards']->design_colors as $key => $map ) {
            $value = '';

            if ( isset( $item_data->design[ $key ] ) ) {
                $value = $item_data->design[ $key ];
            } else if ( isset( $map[2] ) ) {
                $value = $map[2];
            }

            if ( !empty( $value ) ) {
                echo "$map[0] { $map[1]: $value; }\n";
            }
        }
    ?>
</style>
<?php

    if ( !empty( $item_data->from ) ) {
        ?>
        <div id="pwgc-email-from" class="pwgc-email-section pwgc-email-to-message">
            <?php _e( 'From', 'pw-woocommerce-gift-cards' ); ?>: <?php echo $item_data->from; ?>
        </div>
        <?php
    }

    if ( !empty( $item_data->message ) ) {
        ?>
        <div id="pwgc-email-message" class="pwgc-email-section pwgc-email-to-message">
            <?php echo nl2br( $item_data->message ); ?>
        </div>
        <?php
    }
?>
<table id="pwgc-email-gift-card-container" height="275" width="100%">
    <tr>
        <td width="10"></td>
        <td id="pwgc-email-title">
            <?php printf( __( '%s Gift Card', 'pw-woocommerce-gift-cards' ), get_option( 'blogname' ) ); ?>
        </td>
        <td width="10"></td>
    </tr>
    <tr>
        <td colspan="3">&nbsp;</td>
    </tr>
    <tr>
        <td width="10"></td>
        <td>
            <div id="pwgc-email-amount-label" class="pwgc-email-label"><?php _e( 'Amount', 'pw-woocommerce-gift-cards' ); ?></div>
            <div id="pwgc-email-amount"><?php echo wc_price( $item_data->amount, $item_data->wc_price_args ); ?></div>
        </td>
        <td width="10"></td>
    </tr>
    <tr>
        <td width="10"></td>
        <td>
            <div id="pwgc-email-card-number-label" class="pwgc-email-label"><?php _e( 'Gift Card Number', 'pw-woocommerce-gift-cards' ); ?></div>
            <div id="pwgc-email-card-number"><?php echo $item_data->gift_card_number; ?></div>
        </td>
        <td width="10"></td>
    </tr>
    <tr>
        <td width="10"></td>
        <td>
            <div id="pwgc-email-redeem-button">
                <a href="<?php echo $item_data->redeem_url; ?>"><?php _e( 'Redeem', 'pw-woocommerce-gift-cards' ); ?></a>
            </div>
        </td>
        <td width="10"></td>
    </tr>
</table>

<?php
    if ( ! isset( $item_data->preview ) ) {
        do_action( 'woocommerce_email_footer', $email );
    }
?>
