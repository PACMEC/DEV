<?php

add_action( 'parse_request', 'pcf_control_init', 20 );

function pcf_control_init() {
	if ( PCF_Submission::is_restful() ) {
		return;
	}

	if ( isset( $_POST['_pcf'] ) ) {
		$contact_form = pcf_contact_form( (int) $_POST['_pcf'] );

		if ( $contact_form ) {
			$contact_form->submit();
		}
	}
}

add_filter( 'widget_text', 'pcf_widget_text_filter', 9 );

function pcf_widget_text_filter( $content ) {
	$pattern = '/\[[\r\n\t ]*contact-form(-7)?[\r\n\t ].*?\]/';

	if ( ! preg_match( $pattern, $content ) ) {
		return $content;
	}

	$content = do_shortcode( $content );

	return $content;
}

add_action( 'wp_enqueue_scripts', 'pcf_do_enqueue_scripts' );

function pcf_do_enqueue_scripts() {
	if ( pcf_load_js() ) {
		pcf_enqueue_scripts();
	}

	if ( pcf_load_css() ) {
		pcf_enqueue_styles();
	}
}

function pcf_enqueue_scripts() {
	$in_footer = true;

	if ( 'header' === pcf_load_js() ) {
		$in_footer = false;
	}

	wp_enqueue_script( 'contact-form-7',
		pcf_plugin_url( 'includes/js/scripts.js' ),
		array( 'jquery' ), PCF_VERSION, $in_footer );

	$pcf = array(
		'apiSettings' => array(
			'root' => esc_url_raw( rest_url( 'contact-form-7/v1' ) ),
			'namespace' => 'contact-form-7/v1',
		),
		'recaptcha' => array(
			'messages' => array(
				'empty' =>
					__( 'Please verify that you are not a robot.', 'contact-form-7' ),
			),
		),
	);

	if ( defined( 'WP_CACHE' ) && WP_CACHE ) {
		$pcf['cached'] = 1;
	}

	if ( pcf_support_html5_fallback() ) {
		$pcf['jqueryUi'] = 1;
	}

	wp_localize_script( 'contact-form-7', 'pcf', $pcf );

	do_action( 'pcf_enqueue_scripts' );
}

function pcf_script_is() {
	return wp_script_is( 'contact-form-7' );
}

function pcf_enqueue_styles() {
	wp_enqueue_style( 'contact-form-7',
		pcf_plugin_url( 'includes/css/styles.css' ),
		array(), PCF_VERSION, 'all' );

	if ( pcf_is_rtl() ) {
		wp_enqueue_style( 'contact-form-7-rtl',
			pcf_plugin_url( 'includes/css/styles-rtl.css' ),
			array(), PCF_VERSION, 'all' );
	}

	do_action( 'pcf_enqueue_styles' );
}

function pcf_style_is() {
	return wp_style_is( 'contact-form-7' );
}

/* HTML5 Fallback */

add_action( 'wp_enqueue_scripts', 'pcf_html5_fallback', 20 );

function pcf_html5_fallback() {
	if ( ! pcf_support_html5_fallback() ) {
		return;
	}

	if ( pcf_script_is() ) {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-spinner' );
	}

	if ( pcf_style_is() ) {
		wp_enqueue_style( 'jquery-ui-smoothness',
			pcf_plugin_url(
				'includes/js/jquery-ui/themes/smoothness/jquery-ui.min.css' ),
			array(), '1.11.4', 'screen' );
	}
}
