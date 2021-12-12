<?php
/**
 * @author flatfull.com
 */

class Admin_Theme_PACMEC_Footer{

	private $setting;

	function __construct($setting) {
		$this->setting = $setting;

		add_filter( 'admin_footer_text', array( $this, 'admin_footer' ) );
		add_filter( 'update_footer', array( $this, 'admin_footer' ), 999 );

		add_action( 'admin_screen_col_1', array( $this, 'admin_screen' ));
	}

	// admin footer
	function admin_footer( $default ){
		if(  strpos($default, 'wordpress') === false ){
			if( $this->setting->get_setting('footer_version_hide') ){
				return '';
			}
			if( $this->setting->get_setting('footer_version') ){
				return $this->setting->get_setting('footer_version');
			}
		}else{
			if( $this->setting->get_setting('footer_text_hide') ){
				return '';
			}
			if( $this->setting->get_setting('footer_text') ){
				return $this->setting->get_setting('footer_text');
			}
		}
		return $default;
	}

	function admin_screen() {
		include 'tpl.php';
	}
}
