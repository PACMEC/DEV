<?php
/**
 * Plugin Name: S2W - Import Shopify to WooCommerce
 * Plugin URI: https://villatheme.com/extensions/import-shopify-to-woocommerce
 * Description: Import all products from Shopify store to WooCommerce
 * Version: 1.1.3.7
 * Author: VillaTheme
 * Author URI: https://villatheme.com
 * Text Domain: import-shopify-to-woocommerce
 * Domain Path: /languages
 * Copyright 2019 VillaTheme.com. All rights reserved.
 * Tested up to: 5.8
 * WC tested up to: 5.5
 * Requires PHP: 7.0
 **/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
define( 'VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION', '1.1.3.7' );
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 's2w-import-shopify-to-woocommerce/s2w-import-shopify-to-woocommerce.php' ) ) {
	return;
}
define( 'VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DIR', WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "import-shopify-to-woocommerce" . DIRECTORY_SEPARATOR );
define( 'VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_INCLUDES', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DIR . "includes" . DIRECTORY_SEPARATOR );
if ( is_file( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_INCLUDES . "class-s2w-error-images-table.php" ) ) {
	require_once VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_INCLUDES . "class-s2w-error-images-table.php";
}
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	$init_file = WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "import-shopify-to-woocommerce" . DIRECTORY_SEPARATOR . "includes" . DIRECTORY_SEPARATOR . "define.php";
	require_once $init_file;
} else {
	add_action( 'admin_notices', 's2w_global_note' );
	/**
	 * Notify if WooCommerce is not activated
	 */
	function s2w_global_note() { ?>
        <div id="message" class="error">
            <p><?php esc_html_e( 'Please install and activate WooCommerce to use Import Shopify to WooCommerce plugin.', 'import-shopify-to-woocommerce' ); ?></p>
        </div>
		<?php
	}

	return;
}

if ( ! class_exists( 'IMPORT_SHOPIFY_TO_WOOCOMMERCE' ) ) {
	class IMPORT_SHOPIFY_TO_WOOCOMMERCE {
		protected $settings;
		protected $is_page;
		protected $request;
		protected $process;
		protected $process_new;

		public function __construct() {
			register_activation_hook( __FILE__, array( __CLASS__, 'register_activation_hook' ) );
			vi_s2w_init_set();
			$this->settings = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_instance();
			$this->is_page  = false;
			add_action( 'init', array( $this, 'init' ) );
			add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
			add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu_system_log' ), 20 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_script' ) );
			add_action( 'admin_init', array( $this, 'delete_import_history' ) );
			add_action( 'admin_init', array( $this, 'fix_the_wrong' ) );
			add_action( 'wp_ajax_s2w_save_settings', array( $this, 'save_settings' ) );
			add_action( 'wp_ajax_s2w_save_settings_product_options', array( $this, 'save_settings_product_options' ) );
			add_action( 'wp_ajax_s2w_import_shopify_to_woocommerce', array( $this, 'sync' ) );
			add_action( 'wp_ajax_s2w_search_cate', array( $this, 'search_cate' ) );
			add_filter(
				'plugin_action_links_import-shopify-to-woocommerce/import-shopify-to-woocommerce.php', array(
					$this,
					'settings_link'
				)
			);
			add_action( 'wp_ajax_s2w_view_log', array( $this, 'generate_log_ajax' ) );
			add_action( 'admin_init', array( $this, 'update_data_new_version' ) );
		}

		public static function register_activation_hook() {
			S2W_Error_Images_Table::create_table();
			S2W_Error_Images_Table::add_column( 'image_id' );
			S2W_Error_Images_Table::modify_column( 'image_id', 'varchar(200)' );
		}

		public function init() {
			if ( class_exists( 'VillaTheme_Support' ) ) {
				new VillaTheme_Support(
					array(
						'support'   => 'https://wordpress.org/support/plugin/import-shopify-to-woocommerce/',
						'docs'      => 'http://docs.villatheme.com/?item=import-shopify-to-woocommerce',
						'review'    => 'https://wordpress.org/support/plugin/import-shopify-to-woocommerce/reviews/?rate=5#rate-response',
						'pro_url'   => 'https://1.envato.market/9arQ5',
						'css'       => VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS,
						'image'     => VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_IMAGES,
						'slug'      => 'import-shopify-to-woocommerce',
						'menu_slug' => 'import-shopify-to-woocommerce',
						'version'   => VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION
					)
				);
			}
		}

		public function update_data_new_version() {
			if ( ! get_option( 'vi_s2w_update_data_new_version_free' ) ) {
				$files = glob( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CACHE . '*.txt' );
				VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::delete_files( $files );
				$dirs = glob( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CACHE . '*', GLOB_ONLYDIR );
				if ( is_array( $dirs ) && count( $dirs ) ) {
					$domain       = $this->settings->get_params( 'domain' );
					$api_key      = $this->settings->get_params( 'api_key' );
					$api_secret   = $this->settings->get_params( 'api_secret' );
					$new_dir_name = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_cache_path( $domain, $api_key, $api_secret );
					if ( $domain && $api_key && $api_secret ) {
						$shop_name_length = strlen( $domain );
						foreach ( $dirs as $dir ) {
							$dir_name = substr( $dir, ( strlen( $dir ) - $shop_name_length ), $shop_name_length );
							if ( $dir_name === $domain ) {
								if ( $new_dir_name !== $dir ) {
									if ( ! @rename( $dir, $new_dir_name ) ) {
										VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::deleteDir( $dir );
									}
								}
							} else {
								VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::deleteDir( $dir );
							}
						}
					} else {
						foreach ( $dirs as $dir ) {
							VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::deleteDir( $dir );
						}
					}
				}
				update_option( 'vi_s2w_update_data_new_version_free', time() );
			}
		}

		public static function set( $name, $set_name = false ) {
			return VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::set( $name, $set_name );
		}

		public function fix_the_wrong() {
			if ( ! get_option( 's2w_import_shopify_to_woocommerce_fix_the_wrong' ) ) {
				$args      = array(
					'post_type'      => 'product',
					'post_status'    => array( 'publish', 'pending', 'draft' ),
					'posts_per_page' => 500,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'key'     => '_s2w_shopipy_product_id',
							'compare' => 'EXISTS'
						)
					),
				);
				$the_query = new WP_Query( $args );
				if ( $the_query->have_posts() ) {
					while ( $the_query->have_posts() ) {
						$the_query->the_post();
						$product_id = get_the_ID();
						$shopify_id = get_post_meta( $product_id, '_s2w_shopipy_product_id', true );
						update_post_meta( $product_id, '_shopify_product_id', $shopify_id );
						delete_post_meta( $product_id, '_s2w_shopipy_product_id' );
					}
				} else {
					update_option( 's2w_import_shopify_to_woocommerce_fix_the_wrong', current_time( 'timestamp', true ) );
				}
				wp_reset_postdata();
			}
		}

		public function bump_request_timeout( $val ) {
			return $this->settings->get_params( 'request_timeout' );
		}

		public function generate_log_ajax() {
			/*Check the nonce*/
			if ( empty( $_GET['action'] ) || ! check_admin_referer( wp_unslash( sanitize_text_field( $_GET['action'] ) ) ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'import-shopify-to-woocommerce' ) );
			}
			if ( empty( $_GET['s2w_file'] ) ) {
				wp_die( esc_html__( 'No log file selected.', 'import-shopify-to-woocommerce' ) );
			}
			$file = urldecode( wp_unslash( sanitize_text_field( $_GET['s2w_file'] ) ) );
			if ( ! is_file( $file ) ) {
				wp_die( esc_html__( 'Log file not found.', 'import-shopify-to-woocommerce' ) );
			}
			echo( wp_kses_post( nl2br( file_get_contents( $file ) ) ) );
			exit();
		}

		public function delete_import_history() {
			global $pagenow;
			if ( $pagenow === 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] === 'import-shopify-to-woocommerce' && isset( $_POST['s2w_delete_history'] ) ) {
				$domain     = $this->settings->get_params( 'domain' );
				$api_key    = $this->settings->get_params( 'api_key' );
				$api_secret = $this->settings->get_params( 'api_secret' );
				$path       = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_cache_path( $domain, $api_key, $api_secret );
				if ( isset( $_POST['products'] ) && $_POST['products'] ) {
					VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::delete_option( 's2w_' . $this->settings->get_params( 'domain' ) . '_history' );
					$files = glob( $path . '/page_*.txt' );
					VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::delete_files( $files );
				}
				if ( isset( $_POST['product_categories'] ) && $_POST['product_categories'] ) {
					VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::delete_option( 's2w_' . $this->settings->get_params( 'domain' ) . '_history_product_categories' );
					$files = glob( $path . '/category_*.txt' );
					VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::delete_files( $files );
					VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::delete_files( $path . '/categories.txt' );
				}
				$this->settings = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_instance( true );
			}
		}

		public function settings_link( $links ) {
			$settings_link = '<a href="' . admin_url( 'admin.php' ) . '?page=import-shopify-to-woocommerce" title="' . esc_attr__( 'Settings', 'import-shopify-to-woocommerce' ) . '">' . esc_html__( 'Settings', 'import-shopify-to-woocommerce' ) . '</a>';
			array_unshift( $links, $settings_link );

			return $links;
		}

		public function search_cate() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			ob_start();
			$keyword = filter_input( INPUT_GET, 'keyword', FILTER_SANITIZE_STRING );
			if ( ! $keyword ) {
				$keyword = filter_input( INPUT_POST, 'keyword', FILTER_SANITIZE_STRING );
			}
			if ( empty( $keyword ) ) {
				die();
			}
			$categories = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'orderby'    => 'name',
					'order'      => 'ASC',
					'search'     => $keyword,
					'hide_empty' => false,
					'number'     => 100
				)
			);
			$items      = array();
			if ( count( $categories ) ) {
				foreach ( $categories as $category ) {
					$item    = array(
						'id'   => $category->term_id,
						'text' => $category->name
					);
					$items[] = $item;
				}
			}
			wp_send_json( $items );
		}

		public function admin_notices() {
			if ( $this->process_new->is_downloading() ) {
				?>
                <div class="updated">
                    <h4>
						<?php esc_html_e( 'S2W - Import Shopify to WooCommerce: Product images are being downloaded in the background.', 'import-shopify-to-woocommerce' ) ?>
                    </h4>
                    <div>
						<?php printf( __( 'Please goto <a target="_blank" href="%s">Media</a> and view downloaded images. If <strong>some images are downloaded repeatedly and no new images are downloaded</strong>, please:', 'import-shopify-to-woocommerce' ), admin_url( 'upload.php' ), add_query_arg( array( 's2w_cancel_download_image' => '1', ), $_SERVER['REQUEST_URI'] ) ) ?>
                        <ol>
                            <li><?php printf( __( '<strong>Stop importing products immediately</strong>', 'import-shopify-to-woocommerce' ) ) ?></li>
                            <li><?php printf( __( '<a class="s2w-cancel-download-images-button" href="%s">Cancel downloading</a></strong>', 'import-shopify-to-woocommerce' ), add_query_arg( array( 's2w_cancel_download_image' => '1', ), $_SERVER['REQUEST_URI'] ) ) ?></li>
                            <li><?php printf( __( 'Contact <strong>support@villatheme.com</strong> or create your ticket at <a target="_blank" href="https://villatheme.com/supports/forum/plugins/import-shopify-to-woocommerce/">https://villatheme.com/supports/forum/plugins/import-shopify-to-woocommerce/</a>', 'import-shopify-to-woocommerce' ) ) ?></li>
                        </ol>
                    </div>
                </div>
				<?php
			} elseif ( ! $this->process_new->is_queue_empty() ) {
				?>
                <div class="updated">
                    <h4>
						<?php esc_html_e( 'S2W - Import Shopify to WooCommerce: There are products images in the queue.', 'import-shopify-to-woocommerce' ) ?>
                    </h4>
                    <ol>
                        <li>
							<?php printf( __( 'If the same images are downloaded again and again, please <strong><a class="s2w-empty-queue-images-button" href="%s">Empty queue</a></strong> and go to Products to update missing images for your products.', 'import-shopify-to-woocommerce' ), add_query_arg( array( 's2w_cancel_download_image' => '1', ), $_SERVER['REQUEST_URI'] ) ) ?>
                        </li>
                        <li>
							<?php printf( __( 'If products images were downloading normally before, please <strong><a class="s2w-start-download-images-button" href="%s">Resume download</a></strong>', 'import-shopify-to-woocommerce' ), add_query_arg( array( 's2w_start_download_image' => '1', ), $_SERVER['REQUEST_URI'] ) ) ?>
                        </li>
                    </ol>
                </div>
				<?php
			} elseif ( get_transient( 's2w_background_processing_complete' ) ) {
				delete_transient( 's2w_background_processing_complete' );
				?>
                <div class="updated">
                    <p>
						<?php esc_html_e( 'S2W - Import Shopify to WooCommerce: Product images are downloaded successfully.', 'import-shopify-to-woocommerce' ) ?>
                    </p>
                </div>
				<?php
			}
		}

		public function plugins_loaded() {
			$this->process_new = new WP_IMPORT_SHOPIFY_TO_WOOCOMMERCE_Process_New();
			if ( isset( $_REQUEST['s2w_cancel_download_image'] ) && wp_unslash( sanitize_text_field( $_REQUEST['s2w_cancel_download_image'] ) ) ) {
				delete_transient( 's2w_background_processing_complete' );
				$this->process_new->kill_process();
				wp_safe_redirect( @remove_query_arg( 's2w_cancel_download_image' ) );
				exit;
			} elseif ( ! empty( $_REQUEST['s2w_start_download_image'] ) ) {
				if ( ! $this->process_new->is_queue_empty() ) {
					$this->process_new->dispatch();
				}
				wp_safe_redirect( @remove_query_arg( 's2w_start_download_image' ) );
				exit;
			}
		}

		protected function import_categories( $url, $path ) {
			$request    = wp_remote_get(
				$url . 'custom_collections/count.json', array(
					'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
					'timeout'    => $this->settings->get_params( 'request_timeout' ),
					'headers'    => array( 'Authorization' => 'Basic ' . base64_encode( $this->settings->get_params( 'api_key' ) . ':' . $this->settings->get_params( 'api_secret' ) ) ),
				)
			);
			$categories = array();
			$return     = array(
				'status' => 'error',
				'data'   => '',
			);
			if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
				$body = json_decode( $request['body'], true );
				if ( is_array( $body ) && count( $body ) ) {
					if ( isset( $body['errors'] ) ) {
						$return['data'] = $body['errors'];

						return $return;
					}
					$count       = isset( $body['count'] ) ? absint( $body['count'] ) : 0;
					$total_pages = ceil( $count / 250 );
					if ( $total_pages > 0 ) {
						for ( $i = 1; $i <= $total_pages; $i ++ ) {
							$request1 = wp_remote_get(
								$url . 'custom_collections.json?limit=250&page=' . $i, array(
									'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
									'timeout'    => $this->settings->get_params( 'request_timeout' ),
									'headers'    => array( 'Authorization' => 'Basic ' . base64_encode( $this->settings->get_params( 'api_key' ) . ':' . $this->settings->get_params( 'api_secret' ) ) ),
								)
							);
							if ( ! is_wp_error( $request1 ) || wp_remote_retrieve_response_code( $request1 ) === 200 ) {
								$body1 = json_decode( $request1['body'], true );
								if ( is_array( $body1 ) && count( $body1 ) ) {
									if ( isset( $body1['errors'] ) ) {
										if ( isset( $body1['errors'] ) ) {
											$return['data'] = $body1['errors'];

											return $return;
										}
									}
									$custom_collections = isset( $body1['custom_collections'] ) ? $body1['custom_collections'] : array();
									if ( is_array( $custom_collections ) && count( $custom_collections ) ) {
										foreach ( $custom_collections as $custom_collection ) {
											$category = array(
												'shopify_id'          => $custom_collection['id'],
												'name'                => $custom_collection['title'],
												'shopify_product_ids' => array(),
												'woo_id'              => '',
											);
											$cate     = get_term_by( 'name', $category['name'], 'product_cat' );
											if ( ! $cate ) {
												$cate = wp_insert_term( $category['name'], 'product_cat' );
												if ( ! is_wp_error( $cate ) ) {
													$category['woo_id'] = isset( $cate['term_id'] ) ? $cate['term_id'] : '';
												}
											} else {
												$category['woo_id'] = $cate->term_id;
											}
											$categories[] = $category;
										}
									}
								}
							} else {
								$return['data'] = $request1->get_error_messages();

								return $return;
							}
						}
					}
				}
			} else {
				$return['data'] = $request->get_error_messages();

				return $return;
			}
			if ( count( $categories ) ) {
				$file_path = $path . '/categories.txt';
				file_put_contents( $file_path, json_encode( $categories ) );
			}
			$return['status'] = 'success';
			$return['data']   = $categories;

			return $return;
		}

		protected static function process_category_data( $collections, &$categories ) {
			if ( is_array( $collections ) && count( $collections ) ) {
				foreach ( $collections as $collection ) {
					$category = array(
						'shopify_id'          => $collection['id'],
						'name'                => $collection['title'],
						'shopify_product_ids' => array(),
						'woo_id'              => '',
					);
					$cate     = get_term_by( 'name', $category['name'], 'product_cat' );
					if ( ! $cate ) {
						$cate = wp_insert_term( $category['name'], 'product_cat' );
						if ( ! is_wp_error( $cate ) ) {
							$category['woo_id'] = isset( $cate['term_id'] ) ? $cate['term_id'] : '';
						}
					} else {
						$category['woo_id'] = $cate->term_id;
					}
					$categories[] = $category;
				}

			}
		}

		protected function initiate_categories_data( $domain, $api_key, $api_secret, $path ) {
			$timeout    = $this->settings->get_params( 'request_timeout' );
			$categories = array();
			$return     = array(
				'status' => 'error',
				'data'   => '',
			);
			/*get custom collections*/
			$request = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::wp_remote_get(
				$domain, $api_key, $api_secret, 'custom_collections', false, array(), $timeout, true
			);
			$error   = 0;
			if ( $request['status'] === 'success' ) {
				$custom_collections = $request['data'];
				self::process_category_data( $custom_collections, $categories );
				while ( $request['pagination_link']['next'] ) {
					$request = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::wp_remote_get(
						$domain, $api_key, $api_secret, 'custom_collections', false, array( 'page_info' => $request['pagination_link']['next'] ), $timeout, true
					);
					if ( $request['status'] === 'success' ) {
						$custom_collections = $request['data'];
						self::process_category_data( $custom_collections, $categories );
					}
				}
			} else {
				$error ++;
				$return['data'] = $request['data'];
			}
			/*get smart collections*/
			$request = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::wp_remote_get(
				$domain, $api_key, $api_secret, 'smart_collections', false, array(), $timeout, true
			);
			if ( $request['status'] === 'success' ) {
				$smart_collections = $request['data'];
				self::process_category_data( $smart_collections, $categories );
				while ( $request['pagination_link']['next'] ) {
					$request = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::wp_remote_get(
						$domain, $api_key, $api_secret, 'smart_collections', false, array( 'page_info' => $request['pagination_link']['next'] ), $timeout, true
					);
					if ( $request['status'] === 'success' ) {
						$smart_collections = $request['data'];
						self::process_category_data( $smart_collections, $categories );
					}
				}
			} else {
				$error ++;
				$return['data'] = $request['data'];
			}
			if ( $error < 1 ) {
				$file_path = $path . '/categories.txt';
				file_put_contents( $file_path, json_encode( $categories ) );

				$return['status'] = 'success';
				$return['data']   = $categories;
			}

			return $return;
		}

		public function get_product_ids_by_collection( $domain, $api_key, $api_secret, $collection_id, $path ) {
			$timeout     = $this->settings->get_params( 'request_timeout' );
			$product_ids = array();
			$return      = array(
				'status' => 'error',
				'data'   => '',
			);
			$request     = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::wp_remote_get(
				$domain, $api_key, $api_secret, 'products', false, array(
				'collection_id' => $collection_id,
				'fields'        => 'id'
			), $timeout, true
			);
			if ( $request['status'] === 'success' ) {
				$products = $request['data'];
				if ( is_array( $products ) && count( $products ) ) {
					$product_ids = array_merge( array_column( $products, 'id' ), $product_ids );
				}
				while ( $request['pagination_link']['next'] ) {
					$request = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::wp_remote_get(
						$domain, $api_key, $api_secret, 'products', false, array( 'page_info' => $request['pagination_link']['next'] ), $timeout, true
					);
					if ( $request['status'] === 'success' ) {
						$products = $request['data'];
						if ( is_array( $products ) && count( $products ) ) {
							$product_ids = array_merge( array_column( $products, 'id' ), $product_ids );
						}
					}
				}
			} else {
				$return['data'] = $request['data'];

				return $return;
			}
			file_put_contents( $path . 'category_' . $collection_id . '.txt', json_encode( $product_ids ) );
			$return['status'] = 'success';
			$return['data']   = $product_ids;

			return $return;
		}

		public function save_settings() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			$_s2w_nonce = isset( $_POST['_s2w_nonce'] ) ? wp_unslash( sanitize_text_field( $_POST['_s2w_nonce'] ) ) : '';
			if ( ! wp_verify_nonce( $_s2w_nonce, 's2w_action_nonce' ) ) {
				return;
			}
			add_filter( 'http_request_timeout', array( $this, 'bump_request_timeout' ) );
			$domain                  = isset( $_POST['domain'] ) ? sanitize_text_field( $_POST['domain'] ) : '';
			$api_key                 = isset( $_POST['api_key'] ) ? sanitize_text_field( $_POST['api_key'] ) : '';
			$api_secret              = isset( $_POST['api_secret'] ) ? sanitize_text_field( $_POST['api_secret'] ) : '';
			$download_images         = isset( $_POST['download_images'] ) ? sanitize_text_field( $_POST['download_images'] ) : '';
			$product_status          = isset( $_POST['product_status'] ) ? sanitize_text_field( $_POST['product_status'] ) : 'publish';
			$product_categories      = isset( $_POST['product_categories'] ) ? array_map( 'stripslashes', $_POST['product_categories'] ) : array();
			$request_timeout         = isset( $_POST['request_timeout'] ) ? sanitize_text_field( $_POST['request_timeout'] ) : '60';
			$products_per_request    = isset( $_POST['products_per_request'] ) ? sanitize_text_field( $_POST['products_per_request'] ) : '5';
			$product_import_sequence = isset( $_POST['product_import_sequence'] ) ? sanitize_text_field( $_POST['product_import_sequence'] ) : '';

			$path                   = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_cache_path( $domain, $api_key, $api_secret ) . '/';
			$history_product_option = 's2w_' . $domain . '_history';
			$history                = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_option( $history_product_option, array(
				'total_products'         => 0,
				'total_pages'            => 0,
				'current_import_id'      => '',
				'current_import_product' => - 1,
				'current_import_page'    => 1,
				'products_per_file'      => 250,
				'last_product_error'     => '',
			) );

			$url      = 'https://' . $api_key . ':' . $api_secret . '@' . $domain . '/admin/';
			$args     = array(
				'domain'                  => $domain,
				'api_key'                 => $api_key,
				'api_secret'              => $api_secret,
				'download_images'         => $download_images,
				'product_categories'      => $product_categories,
				'product_status'          => $product_status,
				'number'                  => '5',
				'validate'                => $this->settings->get_params( 'validate' ),
				'products_per_request'    => $products_per_request,
				'request_timeout'         => $request_timeout,
				'product_import_sequence' => $product_import_sequence,
			);
			$elements = array(
				'products' => isset( $history['time'] ) && $history['time'] ? 1 : '',
			);

			$api_error      = '';
			$old_domain     = $this->settings->get_params( 'domain' );
			$old_api_key    = $this->settings->get_params( 'api_key' );
			$old_api_secret = $this->settings->get_params( 'api_secret' );
			if ( $domain && $api_key && $api_secret ) {
				if ( ! $args['validate'] || $domain != $old_domain || $api_key != $old_api_key || $api_secret != $old_api_secret ) {
					$request = wp_remote_get(
						$url . 'products/count.json', array(
							'user-agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/61.0.3163.100 Safari/537.36',
							'timeout'    => $this->settings->get_params( 'request_timeout' ),
							'headers'    => array( 'Authorization' => 'Basic ' . base64_encode( $api_key . ':' . $api_secret ) ),
						)
					);
					if ( ! is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) === 200 ) {
						$body = json_decode( $request['body'], true );

						if ( is_array( $body ) && count( $body ) ) {
							if ( isset( $body['errors'] ) ) {
								$api_error        = $body['errors'];
								$args['validate'] = '';
							} else {
								$args['validate'] = 1;
							}
						}
					} else {
						$api_error        = $request->get_error_messages();
						$args['validate'] = '';
					}
				}
			} else {
				$args['validate'] = '';
			}

			if ( $args['product_import_sequence'] != $this->settings->get_params( 'product_import_sequence' ) ) {
				VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::delete_option( 's2w_' . $domain . '_history' );
				$product_files = glob( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_cache_path( $domain, $api_key, $api_secret ) . '/page_*.txt' );
				VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::delete_files( $product_files );
				$history = array(
					'total_products'         => 0,
					'total_pages'            => 0,
					'current_import_id'      => '',
					'current_import_product' => - 1,
					'current_import_page'    => 1,
					'products_per_file'      => 250,
					'last_product_error'     => '',
				);
			}

			if ( $args['validate'] ) {
				if ( $domain === $old_domain ) {
					$old_dir = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_cache_path( $old_domain, $old_api_key, $old_api_secret );
					if ( is_dir( $old_dir ) ) {
						if ( ! @rename( $old_dir, $path ) ) {
							VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::create_cache_folder( $path );
						}
					} else {
						VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::create_cache_folder( $path );
					}
				} else {
					$dirs = glob( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CACHE . "*_$domain", GLOB_ONLYDIR );
					if ( is_array( $dirs ) && count( $dirs ) ) {
						if ( ! @rename( $dirs[0], $path ) ) {
							VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::create_cache_folder( $path );
						}
					} else {
						VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::create_cache_folder( $path );
					}
				}
			}
			VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::update_option( 's2w_params', $args );
			wp_send_json( array_merge( $history, array(
				'api_error'         => $api_error,
				'validate'          => $args['validate'],
				'imported_elements' => $elements,
			) ) );
		}

		public function save_settings_product_options() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			$_s2w_nonce = isset( $_POST['_s2w_nonce'] ) ? wp_unslash( sanitize_text_field( $_POST['_s2w_nonce'] ) ) : '';
			if ( ! wp_verify_nonce( $_s2w_nonce, 's2w_action_nonce' ) ) {
				return;
			}
			$domain                  = $this->settings->get_params( 'domain' );
			$api_key                 = $this->settings->get_params( 'api_key' );
			$api_secret              = $this->settings->get_params( 'api_secret' );
			$download_images         = isset( $_POST['download_images'] ) ? sanitize_text_field( $_POST['download_images'] ) : '';
			$product_status          = isset( $_POST['product_status'] ) ? sanitize_text_field( $_POST['product_status'] ) : 'publish';
			$product_categories      = isset( $_POST['product_categories'] ) ? array_map( 'stripslashes', $_POST['product_categories'] ) : array();
			$products_per_request    = isset( $_POST['products_per_request'] ) ? sanitize_text_field( $_POST['products_per_request'] ) : '5';
			$product_import_sequence = isset( $_POST['product_import_sequence'] ) ? sanitize_text_field( $_POST['product_import_sequence'] ) : '';
			$path                    = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_cache_path( $domain, $api_key, $api_secret ) . '/';
			$history_product_option  = 's2w_' . $domain . '_history';
			$history                 = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_option( $history_product_option, array(
				'total_products'         => 0,
				'total_pages'            => 0,
				'current_import_id'      => '',
				'current_import_product' => - 1,
				'current_import_page'    => 1,
				'products_per_file'      => 250,
				'last_product_error'     => '',
			) );

			$args = array(
				'download_images'         => $download_images,
				'product_categories'      => $product_categories,
				'product_status'          => $product_status,
				'products_per_request'    => $products_per_request,
				'product_import_sequence' => $product_import_sequence,
			);

			$product_import_options = array(
				'product_import_sequence',
			);
			foreach ( $product_import_options as $product_import_option ) {
				if ( $args[ $product_import_option ] != $this->settings->get_params( $product_import_option ) ) {
					VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::delete_option( 's2w_' . $domain . '_history' );
					$product_files = glob( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_cache_path( $domain, $api_key, $api_secret ) . '/page_*.txt' );
					VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::delete_files( $product_files );
					$history = array(
						'total_products'         => 0,
						'total_pages'            => 0,
						'current_import_id'      => '',
						'current_import_product' => - 1,
						'current_import_page'    => 1,
						'products_per_file'      => 250,
						'last_product_error'     => '',
					);
					break;
				}
			}
			VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::create_cache_folder( $path );
			VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::update_option( 's2w_params', array_merge( $this->settings->get_params(), $args ) );
			wp_send_json( array_merge( $history, $args ) );
		}

		protected function initiate_products_data( $history_product_option, $domain, $api_key, $api_secret ) {
			$history = array(
				'total_products'         => 0,
				'total_pages'            => 0,
				'current_import_id'      => '',
				'current_import_product' => - 1,
				'current_import_page'    => 1,
				'products_per_file'      => 250,
				'last_product_error'     => '',
			);
			$this->add_filters_args( $import_args );
			$request = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::wp_remote_get(
				$domain, $api_key, $api_secret, 'products', true, $import_args, $this->settings->get_params( 'request_timeout' )
			);
			$return  = array(
				'status' => 'error',
				'data'   => '',
			);
			if ( $request['status'] === 'success' ) {
				$count                     = $request['data'];
				$history['total_products'] = $count;
				$total_pages               = ceil( $count / $history['products_per_file'] );
				$history['total_pages']    = $total_pages;
				VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::update_option( $history_product_option, $history );
				if ( 0 == $total_pages ) {
					$return['data'] = esc_html__( 'No data to import', 's2w-import-shopify-to-woocommerce' );
				} else {
					$return['status'] = 'success';
					$return['data']   = $history;
				}
			} else {
				$return['data'] = $request['data'];
			}

			return $return;
		}

		public function sync() {
			if ( ! current_user_can( 'manage_options' ) ) {
				die;
			}
			$_s2w_nonce = isset( $_POST['_s2w_nonce'] ) ? wp_unslash( sanitize_text_field( $_POST['_s2w_nonce'] ) ) : '';
			if ( ! wp_verify_nonce( $_s2w_nonce, 's2w_action_nonce' ) ) {
				die;
			}
			ignore_user_abort( true );
			add_filter( 'http_request_timeout', array( $this, 'bump_request_timeout' ) );
			$domain             = $this->settings->get_params( 'domain' );
			$api_key            = $this->settings->get_params( 'api_key' );
			$api_secret         = $this->settings->get_params( 'api_secret' );
			$download_images    = $this->settings->get_params( 'download_images' );
			$product_status     = $this->settings->get_params( 'product_status' );
			$product_categories = $this->settings->get_params( 'product_categories' );
			$path               = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_cache_path( $domain, $api_key, $api_secret ) . '/';
			VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::create_cache_folder( $path );
			$step      = isset( $_POST['step'] ) ? sanitize_text_field( $_POST['step'] ) : '';
			$error_log = isset( $_POST['error_log'] ) ? wp_kses_post( $_POST['error_log'] ) : '';
			$logs      = '';
			$log_file  = $path . 'logs.txt';
			if ( $error_log ) {
				VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::log( $log_file, $error_log );
			}
			$history_option         = 's2w_' . $domain . '_history_' . $step;
			$history_product_option = 's2w_' . $domain . '_history';
			$history                = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::get_option( $history_product_option, array() );/*array(
				'total_products'         => 0,
				'total_pages'            => 0,
				'current_import_id'      => '',
				'current_import_product' => - 1,
				'current_import_page'    => 1,
				'products_per_file'      => 250,
				'last_product_error'     => '',
			)*/
			if ( $domain && $api_key && $api_secret ) {
				switch ( $step ) {
					case 'products':
						$manage_stock           = ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) ? true : false;
						$placeholder_image_id   = s2w_get_placeholder_image();
						$current_import_id      = isset( $_POST['current_import_id'] ) ? sanitize_text_field( $_POST['current_import_id'] ) : '';
						$current_import_product = isset( $_POST['current_import_product'] ) ? intval( sanitize_text_field( $_POST['current_import_product'] ) ) : - 1;
						$current_import_page    = isset( $_POST['current_import_page'] ) ? absint( sanitize_text_field( $_POST['current_import_page'] ) ) : 1;
						$total_pages            = isset( $_POST['total_pages'] ) ? absint( sanitize_text_field( $_POST['total_pages'] ) ) : 1;
						if ( ! $history ) {
							$history_data = $this->initiate_products_data( $history_product_option, $domain, $api_key, $api_secret );
							if ( $history_data['status'] == 'success' ) {
								$history = $history_data['data'];

								wp_send_json( array_merge( $history, array(
										'status' => 'retry'
									)
								) );
							} else {
								wp_send_json( array(
										'status'  => 'error',
										'message' => $history_data['data'],
									)
								);
							}
						} elseif ( ! empty( $history['time'] ) ) {
							VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::delete_option( $history_product_option );
							$files = glob( $path . '/page_*.txt' );
							VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::delete_files( $files );
							$history_data = $this->initiate_products_data( $history_product_option, $domain, $api_key, $api_secret );
							if ( $history_data['status'] == 'success' ) {
								$history = $history_data['data'];

								wp_send_json( array_merge( $history, array(
										'status' => 'retry'
									)
								) );
							} else {
								wp_send_json( array(
										'status'  => 'error',
										'message' => $history_data['data'],
									)
								);
							}
						}
						$products_per_request = $this->settings->get_params( 'products_per_request' );
						$products_per_file    = isset( $history['products_per_file'] ) ? $history['products_per_file'] : 250;
						if ( $total_pages >= $current_import_page ) {
							$file_path     = $path . 'page_' . $current_import_page . '.txt';
							$products      = array();
							$page_info_num = empty( $history['page_info_num'] ) ? 1 : intval( $history['page_info_num'] );
							if ( ! is_file( $file_path ) || $page_info_num < $current_import_page + 1 ) {
								$import_args = array();
								if ( ! empty( $history['page_info'] ) && ! empty( $history['page_info_num'] ) ) {
									$import_args['page_info'] = $history['page_info'];
								} else {
									$this->add_filters_args( $import_args );
								}
								$import_args['limit'] = $products_per_file;
								$request              = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::wp_remote_get(
									$domain, $api_key, $api_secret, $step, false, $import_args, $this->settings->get_params( 'request_timeout' ), true
								);

								if ( $request['status'] === 'success' ) {
									$products = $request['data'];
									if ( is_array( $products ) && count( $products ) ) {
										file_put_contents( $file_path, json_encode( $products ) );
									}
									if ( $request['pagination_link']['next'] ) {
										$page_info_num ++;
										$history['page_info']     = $request['pagination_link']['next'];
										$history['page_info_num'] = $page_info_num;
										if ( $page_info_num < $current_import_page + 1 ) {
											VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::update_option( $history_product_option, $history );
											wp_send_json( array_merge( $history, array(
													'status' => 'retry'
												)
											) );
										}
									}
								} else {
									wp_send_json( array(
										'status'  => 'error',
										'message' => $request['data'],
									) );
								}
							} else {
								$products = json_decode( file_get_contents( $file_path ), true );
							}
							if ( is_array( $products ) && count( $products ) ) {
								$current = $current_import_product;
								$max     = ( $current + $products_per_request + 1 ) < count( $products ) ? ( $current + $products_per_request + 1 ) : count( $products );
								wp_suspend_cache_invalidation( true );
								for ( $key = $current + 1; $key < $max; $key ++ ) {
									vi_s2w_set_time_limit();
									$product = isset( $products[ $key ] ) ? $products[ $key ] : array();
									if ( is_array( $product ) && count( $product ) ) {
										$current_import_id   = $product['id'];
										$log                 = array(
											'shopify_id'  => $current_import_id,
											'woo_id'      => '',
											'title'       => $product['title'],
											'message'     => 'Import successfully',
											'product_url' => '',
										);
										$variations          = isset( $product['variants'] ) ? $product['variants'] : array();
										$sku                 = $current_import_id . '-' . $product['handle'];
										$attr_data           = array();
										$options             = isset( $product['options'] ) ? $product['options'] : array();
										$imported_product_id = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::product_get_woo_id_by_shopify_id( $current_import_id );
										if ( ! $imported_product_id ) {
											if ( ! VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::sku_exists( $sku ) ) {
												if ( $download_images ) {
													$history['last_product_error'] = 1;
													if ( is_array( $options ) && count( $options ) ) {
														if ( count( $options ) == 1 && count( $options[0]['values'] ) == 1 ) {
															$regular_price = $variations[0]['compare_at_price'];
															$sale_price    = $variations[0]['price'];
															if ( ! floatval( $regular_price ) || floatval( $regular_price ) == floatval( $sale_price ) ) {
																$regular_price = $sale_price;
																$sale_price    = '';
															}
															$data = array( // Set up the basic post data to insert for our product
																'post_type'    => 'product',
																'post_excerpt' => '',
																'post_content' => isset( $product['body_html'] ) ? $product['body_html'] : '',
																'post_title'   => isset( $product['title'] ) ? $product['title'] : '',
																'post_status'  => $product_status,
																'post_parent'  => '',

																'meta_input' => array(
																	'_sku'                => VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::sku_exists( $variations[0]['sku'] ) ? '' : $variations[0]['sku'],
																	'_visibility'         => 'visible',
																	'_shopify_product_id' => $current_import_id,
																	'_regular_price'      => $regular_price,
																	'_price'              => $regular_price,
																)
															);
//															if ( $manage_stock ) {
//																$data['meta_input']['_manage_stock'] = 'yes';
//																if ( $variations[0]['inventory_quantity'] ) {
//																	$data['meta_input']['_stock']        = $variations[0]['inventory_quantity'];
//																	$data['meta_input']['_stock_status'] = 'instock';
//																} else {
//																	$data['meta_input']['_stock_status'] = 'outofstock';
//																}
//															} else {
//																$data['meta_input']['_manage_stock'] = 'no';
//																$data['meta_input']['_stock_status'] = 'instock';
//															}
															if ( $variations[0]['weight'] ) {
																$data['meta_input']['_weight'] = $variations[0]['weight'];
															}

															if ( $sale_price ) {
																$data['meta_input']['_sale_price'] = $sale_price;
																$data['meta_input']['_price']      = $sale_price;
															}
															$product_id = wp_insert_post( $data );
															if ( ! is_wp_error( $product_id ) ) {
																$log['woo_id'] = $product_id;
																$images_d      = array();
																$images        = isset( $product['images'] ) ? $product['images'] : array();
																if ( count( $images ) ) {
																	foreach ( $images as $image ) {
																		$images_d[] = array(
																			'id'          => $image['id'],
																			'src'         => $image['src'],
																			'alt'         => $image['alt'],
																			'parent_id'   => $product_id,
																			'product_ids' => array(),
																			'set_gallery' => 1,
																		);
																	}
																	$images_d[0]['product_ids'][] = $product_id;
																	$images_d[0]['set_gallery']   = 0;
																	if ( $placeholder_image_id ) {
																		update_post_meta( $product_id, '_thumbnail_id', $placeholder_image_id );
																	}
																}
																wp_set_object_terms( $product_id, 'simple', 'product_type' );
//																if ( ! empty( $product['product_type'] ) ) {
//																	wp_set_object_terms( $product_id, $product['product_type'], 'product_cat', true );
//																}
																if ( is_array( $product_categories ) && count( $product_categories ) ) {
																	wp_set_post_terms( $product_id, $product_categories, 'product_cat', true );
																}

																$tags = isset( $product['tags'] ) ? $product['tags'] : '';
																if ( $tags ) {
																	wp_set_object_terms( $product_id, explode( ',', $product['tags'] ), 'product_tag' );
																}
																if ( count( $images_d ) ) {
																	foreach ( $images_d as $images_d_k => $images_d_v ) {
																		$this->process_new->push_to_queue( $images_d_v );
																	}
																	$this->process_new->save()->dispatch();
																}
																$history['last_product_error'] = '';
																$product_obj                   = wc_get_product( $product_id );
																if ( $product_obj ) {
																	if ( $manage_stock ) {
																		$product_obj->set_manage_stock( 'yes' );
																		$product_obj->set_stock_quantity( $variations[0]['inventory_quantity'] );
																		if ( $variations[0]['inventory_quantity'] ) {
																			$product_obj->set_stock_status( 'instock' );
																		} else {
																			$product_obj->set_stock_status( 'outofstock' );
																		}
																	} else {
																		$product_obj->set_manage_stock( 'no' );
																		$product_obj->set_stock_status( 'instock' );
																	}
																	$product_obj->save();
																}
															}
														} else {
															foreach ( $options as $option_k => $option_v ) {
																$attribute_object = new WC_Product_Attribute();
																$attribute_object->set_name( $option_v['name'] );
																$attribute_object->set_options( $option_v['values'] );
																$attribute_object->set_position( $option_v['position'] );
																$attribute_object->set_visible( apply_filters( 's2w_create_product_attribute_set_visible', 0, $option_v ) );
																$attribute_object->set_variation( 1 );
																$attr_data[] = $attribute_object;
															}
															$data       = array( // Set up the basic post data to insert for our product
																'post_type'    => 'product',
																'post_excerpt' => '',
																'post_content' => isset( $product['body_html'] ) ? $product['body_html'] : '',
																'post_title'   => isset( $product['title'] ) ? $product['title'] : '',
																'post_status'  => $product_status,
																'post_parent'  => '',
																'meta_input'   => array(
																	'_sku'                => $sku,
																	'_visibility'         => 'visible',
																	'_shopify_product_id' => $current_import_id,
																	'_manage_stock'       => 'no',
																)
															);
															$product_id = wp_insert_post( $data );
															if ( ! is_wp_error( $product_id ) ) {
																wp_set_object_terms( $product_id, 'variable', 'product_type' );
																if ( count( $attr_data ) ) {
																	$product_obj = wc_get_product( $product_id );
																	if ( $product_obj ) {
																		$product_obj->set_attributes( $attr_data );
																		$product_obj->save();
																		wp_set_object_terms( $product_id, 'variable', 'product_type' );
																	}
																}
																$log['woo_id'] = $product_id;
																$images_d      = array();
																$images        = isset( $product['images'] ) ? $product['images'] : array();
																if ( count( $images ) ) {
																	foreach ( $images as $image ) {
																		$images_d[] = array(
																			'id'          => $image['id'],
																			'src'         => $image['src'],
																			'alt'         => $image['alt'],
																			'parent_id'   => $product_id,
																			'product_ids' => array(),
																			'set_gallery' => 1,
																		);
																	}
																	$images_d[0]['product_ids'][] = $product_id;
																	$images_d[0]['set_gallery']   = 0;
																	if ( $placeholder_image_id ) {
																		update_post_meta( $product_id, '_thumbnail_id', $placeholder_image_id );
																	}
																}
//																if ( ! empty( $product['product_type'] ) ) {
//																	wp_set_object_terms( $product_id, $product['product_type'], 'product_cat', true );
//																}
																if ( is_array( $product_categories ) && count( $product_categories ) ) {
																	wp_set_post_terms( $product_id, $product_categories, 'product_cat', true );
																}
																$tags = isset( $product['tags'] ) ? $product['tags'] : '';
																if ( $tags ) {
																	wp_set_object_terms( $product_id, explode( ',', $product['tags'] ), 'product_tag' );
																}
																if ( is_array( $variations ) && count( $variations ) ) {
																	foreach ( $variations as $variation ) {
																		vi_s2w_set_time_limit();
																		$regular_price = $variation['compare_at_price'];
																		$sale_price    = $variation['price'];
																		if ( ! floatval( $regular_price ) || floatval( $regular_price ) == floatval( $sale_price ) ) {
																			$regular_price = $sale_price;
																			$sale_price    = '';
																		}
																		$variation_obj = new WC_Product_Variation();
																		$variation_obj->set_parent_id( $product_id );
																		$attributes = array();
																		foreach ( $options as $option_k => $option_v ) {
																			$j = $option_k + 1;
																			if ( isset( $variation[ 'option' . $j ] ) && $variation[ 'option' . $j ] ) {
																				$attributes[ VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::sanitize_taxonomy_name( $option_v['name'] ) ] = $variation[ 'option' . $j ];
																			}
																		}
																		$variation_obj->set_attributes( $attributes );
																		$fields = array(
																			'sku'           => VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::sku_exists( $variation['sku'] ) ? '' : $variation['sku'],
																			'regular_price' => $regular_price,
																		);
//																		if ( $manage_stock ) {
//																			$fields['stock_quantity'] = $variation['inventory_quantity'];
//																			$fields['manage_stock']   = 'yes';
//																			if ( $variation['inventory_quantity'] ) {
//																				$fields['stock_status'] = 'instock';
//																			} else {
//																				$fields['stock_status'] = 'outofstock';
//																			}
//																		} else {
//																			$fields['manage_stock'] = 'no';
//																			$fields['stock_status'] = 'instock';
//																		}
																		if ( $manage_stock ) {
																			$variation_obj->set_manage_stock( 'yes' );
																			$variation_obj->set_stock_quantity( $variation['inventory_quantity'] );
																			if ( $variation['inventory_quantity'] ) {
																				$variation_obj->set_stock_status( 'instock' );
																			} else {
																				$variation_obj->set_stock_status( 'outofstock' );
																			}
																		} else {
																			$variation_obj->set_manage_stock( 'no' );
																			$variation_obj->set_stock_status( 'instock' );
																		}
																		if ( $variation['weight'] ) {
																			$fields['weight'] = $variation['weight'];
																		}
																		if ( $sale_price ) {
																			$fields['sale_price'] = $sale_price;
																		}
																		foreach ( $fields as $field => $field_v ) {
																			$variation_obj->{"set_$field"}( wc_clean( $field_v ) );
																		}
																		do_action( 'product_variation_linked', $variation_obj->save() );
																		$variation_obj_id = $variation_obj->get_id();
																		if ( count( $images ) ) {
																			foreach ( $images as $image_k => $image_v ) {
																				if ( in_array( $variation['id'], $image_v['variant_ids'] ) ) {
																					$images_d[ $image_k ]['product_ids'][] = $variation_obj_id;
																					if ( $placeholder_image_id ) {
																						update_post_meta( $variation_obj_id, '_thumbnail_id', $placeholder_image_id );
																					}
																				}
																			}
																		}
																		update_post_meta( $variation_obj_id, '_shopify_variation_id', $variation['id'] );
																	}
																}
																if ( count( $images_d ) ) {
																	foreach ( $images_d as $images_d_k => $images_d_v ) {
																		$this->process_new->push_to_queue( $images_d_v );
																	}
																	$this->process_new->save()->dispatch();
																}
																$history['last_product_error'] = '';
															}
														}
													}
												} else {
													if ( is_array( $options ) && count( $options ) ) {
														if ( count( $options ) == 1 && count( $options[0]['values'] ) == 1 ) {
															$regular_price = $variations[0]['compare_at_price'];
															$sale_price    = $variations[0]['price'];
															if ( ! floatval( $regular_price ) || floatval( $regular_price ) == floatval( $sale_price ) ) {
																$regular_price = $sale_price;
																$sale_price    = '';
															}
															$data = array( // Set up the basic post data to insert for our product
																'post_type'    => 'product',
																'post_excerpt' => '',
																'post_content' => isset( $product['body_html'] ) ? $product['body_html'] : '',
																'post_title'   => isset( $product['title'] ) ? $product['title'] : '',
																'post_status'  => $product_status,
																'post_parent'  => '',

																'meta_input' => array(
																	'_sku'                => VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::sku_exists( $variations[0]['sku'] ) ? '' : $variations[0]['sku'],
																	'_visibility'         => 'visible',
																	'_shopify_product_id' => $current_import_id,
																	'_regular_price'      => $regular_price,
																	'_price'              => $regular_price,
																)
															);
//															if ( $manage_stock ) {
//																$data['meta_input']['_manage_stock'] = 'yes';
//																if ( $variations[0]['inventory_quantity'] ) {
//																	$data['meta_input']['_stock']        = $variations[0]['inventory_quantity'];
//																	$data['meta_input']['_stock_status'] = 'instock';
//																} else {
//																	$data['meta_input']['_stock_status'] = 'outofstock';
//																}
//															} else {
//																$data['meta_input']['_manage_stock'] = 'no';
//																$data['meta_input']['_stock_status'] = 'instock';
//															}
															if ( $variations[0]['weight'] ) {
																$data['meta_input']['_weight'] = $variations[0]['weight'];
															}

															if ( $sale_price ) {
																$data['meta_input']['_sale_price'] = $sale_price;
																$data['meta_input']['_price']      = $sale_price;
															}
															$product_id = wp_insert_post( $data );
															if ( ! is_wp_error( $product_id ) ) {
																$log['woo_id'] = $product_id;
																wp_set_object_terms( $product_id, 'simple', 'product_type' );
//																if ( ! empty( $product['product_type'] ) ) {
//																	wp_set_object_terms( $product_id, $product['product_type'], 'product_cat', true );
//																}
																if ( is_array( $product_categories ) && count( $product_categories ) ) {
																	wp_set_post_terms( $product_id, $product_categories, 'product_cat', true );
																}
																$tags = isset( $product['tags'] ) ? $product['tags'] : '';
																if ( $tags ) {
																	wp_set_object_terms( $product_id, explode( ',', $product['tags'] ), 'product_tag' );
																}
																$product_obj = wc_get_product( $product_id );
																if ( $product_obj ) {
																	if ( $manage_stock ) {
																		$product_obj->set_manage_stock( 'yes' );
																		$product_obj->set_stock_quantity( $variations[0]['inventory_quantity'] );
																		if ( $variations[0]['inventory_quantity'] ) {
																			$product_obj->set_stock_status( 'instock' );
																		} else {
																			$product_obj->set_stock_status( 'outofstock' );
																		}
																	} else {
																		$product_obj->set_manage_stock( 'no' );
																		$product_obj->set_stock_status( 'instock' );
																	}
																	$product_obj->save();
																}
															}
														} else {
															foreach ( $options as $option_k => $option_v ) {
																$attribute_object = new WC_Product_Attribute();
																$attribute_object->set_name( $option_v['name'] );
																$attribute_object->set_options( $option_v['values'] );
																$attribute_object->set_position( $option_v['position'] );
																$attribute_object->set_visible( apply_filters( 's2w_create_product_attribute_set_visible', 0, $option_v ) );
																$attribute_object->set_variation( 1 );
																$attr_data[] = $attribute_object;
															}
															$data       = array( // Set up the basic post data to insert for our product
																'post_type'    => 'product',
																'post_excerpt' => '',
																'post_content' => isset( $product['body_html'] ) ? $product['body_html'] : '',
																'post_title'   => isset( $product['title'] ) ? $product['title'] : '',
																'post_status'  => $product_status,
																'post_parent'  => '',

																'meta_input' => array(
																	'_sku'                => $sku,
																	'_visibility'         => 'visible',
																	'_shopify_product_id' => $current_import_id,
																	'_manage_stock'       => 'no',
																)
															);
															$product_id = wp_insert_post( $data );
															if ( ! is_wp_error( $product_id ) ) {
																wp_set_object_terms( $product_id, 'variable', 'product_type' );
																if ( count( $attr_data ) ) {
																	$product_obj = wc_get_product( $product_id );
																	if ( $product_obj ) {
																		$product_obj->set_attributes( $attr_data );
																		$product_obj->save();
																		wp_set_object_terms( $product_id, 'variable', 'product_type' );
																	}
																}
																$log['woo_id'] = $product_id;
//																if ( ! empty( $product['product_type'] ) ) {
//																	wp_set_object_terms( $product_id, $product['product_type'], 'product_cat', true );
//																}
																if ( is_array( $product_categories ) && count( $product_categories ) ) {
																	wp_set_post_terms( $product_id, $product_categories, 'product_cat', true );
																}
																$tags = isset( $product['tags'] ) ? $product['tags'] : '';
																if ( $tags ) {
																	wp_set_object_terms( $product_id, explode( ',', $product['tags'] ), 'product_tag' );
																}
																if ( is_array( $variations ) && count( $variations ) ) {
																	foreach ( $variations as $variation ) {
																		vi_s2w_set_time_limit();
																		$regular_price = $variation['compare_at_price'];
																		$sale_price    = $variation['price'];
																		if ( ! floatval( $regular_price ) || floatval( $regular_price ) == floatval( $sale_price ) ) {
																			$regular_price = $sale_price;
																			$sale_price    = '';
																		}
																		$variation_obj = new WC_Product_Variation();
																		$variation_obj->set_parent_id( $product_id );
																		$attributes = array();
																		foreach ( $options as $option_k => $option_v ) {
																			$j = $option_k + 1;
																			if ( isset( $variation[ 'option' . $j ] ) && $variation[ 'option' . $j ] ) {
																				$attributes[ VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::sanitize_taxonomy_name( $option_v['name'] ) ] = $variation[ 'option' . $j ];
																			}
																		}
																		$variation_obj->set_attributes( $attributes );
																		$fields = array(
																			'sku'           => VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::sku_exists( $variation['sku'] ) ? '' : $variation['sku'],
																			'regular_price' => $regular_price,
																		);
//																		if ( $manage_stock ) {
//																			$fields['stock_quantity'] = $variation['inventory_quantity'];
//																			$fields['manage_stock']   = 'yes';
//																			if ( $variation['inventory_quantity'] ) {
//																				$fields['stock_status'] = 'instock';
//																			} else {
//																				$fields['stock_status'] = 'outofstock';
//																			}
//																		} else {
//																			$fields['manage_stock'] = 'no';
//																			$fields['stock_status'] = 'instock';
//																		}
																		if ( $manage_stock ) {
																			$variation_obj->set_manage_stock( 'yes' );
																			$variation_obj->set_stock_quantity( $variation['inventory_quantity'] );
																			if ( $variation['inventory_quantity'] ) {
																				$variation_obj->set_stock_status( 'instock' );
																			} else {
																				$variation_obj->set_stock_status( 'outofstock' );
																			}
																		} else {
																			$variation_obj->set_manage_stock( 'no' );
																			$variation_obj->set_stock_status( 'instock' );
																		}
																		if ( $variation['weight'] ) {
																			$fields['weight'] = $variation['weight'];
																		}
																		if ( $sale_price ) {
																			$fields['sale_price'] = $sale_price;
																		}
																		foreach ( $fields as $field => $field_v ) {
																			$variation_obj->{"set_$field"}( wc_clean( $field_v ) );
																		}
																		do_action( 'product_variation_linked', $variation_obj->save() );
																		update_post_meta( $variation_obj->get_id(), '_shopify_variation_id', $variation['id'] );
																	}
																}
															}
														}
													}
												}
											} elseif ( $error_log || $history['last_product_error'] ) {
												$product_id = wc_get_product_id_by_sku( $sku );

												if ( $product_id && is_array( $options ) && count( $options ) ) {
													update_post_meta( $product_id, '_shopify_product_id', $current_import_id );
													$log['woo_id'] = $product_id;
													wp_set_object_terms( $product_id, 'variable', 'product_type' );
//													if ( ! empty( $product['product_type'] ) ) {
//														wp_set_object_terms( $product_id, $product['product_type'], 'product_cat', true );
//													}
													if ( is_array( $product_categories ) && count( $product_categories ) ) {
														wp_set_post_terms( $product_id, $product_categories, 'product_cat', true );
													}
													$tags = isset( $product['tags'] ) ? $product['tags'] : '';
													if ( $tags ) {
														wp_set_object_terms( $product_id, explode( ',', $product['tags'] ), 'product_tag' );
													}
													if ( is_array( $variations ) && count( $variations ) ) {
														if ( $download_images ) {
															$images_d = array();
															$images   = isset( $product['images'] ) ? $product['images'] : array();
															if ( count( $images ) ) {
																foreach ( $images as $image ) {
																	$images_d[] = array(
																		'id'          => $image['id'],
																		'src'         => $image['src'],
																		'alt'         => $image['alt'],
																		'parent_id'   => $product_id,
																		'product_ids' => array(),
																		'set_gallery' => 1,
																	);
																}
																$images_d[0]['product_ids'][] = $product_id;
																$images_d[0]['set_gallery']   = 0;
															}
															wp_set_object_terms( $product_id, 'variable', 'product_type' );
//															if ( ! empty( $product['product_type'] ) ) {
//																wp_set_object_terms( $product_id, $product['product_type'], 'product_cat', true );
//															}
															if ( is_array( $product_categories ) && count( $product_categories ) ) {
																wp_set_post_terms( $product_id, $product_categories, 'product_cat', true );
															}
															$tags = isset( $product['tags'] ) ? $product['tags'] : '';
															if ( $tags ) {
																wp_set_object_terms( $product_id, explode( ',', $product['tags'] ), 'product_tag' );
															}
															foreach ( $variations as $variation ) {
																vi_s2w_set_time_limit();
																if ( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::sku_exists( $variation['sku'] ) ) {
																	$variation_id = wc_get_product_id_by_sku( $variation['sku'] );
																	if ( $variation['id'] == get_post_meta( $variation_id, '_shopify_variation_id', true ) ) {
																		continue;
																	}

																}
																$regular_price = $variation['compare_at_price'];
																$sale_price    = $variation['price'];
																if ( ! floatval( $regular_price ) || floatval( $regular_price ) == floatval( $sale_price ) ) {
																	$regular_price = $sale_price;
																	$sale_price    = '';
																}
																$variation_obj = new WC_Product_Variation();
																$variation_obj->set_parent_id( $product_id );
																$attributes = array();
																foreach ( $options as $option_k => $option_v ) {
																	$j = $option_k + 1;
																	if ( isset( $variation[ 'option' . $j ] ) && $variation[ 'option' . $j ] ) {
																		$attributes[ wc_sanitize_taxonomy_name( $option_v['name'] ) ] = $variation[ 'option' . $j ];
																	}
																}
																$variation_obj->set_attributes( $attributes );
																$fields = array(
																	'sku'           => VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::sku_exists( $variation['sku'] ) ? '' : $variation['sku'],
																	'regular_price' => $regular_price,
																);
//																if ( $manage_stock ) {
//																	$fields['stock_quantity'] = $variation['inventory_quantity'];
//																	$fields['manage_stock']   = 'yes';
//																	if ( $variation['inventory_quantity'] ) {
//																		$fields['stock_status'] = 'instock';
//																	} else {
//																		$fields['stock_status'] = 'outofstock';
//																	}
//																} else {
//																	$fields['manage_stock'] = 'no';
//																	$fields['stock_status'] = 'instock';
//																}
																if ( $manage_stock ) {
																	$variation_obj->set_manage_stock( 'yes' );
																	$variation_obj->set_stock_quantity( $variation['inventory_quantity'] );
																	if ( $variation['inventory_quantity'] ) {
																		$variation_obj->set_stock_status( 'instock' );
																	} else {
																		$variation_obj->set_stock_status( 'outofstock' );
																	}
																} else {
																	$variation_obj->set_manage_stock( 'no' );
																	$variation_obj->set_stock_status( 'instock' );
																}
																if ( $variation['weight'] ) {
																	$fields['weight'] = $variation['weight'];
																}
																if ( $sale_price ) {
																	$fields['sale_price'] = $sale_price;
																}
																foreach ( $fields as $field => $field_v ) {
																	$variation_obj->{"set_$field"}( wc_clean( $field_v ) );
																}
																do_action( 'product_variation_linked', $variation_obj->save() );
																$variation_obj_id = $variation_obj->get_id();
																if ( count( $images ) ) {
																	foreach ( $images as $image_k => $image_v ) {
																		if ( in_array( $variation['id'], $image_v['variant_ids'] ) ) {
																			$images_d[ $image_k ]['product_ids'][] = $variation_obj_id;
																			if ( $placeholder_image_id ) {
																				update_post_meta( $variation_obj_id, '_thumbnail_id', $placeholder_image_id );
																			}
																		}
																	}
																}
																update_post_meta( $variation_obj_id, '_shopify_variation_id', $variation['id'] );
															}

															if ( count( $images_d ) ) {
																foreach ( $images_d as $images_d_k => $images_d_v ) {
																	$this->process_new->push_to_queue( $images_d_v );
																}
																$this->process_new->save()->dispatch();
															}
															$history['last_product_error'] = '';
														} else {
															if ( is_array( $variations ) && count( $variations ) ) {
																foreach ( $variations as $variation ) {
																	vi_s2w_set_time_limit();
																	if ( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::sku_exists( $variation['sku'] ) ) {
																		$variation_id = wc_get_product_id_by_sku( $variation['sku'] );
																		if ( $variation['id'] == get_post_meta( $variation_id, '_shopify_variation_id', true ) ) {
																			continue;
																		}
																	}
																	$regular_price = $variation['compare_at_price'];
																	$sale_price    = $variation['price'];
																	if ( ! floatval( $regular_price ) || floatval( $regular_price ) == floatval( $sale_price ) ) {
																		$regular_price = $sale_price;
																		$sale_price    = '';
																	}
																	$variation_obj = new WC_Product_Variation();
																	$variation_obj->set_parent_id( $product_id );
																	$attributes = array();
																	foreach ( $options as $option_k => $option_v ) {
																		$j = $option_k + 1;
																		if ( isset( $variation[ 'option' . $j ] ) && $variation[ 'option' . $j ] ) {
																			$attributes[ wc_sanitize_taxonomy_name( $option_v['name'] ) ] = $variation[ 'option' . $j ];
																		}
																	}
																	$variation_obj->set_attributes( $attributes );
																	$fields = array(
																		'sku'           => VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::sku_exists( $variation['sku'] ) ? '' : $variation['sku'],
																		'regular_price' => $regular_price,
																	);
//																	if ( $manage_stock ) {
//																		$fields['stock_quantity'] = $variation['inventory_quantity'];
//																		$fields['manage_stock']   = 'yes';
//																		if ( $variation['inventory_quantity'] ) {
//																			$fields['stock_status'] = 'instock';
//																		} else {
//																			$fields['stock_status'] = 'outofstock';
//																		}
//																	} else {
//																		$fields['manage_stock'] = 'no';
//																		$fields['stock_status'] = 'instock';
//																	}
																	if ( $manage_stock ) {
																		$variation_obj->set_manage_stock( 'yes' );
																		$variation_obj->set_stock_quantity( $variation['inventory_quantity'] );
																		if ( $variation['inventory_quantity'] ) {
																			$variation_obj->set_stock_status( 'instock' );
																		} else {
																			$variation_obj->set_stock_status( 'outofstock' );
																		}
																	} else {
																		$variation_obj->set_manage_stock( 'no' );
																		$variation_obj->set_stock_status( 'instock' );
																	}
																	if ( $variation['weight'] ) {
																		$fields['weight'] = $variation['weight'];
																	}

																	if ( $sale_price ) {
																		$fields['sale_price'] = $sale_price;
																	}
																	foreach ( $fields as $field => $field_v ) {
																		$variation_obj->{"set_$field"}( wc_clean( $field_v ) );
																	}
																	do_action( 'product_variation_linked', $variation_obj->save() );
																	update_post_meta( $variation_obj->get_id(), '_shopify_variation_id', $variation['id'] );
																}
															}
															$history['last_product_error'] = '';
														}
													}
												}
											} else {
												$log['woo_id']  = wc_get_product_id_by_sku( $sku );
												$log['message'] = 'Product SKU exists';
											}
											$log['product_url'] = admin_url( 'post.php?post=' . $log['woo_id'] . '&action=edit' );
											$logs_content       = $log['title'] . ": " . $log['message'] . ", Shopify product ID: " . $log['shopify_id'] . ", WC product ID: " . $log['woo_id'];
											VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::log( $log_file, $logs_content );
											$logs .= '<div>' . $log['title'] . ': <strong>' . $log['message'] . '.</strong>' . ( $log['product_url'] ? '<a href="' . $log['product_url'] . '" target="_blank" rel="nofollow">View & edit</a>' : '' ) . '</div>';
										} else {
											$log['woo_id']      = $imported_product_id;
											$log['message']     = esc_html__( 'Skip because product exists', 's2w-import-shopify-to-woocommerce' );
											$log['title']       = get_the_title( $imported_product_id );
											$log['product_url'] = admin_url( 'post.php?post=' . $log['woo_id'] . '&action=edit' );
											$logs_content       = $log['title'] . ": " . $log['message'] . ", Shopify product ID: " . $log['shopify_id'] . ", WC product ID: " . $log['woo_id'];
											VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::log( $log_file, $logs_content );
											$logs .= '<div>' . $log['title'] . ': <strong>' . $log['message'] . '.</strong>' . ( $log['product_url'] ? '<a href="' . $log['product_url'] . '" target="_blank" rel="nofollow">View & edit</a>' : '' ) . '</div>';
										}
									}
									$current_import_product            = $key;
									$history['current_import_id']      = $current_import_id;
									$history['current_import_product'] = $current_import_product;
									$history['current_import_page']    = $current_import_page;
									VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::update_option( $history_product_option, $history );
								}
								wp_suspend_cache_invalidation( false );
								$imported_products = ( $current_import_page - 1 ) * $products_per_file + $current_import_product + 1;
								if ( $current_import_product == count( $products ) - 1 ) {
									if ( $current_import_page == $total_pages ) {
										$history['time'] = current_time( 'timestamp' );
										VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::update_option( $history_product_option, $history );
										wp_send_json( array(
											'status'                 => 'finish',
											'message'                => sprintf( esc_html__( 'Completed %s/%s', 's2w-import-shopify-to-woocommerce' ), $history['total_products'], $history['total_products'] ),
											'imported_products'      => $imported_products,
											'current_import_id'      => $current_import_id,
											'current_import_page'    => $current_import_page,
											'current_import_product' => $current_import_product,
											'logs'                   => $logs,
										) );
									} else {
										$current_import_product = - 1;
										$current_import_page ++;
										$history['current_import_page'] = $current_import_page;
										VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::update_option( $history_product_option, $history );
										wp_send_json( array(
											'status'                 => 'successful',
											'message'                => sprintf( esc_html__( 'Importing... %s/%s completed', 's2w-import-shopify-to-woocommerce' ), $imported_products, $history['total_products'] ),
											'imported_products'      => $imported_products,
											'current_import_id'      => $current_import_id,
											'current_import_page'    => $current_import_page,
											'current_import_product' => $current_import_product,
											'logs'                   => $logs,
										) );
									}
								} else {
									VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::update_option( $history_product_option, $history );
									wp_send_json( array(
										'status'                 => 'successful',
										'message'                => sprintf( esc_html__( 'Importing... %s/%s completed', 's2w-import-shopify-to-woocommerce' ), $imported_products, $history['total_products'] ),
										'imported_products'      => $imported_products,
										'current_import_id'      => $current_import_id,
										'current_import_page'    => $current_import_page,
										'current_import_product' => $current_import_product,
										'logs'                   => $logs,
									) );
								}
							}
							if ( $current_import_page == $total_pages ) {
								$history['time'] = current_time( 'timestamp' );
								VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::update_option( $history_product_option, $history );
								wp_send_json( array(
									'status'                 => 'finish',
									'message'                => sprintf( esc_html__( 'Completed %s/%s', 's2w-import-shopify-to-woocommerce' ), $history['total_products'], $history['total_products'] ),
									'imported_products'      => $history['total_products'],
									'current_import_id'      => $current_import_id,
									'current_import_page'    => $current_import_page,
									'current_import_product' => $current_import_product,
									'logs'                   => $logs,
								) );
							} else {
								$imported_products      = $current_import_page * $products_per_file;
								$current_import_product = - 1;
								$current_import_page ++;
								$history['current_import_page'] = $current_import_page;
								VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::update_option( $history_product_option, $history );
								wp_send_json( array(
									'status'                 => 'successful',
									'message'                => sprintf( esc_html__( 'Importing... %s/%s completed', 's2w-import-shopify-to-woocommerce' ), $imported_products, $history['total_products'] ),
									'imported_products'      => $imported_products,
									'current_import_id'      => $current_import_id,
									'current_import_page'    => $current_import_page,
									'current_import_product' => $current_import_product,
									'logs'                   => $logs,
								) );
							}
						}
						wp_send_json( array(
							'status'                 => 'finish',
							'message'                => sprintf( esc_html__( 'Completed %s/%s', 's2w-import-shopify-to-woocommerce' ), $history['total_products'], $history['total_products'] ),
							'imported_products'      => $history['total_products'],
							'current_import_id'      => $current_import_id,
							'current_import_page'    => $current_import_page,
							'current_import_product' => $current_import_product,
							'logs'                   => $logs,
						) );
						break;
					case 'product_categories':
						$file_path  = $path . 'categories.txt';
						$categories = array();
						if ( ! is_file( $file_path ) ) {
							$categories_data = $this->initiate_categories_data( $domain, $api_key, $api_secret, $path );
							if ( $categories_data['status'] == 'success' ) {
								wp_send_json( array(
									'status'                  => 'retry',
									'categories_current_page' => 0,
									'total_categories'        => count( $categories_data['data'] ),
								) );
							} else {
								wp_send_json( array(
									'status'  => $categories_data['status'],
									'message' => $categories_data['data'],
								) );
							}
						} else {
							$categories = json_decode( file_get_contents( $file_path ), true );
						}
						$categories_current_page = isset( $_POST['categories_current_page'] ) ? $_POST['categories_current_page'] : 0;
						$total_categories        = count( $categories );
						if ( ! $total_categories ) {
							wp_send_json( array(
								'status'  => 'error',
								'message' => esc_html__( 'No data to import', 's2w-import-shopify-to-woocommerce' ),
							) );
						}
						if ( isset( $categories[ $categories_current_page ] ) ) {
							$category                 = $categories[ $categories_current_page ];
							$shopify_product_ids_file = $path . 'category_' . $category['shopify_id'] . '.txt';
							$shopify_product_ids      = array();
							if ( ! is_file( $shopify_product_ids_file ) ) {
								$shopify_product_ids_data = $this->get_product_ids_by_collection( $domain, $api_key, $api_secret, $category['shopify_id'], $path );
								if ( $shopify_product_ids_data['status'] == 'success' ) {
									$shopify_product_ids = $shopify_product_ids_data['data'];
								} else {
									wp_send_json( array(
										'status'  => 'error',
										'message' => $shopify_product_ids_data['data'],
									) );
								}
							} else {
								$shopify_product_ids = json_decode( file_get_contents( $shopify_product_ids_file ), true );
							}
							if ( $category['woo_id'] && count( $shopify_product_ids ) ) {
								$args = array(
									'post_type'      => 'product',
									'post_status'    => array( 'publish', 'pending', 'draft' ),
									'posts_per_page' => - 1,
									'fields'         => 'ids',
									'meta_query'     => array(
										'relation' => 'AND',
										array(
											'key'     => '_shopify_product_id',
											'value'   => $shopify_product_ids,
											'compare' => 'IN'
										),
									)
								);

								$the_query = new WP_Query( $args );
								if ( $the_query->have_posts() ) {
									while ( $the_query->have_posts() ) {
										$the_query->the_post();
										$product_id = get_the_ID();
										wp_set_post_terms( $product_id, $category['woo_id'], 'product_cat', true );
									}
								}
								wp_reset_postdata();
							}
						}
						$categories_current_page ++;
						if ( $categories_current_page < $total_categories ) {
							wp_send_json( array(
								'status'                  => 'success',
								'total_categories'        => $total_categories,
								'categories_current_page' => $categories_current_page,
							) );
						} else {
							VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::update_option( $history_option, array(
								'time' => time(),
							) );
							VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_DATA::log( $log_file, 'Import product categories successfully.' );
							wp_send_json( array(
								'status'                  => 'finish',
								'total_categories'        => $total_categories,
								'categories_current_page' => $categories_current_page,
								'message'                 => esc_html__( 'Completed', 's2w-import-shopify-to-woocommerce' ),
							) );
						}
						break;
					default:
				}
			}
		}

		public function add_filters_args( &$import_args ) {
			if ( ! is_array( $import_args ) ) {
				$import_args = array();
			}
			$import_args['order'] = $this->settings->get_params( 'product_import_sequence' );
		}

		public function modal_option() {
			$modals = array(
				'products',
			);
			foreach ( $modals as $modal ) {
				?>
                <div class="<?php esc_attr_e( self::set( array(
					"import-{$modal}-options-modal",
					'hidden'
				) ) ) ?>">
                    <div class="<?php esc_attr_e( self::set( "import-{$modal}-options-overlay" ) ) ?>">
                    </div>
                    <div class="vi-ui segment <?php esc_attr_e( self::set( "import-{$modal}-options-main" ) ) ?>">
                    </div>
                    <div class="<?php esc_attr_e( self::set( array(
						"import-{$modal}-options-saving-overlay",
						'hidden'
					) ) ) ?>">
                    </div>
                </div>
				<?php
			}
		}

		public function admin_enqueue_script() {
			global $pagenow;
			$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';
			wp_enqueue_script( 'import-shopify-to-woocommerce-cancel-download-images', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'cancel-download-images.js', array( 'jquery' ), VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION );
			if ( $pagenow === 'admin.php' && $page === 'import-shopify-to-woocommerce' ) {
				add_action( 'admin_footer', array( $this, 'modal_option' ) );
				$this->is_page = true;
				global $wp_scripts;
				$scripts = $wp_scripts->registered;
				foreach ( $scripts as $k => $script ) {
					preg_match( '/select2/i', $k, $result );
					if ( count( array_filter( $result ) ) ) {
						unset( $wp_scripts->registered[ $k ] );
						wp_dequeue_script( $script->handle );
					}
					preg_match( '/bootstrap/i', $k, $result );
					if ( count( array_filter( $result ) ) ) {
						unset( $wp_scripts->registered[ $k ] );
						wp_dequeue_script( $script->handle );
					}
				}
				// style
				wp_enqueue_style( 'import-shopify-to-woocommerce-message', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'message.min.css' );
				wp_enqueue_style( 'import-shopify-to-woocommerce-form', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'form.min.css' );
				wp_enqueue_style( 'import-shopify-to-woocommerce-button', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'button.min.css' );
				wp_enqueue_style( 'import-shopify-to-woocommerce-icon', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'icon.min.css' );
				wp_enqueue_style( 'import-shopify-to-woocommerce-dropdown', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'dropdown.min.css' );
				wp_enqueue_style( 'import-shopify-to-woocommerce-checkbox', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'checkbox.min.css' );
				wp_enqueue_style( 'import-shopify-to-woocommerce-transition', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'transition.min.css' );
				wp_enqueue_style( 'import-shopify-to-woocommerce-segment', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'segment.min.css' );
				wp_enqueue_style( 'import-shopify-to-woocommerce-menu', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'menu.min.css' );
				wp_enqueue_style( 'import-shopify-to-woocommerce-progress', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'progress.min.css' );
				wp_enqueue_style( 'import-shopify-to-woocommerce-accordion', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'accordion.min.css' );
				wp_enqueue_style( 'import-shopify-to-woocommerce-table', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'table.min.css' );
				wp_enqueue_style( 'import-shopify-to-woocommerce-select2', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'select2.min.css' );

				wp_enqueue_style( 'import-shopify-to-woocommerce-admin', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'admin-style.css', '', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION );
				wp_enqueue_style( 'villatheme-support', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CSS . 'villatheme-support.css' );
				//script
				wp_enqueue_script( 'import-shopify-to-woocommerce-form', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'form.min.js', array( 'jquery' ), VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION );
				wp_enqueue_script( 'import-shopify-to-woocommerce-checkbox', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'checkbox.min.js', array( 'jquery' ), VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION );
				wp_enqueue_script( 'import-shopify-to-woocommerce-dropdown', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'dropdown.min.js', array( 'jquery' ), VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION );
				wp_enqueue_script( 'import-shopify-to-woocommerce-transition', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'transition.min.js', array( 'jquery' ), VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION );
				wp_enqueue_script( 'import-shopify-to-woocommerce-progress', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'progress.min.js', array( 'jquery' ), VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION );
				wp_enqueue_script( 'import-shopify-to-woocommerce-accordion', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'accordion.min.js', array( 'jquery' ), VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION );
				wp_enqueue_script( 'import-shopify-to-woocommerce-select2', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'select2.js', array( 'jquery' ), VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION );
				wp_enqueue_script( 'import-shopify-to-woocommerce-admin', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_JS . 'admin-script.js', array( 'jquery' ), VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_VERSION );
				$history           = get_option( 's2w_' . $this->settings->get_params( 'domain' ) . '_history', array(
					'total_products'         => 0,
					'total_pages'            => 0,
					'current_import_id'      => '',
					'current_import_product' => - 1,
					'current_import_page'    => 1,
					'products_per_file'      => 250,
					'last_product_error'     => '',
				) );
				$history_orders    = get_option( 's2w_' . $this->settings->get_params( 'domain' ) . '_history_orders', array(
					'total_orders'               => 0,
					'orders_total_pages'         => 0,
					'orders_current_import_id'   => '',
					'current_import_order'       => - 1,
					'orders_current_import_page' => 1,
					'orders_per_file'            => 250,
					'orders_per_request'         => 50,
					'last_order_error'           => '',
				) );
				$history_customers = get_option( 's2w_' . $this->settings->get_params( 'domain' ) . '_history_customers', array(
					'total_customers'               => 0,
					'customers_total_pages'         => 0,
					'customers_current_import_id'   => '',
					'current_import_customer'       => - 1,
					'customers_current_import_page' => 1,
					'customers_per_file'            => 250,
					'customers_per_request'         => 100,
					'last_customer_error'           => '',
				) );

				$elements = array(
					'store_settings' => '',
					'payments'       => '',
					'shipping_zones' => '',
					'taxes'          => '',
					'pages'          => '',
					'blogs'          => '',
					'coupons'        => '',
				);

				foreach ( $elements as $key => $value ) {
					$element = get_option( 's2w_' . $this->settings->get_params( 'domain' ) . '_history_' . $key );
					if ( isset( $element['time'] ) && $element['time'] ) {
						$elements[ $key ] = 1;
					}
				}
				$elements['products']  = isset( $history['time'] ) && $history['time'] ? 1 : '';
				$elements['customers'] = isset( $history_customers['time'] ) && $history_customers['time'] ? 1 : '';
				$elements['orders']    = isset( $history_orders['time'] ) && $history_orders['time'] ? 1 : '';
				$elements_titles       = array(
					'store_settings' => esc_html__( 'Store settings', 'import-shopify-to-woocommerce' ),
					'payments'       => esc_html__( 'Payments', 'import-shopify-to-woocommerce' ),
					'shipping_zones' => esc_html__( 'Shipping zones', 'import-shopify-to-woocommerce' ),
					'taxes'          => esc_html__( 'Taxes', 'import-shopify-to-woocommerce' ),
					'pages'          => esc_html__( 'Pages', 'import-shopify-to-woocommerce' ),
					'blogs'          => esc_html__( 'Blogs', 'import-shopify-to-woocommerce' ),
					'coupons'        => esc_html__( 'Coupons', 'import-shopify-to-woocommerce' ),
					'customers'      => esc_html__( 'Customers', 'import-shopify-to-woocommerce' ),
					'products'       => esc_html__( 'Products', 'import-shopify-to-woocommerce' ),
					'orders'         => esc_html__( 'Orders', 'import-shopify-to-woocommerce' ),
				);
				wp_localize_script( 'import-shopify-to-woocommerce-admin', 's2w_params_admin', array_merge( $history_customers, $history_orders, $history, array(
					'url'                       => admin_url( 'admin-ajax.php' ),
					'warning_empty_store'       => esc_html__( 'Store address can not be empty! ', 'import-shopify-to-woocommerce' ),
					'warning_empty_api_key'     => esc_html__( 'API key can not be empty! ', 'import-shopify-to-woocommerce' ),
					'warning_empty_api_secret'  => esc_html__( 'API secret can not be empty! ', 'import-shopify-to-woocommerce' ),
					'error_connection'          => esc_html__( 'Can not connect to your Shopify store. Please check your info.', 'import-shopify-to-woocommerce' ),
					'error_assign_categories'   => esc_html__( 'Error assigning product categories', 'import-shopify-to-woocommerce' ),
					'message_checking'          => esc_html__( 'Checking, please wait ...', 'import-shopify-to-woocommerce' ),
					'message_guide'             => esc_html__( 'Click Import to start importing or Update cache to fetch new data to import', 'import-shopify-to-woocommerce' ),
					'message_assign_categories' => esc_html__( 'Assigning product categories.', 'import-shopify-to-woocommerce' ),
					'message_importing'         => esc_html__( 'Importing...', 'import-shopify-to-woocommerce' ),
					'message_complete'          => esc_html__( 'Completed', 'import-shopify-to-woocommerce' ),

					'imported_elements' => $elements,
					'elements_titles'   => $elements_titles,
				) ) );
			}
		}

		public function admin_menu() {
			add_menu_page( esc_html__( 'Import Shopify to WooCommerce', 'import-shopify-to-woocommerce' ), esc_html__( 'Shopify to Woo', 'import-shopify-to-woocommerce' ), 'manage_options', 'import-shopify-to-woocommerce', array(
				$this,
				'settings_callback'
			), 'dashicons-image-rotate-right', 2 );
		}

		public function admin_menu_system_log() {
			add_submenu_page(
				'import-shopify-to-woocommerce',
				esc_html__( 'Logs', 'import-shopify-to-woocommerce' ),
				esc_html__( 'Logs', 'import-shopify-to-woocommerce' ),
				'manage_options',
				'import-shopify-to-woocommerce-logs',
				array( $this, 'page_callback_logs' )
			);
			add_submenu_page(
				'import-shopify-to-woocommerce',
				esc_html__( 'System Status', 'import-shopify-to-woocommerce' ),
				esc_html__( 'System Status', 'import-shopify-to-woocommerce' ),
				'manage_options',
				'import-shopify-to-woocommerce-status',
				array( $this, 'page_callback_system_status' )
			);
		}

		public function page_callback_logs() {
			$logs = glob( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CACHE . '*/logs.txt' );
			?>
            <div class="wrap">
                <h2><?php esc_html_e( 'Import Shopify to WooCommerce log files', 'import-shopify-to-woocommerce' ); ?></h2>
            </div>
			<?php
			if ( is_array( $logs ) && count( $logs ) ) {
				foreach ( $logs as $log ) {
					?>
                    <p><?php esc_html_e( $log ) ?>
                        <a target="_blank" rel="nofollow"
                           href="<?php esc_attr_e( add_query_arg( array(
							   'action'   => 's2w_view_log',
							   's2w_file' => urlencode( $log ),
							   '_wpnonce' => wp_create_nonce( 's2w_view_log' ),
						   ), admin_url( 'admin-ajax.php' ) ) ) ?>"><?php esc_html_e( 'View', 'import-shopify-to-woocommerce' ) ?>
                        </a>
                    </p>
					<?php
				}
			}
		}

		public static function security_recommendation_html() {
			?>
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
			<?php
		}

		public function settings_callback() {
			$active = $this->settings->get_params( 'validate' );
			?>
            <div class="wrap">
                <h2><?php esc_html_e( 'Import Shopify to WooCommerce', 'import-shopify-to-woocommerce' ); ?></h2>
				<?php self::security_recommendation_html(); ?>
                <p></p>
                <div class="vi-ui negative message <?php esc_attr_e( self::set( 'error-warning' ) ) ?>"
                     style="<?php if ( $active )
					     esc_attr_e( 'display:none' ) ?>">
                    <p><?php esc_html_e( 'You need to enter correct domain, API key and API secret to be able to import', 'import-shopify-to-woocommerce' ); ?></p>
                </div>
                <p></p>
                <div class="vi-ui styled fluid accordion <?php if ( ! $active ) {
					esc_attr_e( 'active' );
				} ?> <?php esc_attr_e( self::set( 'accordion' ) ) ?>">
                    <div class="title">
                        <i class="dropdown icon"></i>
						<?php esc_html_e( 'General settings', 'import-shopify-to-woocommerce' ) ?>
                    </div>
                    <div class="content <?php if ( ! $active )
						esc_attr_e( 'active' ) ?>">
                        <form class="vi-ui form" method="post">
							<?php wp_nonce_field( 's2w_action_nonce', '_s2w_nonce' ); ?>
                            <div class="vi-ui segment">
                                <table class="form-table">
                                    <tbody>
                                    <tr>
                                        <th>
                                            <label for="<?php esc_attr_e( self::set( 'domain' ) ) ?>"><?php esc_html_e( 'Store address', 'import-shopify-to-woocommerce' ) ?></label>
                                        </th>
                                        <td>
                                            <input type="text"
                                                   name="<?php esc_attr_e( self::set( 'domain', true ) ) ?>"
                                                   id="<?php esc_attr_e( self::set( 'domain' ) ) ?>"
                                                   value="<?php esc_attr_e( htmlentities( $this->settings->get_params( 'domain' ) ) ) ?>">
                                            <label for="<?php esc_attr_e( self::set( 'domain' ) ) ?>"><?php echo __( 'Your Store address, eg: <strong>myshop.myshopify.com</strong>', 'import-shopify-to-woocommerce' ) ?></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <label for="<?php esc_attr_e( self::set( 'api_key' ) ) ?>"><?php esc_html_e( 'API key', 'import-shopify-to-woocommerce' ) ?></label>
                                        </th>
                                        <td>
                                            <input type="text"
                                                   name="<?php esc_attr_e( self::set( 'api_key', true ) ) ?>"
                                                   id="<?php esc_attr_e( self::set( 'api_key' ) ) ?>"
                                                   value="<?php esc_attr_e( htmlentities( $this->settings->get_params( 'api_key' ) ) ) ?>">
                                            <label for="<?php esc_attr_e( self::set( 'api_key' ) ) ?>"><?php esc_html_e( 'The API key that has the right to access your products', 'import-shopify-to-woocommerce' ) ?></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <label for="<?php esc_attr_e( self::set( 'api_secret' ) ) ?>"><?php esc_html_e( 'API secret(Password)', 'import-shopify-to-woocommerce' ) ?></label>
                                        </th>
                                        <td>
                                            <input type="text"
                                                   name="<?php esc_attr_e( self::set( 'api_secret', true ) ) ?>"
                                                   id="<?php esc_attr_e( self::set( 'api_secret' ) ) ?>"
                                                   value="<?php esc_attr_e( htmlentities( $this->settings->get_params( 'api_secret' ) ) ) ?>">
                                            <label for="<?php esc_attr_e( self::set( 'api_secret' ) ) ?>"><?php esc_html_e( 'Password of the API key above', 'import-shopify-to-woocommerce' ) ?></label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <td>
                                            <div class="title">
                                                <i class="dropdown icon"></i>
												<?php esc_html_e( 'Learn how to get API key', 'import-shopify-to-woocommerce' ) ?>
                                            </div>
                                            <div class="content">
                                                <iframe width="560" height="315"
                                                        src="https://www.youtube.com/embed/LOgFgNWLp8w" frameborder="0"
                                                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                                        allowfullscreen></iframe>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <label for="<?php esc_attr_e( self::set( 'request_timeout' ) ) ?>"><?php esc_html_e( 'Request timeout(s)', 'import-shopify-to-woocommerce' ) ?></label>
                                        </th>
                                        <td>
                                            <input type="number" min="1"
                                                   name="<?php esc_attr_e( self::set( 'request_timeout', true ) ) ?>"
                                                   id="<?php esc_attr_e( self::set( 'request_timeout' ) ) ?>"
                                                   value="<?php esc_attr_e( $this->settings->get_params( 'request_timeout' ) ) ?>">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="vi-ui segment"
                                 id="<?php esc_attr_e( self::set( 'import-products-options' ) ) ?>">
                                <h3><?php esc_html_e( 'Import Products options', 'import-shopify-to-woocommerce' ) ?></h3>
                                <div class="<?php esc_attr_e( self::set( 'import-products-options-content' ) ) ?>">
                                    <div class="<?php esc_attr_e( self::set( 'import-products-options-heading' ) ) ?>">
                                        <div class="<?php esc_attr_e( self::set( 'save-products-options-container' ) ) ?>">
                                            <span class="vi-ui primary button <?php esc_attr_e( self::set( 'save-products-options' ) ) ?>"><?php esc_html_e( 'Save', 'import-shopify-to-woocommerce' ) ?></span>
                                        </div>
                                        <i class="close icon <?php esc_attr_e( self::set( 'import-products-options-close' ) ) ?>"></i>
                                        <h3><?php esc_html_e( 'Import Products options', 'import-shopify-to-woocommerce' ) ?></h3>
                                    </div>
                                    <table class="form-table">
                                        <tbody>
                                        <tr>
                                            <th>
                                                <label for="<?php esc_attr_e( self::set( 'products_per_request' ) ) ?>"><?php esc_html_e( 'Products per ajax request', 'import-shopify-to-woocommerce' ) ?></label>
                                            </th>
                                            <td>
                                                <input type="number" min="1" max="250"
                                                       name="<?php esc_attr_e( self::set( 'products_per_request', true ) ) ?>"
                                                       id="<?php esc_attr_e( self::set( 'products_per_request' ) ) ?>"
                                                       value="<?php esc_attr_e( $this->settings->get_params( 'products_per_request' ) ) ?>">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="<?php esc_attr_e( self::set( 'product_since_id' ) ) ?>"><?php esc_html_e( 'Restrict results to after the specified ID', 'import-shopify-to-woocommerce' ) ?></label>
                                            </th>
                                            <td>
	                                            <?php self::upgrade_button();?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="<?php esc_attr_e( self::set( 'product_product_type' ) ) ?>"><?php esc_html_e( 'Filter results by product type', 'import-shopify-to-woocommerce' ) ?></label>
                                            </th>
                                            <td>
	                                            <?php self::upgrade_button();?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="<?php esc_attr_e( self::set( 'product_product_type' ) ) ?>"><?php esc_html_e( 'Filter results by product vendor', 'import-shopify-to-woocommerce' ) ?></label>
                                            </th>
                                            <td>
	                                            <?php self::upgrade_button();?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="<?php esc_attr_e( self::set( 'product_collection_id' ) ) ?>"><?php esc_html_e( 'Filter results by collection ID', 'import-shopify-to-woocommerce' ) ?></label>
                                            </th>
                                            <td>
	                                            <?php self::upgrade_button();?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="<?php esc_attr_e( self::set( 'product_published_at_min' ) ) ?>"><?php esc_html_e( 'Import products published after date', 'import-shopify-to-woocommerce' ) ?></label>
                                            </th>
                                            <td>
	                                            <?php self::upgrade_button();?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="<?php esc_attr_e( self::set( 'product_published_at_max' ) ) ?>"><?php esc_html_e( 'Import products published before date', 'import-shopify-to-woocommerce' ) ?></label>
                                            </th>
                                            <td>
	                                            <?php self::upgrade_button();?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="<?php esc_attr_e( self::set( 'global_attributes' ) ) ?>"><?php esc_html_e( 'Use global attributes', 'import-shopify-to-woocommerce' ) ?></label>
                                            </th>
                                            <td>
	                                            <?php self::upgrade_button();?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="<?php esc_attr_e( self::set( 'keep_slug' ) ) ?>"><?php esc_html_e( 'Keep product slug', 'import-shopify-to-woocommerce' ) ?></label>
                                            </th>
                                            <td>
	                                            <?php self::upgrade_button();?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="<?php esc_attr_e( self::set( 'product_import_sequence' ) ) ?>"><?php esc_html_e( 'Import Products sequence', 'import-shopify-to-woocommerce' ) ?></label>
                                            </th>
                                            <td>
                                                <select name="<?php esc_attr_e( self::set( 'product_import_sequence', true ) ) ?>"
                                                        class="vi-ui fluid dropdown"
                                                        id="<?php esc_attr_e( self::set( 'product_import_sequence' ) ) ?>">
                                                    <option value="title asc" <?php selected( 'title asc', $this->settings->get_params( 'product_import_sequence' ) ) ?>><?php esc_html_e( 'Order by Title Ascending', 'import-shopify-to-woocommerce' ) ?></option>
                                                    <option value="title desc" <?php selected( 'title desc', $this->settings->get_params( 'product_import_sequence' ) ) ?>><?php esc_html_e( 'Order by Title Descending', 'import-shopify-to-woocommerce' ) ?></option>
                                                    <option value="created_at asc" <?php selected( 'created_at asc', $this->settings->get_params( 'product_import_sequence' ) ) ?>><?php esc_html_e( 'Order by Created Date Ascending', 'import-shopify-to-woocommerce' ) ?></option>
                                                    <option value="created_at desc" <?php selected( 'created_at desc', $this->settings->get_params( 'product_import_sequence' ) ) ?>><?php esc_html_e( 'Order by Created Date Descending', 'import-shopify-to-woocommerce' ) ?></option>
                                                    <option value="updated_at asc" <?php selected( 'updated_at asc', $this->settings->get_params( 'product_import_sequence' ) ) ?>><?php esc_html_e( 'Order by Updated Date Ascending', 'import-shopify-to-woocommerce' ) ?></option>
                                                    <option value="updated_at desc" <?php selected( 'updated_at desc', $this->settings->get_params( 'product_import_sequence' ) ) ?>><?php esc_html_e( 'Order by Updated Date Descending', 'import-shopify-to-woocommerce' ) ?></option>
                                                </select>
                                                <p><?php esc_html_e( 'This is to sort the results after applying all filters above if any', 'import-shopify-to-woocommerce' ) ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="<?php esc_attr_e( self::set( 'download_images' ) ) ?>"><?php esc_html_e( 'Download images', 'import-shopify-to-woocommerce' ) ?></label>
                                            </th>
                                            <td>
                                                <div class="vi-ui toggle checkbox checked">
                                                    <input type="checkbox"
                                                           name="<?php esc_attr_e( self::set( 'download_images', true ) ) ?>"
                                                           id="<?php esc_attr_e( self::set( 'download_images' ) ) ?>"
                                                           value="1" <?php checked( $this->settings->get_params( 'download_images' ), '1' ) ?>>
                                                    <label for="<?php esc_attr_e( self::set( 'download_images' ) ) ?>"><?php esc_html_e( 'Product images will be downloaded in the background.', 'import-shopify-to-woocommerce' ) ?></label>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="<?php echo esc_attr( self::set( 'download_description_images' ) ) ?>"><?php esc_html_e( 'Download description images', 's2w-import-shopify-to-woocommerce' ) ?></label>
                                            </th>
                                            <td>
	                                            <?php self::upgrade_button();?>
                                                <p class="description"><?php esc_html_e( 'Also download images from product description in the background.', 's2w-import-shopify-to-woocommerce' ) ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="<?php esc_attr_e( self::set( 'product_status' ) ) ?>"><?php esc_html_e( 'Product status', 'import-shopify-to-woocommerce' ) ?></label>
                                            </th>
                                            <td>
                                                <div>
                                                    <select class="vi-ui fluid dropdown"
                                                            id="<?php esc_attr_e( self::set( 'product_status' ) ) ?>"
                                                            name="<?php esc_attr_e( self::set( 'product_status', true ) ) ?>">
                                                        <option value="publish" <?php selected( $this->settings->get_params( 'product_status' ), 'publish' ) ?>><?php esc_html_e( 'Publish', 'import-shopify-to-woocommerce' ) ?></option>
                                                        <option value="pending" <?php selected( $this->settings->get_params( 'product_status' ), 'pending' ) ?>><?php esc_html_e( 'Pending', 'import-shopify-to-woocommerce' ) ?></option>
                                                        <option value="draft" <?php selected( $this->settings->get_params( 'product_status' ), 'draft' ) ?>><?php esc_html_e( 'Draft', 'import-shopify-to-woocommerce' ) ?></option>
                                                    </select>
                                                </div>
                                                <p><?php esc_html_e( 'Status of products after importing successfully', 'import-shopify-to-woocommerce' ) ?></p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <label for="<?php esc_attr_e( self::set( 'product_categories' ) ) ?>"><?php esc_html_e( 'Product categories', 'import-shopify-to-woocommerce' ) ?></label>
                                            </th>
                                            <td>
                                                <div>
                                                    <select class="search-category"
                                                            id="<?php esc_attr_e( self::set( 'product_categories' ) ) ?>"
                                                            name="<?php esc_attr_e( self::set( 'product_categories', true ) ) ?>[]"
                                                            multiple="multiple">
														<?php

														if ( is_array( $this->settings->get_params( 'product_categories' ) ) && count( $this->settings->get_params( 'product_categories' ) ) ) {
															foreach ( $this->settings->get_params( 'product_categories' ) as $category_id ) {
																$category = get_term( $category_id );
																?>
                                                                <option value="<?php esc_attr_e( $category_id ) ?>"
                                                                        selected><?php esc_html_e( $category->name ); ?></option>
																<?php
															}
														}
														?>
                                                    </select>
                                                </div>
                                                <p><?php esc_html_e( 'Choose categories you want to add imported products to', 'import-shopify-to-woocommerce' ) ?></p>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                            <p>
                                <span class="vi-ui primary button <?php esc_attr_e( self::set( 'save' ) ) ?>"><?php esc_html_e( 'Save', 'import-shopify-to-woocommerce' ) ?></span>
                            </p>
                        </form>
                    </div>
                </div>
                <p></p>

                <form class="vi-ui form <?php esc_attr_e( self::set( 'import-container' ) ) ?>"
                      style="<?php if ( ! $active )
					      esc_attr_e( 'display:none' ) ?>"
                      method="POST">
                    <div class="vi-ui segment">
                        <div class="vi-ui styled fluid accordion">
                            <div class="title">
                                <i class="dropdown icon"></i>
								<?php esc_html_e( 'How to use this plugin', 'import-shopify-to-woocommerce' ) ?>
                            </div>
                            <div class="content">
                                <iframe width="560" height="315" src="https://www.youtube.com/embed/DF3XiCeSOhQ"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                            </div>
                        </div>
						<?php
						$elements = array(
							'products'           => esc_html__( 'Products', 'import-shopify-to-woocommerce' ),
							'product_categories' => esc_html__( 'Product categories', 'import-shopify-to-woocommerce' ),
							'store_settings'     => esc_html__( 'Store settings', 'import-shopify-to-woocommerce' ),
							'shipping_zones'     => esc_html__( 'Shipping zones', 'import-shopify-to-woocommerce' ),
							'taxes'              => esc_html__( 'Taxes', 'import-shopify-to-woocommerce' ),
							'pages'              => esc_html__( 'Pages', 'import-shopify-to-woocommerce' ),
							'blogs'              => esc_html__( 'Blogs', 'import-shopify-to-woocommerce' ),
							'customers'          => esc_html__( 'Customers', 'import-shopify-to-woocommerce' ),
							'coupons'            => esc_html__( 'Coupons', 'import-shopify-to-woocommerce' ),
							'orders'             => esc_html__( 'Orders', 'import-shopify-to-woocommerce' ),
						);
						?>
                        <table id="<?php esc_attr_e( self::set( 'table-import-progress' ) ) ?>"
                               class="vi-ui celled table center aligned">
                            <thead>
                            <tr>
                                <th style="width: 200px;"><?php esc_html_e( 'Data', 'import-shopify-to-woocommerce' ) ?></th>
                                <th style="width: 200px;"><?php esc_html_e( 'Enable', 'import-shopify-to-woocommerce' ) ?></th>
                                <th><?php esc_html_e( 'Status', 'import-shopify-to-woocommerce' ) ?></th>
                            </tr>
                            </thead>
                            <tbody>
							<?php
							if ( is_array( $elements ) && count( $elements ) ) {
								foreach ( $elements as $key => $value ) {
									if ( $key == 'products' ) {
										$history           = get_option( 's2w_' . $this->settings->get_params( 'domain' ) . '_history', array(
											'total_products'         => 0,
											'total_pages'            => 0,
											'current_import_id'      => '',
											'current_import_product' => - 1,
											'current_import_page'    => 1,
											'products_per_file'      => 250,
											'last_product_error'     => '',
										) );
										$imported_products = isset( $history['current_import_product'] ) ? ( intval( $history['current_import_product'] ) + 1 ) : '0';
										$total_products    = isset( $history['total_products'] ) ? intval( $history['total_products'] ) : '0';
										?>
                                        <tr>
                                            <td><?php echo $value ?>
                                                <a href="#s2w-import-products-options"
                                                   class="<?php esc_attr_e( self::set( 'import-products-options-shortcut' ) ) ?>"><?php esc_html_e( 'View settings', 'import-shopify-to-woocommerce' ) ?></a>
                                            </td>
                                            <td class="<?php esc_attr_e( self::set( 'import-' . str_replace( '_', '-', $key ) . '-enable' ) ) ?>">
                                                <div class="vi-ui toggle checkbox checked">
                                                    <input type="checkbox"
                                                           id="<?php esc_attr_e( self::set( 'import-' . str_replace( '_', '-', $key ) . '-enable' ) ) ?>"
                                                           class="<?php esc_attr_e( self::set( 'import-element-enable' ) ) ?>"
                                                           data-element_name="<?php esc_attr_e( $key ) ?>"
                                                           name="<?php esc_attr_e( $key ) ?>"
                                                           value="1" <?php if ( ! $total_products || $imported_products < $total_products ) {
														esc_attr_e( ' checked' );
													} ?>>
                                                </div>
                                                <i class="<?php esc_attr_e( self::set( 'import-' . str_replace( '_', '-', $key ) . '-check-icon' ) ) ?> vi-ui check icon <?php esc_attr_e( ( ! $total_products || $imported_products < $total_products ) ? 'grey' : 'green' ) ?>"></i>
                                            </td>
                                            <td class="<?php esc_attr_e( self::set( 'import-' . str_replace( '_', '-', $key ) . '-status' ) ) ?>">
                                                <div class="vi-ui indicating progress standard <?php esc_attr_e( self::set( 'import-progress' ) ) ?>"
                                                     style="visibility: hidden"
                                                     id="<?php esc_attr_e( 's2w-' . str_replace( '_', '-', $key ) . '-progress' ) ?>">
                                                    <div class="label"></div>
                                                    <div class="bar">
                                                        <div class="progress"></div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
										<?php
									} elseif ( in_array( $key, array(
										'store_settings',
										'shipping_zones',
										'taxes',
										'pages',
										'blogs',
										'customers',
										'coupons',
										'orders',
									) ) ) {
										?>
                                        <tr>
                                            <td><?php esc_html_e( isset( $value ) ? $value : ucwords( str_replace( '_', ' ', $key ) ) ) ?></td>
                                            <td class="<?php esc_attr_e( self::set( 'import-' . str_replace( '_', '-', $key ) . '-enable' ) ) ?>">
                                                <?php self::upgrade_button();?>
                                            </td>
                                            <td class="<?php esc_attr_e( self::set( 'import-' . str_replace( '_', '-', $key ) . '-status' ) ) ?>">
                                            </td>
                                        </tr>
										<?php
									} else {
										$history_element = get_option( 's2w_' . $this->settings->get_params( 'domain' ) . '_history_' . $key );
										$check           = 1;
										$time            = isset( $history_element['time'] ) && $history_element['time'] ? $history_element['time'] : '';
										if ( $time ) {
											$check = 0;
										}
										?>
                                        <tr>
                                            <td><?php esc_html_e( isset( $value ) ? $value : ucwords( str_replace( '_', ' ', $key ) ) ) ?></td>
                                            <td class="<?php esc_attr_e( self::set( 'import-' . str_replace( '_', '-', $key ) . '-enable' ) ) ?>">
                                                <div class="vi-ui toggle checkbox checked">
                                                    <input type="checkbox"
                                                           id="<?php esc_attr_e( self::set( 'import-' . str_replace( '_', '-', $key ) . '-enable' ) ) ?>"
                                                           class="<?php esc_attr_e( self::set( 'import-element-enable' ) ) ?>"
                                                           data-element_name="<?php esc_attr_e( $key ) ?>"
                                                           name="<?php esc_attr_e( $key ) ?>"
														<?php checked( $check, 1 ) ?>
                                                           value="1">
                                                </div>
                                                <i class="<?php esc_attr_e( self::set( 'import-' . str_replace( '_', '-', $key ) . '-check-icon' ) ) ?> vi-ui check icon <?php esc_attr_e( $check ? 'grey' : 'green' ) ?>"
                                                   title="<?php if ( ! $check ) {
													   printf( esc_attr__( 'Imported: %s', 'import-shopify-to-woocommerce' ), date_i18n( 'F d, Y', $time ) );
												   } ?>"></i>
                                            </td>
                                            <td class="<?php esc_attr_e( self::set( 'import-' . str_replace( '_', '-', $key ) . '-status' ) ) ?>">
                                                <div class="vi-ui indicating progress standard <?php esc_attr_e( self::set( 'import-progress' ) ) ?>"
                                                     style="visibility: hidden"
                                                     id="<?php esc_attr_e( 's2w-' . str_replace( '_', '-', $key ) . '-progress' ) ?>">
                                                    <div class="label"></div>
                                                    <div class="bar">
                                                        <div class="progress"></div>
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
										<?php
									}

								}
							}
							?>
                            <tr>
                                <td>
                                    <strong><?php esc_html_e( 'Enable all', 'import-shopify-to-woocommerce' ) ?></strong>
                                </td>
                                <td>
                                    <div class="vi-ui toggle checkbox checked">
                                        <input type="checkbox"
                                               class="<?php esc_attr_e( self::set( 'import-element-enable-bulk' ) ) ?>">
                                    </div>
                                    <i class="vi-ui check icon" style="visibility: hidden"></i>
                                </td>
                                <td></td>
                            </tr>
                            </tbody>
                        </table>
                        <p>
                            <span class="vi-ui positive button <?php esc_attr_e( self::set( 'sync' ) ) ?>"><?php esc_html_e( 'Import', 'import-shopify-to-woocommerce' ) ?></span>
                            <input type="submit" name="s2w_delete_history"
                                   value="<?php esc_html_e( 'Delete import history', 'import-shopify-to-woocommerce' ) ?>"
                                   class="vi-ui negative button <?php esc_attr_e( self::set( 'delete-history' ) ) ?>">
                        </p>
                        <h4><?php esc_html_e( 'Logs: ', 'import-shopify-to-woocommerce' ) ?></h4>
                        <div class="vi-ui segment <?php esc_attr_e( self::set( 'logs' ) ) ?>">
                        </div>
                    </div>
                </form>
            </div>
			<?php
			do_action( 'villatheme_support_import-shopify-to-woocommerce' );
		}

		public function page_callback_system_status() { ?>
            <h2><?php esc_html_e( 'System Status', 'import-shopify-to-woocommerce' ) ?></h2>
            <table cellspacing="0" id="status" class="widefat">
                <thead>
                <tr>
                    <th><?php esc_html_e( 'Option name', 'import-shopify-to-woocommerce' ) ?></th>
                    <th><?php esc_html_e( 'Your option value', 'import-shopify-to-woocommerce' ) ?></th>
                    <th><?php esc_html_e( 'Minimum recommended value', 'import-shopify-to-woocommerce' ) ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td data-export-label="file_get_contents">file_get_contents</td>
                    <td>
						<?php
						if ( function_exists( 'file_get_contents' ) ) {
							?>
                            <mark class="yes">&#10004; <code class="private"></code></mark>
							<?php
						} else {
							?>
                            <mark class="error">&#10005;</mark>'
							<?php
						}
						?>
                    </td>
                    <td><?php esc_html_e( 'Required', 'import-shopify-to-woocommerce' ) ?></td>
                </tr>
                <tr>
                    <td data-export-label="file_put_contents">file_put_contents</td>
                    <td>
						<?php
						if ( function_exists( 'file_put_contents' ) ) {
							?>
                            <mark class="yes">&#10004; <code class="private"></code></mark>
							<?php
						} else {
							?>
                            <mark class="error">&#10005;</mark>
							<?php
						}
						?>

                    </td>
                    <td><?php esc_html_e( 'Required', 'import-shopify-to-woocommerce' ) ?></td>
                </tr>
                <tr>
                    <td data-export-label="mkdir">mkdir</td>
                    <td>
						<?php
						if ( function_exists( 'mkdir' ) ) {
							?>
                            <mark class="yes">&#10004; <code class="private"></code></mark>
							<?php
						} else {
							?>
                            <mark class="error">&#10005;</mark>
							<?php
						}
						?>

                    </td>
                    <td><?php esc_html_e( 'Required', 'import-shopify-to-woocommerce' ) ?></td>
                </tr>
                <tr>
                    <td data-export-label="<?php esc_html_e( 'Log Directory Writable', 'import-shopify-to-woocommerce' ) ?>"><?php esc_html_e( 'Log Directory Writable', 'import-shopify-to-woocommerce' ) ?></td>
                    <td>
						<?php

						if ( wp_is_writable( VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CACHE ) ) {
							echo '<mark class="yes">&#10004; <code class="private">' . VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CACHE . '</code></mark> ';
						} else {
							printf( '<mark class="error">&#10005; ' . __( 'To allow logging, make <code>%s</code> writable or define a custom <code>VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CACHE</code>.', 'import-shopify-to-woocommerce' ) . '</mark>', VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_CACHE );
						}
						?>

                    </td>
                    <td><?php esc_html_e( 'Required', 'import-shopify-to-woocommerce' ) ?></td>
                </tr>
				<?php
				$max_execution_time = ini_get( 'max_execution_time' );
				$max_input_vars     = ini_get( 'max_input_vars' );
				$memory_limit       = ini_get( 'memory_limit' );
				?>
                <tr>
                    <td data-export-label="<?php esc_attr_e( 'PHP Time Limit', 'import-shopify-to-woocommerce' ) ?>"><?php esc_html_e( 'PHP Time Limit', 'import-shopify-to-woocommerce' ) ?></td>
                    <td style="<?php if ( $max_execution_time > 0 && $max_execution_time < 300 ) {
						esc_attr_e( 'color:red' );
					} ?>"><?php esc_html_e( $max_execution_time ); ?></td>
                    <td><?php esc_html_e( '300', 'import-shopify-to-woocommerce' ) ?></td>
                </tr>
                <tr>
                    <td data-export-label="<?php esc_attr_e( 'PHP Max Input Vars', 'import-shopify-to-woocommerce' ) ?>"><?php esc_html_e( 'PHP Max Input Vars', 'import-shopify-to-woocommerce' ) ?></td>
                    <td style="<?php if ( $max_input_vars < 1000 ) {
						esc_attr_e( 'color:red' );
					} ?>"><?php esc_html_e( $max_input_vars ); ?></td>
                    <td><?php esc_html_e( '1000', 'import-shopify-to-woocommerce' ) ?></td>
                </tr>
                <tr>
                    <td data-export-label="<?php esc_attr_e( 'Memory Limit', 'import-shopify-to-woocommerce' ) ?>"><?php esc_html_e( 'Memory Limit', 'import-shopify-to-woocommerce' ) ?></td>
                    <td style="<?php if ( intval( $memory_limit ) < 64 ) {
						esc_attr_e( 'color:red' );
					} ?>"><?php esc_html_e( $memory_limit ); ?></td>
                    <td><?php esc_html_e( '64M', 'import-shopify-to-woocommerce' ) ?></td>
                </tr>
                </tbody>
            </table>
			<?php
		}

		public static function upgrade_button() {
			?>
            <a href="https://1.envato.market/9arQ5"
               target="_blank"
               class="vi-ui button yellow"><?php esc_html_e( 'Upgrade this feature', 'import-shopify-to-woocommerce' ) ?></a>
			<?php
		}
	}
}
new IMPORT_SHOPIFY_TO_WOOCOMMERCE();