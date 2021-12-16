<?php

add_filter( 'map_meta_cap', 'pcf_map_meta_cap', 10, 4 );

function pcf_map_meta_cap( $caps, $cap, $user_id, $args ) {
	$meta_caps = array(
		'pcf_edit_contact_form' => PCF_ADMIN_READ_WRITE_CAPABILITY,
		'pcf_edit_contact_forms' => PCF_ADMIN_READ_WRITE_CAPABILITY,
		'pcf_read_contact_forms' => PCF_ADMIN_READ_CAPABILITY,
		'pcf_delete_contact_form' => PCF_ADMIN_READ_WRITE_CAPABILITY,
		'pcf_manage_integration' => 'manage_options',
		'pcf_submit' => 'read',
	);

	$meta_caps = apply_filters( 'pcf_map_meta_cap', $meta_caps );

	$caps = array_diff( $caps, array_keys( $meta_caps ) );

	if ( isset( $meta_caps[$cap] ) ) {
		$caps[] = $meta_caps[$cap];
	}

	return $caps;
}
