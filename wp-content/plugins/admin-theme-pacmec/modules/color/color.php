<?php
/**
 * @author pacmec.co
 */

class Admin_Theme_PACMEC_Color{

	private $setting;

	function __construct($setting) {
		$this->setting = $setting;
		add_action( 'admin_screen_col_1', array( $this, 'admin_screen' ));
		remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
	}

	function admin_screen() {
		$colors = [];
        $dir = plugin_dir_path( $this->setting->plugin_file );
        foreach ( (array) glob( $dir . '/theme/theme.*.css' ) as $file ) {
            if ( preg_match('/theme\.(.*)\.css/', $file, $matches) ) {
                $colors[] = $matches[1];
            }
        }
		include 'tpl.php';
	}
}
