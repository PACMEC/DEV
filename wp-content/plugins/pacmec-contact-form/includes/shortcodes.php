<?php
/**
 * All the functions and classes in this file are deprecated.
 * You shouldn't use them. The functions and classes will be
 * removed in a later version.
 */

function pcf_add_shortcode( $tag, $func, $has_name = false ) {
	pcf_deprecated_function( __FUNCTION__, '4.6', 'pcf_add_form_tag' );

	return pcf_add_form_tag( $tag, $func, $has_name );
}

function pcf_remove_shortcode( $tag ) {
	pcf_deprecated_function( __FUNCTION__, '4.6', 'pcf_remove_form_tag' );

	return pcf_remove_form_tag( $tag );
}

function pcf_do_shortcode( $content ) {
	pcf_deprecated_function( __FUNCTION__, '4.6',
		'pcf_replace_all_form_tags' );

	return pcf_replace_all_form_tags( $content );
}

function pcf_scan_shortcode( $cond = null ) {
	pcf_deprecated_function( __FUNCTION__, '4.6', 'pcf_scan_form_tags' );

	return pcf_scan_form_tags( $cond );
}

class PCF_ShortcodeManager {

	private static $form_tags_manager;

	private function __construct() {}

	public static function get_instance() {
		pcf_deprecated_function( __METHOD__, '4.6',
			'PCF_FormTagsManager::get_instance' );

		self::$form_tags_manager = PCF_FormTagsManager::get_instance();
		return new self;
	}

	public function get_scanned_tags() {
		pcf_deprecated_function( __METHOD__, '4.6',
			'PCF_FormTagsManager::get_scanned_tags' );

		return self::$form_tags_manager->get_scanned_tags();
	}

	public function add_shortcode( $tag, $func, $has_name = false ) {
		pcf_deprecated_function( __METHOD__, '4.6',
			'PCF_FormTagsManager::add' );

		return self::$form_tags_manager->add( $tag, $func, $has_name );
	}

	public function remove_shortcode( $tag ) {
		pcf_deprecated_function( __METHOD__, '4.6',
			'PCF_FormTagsManager::remove' );

		return self::$form_tags_manager->remove( $tag );
	}

	public function normalize_shortcode( $content ) {
		pcf_deprecated_function( __METHOD__, '4.6',
			'PCF_FormTagsManager::normalize' );

		return self::$form_tags_manager->normalize( $content );
	}

	public function do_shortcode( $content, $exec = true ) {
		pcf_deprecated_function( __METHOD__, '4.6',
			'PCF_FormTagsManager::replace_all' );

		if ( $exec ) {
			return self::$form_tags_manager->replace_all( $content );
		} else {
			return self::$form_tags_manager->scan( $content );
		}
	}

	public function scan_shortcode( $content ) {
		pcf_deprecated_function( __METHOD__, '4.6',
			'PCF_FormTagsManager::scan' );

		return self::$form_tags_manager->scan( $content );
	}
}

class PCF_Shortcode extends PCF_FormTag {

	public function __construct( $tag ) {
		pcf_deprecated_function( 'PCF_Shortcode', '4.6', 'PCF_FormTag' );

		parent::__construct( $tag );
	}
}
