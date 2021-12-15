<?php
if ( ! class_exists( 'S2W_Error_Images_Table' ) ) {
	class S2W_Error_Images_Table {
		public static function create_table() {
			global $wpdb;
			$collate = '';
			if ( $wpdb->has_cap( 'collation' ) ) {
				$collate = $wpdb->get_charset_collate();
			}

			$table = $wpdb->prefix . 's2w_error_product_images';

			$query = "CREATE TABLE IF NOT EXISTS {$table} (
                             `id` bigint(20) NOT NULL AUTO_INCREMENT,
                             `product_id` bigint(20) NOT NULL,
                             `product_ids` longtext NOT NULL,
                             `image_src` longtext NOT NULL,
                             `image_alt` longtext COLLATE utf8_unicode_ci,
                             `set_gallery` tinyint(1) NOT NULL,
                             `image_id` varchar (200),
                             PRIMARY KEY  (`id`)
                             ) $collate";

			$wpdb->query( $query );
		}

		public static function insert( $product_id, $product_ids, $image_src, $image_alt, $set_gallery, $image_id = '' ) {
			global $wpdb;
			$table = $wpdb->prefix . 's2w_error_product_images';
			$wpdb->insert( $table,
				array(
					'product_id'  => $product_id,
					'product_ids' => $product_ids,
					'image_src'   => $image_src,
					'image_alt'   => $image_alt,
					'set_gallery' => $set_gallery,
					'image_id'    => $image_id,
				),
				array(
					'%d',
					'%s',
					'%s',
					'%s',
					'%d',
					'%s',
				)
			);

			return $wpdb->insert_id;
		}

		public static function delete( $id ) {
			global $wpdb;
			$table  = $wpdb->prefix . 's2w_error_product_images';
			$delete = $wpdb->delete( $table,
				array(
					'id' => $id,
				),
				array(
					'%d',
				)
			);

			return $delete;
		}

		public static function add_column( $column ) {
			global $wpdb;
			$table = $wpdb->prefix . 's2w_error_product_images';
			$query = "ALTER TABLE {$table} ADD COLUMN if NOT EXISTS `{$column}` varchar(50) default ''";

			return $wpdb->query( $query );
		}

		public static function modify_column( $column, $datatype ) {
			global $wpdb;
			$table = $wpdb->prefix . 's2w_error_product_images';
			$query = "ALTER TABLE {$table} MODIFY COLUMN `{$column}` {$datatype}";

			return $wpdb->query( $query );
		}

		public static function get_row( $id ) {
			global $wpdb;
			$table = $wpdb->prefix . 's2w_error_product_images';
			$query = "SELECT * FROM {$table} WHERE id=%s LIMIT 1";

			return $wpdb->get_row( $wpdb->prepare( $query, $id ), ARRAY_A );
		}

		public static function get_rows( $limit = 0, $offset = 0, $count = false ) {
			global $wpdb;
			$table  = $wpdb->prefix . 's2w_error_product_images';
			$posts  = $wpdb->prefix . 'posts';
			$select = "error_images.*";
			if ( $count ) {
				$select = 'count(*)';
				$query  = "SELECT {$select} FROM {$table} as error_images JOIN {$posts} as wp_posts ON error_images.product_id=wp_posts.ID";

				return $wpdb->get_var( $query );
			} else {
				$query = "SELECT {$select} FROM {$table} as error_images JOIN {$posts} as wp_posts ON error_images.product_id=wp_posts.ID";
				if ( $limit ) {
					$query .= " LIMIT {$offset},{$limit}";
				}

				return $wpdb->get_results( $query, ARRAY_A );
			}
		}
	}
}
