<?php
/**
 * @author pacmec.co
 */

class Admin_Theme_PACMEC_Login{
	
	private $setting;

	function __construct($setting) {
		$this->setting = $setting;
		
		add_action( 'admin_screen_col_1', array( $this, 'admin_screen' ));
		if($this->setting->get_setting('login_disable')){
			return;
		}
		add_action( 'login_enqueue_scripts', array( $this, 'login_style' ), 99 );
		add_action( 'login_enqueue_scripts', array( $this, 'login_script' ), 1 );
		add_action( 'login_message', array( $this, 'login_message' ) );
		add_action( 'login_footer', array( $this, 'login_footer' ) );
	}

	// login
	function login_style() {
		$this->setting->set_setting();
		add_filter( 'login_headerurl', array( $this, 'login_headerurl' ) );
		add_filter( 'login_headertext', array( $this, 'login_headertitle' ) ); 
		wp_enqueue_style( 'admin-theme-login', $this->setting->plugin_url.( "theme/login.css" ) );
		wp_enqueue_style( 'admin-theme-login-theme', $this->setting->plugin_url.( "theme/theme.css" ) ); 
		$this->login_css();
	}

	function login_script() {
		wp_enqueue_script( 'admin-theme-form', $this->setting->plugin_url.( "assets/js/form.js" ), array('jquery'));
	}

	function login_headerurl() {
		return esc_url( trailingslashit( get_bloginfo( 'url' ) ) );
	}

	function login_headertitle() {
		return esc_attr( get_bloginfo( 'name' ) );
	}

	function login_message(){
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'login';
		$title = $this->setting->get_setting('login_form_'.$action.'_title');
		echo sprintf('<div class="login-subtitle">%s</div></div><div id="login-form"><div class="login-form"><h3 class="login-form-title">%s</h3>', $this->setting->get_setting('login_subtitle'), $title );
	}

	function login_footer(){
		echo sprintf('<div class="login-footer">%s</div></div></div>', $this->setting->get_setting('login_footer') );
	}

	function login_css(){
		$css = '';

		$bg_color = $this->setting->get_setting('login_bg_color');
		if( $bg_color ) { $css .= sprintf('body{background-color: %s}', $bg_color); }

		$bg_img = $this->setting->get_setting('login_bg_img');
		if( $bg_img ) { $css .= sprintf('body{background-image: url( %s )}', $bg_img); }

		$left_bg_color = $this->setting->get_setting('login_left_bg_color');
		if( $left_bg_color ) { $css .= sprintf('#login{background-color: %s}', $left_bg_color); }

		$left_bg_img = $this->setting->get_setting('login_left_bg_img');
		if( $left_bg_img ) { $css .= sprintf('#login{background-image: url( %s )}', $left_bg_img); }

		$text_color = $this->setting->get_setting('login_text_color');
		if( $text_color ) { $css .= sprintf('body{color: %s}', $text_color); }

		$logo = $this->setting->get_setting('login_logo');
		if( $logo ) { $css .= sprintf('body.login div#login > h1{background-image: url( %s )} body.login div#login h1 a {background-image: none;}', $logo); }

		wp_add_inline_style('admin-theme-login', $css.$this->setting->get_setting('login_css') );
	}

	function admin_screen() {
		include 'tpl.php';
	}
}
