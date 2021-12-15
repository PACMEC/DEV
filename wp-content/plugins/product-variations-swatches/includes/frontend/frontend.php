<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WOO_PRODUCT_VARIATIONS_SWATCHES_Frontend_Frontend {
	protected $settings;

	public function __construct() {
		$this->settings = new VI_WOO_PRODUCT_VARIATIONS_SWATCHES_DATA();
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ), 99 );
		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array(
			$this,
			'variation_attribute_options_html'
		), PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_ajax_variation_threshold', array(
			$this,
			'viwpvs_ajax_variation_threshold'
		), PHP_INT_MAX, 2 );
		add_filter( 'woocommerce_available_variation', array(
			$this,
			'wvps_woocommerce_available_variation'
		), PHP_INT_MAX, 3 );
	}

	/**
	 * @param $result
	 * @param $object
	 * @param $variation WC_Product_Variation
	 *
	 * @return bool
	 */
	public function wvps_woocommerce_available_variation( $result, $object, $variation ) {
		if ( $variation->get_status() !== 'publish' || ( ! $variation->is_in_stock() || ( $variation->managing_stock() && $variation->get_stock_quantity() <= get_option( 'woocommerce_notify_no_stock_amount', 0 ) && 'no' !== $variation->get_backorders() ) ) ) {
			if ( $this->settings->get_params( 'out_of_stock_variation_disable' ) ) {
				$result = false;
			} else {
				$result['viwpvs_not_available'] = 1;
			}
		}

		return $result;
	}

	public function viwpvs_ajax_variation_threshold( $limit, $product ) {
		$settings = new  VI_WOO_PRODUCT_VARIATIONS_SWATCHES_DATA();
		$result   = $settings->get_params( 'variation_threshold_single_page' );
		$result   = $result ?: 30;

		return $result;
	}

	private function get_select_dropdown( $args ) {
		$args                  = wp_parse_args( $args, array(
			'options'          => false,
			'attribute'        => false,
			'product'          => false,
			'selected'         => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'type'             => '',
			'assigned'         => '',
			'show_option_none' => esc_html__( 'Choose an option', 'product-variations-swatches-for-woocommerce' )
		) );
		$options               = $args['options'] ?: array();
		$product               = $args['product'] ?: null;
		$attribute             = $args['attribute'] ?: '';
		$name                  = $args['name'] ?: 'attribute_' . sanitize_title( $attribute );
		$id                    = $args['id'] ?: sanitize_title( $attribute );
		$class                 = $args['class'] ? $args['class'] . ' vi-wpvs-select-option' : 'vi-wpvs-select-option';
		$show_option_none      = (bool) $args['show_option_none'];
		$show_option_none_text = $args['show_option_none'] ?: __( 'Choose an option', 'product-variations-swatches-for-woocommerce' );

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}
		ob_start();
		?>
        <select name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $id ); ?>"
                class="<?php echo esc_attr( $class ); ?>"
                data-attribute_name="attribute_<?php echo esc_attr( sanitize_title( $attribute ) ) ?>"
                data-show_option_none="attribute_<?php echo $show_option_none ? esc_attr( 'yes' ) : esc_attr( 'no' ); ?>">
            <option value=""><?php echo esc_html( $show_option_none_text ); ?></option>
			<?php
			if ( ! empty( $options ) ) {
				if ( $product && taxonomy_exists( $attribute ) ) {
					// Get terms if this is a taxonomy - ordered. We need the names too.
					$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
					foreach ( $terms as $term ) {
						if ( in_array( $term->slug, $options, true ) ) {
							echo sprintf( '<option value="%s" %s>%s</option>',
								esc_attr( $term->slug ),
								selected( sanitize_title( $args['selected'] ), $term->slug, false ),
								esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product ) )
							);
						}
					}
				} else {
					foreach ( $options as $option ) {
						$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
						echo sprintf( '<option value="%s" %s>%s</option>',
							esc_attr( $option ),
							$selected,
							esc_html( apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product ) )
						);
					}
				}
			}
			?>
        </select>
		<?php
		$html = ob_get_clean();

		return $html;
	}

	public function variation_attribute_options_html( $html, $args ) {
		$args       = wp_parse_args( $args, array(
			'options'          => false,
			'attribute'        => false,
			'product'          => false,
			'selected'         => false,
			'name'             => '',
			'id'               => '',
			'class'            => '',
			'type'             => '',
			'assigned'         => '',
			'show_option_none' => esc_html__( 'Choose an option', 'product-variations-swatches-for-woocommerce' )
		) );
		$check_null = strpos( $html, '<select' );
		if ( $check_null === false ) {
			$html = $this->get_select_dropdown( $args );
		}
		$attribute = $args['attribute'];
		if ( ! $attribute ) {
			return $html;
		}
		$product                   = $args['product'];
		$product_id                = method_exists( $product, 'get_id' ) ? $product->get_id() : $product->id;
		$vi_attribute_settings     = get_post_meta( $product_id, '_vi_woo_product_variation_swatches_product_attribute', true );
		$vi_attribute_settings     = $vi_attribute_settings ? json_decode( $vi_attribute_settings, true ) : array();
		$vi_attribute_type         = $vi_attribute_settings['attribute_type'][ $attribute ] ?? null;
		$vi_attribute_profile      = $vi_attribute_settings['attribute_profile'][ $attribute ] ?? null;
		$vi_attribute_display_type = $vi_attribute_settings['attribute_display_type'][ $attribute ] ?? null;
		$settings                  = new  VI_WOO_PRODUCT_VARIATIONS_SWATCHES_DATA();
		$is_taxonomy               = ( 'pa_' === substr( $attribute, 0, 3 ) ) ? 1 : 0;
		$use_taxonomy_type         = false;
		if ( $is_taxonomy ) {
			if ( ! $vi_attribute_profile ) {
				$vi_attribute_profile = $settings->get_params( 'taxonomy_profiles' )[ $attribute ] ?? '';
			}
			if ( ! $vi_attribute_display_type ) {
				$vi_attribute_display_type = $settings->get_params( 'taxonomy_display_type' )[ $attribute ] ?? '';
			}
			if ( ! $vi_attribute_type ) {
				$use_taxonomy_type = true;
				$vi_attribute_type = self::get_attribute_taxonomy_type( $attribute );
				if ( ! in_array( $vi_attribute_type, array(
						'button',
						'color',
						'image',
						'variation_img',
						'radio',
						'viwpvs_default'
					) ) && ! isset( $settings->get_params( 'taxonomy_profiles' )[ $attribute ] ) ) {
					if ( $settings->get_params( 'attribute_display_default' ) !== 'none' ) {
						$vi_attribute_type = $settings->get_params( 'attribute_display_default' );
					}
				}
			}
		} else {
			if ( ! $vi_attribute_type && $settings->get_params( 'attribute_display_default' ) !== 'none' ) {
				$vi_attribute_type = $settings->get_params( 'attribute_display_default' );
			}
		}
		$options = $args['options'];
		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}
		if ( empty( $options ) ) {
			return $html;
		}
		$vi_args                       = array();
		$vi_args['vi_variation_class'] = $args['vi_variation_class'] ?? '';
		$vi_args['selected']           = $args['selected'] ?? '';
		$vi_args['show_option_none']   = $args['show_option_none'] ?? esc_html__( 'Choose an option', 'product-variations-swatches-for-woocommerce' );
		$attribute_double_click        = $args['viwpvs_double_click'] ?? $settings->get_params( 'attribute_double_click' );
		$attribute_double_click        = $attribute_double_click ? 1 : '';
		$attribute_title_enable        = $args['viwpvs_attr_title'] ?? $settings->get_params( 'single_attr_title' );
		$attribute_title_enable        = $attribute_title_enable ? 1 : '';
		$attribute_attr_selected       = $args['viwpvs_attr_selected'] ?? $settings->get_params( 'single_attr_selected' );
		$attribute_attr_selected       = $attribute_attr_selected ? 1 : '';
		$vi_attribute_display_type     = $attribute_title_enable ? $vi_attribute_display_type : '';
		$display_type_class            = array(
			'vi-wpvs-variation-style',
			'vi-wpvs-variation-style-' . $vi_attribute_display_type ?: 'vertical'
		);
		$display_type_class[]          = is_rtl() ? 'vi-wpvs-variation-style-rtl' : '';
		$display_type_class            = trim( implode( ' ', $display_type_class ) );
		$new_html                      = '<div class="vi-wpvs-variation-wrap-wrap vi-wpvs-hidden" data-wpvs_double_click="' . $attribute_double_click . '" data-wpvs_attr_title="' . $attribute_title_enable . '" ';
		$new_html                      .= 'data-display_type="' . $display_type_class . '" data-swatch_type="' . $vi_attribute_type . '" data-show_selected_item="' . $attribute_attr_selected . '" data-hide_outofstock="' . $this->settings->get_params( 'out_of_stock_variation_disable' ) . '" data-wpvs_attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" ';
		$new_html                      .= '>';
		if ( $vi_attribute_type === 'viwpvs_default' ) {
			$new_html .= $html;
		} else {
			$new_html .= '<div class="vi-wpvs-select-attribute vi-wpvs-select-attribute-attribute_' . esc_attr( $attribute ) . '">';
			$new_html .= $html;
			$new_html .= '</div>';
			$new_html .= self::get_attribute_option_html( $attribute, $product, $options, $vi_attribute_settings, $vi_args, $vi_attribute_type,
				$vi_attribute_profile, $use_taxonomy_type );
		}
		$new_html .= '</div>';

		return $new_html;
	}

	public static function get_attribute_option_color( $option, $colors = array(), $color_separator = '1' ) {
		if ( empty( $option ) ) {
			return '';
		}
		$settings = new VI_WOO_PRODUCT_VARIATIONS_SWATCHES_DATA();
		if ( empty( $colors ) ) {
			$result = $settings->get_default_color( strtolower( $option ) );
		} else {
			if ( ( $count_colors = count( $colors ) ) === 1 ) {
				$result = $colors[0];
				$result = $result ?: $settings->get_default_color( strtolower( $option ) );
			} else {
				$temp = (int) floor( 100 / $count_colors );
				switch ( $color_separator ) {
					case '2':
						$result = 'linear-gradient( ' . implode( ',', $colors ) . ' )';
						break;
					case '3':
						$result = 'linear-gradient(to bottom left, ' . implode( ',', $colors ) . ' )';
						break;
					case '4':
						$result = 'linear-gradient( to bottom right, ' . implode( ',', $colors ) . ' )';
						break;
					case '5':
						$result = 'linear-gradient(to right,' . $colors[0] . ' ' . $temp . '%';
						for ( $i = 1; $i < $count_colors; $i ++ ) {
							$result .= ' , ' . $colors[ $i ] . ' ' . ( $i * $temp ) . '% ' . ( ( $i + 1 ) * $temp ) . '%';
						}
						$result .= ' )';
						break;
					case '6':
						$result = 'linear-gradient(' . $colors[0] . ' ' . $temp . '%';
						for ( $i = 1; $i < $count_colors; $i ++ ) {
							$result .= ' , ' . $colors[ $i ] . ' ' . ( $i * $temp ) . '% ' . ( ( $i + 1 ) * $temp ) . '%';
						}
						$result .= ' )';
						break;
					case '7':
						$result = 'linear-gradient(to bottom left, ' . $colors[0] . ' ' . $temp . '%';
						for ( $i = 1; $i < $count_colors; $i ++ ) {
							$result .= ' , ' . $colors[ $i ] . ' ' . ( $i * $temp ) . '% ' . ( ( $i + 1 ) * $temp ) . '%';
						}
						$result .= ' )';
						break;
					case '8':
						$result = 'linear-gradient(to bottom right, ' . $colors[0] . ' ' . $temp . '%';
						for ( $i = 1; $i < $count_colors; $i ++ ) {
							$result .= ' , ' . $colors[ $i ] . ' ' . ( $i * $temp ) . '% ' . ( ( $i + 1 ) * $temp ) . '%';
						}
						$result .= ' )';
						break;
					default:
						$result = 'linear-gradient( to right, ' . implode( ',', $colors ) . ' )';
				}
			}
		}

		return $result;
	}

	public static function get_attribute_option_html( $attribute, $product, $options, $vi_attribute_settings, $vi_args, $type, $profile, $use_taxonomy_type = '' ) {
		if ( empty( $attribute ) || empty( $product ) || empty( $options ) ) {
			return false;
		}
		$settings                   = new VI_WOO_PRODUCT_VARIATIONS_SWATCHES_DATA();
		$profile_default            = $settings->get_params( 'attribute_profile_default' );
		$profile_ids                = $settings->get_params( 'ids' );
		$profile_default_index      = array_search( $profile_default, $profile_ids ) ? array_search( $profile_default, $profile_ids ) : 0;
		$profile_index              = array_search( $profile, $profile_ids ) ? array_search( $profile, $profile_ids ) : $profile_default_index;
		$profile                    = $profile_ids[ $profile_index ];
		$attribute_tooltip_position = $settings->get_current_setting( 'attribute_tooltip_position', $profile_index );
		$type                       = $type ?: 'select';
		$colors                     = $vi_attribute_settings['attribute_colors'][ $attribute ] ?? array();
		$color_separator            = $vi_attribute_settings['attribute_color_separator'][ $attribute ] ?? array();
		$img_ids                    = $vi_attribute_settings['attribute_img_ids'][ $attribute ] ?? array();
		$option_selected            = $vi_args['selected'] ?? '';
		$div_class                  = array(
			'vi-wpvs-variation-wrap',
			'vi-wpvs-variation-wrap-' . $profile,
			'vi-wpvs-variation-wrap-' . $type,
		);
		$div_class[]                = taxonomy_exists( $attribute ) ? 'vi-wpvs-variation-wrap-taxonomy' : '';
		$div_class[]                = $vi_args['vi_variation_class'] ?? '';
		$div_class[]                = is_rtl() ? 'vi-wpvs-variation-wrap-rtl' : '';
		$div_class                  = implode( ' ', $div_class );
		ob_start();
		?>
        <div class="<?php echo esc_attr( trim( $div_class ) ); ?>"
             data-our_of_stock="<?php echo esc_attr( $settings->get_current_setting( 'attribute_out_of_stock', $profile_index ) ) ?>"
             data-attribute="attribute_<?php echo esc_attr( sanitize_title( $attribute ) ); ?>">
			<?php
			$variations = $product->get_children();
			if ( taxonomy_exists( $attribute ) ) {
				$terms = wc_get_product_terms(
					$product->get_id(),
					$attribute,
					array(
						'fields' => 'all',
					)
				);
				switch ( $type ) {
					case 'button':
						foreach ( $terms as $term ) {
							if ( ! in_array( $term->slug, $options ) ) {
								continue;
							}
							$term_name    = apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product );
							$term_tooltip = apply_filters( 'viwpvs_variation_option_tooltip', $term_name, $term, $attribute, $variations, $product );
							$term_class   = $option_selected === $term->slug ? 'vi-wpvs-option-wrap vi-wpvs-option-wrap-selected' : 'vi-wpvs-option-wrap vi-wpvs-option-wrap-default';
							?>
                            <div class="<?php echo esc_attr( $term_class ); ?>"
                                 data-attribute_label="<?php echo esc_attr( $term_name ); ?>"
                                 data-attribute_value="<?php echo esc_attr( $term->slug ); ?>">
                                <span class="vi-wpvs-option vi-wpvs-option-button">
						            <?php echo wp_kses_post( $term_name ); ?>
					            </span>
                                <div class="vi-wpvs-option-tooltip vi-wpvs-option-tooltip-<?php echo esc_attr( $attribute_tooltip_position ); ?>"
                                     data-attribute_label="<?php echo esc_attr( $term_name ); ?>">
                                    <span>
                                        <?php echo wp_kses_post( $term_tooltip ); ?>
                                    </span>
                                </div>
                            </div>
							<?php
						}
						break;
					case 'color':
						foreach ( $terms as $term ) {
							if ( ! in_array( $term->slug, $options ) ) {
								continue;
							}
							$term_name              = apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product );
							$term_tooltip           = apply_filters( 'viwpvs_variation_option_tooltip', $term_name, $term, $attribute, $variations, $product );
							$vi_wpvs_terms_settings = get_term_meta( $term->term_id, 'vi_wpvs_terms_params', true );
							$term_class             = $option_selected === $term->slug ? 'vi-wpvs-option-wrap vi-wpvs-option-wrap-selected' : 'vi-wpvs-option-wrap vi-wpvs-option-wrap-default';
							if ( $use_taxonomy_type ) {
								$term_colors          = $vi_wpvs_terms_settings['color'] ?? array();
								$term_color_separator = $vi_wpvs_terms_settings['color_separator'] ?? '1';
							} else {
								$term_colors          = $colors[ $term->term_id ] ?? $vi_wpvs_terms_settings['color'] ?? array();
								$term_color_separator = $color_separator[ $term->term_id ] ?? $vi_wpvs_terms_settings['color_separator'] ?? '1';
							}
							$term_color = self::get_attribute_option_color( $term->slug, $term_colors, $term_color_separator );
							?>
                            <div class="<?php echo esc_attr( $term_class ); ?>"
                                 data-attribute_label="<?php echo esc_attr( $term_name ); ?>"
                                 data-attribute_value="<?php echo esc_attr( $term->slug ); ?>">
                                <span class="vi-wpvs-option vi-wpvs-option-color"
                                      data-option_color="<?php echo esc_attr( $term_color ); ?>"></span>
                                <div class="vi-wpvs-option-tooltip vi-wpvs-option-tooltip-<?php echo esc_attr( $attribute_tooltip_position ); ?>"
                                     data-attribute_label="<?php echo esc_attr( $term_name ); ?>">
                                    <span>
                                        <?php echo wp_kses_post( $term_tooltip ); ?>
                                    </span>
                                </div>
                            </div>
							<?php
						}
						break;
					case 'image':
						foreach ( $terms as $term ) {
							if ( ! in_array( $term->slug, $options ) ) {
								continue;
							}
							$term_name              = apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product );
							$term_tooltip           = apply_filters( 'viwpvs_variation_option_tooltip', $term_name, $term, $attribute, $variations, $product );
							$vi_wpvs_terms_settings = get_term_meta( $term->term_id, 'vi_wpvs_terms_params', true );
							$term_class             = $option_selected === $term->slug ? 'vi-wpvs-option-wrap vi-wpvs-option-wrap-selected' : 'vi-wpvs-option-wrap vi-wpvs-option-wrap-default';
							if ( $use_taxonomy_type ) {
								$terms_img_id = $vi_wpvs_terms_settings['img_id'] ?? '';
							} else {
								$terms_img_id = $img_ids[ $term->term_id ] ?? $vi_wpvs_terms_settings['img_id'] ?? '';
							}
							$img_url      = $terms_img_id ? wp_get_attachment_image_url( $terms_img_id, 'woocommerce_gallery_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' );
							$img_loop_src = $terms_img_id ? wp_get_attachment_image_url( $terms_img_id, 'woocommerce_thumbnail', true ) : '';
							?>
                            <div class="<?php echo esc_attr( $term_class ); ?>"
                                 data-attribute_label="<?php echo esc_attr( $term_name ); ?>"
                                 data-attribute_value="<?php echo esc_attr( $term->slug ); ?>">
                                <img src="<?php echo esc_url( $img_url ); ?>"
                                     alt="<?php echo esc_attr( $term->slug ); ?>"
                                     data-loop_src="<?php echo esc_url( $img_loop_src ); ?>"
                                     class="vi-wpvs-option vi-wpvs-option-image">
                                <div class="vi-wpvs-option-tooltip vi-wpvs-option-tooltip-<?php echo esc_attr( $attribute_tooltip_position ); ?>"
                                     data-attribute_label="<?php echo esc_attr( $term_name ); ?>">
                                    <span>
                                        <?php echo wp_kses_post( $term_tooltip ); ?>
                                    </span>
                                </div>
                            </div>
							<?php
						}
						break;
					case 'variation_img':
						$variations = $product->get_children();
						foreach ( $terms as $term ) {
							if ( ! in_array( $term->slug, $options ) ) {
								continue;
							}
							$term_name    = apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product );
							$term_tooltip = apply_filters( 'viwpvs_variation_option_tooltip', $term_name, $term, $attribute, $variations, $product );
							$term_class   = $option_selected === $term->slug ? 'vi-wpvs-option-wrap vi-wpvs-option-wrap-selected' : 'vi-wpvs-option-wrap vi-wpvs-option-wrap-default';
							?>
                            <div class="<?php echo esc_attr( $term_class ); ?>"
                                 data-attribute_label="<?php echo esc_attr( $term_name ); ?>"
                                 data-attribute_value="<?php echo esc_attr( $term->slug ); ?>">
								<?php
								$terms_img_id = '';
								foreach ( $variations as $variation_id ) {
									if ( $term->slug === get_post_meta( $variation_id, 'attribute_' . sanitize_title( $attribute ), true ) ) {
										$terms_img_id = get_post_thumbnail_id( $variation_id );
										break;
									}
								}
								$img_url      = $terms_img_id ? wp_get_attachment_image_url( $terms_img_id, 'woocommerce_gallery_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' );
								$img_loop_src = $terms_img_id ? wp_get_attachment_image_url( $terms_img_id, 'woocommerce_thumbnail', true ) : '';
								?>
                                <img src="<?php echo esc_url( $img_url ); ?>"
                                     alt="<?php echo esc_attr( $term->slug ); ?>"
                                     data-loop_src="<?php echo esc_url( $img_loop_src ); ?>"
                                     class="vi-wpvs-option vi-wpvs-option-image">
                                <div class="vi-wpvs-option-tooltip vi-wpvs-option-tooltip-<?php echo esc_attr( $attribute_tooltip_position ); ?>"
                                     data-attribute_label="<?php echo esc_attr( $term_name ); ?>">
                                    <span>
                                        <?php echo wp_kses_post( $term_tooltip ); ?>
                                    </span>
                                </div>
                            </div>
							<?php
						}
						break;
					case 'radio':
						foreach ( $terms as $term ) {
							if ( ! in_array( $term->slug, $options ) ) {
								continue;
							}
							$term_name    = apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product );
							$term_tooltip = apply_filters( 'viwpvs_variation_option_tooltip', $term_name, $term, $attribute, $variations, $product );
							$term_class   = $option_selected === $term->slug ? 'vi-wpvs-option-wrap vi-wpvs-option-wrap-selected' : 'vi-wpvs-option-wrap vi-wpvs-option-wrap-default';
							?>
                            <div class="<?php echo esc_attr( $term_class ); ?>"
                                 data-attribute_label="<?php echo esc_attr( $term_name ); ?>"
                                 data-attribute_value="<?php echo esc_attr( $term->slug ); ?>">
								<?php
								$option_radio_id = '"vi-wpvs-option-radio-' . $product->get_id() . '-' . $term->slug;
								?>
                                <label for="<?php echo esc_attr( $option_radio_id ); ?>" class="vi-wpvs-option">
                                    <input type="radio" value="<?php echo esc_attr( $term->slug ); ?>"
                                           class="vi-wpvs-option-radio" id="<?php echo esc_attr( $option_radio_id ); ?>"
										<?php checked( $term->slug, $option_selected ); ?> >
									<?php echo wp_kses_post( $term_name ); ?>
                                </label>
                                <div class="vi-wpvs-option-tooltip vi-wpvs-option-tooltip-<?php echo esc_attr( $attribute_tooltip_position ); ?>"
                                     data-attribute_label="<?php echo esc_attr( $term_name ); ?>">
                                    <span>
                                        <?php echo wp_kses_post( $term_tooltip ); ?>
                                    </span>
                                </div>
                            </div>
							<?php
						}
						break;
					default:
						$show_option_none_text = empty( $vi_args['show_option_none'] ) ? esc_html__( 'Choose an option', 'product-variations-swatches-for-woocommerce' ) : $vi_args['show_option_none'];
						?>
                        <div class="vi-wpvs-variation-wrap-select-wrap">
                            <div class="vi-wpvs-variation-button-select">
                        <span>
                            <?php
                            echo esc_html( $show_option_none_text );
                            ?>
                        </span>
                            </div>
                            <div class="vi-wpvs-variation-wrap-option vi-wpvs-select-hidden">
								<?php
								if ( ! empty( $vi_args['show_option_none'] ) ) {
									?>
                                    <div class="vi-wpvs-option-wrap vi-wpvs-option-wrap-default" data-attribute_value=""
                                         data-attribute_label="">
                                <span class="vi-wpvs-option vi-wpvs-option-select">
						            <?php echo esc_html( $show_option_none_text ); ?>
					            </span>
                                    </div>
									<?php
								}
								foreach ( $terms as $term ) {
									if ( ! in_array( $term->slug, $options ) ) {
										continue;
									}
									$term_name    = apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product );
									$term_tooltip = apply_filters( 'viwpvs_variation_option_tooltip', $term_name, $term, $attribute, $variations, $product );
									$term_class   = $option_selected === $term->slug ? 'vi-wpvs-option-wrap vi-wpvs-option-wrap-selected' : 'vi-wpvs-option-wrap vi-wpvs-option-wrap-default';
									?>
                                    <div class="<?php echo esc_attr( $term_class ); ?>"
                                         data-attribute_label="<?php echo esc_attr( $term_name ); ?>"
                                         data-attribute_value="<?php echo esc_attr( $term->slug ); ?>">
                                <span class="vi-wpvs-option vi-wpvs-option-select">
						            <?php echo wp_kses_post( $term_name ); ?>
					            </span>
                                    </div>
									<?php
								}
								?>
                            </div>
                        </div>
					<?php
				}
			} else {
				$attribute_options = $product->get_attribute( $attribute );
				$attribute_options = explode( '|', $attribute_options );
				$attribute_options = array_map( 'trim', $attribute_options );
				switch ( $type ) {
					case 'button':
						foreach ( $options as $k => $option ) {
							$option_name    = apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product );
							$option_tooltip = apply_filters( 'viwpvs_variation_option_tooltip', $option_name, null, $attribute, $variations, $product );
							$option_class   = ( $option_selected === $option || $option_selected === sanitize_title( $option ) ) ? 'vi-wpvs-option-wrap vi-wpvs-option-wrap-selected' : 'vi-wpvs-option-wrap vi-wpvs-option-wrap-default';
							?>
                            <div class="<?php echo esc_attr( $option_class ); ?>"
                                 data-attribute_value="<?php echo esc_attr( $option ); ?>"
                                 data-attribute_label="<?php echo esc_attr( $option_name ); ?>">
                                <span class="vi-wpvs-option vi-wpvs-option-button">
						            <?php echo wp_kses_post( $option_name ); ?>
					            </span>
                                <div class="vi-wpvs-option-tooltip vi-wpvs-option-tooltip-<?php echo esc_attr( $attribute_tooltip_position ); ?>"
                                     data-attribute_label="<?php echo esc_attr( $option_name ); ?>">
                                    <span>
                                        <?php echo wp_kses_post( $option_tooltip ); ?>
                                    </span>
                                </div>
                            </div>
							<?php
						}
						break;
					case 'color':
						foreach ( $options as $k => $option ) {
							$option_class   = ( $option_selected === $option || $option_selected === sanitize_title( $option ) ) ? 'vi-wpvs-option-wrap vi-wpvs-option-wrap-selected' : 'vi-wpvs-option-wrap vi-wpvs-option-wrap-default';
							$option_name    = apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product );
							$option_tooltip = apply_filters( 'viwpvs_variation_option_tooltip', $option_name, null, $attribute, $variations, $product );
							?>
                            <div class="<?php echo esc_attr( $option_class ); ?>"
                                 data-attribute_value="<?php echo esc_attr( $option ); ?>"
                                 data-attribute_label="<?php echo esc_attr( $option_name ); ?>">
								<?php
								$key = array_search( $option, $attribute_options );
								if ( $key !== false ) {
									$option_colors          = $colors[ $key ] ?? array();
									$option_color_separator = $color_separator[ $key ] ?? '1';
								} else {
									$option_colors          = array();
									$option_color_separator = '1';
								}
								$option_color = self::get_attribute_option_color( $option, $option_colors, $option_color_separator );
								?>
                                <span class="vi-wpvs-option vi-wpvs-option-color"
                                      data-option_color="<?php echo esc_attr( $option_color ); ?>"
                                      data-option_separator="<?php echo esc_attr( $option_color_separator ); ?>"></span>
                                <div class="vi-wpvs-option-tooltip vi-wpvs-option-tooltip-<?php echo esc_attr( $attribute_tooltip_position ); ?>"
                                     data-attribute_label="<?php echo esc_attr( $option_name ); ?>">
                                    <span>
                                        <?php echo wp_kses_post( $option_tooltip ); ?>
                                    </span>
                                </div>
                            </div>
							<?php
						}
						break;
					case 'image':
						foreach ( $options as $k => $option ) {
							$option_class   = ( $option_selected === $option || $option_selected === sanitize_title( $option ) ) ? 'vi-wpvs-option-wrap vi-wpvs-option-wrap-selected' : 'vi-wpvs-option-wrap vi-wpvs-option-wrap-default';
							$option_name    = apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product );
							$option_tooltip = apply_filters( 'viwpvs_variation_option_tooltip', $option_name, null, $attribute, $variations, $product );
							?>
                            <div class="<?php echo esc_attr( $option_class ); ?>"
                                 data-attribute_value="<?php echo esc_attr( $option ); ?>"
                                 data-attribute_label="<?php echo esc_attr( $option_name ); ?>">
								<?php
								$key = array_search( $option, $attribute_options );
								if ( $key !== false ) {
									$option_img = $img_ids[ $key ] ?? '';
								} else {
									$option_img = '';
								}
								$img_url      = $option_img ? wp_get_attachment_image_url( $option_img, 'woocommerce_gallery_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' );
								$img_loop_src = $option_img ? wp_get_attachment_image_url( $option_img, 'woocommerce_thumbnail', true ) : '';
								?>
                                <img src="<?php echo esc_url( $img_url ); ?>"
                                     alt="<?php echo esc_attr( $option ); ?>"
                                     data-loop_src="<?php echo esc_url( $img_loop_src ); ?>"
                                     class="vi-wpvs-option vi-wpvs-option-image">
                                <div class="vi-wpvs-option-tooltip vi-wpvs-option-tooltip-<?php echo esc_attr( $attribute_tooltip_position ); ?>"
                                     data-attribute_label="<?php echo esc_attr( $option_name ); ?>">
                                    <span>
                                        <?php echo wp_kses_post( $option_tooltip ); ?>
                                    </span>
                                </div>
                            </div>
							<?php
						}
						break;
					case 'variation_img':
						$variations = $product->get_children();
						foreach ( $options as $k => $option ) {
							$option_class   = ( $option_selected === $option || $option_selected === sanitize_title( $option ) ) ? 'vi-wpvs-option-wrap vi-wpvs-option-wrap-selected' : 'vi-wpvs-option-wrap vi-wpvs-option-wrap-default';
							$option_name    = apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product );
							$option_tooltip = apply_filters( 'viwpvs_variation_option_tooltip', $option_name, null, $attribute, $variations, $product );
							?>
                            <div class="<?php echo esc_attr( $option_class ); ?>"
                                 data-attribute_value="<?php echo esc_attr( $option ); ?>"
                                 data-attribute_label="<?php echo esc_attr( $option_name ); ?>">
								<?php
								$option_img = '';
								foreach ( $variations as $variation_id ) {
									if ( $option === get_post_meta( $variation_id, 'attribute_' . sanitize_title( $attribute ), true ) ) {
										$option_img = get_post_thumbnail_id( $variation_id );
										break;
									}
								}
								$img_url      = $option_img ? wp_get_attachment_image_url( $option_img, 'woocommerce_gallery_thumbnail' ) : wc_placeholder_img_src( 'woocommerce_thumbnail' );
								$img_loop_src = $option_img ? wp_get_attachment_image_url( $option_img, 'woocommerce_thumbnail', true ) : '';
								?>
                                <img src="<?php echo esc_url( $img_url ); ?>"
                                     alt="<?php echo esc_attr( $option ); ?>"
                                     data-loop_src="<?php echo esc_url( $img_loop_src ); ?>"
                                     class="vi-wpvs-option vi-wpvs-option-image">
                                <div class="vi-wpvs-option-tooltip vi-wpvs-option-tooltip-<?php echo esc_attr( $attribute_tooltip_position ); ?>"
                                     data-attribute_label="<?php echo esc_attr( $option_name ); ?>">
                                    <span>
                                        <?php echo wp_kses_post( $option_tooltip ); ?>
                                    </span>
                                </div>
                            </div>
							<?php
						}
						break;
					case 'radio':
						foreach ( $options as $k => $option ) {
							$option_name    = apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product );
							$option_tooltip = apply_filters( 'viwpvs_variation_option_tooltip', $option_name, null, $attribute, $variations, $product );
							$option_class   = ( $option_selected === $option || $option_selected === sanitize_title( $option ) ) ? 'vi-wpvs-option-wrap vi-wpvs-option-wrap-selected' : 'vi-wpvs-option-wrap vi-wpvs-option-wrap-default';
							?>
                            <div class="<?php echo esc_attr( $option_class ); ?>"
                                 data-attribute_value="<?php echo esc_attr( $option ); ?>"
                                 data-attribute_label="<?php echo esc_attr( $option_name ); ?>">
								<?php
								$option_radio_id = '"vi-wpvs-option-radio-' . $product->get_id() . '-' . $option;
								?>
                                <label for="<?php echo esc_attr( $option_radio_id ); ?>" class="vi-wpvs-option">
                                    <input type="radio" value="<?php echo esc_attr( $option ); ?>"
                                           class="vi-wpvs-option vi-wpvs-option-radio"
                                           id="<?php echo esc_attr( $option_radio_id ); ?>"
										<?php echo $option_selected === $option || $option_selected === sanitize_title( $option ) ? esc_attr( 'checked' ) : ''; ?>>
									<?php echo wp_kses_post( $option_name ); ?>
                                </label>
                                <div class="vi-wpvs-option-tooltip vi-wpvs-option-tooltip-<?php echo esc_attr( $attribute_tooltip_position ); ?>"
                                     data-attribute_label="<?php echo esc_attr( $option_name ); ?>">
                                    <span>
                                        <?php echo wp_kses_post( $option_tooltip ); ?>
                                    </span>
                                </div>
                            </div>
							<?php
						}
						break;
					default:
						$show_option_none_text = empty( $vi_args['show_option_none'] ) ? esc_html__( 'Choose an option', 'product-variations-swatches-for-woocommerce' ) : $vi_args['show_option_none'];
						?>
                        <div class="vi-wpvs-variation-wrap-select-wrap">
                            <div class="vi-wpvs-variation-button-select">
                        <span>
                            <?php echo esc_html( $show_option_none_text ); ?>
                        </span>
                            </div>
                            <div class="vi-wpvs-variation-wrap-option vi-wpvs-select-hidden">
								<?php
								if ( ! empty( $show_option_none_text ) ) {
									?>
                                    <div class="vi-wpvs-option-wrap vi-wpvs-option-wrap-default"
                                         data-attribute_value=""
                                         data-attribute_label="">
                                <span class="vi-wpvs-option vi-wpvs-option-select">
						            <?php echo esc_html( $show_option_none_text ); ?>
					            </span>
                                    </div>
									<?php
								}
								foreach ( $options as $k => $option ) {
									$option_class   = ( $option_selected === $option || $option_selected === sanitize_title( $option ) ) ? 'vi-wpvs-option-wrap vi-wpvs-option-wrap-selected' : 'vi-wpvs-option-wrap vi-wpvs-option-wrap-default';
									$option_name    = apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product );
									$option_tooltip = apply_filters( 'viwpvs_variation_option_tooltip', $option_name, null, $attribute, $variations, $product );
									?>
                                    <div class="<?php echo esc_attr( $option_class ); ?>"
                                         data-attribute_value="<?php echo esc_attr( $option ); ?>"
                                         data-attribute_label="<?php echo esc_attr( $option_name ); ?>"
                                         value="<?php echo esc_attr( $option ); ?>">
										<span class="vi-wpvs-option vi-wpvs-option-select">
                                        <?php echo wp_kses_post( $option_name ); ?>
                                        </span>
                                    </div>
									<?php
								}
								?>
                            </div>
                        </div>
					<?php
				}
			}
			?>
        </div>
		<?php
		$html = ob_get_clean();

		return $html;
	}

	public static function get_attribute_taxonomy_type( $attribute = '' ) {
		if ( ! $attribute ) {
			return 'select';
		}
		$attribute_taxonomies = wc_get_attribute_taxonomies();
		$attribute_type       = 'select';
		foreach ( $attribute_taxonomies as $item ) {
			if ( $attribute === 'pa_' . $item->attribute_name ) {
				$attribute_type = $item->attribute_type;
				break;
			}
		}

		return $attribute_type;
	}

	public function wp_enqueue_scripts() {
		if ( is_admin() ) {
			return;
		}
		if ( WP_DEBUG ) {
			wp_enqueue_style( 'vi-wpvs-frontend-style',
				VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'frontend-style.css',
				array(),
				VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'vi-wpvs-frontend-script',
				VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'frontend-script.js',
				array( 'jquery' ),
				VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION,
				true );
		} else {
			wp_enqueue_style( 'vi-wpvs-frontend-style',
				VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'frontend-style.min.css',
				array(),
				VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_script( 'vi-wpvs-frontend-script',
				VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'frontend-script.min.js',
				array( 'jquery' ),
				VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION,
				true );
		}

		$this->settings = new VI_WOO_PRODUCT_VARIATIONS_SWATCHES_DATA();
		$ids            = $this->settings->get_params( 'ids' );
		if ( $ids && is_array( $ids ) && $count_ids = count( $ids ) ) {
			$css = '';
			for ( $i = 0; $i < $count_ids; $i ++ ) {
				$id                   = $ids[ $i ];
				$reduce_mobile        = $this->settings->get_current_setting( 'attribute_reduce_size_mobile', $i );
				$attribute_height     = $this->settings->get_current_setting( 'attribute_height', $i );
				$attribute_width      = $this->settings->get_current_setting( 'attribute_width', $i );
				$attribute_transition = $this->settings->get_current_setting( 'attribute_transition', $i );

				$default_box_shadow_color = $this->settings->get_current_setting( 'attribute_default_box_shadow_color', $i );
				$default_border_color     = $this->settings->get_current_setting( 'attribute_default_border_color', $i );
				$default_border_width     = $this->settings->get_current_setting( 'attribute_default_border_width', $i );

				$hover_scale            = $this->settings->get_current_setting( 'attribute_hover_scale', $i );
				$hover_box_shadow_color = $this->settings->get_current_setting( 'attribute_hover_box_shadow_color', $i );
				$hover_border_color     = $this->settings->get_current_setting( 'attribute_hover_border_color', $i );
				$hover_border_width     = $this->settings->get_current_setting( 'attribute_hover_border_width', $i );

				$selected_scale            = $this->settings->get_current_setting( 'attribute_selected_scale', $i );
				$selected_box_shadow_color = $this->settings->get_current_setting( 'attribute_selected_box_shadow_color', $i );
				$selected_border_color     = $this->settings->get_current_setting( 'attribute_selected_border_color', $i );
				$selected_border_width     = $this->settings->get_current_setting( 'attribute_selected_border_width', $i );

				$out_of_stock = $this->settings->get_current_setting( 'attribute_out_of_stock', $i );

				$tooltip_enable       = $this->settings->get_current_setting( 'attribute_tooltip_enable', $i );
				$tooltip_type         = $this->settings->get_current_setting( 'attribute_tooltip_type', $i );
				$tooltip_position     = $this->settings->get_current_setting( 'attribute_tooltip_position', $i );
				$tooltip_border_color = $this->settings->get_current_setting( 'attribute_tooltip_border_color', $i );
				$tooltip_bg_color     = $this->settings->get_current_setting( 'attribute_tooltip_bg_color', $i );

				if ( $attribute_transition ) {
					$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap{';
					$css .= 'transition: all ' . $attribute_transition . 'ms ease-in-out;';
					$css .= '}';
				}

				//style css for style select

				$css .= $this->add_inline_style(
					'.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-variation-button-select',
					$i,
					array(
						'attribute_height',
						'attribute_width',
						'attribute_padding',
						'attribute_fontsize',
						'attribute_default_border_radius'
					),
					array( 'height', 'width', 'padding', 'font-size', 'border-radius' ),
					array( 'px', 'px', '', 'px', 'px' )
				);

				//style css for other style
				$css .= $this->add_inline_style(
					'.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap',
					$i,
					array(
						'attribute_height',
						'attribute_width',
						'attribute_padding',
						'attribute_fontsize',
						'attribute_default_border_radius'
					),
					array( 'height', 'width', 'padding', 'font-size', 'border-radius' ),
					array( 'px', 'px', '', 'px', 'px' )
				);
				$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap .vi-wpvs-option:not(.vi-wpvs-option-select){';
				$css .= 'border-radius: inherit;';
				$css .= '}';
				$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-image.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap ,';
				$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-variation_img.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap ,';
				$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-color.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap{';
				if ( ! $attribute_width ) {
					$attribute_width_t = $attribute_height ?: 48;
					$css               .= 'width: ' . $attribute_width_t . 'px;';
				}
				if ( ! $attribute_height ) {
					$attribute_height_t = $attribute_width ?: 48;
					$css                .= 'height:' . $attribute_height_t . 'px;';
				}
				$css .= '}';

				//out of stock
				switch ( $out_of_stock ) {
					case 'blur':
						$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-disable{';
						$css .= 'opacity: 1;';
						$css .= '}';
						$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-disable .vi-wpvs-option{';
						$css .= 'opacity: .5;';
						$css .= '}';
						break;
					case 'blur_icon':
						$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-disable .vi-wpvs-option{';
						$css .= 'opacity: .5;';
						$css .= '}';
						$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-disable::before,';
						$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-disable::after{';
						$css .= 'display: block;';
						$css .= '}';
						break;
					default:
						$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-disable{';
						$css .= 'display: none !important;';
						$css .= '}';
				}

				//selected styling
				$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-selected{';
				if ( $selected_border_width ) {
					if ( $selected_box_shadow_color ) {
//						$css .= 'box-shadow:  0 0 0 ' . $selected_border_width . 'px ' . $selected_border_color . ', 0px ' . ( 3 + $selected_border_width ) . 'px 2px -2px ' . $selected_box_shadow_color . ';';
						$css .= 'box-shadow:  0 0 0 ' . $selected_border_width . 'px ' . $selected_border_color . ' inset, 0px 4px 2px -2px ' . $selected_box_shadow_color . ';';
					} else {
//						$css .= 'box-shadow:  0 0 0 ' . $selected_border_width . 'px ' . $selected_border_color . ';';
						$css .= 'box-shadow:  0 0 0 ' . $selected_border_width . 'px ' . $selected_border_color . ' inset;';
					}
				} elseif ( $selected_box_shadow_color ) {
					$css .= 'box-shadow:  0px 4px 2px -2px ' . $selected_box_shadow_color . ';';
				}
				$css .= '}';
				$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-variation-wrap-select-wrap .vi-wpvs-option-wrap.vi-wpvs-option-wrap-selected{';
				if ( $selected_border_width ) {
					if ( $selected_box_shadow_color ) {
						$css .= 'box-shadow:  0 0 0 ' . $selected_border_width . 'px ' . $selected_border_color . ', 0px 4px 2px -2px ' . $selected_box_shadow_color . ';';
					} else {
						$css .= 'box-shadow:  0 0 0 ' . $selected_border_width . 'px ' . $selected_border_color . ';';
					}
				} elseif ( $selected_box_shadow_color ) {
					$css .= 'box-shadow:  0px 4px 2px -2px ' . $selected_box_shadow_color . ';';
				}
				$css .= '}';

				if ( $selected_scale && $selected_scale !== '1' ) {
					$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-selected{';
					$css .= 'transform: perspective(1px)  scale(' . $selected_scale . ') translateZ(0);';
					$css .= 'backface-visibility: hidden;';
					$css .= 'transform-style: preserve-3d;';
					$css .= '-webkit-font-smoothing: antialiased !important;';
					$css .= '-moz-osx-font-smoothing: grayscale !important;';
					$css .= 'will-change: transform;';
					$css .= '}';
				}
				$css .= $this->add_inline_style(
					'.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-selected',
					$i,
					array(
						'attribute_selected_color',
						'attribute_selected_bg_color',
						'attribute_selected_border_radius'
					),
					array( 'color', 'background', 'border-radius' ),
					array( '', '', 'px' )
				);
				$css .= $this->add_inline_style(
					'.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-selected .vi-wpvs-option',
					$i,
					array( 'attribute_selected_color' ),
					array( 'color' ),
					array( '' )
				);

				//hover styling
				$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-hover{';
				if ( $hover_border_width ) {
					if ( $hover_box_shadow_color ) {
//						$css .= 'box-shadow:  0 0 0 ' . $hover_border_width . 'px ' . $hover_border_color . ', 0px ' . ( 3 + $hover_border_width ) . 'px 2px -2px ' . $hover_box_shadow_color . ';';
						$css .= 'box-shadow:  0 0 0 ' . $hover_border_width . 'px ' . $hover_border_color . ' inset , 0px 4px 2px -2px ' . $hover_box_shadow_color . ';';
					} else {
//						$css .= 'box-shadow:  0 0 0 ' . $hover_border_width . 'px ' . $hover_border_color . ';';
						$css .= 'box-shadow:  0 0 0 ' . $hover_border_width . 'px ' . $hover_border_color . ' inset;';
					}
				} elseif ( $hover_box_shadow_color ) {
					$css .= 'box-shadow:  0px 4px 2px -2px ' . $hover_box_shadow_color . ';';
				}
				$css .= '}';
				$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-variation-wrap-select-wrap .vi-wpvs-option-wrap.vi-wpvs-option-wrap-hover{';
				if ( $hover_border_width ) {
					if ( $hover_box_shadow_color ) {
						$css .= 'box-shadow:  0 0 0 ' . $hover_border_width . 'px ' . $hover_border_color . ', 0px 4px 2px -2px ' . $hover_box_shadow_color . ';';
					} else {
						$css .= 'box-shadow:  0 0 0 ' . $hover_border_width . 'px ' . $hover_border_color . ';';
					}
				} elseif ( $hover_box_shadow_color ) {
					$css .= 'box-shadow:  0px 4px 2px -2px ' . $hover_box_shadow_color . ';';
				}
				$css .= '}';

				if ( $hover_scale && $hover_scale !== '1' ) {
					$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-hover{';
					$css .= 'transform: perspective(1px)  scale(' . $hover_scale . ') translateZ(0);';
					$css .= 'backface-visibility: hidden;';
					$css .= 'transform-style: preserve-3d;';
					$css .= '-webkit-font-smoothing: antialiased !important;';
					$css .= '-moz-osx-font-smoothing: grayscale !important;';
					$css .= 'will-change: transform;';
					$css .= '}';
				}
				$css .= $this->add_inline_style(
					'.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-hover',
					$i,
					array( 'attribute_hover_color', 'attribute_hover_bg_color', 'attribute_hover_border_radius' ),
					array( 'color', 'background', 'border-radius' ),
					array( '', '', 'px' )
				);
				$css .= $this->add_inline_style(
					'.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-hover .vi-wpvs-option',
					$i,
					array( 'attribute_hover_color' ),
					array( 'color' ),
					array( '' )
				);

				//default styling
				$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-default{';
				if ( $default_border_width ) {
					if ( $default_box_shadow_color ) {
//						$css .= 'box-shadow:  0 0 0 ' . $default_border_width . 'px ' . $default_border_color . ', 0px ' . ( 3 + $default_border_width ) . 'px 2px -2px ' . $default_box_shadow_color . ';';
						$css .= 'box-shadow:  0 0 0 ' . $default_border_width . 'px ' . $default_border_color . ' inset, 0px 4px 2px -2px ' . $default_box_shadow_color . ';';
					} else {
						$css .= 'box-shadow:  0 0 0 ' . $default_border_width . 'px ' . $default_border_color . ' inset;';
					}
				} elseif ( $default_box_shadow_color ) {
					$css .= 'box-shadow:  0px 4px 2px -2px ' . $default_box_shadow_color . ';';
				}
				$css .= '}';
				$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-variation-wrap-select-wrap .vi-wpvs-option-wrap.vi-wpvs-option-wrap-default{';
				if ( $default_border_width ) {
					if ( $default_box_shadow_color ) {
						$css .= 'box-shadow:  0 0 0 ' . $default_border_width . 'px ' . $default_border_color . ', 0px 4px 2px -2px ' . $default_box_shadow_color . ';';
					} else {
						$css .= 'box-shadow:  0 0 0 ' . $default_border_width . 'px ' . $default_border_color . ' ;';
					}
				} elseif ( $default_box_shadow_color ) {
					$css .= 'box-shadow:  0px 4px 2px -2px ' . $default_box_shadow_color . ';';
				}
				$css .= '}';

				$css .= $this->add_inline_style(
					'.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-default',
					$i,
					array(
						'attribute_default_color',
						'attribute_default_bg_color',
						'attribute_default_border_radius'
					),
					array( 'color', 'background', 'border-radius' ),
					array( '', '', 'px' )
				);
				$css .= $this->add_inline_style(
					'.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-default .vi-wpvs-option',
					$i,
					array( 'attribute_default_color' ),
					array( 'color' ),
					array( '' )
				);

				// tooltip styling
				if ( $tooltip_enable ) {
					switch ( $tooltip_type ) {
						case 'image':
							$css .= $this->add_inline_style(
								'.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap .vi-wpvs-option-tooltip',
								$i,
								array(
									'attribute_tooltip_width',
									'attribute_tooltip_height',
									'attribute_tooltip_fontsize',
									'attribute_tooltip_border_radius'
								),
								array( 'width', 'height', 'font-size', 'border-radius' ),
								array( 'px', 'px', 'px', 'px' )
							);
							break;
						default:
							$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap .vi-wpvs-option-tooltip{';
							$css .= 'min-width: 100px;';
							$css .= 'height: auto;';
							$css .= 'padding: 5px 8px;';
							$css .= '}';
							$css .= $this->add_inline_style(
								'.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap .vi-wpvs-option-tooltip',
								$i,
								array( 'attribute_tooltip_fontsize', 'attribute_tooltip_border_radius' ),
								array( 'font-size', 'border-radius' ),
								array( 'px', 'px' )
							);
							$css .= $this->add_inline_style(
								'.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap .vi-wpvs-option-tooltip',
								$i,
								array( 'attribute_tooltip_color', 'attribute_tooltip_bg_color' ),
								array( 'color', 'background' ),
								array( '', '' )
							);
					}
					if ( $tooltip_bg_color ) {
						$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap .vi-wpvs-option-tooltip::after{';
						$css .= 'border-width: 5px;';
						$css .= 'border-style: solid;';
						switch ( $tooltip_position ) {
							case 'bottom':
								$css .= 'margin-left: -5px;';
								$css .= 'margin-top: -1px;';
								$css .= 'border-color:  transparent transparent ' . $tooltip_bg_color . ' transparent;';
								break;
							case 'left':
								$css .= 'margin-left: -1px;';
								$css .= 'margin-top: -5px;';
								$css .= 'border-color:  transparent transparent transparent ' . $tooltip_bg_color . ' ;';
								break;
							case 'right':
								$css .= 'margin-left: -1px;';
								$css .= 'margin-top: -5px;';
								$css .= 'border-color:  transparent ' . $tooltip_bg_color . ' transparent  transparent;';
								break;
							default:
								$css .= 'margin-left: -5px;';
								$css .= 'margin-top: -1px;';
								$css .= 'border-color: ' . $tooltip_bg_color . ' transparent transparent transparent;';
						}
						$css .= '}';
					}
					if ( $tooltip_border_color ) {
						$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap .vi-wpvs-option-tooltip{';
						$css .= 'border: 1px solid ' . $tooltip_border_color . ';';
						$css .= '}';
						$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap .vi-wpvs-option-tooltip::before{';
						$css .= 'border-width: 6px;';
						$css .= 'border-style: solid;';
						switch ( $tooltip_position ) {
							case 'bottom':
								$css .= 'margin-left: -6px;';
								$css .= 'border-color:  transparent transparent ' . $tooltip_border_color . ' transparent;';
								break;
							case 'left':
								$css .= 'margin-top: -6px;';
								$css .= 'border-color:  transparent transparent transparent ' . $tooltip_border_color . ' ;';
								break;
							case 'right':
								$css .= 'margin-top: -6px;';
								$css .= 'border-color:  transparent ' . $tooltip_border_color . ' transparent  transparent;';
								break;
							default:
								$css .= 'margin-left: -6px;';
								$css .= 'border-color: ' . $tooltip_border_color . ' transparent transparent transparent;';
						}
						$css .= '}';
					}
				} else {
					$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap .vi-wpvs-option-tooltip{';
					$css .= 'display: none;';
					$css .= '}';
				}

				if ( $reduce_mobile ) {
					$css .= '@media screen and (max-width:600px){';
					$css .= $this->add_inline_style_reduce(
						'.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap',
						$i,
						array( 'attribute_width', 'attribute_height', 'attribute_fontsize' ),
						array( 'width', 'height', 'font-size' ),
						array( 'px', 'px', 'px' ),
						$reduce_mobile
					);
					$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-image.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap ,';
					$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-variation_img.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap ,';
					$css .= '.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-color.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap{';
					if ( ! $attribute_width ) {
						$attribute_width_t = $attribute_height ? ( $attribute_height * $reduce_mobile / 100 ) : 48 * $reduce_mobile / 100;
						$css               .= 'width: ' . $attribute_width_t . 'px;';
					}
					if ( ! $attribute_height ) {
						$attribute_height_t = $attribute_width ? ( $attribute_width * $reduce_mobile / 100 ) : 48 * $reduce_mobile / 100;
						$css                .= 'height:' . $attribute_height_t . 'px;';
					}
					$css .= '}';
					$css .= $this->add_inline_style_reduce(
						'.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-selected',
						$i,
						array( 'attribute_selected_border_radius' ),
						array( 'border-radius' ),
						array( 'px' ),
						$reduce_mobile
					);
					$css .= $this->add_inline_style_reduce(
						'.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-hover',
						$i,
						array( 'attribute_hover_border_radius' ),
						array( 'border-radius' ),
						array( 'px' ),
						$reduce_mobile
					);
					$css .= $this->add_inline_style_reduce(
						'.vi-wpvs-variation-wrap.vi-wpvs-variation-wrap-' . $id . ' .vi-wpvs-option-wrap.vi-wpvs-option-wrap-default',
						$i,
						array( 'attribute_default_border_radius' ),
						array( 'border-radius' ),
						array( 'px' ),
						$reduce_mobile
					);
					$css .= '}';
				}
			}
			wp_add_inline_style( 'vi-wpvs-frontend-style', $css );
		}
	}

	private function add_inline_style( $element, $i, $name, $style, $suffix = '' ) {
		$return = $element . '{';
		if ( is_array( $name ) && count( $name ) ) {
			foreach ( $name as $key => $value ) {
				$get_value  = $this->settings->get_current_setting( $name[ $key ], $i );
				$get_suffix = isset( $suffix[ $key ] ) ? $suffix[ $key ] : '';
				if ( $get_value ) {
					$return .= $style[ $key ] . ':' . $get_value . $get_suffix . ';';
				}
			}
		}
		$return .= '}';

		return $return;
	}

	private function add_inline_style_reduce( $element, $i, $name, $style, $suffix = '', $reduce = 0, $default = 0 ) {
		$return = $element . '{';
		if ( is_array( $name ) && count( $name ) ) {
			foreach ( $name as $key => $value ) {
				$get_value = $this->settings->get_current_setting( $name[ $key ], $i );
				if ( $reduce > 0 && $get_value ) {
					if ( $default > 0 ) {
						$get_value = $get_value * $default / 100;
					}
					$get_value = $get_value * $reduce / 100;
				}
				$return .= $style[ $key ] . ':' . $get_value . $suffix[ $key ] . ';';
			}
		}
		$return .= '}';

		return $return;
	}
}