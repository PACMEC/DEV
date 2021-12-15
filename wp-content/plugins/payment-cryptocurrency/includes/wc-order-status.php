<?php
/**
 * Custom WooCommerce order status "Quote Refresh Needed"
 */

/**
 * Register Order Status
 *
 * @param $order_statuses
 *
 * @return mixed
 */
function cryptowoo_register_custom_order_status( $order_statuses ) {

	// Status must start with "wc-"
	$order_statuses['wc-quote-refresh'] = array(
		'label'                     => _x( 'Quote Refresh Needed', 'Order status', 'woocommerce' ),
		'public'                    => false,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Quote Refresh Needed <span class="count">(%s)</span>', 'Quote Refresh Needed <span class="count">(%s)</span>', 'woocommerce' ),
	);
	return $order_statuses;
}
add_filter( 'woocommerce_register_shop_order_post_statuses', 'cryptowoo_register_custom_order_status' );

/**
 * Show Order Status in the Dropdown @ Single Order
 *
 * @param $order_statuses
 *
 * @return array
 */
function cryptowoo_show_custom_order_status( $order_statuses ) {

	$new_order_statuses = array();

	// Add new order status after processing
	foreach ( $order_statuses as $key => $status ) {

		$new_order_statuses[ $key ] = $status;

		if ( 'wc-processing' === $key ) {
			$new_order_statuses['wc-quote-refresh'] = _x( 'Quote Refresh Needed', 'Order status', 'woocommerce' );
		}
	}

	return $new_order_statuses;
}
add_filter( 'wc_order_statuses', 'cryptowoo_show_custom_order_status' );

/**
 * Show Order Status in the "Bulk Actions" @ Orders
 *
 * @param $bulk_actions
 *
 * @return mixed
 */
function cryptowoo_get_custom_order_status_bulk( $bulk_actions ) {
	// Note: "mark_" must be there instead of "wc"
	$bulk_actions['mark_quote-refresh'] = 'Change status to quote-refresh';
	return $bulk_actions;
}
// add_filter( 'bulk_actions-edit-shop_order', 'cryptowoo_get_custom_order_status_bulk' );

/**
 * Add quote-refresh Order Status to payable order statuses
 * Adding filter @ priority=11 to run this after other plugins (e.g. Woo OSM)
 *
 * @param $order_statuses array
 * @param $order          WC_Order
 *
 * @return array
 */
function cryptowoo_valid_order_statuses_for_payment( array $order_statuses, WC_Order $order ) {
	$order_statuses[] = 'quote-refresh';
	return $order_statuses;
}
add_filter( 'woocommerce_valid_order_statuses_for_payment', 'cryptowoo_valid_order_statuses_for_payment', 20, 2 );
