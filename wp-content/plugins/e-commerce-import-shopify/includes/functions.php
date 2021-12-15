<?php
/**
 * Function include all files in folder
 *
 * @param $path   Directory address
 * @param $ext    array file extension what will include
 * @param $prefix string Class prefix
 */
if ( ! function_exists( 'vi_include_folder' ) ) {
	function vi_include_folder( $path, $prefix = '', $ext = array( 'php' ) ) {

		/*Include all files in payment folder*/
		if ( ! is_array( $ext ) ) {
			$ext = explode( ',', $ext );
			$ext = array_map( 'trim', $ext );
		}
		$sfiles = scandir( $path );
		foreach ( $sfiles as $sfile ) {
			if ( $sfile != '.' && $sfile != '..' ) {
				if ( is_file( $path . "/" . $sfile ) ) {
					$ext_file  = pathinfo( $path . "/" . $sfile );
					$file_name = $ext_file['filename'];
					if ( $ext_file['extension'] ) {
						if ( in_array( $ext_file['extension'], $ext ) ) {
							$class = preg_replace( '/\W/i', '_', $prefix . ucfirst( $file_name ) );

							if ( ! class_exists( $class ) ) {
								require_once $path . $sfile;
								if ( class_exists( $class ) ) {
									new $class;
								}
							}
						}
					}
				}
			}
		}
	}
}

if ( ! function_exists( 's2w_get_placeholder_image' ) ) {
	function s2w_get_placeholder_image() {
		$placeholder_image = get_option( 's2w_woocommerce_placeholder_img_src', 0 );

		/*Validate current setting if set. If set, return.*/
		if ( ! empty( $placeholder_image ) ) {
			if ( ! is_numeric( $placeholder_image ) ) {
				return $placeholder_image;
			} elseif ( $placeholder_image && wp_attachment_is_image( $placeholder_image ) ) {
				return $placeholder_image;
			}
		}

		$upload_dir = wp_upload_dir();
		$source     = VI_IMPORT_SHOPIFY_TO_WOOCOMMERCE_IMAGES_DIR . 's2w-placeholder.jpg';
		$filename   = $upload_dir['basedir'] . '/s2w-placeholder.png';

		if ( ! file_exists( $filename ) ) {
			copy( $source, $filename ); // @codingStandardsIgnoreLine.
		}

		if ( ! file_exists( $filename ) ) {
			update_option( 's2w_woocommerce_placeholder_img_src', 0 );

			return 0;
		}

		$filetype   = wp_check_filetype( basename( $filename ), null );
		$attachment = array(
			'guid'           => $upload_dir['url'] . '/' . basename( $filename ),
			'post_mime_type' => $filetype['type'],
			'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);
		$attach_id  = wp_insert_attachment( $attachment, $filename );

		update_option( 's2w_woocommerce_placeholder_img_src', $attach_id );

		/*Make sure that this file is included, as wp_generate_attachment_metadata() depends on it.*/
		require_once ABSPATH . 'wp-admin/includes/image.php';

		/*Generate the metadata for the attachment, and update the database record.*/
		$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
		wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}
}
if ( ! function_exists( 'vi_s2w_init_set' ) ) {
	function vi_s2w_init_set() {
		ini_set( 'memory_limit', '3000M' );
		ini_set( 'max_execution_time', '3000' );
		ini_set( 'max_input_time', '3000' );
		ini_set( 'default_socket_timeout', '3000' );
		vi_s2w_set_time_limit();
	}
}
if ( ! function_exists( 'vi_s2w_set_time_limit' ) ) {
	function vi_s2w_set_time_limit( $limit = 0 ) {
		if ( function_exists( 'set_time_limit' ) && false === strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) { // phpcs:ignore PHPCompatibility.IniDirectives.RemovedIniDirectives.safe_modeDeprecatedRemoved
			@set_time_limit( $limit ); // @codingStandardsIgnoreLine
		}
	}
}
if ( ! function_exists( 's2w_upload_image' ) ) {
	function s2w_upload_image( $url, $post_parent = 0, $exclude = array(), $post_title = '', $desc = null ) {
		preg_match( '/[^\?]+\.(jpg|JPG|jpeg|JPEG|jpe|JPE|gif|GIF|png|PNG)/', $url, $matches );
		if ( is_array( $matches ) && count( $matches ) ) {
			if ( ! in_array( strtolower( $matches[1] ), $exclude ) ) {
				$file_array['name'] = basename( $matches[0] );
				add_filter( 'big_image_size_threshold', '__return_false' );
				//add product image:
				if ( ! function_exists( 'media_handle_upload' ) ) {
					require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
					require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
					require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
				}
				// Download file to temp location
				$tmp                    = download_url( $url );
				$file_array['tmp_name'] = $tmp;

				// If error storing temporarily, unlink
				if ( is_wp_error( $tmp ) ) {
					@unlink( $file_array['tmp_name'] );

					return $tmp;
				}
				$args = array();
				if ( $post_parent ) {
					$args['post_parent'] = $post_parent;
				}
				if ( $post_title ) {
					$args['post_title'] = $post_title;
				}
				//use media_handle_sideload to upload img:
				$thumbid = media_handle_sideload( $file_array, '', $desc, $args );
				// If error storing permanently, unlink
				if ( is_wp_error( $thumbid ) ) {
					@unlink( $file_array['tmp_name'] );
				}

				return $thumbid;
			}
		}

		return false;
	}
}

if ( ! function_exists( 'woocommerce_version_check' ) ) {
	function woocommerce_version_check( $version = '3.0' ) {
		global $woocommerce;

		if ( version_compare( $woocommerce->version, $version, ">=" ) ) {
			return true;
		}

		return false;
	}
}