<?php

class WP_IMPORT_SHOPIFY_TO_WOOCOMMERCE_Process extends WP_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 's2w_process';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
		$placeholder = get_option( 's2w_woocommerce_placeholder_img_src', 0 );
		if ( is_array( $item ) && count( $item ) ) {
			$product_id       = isset( $item[0]['product_ids'][0] ) ? $item[0]['product_ids'][0] : '';
			$current_image_id = get_post_meta( $product_id, '_thumbnail_id', true );
			if ( $current_image_id && $current_image_id != $placeholder ) {
				return false;
			}
			if ( $product_id && get_post_type( $product_id ) == 'product' ) {
				try {
					$gallery = array();
					foreach ( $item as $image ) {
						$src = isset( $image['src'] ) ? $image['src'] : '';
						if ( $src ) {
							$thumb_id = s2w_upload_image( $src);
							if ( ! is_wp_error( $thumb_id ) ) {
								if ( count( $image['product_ids'] ) ) {
									foreach ( $image['product_ids'] as $v_id ) {
										if ( in_array( get_post_type( $v_id ), array(
											'product',
											'product_variation'
										) ) ) {
											update_post_meta( $v_id, '_thumbnail_id', $thumb_id );
										}
									}
								}
								if ( isset( $image['alt'] ) && $image['alt'] ) {
									update_post_meta( $thumb_id, '_wp_attachment_image_alt', $image['alt'] );
								}
								$gallery[] = $thumb_id;
							} else {
								error_log( 'S2W error log - background download images: ' . $thumb_id->get_error_code() . ' - ' . $thumb_id->get_error_message() );
							}
						}
					}
					if ( count( $gallery ) > 1 ) {
						unset( $gallery[0] );
						update_post_meta( $product_id, '_product_image_gallery', implode( ',', $gallery ) );
					}
				} catch ( Exception $e ) {
					error_log( 'S2W error log - background download images: ' . $e->getMessage() );

					return false;
				}
			}
		}

		return false;
	}

	/**
	 * Is the updater running?
	 *
	 * @return boolean
	 */
	public function is_downloading() {
		global $wpdb;

		$table  = $wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		$key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';


		return boolval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE {$column} LIKE %s", $key ) ) );
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		if ( ! $this->is_downloading() ) {
			set_transient( 's2w_background_processing_complete', time() );
		}
		// Show notice to user or perform some other arbitrary task...
		parent::complete();
	}

	/**
	 * Delete all batches.
	 *
	 * @return WP_IMPORT_SHOPIFY_TO_WOOCOMMERCE_Process
	 */
	public function delete_all_batches() {
		global $wpdb;

		$table  = $wpdb->options;
		$column = 'option_name';

		if ( is_multisite() ) {
			$table  = $wpdb->sitemeta;
			$column = 'meta_key';
		}

		$key = $wpdb->esc_like( $this->identifier . '_batch_' ) . '%';

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$table} WHERE {$column} LIKE %s", $key ) ); // @codingStandardsIgnoreLine.

		return $this;
	}

	/**
	 * Kill process.
	 *
	 * Stop processing queue items, clear cronjob and delete all batches.
	 */
	public function kill_process() {
		if ( ! $this->is_queue_empty() ) {
			$this->delete_all_batches();
			wp_clear_scheduled_hook( $this->cron_hook_identifier );
		}
	}
}