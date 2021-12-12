<?php
/**
Plugin Name: Admin Theme PACMEC
Plugin URI: #
Description: Cambiar la barra de administración, el menú, el inicio de sesión, el pie de página, el icono y los colores de PACMEC.
Version: 1.0.0
Author: PACMEC
Author URI: #
Text Domain: admin_theme_PACMEC
*/

class Admin_Theme_Material_PACMEC {

	function __construct() {
		$this->init();
	}

	function init(){
		
		$dir = dirname(__FILE__);
		require $dir . '/modules/setting/setting.php';
		require $dir . '/modules/nav/nav.php';
		require $dir . '/modules/color/color.php';
		require $dir . '/modules/login/login.php';
		require $dir . '/modules/footer/footer.php';

		$arg = array(
		     'page_title'   => 'Material Admin Theme'
		    ,'menu_title'	=> 'Material Admin'
		    ,'menu_slug'	=> 'admin-material'
		    ,'setting_name' => 'admin_theme_material_option'
			,'plugin_file'  => __FILE__
		);

		$setting = 
		new Admin_Theme_PACMEC_Setting($arg);
		new Admin_Theme_PACMEC_Nav($setting);
		new Admin_Theme_PACMEC_Color($setting);
		new Admin_Theme_PACMEC_Footer($setting);
		new Admin_Theme_PACMEC_Login($setting);

		require $dir . '/modules/demo/demo.php';
		new Admin_Theme_PACMEC_Demo($setting);

	}

}

new Admin_Theme_Material_PACMEC;
