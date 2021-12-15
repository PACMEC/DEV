<?php

trait IMPORT_SHOPIFY_TO_WOOCOMMERCE_Background_Functions {

	/**
	 * Really long running process
	 *
	 * @return int
	 */
	public function really_long_running_task() {
		return sleep( 5 );
	}

	/**
	 * Log
	 *
	 * @param string $message
	 */
	public function log( $message ) {
		error_log( $message );
	}

	/**
	 * Get lorem
	 *
	 * @param string $name
	 *
	 * @return string
	 */
	protected function get_message( $name ) {
		$response = wp_remote_get( esc_url_raw( 'http://loripsum.net/api/1/short/plaintext' ) );
		$body     = trim( wp_remote_retrieve_body( $response ) );

		if ( empty( $body ) ) {
			$body = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quippe: habes enim a rhetoribus; Eaedem res maneant alio modo.';
		}

		return $name . ': ' . $body;
	}
	protected function upload_image( $url,$desc='gallery desc' ) {
		//add product image:
		if ( ! function_exists( 'media_handle_upload' ) ) {
			require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/file.php' );
			require_once( ABSPATH . "wp-admin" . '/includes/media.php' );
		}
		$thumb_url = $url;

		// Download file to temp location
		$tmp = download_url( $thumb_url );

		// Set variables for storage
		// fix file name for query strings
		preg_match( '/[^\?]+\.(jpg|JPG|jpe|JPE|jpeg|JPEG|gif|GIF|png|PNG)/', $thumb_url, $matches );
		$file_array['name']     = basename( $matches[0] );
		$file_array['tmp_name'] = $tmp;

		// If error storing temporarily, unlink
		if ( is_wp_error( $tmp ) ) {
			@unlink( $file_array['tmp_name'] );
		}

		//use media_handle_sideload to upload img:
		$thumbid = media_handle_sideload( $file_array, '', $desc );
		//			print_r($thumbid);
		// If error storing permanently, unlink
		if ( is_wp_error( $thumbid ) ) {
			@unlink( $file_array['tmp_name'] );
		}

		return $thumbid;
	}
}