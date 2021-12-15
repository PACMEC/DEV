<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'IMPORT_SHOPIFY_TO_WOOCOMMERCE_ADMIN_Cron_Update_Orders' ) ) {
	class IMPORT_SHOPIFY_TO_WOOCOMMERCE_ADMIN_Cron_Update_Orders {
		protected $settings;
		protected $update_orders;
		protected $get_data_to_update;
		protected $next_schedule;

		public function __construct() {
			$this->settings = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_instance();
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_script' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 25 );
		}

		public static function set( $name, $set_name = false ) {
			return VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::set( $name, $set_name );
		}

		public function admin_menu() {
			add_submenu_page( 'import-shopify-to-woocommerce', esc_html__( 'Cron Update Orders', 'import-shopify-to-woocommerce' ), esc_html__( 'Cron Update Orders', 'import-shopify-to-woocommerce' ), 'manage_options', 'import-shopify-to-woocommerce-cron-update-orders', array(
				$this,
				'page_callback'
			) );
		}

		public function page_callback() {
			?>
            <div class="wrap">
                <h2><?php esc_html_e( 'Cron Update Orders', 'import-shopify-to-woocommerce' ) ?></h2>
                <div class="<?php esc_attr_e( self::set( 'security-warning' ) ) ?>">
                    <div class="vi-ui warning message">
                        <div class="header">
				            <?php esc_html_e( 'Shopify Admin API security recommendation', 'import-shopify-to-woocommerce' ); ?>
                        </div>
                        <ul class="list">
                            <li><?php esc_html_e( 'You should enable only what is necessary for your app to work.', 'import-shopify-to-woocommerce' ); ?></li>
                            <li><?php esc_html_e( 'Treat the API key and password like you would any other password, since whoever has access to these credentials has API access to the store.', 'import-shopify-to-woocommerce' ); ?></li>
                            <li><?php esc_html_e( 'Change your API at least once a month', 'import-shopify-to-woocommerce' ); ?></li>
                            <li><?php esc_html_e( 'If you only use API to import data, remove API permissions or delete the API after import completed', 'import-shopify-to-woocommerce' ); ?></li>
                        </ul>
                    </div>
                </div>
                <p></p>
                <form class="vi-ui form" method="post">
					<?php wp_nonce_field( 's2w_action_nonce', '_s2w_nonce' ); ?>
                    <div class="vi-ui segment">
                        <div class="vi-ui negative message">
                            <?php esc_html_e( 'Cron Update Orders is currently DISABLED', 'import-shopify-to-woocommerce' ); ?>
                            <?php IMPORT_SHOPIFY_TO_WOOCOMMERCE::upgrade_button();?>
                        </div>
                        <table class="form-table">
                            <tbody>
                            <tr>
                                <th>
                                    <label for="<?php esc_attr_e( self::set( 'cron_update_orders_interval' ) ) ?>"><?php esc_html_e( 'Run update every', 'import-shopify-to-woocommerce' ) ?></label>
                                </th>
                                <td>
                                    <div class="vi-ui right labeled input">
                                        <input type="number" min="1"
                                               name="<?php esc_attr_e( self::set( 'cron_update_orders_interval', true ) ) ?>"
                                               id="<?php esc_attr_e( self::set( 'cron_update_orders_interval' ) ) ?>"
                                               value="<?php esc_attr_e( $this->settings->get_params( 'cron_update_orders_interval' ) ) ?>">
                                        <label for="<?php esc_attr_e( self::set( 'cron_update_orders_interval' ) ) ?>"
                                               class="vi-ui label"><?php esc_html_e( 'Day(s)', 'import-shopify-to-woocommerce' ) ?></label>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="<?php esc_attr_e( self::set( 'cron_update_orders_hour' ) ) ?>"><?php esc_html_e( 'Run update at', 'import-shopify-to-woocommerce' ) ?></label>
                                </th>
                                <td>
                                    <div class="equal width fields">
                                        <div class="field">
                                            <div class="vi-ui right labeled input">
                                                <input type="number" min="0" max="23"
                                                       name="<?php esc_attr_e( self::set( 'cron_update_orders_hour', true ) ) ?>"
                                                       id="<?php esc_attr_e( self::set( 'cron_update_orders_hour' ) ) ?>"
                                                       value="<?php esc_attr_e( $this->settings->get_params( 'cron_update_orders_hour' ) ) ?>">
                                                <label for="<?php esc_attr_e( self::set( 'cron_update_orders_hour' ) ) ?>"
                                                       class="vi-ui label"><?php esc_html_e( 'Hour', 'import-shopify-to-woocommerce' ) ?></label>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <div class="vi-ui right labeled input">
                                                <input type="number" min="0" max="59"
                                                       name="<?php esc_attr_e( self::set( 'cron_update_orders_minute', true ) ) ?>"
                                                       id="<?php esc_attr_e( self::set( 'cron_update_orders_minute' ) ) ?>"
                                                       value="<?php esc_attr_e( $this->settings->get_params( 'cron_update_orders_minute' ) ) ?>">
                                                <label for="<?php esc_attr_e( self::set( 'cron_update_orders_minute' ) ) ?>"
                                                       class="vi-ui label"><?php esc_html_e( 'Minute', 'import-shopify-to-woocommerce' ) ?></label>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <div class="vi-ui right labeled input">
                                                <input type="number" min="0" max="59"
                                                       name="<?php esc_attr_e( self::set( 'cron_update_orders_second', true ) ) ?>"
                                                       id="<?php esc_attr_e( self::set( 'cron_update_orders_second' ) ) ?>"
                                                       value="<?php esc_attr_e( $this->settings->get_params( 'cron_update_orders_second' ) ) ?>">
                                                <label for="<?php esc_attr_e( self::set( 'cron_update_orders_second' ) ) ?>"
                                                       class="vi-ui label"><?php esc_html_e( 'Second', 'import-shopify-to-woocommerce' ) ?></label>
                                            </div>
                                        </div>
                                    </div>

                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="<?php esc_attr_e( self::set( 'cron_update_orders_status' ) ) ?>"><?php esc_html_e( 'Only update orders with status:', 'import-shopify-to-woocommerce' ) ?></label>
                                </th>
                                <td>
                                    <select class="vi-ui fluid dropdown"
                                            name="<?php esc_attr_e( self::set( 'cron_update_orders_status', true ) ) ?>[]"
                                            multiple="multiple"
                                            id="<?php esc_attr_e( self::set( 'cron_update_orders_status' ) ) ?>">
										<?php
										$cron_update_orders_status = $this->settings->get_params( 'cron_update_orders_status' );
										$options                   = wc_get_order_statuses();
										foreach ( $options as $option_k => $option_v ) {
											?>
                                            <option value="<?php echo $option_k ?>"<?php if ( in_array( $option_k, $cron_update_orders_status ) )
												esc_attr_e( 'selected' ) ?>><?php echo $option_v; ?></option>
											<?php
										}
										?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="<?php esc_attr_e( self::set( 'cron_update_orders_range' ) ) ?>"><?php esc_html_e( 'Only update orders created in the last x day(s):', 'import-shopify-to-woocommerce' ) ?></label>
                                </th>
                                <td>
                                    <div class="vi-ui right labeled input">
                                        <input type="number" min="0" max=""
                                               name="<?php esc_attr_e( self::set( 'cron_update_orders_range', true ) ) ?>"
                                               id="<?php esc_attr_e( self::set( 'cron_update_orders_range' ) ) ?>"
                                               value="<?php esc_attr_e( $this->settings->get_params( 'cron_update_orders_range' ) ) ?>">
                                        <label for="<?php esc_attr_e( self::set( 'cron_update_orders_range' ) ) ?>"
                                               class="vi-ui label"><?php esc_html_e( 'Day(s)', 'import-shopify-to-woocommerce' ) ?></label>
                                    </div>
                                    <p class="description"><?php esc_html_e( 'Set 0 to skip filtering orders by date range', 'import-shopify-to-woocommerce' ) ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="<?php esc_attr_e( self::set( 'cron_update_orders_options' ) ) ?>"><?php esc_html_e( 'Select options to update', 'import-shopify-to-woocommerce' ) ?></label>
                                </th>
                                <td>
                                    <select class="vi-ui fluid dropdown"
                                            name="<?php esc_attr_e( self::set( 'cron_update_orders_options', true ) ) ?>[]"
                                            multiple="multiple"
                                            id="<?php esc_attr_e( self::set( 'cron_update_orders_options' ) ) ?>">
										<?php
										$cron_update_orders_options = $this->settings->get_params( 'cron_update_orders_options' );
										$options                    = array(
											'status'           => esc_html__( 'Status', 'import-shopify-to-woocommerce' ),
											'billing_address'  => esc_html__( 'Billing Address', 'import-shopify-to-woocommerce' ),
											'shipping_address' => esc_html__( 'Shipping Address', 'import-shopify-to-woocommerce' ),
											'fulfillments'     => esc_html__( 'Fulfillments', 'import-shopify-to-woocommerce' ),
										);
										foreach ( $options as $option_k => $option_v ) {
											?>
                                            <option value="<?php echo $option_k ?>"<?php if ( in_array( $option_k, $cron_update_orders_options ) )
												esc_attr_e( 'selected' ) ?>><?php echo $option_v; ?></option>
											<?php
										}
										?>
                                    </select>
                                    <p class="description"><?php esc_html_e( 'Which order data do you want to update?', 'import-shopify-to-woocommerce' ) ?></p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </form>
            </div>
			<?php
		}
		
		public function enqueue_semantic() {
			/*Stylesheet*/
			wp_enqueue_style( 'import-shopify-to-woocommerce-form', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'form.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-table', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'table.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-icon', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'icon.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-segment', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'segment.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-button', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'button.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-label', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'label.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-input', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'input.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-checkbox', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'checkbox.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-transition', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'transition.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-dropdown', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'dropdown.min.css' );
			wp_enqueue_style( 'import-shopify-to-woocommerce-message', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'message.min.css' );
			wp_enqueue_style( 'select2', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'select2.min.css' );
			wp_enqueue_script( 'select2-v4', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'select2.js', array( 'jquery' ) );
			wp_enqueue_script( 'import-shopify-to-woocommerce-transition', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'transition.min.js' );
			wp_enqueue_script( 'import-shopify-to-woocommerce-dropdown', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'dropdown.min.js' );
		}

		public function admin_enqueue_script( $page ) {
			if ( $page === 'shopify-to-woo_page_import-shopify-to-woocommerce-cron-update-orders' ) {
				$this->enqueue_semantic();
				wp_enqueue_style( 'import-shopify-to-woocommerce-cron-update-orders', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'cron-update-orders.css' );
				wp_enqueue_script( 'import-shopify-to-woocommerce-cron-update-orders', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'cron-update-orders.js', array( 'jquery' ), VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION );
			}
		}
	}
}
