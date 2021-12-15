<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://makewebbetter.com/
 * @since      1.0.0
 *
 * @author MakeWebBetter <webmaster@makewebbetter.com>
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 * namespace pos_for_woocommerce_public.
 *
 * @package    MWB_Point_Of_Sale_Woocommerce
 * @subpackage MWB_Point_Of_Sale_Woocommerce/public
 * @author     MakeWebBetter <webmaster@makewebbetter.com>
 */
class Pos_For_Woocommerce_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @author   MakeWebBetter <webmaster@makewebbetter.com>
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @author   MakeWebBetter <webmaster@makewebbetter.com>
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_public_enqueue_styles() {
		wp_enqueue_style( $this->plugin_name, POS_FOR_WOOCOMMERCE_DIR_URL . 'public/src/scss/pos-for-woocommerce-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'mwb-pos-notifications', POS_FOR_WOOCOMMERCE_DIR_URL . 'public/src/scss/pos-for-woocommerce-notifications.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_public_enqueue_scripts() {

		wp_register_script( $this->plugin_name, POS_FOR_WOOCOMMERCE_DIR_URL . 'public/src/js/pos-for-woocommerce-public.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, 'pfw_public_param', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		wp_enqueue_script( $this->plugin_name );

		wp_enqueue_script( 'mwb-pos-app-js', POS_FOR_WOOCOMMERCE_DIR_URL . 'public/src/js/mwb-pos-app-build.js', array( 'jquery' ), time(), true );

	}

	/**
	 * Remove inline styles and scripts for pos page.
	 * 
	 * @param  array $all_styles_url collection of style urls.
	 * @return array $all_styles_url Return all style urls. 
	 */
	public function mwb_pos_panel_remove_admin_bar() {
		global $wp_query;

		if ( ! is_admin() ) {
			if ( isset( $wp_query->query['pagename'] ) && 'point-of-sale' === $wp_query->query['pagename'] ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Change the page template for POS panel on frontend.
	 *
	 * @param  string $template url of file selected.
	 * @return string $template return url of selected file.
	 * @since  1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_panel_template( $template ) {
		$mwb_pos_page = get_option( 'mwb_pos_page_exists', false );
		if ( isset( $mwb_pos_page ) && '' !== $mwb_pos_page ) {
			if ( is_page( $mwb_pos_page ) ) {
				$template = POS_FOR_WOOCOMMERCE_DIR_PATH . 'public/partials/pos-for-woocommerce-public-display.php';
			}
		}
		return $template;
	}

	/**
	 * Process all general settings data.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_config() {
		check_ajax_referer( 'mwb-pos-operarions', 'security' );
		global $wp_filesystem;
		WP_Filesystem();
		$manager_id                         = get_current_user_id();
		$mwb_pos_general_data               = $wp_filesystem->get_contents( POS_FOR_WOOCOMMERCE_DIR_PATH . 'components/settings-data/general-settings.json' );
		$mwb_pos_general_data               = json_decode( $mwb_pos_general_data, true );
		$mwb_pos_general_data['profileimg'] = get_avatar_url( $manager_id, 32 );
		$mwb_pos_general_data['crr_symbol'] = get_woocommerce_currency_symbol();
		echo wp_json_encode( $mwb_pos_general_data );
		wp_die();
	}

	/**
	 * Get woocommerce store currency symbol.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_get_currency_symb() {
		check_ajax_referer( 'mwb-pos-operarions', 'security' );
		$mwb_pos_currency_symbol = get_woocommerce_currency_symbol();
		echo wp_json_encode( $mwb_pos_currency_symbol );
		wp_die();
	}

	/**
	 * Validate user logged in or not.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_validate_user() {
		check_ajax_referer( 'mwb-pos-operarions', 'security' );
		global $wp_filesystem;
		WP_Filesystem();
		$mwb_posuser_data         = array();
		$mwb_pos_loging_settings  = $wp_filesystem->get_contents( POS_FOR_WOOCOMMERCE_DIR_PATH . 'components/settings-data/login-settings.json' );
		$mwb_posuser_data['data'] = json_decode( $mwb_pos_loging_settings, true );
		$mwb_pos_get_current_user = get_user_by( 'id', get_current_user_id() );
		if ( ! is_wp_error( $mwb_pos_get_current_user ) ) {
			if ( is_array( $mwb_pos_get_current_user->roles ) && in_array( 'administrator', $mwb_pos_get_current_user->roles, true ) ) {
				if ( is_user_logged_in() ) {
					$mwb_posuser_data['msg'] = 'success';
				} else {
					$mwb_posuser_data['msg'] = 'failed';
				}
			} else {
				$mwb_posuser_data['msg'] = 'failed';
			}
		} else {
			$mwb_posuser_data['msg'] = 'failed';
		}

		echo wp_json_encode( $mwb_posuser_data );
		wp_die();
	}

	/**
	 * Get login page settings data.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_login_page_settings() {
		check_ajax_referer( 'mwb-pos-operarions', 'security' );
		global $wp_filesystem;
		WP_Filesystem();
		$mwb_pos_loging_settings = $wp_filesystem->get_contents( POS_FOR_WOOCOMMERCE_DIR_PATH . 'components/settings-data/login-settings.json' );
		$mwb_pos_loging_settings = json_decode( $mwb_pos_loging_settings, true );
		echo wp_json_encode( $mwb_pos_loging_settings );
		wp_die();
	}

	/**
	 * Check current user existance.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_check_user_existence() {
		check_ajax_referer( 'mwb-pos-operarions', 'security' );
		$response_data      = array();
		$mwb_login_username = isset( $_POST['username'] ) ? sanitize_text_field( wp_unslash( $_POST['username'] ) ) : '';
		$mwb_login_userpass = isset( $_POST['userpass'] ) ? sanitize_text_field( wp_unslash( $_POST['userpass'] ) ) : '';
		$mwb_login_status   = isset( $_POST['remember'] ) ? sanitize_text_field( wp_unslash( $_POST['remember'] ) ) : false;
		if ( ( isset( $mwb_login_username ) && isset( $mwb_login_userpass ) ) && ( '' !== $mwb_login_username && '' !== $mwb_login_userpass ) ) {
			$creds                  = array();
			$creds['user_login']    = $mwb_login_username;
			$creds['user_password'] = $mwb_login_userpass;
			$creds['remember']      = $mwb_login_status;
			$user                   = wp_signon( $creds, false );

			if ( ! is_wp_error( $user ) ) {
				if ( is_array( $user->roles ) && in_array( 'administrator', $user->roles, true ) ) {
					$user_id     = $user->ID;
					$user_login = $user->user_email;
					wp_set_current_user( $user_id );
					wp_set_auth_cookie( $user_id, true, false );

					$mwb_pos_page = get_option( 'mwb_pos_page_exists', false );
					if ( isset( $mwb_pos_page ) && '' !== $mwb_pos_page ) {
						$mwb_pos_page_link = get_permalink( $mwb_pos_page );
					}

					$response_data['msg']  = 'success';
					$response_data['link'] = $mwb_pos_page_link;
					echo wp_json_encode( $response_data );
				} else {
					$mwb_pos_page = get_option( 'mwb_pos_page_exists', false );
					if ( isset( $mwb_pos_page ) && '' !== $mwb_pos_page ) {
						$mwb_pos_page_link = get_permalink( $mwb_pos_page );
					}
					$response_data['msg']  = 'failed';
					$response_data['link'] = $mwb_pos_page_link;
					echo wp_json_encode( $response_data );
				}
			} else {
				$mwb_pos_page = get_option( 'mwb_pos_page_exists', false );
				if ( isset( $mwb_pos_page ) && '' !== $mwb_pos_page ) {
					$mwb_pos_page_link = get_permalink( $mwb_pos_page );
				}
				$response_data['msg']  = 'failed';
				$response_data['link'] = $mwb_pos_page_link;
				echo wp_json_encode( $response_data );
			}
		} else {
			$mwb_pos_page = get_option( 'mwb_pos_page_exists', false );
			if ( isset( $mwb_pos_page ) && '' !== $mwb_pos_page ) {
				$mwb_pos_page_link = get_permalink( $mwb_pos_page );
			}
			$response_data['msg']  = 'failed';
			$response_data['link'] = $mwb_pos_page_link;
			echo wp_json_encode( $response_data );
		}
		wp_die();
	}

	/**
	 * Current users logout process.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_logout_user() {
		if ( is_user_logged_in() ) {
			wp_logout();
			$mwb_pos_page = get_option( 'mwb_pos_page_exists', false );
			if ( isset( $mwb_pos_page ) && '' !== $mwb_pos_page ) {
				$mwb_pos_page_link = get_permalink( $mwb_pos_page );
			}
			echo esc_attr( $mwb_pos_page_link );
		}
		wp_die();
	}

	/**
	 * List all woocommerce product categories.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_prod_category() {
		check_ajax_referer( 'mwb-pos-operarions', 'security' );
		$cat_list       = array();
		$args           = array(
			'taxonomy'     => 'product_cat',
			'orderby'      => 'name',
			'hierarchical' => false,
			'hide_empty'   => false,
		);
		$all_categories = get_categories( $args );
		if ( is_array( $all_categories ) && ! empty( $all_categories ) ) {
			foreach ( $all_categories as $all_category ) {
				if ( isset( $all_category ) && 'uncategorized' !== $all_category->slug ) {
					$cat_list[] = array(
						'title'  => $all_category->name,
						'cat_id' => $all_category->term_id,
						'slug'   => $all_category->slug,
					);
				}
			}
			echo wp_json_encode( $cat_list );
		}
		wp_die();
	}

	/**
	 * Get product by category id.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_get_category_prod() {
		check_ajax_referer( 'mwb-pos-operarions', 'security' );
		$mwb_prod_cat_slug = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '';
		if ( isset( $mwb_prod_cat_slug ) && 'all' !== $mwb_prod_cat_slug ) {
			$args = array(
				'post_status' => 'publish',
				'category'    => array( $mwb_prod_cat_slug ),
			);
		} elseif ( isset( $mwb_prod_cat_slug ) && 'all' === $mwb_prod_cat_slug ) {
			$args = array(
				'post_status' => 'publish',
				'limit'       => -1,
			);
		}
		$posts    = wc_get_products( $args );
		$products = array();
		if ( is_array( $posts ) && ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				if ( isset( $post ) && 'simple' === $post->get_type() ) {
					$products[] = $this->mwb_pos_prepare_simple_product( $post );
				} elseif ( isset( $post ) && 'variable' === $post->get_type() ) {

					$product_variations = $post->get_available_variations();
					if ( is_array( $product_variations ) && ! empty( $product_variations ) ) {
						foreach ( $product_variations as $product_variation ) {
							$_products[] = $this->mwb_pos_get_variations( $post, $product_variation );
						}
					}
				}
			}
		}
		if ( is_array( $products ) && ! empty( $products ) ) {
			if ( is_array( $_products ) && ! empty( $_products ) ) {
				$products = array_merge( $products, $_products );
			}
			echo wp_json_encode( $products );
		}
		wp_die();
	}

	/**
	 * Get List of searched products.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_get_search_prod() {
		check_ajax_referer( 'mwb-pos-operarions', 'security' );
		$mwb_prod_search = isset( $_POST['search'] ) ? sanitize_text_field( wp_unslash( $_POST['search'] ) ) : '';
		if ( isset( $mwb_prod_search ) && '' !== $mwb_prod_search ) {
			$_product    = array();
			$args        = array(
				'status' => 'publish',
				'limit'  => -1,
			);
			$allproducts = wc_get_products( $args );
			if ( is_array( $allproducts ) && ! empty( $allproducts ) ) {
				foreach ( $allproducts as $allproduct ) {
					if ( false !== strpos( $allproduct->get_slug(), $mwb_prod_search ) ) {
						if ( 'simple' === $allproduct->get_type() ) {
							$_product[] = $this->mwb_pos_prepare_simple_product( $allproduct );
						} elseif ( 'variable' === $allproduct->get_type() ) {
							$product_variations = $allproduct->get_available_variations();
							if ( is_array( $product_variations ) && ! empty( $product_variations ) ) {
								foreach ( $product_variations as $product_variation ) {
									$_products[] = $this->mwb_pos_get_variations( $allproduct, $product_variation );
								}
							}
						}
					}
				}
			}
		} else {
			$_product    = array();
			$args        = array(
				'status' => 'publish',
				'limit'  => -1,
			);
			$allproducts = wc_get_products( $args );
			if ( is_array( $allproducts ) && ! empty( $allproducts ) ) {
				foreach ( $allproducts as $allproduct ) {
					if ( 'simple' === $allproduct->get_type() ) {
						$_product[] = $this->mwb_pos_prepare_simple_product( $allproduct );
					} elseif ( 'variable' === $allproduct->get_type() ) {
						$product_variations = $allproduct->get_available_variations();
						if ( is_array( $product_variations ) && ! empty( $product_variations ) ) {
							foreach ( $product_variations as $product_variation ) {
								$_products[] = $this->mwb_pos_get_variations( $allproduct, $product_variation );
							}
						}
					}
				}
			}
		}
		if ( is_array( $_product ) && ! empty( $_product ) ) {
			if ( is_array( $_products ) && ! empty( $_products ) ) {
				$_product = array_merge( $_product, $_products );
			}
			echo wp_json_encode( $_product );
		}
		wp_die();
	}


	/**
	 * Get WooCommerce products.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_get_products() {
		check_ajax_referer( 'mwb-pos-operarions', 'security' );
		$_product = array();
		$args     = array(
			'status' => 'publish',
			'limit'  => -1,
		);
		$products = wc_get_products( $args );
		if ( is_array( $products ) && ! empty( $products ) ) {
			foreach ( $products as $product ) {
				if ( $product->is_type( 'simple' ) ) {
					$_product[] = $this->mwb_pos_prepare_simple_product( $product );
				} elseif ( $product->is_type( 'variable' ) ) {
					$product_variations = $product->get_available_variations();
					if ( is_array( $product_variations ) && ! empty( $product_variations ) ) {
						foreach ( $product_variations as $product_variation ) {
							$_products[] = $this->mwb_pos_get_variations( $product, $product_variation );
						}
					}
				}
			}
		}

		if ( is_array( $_products ) && ! empty( $_products ) ) {
			$_product = array_merge( $_product, $_products );
		}

		echo wp_json_encode( $_product );
		wp_die();
	}

	/**
	 * Prepare all simple products.
	 *
	 * @param object $prod_data collection of simple data array.
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_prepare_simple_product( $prod_data ) {
		$updated_prod       = array();
		$product_categories = array();
		if ( isset( $prod_data ) ) {
			$updated_prod['prod_id']            = $prod_data->get_id();
			$updated_prod['name']               = $prod_data->get_name();
			$updated_prod['slug']               = $prod_data->get_slug();
			$updated_prod['sku']                = $prod_data->get_sku();
			$updated_prod['created_date']       = $prod_data->get_date_created();
			$updated_prod['modified_date']      = $prod_data->get_date_modified();
			$updated_prod['status']             = $prod_data->get_status();
			$updated_prod['featured']           = $prod_data->get_featured();
			$updated_prod['descp']              = $prod_data->get_description();
			$updated_prod['short_descp']        = $prod_data->get_short_description();
			$updated_prod['permalink']          = get_permalink( $prod_data->get_id() );
			$updated_prod['catalog_visibility'] = $prod_data->get_catalog_visibility();
			$updated_prod['prod_menu_order']    = $prod_data->get_menu_order();
			// product prices.
			$updated_prod['crr_symbol']     = get_woocommerce_currency_symbol();
			$updated_prod['price']          = $prod_data->get_price();
			$updated_prod['regular_price']  = $prod_data->get_regular_price();
			$updated_prod['sale_price']     = $prod_data->get_sale_price();
			$updated_prod['on_sale_from']   = $prod_data->get_date_on_sale_from();
			$updated_prod['on_sale_to']     = $prod_data->get_date_on_sale_to();
			$updated_prod['number_of_sale'] = $prod_data->get_total_sales();
			// Get Product Stock.
			$updated_prod['manage_stock']       = $prod_data->get_manage_stock();
			$updated_prod['stock_status']       = $prod_data->get_stock_status();
			$updated_prod['backorders_allowed'] = $prod_data->get_backorders();

			if ( 'instock' === $prod_data->get_stock_status() && '' !== $prod_data->get_stock_quantity() ) {
				$updated_prod['quantity'] = 'in stock (' . $prod_data->get_stock_quantity() . ' )';
			} elseif ( 'instock' === $prod_data->get_stock_status() && '' === $prod_data->get_stock_quantity() ) {
				$updated_prod['quantity'] = 'in stock';
			} elseif ( 'instock' !== $prod_data->get_stock_status() ) {
				$updated_prod['quantity'] = 'out of stock';
			}

			// Get Product image and gallery images.
			$updated_prod['main_image'] = wp_get_attachment_url( $prod_data->get_image_id() );
		}
		return $updated_prod;
	}

	/**
	 * Prepare all variable products and it's variations.
	 *
	 * @param object $prod_data collection of variable data array.
	 * @since    1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_prepare_variable_product( $prod_data ) {
		$updated_prod       = array();
		$product_categories = array();
		if ( isset( $prod_data ) ) {
			$updated_prod['prod_id']            = $prod_data->get_id();
			$updated_prod['name']               = $prod_data->get_name();
			$updated_prod['slug']               = $prod_data->get_slug();
			$updated_prod['sku']                = $prod_data->get_sku();
			$updated_prod['status']             = $prod_data->get_status();
			$updated_prod['featured']           = $prod_data->get_featured();
			$updated_prod['descp']              = $prod_data->get_description();
			$updated_prod['short_descp']        = $prod_data->get_short_description();
			$updated_prod['permalink']          = get_permalink( $prod_data->get_id() );
			$updated_prod['catalog_visibility'] = $prod_data->get_catalog_visibility();
			$updated_prod['prod_menu_order']    = $prod_data->get_menu_order();
			// product prices.
			$updated_prod['crr_symbol']     = get_woocommerce_currency_symbol();
			$updated_prod['price']          = $prod_data->get_price();
			$updated_prod['regular_price']  = $prod_data->get_regular_price();
			$updated_prod['sale_price']     = $prod_data->get_sale_price();
			$updated_prod['on_sale_from']   = $prod_data->get_date_on_sale_from();
			$updated_prod['on_sale_to']     = $prod_data->get_date_on_sale_to();
			$updated_prod['number_of_sale'] = $prod_data->get_total_sales();
			// Get product Stock.
			$updated_prod['manage_stock']       = $prod_data->get_manage_stock();
			$updated_prod['stock_status']       = $prod_data->get_stock_status();
			$updated_prod['backorders_allowed'] = $prod_data->get_backorders();

			if ( 'instock' === $prod_data->get_stock_status() && '' !== $prod_data->get_stock_quantity() ) {
				$updated_prod['quantity'] = 'in stock (' . $prod_data->get_stock_quantity() . ' )';
			} elseif ( 'instock' === $prod_data->get_stock_status() && '' === $prod_data->get_stock_quantity() ) {
				$updated_prod['quantity'] = 'in stock';
			} elseif ( 'instock' !== $prod_data->get_stock_status() ) {
				$updated_prod['quantity'] = 'out of stock';
			}

			// Get Product image and gallery images.
			$updated_prod['main_image'] = wp_get_attachment_url( $prod_data->get_image_id() );
		}
		return $updated_prod;
	}

	/**
	 * Collect all variations.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @param  object $prod_data    Holds product data.
	 * @param  array  $product_variation    Holds product variation data.
	 */
	public function mwb_pos_get_variations( $prod_data, $product_variation ) {
		// Get all avialable variations data.
		$prepare_variation_data = array();
		if ( is_array( $product_variation ) && ! empty( $product_variation ) ) {
			if ( isset( $product_variation['image_id'] ) && '' !== $product_variation['image_id'] ) {
				$variation_img = wp_get_attachment_image_src( $product_variation['image_id'], 'shop_thumbnail' )[0];
			} else {
				$variation_img = '';
			}

			$prepare_variation_data['prod_id']            = $product_variation['variation_id'];
			$prepare_variation_data['name']               = rtrim( $prod_data->get_name() . '-' . implode( '-', $product_variation['attributes'] ), '-' );
			$prepare_variation_data['attributes']         = $product_variation['attributes'];
			$prepare_variation_data['backorders_allowed'] = $product_variation['backorders_allowed'];
			$prepare_variation_data['price']              = $product_variation['display_price'];
			$prepare_variation_data['regular_price']      = $product_variation['display_regular_price'];
			$prepare_variation_data['stock_status']       = $product_variation['is_in_stock'];
			$prepare_variation_data['min_qty']            = $product_variation['min_qty'];
			$prepare_variation_data['max_qty']            = $product_variation['max_qty'];
			$prepare_variation_data['sku']                = $product_variation['sku'];
			$prepare_variation_data['short_descp']        = wp_strip_all_tags( $product_variation['variation_description'] );
			$prepare_variation_data['main_image']         = $variation_img;
			$prepare_variation_data['crr_symbol']         = get_woocommerce_currency_symbol();
			if ( $product_variation['is_in_stock'] && '' !== $product_variation['max_qty'] ) {
				$prepare_variation_data['quantity'] = 'in stock ( ' . $product_variation['max_qty'] . ' )';
			} elseif ( $product_variation['is_in_stock'] && '' === $product_variation['max_qty'] ) {
				$prepare_variation_data['quantity'] = 'in stock';
			} elseif ( ! $product_variation['is_in_stock'] ) {
				$prepare_variation_data['quantity'] = 'out of stock';
			}
		}
		return $prepare_variation_data;
	}

	/**
	 * Collect manager's data.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_current_manager_data() {
		check_ajax_referer( 'mwb-pos-operarions', 'security' );
		if ( is_user_logged_in() ) {
			$manager_data     = array();
			$manager_id       = get_current_user_id();
			$mwb_user_manager = get_userdata( $manager_id );
			if ( is_object( $mwb_user_manager ) ) {
				if ( isset( $mwb_user_manager->data ) && is_object( $mwb_user_manager->data ) ) {
					$manager_data['fname']      = get_user_meta( $manager_id, 'first_name', true );
					$manager_data['lname']      = get_user_meta( $manager_id, 'last_name', true );
					$manager_data['nicename']   = isset( $mwb_user_manager->data->user_nicename ) ? $mwb_user_manager->data->user_nicename : '';
					$manager_data['phone']      = get_user_meta( $manager_id, 'billing_phone', true );
					$manager_data['email']      = isset( $mwb_user_manager->data->user_email ) ? $mwb_user_manager->data->user_email : '';
					$manager_data['profileimg'] = get_avatar_url( $manager_id, 32 );
					$manager_data['id']         = isset( $mwb_user_manager->data->ID ) ? $mwb_user_manager->data->ID : '';
				}
			}
			echo wp_json_encode( $manager_data );
		}
		wp_die();
	}

	/**
	 * Update manager profile.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_update_manager_profile() {
		check_ajax_referer( 'mwb-pos-operarions', 'security' );
		$user_id = isset( $_POST['manager_key'] ) ? explode( '-', map_deep( wp_unslash( $_POST['manager_key'] ), 'sanitize_text_field' ) ) : '';

		if ( is_array( $user_id ) && isset( $user_id[1] ) && '' !== $user_id[1] ) {
			$current_user_id       = $user_id[1];
			$managers_updated_data = isset( $_POST['manager_data'] ) ? map_deep( wp_unslash( $_POST['manager_data'] ), 'sanitize_text_field' ) : array();
			if ( is_array( $managers_updated_data ) && ! empty( $managers_updated_data ) ) {
				$manager_update                  = array();
				$manager_update['ID']            = $current_user_id;
				$manager_update['user_email']    = isset( $managers_updated_data['mwb-pos-manager-email'] ) ? $managers_updated_data['mwb-pos-manager-email'] : '';
				$manager_update['user_nicename'] = isset( $managers_updated_data['mwb-pos-manager-nickName'] ) ? $managers_updated_data['mwb-pos-manager-nickName'] : '';
				$manager_update['first_name']    = isset( $managers_updated_data['mwb-pos-manager-fname'] ) ? $managers_updated_data['mwb-pos-manager-fname'] : '';
				$manager_update['last_name']     = isset( $managers_updated_data['mwb-pos-manager-lname'] ) ? $managers_updated_data['mwb-pos-manager-lname'] : '';

				wp_update_user( $manager_update );

				if ( isset( $managers_updated_data['mwb-pos-manager-phone'] ) && '' !== $managers_updated_data['mwb-pos-manager-phone'] ) {
					update_user_meta( $current_user_id, 'billing_phone', $managers_updated_data['mwb-pos-manager-phone'] );
				}
				echo 'success';
			}
		}
		wp_die();
	}


	/**
	 * Create customer order.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 */
	public function mwb_pos_order_details() {
		check_ajax_referer( 'mwb-pos-operarions', 'security' );
		$cart_items              = isset( $_POST['cartItems'] ) ? map_deep( wp_unslash( $_POST['cartItems'] ), 'sanitize_text_field' ) : '';
		$cart_additional_details = isset( $_POST['cartData'] ) ? map_deep( wp_unslash( $_POST['cartData'] ), 'sanitize_text_field' ) : '';
		$customer_details        = isset( $_POST['customerData'] ) ? map_deep( wp_unslash( $_POST['customerData'] ), 'sanitize_text_field' ) : '';
		if ( is_array( $cart_items ) && is_array( $cart_additional_details ) && is_array( $customer_details ) ) {
			$order = $this->mwb_pos_create_order( $cart_items, $cart_additional_details, $customer_details );
			echo 'success';
		} else {
			echo 'failed';
		}

		wp_die();
	}

	/**
	 * Process the order data and create the orders for woocommerce.
	 *
	 * @since 1.0.0
	 * @author MakeWebBetter <webmaster@makewebbetter.com>
	 * @param      array $products        Holds product data.
	 * @param      array $cart_extra      Holds cart data.
	 * @param      array $cust_addr       Holds customers info.
	 */
	public function mwb_pos_create_order( $products, $cart_extra, $cust_addr ) {
		global $woocommerce;
		$address = array(
			'first_name' => isset( $cust_addr['mwb-pos-customer-fname'] ) ? $cust_addr['mwb-pos-customer-fname'] : '',
			'last_name'  => isset( $cust_addr['mwb-pos-customer-lname'] ) ? $cust_addr['mwb-pos-customer-lname'] : '',
			'email'      => isset( $cust_addr['mwb-pos-customer-email'] ) ? $cust_addr['mwb-pos-customer-email'] : '',
			'phone'      => isset( $cust_addr['mwb-pos-customer-phone'] ) ? $cust_addr['mwb-pos-customer-phone'] : '',
			'address_1'  => isset( $cust_addr['mwb-pos-customer-address1'] ) ? $cust_addr['mwb-pos-customer-address1'] : '',
			'address_2'  => isset( $cust_addr['mwb-pos-customer-address2'] ) ? $cust_addr['mwb-pos-customer-address2'] : '',
			'city'       => isset( $cust_addr['mwb-pos-customer-city'] ) ? $cust_addr['mwb-pos-customer-city'] : '',
			'state'      => isset( $cust_addr['mwb-pos-customer-state'] ) ? $cust_addr['mwb-pos-customer-state'] : '',
			'postcode'   => isset( $cust_addr['mwb-pos-customer-postcode'] ) ? $cust_addr['mwb-pos-customer-postcode'] : '',
			'country'    => isset( $cust_addr['mwb-pos-customer-country'] ) ? $cust_addr['mwb-pos-customer-country'] : '',
		);
		// Now we create the order.
		$order = wc_create_order( array( 'customer_id' => get_current_user_id() ) );
		if ( is_array( $products ) && ! empty( $products ) ) {
			foreach ( $products as $product ) {
				$_product     = wc_get_product( $product['id'] );
				$product_type = $_product->get_type();
				if ( 'variation' === $product_type ) {
					$variation_args = $_product->get_attributes();
					$order->add_product( $_product, $product['qty'], $variation_args ); // This is an existing VARIATION product.
				} elseif ( 'simple' === $product_type ) {
					$order->add_product( $_product, $product['qty'] ); // This is an existing SIMPLE product.
				}
			}
		}

		$order->set_address( $address, 'billing' );
		$order->set_address( $address, 'shipping' );

		if ( is_array( $cart_extra ) && ! empty( $cart_extra ) ) {
			if ( isset( $cart_extra['shipping'] ) && '' !== $cart_extra['shipping'] ) {
				$shipping_rate = new WC_Shipping_Rate( '', 'Flat Rate', floatval( $cart_extra['shipping'] ), array(), 'custom_shipping_method' );
				$order->add_shipping( $shipping_rate );
			}
			// Get a new instance of the WC_Order_Item_Fee Object.

			if ( isset( $cart_extra['tax'] ) && '' !== $cart_extra['tax'] ) {
				$item_fee_tax = new WC_Order_Item_Fee();
				$item_fee_tax->set_name( 'Vat' );
				$item_fee_tax->set_amount( floatval( $cart_extra['tax'] ) ); // Fee amount.
				$item_fee_tax->set_tax_class( '' );
				$item_fee_tax->set_tax_status( 'taxable' );
				$item_fee_tax->set_total( floatval( $cart_extra['tax'] ) );
				$order->add_item( $item_fee_tax );
				$order->calculate_totals();
			}

			if ( ( isset( $cart_extra['couponName'] ) && isset( $cart_extra['couponValue'] ) ) && ( '' !== $cart_extra['couponName'] && '' !== $cart_extra['couponValue'] ) ) {
				$item_fee = new WC_Order_Item_Fee();
				$item_fee->set_name( 'Discount for coupon {' . $cart_extra['couponName'] . '} ' ); // Generic fee name.
				$item_fee->set_amount( -floatval( $cart_extra['couponValue'] ) ); // Fee amount.
				$item_fee->set_total( -floatval( $cart_extra['couponValue'] ) );
				$order->add_item( $item_fee );
				$order->calculate_totals();
			}
		}

		$order->update_status( 'completed', 'Imported POS Order', true );
		update_post_meta( $order->get_id(), '_payment_method', $cart_extra['paymentmode'] );
		update_post_meta( $order->get_id(), '_payment_method_title', $cart_extra['paymentmode'] );
		update_post_meta( $order->get_id(), 'mwb_pos_order', 'yes' );

		$mwb_order_total_tax = $order->get_total_tax();
		$mwb_order_total = $order->get_total();
		if ( isset( $mwb_order_total_tax ) && '' !== $mwb_order_total_tax ) {
			$mwb_updated_total = $mwb_order_total - $mwb_order_total_tax;
		} else {
			$mwb_updated_total = $mwb_order_total;
		}
		$order->set_total( $mwb_updated_total );
		$order->save();

		return $order;
	}

		/**
	 * Remove unwanted styles and scripts for pos page.
	 * 
	 * @param  array $all_styles_url collection of style urls.
	 * @return array $all_styles_url Return all style urls. 
	 */
	public function mwb_pos_remove_theme_styles() {
		global $wp_query;
		global $wp_styles, $wp_scripts;
		$stylesheet_uri = get_stylesheet_directory_uri();
		if ( ! is_admin() ) {
			if ( isset( $wp_query->query['pagename'] ) && 'point-of-sale' === $wp_query->query['pagename'] ) {
				foreach ( $wp_styles->queue as $handle ) {
					$obj        = $wp_styles->registered[$handle];
					$obj_handle = $obj->handle;
					$obj_uri    = $obj->src;
					if ( strpos( $obj_uri, $stylesheet_uri ) === 0 ) {
						wp_dequeue_style($obj_handle);
					} 
				}

				foreach ( $wp_scripts->queue as $handles ) {
					$objs        = $wp_scripts->registered[$handles];
					$obj_handles = $objs->handle;
					$obj_uris    = $objs->src;
					if ( strpos( $obj_uris, $stylesheet_uri ) === 0 ) {
						wp_dequeue_script($obj_handles);
					} 
				}
			}
		}
	}

		/**
	 * Remove inline styles and scripts for pos page.
	 * 
	 * @param  array $all_styles_url collection of style urls.
	 * @return array $all_styles_url Return all style urls. 
	 */
	public function mwb_pos_remve_inline_styles( $styles ) {
		global $wp_query;
		if ( ! is_admin() ) {
			if ( isset( $wp_query->query['pagename'] ) && 'point-of-sale' === $wp_query->query['pagename'] ) {
				if ( is_array($styles) && count($styles) > 0 ) {
					foreach ($styles as $key => $code) {
						if ( 'mwb-point-of-sale-woocommerce' === $code || 'mwb-pos-notifications' === $code ) {
							continue;
						} else {
							unset( $styles[$key] );
						}
					}
				}
			}
		}
		return $styles;
	}
}
