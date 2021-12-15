<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class VI_WOO_PRODUCT_VARIATIONS_SWATCHES_Admin_Global_Attributes {
	protected $settings;

	function __construct() {
		$this->settings = new VI_WOO_PRODUCT_VARIATIONS_SWATCHES_DATA();
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 99 );
		add_filter( 'product_attributes_type_selector', array( $this, 'product_attributes_type_selector' ), 10, 1 );
		add_action( 'woocommerce_after_add_attribute_fields', array(
			$this,
			'woocommerce_after_add_attribute_fields'
		) );
		add_action( 'woocommerce_after_edit_attribute_fields', array(
			$this,
			'woocommerce_after_edit_attribute_fields'
		) );
		add_action( 'woocommerce_attribute_updated', array( $this, 'woocommerce_attribute_updated' ), 99, 3 );
		add_action( 'woocommerce_attribute_added', array( $this, 'woocommerce_attribute_added' ), 99, 2 );

		add_action( 'create_term', array( $this, 'save_term' ), 10, 3 );
		add_action( 'edited_term', array( $this, 'save_term' ), 10, 3 );
	}

	public function woocommerce_attribute_added( $id, $data ) {
		global $vi_wpvs_settings;
		$vi_attribute_profile                                     = isset( $_POST['attribute_vi_profile'] ) ? sanitize_text_field( $_POST['attribute_vi_profile'] ) : '';
		$attribute_vi_display_type                                = isset( $_POST['attribute_vi_display_type'] ) ? sanitize_text_field( $_POST['attribute_vi_display_type'] ) : '';
		$args                                                     = array();
		$taxonomy_profiles                                        = isset( $vi_wpvs_settings['taxonomy_profiles'] ) ? $vi_wpvs_settings['taxonomy_profiles'] : array();
		$taxonomy_display_type                                    = isset( $vi_wpvs_settings['taxonomy_display_type'] ) ? $vi_wpvs_settings['taxonomy_display_type'] : array();
		$taxonomy_profiles[ 'pa_' . $data['attribute_name'] ]     = $vi_attribute_profile;
		$taxonomy_display_type[ 'pa_' . $data['attribute_name'] ] = $attribute_vi_display_type;
		$args ['taxonomy_profiles']                               = $taxonomy_profiles;
		$args ['taxonomy_display_type']                           = $taxonomy_display_type;
		$args                                                     = wp_parse_args( $args, get_option( 'vi_woo_product_variation_swatches_params', $vi_wpvs_settings ) );
		update_option( 'vi_woo_product_variation_swatches_params', $args );
		$vi_wpvs_settings = $args;
	}

	public function woocommerce_attribute_updated( $id, $data, $old_slug ) {
		global $vi_wpvs_settings;
		$vi_attribute_profile      = isset( $_POST['attribute_vi_profile'] ) ? sanitize_text_field( $_POST['attribute_vi_profile'] ) : '';
		$attribute_vi_display_type = isset( $_POST['attribute_vi_display_type'] ) ? sanitize_text_field( $_POST['attribute_vi_display_type'] ) : '';
		$args                      = array();
		$taxonomy_profiles         = isset( $vi_wpvs_settings['taxonomy_profiles'] ) ? $vi_wpvs_settings['taxonomy_profiles'] : array();
		$taxonomy_display_type     = isset( $vi_wpvs_settings['taxonomy_display_type'] ) ? $vi_wpvs_settings['taxonomy_display_type'] : array();
		unset( $taxonomy_profiles[ 'pa_' . $old_slug ] );
		$taxonomy_profiles[ 'pa_' . $data['attribute_name'] ] = $vi_attribute_profile;
		unset( $taxonomy_display_type[ 'pa_' . $old_slug ] );
		$taxonomy_display_type[ 'pa_' . $data['attribute_name'] ] = $attribute_vi_display_type;
		$args ['taxonomy_profiles']                               = $taxonomy_profiles;
		$args ['taxonomy_display_type']                           = $taxonomy_display_type;
		$args                                                     = wp_parse_args( $args, get_option( 'vi_woo_product_variation_swatches_params', $vi_wpvs_settings ) );
		update_option( 'vi_woo_product_variation_swatches_params', $args );
		$vi_wpvs_settings = $args;
	}

	public function product_attributes_type_selector( $selector ) {
		$new_selector                  = array();
		$new_selector['button']        = esc_html__( 'Button', 'product-variations-swatches-for-woocommerce' );
		$new_selector['color']         = esc_html__( 'Color', 'product-variations-swatches-for-woocommerce' );
		$new_selector['image']         = esc_html__( 'Image', 'product-variations-swatches-for-woocommerce' );
		$new_selector['variation_img'] = esc_html__( 'Variation Image', 'product-variations-swatches-for-woocommerce' );
		$new_selector['radio']         = esc_html__( 'Radio', 'product-variations-swatches-for-woocommerce' );
		$new_selector['viwpvs_default']         = esc_html__( 'Theme Default', 'product-variations-swatches-for-woocommerce'  );
		$selector                      = array_merge( $new_selector, $selector );

		return $selector;
	}

	public function woocommerce_after_add_attribute_fields() {
		$vi_wpvs_ids   = $this->settings->get_params( 'ids' );
		$vi_wpvs_names = $this->settings->get_params( 'names' );
		?>
        <div class="vi-wpvs-swatches-setting-wrap">
            <div class="form-field">
                <h2> <?php esc_html_e( 'Swatches settings', 'product-variations-swatches-for-woocommerce' ); ?></h2>
            </div>
            <div class="form-field">
                <label>
                    <input type="checkbox" value="1" disabled/> <?php esc_html_e( 'Show in product list', 'product-variations-swatches-for-woocommerce' ); ?>
                </label>

                <p class="description">
                    <?php esc_html_e( 'Enable this if you want this attribute to show in product list. Please update Pro version to unlock this feature', 'product-variations-swatches-for-woocommerce' ); ?>
                </p>
            </div>
            <div class="form-field">
                <label for="attribute_vi_display_type">
					<?php esc_html_e( 'Display style', 'product-variations-swatches-for-woocommerce' ); ?>
                </label>
                <select name="attribute_vi_display_type" id="attribute_vi_display_type">
                    <option value="vertical">
						<?php esc_html_e( 'Vertical', 'product-variations-swatches-for-woocommerce' ); ?>
                    </option>
                    <option value="horizontal">
						<?php esc_html_e( 'Horizontal', 'product-variations-swatches-for-woocommerce' ); ?>
                    </option>
                </select>
            </div>
            <div class="form-field">
                <label for="attribute_vi_profile">
					<?php esc_html_e( 'Design profile', 'product-variations-swatches-for-woocommerce' ); ?>
                </label>
                <select name="attribute_vi_profile" id="attribute_vi_profile">
					<?php
					foreach ( $vi_wpvs_ids as $k => $v ) {
						?>
                        <option value="<?php echo esc_attr( $v ) ?>"><?php echo esc_html( $vi_wpvs_names[ $k ] ); ?></option>
						<?php
					}
					?>
                </select>
            </div>
        </div>
		<?php
	}

	public function woocommerce_after_edit_attribute_fields() {
		global $wpdb;
		$this->settings           = new  VI_WOO_PRODUCT_VARIATIONS_SWATCHES_DATA();
		$attribute_id             = isset( $_GET['edit'] ) ? absint( sanitize_text_field( $_GET['edit'] ) ) : 0;
		$attribute_slug           = $wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT attribute_name
				FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_id = %d
				",
				$attribute_id
			)
		);
		$vi_wpvs_ids              = $this->settings->get_params( 'ids' );
		$vi_wpvs_names            = $this->settings->get_params( 'names' );
		$vi_attribute_profiles    = $this->settings->get_params( 'taxonomy_profiles' );
		$taxonomy_display_type    = $this->settings->get_params( 'taxonomy_display_type' );
		$vi_attribute_profile     = isset( $vi_attribute_profiles[ 'pa_' . $attribute_slug ] ) ? $vi_attribute_profiles[ 'pa_' . $attribute_slug ] : '';
		$vi_taxonomy_display_type = isset( $taxonomy_display_type[ 'pa_' . $attribute_slug ] ) ? $taxonomy_display_type[ 'pa_' . $attribute_slug ] : '';
		?>
        <tr class="form-field form-required vi-wpvs-swatches-setting-wrap">
            <th scope="row" valign="top" colspan="2">
                <label>
					<?php esc_html_e( 'Swatches settings', 'product-variations-swatches-for-woocommerce' ); ?>
                </label>
            </th>
        </tr>
        <tr class="form-field form-required vi-wpvs-swatches-setting-wrap">
            <th scope="row" valign="top">
                <label>
					<?php esc_html_e( 'Show in product list', 'product-variations-swatches-for-woocommerce' ); ?>
                </label>
            </th>
            <td>
                <label for="attribute_vi_loop_enable">
                    <input type="checkbox" value="1" disabled/>
                </label>

                <p class="description">
                    <?php esc_html_e( 'Enable this if you want this attribute to show in product list. Please update Pro version to unlock this feature', 'product-variations-swatches-for-woocommerce' ); ?>
                </p>
            </td>
        </tr>
        <tr class="form-field form-required vi-wpvs-swatches-setting-wrap">
            <th scope="row">
                <label for="attribute_vi_display_type">
					<?php esc_html_e( 'Display style', 'product-variations-swatches-for-woocommerce' ); ?>
                </label>
            </th>
            <td>
                <select name="attribute_vi_display_type" id="attribute_vi_display_type">
                    <option value="vertical" <?php selected( $vi_taxonomy_display_type, 'vertical' ) ?> >
						<?php esc_html_e( 'Vertical', 'product-variations-swatches-for-woocommerce' ); ?>
                    </option>
                    <option value="horizontal" <?php selected( $vi_taxonomy_display_type, 'horizontal' ) ?> >
						<?php esc_html_e( 'Horizontal', 'product-variations-swatches-for-woocommerce' ); ?>
                    </option>
                </select>
            </td>
        </tr>
        <tr class="form-field form-required vi-wpvs-swatches-setting-wrap">
            <th scope="row" valign="top">
                <label for="attribute_vi_profile">
					<?php esc_html_e( 'Design profile', 'product-variations-swatches-for-woocommerce' ); ?>
                </label>
            </th>
            <td>
                <select name="attribute_vi_profile" id="attribute_vi_profile">
					<?php
					foreach ( $vi_wpvs_ids as $k => $v ) {
						?>
                        <option value="<?php echo esc_attr( $v ) ?>" <?php selected( $vi_attribute_profile,
							$v ) ?>><?php echo esc_html( $vi_wpvs_names[ $k ] ); ?></option>
						<?php
					}
					?>
                </select>
            </td>
        </tr>
		<?php
	}


	/*term of taxonomy */
	public function save_term( $term_id, $tt_id, $taxonomy ) {
		if ( 'pa_' !== substr( $taxonomy, 0, 3 ) ) {
			return;
		}
		$args                    = array();
		$args['type']            = isset( $_POST['vi_wpvs_term_type'] ) ? sanitize_text_field( $_POST['vi_wpvs_term_type'] ) : '';
		$args['img_id']          = isset( $_POST['vi_wpvs_term_image'] ) ? sanitize_text_field( $_POST['vi_wpvs_term_image'] ) : '';
		$args['color']           = isset( $_POST['vi_wpvs_term_color'] ) ? array_map( 'sanitize_text_field', $_POST['vi_wpvs_term_color'] ) : array();
		$args['color_separator'] = isset( $_POST['vi_wpvs_term_color_separator'] ) ? sanitize_text_field( $_POST['vi_wpvs_term_color_separator'] ) : '';
		$args                    = wp_parse_args( $args, get_term_meta( $term_id, 'vi_wpvs_terms_params', true ) );
		update_term_meta( $term_id, 'vi_wpvs_terms_params', $args );
	}

	public function global_attribute_edit_form_fields() {
		$taxonomy_name = isset( $_GET['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) : '';
		if ( ! $taxonomy_name ) {
			return;
		}
		global $wpdb;
		$tag_ID                        = isset( $_GET['tag_ID'] ) ? absint( wp_unslash( $_GET['tag_ID'] ) ) : '';
		$attribute_name                = substr( $taxonomy_name, 3 );
		$attribute_type                = $wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT attribute_type
				FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s
				",
				$attribute_name
			)
		);
		$vi_wpvs_terms_settings        = get_term_meta( $tag_ID, 'vi_wpvs_terms_params', true );
		?>
        <input type="hidden" name="vi_wpvs_term_type" id="vi_wpvs_term_type"
               value="<?php echo esc_attr( $attribute_type ); ?>">
		<?php
		switch ( $attribute_type ) {
			case 'color':
				$terms_color =  $vi_wpvs_terms_settings['color'] ?? array();
				$terms_color_separator = $vi_wpvs_terms_settings['color_separator'] ?? '1';
				?>
                <tr class="form-field form-required vi-wpvs-swatches-setting-wrap">
                    <th scope="row" colspan="2">
                        <label>
							<?php esc_html_e( 'Swatches settings', 'product-variations-swatches-for-woocommerce' ); ?>
                        </label>
                    </th>
                </tr>
                <tr class="form-field term-description-wrap vi-wpvs-swatches-setting-wrap">
                    <th scope="row">
                        <label><?php esc_html_e( 'Color', 'product-variations-swatches-for-woocommerce' ); ?></label>
                    </th>
                    <td class="vi-wpvs-term-color-container-wrap">
                        <table cellspacing="0" cellpadding="0">
                            <tr>
                                <th><?php esc_html_e( 'Color', 'product-variations-swatches-for-woocommerce' ); ?></th>
                                <th><?php esc_html_e( 'Action', 'product-variations-swatches-for-woocommerce' ); ?></th>
                            </tr>
							<?php
							if ( $terms_color && is_array( $terms_color ) && count( $terms_color ) ) {
								foreach ( $terms_color as $color ) {
									?>
                                    <tr>
                                        <td>
                                            <input type="text"
                                                   class="vi-wpvs-color vi_wpvs_term_color"
                                                   name="vi_wpvs_term_color[]"
                                                   value="<?php echo esc_attr( $color ) ?>">
                                        </td>
                                        <td>
					                        <span class="vi-wpvs-term-color-action-clone button button-primary button-small">
						                        <?php esc_html_e( 'Clone', 'product-variations-swatches-for-woocommerce' ) ?>
					                        </span>
                                            <span class="vi-wpvs-term-color-action-remove button button-secondary delete button-small">
		                                        <?php esc_html_e( 'Remove', 'product-variations-swatches-for-woocommerce' ) ?>
					                        </span>
                                        </td>
                                    </tr>
									<?php
								}
							} else {
								?>
                                <tr>
                                    <td>
                                        <input type="text"
                                               class="vi-wpvs-color vi_wpvs_term_color"
                                               name="vi_wpvs_term_color[]">
                                    </td>
                                    <td>
					                    <span class="vi-wpvs-term-color-action-clone button button-primary button-small">
						                    <?php esc_html_e( 'Clone', 'product-variations-swatches-for-woocommerce' ) ?>
					                    </span>
                                        <span class="vi-wpvs-term-color-action-remove button button-secondary delete button-small">
						                    <?php esc_html_e( 'Remove', 'product-variations-swatches-for-woocommerce' ) ?>
					                    </span>
                                    </td>
                                </tr>
								<?php
							}
							?>
                        </table>
                    </td>
                </tr>
                <tr class="form-field term-description-wrap vi-wpvs-swatches-setting-wrap">
                    <th scope="row">
                        <label for="vi_wpvs_term_color_separator">
							<?php esc_html_e( 'Color separator', 'product-variations-swatches-for-woocommerce' ); ?>
                        </label>
                    </th>
                    <td>
                        <select name="vi_wpvs_term_color_separator" id="vi_wpvs_term_color_separator"
                                class="vi_wpvs_term_color_separator">
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
				<?php
				break;
			case 'image':
				$terms_img = isset( $vi_wpvs_terms_settings['img_id'] ) ? absint( $vi_wpvs_terms_settings['img_id'] ) : '';
				$placeholder_img_src = wc_placeholder_img_src( 'woocommerce_gallery_thumbnail' );
				$terms_img_src          = $terms_img ? wp_get_attachment_image_url( $terms_img, 'woocommerce_thumbnail', true ) : $placeholder_img_src;
				?>
                <tr class="form-field form-required vi-wpvs-swatches-setting-wrap">
                    <th scope="row" colspan="2">
                        <label>
							<?php esc_html_e( 'Swatches settings', 'product-variations-swatches-for-woocommerce' ); ?>
                        </label>
                    </th>
                </tr>
                <tr class="form-field term-description-wrap vi-wpvs-swatches-setting-wrap">
                    <th scope="row" class="vi-wpvs-term-image-upload-img">
                        <label for="">
							<?php esc_html_e( 'Image', 'product-variations-swatches-for-woocommerce' ); ?>
                        </label>
                    </th>
                    <td>
                        <input type="hidden" name="vi_wpvs_term_image" id="vi_wpvs_term_image" class="vi_wpvs_term_image"
                               value="<?php echo esc_attr( $terms_img ); ?>">
                        <div class="vi-wpvs-term-image-wrap vi-wpvs-term-edit-image-wrap vi-wpvs-term-image-upload-img">
	                        <span class="vi-wpvs-term-edit-image-preview vi-wpvs-term-image-preview">
                                <img src="<?php echo esc_attr($terms_img_src); ?>" data-src_placeholder="<?php echo esc_attr($placeholder_img_src); ?>">
                            </span>
                            <span class="vi-wpvs-term-image-remove dashicons dashicons-dismiss<?php echo $terms_img ? '': esc_attr(' vi-wpvs-hidden'); ?>"></span>
                            <div class="vi-wpvs-term-image-add-new"><?php esc_html_e( 'Upload / Add image', 'product-variations-swatches-for-woocommerce' ); ?></div>
                        </div>
                        <p class="description">
							<?php esc_html_e( 'Choose an image', 'product-variations-swatches-for-woocommerce' ); ?>
                        </p>
                    </td>
                </tr>
				<?php
				break;
			default:
		}
	}

	public function global_attribute_add_form_fields() {
		$taxonomy_name = isset( $_GET['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_GET['taxonomy'] ) ) : '';
		if ( ! $taxonomy_name ) {
			return;
		}
		global $wpdb;
		$attribute_name = substr( $taxonomy_name, 3 );
		$attribute_type = $wpdb->get_var(
			$wpdb->prepare(
				"
				SELECT attribute_type
				FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name = %s
				",
				$attribute_name
			)
		);
		?>
        <input type="hidden" name="vi_wpvs_term_type" id="vi_wpvs_term_type"
               value="<?php echo esc_attr( $attribute_type ); ?>">
		<?php
		switch ( $attribute_type ) {
			case 'color':
				?>
                <div class="vi-wpvs-swatches-setting-wrap">
                    <div class="form-field term-description-wrap">
						<?php esc_html_e( 'Swatches settings', 'product-variations-swatches-for-woocommerce' ); ?>
                    </div>
                    <div class="form-field term-description-wrap">
                        <label for="">
							<?php esc_html_e( 'Color', 'product-variations-swatches-for-woocommerce' ); ?>
                        </label>
                        <div class="vi-wpvs-term-color-container-wrap">
                            <table cellspacing="0" cellpadding="0">
                                <tr>
                                    <th><?php esc_html_e( 'Color', 'product-variations-swatches-for-woocommerce' ); ?></th>
                                    <th><?php esc_html_e( 'Action', 'product-variations-swatches-for-woocommerce' ); ?></th>
                                </tr>
                                <tr>
                                    <td>
                                        <input type="text"
                                               class="vi-wpvs-color vi_wpvs_term_color"
                                               name="vi_wpvs_term_color[]">
                                    </td>
                                    <td>
					                    <span class="vi-wpvs-term-color-action-clone button button-primary button-small">
						                    <?php esc_html_e( 'Clone', 'product-variations-swatches-for-woocommerce' ); ?>
					                    </span>
                                        <span class="vi-wpvs-term-color-action-remove button button-secondary delete button-small">
						                    <?php esc_html_e( 'Remove', 'product-variations-swatches-for-woocommerce' ); ?>
					                    </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="form-field term-description-wrap">
                        <label for="vi_wpvs_term_color_separator">
							<?php esc_html_e( 'Color separator', 'product-variations-swatches-for-woocommerce' ); ?>
                        </label>

                        <select name="vi_wpvs_term_color_separator" id="vi_wpvs_term_color_separator"
                                class="vi_wpvs_term_color_separator">
                            <option value="1">
								<?php esc_html_e( 'Basic horizontal', 'product-variations-swatches-for-woocommerce' ); ?>
                            </option>
                            <option value="2">
								<?php esc_html_e( 'Basic vertical', 'product-variations-swatches-for-woocommerce' ); ?>
                            </option>
                            <option value="3">
								<?php esc_html_e( 'Basic diagonal left', 'product-variations-swatches-for-woocommerce' ); ?>
                            </option>
                            <option value="4">
								<?php esc_html_e( 'Basic diagonal right', 'product-variations-swatches-for-woocommerce' ); ?>
                            </option>
                            <option value="5">
								<?php esc_html_e( 'Hard lines horizontal', 'product-variations-swatches-for-woocommerce' ); ?>
                            </option>
                            <option value="6">
								<?php esc_html_e( 'Hard lines vertical', 'product-variations-swatches-for-woocommerce' ); ?>
                            </option>
                            <option value="7">
								<?php esc_html_e( 'Hard lines diagonal left', 'product-variations-swatches-for-woocommerce' ); ?>
                            </option>
                            <option value="8">
								<?php esc_html_e( 'Hard lines diagonal right', 'product-variations-swatches-for-woocommerce' ); ?>
                            </option>
                        </select>
                    </div>
                </div>
				<?php
				break;
			case 'image':
				$placeholder_img_src = wc_placeholder_img_src( 'woocommerce_gallery_thumbnail' );
				?>
                <div class="vi-wpvs-swatches-setting-wrap">
                    <div class="form-field term-description-wrap">
						<?php esc_html_e( 'Swatches settings', 'product-variations-swatches-for-woocommerce' ); ?>
                    </div>
                    <div class="form-field term-description-wrap">
                        <label for="" class="vi-wpvs-term-image-upload-img">
							<?php esc_html_e( 'Image', 'product-variations-swatches-for-woocommerce' ); ?>
                        </label>
                        <input type="hidden" name="vi_wpvs_term_image" id="vi_wpvs_term_image" class="vi_wpvs_term_image" value="">
                        <div class="vi-wpvs-term-image-wrap vi-wpvs-term-add-image-wrap vi-wpvs-term-image-upload-img">
	                        <span class="vi-wpvs-term-add-image-preview vi-wpvs-term-image-preview">
                                <img src="<?php echo esc_attr($placeholder_img_src); ?>" data-src_placeholder="<?php echo esc_attr($placeholder_img_src); ?>">
                            </span>
                            <span class="vi-wpvs-term-image-remove dashicons dashicons-dismiss vi-wpvs-hidden"></span>
                            <div class="vi-wpvs-term-image-add-new"><?php esc_html_e( 'Upload / Add image', 'product-variations-swatches-for-woocommerce' ); ?></div>
                        </div>
                        <p><?php esc_html_e( 'Choose an image', 'product-variations-swatches-for-woocommerce' ); ?></p>
                    </div>
                </div>
				<?php
				break;
			default:
		}
	}

	public function global_attribute_taxonomy_columns( $columns ) {
		$args = array();
		if ( isset( $columns['cb'] ) ) {
			$args['cb'] = $columns['cb'];
			unset( $columns['cb'] );
		}
		$args['vi-wpvs-term-preview'] = '';

		return $columns;
	}

	public function global_attribute_custom_column( $contents, $column_name, $term_id ) {
		$vi_wpvs_terms_settings = get_term_meta( $term_id, 'vi_wpvs_terms_params', true );
		if ( $column_name === 'vi-wpvs-term-preview' && $vi_wpvs_terms_settings && is_array( $vi_wpvs_terms_settings ) && isset( $vi_wpvs_terms_settings['type'] ) ) {

		}

		return $contents;
	}

	public function admin_enqueue_scripts() {
		$screen = get_current_screen();
		if ( $screen->id === 'product_page_product_attributes' || ( 'pa_' === substr( $screen->taxonomy, 0, 3 ) ) ) {

			wp_enqueue_script( 'product-variations-swatches-for-woocommerce-admin-global-attributes',
				VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'admin-global-attributes.js',
				array( 'jquery' ) );
			wp_enqueue_script( 'product-variations-swatches-for-woocommerce-admin-minicolors', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_JS . 'minicolors.min.js', array( 'jquery' ), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			wp_enqueue_style( 'product-variations-swatches-for-woocommerce-admin-global-attributes',
				VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'admin-global-attributes.css' );
			wp_enqueue_style( 'product-variations-swatches-for-woocommerce-admin-minicolors', VI_WOO_PRODUCT_VARIATIONS_SWATCHES_CSS . 'minicolors.css', array(), VI_WOO_PRODUCT_VARIATIONS_SWATCHES_VERSION );
			$args = array(
				'ajax_url'                  => admin_url( 'admin-ajax.php' ),
				'settings_default_color'    => $this->settings->get_default_color(),
				'global_setting_url'        => admin_url( 'admin.php?page=woocommerce-product-variations-swatches-global-attrs' ),
				'global_setting_title'      => esc_html__( 'Swatches settings', 'product-variations-swatches-for-woocommerce' ),
				'global_item_setting_title' => esc_html__( 'Swatches settings', 'product-variations-swatches-for-woocommerce' ),
				'remove_item'               => esc_html__( 'Would you want to remove this?', 'product-variations-swatches-for-woocommerce' ),
				'remove_last_item'          => esc_html__( 'You can not remove the last item.', 'product-variations-swatches-for-woocommerce' ),
			);
			if ( $screen->taxonomy ) {
				wp_enqueue_media();
				add_action( $screen->taxonomy . '_edit_form_fields', array(
					$this,
					'global_attribute_edit_form_fields'
				) );
				add_action( $screen->taxonomy . '_add_form_fields', array(
					$this,
					'global_attribute_add_form_fields'
				) );
				$args['taxonomy']                  = substr( $screen->taxonomy, 3 );
				$args['global_attr_setting_title'] = esc_html__( 'Swatches settings', 'product-variations-swatches-for-woocommerce' );
			}
			wp_localize_script( 'product-variations-swatches-for-woocommerce-admin-global-attributes', 'vi_wpvs_admin_global_attributes', $args );
		}
	}
}