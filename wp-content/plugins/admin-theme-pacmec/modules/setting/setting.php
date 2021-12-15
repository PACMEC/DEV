<?php
/**
 * @author flatfull.com
 */

class Admin_Theme_PACMEC_Setting{

	public   $menus
			,$submenus
			,$setting
			,$setting_name
			,$page_title
			,$menu_title
			,$menu_slug
			,$plugin_url
			,$plugin_path
			,$plugin_file
			,$plugin_post  = 'options.php'
			;
	function __construct($arg) {
		foreach ($arg as $k => $value) {
		    $this->{$k} = $value;
		}

		$this->plugin_url = plugins_url('', $this->plugin_file ).'/';
		$this->plugin_path = plugin_dir_path( $this->plugin_file );

		$this->set_setting();
		
		add_action( 'admin_menu', array( $this, 'add_menu' ) );
		add_action( 'admin_init', array( $this, 'register_setting' ) );
		add_action( 'admin_init', array( $this, 'set_setting' ) );
		add_action( 'admin_init', array( $this, 'process_setting_import' ) );
		add_action( 'admin_init', array( $this, 'process_setting_export' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ), 999 );

		register_deactivation_hook( $this->plugin_file, array($this, "deactivation"));
	}

	// add plugin to setting menu
	function add_menu() {
		if($this->active){
			$page = add_submenu_page( 'themes.php', $this->page_title, $this->menu_title, 'switch_themes', $this->menu_slug, array( $this, 'admin_screen' ) );
			add_action('load-'.$page, array( $this, 'admin_help' ));
		}
	}

	// register
	function register_setting() {
		register_setting( $this->setting_name.'_group', $this->setting_name );
	}

	function set_setting(){
		$this->active = true;
		if ( is_multisite() ) {
			$this->setting = get_blog_option(1, $this->setting_name );
			if(get_current_blog_id() != 1){
				if($this->get_setting('network') == true){
					$this->active = false;
				}else{
					$this->setting = get_option( $this->setting_name );
				}
			}
		}else{
			$this->setting = get_option( $this->setting_name );
		}
	}

	// get setting
	public function get_setting($arg){
		$settings = isset($_SESSION[$this->setting_name]) ? $_SESSION[$this->setting_name] : $this->setting;
	    foreach (func_get_args() as $arg) {
	        if (!is_array($settings) || !is_scalar($arg) || !isset($settings[$arg])) {
	            return NULL;
	        }
	        $settings = $settings[$arg];
	    }
	    return $settings;
	}

	/**
	 * Process a setting export to a json file
	 */
	function process_setting_export() {
		if( empty( $_POST['setting_action'] ) || 'export_setting' != $_POST['setting_action'] )
			return;
		if( ! wp_verify_nonce( $_POST['setting_export_nonce'], 'setting_export_nonce' ) )
			return;
		if( ! current_user_can( 'manage_options' ) )
			return;
		
		$setting = get_option( $this->setting_name );
		ignore_user_abort( true );
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=admin-theme-setting-export-' . date( 'm-d-Y' ) . '.json' );
		header( "Expires: 0" );
		echo json_encode( $setting );
		exit;
	}

	/**
	 * Process a setting import from a json file
	 */
	function process_setting_import() {
		if( empty( $_POST['setting_action'] ) || 'import_setting' != $_POST['setting_action'] )
			return;
		if( ! wp_verify_nonce( $_POST['setting_import_nonce'], 'setting_import_nonce' ) )
			return;
		if( ! current_user_can( 'manage_options' ) )
			return;

		$import_file = $_FILES['import_file']['tmp_name'];
		if( empty( $import_file ) ) {
			wp_die( __( 'Please upload a file to import' ) );
		}
		// Retrieve the setting from the file and convert the json object to an array.
		$setting = (array) json_decode( file_get_contents( $import_file ), true );
		update_option( $this->setting_name, $setting );
		wp_safe_redirect( admin_url( 'admin.php?page='.$this->menu_slug ) ); exit;
	}

	// help
	function admin_help() {
		$current_screen = get_current_screen();

		// Overview
		$current_screen->add_help_tab(
			array(
				'id'		=> 'overview',
				'title'		=> __( 'Overview', 'ffl_admin_theme' ),
				'content'	=>
					'<p><strong>' . __( 'Admin Theme by flatfull.com', 'ffl_admin_theme' ) . '</strong></p>' .
					'<p>' . __( 'Admin Theme changes your wordpress admin appearance', 'ffl_admin_theme' ) . '</p>' .
					'<p>' . __( 'Have fun.', 'ffl_admin_theme' ) . '</p>',
			)
		);

		// Help Sidebar
		$current_screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'ffl_admin_theme' ) . '</strong></p>' .
			'<p><a href="http://flatfull.com/" target="_blank">'     . __( 'FAQ',     'ffl_admin_theme' ) . '</a></p>' .
			'<p></p>'
		);
	}

	function admin_scripts() {

		wp_enqueue_style( 'admin-theme-variables', $this->plugin_url.( "theme/color.variables.css" ) );

		$this->export_customize();

		wp_enqueue_style( 'admin-theme-admin', $this->plugin_url.( "theme/admin.css" ) );
		
		wp_enqueue_style( 'admin-theme-color', $this->plugin_url.( "theme/color.css" ) );
		
		wp_enqueue_style( 'admin-theme-theme', $this->plugin_url.( "theme/theme.css" ) );

		wp_add_inline_style('admin-theme-style', $this->get_setting('admin_css') );
		wp_add_inline_script('admin-theme-main', $this->get_setting('admin_js') );
	}

	function export_customize(){
		$default = $this->get_setting('use-default-color');
		if($default){
			$color = $this->get_setting('default-color');
			$this->default_style( $color );
		}else{
			$this->customize_style( $this->setting );
		}
	}

	function default_style($style){
		wp_enqueue_style( 'admin-theme-default', $this->plugin_url.( 'theme/theme.'.$style.'.css' ) );
	}

	function customize_style($style){
		$css = "";
		// menu
		if( isset($style['color']) ){
		    foreach ( $style['color'] as $variable => $vvalue ) {
		    	if($vvalue != ''){
		        	$css .= '--' . $variable . ': ' . $vvalue . ';';
		        }
		    }
		}
	  	// bar
	  	if( isset($style['bar']) ){
		    foreach ( $style['bar'] as $variable => $vvalue ) {
		    	if($vvalue != ''){
		        	$css .= '--bar-' . $variable . ': ' . $vvalue . ';';
		        }
		    }
	    }
	    wp_add_inline_style('admin-theme-variables', sprintf(':root{%s}', $css) );
	}

	// deactivation
	function deactivation() {
		delete_option( $this->setting_name );
	}

	function admin_screen() {
		do_action('admin_screen_start');
		include 'tpl.php';
		do_action('admin_screen_end');
	}
}
