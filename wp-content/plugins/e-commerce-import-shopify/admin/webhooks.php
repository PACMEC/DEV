<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'IMPORT_SHOPIFY_TO_WOOCOMMERCE_ADMIN_Webhooks' ) ) {
	class IMPORT_SHOPIFY_TO_WOOCOMMERCE_ADMIN_Webhooks {
		protected $settings;
		protected $process;
		protected $process_for_update;

		public function __construct() {
			$this->settings = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_instance();
			add_action( 'admin_menu', array( $this, 'admin_menu' ), 21 );
			add_action( 'admin_init', array( $this, 'save_options' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		public function save_options() {
			global $s2w_settings;
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			if ( ! isset( $_POST['s2w_save_webhooks_options'] ) || ! $_POST['s2w_save_webhooks_options'] ) {
				return;
			}
			if ( ! isset( $_POST['_s2w_nonce'] ) || ! wp_verify_nonce( $_POST['_s2w_nonce'], 's2w_action_nonce' ) ) {
				return;
			}
			$args = array(
				'webhooks_shared_secret'    => isset( $_POST['s2w_webhooks_shared_secret'] ) ? sanitize_text_field( $_POST['s2w_webhooks_shared_secret'] ) : '',
				'webhooks_orders_enable'    => isset( $_POST['s2w_webhooks_orders_enable'] ) ? sanitize_text_field( $_POST['s2w_webhooks_orders_enable'] ) : '',
				'webhooks_products_enable'  => isset( $_POST['s2w_webhooks_products_enable'] ) ? sanitize_text_field( $_POST['s2w_webhooks_products_enable'] ) : '',
				'webhooks_customers_enable' => isset( $_POST['s2w_webhooks_customers_enable'] ) ? sanitize_text_field( $_POST['s2w_webhooks_customers_enable'] ) : '',
				'webhooks_orders_options'   => isset( $_POST['s2w_webhooks_orders_options'] ) ? array_map( 'stripslashes', $_POST['s2w_webhooks_orders_options'] ) : array(),
				'webhooks_products_options' => isset( $_POST['s2w_webhooks_products_options'] ) ? array_map( 'stripslashes', $_POST['s2w_webhooks_products_options'] ) : array(),
			);
			VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::update_option( 's2w_params', array_merge( $this->settings->get_params(), $args ) );
			$s2w_settings   = $args;
			$this->settings = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_instance(true);
		}

		public function admin_enqueue_scripts() {
			global $pagenow;
			$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';
			if ( $pagenow === 'admin.php' && $page === 'import-shopify-to-woocommerce-webhooks' ) {
				$this->enqueue_semantic();
				wp_enqueue_style( 'import-shopify-to-woocommerce-webhooks', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'webhooks.css' );
				wp_enqueue_script( 'import-shopify-to-woocommerce-webhooks', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'webhooks.js', array( 'jquery' ), VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION );
			}
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

		public function admin_menu() {
			add_submenu_page( 'import-shopify-to-woocommerce', esc_html__( 'Webhooks', 'import-shopify-to-woocommerce' ), esc_html__( 'Webhooks', 'import-shopify-to-woocommerce' ), 'manage_options', 'import-shopify-to-woocommerce-webhooks', array(
				$this,
				'page_callback'
			) );
		}

		public function page_callback() {
			?>
            <div class="wrap">
                <h2><?php esc_html_e( 'Webhooks', 'import-shopify-to-woocommerce' ) ?></h2>
				<?php IMPORT_SHOPIFY_TO_WOOCOMMERCE::security_recommendation_html(); ?>
                <p></p>
                <form class="vi-ui form" method="post">
					<?php wp_nonce_field( 's2w_action_nonce', '_s2w_nonce' ); ?>
                    <div class="vi-ui segment">
                        <table class="form-table">
                            <tbody>
                            <tr>
                                <th>
                                    <label for="<?php echo esc_attr( self::set( 'webhooks_shared_secret' ) ) ?>"><?php esc_html_e( 'Webhooks shared secret', 'import-shopify-to-woocommerce' ) ?></label>
                                </th>
                                <td>
                                    <input type="text"
                                           class="<?php echo esc_attr( self::set( 'webhooks_shared_secret' ) ) ?>"
                                           name="<?php echo esc_attr( self::set( 'webhooks_shared_secret', true ) ) ?>"
                                           id="<?php echo esc_attr( self::set( 'webhooks_shared_secret' ) ) ?>"
                                           value="<?php echo esc_attr( htmlentities( $this->settings->get_params( 'webhooks_shared_secret' ) ) ) ?>">
                                    <div class="vi-ui positive message">
                                        <ul class="list">
                                            <li><?php echo wp_kses_post( __( 'You can find your shared secret within the message at the bottom of Notifications settings in your Shopify admin: "All your webhooks will be signed with <strong>{your_shared_secret}</strong> so you can verify their integrity."', 'import-shopify-to-woocommerce' ) ) ?></li>
                                            <li><?php echo wp_kses_post( __( 'You must create at least 1 webhook to see the shared secret', 'import-shopify-to-woocommerce' ) ) ?></li>
                                            <li><?php echo wp_kses_post( __( 'Please read the <a href="http://docs.villatheme.com/import-shopify-to-woocommerce/#set_up_child_menu_4124" target="_blank">docs</a> for more details', 'import-shopify-to-woocommerce' ) ) ?></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="vi-ui segment">
                            <table class="form-table">
                                <tbody>
                                <tr>
                                    <th>
                                        <label for="<?php echo esc_attr( self::set( 'webhooks_orders_enable' ) ) ?>"><?php esc_html_e( 'Enable Orders', 'import-shopify-to-woocommerce' ) ?></label>
                                    </th>
                                    <td>
	                                    <?php IMPORT_SHOPIFY_TO_WOOCOMMERCE::upgrade_button();?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label for="<?php echo esc_attr( self::set( 'webhooks_orders_options' ) ) ?>"><?php esc_html_e( 'Update which?', 'import-shopify-to-woocommerce' ) ?></label>
                                    </th>
                                    <td>
										<?php
										$all_options             = array(
											'order_status'     => esc_html__( 'Order status', 'import-shopify-to-woocommerce' ),
											'order_date'       => esc_html__( 'Order date', 'import-shopify-to-woocommerce' ),
											'fulfillments'     => esc_html__( 'Order fulfillments', 'import-shopify-to-woocommerce' ),
											'billing_address'  => esc_html__( 'Billing address', 'import-shopify-to-woocommerce' ),
											'shipping_address' => esc_html__( 'Shipping address', 'import-shopify-to-woocommerce' ),
											'line_items'       => esc_html__( 'Line items', 'import-shopify-to-woocommerce' ),
										);
										$webhooks_orders_options = $this->settings->get_params( 'webhooks_orders_options' );
										?>
                                        <select id="<?php echo esc_attr( self::set( 'webhooks_orders_options' ) ) ?>"
                                                class="vi-ui fluid dropdown"
                                                name="<?php echo esc_attr( self::set( 'webhooks_orders_options', true ) ) ?>[]"
                                                multiple="multiple">
											<?php
											foreach ( $all_options as $all_option_k => $all_option_v ) {
												?>
                                                <option value="<?php echo esc_attr( $all_option_k ) ?>" <?php if ( in_array( $all_option_k, $webhooks_orders_options ) ) {
													echo esc_attr( 'selected' );
												} ?>><?php echo esc_html( $all_option_v ) ?></option>
												<?php
											}
											?>
                                        </select>
                                        <div class="description"><?php esc_html_e( 'This option is used for updating order via webhook', 'import-shopify-to-woocommerce' ) ?></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label><?php esc_html_e( 'Orders Webhook URL', 'import-shopify-to-woocommerce' ) ?></label>
                                    </th>
                                    <td>
                                        <div class="vi-ui fluid right labeled input <?php echo esc_attr( self::set( 'webhooks-url-container' ) ) ?>">
                                            <input type="text" readonly
                                                   class="<?php echo esc_attr( self::set( 'webhooks-url' ) ) ?>"
                                                   value="<?php echo esc_url( get_site_url( null, 'wp-json/s2w-import-shopify-to-woocommerce/orders' ) ) ?>">
                                            <i class="check green icon"></i>
                                            <label class="vi-ui label"><span
                                                        class="vi-ui tiny positive button <?php echo esc_attr( self::set( 'webhooks-url-copy' ) ) ?>"><?php esc_html_e( 'Copy', 'import-shopify-to-woocommerce' ) ?></span></label>
                                        </div>
                                        <div class="vi-ui positive message">
                                            <ul class="list">
                                                <li><?php echo wp_kses_post( __( 'If you want to <strong>only import new order when one is created</strong> at your Shopify store, create a webhook with event <strong>Order Creation</strong> and use this URL for the webhook URL.', 'import-shopify-to-woocommerce' ) ) ?></li>
                                                <li><?php echo wp_kses_post( __( 'If you want to both <strong>create new order when one is created and update existing order when one is updated</strong> at your Shopify store, create a webhook with event <strong>Order Update</strong> and use this URL for the webhook URL.', 'import-shopify-to-woocommerce' ) ) ?></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="vi-ui segment">
                            <table class="form-table">
                                <tbody>
                                <tr>
                                    <th>
                                        <label for="<?php echo esc_attr( self::set( 'webhooks_products_enable' ) ) ?>"><?php esc_html_e( 'Enable Products', 'import-shopify-to-woocommerce' ) ?></label>
                                    </th>
                                    <td>
	                                    <?php IMPORT_SHOPIFY_TO_WOOCOMMERCE::upgrade_button();?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label for="<?php echo esc_attr( self::set( 'webhooks_products_options' ) ) ?>"><?php esc_html_e( 'Update which?', 'import-shopify-to-woocommerce' ) ?></label>
                                    </th>
                                    <td>
										<?php
										$all_options               = array(
											'title'                => esc_html__( 'Product title', 'import-shopify-to-woocommerce' ),
											'price'                => esc_html__( 'Product price', 'import-shopify-to-woocommerce' ),
											'inventory'            => esc_html__( 'Product inventory', 'import-shopify-to-woocommerce' ),
											'description'          => esc_html__( 'Product description', 'import-shopify-to-woocommerce' ),
											'images'               => esc_html__( 'Product images', 'import-shopify-to-woocommerce' ),
											'variation_attributes' => esc_html__( 'Variation attributes', 'import-shopify-to-woocommerce' ),
											'variation_sku'        => esc_html__( 'Variation SKU', 'import-shopify-to-woocommerce' ),
											'product_url'          => esc_html__( 'Product slug', 'import-shopify-to-woocommerce' ),
										);
										$webhooks_products_options = $this->settings->get_params( 'webhooks_products_options' );
										?>
                                        <select id="<?php echo esc_attr( self::set( 'webhooks_products_options' ) ) ?>"
                                                class="vi-ui fluid dropdown"
                                                name="<?php echo esc_attr( self::set( 'webhooks_products_options', true ) ) ?>[]"
                                                multiple="multiple">
											<?php
											foreach ( $all_options as $all_option_k => $all_option_v ) {
												?>
                                                <option value="<?php echo esc_attr( $all_option_k ) ?>" <?php if ( in_array( $all_option_k, $webhooks_products_options ) ) {
													echo esc_attr( 'selected' );
												} ?>><?php echo esc_html( $all_option_v ) ?></option>
												<?php
											}
											?>
                                        </select>
                                        <div class="description"><?php esc_html_e( 'This option is used for updating product via webhook', 'import-shopify-to-woocommerce' ) ?></div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label><?php esc_html_e( 'Products Webhook URL', 'import-shopify-to-woocommerce' ) ?></label>
                                    </th>
                                    <td>
                                        <div class="vi-ui fluid right labeled input <?php echo esc_attr( self::set( 'webhooks-url-container' ) ) ?>">
                                            <input type="text" readonly
                                                   class="<?php echo esc_attr( self::set( 'webhooks-url' ) ) ?>"
                                                   value="<?php echo esc_url( get_site_url( null, 'wp-json/s2w-import-shopify-to-woocommerce/products' ) ) ?>">
                                            <i class="check green icon"></i>
                                            <label class="vi-ui label"><span
                                                        class="vi-ui tiny positive button <?php echo esc_attr( self::set( 'webhooks-url-copy' ) ) ?>"><?php esc_html_e( 'Copy', 'import-shopify-to-woocommerce' ) ?></span></label>
                                        </div>
                                        <div class="vi-ui positive message">
                                            <ul class="list">
                                                <li><?php echo wp_kses_post( __( 'If you want to <strong>only import new product when one is created</strong> at your Shopify store, create a webhook with event <strong>Product Creation</strong> and use this URL for the webhook URL.', 'import-shopify-to-woocommerce' ) ) ?></li>
                                                <li><?php echo wp_kses_post( __( 'If you want to both <strong>create new product when one is created and update existing product when one is updated</strong> at your Shopify store, create a webhook with event <strong>Product Update</strong> and use this URL for the webhook URL.', 'import-shopify-to-woocommerce' ) ) ?></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="vi-ui segment">
                            <table class="form-table">
                                <tbody>
                                <tr>
                                    <th>
                                        <label for="<?php echo esc_attr( self::set( 'webhooks_customers_enable' ) ) ?>"><?php esc_html_e( 'Enable Customers', 'import-shopify-to-woocommerce' ) ?></label>
                                    </th>
                                    <td>
	                                    <?php IMPORT_SHOPIFY_TO_WOOCOMMERCE::upgrade_button();?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        <label><?php esc_html_e( 'Customers Webhook URL', 'import-shopify-to-woocommerce' ) ?></label>
                                    </th>
                                    <td>
                                        <div class="vi-ui fluid right labeled input <?php echo esc_attr( self::set( 'webhooks-url-container' ) ) ?>">
                                            <input type="text" readonly
                                                   class="<?php echo esc_attr( self::set( 'webhooks-url' ) ) ?>"
                                                   value="<?php echo esc_url( get_site_url( null, 'wp-json/s2w-import-shopify-to-woocommerce/customers' ) ) ?>">
                                            <i class="check green icon"></i>
                                            <label class="vi-ui label"><span
                                                        class="vi-ui tiny positive button <?php echo esc_attr( self::set( 'webhooks-url-copy' ) ) ?>"><?php esc_html_e( 'Copy', 'import-shopify-to-woocommerce' ) ?></span></label>
                                        </div>
                                        <div class="vi-ui positive message">
                                            <ul class="list">
                                                <li><?php echo wp_kses_post( __( 'If you want to <strong>only import new customer when one is created</strong> at your Shopify store, create a webhook with event <strong>Customer Creation</strong> and use this URL for the webhook URL.', 'import-shopify-to-woocommerce' ) ) ?></li>
                                                <li><?php echo wp_kses_post( __( 'If you want to both <strong>create new customer when one is created and update existing customer when one is updated</strong> at your Shopify store, create a webhook with event <strong>Customer Update</strong> and use this URL for the webhook URL.', 'import-shopify-to-woocommerce' ) ) ?></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <p>
                        <input type="submit" class="vi-ui button primary" name="s2w_save_webhooks_options"
                               value="<?php esc_html_e( 'Save', 'import-shopify-to-woocommerce' ) ?> "/>
                    </p>
                </form>
            </div>
			<?php
		}

		public static function set( $name, $set_name = false ) {
			return VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::set( $name, $set_name );
		}

	}
}