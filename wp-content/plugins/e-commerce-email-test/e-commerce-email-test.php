<?php
/*
 * Plugin Name: E-Commerce - Pruebas de correo electronico
 * Plugin URI: #
 * Description: Le permite enviar correos electrónicos de prueba de E-Commerce.
 * Version:  1.0.0
 * Author: PACMEC
 * Author URI: #
 * Developer: PACMEC
 * Developer URI: #
 * Text Domain: e-commerce-email-test
 * Domain Path: /langs
 */
 
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {    
	 	
		// set email classes for test buttons
		$wetp_test_email_class = array(
			'WC_Email_New_Order'=>'New Order',
			'WC_Email_Customer_Processing_Order'=>'Processing Order',
			'WC_Email_Customer_Completed_Order'=>'Completed Order',
			'WC_Email_Customer_Invoice'=>'Customer Invoice',
			'WC_Email_Customer_Note'=>'Customer Note',
		);
		 
		// include plugin files
		require_once( plugin_dir_path( __FILE__ ) . 'functions.php' );
		require_once( plugin_dir_path( __FILE__ ) . 'email-trigger.php' );

        
		if( is_admin() ) { 
		 
			// register admin page and add menu
			add_action('admin_menu', 'wept_register_test_email_submenu_page');

			function wept_register_test_email_submenu_page() {
				add_submenu_page( 'woocommerce', 'Email Test', 'Email Test', 'manage_options', 'woocommerce-email-test', 'wept_register_test_email_submenu_page_callback' ); 
			}

			function wept_register_test_email_submenu_page_callback() {
				require_once( plugin_dir_path( __FILE__ ) . 'admin-menu.php' );
			}
			
		}

	
}