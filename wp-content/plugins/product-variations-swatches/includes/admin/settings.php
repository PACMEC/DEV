<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WOO_PRODUCT_VARIATIONS_SWATCHES_Admin_Settings {
	protected $settings;
	protected $error;

	function __construct() {
		$this->settings = new VI_WOO_PRODUCT_VARIATIONS_SWATCHES_DATA();
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 10 );
		add_action( 'admin_init', array( $this, 'save_settings' ), 100 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), PHP_INT_MAX );
	}

	public function admin_menu() {
		add_menu_page(
			esc_html__( 'Variation Swatches', 'product-variations-swatches-for-woocommerce' ),
			esc_html__( 'Variation Swatches', 'product-variations-swatches-for-woocommerce' ),
			'manage_options',
			'woocommerce-product-variations-swatches',
			array( $this, 'settings_callback' ),
			'dashicons-image-filter',
			2 );
		add_submenu_page(
			'woocommerce-product-variations-swatches',
			esc_html__( 'Variation Swatches', 'product-variations-swatches-for-woocommerce' ),
			esc_html__( 'Variation Swatches', 'product-variations-swatches-for-woocommerce' ),
			'manage_options',
			'woocommerce-product-variations-swatches',
			array( $this, 'settings_callback' )
		);
	}

	public function settings_callback() {
		$this->settings                  = new VI_WOO_PRODUCT_VARIATIONS_SWATCHES_DATA();
		$out_of_stock_variation_disable  = $this->settings->get_params( 'out_of_stock_variation_disable' );
		$attribute_display_default       = $this->settings->get_params( 'attribute_display_default' );
		$attribute_double_click          = $this->settings->get_params( 'attribute_double_click' );
		$attribute_profile_default       = $this->settings->get_params( 'attribute_profile_default' );
		$variation_threshold_single_page = $this->settings->get_params( 'variation_threshold_single_page' );
		$single_attr_title               = $this->settings->get_params( 'single_attr_title' );
		$single_attr_selected            = $this->settings->get_params( 'single_attr_selected' );
		$ids                             = $this->settings->get_params( 'ids' );
		$count_ids                       = is_array( $ids ) ? count( $ids ) : 0;
		$count_ids                       = $count_ids > 3 ? 3 : $count_ids;
		$attribute_profile_default       = $attribute_profile_default ?: $ids[0];
		?>
        <div class="wrap">
            <h2 class=""><?php esc_html_e( 'Product Variations Swatches for WooCommerce', 'product-variations-swatches-for-woocommerce' ) ?></h2>
            <div id="vi-wpvs-message" class="error <?php echo $this->error ? '' : esc_attr( 'hidden' ); ?>">
                <p><?php echo esc_html( $this->error ); ?></p>
            </div>
            <div class="vi-ui raised">
                <form action="" class="vi-ui form" method="post" enctype="multipart/form-data">
					<?php
					wp_nonce_field( '_vi_woo_product_variation_swatches_settings_action', '_vi_woo_product_variation_swatches_settings' );
					?>
                    <div class="vi-ui vi-ui-main top tabular attached menu">
                        <a class="item"
                           data-tab="general"><?php esc_html_e( 'General Settings', 'product-variations-swatches-for-woocommerce' ); ?></a>
                        <a class="item active"
                           data-tab="swatches_profile"><?php esc_html_e( 'Swatches Profile', 'product-variations-swatches-for-woocommerce' ); ?></a>
                        <a class="item"
                           data-tab="single_page"><?php esc_html_e( 'Swatches on Single page', 'product-variations-swatches-for-woocommerce' ); ?></a>
                        <a class="item"
                           data-tab="product_list"><?php esc_html_e( 'Swatches on Product List', 'product-variations-swatches-for-woocommerce' ); ?></a>
                        <a class="item"
                           data-tab="custom_attrs"><?php esc_html_e( 'Custom Attributes', 'product-variations-swatches-for-woocommerce' ); ?></a>
                    </div>
                    <div class="vi-ui bottom attached tab segment" data-tab="general">
                        <table class="form-table">
                            <tbody>
                            <tr valign="top">
                                <th>
                                    <label for="vi-wpvs-attribute_display_default">
										<?php esc_html_e( 'Default display type', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <select name="attribute_display_default" id="vi-wpvs-attribute_display_default"
                                            class="vi-ui fluid dropdown vi-wpvs-attribute_display_default">
                                        <option value="none" <?php selected( $attribute_display_default, 'none' ) ?>>
											<?php esc_html_e( 'No change', 'product-variations-swatches-for-woocommerce' ); ?>
                                        </option>
                                        <option value="button" <?php selected( $attribute_display_default, 'button' ) ?>>
											<?php esc_html_e( 'Button', 'product-variations-swatches-for-woocommerce' ); ?>
                                        </option>
                                        <option value="radio" <?php selected( $attribute_display_default, 'radio' ) ?>>
											<?php esc_html_e( 'Radio', 'product-variations-swatches-for-woocommerce' ); ?>
                                        </option>
                                    </select>
                                    <p class="description">
										<?php esc_html_e( 'This is used if an attribute is not config yet or no rules are applied', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="vi-wpvs-custom_css">
										<?php esc_html_e( 'Custom css', 'product-variations-swatches-for-woocommerce' ) ?>
                                    </label>
                                </th>
                                <td>
                                    <a class="vi-ui button" href="https://1.envato.market/bd0ek"
                                       target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="vi-ui bottom attached tab segment active" data-tab="swatches_profile">
                        <div class="vi-ui blue message">
							<?php esc_html_e( 'The settings allow to design variation swatches', 'product-variations-swatches-for-woocommerce' ); ?>
                        </div>
						<?php
						if ( $count_ids ) {
							for ( $i = 0; $i < $count_ids; $i ++ ) {
								$name                         = $this->settings->get_current_setting( 'names', $i );
								$attribute_reduce_size_mobile = $this->settings->get_current_setting( 'attribute_reduce_size_mobile', $i );
								$attribute_height             = $this->settings->get_current_setting( 'attribute_height', $i );
								$attribute_width              = $this->settings->get_current_setting( 'attribute_width', $i );
								$attribute_fontsize           = $this->settings->get_current_setting( 'attribute_fontsize', $i );
								$attribute_padding            = $this->settings->get_current_setting( 'attribute_padding', $i );
								$attribute_transition         = $this->settings->get_current_setting( 'attribute_transition', $i );

								$attribute_default_box_shadow_color = $this->settings->get_current_setting( 'attribute_default_box_shadow_color', $i );
								$attribute_default_color            = $this->settings->get_current_setting( 'attribute_default_color', $i );
								$attribute_default_bg_color         = $this->settings->get_current_setting( 'attribute_default_bg_color', $i );
								$attribute_default_border_color     = $this->settings->get_current_setting( 'attribute_default_border_color', $i );
								$attribute_default_border_radius    = $this->settings->get_current_setting( 'attribute_default_border_radius', $i );
								$attribute_default_border_width     = $this->settings->get_current_setting( 'attribute_default_border_width', $i );

								$attribute_out_of_stock = $this->settings->get_current_setting( 'attribute_out_of_stock', $i );

								$attribute_hover_scale            = $this->settings->get_current_setting( 'attribute_hover_scale', $i );
								$attribute_hover_box_shadow_color = $this->settings->get_current_setting( 'attribute_hover_box_shadow_color', $i );
								$attribute_hover_color            = $this->settings->get_current_setting( 'attribute_hover_color', $i );
								$attribute_hover_bg_color         = $this->settings->get_current_setting( 'attribute_hover_bg_color', $i );
								$attribute_hover_border_color     = $this->settings->get_current_setting( 'attribute_hover_border_color', $i );
								$attribute_hover_border_radius    = $this->settings->get_current_setting( 'attribute_hover_border_radius', $i );
								$attribute_hover_border_width     = $this->settings->get_current_setting( 'attribute_hover_border_width', $i );

								$attribute_selected_scale            = $this->settings->get_current_setting( 'attribute_selected_scale', $i );
								$attribute_selected_box_shadow_color = $this->settings->get_current_setting( 'attribute_selected_box_shadow_color', $i );
								$attribute_selected_color            = $this->settings->get_current_setting( 'attribute_selected_color', $i );
								$attribute_selected_bg_color         = $this->settings->get_current_setting( 'attribute_selected_bg_color', $i );
								$attribute_selected_border_color     = $this->settings->get_current_setting( 'attribute_selected_border_color', $i );
								$attribute_selected_border_radius    = $this->settings->get_current_setting( 'attribute_selected_border_radius', $i );
								$attribute_selected_border_width     = $this->settings->get_current_setting( 'attribute_selected_border_width', $i );

								$attribute_tooltip_enable        = $this->settings->get_current_setting( 'attribute_tooltip_enable', $i );
								$attribute_tooltip_position      = $this->settings->get_current_setting( 'attribute_tooltip_position', $i );
								$attribute_tooltip_border_radius = $this->settings->get_current_setting( 'attribute_tooltip_border_radius', $i );
								$attribute_tooltip_fontsize      = $this->settings->get_current_setting( 'attribute_tooltip_fontsize', $i );
								$attribute_tooltip_color         = $this->settings->get_current_setting( 'attribute_tooltip_color', $i );
								$attribute_tooltip_bg_color      = $this->settings->get_current_setting( 'attribute_tooltip_bg_color', $i );
								$attribute_tooltip_border_color  = $this->settings->get_current_setting( 'attribute_tooltip_border_color', $i );
								?>
                                <div class="vi-ui styled fluid accordion vi-wpvs-accordion-wrap vi-wpvs-accordion-wrap-<?php echo esc_attr( $i ); ?>"
                                     data-accordion_id="<?php echo esc_attr( $i ); ?>">
                                    <div class="woo-sctr-accordion-info">
                                        <div class="vi-ui toggle checkbox checked"
                                             data-tooltip="<?php esc_attr_e( 'Default profile', 'product-variations-swatches-for-woocommerce' ); ?>">
                                            <input type="radio" name="attribute_profile_default"
                                                   id="vi-wpvs-attribute_profile_default-<?php echo esc_attr( $ids[ $i ] ); ?>"
                                                   class="vi-wpvs-attribute_profile_default"
                                                   value="<?php echo esc_attr( $ids[ $i ] ); ?>" <?php checked( $attribute_profile_default, $ids[ $i ] ) ?>>
                                            <label for="vi-wpvs-attribute_profile_default-<?php echo esc_attr( $ids[ $i ] ); ?>"></label>
                                        </div>
                                        <span>
						                    <h4><span class="vi-wpvs-accordion-name"><?php echo esc_html( $name ); ?></span></h4>
					                    </span>
                                    </div>
                                    <div class="title">
                                        <i class="dropdown icon"></i>
										<?php esc_html_e( 'Default styling', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </div>
                                    <div class="content">
                                        <div class="field">
                                            <label for=""><?php esc_html_e( 'Name', 'product-variations-swatches-for-woocommerce' ); ?></label>
                                            <input type="hidden" name="ids[]" class="vi-wpvs-ids"
                                                   value="<?php echo esc_attr( $ids[ $i ] ); ?>">
                                            <input type="text" name="names[]" class="vi-wpvs-names"
                                                   value="<?php echo esc_attr( $name ); ?>">
                                        </div>
                                        <div class="equal width fields">
                                            <div class="field">
                                                <label><?php esc_html_e( 'Transition Duration', 'product-variations-swatches-for-woocommerce' ); ?></label>
                                                <div class="vi-ui right labeled fluid input">
                                                    <input type="number"
                                                           class="vi-wpvs-attribute_transition"
                                                           name="attribute_transition[]"
                                                           min="0"
                                                           max="1000"
                                                           value="<?php echo esc_attr( $attribute_transition ) ?>">
                                                    <div class="vi-ui label vi-wpvs-right-input-label">
														<?php esc_html_e( 'Millisecond', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="field vi-wpvs-field-min-width">
                                                <label><?php esc_html_e( 'Padding', 'product-variations-swatches-for-woocommerce' ); ?></label>
                                                <input type="text" class="vi-wpvs-attribute_padding"
                                                       name="attribute_padding[]"
                                                       placeholder="<?php esc_attr_e( 'eg: 3px 5px', 'product-variations-swatches-for-woocommerce' ); ?>"
                                                       value="<?php echo esc_attr( $attribute_padding ) ?>">
                                            </div>
                                            <div class="field vi-wpvs-field-max-width">
                                                <label><?php esc_html_e( 'Height', 'product-variations-swatches-for-woocommerce' ); ?></label>
                                                <div class="vi-ui right labeled fluid input">
                                                    <input type="number"
                                                           class="vi-wpvs-attribute_height"
                                                           name="attribute_height[]"
                                                           min="0"
                                                           value="<?php echo esc_attr( $attribute_height ) ?>">
                                                    <div class="vi-ui label vi-wpvs-right-input-label">
														<?php esc_html_e( 'Px', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="field vi-wpvs-field-max-width">
                                                <label><?php esc_html_e( 'Width', 'product-variations-swatches-for-woocommerce' ); ?></label>
                                                <div class="vi-ui right labeled fluid input">
                                                    <input type="number"
                                                           class="vi-wpvs-attribute_width"
                                                           name="attribute_width[]"
                                                           min="0"
                                                           value="<?php echo esc_attr( $attribute_width ) ?>">
                                                    <div class="vi-ui label vi-wpvs-right-input-label">
														<?php esc_html_e( 'Px', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="field vi-wpvs-field-max-width">
                                                <label><?php esc_html_e( 'Font size', 'product-variations-swatches-for-woocommerce' ); ?></label>
                                                <div class="vi-ui right labeled fluid input">
                                                    <input type="number"
                                                           class="vi-wpvs-attribute_fontsize"
                                                           name="attribute_fontsize[]"
                                                           min="0"
                                                           value="<?php echo esc_attr( $attribute_fontsize ) ?>">
                                                    <div class="vi-ui label vi-wpvs-right-input-label">
														<?php esc_html_e( 'Px', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <label>
												<?php esc_html_e( 'Change the size of attribute items on', 'product-variations-swatches-for-woocommerce' ); ?>
                                            </label>
                                            <div class="equal width fields">
                                                <div class="field">
                                                    <div class="vi-ui right labeled fluid input">
                                                        <div class="vi-ui label vi-wpvs-right-input-label">
															<?php esc_html_e( 'Mobile', 'product-variations-swatches-for-woocommerce' ) ?>
                                                        </div>
                                                        <input type="number"
                                                               name="attribute_reduce_size_mobile[]"
                                                               min="30"
                                                               max="100"
                                                               class="vi-wpvs-attribute_reduce_size_mobile"
                                                               value="<?php echo esc_attr( $attribute_reduce_size_mobile ); ?>">
                                                        <div class="vi-ui label">
															<?php esc_html_e( '%', 'product-variations-swatches-for-woocommerce' ); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="field">
                                            <div class="equal width fields">
                                                <div class="field">
                                                    <label>
														<?php esc_html_e( 'Text color', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="text"
                                                           class="vi-wpvs-color vi-wpvs-attribute_default_color"
                                                           name="attribute_default_color[]"
                                                           value="<?php echo esc_attr( $attribute_default_color ) ?>">
                                                </div>
                                                <div class="field">
                                                    <label>
														<?php esc_html_e( 'Background color', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="text"
                                                           class="vi-wpvs-color vi-wpvs-attribute_default_bg_color"
                                                           name="attribute_default_bg_color[]"
                                                           value="<?php echo esc_attr( $attribute_default_bg_color ) ?>"">
                                                </div>
                                                <div class="field">
                                                    <label>
														<?php esc_html_e( 'Border color', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="text"
                                                           class="vi-wpvs-color vi-wpvs-attribute_default_border_color"
                                                           name="attribute_default_border_color[]"
                                                           value="<?php echo esc_attr( $attribute_default_border_color ) ?>">
                                                </div>
                                            </div>
                                            <div class="equal width fields">
                                                <div class="field">
                                                    <div class="equal width fields">
                                                        <div class="field vi-wpvs-field-max-width-number">
                                                            <label>
																<?php esc_html_e( 'Border radius', 'product-variations-swatches-for-woocommerce' ); ?>
                                                            </label>
                                                            <div class="vi-ui right labeled fluid input">
                                                                <input type="number"
                                                                       min="0"
                                                                       class="vi-wpvs-attribute_default_border_radius"
                                                                       name="attribute_default_border_radius[]"
                                                                       value="<?php echo esc_attr( $attribute_default_border_radius ) ?>">
                                                                <div class="vi-ui label vi-wpvs-right-input-label">
																	<?php esc_html_e( 'Px', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="field vi-wpvs-field-max-width-number">
                                                            <label>
																<?php esc_html_e( 'Border width', 'product-variations-swatches-for-woocommerce' ); ?>
                                                            </label>
                                                            <div class="vi-ui right labeled fluid input">
                                                                <input type="number"
                                                                       min="0"
                                                                       class="vi-wpvs-attribute_default_border_width"
                                                                       name="attribute_default_border_width[]"
                                                                       value="<?php echo esc_attr( $attribute_default_border_width ) ?>">
                                                                <div class="vi-ui label vi-wpvs-right-input-label">
																	<?php esc_html_e( 'Px', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label>
														<?php esc_html_e( 'Box shadow color', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="text"
                                                           class="vi-wpvs-color vi-wpvs-attribute_default_box_shadow_color"
                                                           name="attribute_default_box_shadow_color[]"
                                                           value="<?php echo esc_attr( $attribute_default_box_shadow_color ) ?>">
                                                </div>
                                                <div class="field">
                                                    <label>
														<?php esc_html_e( 'Out of stock', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <select name="attribute_out_of_stock[]"
                                                            class="vi-ui fluid dropdown vi-wpvs-attribute_out_of_stock">
                                                        <option value="hide" <?php selected( $attribute_out_of_stock, 'hide' ) ?>>
															<?php esc_html_e( 'Hide', 'product-variations-swatches-for-woocommerce' ); ?>
                                                        </option>
                                                        <option value="blur" <?php selected( $attribute_out_of_stock, 'blur' ) ?>>
															<?php esc_html_e( 'Blur', 'product-variations-swatches-for-woocommerce' ); ?>
                                                        </option>
                                                        <option value="blur_icon" <?php selected( $attribute_out_of_stock, 'blur_icon' ) ?>>
															<?php esc_html_e( 'Blur with icon', 'product-variations-swatches-for-woocommerce' ); ?>
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="title">
                                        <i class="dropdown icon"></i>
										<?php esc_html_e( 'Hover styling', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </div>
                                    <div class="content">
                                        <div class="field">
                                            <div class="equal width fields">
                                                <div class="field">
                                                    <label>
														<?php esc_html_e( 'Text color', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="text"
                                                           class="vi-wpvs-color vi-wpvs-attribute_hover_color"
                                                           name="attribute_hover_color[]"
                                                           value="<?php echo esc_attr( $attribute_hover_color ) ?>">
                                                </div>
                                                <div class="field">
                                                    <label for="">
														<?php esc_html_e( 'Background color', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="text"
                                                           class="vi-wpvs-color vi-wpvs-attribute_hover_bg_color"
                                                           name="attribute_hover_bg_color[]"
                                                           value="<?php echo esc_attr( $attribute_hover_bg_color ) ?>">
                                                </div>
                                                <div class="field">
                                                    <label>
														<?php esc_html_e( 'Border color', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="text"
                                                           class="vi-wpvs-color vi-wpvs-attribute_hover_border_color"
                                                           name="attribute_hover_border_color[]"
                                                           value="<?php echo esc_attr( $attribute_hover_border_color ) ?>">
                                                </div>
                                            </div>
                                            <div class="equal width fields">
                                                <div class="field">
                                                    <div class="equal width fields">
                                                        <div class="field vi-wpvs-field-max-width-number">
                                                            <label>
																<?php esc_html_e( 'Border radius', 'product-variations-swatches-for-woocommerce' ); ?>
                                                            </label>
                                                            <div class="vi-ui right labeled fluid input">
                                                                <input type="number"
                                                                       min="0"
                                                                       class="vi-wpvs-attribute_hover_border_radius"
                                                                       name="attribute_hover_border_radius[]"
                                                                       value="<?php echo esc_attr( $attribute_hover_border_radius ) ?>">
                                                                <div class="vi-ui label vi-wpvs-right-input-label">
																	<?php esc_html_e( 'Px', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="field vi-wpvs-field-max-width-number">
                                                            <label>
																<?php esc_html_e( 'Border width', 'product-variations-swatches-for-woocommerce' ); ?>
                                                            </label>
                                                            <div class="vi-ui right labeled fluid input">
                                                                <input type="number"
                                                                       min="0"
                                                                       class="vi-wpvs-attribute_hover_border_width"
                                                                       name="attribute_hover_border_width[]"
                                                                       value="<?php echo esc_attr( $attribute_hover_border_width ) ?>">
                                                                <div class="vi-ui label vi-wpvs-right-input-label">
																	<?php esc_html_e( 'Px', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label for="">
														<?php esc_html_e( 'Box shadow color', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="text"
                                                           class="vi-wpvs-color vi-wpvs-attribute_hover_box_shadow_color"
                                                           name="attribute_hover_box_shadow_color[]"
                                                           value="<?php echo esc_attr( $attribute_hover_box_shadow_color ) ?>">
                                                </div>
                                                <div class="field">
                                                    <label for="">
														<?php esc_html_e( 'Change the size of attribute items', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="number" min="0.5" max="2" step="0.01"
                                                           class="vi-wpvs-attribute_hover_scale vi-wpvs-attribute-scale"
                                                           name="attribute_hover_scale[]"
                                                           value="<?php echo esc_attr( $attribute_hover_scale ) ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="title">
                                        <i class="dropdown icon"></i>
										<?php esc_html_e( 'Selected styling', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </div>
                                    <div class="content">
                                        <div class="field">
                                            <div class="equal width fields">
                                                <div class="field">
                                                    <label>
														<?php esc_html_e( 'Text color', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="text"
                                                           class="vi-wpvs-color vi-wpvs-attribute_selected_color"
                                                           name="attribute_selected_color[]"
                                                           value="<?php echo esc_attr( $attribute_selected_color ) ?>">
                                                </div>
                                                <div class="field">
                                                    <label>
														<?php esc_html_e( 'Background', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="text"
                                                           class="vi-wpvs-color vi-wpvs-attribute_selected_bg_color"
                                                           name="attribute_selected_bg_color[]"
                                                           value="<?php echo esc_attr( $attribute_selected_bg_color ) ?>">
                                                </div>
                                                <div class="field">
                                                    <label>
														<?php esc_html_e( 'Border color', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="text"
                                                           class="vi-wpvs-color vi-wpvs-attribute_selected_border_color"
                                                           name="attribute_selected_border_color[]"
                                                           value="<?php echo esc_attr( $attribute_selected_border_color ) ?>">
                                                </div>
                                            </div>
                                            <div class="equal width fields">
                                                <div class="field">
                                                    <div class="equal width fields">
                                                        <div class="field vi-wpvs-field-max-width-number">
                                                            <label>
																<?php esc_html_e( 'Border radius', 'product-variations-swatches-for-woocommerce' ); ?>
                                                            </label>
                                                            <div class="vi-ui right labeled fluid input">
                                                                <input type="number"
                                                                       min="0"
                                                                       class="vi-wpvs-attribute_selected_border_radius"
                                                                       name="attribute_selected_border_radius[]"
                                                                       value="<?php echo esc_attr( $attribute_selected_border_radius ) ?>">
                                                                <div class="vi-ui label vi-wpvs-right-input-label">
																	<?php esc_html_e( 'Px', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="field vi-wpvs-field-max-width-number">
                                                            <label>
																<?php esc_html_e( 'Border width', 'product-variations-swatches-for-woocommerce' ); ?>
                                                            </label>
                                                            <div class="vi-ui right labeled fluid input">
                                                                <input type="number"
                                                                       min="0"
                                                                       class="vi-wpvs-attribute_selected_border_width"
                                                                       name="attribute_selected_border_width[]"
                                                                       value="<?php echo esc_attr( $attribute_selected_border_width ) ?>">
                                                                <div class="vi-ui label vi-wpvs-right-input-label">
																	<?php esc_html_e( 'Px', 'product-variations-swatches-for-woocommerce' ); ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label>
														<?php esc_html_e( 'Box shadow color', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="text"
                                                           class="vi-wpvs-color vi-wpvs-attribute_selected_box_shadow_color"
                                                           name="attribute_selected_box_shadow_color[]"
                                                           value="<?php echo esc_attr( $attribute_selected_box_shadow_color ) ?>">
                                                </div>
                                                <div class="field">
                                                    <label>
														<?php esc_html_e( 'Change the size of attribute items', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="number" min="0.5" max="2" step="0.01"
                                                           class="vi-wpvs-attribute_selected_scale vi-wpvs-attribute-scale"
                                                           name="attribute_selected_scale[]"
                                                           value="<?php echo esc_attr( $attribute_selected_scale ) ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="title">
                                        <i class="dropdown icon"></i>
										<?php esc_html_e( 'Tooltip styling', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </div>
                                    <div class="content">
                                        <div class="field">
                                            <div class="equal width fields">
                                                <div class="field">
                                                    <label for="">
														<?php esc_html_e( 'Enable', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <div class="vi-ui toggle checkbox">
                                                        <input type="hidden" name="attribute_tooltip_enable[]"
                                                               class="vi-wpvs-attribute_tooltip_enable"
                                                               value="<?php echo esc_attr( $attribute_tooltip_enable ); ?>">
                                                        <input type="checkbox"
                                                               class="vi-wpvs-attribute_default_box_shadow-checkbox" <?php checked( $attribute_tooltip_enable, '1' ) ?>><label>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label for="">
														<?php esc_html_e( 'Text color', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="text"
                                                           class="vi-wpvs-color vi-wpvs-attribute_tooltip_color"
                                                           name="attribute_tooltip_color[]"
                                                           value="<?php echo esc_attr( $attribute_tooltip_color ) ?>">
                                                </div>
                                                <div class="field">
                                                    <label for="">
														<?php esc_html_e( 'Background', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="text"
                                                           class="vi-wpvs-color vi-wpvs-attribute_tooltip_bg_color"
                                                           name="attribute_tooltip_bg_color[]"
                                                           value="<?php echo esc_attr( $attribute_tooltip_bg_color ) ?>">
                                                </div>
                                                <div class="field">
                                                    <label for="">
														<?php esc_html_e( 'Border color', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <input type="text"
                                                           class="vi-wpvs-color vi-wpvs-attribute_tooltip_border_color"
                                                           name="attribute_tooltip_border_color[]"
                                                           value="<?php echo esc_attr( $attribute_tooltip_border_color ) ?>">
                                                </div>
                                            </div>
                                            <div class="equal width fields">
                                                <div class="field">
                                                </div>
                                                <div class="field">
                                                    <label for="">
														<?php esc_html_e( 'Position', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <select name="attribute_tooltip_position[]"
                                                            class="vi-ui fluid dropdown vi-wpvs-attribute_tooltip_position">
                                                        <option value="bottom" <?php selected( $attribute_tooltip_position, 'bottom' ) ?>>
															<?php esc_html_e( 'Bottom', 'product-variations-swatches-for-woocommerce' ); ?>
                                                        </option>
                                                        <option value="left" <?php selected( $attribute_tooltip_position, 'left' ) ?>>
															<?php esc_html_e( 'Left', 'product-variations-swatches-for-woocommerce' ); ?>
                                                        </option>
                                                        <option value="right" <?php selected( $attribute_tooltip_position, 'right' ) ?>>
															<?php esc_html_e( 'Right', 'product-variations-swatches-for-woocommerce' ); ?>
                                                        </option>
                                                        <option value="top" <?php selected( $attribute_tooltip_position, 'top' ) ?>>
															<?php esc_html_e( 'Top', 'product-variations-swatches-for-woocommerce' ); ?>
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="field">
                                                    <label for="">
														<?php esc_html_e( 'Border radius', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <div class="vi-ui right labeled fluid input">
                                                        <input type="number"
                                                               class="vi-wpvs-attribute_tooltip_border_radius"
                                                               name="attribute_tooltip_border_radius[]"
                                                               min="0"
                                                               value="<?php echo esc_attr( $attribute_tooltip_border_radius ) ?>">
                                                        <div class="vi-ui label vi-wpvs-right-input-label">
															<?php esc_html_e( 'Px', 'product-variations-swatches-for-woocommerce' ); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label for="">
														<?php esc_html_e( 'font size', 'product-variations-swatches-for-woocommerce' ); ?>
                                                    </label>
                                                    <div class="vi-ui right labeled fluid input">
                                                        <input type="number"
                                                               class="vi-wpvs-attribute_tooltip_fontsize"
                                                               name="attribute_tooltip_fontsize[]"
                                                               min="0"
                                                               value="<?php echo esc_attr( $attribute_tooltip_fontsize ) ?>">
                                                        <div class="vi-ui label vi-wpvs-right-input-label">
															<?php esc_html_e( 'Px', 'product-variations-swatches-for-woocommerce' ); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
								<?php
							}
						}
						?>
                        <p>
							<?php esc_html_e( 'You can add only 3 profiles. Please update Pro version to add unlimited profiles.', 'product-variations-swatches-for-woocommerce' ); ?>
                            <a class="vi-ui button" href="https://1.envato.market/bd0ek"
                               target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
                        </p>
                    </div>
                    <div class="vi-ui bottom attached tab segment vi-wpvs-tab-single_page" data-tab="single_page">
                        <table class="form-table">
                            <tr valign="top">
                                <th>
                                    <label for="vi-wpvs-variation_threshold_single_page">
										<?php esc_html_e( 'Ajax variation threshold', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <input type="number" min="1" max="300" name="variation_threshold_single_page"
                                           class="vi-wpvs-variation_threshold_single_page"
                                           id="vi-wvps-variation_threshold_single_page"
                                           value="<?php echo esc_attr( $variation_threshold_single_page ); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="vi-wpvs-out_of_stock_variation_disable-checkbox">
										<?php esc_html_e( 'Disable \'out of stock\' variation items', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <div class="vi-ui toggle checkbox">
                                        <input type="hidden" name="out_of_stock_variation_disable"
                                               class="vi-wpvs-out_of_stock_variation_disable"
                                               value="<?php echo esc_attr( $out_of_stock_variation_disable ); ?>">
                                        <input type="checkbox" id="vi-wpvs-out_of_stock_variation_disable-checkbox"
                                               class="vi-wpvs-out_of_stock_variation_disable-checkbox" <?php checked( $out_of_stock_variation_disable, '1' ); ?>><label>
                                    </div>
                                    <p class="description">
										<?php esc_html_e( 'This function does not work for products whose number of variations is greater than the "Ajax variation threshold"', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th>
                                    <label for="vi-wpvs-attribute_double_click-checkbox">
										<?php esc_html_e( 'Clear on Reselect', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <div class="vi-ui toggle checkbox">
                                        <input type="hidden" name="attribute_double_click"
                                               class="vi-wpvs-attribute_double_click"
                                               value="<?php echo esc_attr( $attribute_double_click ); ?>">
                                        <input type="checkbox" id="vi-wpvs-attribute_double_click-checkbox"
                                               class="vi-wpvs-attribute_double_click-checkbox" <?php checked( $attribute_double_click, '1' ); ?>><label>
                                    </div>
                                    <p class="description">
										<?php esc_html_e( 'On single product page, clicking on a selected attribute will deselect it', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label for="vi-wpvs-single_attr_title-checkbox">
										<?php esc_html_e( 'Enable attribute title', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <div class="vi-ui toggle checkbox">
                                        <input type="hidden" name="single_attr_title"
                                               class="vi-wpvs-single_attr_title"
                                               value="<?php echo esc_attr( $single_attr_title ); ?>">
                                        <input type="checkbox" id="vi-wpvs-single_attr_title-checkbox"
                                               class="vi-wpvs-single_attr_title-checkbox" <?php checked( $single_attr_title, '1' ); ?>><label>
                                    </div>
                                    <p class="description">
										<?php esc_html_e( 'Show attribute title on single product page', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr class="vi-wpvs-single_attr_title-enable <?php echo $single_attr_title ? '' : ' vi-wpvs-hidden' ?>">
                                <th>
                                    <label for="vi-wpvs-single_attr_selected-checkbox">
										<?php esc_html_e( 'Show selected attribute item', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <div class="vi-ui toggle checkbox">
                                        <input type="hidden" name="single_attr_selected"
                                               class="vi-wpvs-single_attr_selected"
                                               value="<?php echo esc_attr( $single_attr_selected ); ?>">
                                        <input type="checkbox" id="vi-wpvs-single_attr_selected-checkbox"
                                               class="vi-wpvs-single_attr_selected-checkbox" <?php checked( $single_attr_selected, '1' ); ?>><label>
                                    </div>
                                    <p class="description">
										<?php esc_html_e( 'Display the selected item beside attribute title on single product page', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="vi-ui bottom attached tab segment" data-tab="product_list">
                        <table class="form-table">
                            <tbody>
                            <tr valign="top">
                                <th>
                                    <label>
										<?php esc_html_e( 'Ajax variation threshold', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <a class="vi-ui button" href="https://1.envato.market/bd0ek"
                                       target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th>
                                    <label>
										<?php esc_html_e( 'Enable add to cart', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <a class="vi-ui button" href="https://1.envato.market/bd0ek"
                                       target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
                                    <p class="description"><?php esc_html_e( 'Show the Add to cart button after selecting variation swatches on the product list', 'product-variations-swatches-for-woocommerce' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label>
										<?php esc_html_e( 'Show attribute name', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <a class="vi-ui button" href="https://1.envato.market/bd0ek"
                                       target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
                                    <p class="description"><?php esc_html_e( 'Enable to show the attribute name on the product list', 'product-variations-swatches-for-woocommerce' ); ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th>
                                    <label>
										<?php esc_html_e( 'Clear on Reselect', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <a class="vi-ui button" href="https://1.envato.market/bd0ek"
                                       target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
                                    <p class="description">
										<?php esc_html_e( 'On Product list, clicking on a selected attribute will deselect it', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th>
                                    <label>
										<?php esc_html_e( 'Enable tooltip', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <a class="vi-ui button" href="https://1.envato.market/bd0ek"
                                       target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
                                    <p class="description"><?php esc_html_e( 'Show tooltip on the product list if this tooltip is enabled on swatches profile', 'product-variations-swatches-for-woocommerce' ); ?></p>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label>
										<?php esc_html_e( 'Assign page', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <a class="vi-ui button" href="https://1.envato.market/bd0ek"
                                       target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
                                    <p class="description"><?php echo wp_kses_post( __( 'You can use WP\'s <a href="https://villatheme.com/knowledge-base/conditional-tags/" target="_blank">Conditional tags</a> to enable/disable swatches of product list on specific pages.', 'product-variations-swatches-for-woocommerce' ) ) ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th>
                                    <label>
										<?php esc_html_e( 'Swatches align', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <a class="vi-ui button" href="https://1.envato.market/bd0ek"
                                       target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th>
                                    <label>
										<?php esc_html_e( 'Position', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <a class="vi-ui button" href="https://1.envato.market/bd0ek"
                                       target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
                                    <p class="description"><?php esc_html_e( 'The position of variation on shop page, category page and other product list pages', 'product-variations-swatches-for-woocommerce' ); ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th>
                                    <label>
										<?php esc_html_e( 'Maximum attribute items', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <a class="vi-ui button" href="https://1.envato.market/bd0ek"
                                       target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
                                    <p class="description">
										<?php esc_html_e( 'The maximum number of items of an attribute can be displayed. Set to 0 to not limit this.', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label>
										<?php esc_html_e( 'Show more link', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <a class="vi-ui button" href="https://1.envato.market/bd0ek"
                                       target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
                                    <p class="description">
										<?php esc_html_e( 'This option is used when total items of an attribute is greater than the Maximum attribute items above', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    <label>
										<?php esc_html_e( 'Swatches slider', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </label>
                                </th>
                                <td>
                                    <a class="vi-ui button" href="https://1.envato.market/bd0ek"
                                       target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
                                    <p>
										<?php esc_html_e( 'Show all items of the attribute in a slider. The tooltip will hide on slider.', 'product-variations-swatches-for-woocommerce' ); ?>
                                    </p>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="vi-ui bottom attached tab segment" data-tab="custom_attrs">
                        <div class="vi-ui blue message">
							<?php esc_html_e( 'Settings the rules for Custom Attributes', 'product-variations-swatches-for-woocommerce' ); ?>
                            <ul class="list">
                                <li><?php esc_html_e( 'For each rule, if a custom attribute has the same name as field "Attribute name" and products that contain this custom attribute belongs to one of selected "Product category", the swatches settings of current rule will be applied to that custom attribute', 'product-variations-swatches-for-woocommerce' ); ?></li>
                                <li><?php esc_html_e( 'Rules are checked from top to bottom and will stop if the attribute matches a rule', 'product-variations-swatches-for-woocommerce' ); ?></li>
                                <li><?php esc_html_e( 'Attribute name is case-insensitive', 'product-variations-swatches-for-woocommerce' ); ?></li>
                                <li><?php esc_html_e( 'If Product category of a rule is empty, this rule applies to products from all categories', 'product-variations-swatches-for-woocommerce' ); ?></li>
                            </ul>
                        </div>
                        <table class="form-table vi-wpvs-table">
                            <thead>
                            <tr>
                                <th colspan="2"><?php esc_html_e( 'Conditions(AND)', 'product-variations-swatches-for-woocommerce' ); ?></th>
                                <th colspan="5"><?php esc_html_e( 'Apply these settings for attributes that match conditions', 'product-variations-swatches-for-woocommerce' ); ?></th>
                            </tr>
                            <tr>
                                <td>
                                    <label><?php esc_html_e( 'Attribute name', 'product-variations-swatches-for-woocommerce' ); ?></label>
                                </td>
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
                            <tr class="vi-wpvs-rule-custom-attrs-container">
                                <td colspan="7">
                                    <a class="vi-ui button" href="https://1.envato.market/bd0ek"
                                       target="_blank"><?php esc_html_e( 'Unlock This Feature', 'product-variations-swatches-for-woocommerce' ); ?> </a>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <p class="vi-wpvs-save-wrap">
                        <button type="button" class="vi-wpvs-save vi-ui primary labeled icon button"
                                name="vi-wpvs-save"><i class="save icon"></i>
							<?php esc_html_e( 'Save', 'product-variations-swatches-for-woocommerce' ); ?>
                        </button>
                        <button type="button" class="vi-ui button labeled icon vi-wpvs-import">
                            <i class="download icon"></i><?php esc_html_e( 'Import Settings', 'product-variations-swatches-for-woocommerce' ) ?>
                        </button>
                        <button class="vi-ui button labeled icon"
                                name="vi-wpvs-export">
                            <i class="upload icon"></i><?php esc_html_e( 'Export Settings', 'product-variations-swatches-for-woocommerce' ) ?>
                        </button>
                        <button type="button" class="vi-ui button negative labeled icon vi-wpvs-reset"
                                name="vi-wpvs-reset">
                            <i class="undo icon"></i><?php esc_html_e( 'Reset Settings', 'product-variations-swatches-for-woocommerce' ) ?>
                        </button>
                    </p>
                    <div class="vi-ui vi-wpvs-import-wrap-wrap segment vi-wpvs-hidden">
                        <div class="vi-wpvs-import-wrap">
                            <input type="file" name="vi_wpvs_import_file" id="vi-wpvs-import-file"
                                   class="vi-wpvs-import-file" accept=".csv">
                            <button type="submit" class="vi-ui button green icon vi-wpvs-import1"
                                    name="vi-wpvs-import-choose_file">
								<?php esc_html_e( 'Import', 'product-variations-swatches-for-woocommerce' ) ?>
                            </button>
                        </div>
                    </div>
                </form>
				<?php
				do_action( 'villatheme_support_product-variations-swatches-for-woocommerce' );
				?>
            </div>
        </div>
		<?php
	}

	public function save_settings() {
		$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( $_REQUEST['page'] ) : '';
		if ( $page !== 'woocommerce-product-variations-swatches' ) {
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! isset( $_POST['_vi_woo_product_variation_swatches_settings'] ) || ! wp_verify_nonce( $_POST['_vi_woo_product_variation_swatches_settings'],
				'_vi_woo_product_variation_swatches_settings_action' ) ) {
			return;
		}
		global $vi_wpvs_settings;
		if ( isset( $_POST['vi-wpvs-reset'] ) ) {
			$args = json_decode( $this->settings->get_reset_data(), true );
			update_option( 'vi_woo_product_variation_swatches_params', $args );
			$vi_wpvs_settings = $args;

			return;
		}
		if ( isset( $_POST['vi-wpvs-export'] ) ) {
			$filename     = 'wpvs_swatches_settings.csv';
			$export_value = json_encode( get_option( 'vi_woo_product_variation_swatches_params', $vi_wpvs_settings ), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
			$fh           = @fopen( 'php://output', 'w' );
			header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
			header( 'Content-Description: File Transfer' );
			header( 'Content-Type: application/json;charset=utf-8' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Expires: 0' );
			header( 'Pragma: public' );
			fwrite( $fh, $export_value );
			fclose( $fh );
			die;
		}
		if ( isset( $_POST['vi-wpvs-import-choose_file'] ) ) {
			if ( ! isset( $_FILES['vi_wpvs_import_file'] ) ) {
				$this->error = __( 'File is empty. Please upload something more substantial. This error could also be caused by uploads being disabled in your php.ini or by post_max_size being defined as smaller than upload_max_filesize in php.ini.', 'product-variations-swatches-for-woocommerce' );

				return;
			}
			if ( ! empty( $_FILES['vi_wpvs_import_file']['error'] ) ) {
				$this->error = __( 'File is error.', 'product-variations-swatches-for-woocommerce' );

				return;
			}
			$import      = $_FILES['vi_wpvs_import_file'];
			$import_type = strtolower( pathinfo( $import['name'], PATHINFO_EXTENSION ) );
			if ( $import_type !== 'csv' ) {
				$this->error = __( 'Please select the csv file', 'product-variations-swatches-for-woocommerce' );

				return;
			}
			$file_content = file_get_contents( $import['tmp_name'] );
			if ( ! $file_content ) {
				$this->error = __( 'File is empty.', 'product-variations-swatches-for-woocommerce' );

				return;
			}
			if ( strpos( $file_content, 'check_swatches_settings' ) === false ) {
				$this->error = __( 'There isn\'t Swatches Settings. Please select the another', 'product-variations-swatches-for-woocommerce' );

				return;
			}
			$vi_wpvs_settings = json_decode( $file_content, true );
			update_option( 'vi_woo_product_variation_swatches_params', $vi_wpvs_settings );

			return;
		}
		if ( isset( $_POST['vi-wpvs-save'] ) || isset( $_POST['vi-wpvs-check_key'] ) ) {
			$map_args_1 = array(
				'ids',
				'names',
				'attribute_reduce_size_mobile',
				'attribute_width',
				'attribute_height',
				'attribute_fontsize',
				'attribute_padding',
				'attribute_transition',

				'attribute_default_box_shadow_color',
				'attribute_default_color',
				'attribute_default_bg_color',
				'attribute_default_border_color',
				'attribute_default_border_radius',
				'attribute_default_border_width',

				'attribute_hover_scale',
				'attribute_hover_box_shadow_color',
				'attribute_hover_color',
				'attribute_hover_bg_color',
				'attribute_hover_border_color',
				'attribute_hover_border_radius',
				'attribute_hover_border_width',

				'attribute_selected_scale',
				'attribute_selected_icon_enable',
				'attribute_selected_icon_type',
				'attribute_selected_icon_color',
				'attribute_selected_box_shadow_color',
				'attribute_selected_color',
				'attribute_selected_bg_color',
				'attribute_selected_border_color',
				'attribute_selected_border_radius',
				'attribute_selected_border_width',

				'attribute_out_of_stock',

				'attribute_tooltip_enable',
				'attribute_tooltip_type',
				'attribute_tooltip_position',
				'attribute_tooltip_width',
				'attribute_tooltip_height',
				'attribute_tooltip_fontsize',
				'attribute_tooltip_border_radius',
				'attribute_tooltip_bg_color',
				'attribute_tooltip_color',
				'attribute_tooltip_border_color',

			);
			$map_args_2 = array(
				'attribute_display_default',
				'attribute_double_click',
				'attribute_profile_default',
				'out_of_stock_variation_disable',
				'variation_threshold_single_page',
				'single_attr_title',
				'single_attr_selected',
			);
			$args       = array();
			foreach ( $map_args_1 as $item ) {
				$args[ $item ] = isset( $_POST[ $item ] ) ? vi_wpvs_sanitize_fields( $_POST[ $item ] ) : array();
			}
			foreach ( $map_args_2 as $item ) {
				$args[ $item ] = isset( $_POST[ $item ] ) ? sanitize_text_field( stripslashes( $_POST[ $item ] ) ) : '';
			}
			if ( ! count( $args['names'] ) ) {
				$this->error = esc_html__( 'Can not remove all Countdown timer settings.', 'product-variations-swatches-for-woocommerce' );

				return;
			} else {
				if ( count( $args['names'] ) != count( array_unique( $args['names'] ) ) ) {
					$this->error = esc_html__( 'Names are unique.', 'product-variations-swatches-for-woocommerce' );

					return;
				}
				foreach ( $args['names'] as $key => $name ) {
					if ( ! $name ) {
						$this->error = esc_html__( 'Names can not be empty.', 'product-variations-swatches-for-woocommerce' );

						return;
					}
				}
			}
			$args = wp_parse_args( $args, get_option( 'vi_woo_product_variation_swatches_params', $vi_wpvs_settings ) );
			update_option( 'vi_woo_product_variation_swatches_params', $args );
			$vi_wpvs_settings = $args;
		}
	}


	public function admin_enqueue_scripts() {
		$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : '';
		if ( $page === 'woocommerce-product-variations-swatches' ) {
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
			wp_enqueue_style( 'semantic-ui-menu', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'menu.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-message', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'message.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-popup', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'popup.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-segment', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'segment.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'transition', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'transition.min.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'semantic-ui-tab', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'tab.css', '', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'product-variations-swatches-for-woocommerce-admin-css', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'admin-settings.css', array(), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'product-variations-swatches-for-woocommerce-admin-minicolors', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'minicolors.css', array(), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'semantic-ui-accordion', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'accordion.min.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'semantic-ui-address', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'address.min.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'semantic-ui-checkbox', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'checkbox.min.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'semantic-ui-dropdown', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'dropdown.min.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'semantic-ui-form', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'form.min.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'semantic-ui-tab', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'tab.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'transition', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'transition.min.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'product-variations-swatches-for-woocommerce-admin-js', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'admin-settings.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'product-variations-swatches-for-woocommerce-admin-minicolors', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'minicolors.min.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
		}
	}
}