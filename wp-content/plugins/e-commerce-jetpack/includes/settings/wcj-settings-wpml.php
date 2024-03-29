<?php
/**
 * Booster for WooCommerce - Settings - WPML
 *
 * @version 5.1.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$all_modules = array();
if ( function_exists( 'WCJ' ) && ! empty( WCJ()->modules ) ) {
	foreach ( WCJ()->modules as $module_key => $module ) {
		$desc_prefix = ( false !== strpos( $module_key, 'pdf_invoicing_' ) ? __( 'PDF Invoicing', 'e-commerce-jetpack' ) . ': ' : '' );
		$all_modules[ $module_key ] = $desc_prefix . $module->short_desc;
	}
}

$settings = array(
	array(
		'title'    => __( 'General Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_wpml_general_options',
	),
	array(
		'title'    => __( 'Use Translation Product IDs', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'type'     => 'checkbox',
		'id'       => 'wcj_wpml_use_translation_product_id',
		'default'  => 'yes',
	),
	array(
		'title'    => __( 'Auto Switch Booster Currency', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => __( "Try to automatically switch Booster currency according to WPML. It's necessary to enable MultiCurrency module", 'e-commerce-jetpack' ),
		'type'     => 'checkbox',
		'id'       => 'wcj_wpml_switch_booster_currency',
		'default'  => 'no',
	),
	array(
		'title'             => __( 'Synchronize Metas', 'e-commerce-jetpack' ),
		'custom_attributes' => apply_filters( 'booster_message', '', 'disabled' ),
		'desc'              => empty( $message = apply_filters( 'booster_message', '', 'desc' ) ) ? __( 'Enable', 'e-commerce-jetpack' ) : $message,
		'desc_tip'          => __( "Try to automatically synchronize some Booster metas between products on different languages.", 'e-commerce-jetpack' ),
		'type'              => 'checkbox',
		'id'                => 'wcj_wpml_sync_metas',
		'default'           => 'no',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_wpml_general_options',
	),
	array(
		'title'    => __( 'WPML Language Configuration File Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'desc'     => sprintf( __( 'Options for regenerating %s file.', 'e-commerce-jetpack' ), '<code>wpml-config.xml</code>' ),
		'id'       => 'wcj_wpml_config_xml_options',
	),
	array(
		'title'    => __( 'Automatically Regenerate', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'Automatically regenerate %s file on each Booster version update.', 'e-commerce-jetpack' ), '<code>wpml-config.xml</code>' ),
		'type'     => 'checkbox',
		'id'       => 'wcj_wpml_config_xml_auto_regenerate',
		'default'  => 'no',
	),
	array(
		'title'    => __( 'Modules to Skip', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Select modules, which options you wish to exclude from wpml-config.xml file.', 'e-commerce-jetpack' ),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'id'       => 'wcj_wpml_config_xml_modules_to_skip',
		'options'  => $all_modules,
		'default'  => $this->get_default_modules_to_skip(),
	),
	array(
		'title'    => __( 'Option IDs to Skip', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Select options, which you wish to exclude from wpml-config.xml file.', 'e-commerce-jetpack' ),
		'desc'     => sprintf( __( 'Full or part of option ID. Separated by vertical bar %s.', 'e-commerce-jetpack' ), '( | )' ),
		'type'     => 'textarea',
		'id'       => 'wcj_wpml_config_xml_values_to_skip',
		'default'  => $this->get_default_values_to_skip(),
		'css'      => 'width:100%;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_wpml_config_xml_options',
	),
	array(
		'title'    => __( 'Tools', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_' . $this->id . '_tools_options',
	),
	array(
		'title'    => __( 'Module Tools', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'To use tools, module must be enabled.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_' . $this->id . '_module_tools',
		'type'     => 'custom_link',
		'link'     => ( $this->is_enabled() ) ?
			'<code>' . '<a href="' . add_query_arg( 'create_wpml_xml_file', '1' ) . '">' .
				__( 'Regenerate wpml-config.xml file', 'e-commerce-jetpack' ) . '</a>' . '</code>' .
				'<pre>' . $this->notice . '</pre>' :
			'<code>' . __( 'Regenerate wpml-config.xml file', 'e-commerce-jetpack' ) . '</code>',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_' . $this->id . '_tools_options',
	),
);
$this->notice = '';
return $settings;
