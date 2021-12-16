<?php
/**
 * Booster for WooCommerce - Settings - Checkout Files Upload
 *
 * @version 4.2.0
 * @since   2.8.0
 * @author  Pluggabl LLC.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$product_tags_options = wcj_get_terms( 'product_tag' );
$product_cats_options = wcj_get_terms( 'product_cat' );
$products_options     = wcj_get_products();
$user_roles_options   = wcj_get_user_roles_options();
$settings = array(
	array(
		'title'    => __( 'Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_files_upload_options',
	),
	array(
		'title'    => __( 'Total Files', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_total_number',
		'default'  => 1,
		'type'     => 'custom_number',
		'desc'     => apply_filters( 'booster_message', '', 'desc' ),
		'custom_attributes' => array_merge(
			is_array( apply_filters( 'booster_message', '', 'readonly' ) ) ? apply_filters( 'booster_message', '', 'readonly' ) : array(),
			array( 'step' => '1', 'min' => '1' )
		),
	),
);
$total_number = apply_filters( 'booster_option', 1, wcj_get_option( 'wcj_checkout_files_upload_total_number', 1 ) );
for ( $i = 1; $i <= $total_number; $i++ ) {
	$settings = array_merge( $settings, array(
		array(
			'title'    => __( 'File', 'e-commerce-jetpack' ) . ' #' . $i,
			'id'       => 'wcj_checkout_files_upload_enabled_' . $i,
			'desc'     => __( 'Enabled', 'e-commerce-jetpack' ),
			'type'     => 'checkbox',
			'default'  => 'yes',
		),
		array(
			'id'       => 'wcj_checkout_files_upload_required_' . $i,
			'desc'     => __( 'Required', 'e-commerce-jetpack' ),
			'type'     => 'checkbox',
			'default'  => 'no',
		),
		array(
			'id'       => 'wcj_checkout_files_upload_hook_' . $i,
			'desc'     => __( 'Position', 'e-commerce-jetpack' ),
			'default'  => 'woocommerce_before_checkout_form',
			'type'     => 'select',
			'options'  => array(
				'woocommerce_before_checkout_form'  => __( 'Before checkout form', 'e-commerce-jetpack' ),
				'woocommerce_after_checkout_form'   => __( 'After checkout form', 'e-commerce-jetpack' ),
				'disable'                           => __( 'Do not add on checkout', 'e-commerce-jetpack' ),
			),
		),
		array(
			'desc'     => __( 'Position order', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_hook_priority_' . $i,
			'default'  => 20,
			'type'     => 'number',
			'custom_attributes' => array( 'min' => '0' ),
		),
		array(
			'id'       => 'wcj_checkout_files_upload_add_to_thankyou_' . $i,
			'desc'     => __( 'Add to Thank You page', 'e-commerce-jetpack' ),
			'type'     => 'checkbox',
			'default'  => 'no',
		),
		array(
			'id'       => 'wcj_checkout_files_upload_add_to_myaccount_' . $i,
			'desc'     => __( 'Add to My Account page', 'e-commerce-jetpack' ),
			'type'     => 'checkbox',
			'default'  => 'no',
		),
		array(
			'desc'     => __( 'Label', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Leave blank to disable label', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_label_' . $i,
			'default'  => __( 'Please select file to upload', 'e-commerce-jetpack' ),
			'type'     => 'textarea',
		),
		array(
			'desc'     => __( 'Accepted file types', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Accepted file types. E.g.: ".jpg,.jpeg,.png". Leave blank to accept all files', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_file_accept_' . $i,
			'default'  => '.jpg,.jpeg,.png',
			'type'     => 'text',
		),
		array(
			'desc'     => __( 'Label: Upload button', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_label_upload_button_' . $i,
			'default'  =>  __( 'Upload', 'e-commerce-jetpack' ),
			'type'     => 'text',
		),
		array(
			'desc'     => __( 'Label: Remove button', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_label_remove_button_' . $i,
			'default'  =>  __( 'Remove', 'e-commerce-jetpack' ),
			'type'     => 'text',
		),
		array(
			'desc'     => __( 'Notice: Wrong file type', 'e-commerce-jetpack' ),
			'desc_tip' => __( '%s will be replaced with file name', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_notice_wrong_file_type_' . $i,
			'default'  =>  __( 'Wrong file type: "%s"!', 'e-commerce-jetpack' ),
			'type'     => 'textarea',
		),
		array(
			'desc'     => __( 'Notice: File is required', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_notice_required_' . $i,
			'default'  =>  __( 'File is required!', 'e-commerce-jetpack' ),
			'type'     => 'textarea',
		),
		array(
			'desc'     => __( 'Notice: File was successfully uploaded', 'e-commerce-jetpack' ),
			'desc_tip' => __( '%s will be replaced with file name', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_notice_success_upload_' . $i,
			'default'  =>  __( 'File "%s" was successfully uploaded.', 'e-commerce-jetpack' ),
			'type'     => 'textarea',
		),
		array(
			'desc'     => __( 'Notice: No file selected', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_notice_upload_no_file_' . $i,
			'default'  =>  __( 'Please select file to upload!', 'e-commerce-jetpack' ),
			'type'     => 'textarea',
		),
		array(
			'desc'     => __( 'Notice: File was successfully removed', 'e-commerce-jetpack' ),
			'desc_tip' => __( '%s will be replaced with file name', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_notice_success_remove_' . $i,
			'default'  =>  __( 'File "%s" was successfully removed.', 'e-commerce-jetpack' ),
			'type'     => 'textarea',
		),
		array(
			'desc'     => __( 'PRODUCTS to show this field', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'To show this field only if at least one selected product is in cart, enter products here. Leave blank to show for all products.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_show_products_in_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $products_options,
		),
		array(
			'desc'     => __( 'CATEGORIES to show this field', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'To show this field only if at least one product of selected category is in cart, enter categories here. Leave blank to show for all products.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_show_cats_in_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $product_cats_options,
		),
		array(
			'desc'     => __( 'TAGS to show this field', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'To show this field only if at least one product of selected tag is in cart, enter tags here. Leave blank to show for all products.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_show_tags_in_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $product_tags_options,
		),
		array(
			'desc'     => __( 'USER ROLES to show this field', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Leave blank to show for all user roles.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_show_user_roles_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $user_roles_options,
		),
		array(
			'desc'     => __( 'PRODUCTS to hide this field', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'To hide this field if at least one selected product is in cart, enter products here. Leave blank to show for all products.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_hide_products_in_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $products_options,
		),
		array(
			'desc'     => __( 'CATEGORIES to hide this field', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'To hide this field if at least one product of selected category is in cart, enter categories here. Leave blank to show for all products.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_hide_cats_in_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $product_cats_options,
		),
		array(
			'desc'     => __( 'TAGS to hide this field', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'To hide this field if at least one product of selected tag is in cart, enter tags here. Leave blank to show for all products.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_hide_tags_in_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $product_tags_options,
		),
		array(
			'desc'     => __( 'USER ROLES to hide this field', 'e-commerce-jetpack' ),
			'desc_tip' => __( 'Leave blank to show for all user roles.', 'e-commerce-jetpack' ),
			'id'       => 'wcj_checkout_files_upload_hide_user_roles_' . $i,
			'default'  => '',
			'class'    => 'chosen_select',
			'type'     => 'multiselect',
			'options'  => $user_roles_options,
		),
	) );
}
$settings = array_merge( $settings, array(
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_files_upload_options',
	),
) );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'General Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_files_upload_general_options',
	),
	array(
		'title'    => __( 'Remove All Uploaded Files on Empty Cart', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_remove_on_empty_cart',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Add notice', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_remove_on_empty_cart_add_notice',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'id'       => 'wcj_checkout_files_upload_notice_remove_on_empty_cart',
		'default'  => __( 'Files were successfully removed.', 'e-commerce-jetpack' ),
		'type'     => 'textarea',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_files_upload_general_options',
	),
) );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Emails Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_files_upload_emails_options',
	),
	array(
		'title'    => __( 'Attach Files to Admin\'s New Order Emails', 'e-commerce-jetpack' ),
		'desc'     => __( 'Attach', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_attach_to_admin_new_order',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Attach Files to Customer\'s Processing Order Emails', 'e-commerce-jetpack' ),
		'desc'     => __( 'Attach', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_attach_to_customer_processing_order',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Send Additional Email to Admin on User Actions', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'Admin email: <em>%s</em>.', 'e-commerce-jetpack' ), wcj_get_option( 'admin_email' ) ),
		'id'       => 'wcj_checkout_files_upload_additional_admin_emails[actions]',
		'default'  => array(),
		'type'     => 'multiselect',
		'class'    => 'chosen_select',
		'options'  => array(
			'remove_file' => __( 'File removed on "Thank You" or "My Account" page', 'e-commerce-jetpack' ),
			'upload_file' => __( 'File uploaded on "Thank You" or "My Account" page', 'e-commerce-jetpack' ),
		),
	),
	array(
		'desc'     => __( 'Attach file on upload action', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_additional_admin_emails[do_attach]',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_files_upload_emails_options',
	),
) );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Form Template Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_files_upload_form_template_options',
	),
	array(
		'title'    => __( 'Before', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_form_template_before',
		'default'  => '<table>',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Label', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Replaced values: %field_id%, %field_label%, %required_html%.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_form_template_label',
		'default'  => '<tr><td colspan="2"><label for="%field_id%">%field_label%</label>%required_html%</td></tr>',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Field', 'e-commerce-jetpack' ),
		'desc_tip' => __( 'Replaced values: %field_html%, %button_html%.', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_form_template_field',
		'default'  => '<tr><td style="width:50%;max-width:50vw;">%field_html%</td><td style="width:50%;">%button_html%</td></tr>',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'After', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_form_template_after',
		'default'  => '</table>',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Show images in field', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_form_template_field_show_images',
		'default'  => 'no',
		'type'     => 'checkbox',
	),
	array(
		'desc'     => __( 'Image style', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_form_template_field_image_style',
		'default'  => 'width:64px;',
		'type'     => 'text',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_files_upload_form_template_options',
	),
) );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Order Template Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_files_upload_templates[order_options]',
	),
	array(
		'title'    => __( 'Before', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_templates[order_before]',
		'default'  => '',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Item', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%file_name%', '%image%' ) ),
		'id'       => 'wcj_checkout_files_upload_templates[order_item]',
		'default'  => sprintf( __( 'File: %s', 'e-commerce-jetpack' ), '%file_name%' ) . '<br>',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'After', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_templates[order_after]',
		'default'  => '',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'desc'     => __( 'Image style', 'e-commerce-jetpack' ),
		'desc_tip' => sprintf( __( 'Ignored, if %s is not included in %s option above.', 'e-commerce-jetpack' ),
			'<em>%image%</em>', '<em>' . __( 'Item', 'e-commerce-jetpack' ) . '</em>' ),
		'id'       => 'wcj_checkout_files_upload_templates[order_image_style]',
		'default'  => 'width:64px;',
		'type'     => 'text',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_files_upload_templates[order_options]',
	),
) );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Email Template Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_files_upload_templates[email_options]',
	),
	array(
		'title'    => __( 'Before', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_templates[email_before]',
		'default'  => '',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'Item', 'e-commerce-jetpack' ),
		'desc'     => wcj_message_replaced_values( array( '%file_name%' ) ),
		'id'       => 'wcj_checkout_files_upload_templates[email_item]',
		'default'  => sprintf( __( 'File: %s', 'e-commerce-jetpack' ), '%file_name%' ) . '<br>',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'title'    => __( 'After', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_templates[email_after]',
		'default'  => '',
		'type'     => 'textarea',
		'css'      => 'width:100%;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_files_upload_templates[email_options]',
	),
) );
$settings = array_merge( $settings, array(
	array(
		'title'    => __( 'Advanced Options', 'e-commerce-jetpack' ),
		'type'     => 'title',
		'id'       => 'wcj_checkout_files_upload_advanced_options',
	),
	array(
		'title'    => __( 'Notice Type', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_notice_type',
		'default'  => 'wc_add_notice',
		'type'     => 'select',
		'options'  => array(
			'wc_add_notice'   => __( 'Add notice', 'e-commerce-jetpack' ),
			'wc_print_notice' => __( 'Print notice', 'e-commerce-jetpack' ),
			'disable'         => __( 'Disable notice', 'e-commerce-jetpack' ),
		),
	),
	array(
		'title'    => __( 'Block Potentially Harmful Files', 'e-commerce-jetpack' ),
		'desc'     => __( 'Enable', 'e-commerce-jetpack' ),
		'id'       => 'wcj_checkout_files_upload_block_files_enabled',
		'default'  => 'yes',
		'type'     => 'checkbox',
	),
	array(
		'title'    => __( 'Potentially Harmful File Extensions', 'e-commerce-jetpack' ),
		'desc'     => sprintf( __( 'List of file extensions separated by vertical bar %s.', 'e-commerce-jetpack' ), '<code>|</code>' ),
		'id'       => 'wcj_checkout_files_upload_block_files_exts',
		'default'  => 'bat|exe|cmd|sh|php|php0|php1|php2|php3|php4|php5|php6|php7|php8|php9|ph|ph0|ph1|ph2|ph3|ph4|ph5|ph6|ph7|ph8|ph9|pl|cgi|386|dll|com|torrent|js|app|jar|pif|vb|vbscript|wsf|asp|cer|csr|jsp|drv|sys|ade|adp|bas|chm|cpl|crt|csh|fxp|hlp|hta|inf|ins|isp|jse|htaccess|htpasswd|ksh|lnk|mdb|mde|mdt|mdw|msc|msi|msp|mst|ops|pcd|prg|reg|scr|sct|shb|shs|url|vbe|vbs|wsc|wsf|wsh|html|htm',
		'type'     => 'text',
		'css'      => 'width:100%;',
	),
	array(
		'type'     => 'sectionend',
		'id'       => 'wcj_checkout_files_upload_advanced_options',
	),
) );
return $settings;
