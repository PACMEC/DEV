<?php
/**
 * Booster for WooCommerce - Widget - Multicurrency
 *
 * @version 4.0.0
 * @since   2.4.3
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WCJ_Widget_Multicurrency' ) ) :

class WCJ_Widget_Multicurrency extends WCJ_Widget {

	/**
	 * get_data.
	 *
	 * @version 3.1.0
	 * @since   3.1.0
	 */
	function get_data( $id ) {
		switch ( $id ) {
			case 'id_base':
				return 'wcj_widget_multicurrency';
			case 'name':
				return __( 'Booster - Multicurrency Switcher', 'e-commerce-jetpack' );
			case 'description':
				return __( 'Booster: Multicurrency Switcher Widget', 'e-commerce-jetpack' );
		}
	}

	/**
	 * get_content.
	 *
	 * @version 4.0.0
	 * @since   3.1.0
	 */
	function get_content( $instance ) {
		if ( ! wcj_is_module_enabled( 'multicurrency' ) ) {
			return __( 'Multicurrency module not enabled!', 'e-commerce-jetpack' );
		} else {
			switch ( $instance['switcher_type'] ) {
				case 'link_list':
					return do_shortcode( '[wcj_currency_select_link_list]' );
				case 'radio_list':
					return do_shortcode( '[wcj_currency_select_radio_list' .
							' form_method="' . ( ! empty( $instance['form_method'] ) ? $instance['form_method'] : 'post' ) . '"' .
						']' );
				default: // 'drop_down'
					return do_shortcode( '[wcj_currency_select_drop_down_list' .
							' form_method="' . ( ! empty( $instance['form_method'] ) ? $instance['form_method'] : 'post' ) . '"' .
							' class="'       . ( ! empty( $instance['class'] )       ? $instance['class']       : ''     ) . '"' .
							' style="'       . ( ! empty( $instance['style'] )       ? $instance['style']       : ''     ) . '"' .
						']' );
			}
		}
	}

	/**
	 * get_options.
	 *
	 * @version 4.0.0
	 * @since   3.1.0
	 */
	function get_options() {
		return array(
			array(
				'title'    => __( 'Title', 'e-commerce-jetpack' ),
				'id'       => 'title',
				'default'  => '',
				'type'     => 'text',
				'class'    => 'widefat',
			),
			array(
				'title'    => __( 'Type', 'e-commerce-jetpack' ),
				'id'       => 'switcher_type',
				'default'  => 'drop_down',
				'type'     => 'select',
				'options'  => array(
					'drop_down'  => __( 'Drop down', 'e-commerce-jetpack' ),
					'radio_list' => __( 'Radio list', 'e-commerce-jetpack' ),
					'link_list'  => __( 'Link list', 'e-commerce-jetpack' ),
				),
				'class'    => 'widefat',
			),
			array(
				'title'    => __( 'Form Method', 'e-commerce-jetpack' ),
				'desc'     => '* ' . __( 'HTML form method for "Drop down" and "Radio list" types.', 'e-commerce-jetpack' ),
				'id'       => 'form_method',
				'default'  => 'post',
				'type'     => 'select',
				'options'  => array(
					'post'  => __( 'Post', 'e-commerce-jetpack' ),
					'get'   => __( 'Get', 'e-commerce-jetpack' ),
				),
				'class'    => 'widefat',
			),
			array(
				'title'    => __( 'Class', 'e-commerce-jetpack' ),
				'desc'     => '* ' . __( 'HTML class for "Drop down" type.', 'e-commerce-jetpack' ),
				'id'       => 'class',
				'default'  => '',
				'type'     => 'text',
				'class'    => 'widefat',
			),
			array(
				'title'    => __( 'Style', 'e-commerce-jetpack' ),
				'desc'     => '* ' . __( 'HTML style for "Drop down" type.', 'e-commerce-jetpack' ),
				'id'       => 'style',
				'default'  => '',
				'type'     => 'text',
				'class'    => 'widefat',
			),
		);
	}

}

endif;

if ( ! function_exists( 'register_wcj_widget_multicurrency' ) ) {
	/**
	 * Register WCJ_Widget_Multicurrency widget.
	 */
	function register_wcj_widget_multicurrency() {
		register_widget( 'WCJ_Widget_Multicurrency' );
	}
}
add_action( 'widgets_init', 'register_wcj_widget_multicurrency' );
