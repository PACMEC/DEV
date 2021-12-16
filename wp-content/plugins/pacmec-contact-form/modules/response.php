<?php
/**
** A base module for [response]
**/

/* form_tag handler */

add_action( 'pcf_init', 'pcf_add_form_tag_response' );

function pcf_add_form_tag_response() {
	pcf_add_form_tag( 'response', 'pcf_response_form_tag_handler',
		array( 'display-block' => true ) );
}

function pcf_response_form_tag_handler( $tag ) {
	if ( $contact_form = pcf_get_current_contact_form() ) {
		return $contact_form->form_response_output();
	}
}
