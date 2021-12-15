<?php

defined( 'ABSPATH' ) or exit;

global $wpdb;

$active_count = 0;
$outstanding_balance = 0;

$price_decimals = wc_get_price_decimals();

$results = $wpdb->get_row( "
    SELECT
        COUNT(DISTINCT card.pimwick_gift_card_id) AS active_count,
        SUM(ROUND(activity.amount, $price_decimals)) AS outstanding_balance
    FROM
        {$wpdb->pimwick_gift_card} AS card
    JOIN
        {$wpdb->pimwick_gift_card_activity} AS activity ON (activity.pimwick_gift_card_id = card.pimwick_gift_card_id)
    WHERE
        card.active = 1
        AND (card.expiration_date IS NULL OR card.expiration_date >= NOW())
    ORDER BY
        card.create_date
" );
if ( null !== $results ) {
    $active_count = $results->active_count;
    $outstanding_balance = $results->outstanding_balance;
}

?>
<div class="pwgc-summary-item">
    <div class="pwgc-summary-item-header"><?php echo number_format( $active_count ); ?></div>
    <div><?php _e( 'Active gift cards', 'pw-woocommerce-gift-cards' ); ?></div>
</div>
<div class="pwgc-summary-item">
    <div class="pwgc-summary-item-header"><?php echo wc_price( $outstanding_balance ); ?></div>
    <div><?php _e( 'Outstanding balances', 'pw-woocommerce-gift-cards' ); ?></div>
</div>