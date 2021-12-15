<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WOO_PRODUCT_VARIATIONS_SWATCHES_Admin_Setting_Global_Attrs {
	protected $settings;
	protected $error;

	function __construct() {
		$this->settings = new VI_WOO_PRODUCT_VARIATIONS_SWATCHES_DATA();
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 20 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 99999 );
		add_action( 'wp_ajax_vi_wvps_save_global_attrs', array( $this, 'save_attr' ) );
		add_filter( 'set-screen-option', array( $this, 'save_screen_options' ), 10, 3 );
	}

	public function save_attr() {
		$response = array(
			'status'  => 'failed',
			'message' => '',
		);
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'vi_wvps_global_attrs_action' ) ) {
			$response['message'] = 'wp_verify_nonce failed';
			wp_send_json( $response );
		}
		global $vi_wpvs_settings;
		$slug         = isset( $_POST['taxonomy_slug'] ) ? sanitize_text_field( $_POST['taxonomy_slug'] ) : '';
		$profile      = isset( $_POST['taxonomy_profile'] ) ? sanitize_text_field( $_POST['taxonomy_profile'] ) : '';
		$type         = isset( $_POST['taxonomy_type'] ) ? sanitize_text_field( $_POST['taxonomy_type'] ) : 'select';
		$display_type = isset( $_POST['taxonomy_display_type'] ) ? sanitize_text_field( $_POST['taxonomy_display_type'] ) : '';
		$term_data    = isset( $_POST['term_data'] ) ? vi_wpvs_sanitize_fields( $_POST['term_data'] ) : array();

		if ( ! $slug ) {
			$response['message'] = 'not found taxonomy_slug';
			wp_send_json( $response );
		}
		//save option
		$args                                   = array();
		$taxonomy_profiles                      = isset( $vi_wpvs_settings['taxonomy_profiles'] ) ? $vi_wpvs_settings['taxonomy_profiles'] : array();
		$taxonomy_display_type                  = isset( $vi_wpvs_settings['taxonomy_display_type'] ) ? $vi_wpvs_settings['taxonomy_display_type'] : array();
		$taxonomy_profiles[ 'pa_' . $slug ]     = $profile;
		$args ['taxonomy_profiles']             = $taxonomy_profiles;
		$taxonomy_display_type[ 'pa_' . $slug ] = $display_type;
		$args ['taxonomy_display_type']         = $taxonomy_display_type;
		$args                                   = wp_parse_args( $args, get_option( 'vi_woo_product_variation_swatches_params', $vi_wpvs_settings ) );
		update_option( 'vi_woo_product_variation_swatches_params', $args );
		$vi_wpvs_settings = $args;

		//save attribute type
		if ( $type ) {
			global $wpdb;
			$wpdb->update( "{$wpdb->prefix}woocommerce_attribute_taxonomies", array( 'attribute_type' => $type ), array( 'attribute_name' => $slug ), array( '%s' ), array( '%s' ) );
			// Clear cache and flush rewrite rules.
			wp_schedule_single_event( time(), 'woocommerce_flush_rewrite_rules' );
			delete_transient( 'wc_attribute_taxonomies' );
			WC_Cache_Helper::invalidate_cache_group( 'woocommerce-attributes' );
		}
		//save term
		if ( is_array( $term_data ) && count( $term_data ) ) {
			foreach ( $term_data as $term_id => $term_settings ) {
				$term_settings = wp_parse_args( $term_settings, get_term_meta( $term_id, 'vi_wpvs_terms_params', true ) );
				update_term_meta( $term_id, 'vi_wpvs_terms_params', $term_settings );
			}
		}
		$response['status'] = $response['message'] ? 'failed' : 'successfully';
		wp_send_json( $response );
	}

	public function admin_menu() {

		$import_list = add_submenu_page(
			'woocommerce-product-variations-swatches',
			esc_html__( 'Swatches Settings for Global Attributes', 'product-variations-swatches-for-woocommerce' ),
			esc_html__( 'Global Attributes', 'product-variations-swatches-for-woocommerce' ),
			'manage_options',
			'woocommerce-product-variations-swatches-global-attrs',
			array( $this, 'settings_callback' )
		);
		add_action( "load-$import_list", array( $this, 'screen_options_page' ) );
	}

	/**
	 * @param $status
	 * @param $option
	 * @param $value
	 *
	 * @return mixed
	 */
	public function save_screen_options( $status, $option, $value ) {
		if ( 'vi_wvps_per_page' == $option ) {
			return $value;
		}

		return $status;
	}

	/**
	 * Add Screen Options
	 */
	public function screen_options_page() {
		$option = 'per_page';
		$args   = array(
			'label'   => esc_html__( 'Number of items per page', 'product-variations-swatches-for-woocommerce' ),
			'default' => 5,
			'option'  => 'vi_wvps_attrs_per_page'
		);

		add_screen_option( $option, $args );
	}

	public function get_pagination_html( $page, $keyword, $paged, $p_paged, $n_paged, $total_page ) {
		?>
        <form method="get">
            <input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>">
            <div class="tablenav top">
                <div class="vi-wvps-attrs-save">
                    <span class="vi-ui button primary vi-wvps-attrs-save-button vi-wvps-attrs-save-all-button"
                          title="<?php esc_attr_e( 'Save all', 'product-variations-swatches-for-woocommerce' ) ?>">
                        <?php esc_html_e( 'Save All', 'product-variations-swatches-for-woocommerce' ) ?>
                    </span>
                </div>
                <div class="tablenav-pages">
                    <div class="pagination-links">
						<?php
						if ( $paged > 2 ) {
							?>
                            <a class="prev-page button" href="<?php echo esc_url( add_query_arg(
								array(
									'page'           => $page,
									'paged'          => 1,
									'vi_wvps_search' => $keyword,
								), admin_url( 'admin.php' )
							) ) ?>"><span
                                        class="screen-reader-text"><?php esc_html_e( 'First Page', 'product-variations-swatches-for-woocommerce' ) ?></span><span
                                        aria-hidden="true">«</span></a>
							<?php
						} else {
							?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">«</span>
							<?php
						}
						/*Previous button*/
						if ( $p_paged ) {
							$p_url = add_query_arg(
								array(
									'page'           => $page,
									'paged'          => $p_paged,
									'vi_wvps_search' => $keyword,
								), admin_url( 'admin.php' )
							);
							?>
                            <a class="prev-page button" href="<?php echo esc_url( $p_url ) ?>"><span
                                        class="screen-reader-text"><?php esc_html_e( 'Previous Page', 'product-variations-swatches-for-woocommerce' ) ?></span><span
                                        aria-hidden="true">‹</span></a>
							<?php
						} else {
							?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">‹</span>
							<?php
						}
						?>
                        <span class="screen-reader-text"><?php esc_html_e( 'Current Page', 'product-variations-swatches-for-woocommerce' ) ?></span>
                        <span id="table-paging" class="paging-input">
                            <input class="current-page" type="text" name="paged" size="1"
                                   value="<?php echo esc_attr( $paged ) ?>">
                            <span class="tablenav-paging-text">
                                <?php esc_html_e( 'of', 'product-variations-swatches-for-woocommerce' ) ?>
                                 <span class="total-pages"><?php echo esc_html( $total_page ) ?></span>
                            </span>
                        </span>
						<?php
						/*Next button*/
						if ( $n_paged ) {
							$n_url = add_query_arg(
								array(
									'page'           => $page,
									'paged'          => $n_paged,
									'vi_wvps_search' => $keyword,
								), admin_url( 'admin.php' )
							); ?>
                            <a class="next-page button" href="<?php echo esc_url( $n_url ) ?>"><span
                                        class="screen-reader-text"><?php esc_html_e( 'Next Page', 'product-variations-swatches-for-woocommerce' ) ?></span><span
                                        aria-hidden="true">›</span></a>
							<?php
						} else {
							?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">›</span>
							<?php
						}
						if ( $total_page > $paged + 1 ) {
							?>
                            <a class="next-page button" href="<?php echo esc_url( add_query_arg(
								array(
									'page'           => $page,
									'paged'          => $total_page,
									'vi_wvps_search' => $keyword,
								), admin_url( 'admin.php' )
							) ) ?>"><span
                                        class="screen-reader-text"><?php esc_html_e( 'Last Page', 'product-variations-swatches-for-woocommerce' ) ?></span><span
                                        aria-hidden="true">»</span></a>
							<?php
						} else {
							?>
                            <span class="tablenav-pages-navspan button disabled" aria-hidden="true">»</span>
							<?php
						}
						?>
                    </div>
                </div>
                <p class="search-box">
                    <input type="text" class="text short" name="vi_wvps_search"
                           value="<?php echo esc_attr( $keyword ) ?>">
                    <input type="submit" name="submit" class="button"
                           value="<?php esc_attr_e( 'Search attribute', 'product-variations-swatches-for-woocommerce' ) ?>">
                </p>
            </div>
        </form>
		<?php
	}

	public function settings_callback() {
		$user     = get_current_user_id();
		$screen   = get_current_screen();
		$option   = $screen->get_option( 'per_page', 'option' );
		$per_page = get_user_meta( $user, $option, true );
		if ( empty ( $per_page ) || $per_page < 1 ) {
			$per_page = $screen->get_option( 'per_page', 'default' );
		}
		$paged                = isset( $_GET['paged'] ) ? sanitize_text_field( $_GET['paged'] ) : 1;
		$keyword              = isset( $_GET['vi_wvps_search'] ) ? strtolower( sanitize_text_field( $_GET['vi_wvps_search'] ) ) : '';
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		?>
        <div class="wrap<?php echo is_rtl() ? esc_attr( ' vi-wpvs-wrap-rtl' ) : ''; ?>">
            <h2><?php esc_html_e( 'Swatches Settings for Global Attributes', 'product-variations-swatches-for-woocommerce' ) ?></h2>
            <div class="vi-ui blue message">
				<?php esc_html_e( 'This page allows you to customize all WooCommerce global attributes rapidly', 'product-variations-swatches-for-woocommerce' ); ?>
            </div>
			<?php
			if ( $attribute_taxonomies ) {
				if ( $keyword ) {
					$attribute_taxonomies_t = array();
					foreach ( $attribute_taxonomies as $attr ) {
						$check = strtolower( $attr->attribute_label);
						if ( strlen( strstr( $check, $keyword ) ) ) {
							$attribute_taxonomies_t[] = $attr;
						}
					}
				} else {
					$attribute_taxonomies_t = $attribute_taxonomies;
				}
				$count_taxonomies = ! empty( $attribute_taxonomies_t ) ? count( $attribute_taxonomies_t ) : 1;
				$total_page       = ceil( $count_taxonomies / $per_page );
				/*Previous page*/
				$p_paged = $per_page * $paged > $per_page ? $paged - 1 : 0;
				/* next page */
				$n_paged = $per_page * $paged < $count_taxonomies ? $paged + 1 : 0;
				ob_start();
				$this->get_pagination_html( 'woocommerce-product-variations-swatches-global-attrs', $keyword, $paged, $p_paged, $n_paged, $total_page );
				$pagination_html = ob_get_clean();
				echo wp_kses( $pagination_html, VI_WOO_PRODUCT_VARIATIONS_SWATCHES_DATA::extend_post_allowed_html() );
				wp_nonce_field( 'vi_wvps_global_attrs_action', '_vi_wvps_global_attrs_nonce' );
				$global_attrs              = array_slice( $attribute_taxonomies_t, $p_paged * $per_page, $paged * $per_page );
				$vi_wpvs_ids               = $this->settings->get_params( 'ids' );
				$vi_wpvs_names             = $this->settings->get_params( 'names' );
				$vi_attribute_profiles     = $this->settings->get_params( 'taxonomy_profiles' );
				$vi_attribute_display_type = $this->settings->get_params( 'taxonomy_display_type' );
				$attribute_types           = wc_get_attribute_types();
				$vi_default_colors         = $this->settings->get_default_color();
				echo sprintf( '<form  class="vi-ui form" method="post" >' );
				foreach ( $global_attrs as $attribute ) {
					$attribute_name         = wc_attribute_taxonomy_name( $attribute->attribute_name );
					$attribute_profile      =  $vi_attribute_profiles[ $attribute_name ] ?? '';
					$attribute_display_type =  $vi_attribute_display_type[ $attribute_name ] ?? 'vertical';
					?>
                    <div class="vi-ui styled fluid accordion active vi-wpvs-accordion-wrap vi-wpvs-accordion-attr-wrap vi-wpvs-accordion-attr-wrap-<?php echo esc_attr( $attribute_name ); ?>"
                         data-attribute_name="<?php echo esc_attr( $attribute_name ); ?>"
                         data-attribute_id="<?php echo esc_attr( $attribute->attribute_id ); ?>">
                        <div class="vi-wpvs-accordion-info-wrap">
                            <div class="vi-wpvs-accordion-name">
								<?php echo esc_html( $attribute->attribute_label ); ?>
                            </div>
                            <div class="vi-wpvs-accordion-action">
                                <div class="vi-wvps-attrs-save">
									<span class="vi-ui mini button primary vi-wvps-attrs-save-button vi-wvps-attr-taxonomy-save-button">
										<?php esc_html_e( 'Save', 'product-variations-swatches-for-woocommerce' ); ?>
									</span>
                                </div>
                            </div>
                        </div>
                        <div class="title active">
                            <i class="dropdown icon"></i>
							<?php esc_html_e( 'Default design', 'product-variations-swatches-for-woocommerce' ); ?>
                        </div>
                        <div class="content active">
                            <input type="hidden" name="taxonomy_slug" value="<?php echo esc_attr( $attribute->attribute_name ); ?>">
                            <div class="equal width fields">
                                <div class="field">
                                    <label>
										<?php esc_html_e( 'Show in product list', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                    <a class="vi-ui button" href="https://1.envato.market/bd0ek"
                                       target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
                                </div>
                                <div class="field">
                                    <label>
										<?php esc_html_e( 'Display style', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                    <select name="taxonomy_display_type"
                                            class="vi-ui fluid dropdown vi-wpvs-accordion-taxonomy_display_type">
                                        <option value="vertical" <?php selected( $attribute_display_type, 'vertical' ) ?> >
											<?php esc_html_e( 'Vertical', 'product-variations-swatches-for-woocommerce' ); ?>
                                        </option>
                                        <option value="horizontal" <?php selected( $attribute_display_type, 'horizontal' ) ?> >
											<?php esc_html_e( 'Horizontal', 'product-variations-swatches-for-woocommerce' ); ?>
                                        </option>
                                    </select>
                                </div>
                                <div class="field">
                                    <label>
										<?php esc_html_e( 'Display type', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                    <select name="taxonomy_type"
                                            class="vi-ui fluid dropdown vi-wpvs-accordion-taxonomy_type">
										<?php
										foreach ( $attribute_types as $k => $v ) {
											?>
                                            <option value="<?php echo esc_attr( $k ); ?>" <?php selected( $attribute->attribute_type, $k ) ?>><?php echo esc_html( $v ); ?></option>
											<?php
										}
										?>
                                    </select>
                                </div>
                                <div class="field">
                                    <label for="">
										<?php esc_html_e( 'Swatches profile', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                    <select name="taxonomy_profile"
                                            class="vi-ui fluid dropdown vi-wpvs-accordion-taxonomy_profile">
										<?php
										foreach ( $vi_wpvs_ids as $k => $id ) {
											?>
                                            <option value="<?php echo esc_attr( $id ) ?>" <?php selected( $attribute_profile,
												$id ) ?>><?php echo esc_html( $vi_wpvs_names[ $k ] ); ?></option>
											<?php
										}
										?>
                                    </select>
                                </div>
                            </div>
							<?php
							if ( taxonomy_exists( $attribute_name ) ) {
								$terms = get_terms( $attribute_name, 'hide_empty=0' );
								if ( $terms ) {
									$placeholder_img_src = wc_placeholder_img_src( 'woocommerce_gallery_thumbnail' );
									$taxonomy_term_class = in_array( $attribute->attribute_type, array(
										'color',
										'image'
									) ) ? 'vi-wpvs-accordion-term-wrap-wrap' : 'vi-wpvs-accordion-term-wrap-wrap vi-wpvs-accordion-term-wrap-close';
									?>
                                    <div class="<?php echo esc_attr( $taxonomy_term_class ) ?>">
										<?php
										foreach ( $terms as $term ) {
											$vi_wpvs_terms_settings = get_term_meta( $term->term_id, 'vi_wpvs_terms_params', true );
											$term_class             = 'vi-ui styled fluid accordion vi-wpvs-accordion-wrap vi-wpvs-accordion-term-wrap vi-wpvs-accordion-term-wrap-' . $term->term_id;
											$terms_color_separator  = isset( $vi_wpvs_terms_settings['color_separator'] ) ? $vi_wpvs_terms_settings['color_separator'] : '1';
											$terms_colors           = isset( $vi_wpvs_terms_settings['color'] ) ? $vi_wpvs_terms_settings['color'] : array();
											$terms_img_id           = isset( $vi_wpvs_terms_settings['img_id'] ) ? $vi_wpvs_terms_settings['img_id'] : '';
											$terms_img_src          = $terms_img_id ? wp_get_attachment_image_url( $terms_img_id, 'woocommerce_thumbnail', true ) : $placeholder_img_src;
											$i_class                = 'dropdown icon';
											$i_class                .= in_array( $attribute->attribute_type, array(
												'color',
												'image'
											) ) ? '' : ' vi-wpvs-hidden';
											?>
                                            <div class="<?php echo esc_attr( $term_class ); ?>"
                                                 data-term_id="<?php echo esc_attr( $term->term_id ); ?>">
                                                <div class="title">
                                                    <i class="<?php echo esc_attr( $i_class ); ?>"></i>
                                                    <span class="vi-wpvs-attribute-value-name">
                                                    <?php echo esc_html( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) ) ?>
                                                    </span>
                                                </div>
                                                <div class="content">
                                                    <input type="hidden" name="term_id"
                                                           value="<?php echo esc_attr( $term->term_id ); ?>">
                                                    <div class="field">
                                                        <div class="vi-wpvs-attribute-value-content-wrap vi-wpvs-attribute-value-content-color-wrap <?php echo $attribute->attribute_type === 'color' ? '' : esc_attr( 'vi-wpvs-hidden' ); ?>">
                                                            <table class="form-table">
                                                                <tbody>
                                                                <tr>
                                                                    <td>
																		<?php esc_html_e( 'Color separator', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                    </td>
                                                                    <td>
                                                                        <select name="vi_attribute_color_separator"
                                                                                id="vi_attribute_color_separator_<?php echo esc_attr( $term->term_id ); ?>"
                                                                                class="vi-ui fluid dropdown vi_attribute_color_separator">
                                                                            <option value="1" <?php selected( $terms_color_separator, '1' ) ?>>
																				<?php esc_html_e( 'Basic horizontal', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                            </option>
                                                                            <option value="2" <?php selected( $terms_color_separator, '2' ) ?>>
																				<?php esc_html_e( 'Basic vertical', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                            </option>
                                                                            <option value="3" <?php selected( $terms_color_separator, '3' ) ?>>
																				<?php esc_html_e( 'Basic diagonal left', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                            </option>
                                                                            <option value="4" <?php selected( $terms_color_separator, '4' ) ?>>
																				<?php esc_html_e( 'Basic diagonal right', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                            </option>
                                                                            <option value="5" <?php selected( $terms_color_separator, '5' ) ?>>
																				<?php esc_html_e( 'Hard lines horizontal', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                            </option>
                                                                            <option value="6" <?php selected( $terms_color_separator, '6' ) ?>>
																				<?php esc_html_e( 'Hard lines vertical', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                            </option>
                                                                            <option value="7" <?php selected( $terms_color_separator, '7' ) ?>>
																				<?php esc_html_e( 'Hard lines diagonal left', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                            </option>
                                                                            <option value="8" <?php selected( $terms_color_separator, '8' ) ?>>
																				<?php esc_html_e( 'Hard lines diagonal right', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                            </option>
                                                                        </select>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
																		<?php esc_html_e( 'Color', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                    </td>
                                                                    <td>
                                                                        <table class="form-table vi-wpvs-table vi-wpvs-attribute-value-content-color-table">
                                                                            <tr class="vi-wpvs-table-head">
                                                                                <th><?php esc_html_e( 'Color', 'product-variations-swatches-for-woocommerce' ); ?></th>
                                                                                <th><?php esc_html_e( 'Action', 'product-variations-swatches-for-woocommerce' ); ?></th>
                                                                            </tr>
																			<?php
																			if ( $terms_colors && is_array( $terms_colors ) && count( $terms_colors ) ) {
																				foreach ( $terms_colors as $terms_color ) {
																					?>
                                                                                    <tr>
                                                                                        <td>
                                                                                            <input type="text"
                                                                                                   class="vi-wpvs-color vi_attribute_colors"
                                                                                                   name="vi_attribute_colors[]"
                                                                                                   value="<?php echo esc_attr( $terms_color ) ?>">
                                                                                        </td>
                                                                                        <td>
                                                                                <span class="vi-wpvs-term-color-action-clone vi-ui positive button">
                                                                                    <?php esc_html_e( 'Clone', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                                </span>
                                                                                            <span class="vi-wpvs-term-color-action-remove vi-ui negative button">
                                                                                    <?php esc_html_e( 'Remove', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                                </span>
                                                                                        </td>
                                                                                    </tr>
																					<?php
																				}
																			} else {
																				$terms_color = isset( $vi_default_colors[ strtolower( $term->name ) ] ) ? $vi_default_colors[ strtolower( $term->name ) ] : '';
																				?>
                                                                                <tr>
                                                                                    <td>
                                                                                        <input type="text"
                                                                                               class="vi-wpvs-color vi_attribute_colors"
                                                                                               name="vi_attribute_colors[]"
                                                                                               value="<?php echo esc_attr( $terms_color ) ?>">
                                                                                    </td>
                                                                                    <td>
                                                                            <span class="vi-wpvs-term-color-action-clone vi-ui positive button">
                                                                                <?php esc_html_e( 'Clone', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                            </span>
                                                                                        <span class="vi-wpvs-term-color-action-remove vi-ui negative button">
                                                                                <?php esc_html_e( 'Remove', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                            </span>
                                                                                    </td>
                                                                                </tr>
																				<?php
																			}
																			?>
                                                                        </table>
                                                                    </td>
                                                                </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="field">
                                                        <div class="vi-wpvs-attribute-value-content-wrap vi-wpvs-attribute-value-content-image-wrap <?php echo $attribute->attribute_type === 'image' ? '' : esc_attr( 'vi-wpvs-hidden' ); ?>">
                                                            <table class="form-table">
                                                                <tr>
                                                                    <td>
																		<?php esc_html_e( 'Image', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                    </td>
                                                                    <td>
                                                                        <input type="hidden"
                                                                               name="vi_attribute_images"
                                                                               class="vi_attribute_image"
                                                                               value="<?php echo esc_attr( $terms_img_id ); ?>">
                                                                        <div class="vi-attribute-image-wrap vi-attribute-edit-image-wrap vi-wpvs-term-image-upload-img">
                                                                <span class="vi-attribute-edit-image-preview vi-attribute-image-preview">
                                                                                <img src="<?php echo esc_attr( esc_url( $terms_img_src ) ); ?>" data-src_placeholder="<?php echo esc_attr( $placeholder_img_src ); ?>">
                                                                </span>
                                                                            <i class="vi-attribute-image-remove times circle outline icon<?php echo $terms_img_id ? '': esc_attr(' vi-wpvs-hidden'); ?>"></i>
                                                                            <div class="vi-attribute-image-add-new"><?php esc_html_e( 'Upload / Add image', 'product-variations-swatches-for-woocommerce' ); ?></div>
                                                                        </div>
                                                                        <p class="description">
																			<?php esc_html_e( 'Choose an image', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                        </p>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
											<?php
										}
										?>
                                    </div>
									<?php
								}
							}
							?>
                        </div>
                        <div class="title">
                            <i class="dropdown icon"></i>
							<?php esc_html_e( 'Design with Product category', 'product-variations-swatches-for-woocommerce' ); ?>
                        </div>
                        <div class="content">
                            <div class="field">
                                <table class="form-table vi-wpvs-table">
                                    <thead>
                                    <tr>
                                        <td>
                                            <label><?php esc_html_e( 'Product category', 'product-variations-swatches-for-woocommerce' ); ?></label>
                                        </td>
                                        <td>
                                            <label><?php esc_html_e( 'Display type', 'product-variations-swatches-for-woocommerce' ); ?></label>
                                        </td>
                                        <td>
                                            <label><?php esc_html_e( 'Swatches profile', 'product-variations-swatches-for-woocommerce' ); ?></label>
                                        </td>
                                        <td>
                                            <label><?php esc_html_e( 'Show in product list', 'product-variations-swatches-for-woocommerce' ); ?></label>
                                        </td>
                                        <td>
                                            <label><?php esc_html_e( 'Display style', 'product-variations-swatches-for-woocommerce' ); ?></label>
                                        </td>
                                        <td>
                                            <label><?php esc_html_e( 'Action', 'product-variations-swatches-for-woocommerce' ); ?></label>
                                        </td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td colspan="6">
                                            <a class="vi-ui button" href="https://1.envato.market/bd0ek" target="_blank">
												<?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?>
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
					<?php
				}
				echo sprintf( '</form>' );
				echo wp_kses( $pagination_html, VI_WOO_PRODUCT_VARIATIONS_SWATCHES_DATA::extend_post_allowed_html() );
			} else {
				?>
                <div class="vi-ui orange message">
					<?php esc_html_e( 'No attributes currently exist.', 'product-variations-swatches-for-woocommerce' ) ?>
                </div>
				<?php
			}
			?>
            <div class="vi-wvps-save-sucessful-popup">
				<?php esc_html_e( 'Settings saved', 'sales-countdown-timer' ); ?>
            </div>
        </div>
		<?php
	}

	public function admin_enqueue_scripts() {
		$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : '';
		if ( $page === 'woocommerce-product-variations-swatches-global-attrs' ) {
			global $wp_scripts;
			if ( isset( $wp_scripts->registered['jquery-ui-accordion'] ) ) {
				unset( $wp_scripts->registered['jquery-ui-accordion'] );
				wp_dequeue_script( 'jquery-ui-accordion' );
			}
			if ( isset( $wp_scripts->registered['accordion'] ) ) {
				unset( $wp_scripts->registered['accordion'] );
				wp_dequeue_script( 'accordion' );
			}
			$scripts = $wp_scripts->registered;
			foreach ( $scripts as $k => $script ) {
				preg_match( '/^\/wp-/i', $script->src, $result );
				if ( count( array_filter( $result ) ) ) {
					preg_match( '/^(\/wp-content\/plugins|\/wp-content\/themes)/i', $script->src, $result1 );
					if ( count( array_filter( $result1 ) ) ) {
						wp_dequeue_script( $script->handle );
					}
				} else {
					if ( $script->handle != 'query-monitor' ) {
						wp_dequeue_script( $script->handle );
					}
				}
			}
			/*Stylesheet*/
			wp_enqueue_style( 'semantic-ui-accordion', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'accordion.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-button', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'button.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-checkbox', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'checkbox.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-dropdown', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'dropdown.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-form', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'form.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-header', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'header.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-icon', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'icon.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-input', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'input.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-label', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'label.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-message', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'message.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-popup', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'popup.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-segment', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'segment.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'transition', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'transition.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );

			wp_enqueue_style( 'product-variations-swatches-for-woocommerce-admin-attrs-attrs-css', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'admin-setting-attrs.css', array(), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'product-variations-swatches-for-woocommerce-admin-minicolors', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'minicolors.css', array(), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );


			wp_enqueue_media();
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'semantic-ui-accordion', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'accordion.min.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'semantic-ui-address', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'address.min.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'semantic-ui-checkbox', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'checkbox.min.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'semantic-ui-dropdown', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'dropdown.min.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'semantic-ui-form', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'form.min.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'transition', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'transition.min.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );

			wp_enqueue_script( 'product-variations-swatches-for-woocommerce-admin-attrs-js', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'admin-setting-attrs.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'product-variations-swatches-for-woocommerce-admin-minicolors', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'minicolors.min.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			$args = array(
				'ajax_url'               => admin_url( 'admin-ajax.php' ),
				'settings_default_color' => $this->settings->get_default_color(),
				'remove_item'            => esc_html__( 'Would you want to remove this?', 'product-variations-swatches-for-woocommerce' ),
				'remove_last_item'       => esc_html__( 'You can not remove the last item.', 'product-variations-swatches-for-woocommerce' ),
				'save_all_confirm'       => esc_html__( 'Save all settings of the attribute taxonomies on this page?', 'product-variations-swatches-for-woocommerce' ),
				'not_found_error'        => esc_html__( 'No taxonomy found.', 'product-variations-swatches-for-woocommerce' ),
			);
			wp_localize_script( 'product-variations-swatches-for-woocommerce-admin-attrs-js', 'vi_woo_product_variation_swatches_admin_attrs_js', $args );
		}
	}
}