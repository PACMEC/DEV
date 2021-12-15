<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @author MakeWebBetter <webmaster@makewebbetter.com>
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/admin
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */
class Pos_For_Woocommerce_Pos_Orders extends WP_List_Table {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function __construct() {

		parent::__construct(
			array(
				'singular' => 'singular_form',
				'plural' => 'plural_form',
				'ajax' => true,
			)
		);

	}

	/**
	 * Get columns.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function get_columns() {
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'order_title' => 'Order Title',
			'seller_name' => 'Seller Name',
			'customer_name' => 'Customer Name',
			'billing_addr' => 'Billing Address',
			'order_total' => 'Total',
		);
		return $columns;
	}

	/**
	 * Prepare_items.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function prepare_items() {
		$columns            = $this->get_columns();
		$hidden             = array();
		$sortable           = array();
		$mwb_all_orders_pos = array();
		$sortable           = $this->get_sortable_columns();
		$this->get_bulk_actions();
		$this->process_bulk_action();
		$mwb_all_orders_pos = $this->mwb_pos_get_orders();
		usort( $mwb_all_orders_pos, array( $this, 'usort_reorder' ) );
		$per_page     = 10;
		$current_page = $this->get_pagenum();
		$total_items  = count( $mwb_all_orders_pos );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
			)
		);

		$mwb_all_orders_pos    = array_slice( $mwb_all_orders_pos, ( ( $current_page - 1 ) * $per_page ), $per_page );
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $mwb_all_orders_pos;
	}

	/**
	 * Mwb_pos_get_orders.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_get_orders() {
		$mwb_prepare_pos_orders = array();
		$seller_name            = '';
		$orders                 = wc_get_orders( array( 'numberposts' => -1 ) );
		if ( is_array( $orders ) && ! empty( $orders ) ) {
			foreach ( $orders as $order ) {
				$order_id               = $order->get_id();
				$mwb_order_check_status = get_post_meta( $order_id, 'mwb_pos_order_hide_from_table', true );

				if ( ! isset( $mwb_order_check_status ) || ( 'no' === $mwb_order_check_status || '' === $mwb_order_check_status ) ) {
					$mwb_pos_post = get_post( $order_id );
					if ( is_object( $mwb_pos_post ) ) {
						$seller_name = get_user_by( 'id', $mwb_pos_post->post_author );
						$seller_name = $seller_name->user_nicename;
					}
					if ( 'yes' === get_post_meta( $order_id, 'mwb_pos_order', true ) ) {
						$mwb_prepare_pos_orders[] = array(
							'order_title' => '#Order ' . $order_id,
							'seller_name' => $seller_name,
							'customer_name' => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
							'billing_addr' => $order->get_billing_address_1() . ' ' . $order->get_billing_address_2(),
							'order_total' => $order->get_total(),
							'order-id' => $order_id,
							'order-uri' => $order->get_edit_order_url(),
						);
					}
				}
			}
		}
		return $mwb_prepare_pos_orders;
	}

	/**
	 * Column_default.
	 *
	 * @param array  $item item.
	 * @param string $column_name column_name.
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'seller_name':
			case 'customer_name':
			case 'billing_addr':
				return $item[ $column_name ];
			default:
				return 'N/A';
		}
	}

	/**
	 * Get_sortable_columns.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'order_title'  => array( 'order_title', false ),
			'customer_name'  => array( 'customer_name', false ),
			'order_total'  => array( 'order_total', false ),
		);
		return $sortable_columns;
	}

	/**
	 * Usort_reorder.
	 *
	 * @param array $a a.
	 * @param array $b b.
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function usort_reorder( $a, $b ) {
		// If no sort, default to title.
		$orderby = ( isset( $_GET['orderby'] ) && ! empty( $_GET['orderby'] ) ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'order_title'; //phpcs:disable
		// If no order, default to asc.
		$order  = ( ! empty( $_GET['order'] ) ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : 'asc';
		$result = strcmp( $a[ $orderby ], $b[ $orderby ] );
		// Send final sort direction to usort.
		return ( 'asc' === $order ) ? $result : -$result;
	}

	/**
	 * Get_bulk_actions.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function get_bulk_actions() {
		$actions = array(
			'delete'    => esc_html__( 'Delete', 'mwb-point-of-sale-woocommerce' ),
		);
		return $actions;
	}

	/**
	 * Process_bulk_action.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function process_bulk_action() {
		if ( 'delete' === $this->current_action() ) {
			if ( isset( $_POST['order_id'] ) && isset( $_POST['_mwb_pfw_nonce'] ) ) { //phpcs:disable
				$mwb_pfw_nonce = sanitize_text_field( wp_unslash( $_POST['_mwb_pfw_nonce'] ) );
				if ( wp_verify_nonce( $mwb_pfw_nonce, 'mwb_pfw__order_show' ) ) {
					if ( is_array( $_POST['order_id'] ) && ! empty( $_POST['order_id'] ) ) { //phpcs:disable
						$all_order_id = map_deep( wp_unslash( $_POST['order_id'] ), 'sanitize_text_field' );
						foreach ( $all_order_id as $order_id ) {
							update_post_meta( $order_id, 'mwb_pos_order_hide_from_table', 'yes' );
						}
					}
				}
			}
		} elseif ( 'recover' === $this->current_action() ) {
			$orders = wc_get_orders( array( 'numberposts' => -1 ) );
			if ( is_array( $orders ) && ! empty( $orders ) ) {
				foreach ( $orders as $order ) {
					update_post_meta( $order->get_id(), 'mwb_pos_order_hide_from_table', 'no' );
				}
			}
		}
	}

	/**
	 * Column_cb.
	 *
	 * @param array $item item.
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="order_id[]" value="%s" />',
			$item['order-id']
		);
	}


	/**
	 * Column_order_title.
	 *
	 * @param array $item item.
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function column_order_title( $item ) {
		$actions = array(
			'view'      => sprintf( '<a target="_blank" href="%s">Edit</a>', $item['order-uri'] ),
		);
		return sprintf( '%1$s %2$s', '<a target="_blank" href="' . $item['order-uri'] . '"><strong>' . $item['order_title'] . '</strong></a>', $this->row_actions( $actions ) );
	}


	/**
	 * Column_order_total.
	 *
	 * @param array $item item.
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function column_order_total( $item ) {
		return wc_price( $item['order_total'] );
	}
}

$mwb_pos_orders_obj = new Pos_For_Woocommerce_Pos_Orders();
